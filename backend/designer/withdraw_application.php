<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLogin();

$user_id        = (int) $_SESSION["user_id"];
$designer_id    = getDesignerId($conn, $user_id);
$application_id = isset($_POST["application_id"]) ? (int) $_POST["application_id"] : 0;

if ($application_id > 0 && $designer_id) {

    $delete_sql = "
        DELETE FROM job_applications
        WHERE id = ?
            AND designer_id = ?
            AND application_status = 'pending'
    ";

    $delete_stmt = $conn->prepare($delete_sql);

    $delete_stmt->bind_param(
        "ii",
        $application_id,
        $designer_id
    );

    $delete_stmt->execute();

    $delete_stmt->close();

}

header("Location: ../../frontend/pages/designer/my_jobs.php");
exit;

?>
