<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";
require_once __DIR__ . "/message_helpers.php";

requireRoleLoginJson(["designer", "company"]);

$user_id     = (int) $_SESSION["user_id"];
$only_role   = $_SESSION["user_role"] === "company" ? "designer" : null;

session_write_close();

$is_post = $_SERVER["REQUEST_METHOD"] === "POST";
$action  = $is_post ? ($_POST["action"] ?? "") : ($_GET["action"] ?? "");

switch ($action) {

    case "poll":

        $with     = isset($_GET["with"]) ? (int) $_GET["with"] : 0;
        $after_id = isset($_GET["after_id"]) ? (int) $_GET["after_id"] : 0;

        $out = ["success" => true];

        if ($with > 0 && $with !== $user_id) {

            $partner = getMessageablePartner($conn, $with, $only_role);

            $out["partner"] = $partner;

            if ($partner) {

                $out["messages"] = getConversationMessages(
                    $conn,
                    $user_id,
                    $with,
                    $after_id
                );

                markConversationRead($conn, $user_id, $with);
            }
        }

        $out["conversations"]         = getConversations($conn, $user_id);
        $out["unread_messages"]       = getUnreadMessageCount($conn, $user_id);
        $out["unread_notifications"]  = getUnreadNotificationCount($conn, $user_id);

        jsonResponse($out);

        break;

    case "search":

        $q = trim($_GET["q"] ?? "");

        if (mb_strlen($q) < 2) {
            jsonResponse(["success" => true, "people" => []]);
        }

        $q = mb_substr($q, 0, 60);

        jsonResponse([
            "success" => true,
            "people"  => searchMessageableUsers($conn, $user_id, $q, $only_role),
        ]);

        break;

    case "unread_counts":

        jsonResponse([
            "success"              => true,
            "unread_messages"      => getUnreadMessageCount($conn, $user_id),
            "unread_notifications" => getUnreadNotificationCount($conn, $user_id),
        ]);

        break;

    case "send":

        if (!$is_post) {
            jsonResponse(["success" => false, "message" => "POST required"], 405);
        }

        if (!verifyCsrf($_POST["csrf"] ?? "")) {
            jsonResponse(["success" => false, "message" => "Session expired. Please refresh the page."], 403);
        }

        $receiver_id = isset($_POST["receiver_id"]) ? (int) $_POST["receiver_id"] : 0;

        $text = trim((string) ($_POST["message"] ?? ""));
        $text = str_replace("\r\n", "\n", $text);

        if ($text === "") {
            jsonResponse(["success" => false, "message" => "Type a message first."], 400);
        }

        if (mb_strlen($text) > 2000) {
            jsonResponse(["success" => false, "message" => "Messages can be up to 2000 characters."], 400);
        }

        if ($receiver_id <= 0 || $receiver_id === $user_id) {
            jsonResponse(["success" => false, "message" => "Invalid recipient."], 400);
        }

        if (!getMessageablePartner($conn, $receiver_id, $only_role)) {
            jsonResponse(["success" => false, "message" => "That user can't receive messages."], 404);
        }

        $insert = $conn->prepare("
            INSERT INTO messages (sender_id, receiver_id, message)
            VALUES (?, ?, ?)
        ");

        $insert->bind_param("iis", $user_id, $receiver_id, $text);

        if (!$insert->execute()) {
            $insert->close();
            jsonResponse(["success" => false, "message" => "Could not send message."], 500);
        }

        $new_id = (int) $conn->insert_id;

        $insert->close();

        notifyNewMessage($conn, $user_id, $receiver_id, $text);

        $fetch = $conn->prepare("
            SELECT
                id,
                sender_id,
                message,
                sent_at,
                DATEDIFF(CURDATE(), DATE(sent_at)) AS days_ago
            FROM messages
            WHERE id = ?
            LIMIT 1
        ");

        $fetch->bind_param("i", $new_id);
        $fetch->execute();

        $row = $fetch->get_result()->fetch_assoc();

        $fetch->close();

        jsonResponse([
            "success" => true,
            "message" => formatMessageRow($row, $user_id),
        ]);

        break;

    default:

        jsonResponse(["success" => false, "message" => "Unknown action"], 400);
}

?>
