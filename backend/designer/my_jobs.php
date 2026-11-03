<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLogin();

$user_id     = (int) $_SESSION["user_id"];
$designer_id = getDesignerId($conn, $user_id);

$my_jobs_sql = "
    SELECT

        ja.id AS application_id,

        ja.application_status,

        ja.applied_at,

        j.title,

        j.location,

        j.employment_type,

        c.company_name

    FROM job_applications ja

    INNER JOIN jobs j
        ON j.id = ja.job_id

    INNER JOIN companies c
        ON c.id = j.company_id

    WHERE ja.designer_id = ?

    ORDER BY ja.applied_at DESC
";

$my_jobs_stmt = $conn->prepare($my_jobs_sql);

$my_jobs_stmt->bind_param(
    "i",
    $designer_id
);

$my_jobs_stmt->execute();

$my_jobs_result = $my_jobs_stmt->get_result();

$my_jobs_full_data = [];

while ($job = $my_jobs_result->fetch_assoc()) {

    $my_jobs_full_data[] = $job;

}

$my_jobs_stmt->close();

?>
