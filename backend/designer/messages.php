<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";
require_once __DIR__ . "/message_helpers.php";

requireRoles(["designer", "company"]);

$user_id   = (int) $_SESSION["user_id"];
$only_role = $_SESSION["user_role"] === "company" ? "designer" : null;

$with_id = isset($_GET["with"]) ? (int) $_GET["with"] : 0;

$active_partner  = null;
$active_messages = [];

if ($with_id > 0 && $with_id !== $user_id) {

    $active_partner = getMessageablePartner($conn, $with_id, $only_role);

    if ($active_partner) {

        $active_messages = getConversationMessages($conn, $user_id, $with_id);

        markConversationRead($conn, $user_id, $with_id);
    }
}

$conversations_data = getConversations($conn, $user_id);

$chat_boot = [
    "me"            => $user_id,
    "partner"       => $active_partner,
    "messages"      => $active_messages,
    "conversations" => $conversations_data,
];

$chat_boot_json = json_encode(
    $chat_boot,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
);

?>
