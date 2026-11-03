<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/company_helpers.php";

requireCompanyLoginJson();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(["success" => false, "message" => "POST required"], 405);
}

if (!verifyCsrf($_POST["csrf"] ?? "")) {
    jsonResponse(["success" => false, "message" => "Session expired. Please refresh the page."], 403);
}

$user_id        = (int) $_SESSION["user_id"];
$company_id     = getCompanyId($conn, $user_id);
$application_id = isset($_POST["application_id"]) ? (int) $_POST["application_id"] : 0;
$new_status     = $_POST["status"] ?? "";

if (($_POST["action"] ?? "") !== "set_status") {
    jsonResponse(["success" => false, "message" => "Unknown action"], 400);
}

if ($application_id <= 0 || !in_array($new_status, companyApplicationStatuses(), true)) {
    jsonResponse(["success" => false, "message" => "Invalid request"], 400);
}

$stmt = $conn->prepare("
    SELECT ja.application_status
    FROM job_applications ja
    JOIN jobs j ON j.id = ja.job_id
    WHERE ja.id = ? AND j.company_id = ?
    LIMIT 1
");

$stmt->bind_param("ii", $application_id, $company_id);
$stmt->execute();

$current = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$current) {
    jsonResponse(["success" => false, "message" => "Application not found"], 404);
}

if ($current["application_status"] === $new_status) {
    jsonResponse(["success" => true, "status" => $new_status, "changed" => false]);
}

$update = $conn->prepare("UPDATE job_applications SET application_status = ? WHERE id = ?");

$update->bind_param("si", $new_status, $application_id);

$ok = $update->execute();

$update->close();

if (!$ok) {
    jsonResponse(["success" => false, "message" => "Could not update the status"], 500);
}

notifyApplicationStatus($conn, $application_id, $new_status);

jsonResponse([
    "success" => true,
    "status"  => $new_status,
    "changed" => true,
]);

?>
