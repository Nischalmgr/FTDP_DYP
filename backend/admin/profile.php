<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/admin_helpers.php";

requireAdminLogin();

$admin_flash = adminTakeFlash();

$user_id = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("
    SELECT full_name, email, created_at
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$admin_user = $stmt->get_result()->fetch_assoc();

$stmt->close();

?>
