<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/admin_helpers.php";

requireAdminLogin();

$admin_flash = adminTakeFlash();

$announce_old = $_SESSION["announce_old"] ?? ["audience" => "all", "title" => "", "message" => ""];
unset($_SESSION["announce_old"]);

$audience_counts = $conn->query("
    SELECT
        COALESCE(SUM(role = 'designer'), 0) AS designers,
        COALESCE(SUM(role = 'company'), 0)  AS companies
    FROM users
")->fetch_assoc();

?>
