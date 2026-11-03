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

$admin_designs = adminFetchAll($conn->query("
    SELECT
        d.id,
        d.title,
        d.image,
        d.created_at,
        u.id AS designer_user_id,
        u.full_name AS designer_name,
        (SELECT COUNT(*) FROM design_likes l    WHERE l.design_id = d.id) AS like_count,
        (SELECT COUNT(*) FROM design_comments c WHERE c.design_id = d.id) AS comment_count
    FROM designs d
    JOIN users u ON u.id = d.user_id
    ORDER BY d.created_at $dir, d.id $dir
"));

?>
