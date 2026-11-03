<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/admin_helpers.php";

requireAdminLogin();

$admin_flash = adminTakeFlash();

$sort = adminSort();
$dir  = adminSortDir($sort);

$admin_jobs = adminFetchAll($conn->query("
    SELECT
        j.id,
        j.title,
        j.location,
        j.employment_type,
        j.deadline,
        j.status,
        j.created_at,
        c.company_name,
        COUNT(ja.id) AS applicant_count
    FROM jobs j
    JOIN companies c ON c.id = j.company_id
    LEFT JOIN job_applications ja ON ja.job_id = j.id
    GROUP BY j.id
    ORDER BY j.created_at $dir, j.id $dir
"));

?>
