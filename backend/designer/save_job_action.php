<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLoginJson();

$user_id = (int) $_SESSION["user_id"];
$job_id  = isset($_POST["job_id"]) ? (int) $_POST["job_id"] : 0;

if ($job_id <= 0) {
    jsonResponse(["success" => false, "message" => "Invalid job"], 400);
}

$exists_stmt = $conn->prepare("SELECT id FROM jobs WHERE id = ? AND status = 'open' LIMIT 1");

$exists_stmt->bind_param("i", $job_id);
$exists_stmt->execute();

$job_found = $exists_stmt->get_result()->num_rows > 0;

$exists_stmt->close();

if (!$job_found) {
    jsonResponse(["success" => false, "message" => "This job is no longer available."], 404);
}

$check_sql = "
    SELECT id
    FROM saved_jobs
    WHERE job_id = ? AND user_id = ?
";

$check_stmt = $conn->prepare($check_sql);

$check_stmt->bind_param(
    "ii",
    $job_id,
    $user_id
);

$check_stmt->execute();

$check_result = $check_stmt->get_result();

$existing = $check_result->fetch_assoc();

$check_stmt->close();

if ($existing) {

    $delete_sql = "DELETE FROM saved_jobs WHERE id = ?";

    $delete_stmt = $conn->prepare($delete_sql);

    $existing_id = (int) $existing["id"];

    $delete_stmt->bind_param("i", $existing_id);

    $delete_stmt->execute();

    $delete_stmt->close();

    $saved = false;

} else {

    $insert_sql = "
        INSERT INTO saved_jobs (user_id, job_id)
        VALUES (?, ?)
    ";

    $insert_stmt = $conn->prepare($insert_sql);

    $insert_stmt->bind_param(
        "ii",
        $user_id,
        $job_id
    );

    $insert_stmt->execute();

    $insert_stmt->close();

    $saved = true;

}

jsonResponse([
    "success" => true,
    "saved"   => $saved,
]);

?>
