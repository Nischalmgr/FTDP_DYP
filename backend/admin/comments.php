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

$admin_comments = adminFetchAll($conn->query("
    SELECT
        c.id,
        c.comment,
        c.created_at,
        u.full_name AS commenter_name,
        d.title AS design_title
    FROM design_comments c
    JOIN users u   ON u.id = c.user_id
    JOIN designs d ON d.id = c.design_id
    ORDER BY c.created_at $dir, c.id $dir
"));

?>
