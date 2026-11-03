<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";
require_once __DIR__ . "/comm_helpers.php";

requireRoles(["designer", "company"]);

$user_id = (int) $_SESSION["user_id"];

$notif_filter = (($_GET["filter"] ?? "all") === "unread") ? "unread" : "all";

$notif_per_page = 20;
$notif_page     = max(1, (int) ($_GET["page"] ?? 1));

$count_sql = "
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN is_read = 'N' THEN 1 ELSE 0 END) AS unread
    FROM notifications
    WHERE user_id = ?
";

$count_stmt = $conn->prepare($count_sql);

$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();

$count_row = $count_stmt->get_result()->fetch_assoc();

$count_stmt->close();

$notif_total_all    = (int) $count_row["total"];
$notif_total_unread = (int) ($count_row["unread"] ?? 0);

$notif_total_filtered = ($notif_filter === "unread")
    ? $notif_total_unread
    : $notif_total_all;

$notif_total_pages = max(1, (int) ceil($notif_total_filtered / $notif_per_page));

if ($notif_page > $notif_total_pages) {
    $notif_page = $notif_total_pages;
}

$notif_offset = ($notif_page - 1) * $notif_per_page;

$list_sql = "
    SELECT
        id,
        type,
        title,
        message,
        link,
        is_read,
        created_at,
        TIMESTAMPDIFF(SECOND, created_at, NOW()) AS age_seconds
    FROM notifications
    WHERE user_id = ?
";

if ($notif_filter === "unread") {
    $list_sql .= " AND is_read = 'N'";
}

$list_sql .= "
    ORDER BY created_at DESC, id DESC
    LIMIT ? OFFSET ?
";

$list_stmt = $conn->prepare($list_sql);

$list_stmt->bind_param("iii", $user_id, $notif_per_page, $notif_offset);
$list_stmt->execute();

$list_result = $list_stmt->get_result();

$notifications_data = [];

while ($row = $list_result->fetch_assoc()) {
    $notifications_data[] = $row;
}

$list_stmt->close();

?>
