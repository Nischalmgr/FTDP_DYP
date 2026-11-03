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

$admin_applications = adminFetchAll($conn->query("
    SELECT
        ja.id,
        ja.application_status,
        ja.applied_at,
        u.full_name AS designer_name,
        j.title AS job_title,
        c.company_name
    FROM job_applications ja
    JOIN designers d ON d.id = ja.designer_id
    JOIN users u     ON u.id = d.user_id
    JOIN jobs j      ON j.id = ja.job_id
    JOIN companies c ON c.id = j.company_id
    ORDER BY ja.applied_at $dir, ja.id $dir
"));

?>
