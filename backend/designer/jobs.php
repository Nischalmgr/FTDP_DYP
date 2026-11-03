<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLogin();

$user_id     = (int) $_SESSION["user_id"];
$designer_id = getDesignerId($conn, $user_id);

$jobs_sql = "
    SELECT

        j.id,

        j.title,

        j.description,

        j.location,

        j.salary,

        j.employment_type,

        j.deadline,

        j.created_at,

        c.company_name,

        ja.application_status,

        CASE WHEN ja.id IS NULL THEN 0 ELSE 1 END AS has_applied,

        CASE WHEN sj.id IS NULL THEN 0 ELSE 1 END AS is_saved

    FROM jobs j

    INNER JOIN companies c
        ON c.id = j.company_id

    LEFT JOIN job_applications ja
        ON ja.job_id = j.id AND ja.designer_id = ?

    LEFT JOIN saved_jobs sj
        ON sj.job_id = j.id AND sj.user_id = ?

    WHERE j.status = 'open'

    ORDER BY j.created_at DESC
";

$jobs_stmt = $conn->prepare($jobs_sql);

$jobs_stmt->bind_param(
    "ii",
    $designer_id,
    $user_id
);

$jobs_stmt->execute();

$jobs_result = $jobs_stmt->get_result();

$jobs_data = [];

while ($job = $jobs_result->fetch_assoc()) {

    $jobs_data[] = $job;

}

$jobs_stmt->close();

?>
