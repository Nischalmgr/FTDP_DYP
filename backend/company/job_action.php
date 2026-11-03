<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/company_helpers.php";

requireCompanyLogin();

$user_id    = (int) $_SESSION["user_id"];
$company_id = getCompanyId($conn, $user_id);

$action = $_POST["action"] ?? "";
$job_id = isset($_POST["job_id"]) ? (int) $_POST["job_id"] : 0;

$back = "../../frontend/pages/company/jobs.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !verifyCsrf($_POST["csrf"] ?? "")) {

    $_SESSION["jobs_flash"] = ["type" => "error", "message" => "Your session expired. Please try again."];

    header("Location: " . $back);
    exit;
}

if ($job_id <= 0) {

    $_SESSION["jobs_flash"] = ["type" => "error", "message" => "Invalid job."];

    header("Location: " . $back);
    exit;
}

switch ($action) {

    case "close":
    case "reopen":

        $new_status = ($action === "close") ? "closed" : "open";

        $stmt = $conn->prepare("UPDATE jobs SET status = ? WHERE id = ? AND company_id = ?");

        $stmt->bind_param("sii", $new_status, $job_id, $company_id);
        $stmt->execute();

        $changed = $stmt->affected_rows > 0;

        $stmt->close();

        $message = $action === "close"
            ? "Job closed. Designers can no longer see or apply to it."
            : "Job reopened and visible to designers again.";

        break;

    case "delete":

        $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ? AND company_id = ?");

        $stmt->bind_param("ii", $job_id, $company_id);
        $stmt->execute();

        $changed = $stmt->affected_rows > 0;

        $stmt->close();

        $message = "Job deleted along with its applications.";

        break;

    default:

        $changed = false;
        $message = "Unknown action.";
}

$_SESSION["jobs_flash"] = [
    "type"    => $changed ? "success" : "error",
    "message" => $changed ? $message : "Nothing was changed.",
];

header("Location: " . $back);
exit;

?>
