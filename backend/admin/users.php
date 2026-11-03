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

$admin_users = adminFetchAll($conn->query("
    SELECT
        u.id,
        u.full_name,
        u.email,
        u.role,
        u.is_verified,
        u.profile_picture,
        u.created_at,
        c.company_name
    FROM users u
    LEFT JOIN companies c ON c.user_id = u.id
    ORDER BY u.created_at $dir, u.id $dir
"));

?>
