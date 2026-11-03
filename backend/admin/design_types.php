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

$admin_types = adminFetchAll($conn->query("
    SELECT
        t.id,
        t.name,
        t.description,
        t.created_at,
        (SELECT COUNT(*) FROM design_type_mapping m WHERE m.design_type_id = t.id) AS design_count
    FROM design_types t
    ORDER BY t.created_at $dir, t.id $dir
"));

?>
