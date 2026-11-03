<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLogin();

$user_id     = (int) $_SESSION["user_id"];
$designer_id = getDesignerId($conn, $user_id);

$saved_jobs_sql = "
    SELECT

        j.id,

        j.title,

        j.location,

        j.salary,

        j.employment_type,

        j.deadline,

        c.company_name,

        sj.saved_at,

        CASE WHEN ja.id IS NULL THEN 0 ELSE 1 END AS has_applied

    FROM saved_jobs sj

    INNER JOIN jobs j
        ON j.id = sj.job_id

    INNER JOIN companies c
        ON c.id = j.company_id

    LEFT JOIN job_applications ja
        ON ja.job_id = j.id AND ja.designer_id = ?

    WHERE sj.user_id = ?

    ORDER BY sj.saved_at DESC
";

$saved_jobs_stmt = $conn->prepare($saved_jobs_sql);

$saved_jobs_stmt->bind_param(
    "ii",
    $designer_id,
    $user_id
);

$saved_jobs_stmt->execute();

$saved_jobs_result = $saved_jobs_stmt->get_result();

$saved_jobs_data = [];

while ($job = $saved_jobs_result->fetch_assoc()) {

    $saved_jobs_data[] = $job;

}

$saved_jobs_stmt->close();

?>
