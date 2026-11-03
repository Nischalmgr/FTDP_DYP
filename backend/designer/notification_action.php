<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";
require_once __DIR__ . "/comm_helpers.php";

requireRoleLoginJson(["designer", "company"]);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(["success" => false, "message" => "POST required"], 405);
}

if (!verifyCsrf($_POST["csrf"] ?? "")) {
    jsonResponse(["success" => false, "message" => "Session expired. Please refresh the page."], 403);
}

$user_id = (int) $_SESSION["user_id"];
$action  = $_POST["action"] ?? "";
$id      = isset($_POST["id"]) ? (int) $_POST["id"] : 0;

switch ($action) {

    case "mark_read":

        if ($id <= 0) {
            jsonResponse(["success" => false, "message" => "Invalid notification"], 400);
        }

        $stmt = $conn->prepare(
            "UPDATE notifications SET is_read = 'Y' WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();

        break;

    case "mark_all_read":

        $stmt = $conn->prepare(
            "UPDATE notifications SET is_read = 'Y' WHERE user_id = ? AND is_read = 'N'"
        );

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        break;

    case "delete":

        if ($id <= 0) {
            jsonResponse(["success" => false, "message" => "Invalid notification"], 400);
        }

        $stmt = $conn->prepare(
            "DELETE FROM notifications WHERE id = ? AND user_id = ?"
        );

        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();

        break;

    case "clear_all":

        $stmt = $conn->prepare("DELETE FROM notifications WHERE user_id = ?");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        break;

    default:

        jsonResponse(["success" => false, "message" => "Unknown action"], 400);
}

$total_stmt = $conn->prepare("SELECT COUNT(*) AS c FROM notifications WHERE user_id = ?");

$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();

$total = (int) $total_stmt->get_result()->fetch_assoc()["c"];

$total_stmt->close();

jsonResponse([
    "success" => true,
    "unread"  => getUnreadNotificationCount($conn, $user_id),
    "total"   => $total,
]);

?>
