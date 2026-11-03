<?php

session_start();

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true
) {
    header("Location: ../../auth/login.php");
    exit;
}

if (
    !isset($_SESSION["user_role"]) ||
    $_SESSION["user_role"] !== "company"
) {
    header("Location: ../../auth/login.php");
    exit;
}

require_once __DIR__ . "/../../../backend/company/notifications.php";

$csrf_token = csrfToken();

$notif_icons = [
    "like"        => "♥",
    "comment"     => "✎",
    "application" => "▣",
    "message"     => "✉",
    "announcement" => "✦",
    "removal"     => "✕",
    "general"     => "♢",
];

function notifPageUrl(string $filter, int $page): string
{
    $params = [];

    if ($filter === "unread") {
        $params["filter"] = "unread";
    }

    if ($page > 1) {
        $params["page"] = $page;
    }

    return "notifications.php" . (count($params) ? "?" . http_build_query($params) : "");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token); ?>">

    <title>Notifications | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/designer_comm.css">
    <link rel="stylesheet" href="../../css/notifications.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/company_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>Notifications</h1>
            <p>New applications and messages.</p>
        </div>

        <div class="notif-toolbar">

            <div class="notif-tabs" role="tablist">

                <a href="<?= notifPageUrl("all", 1); ?>"
                   class="notif-tab <?= $notif_filter === "all" ? "active" : ""; ?>">
                    All
                    <span class="notif-tab-count js-count-all"><?= $notif_total_all; ?></span>
                </a>

                <a href="<?= notifPageUrl("unread", 1); ?>"
                   class="notif-tab <?= $notif_filter === "unread" ? "active" : ""; ?>">
                    Unread
                    <span class="notif-tab-count js-count-unread"><?= $notif_total_unread; ?></span>
                </a>

            </div>

            <div class="notif-actions">

                <button type="button"
                        class="comm-btn ghost small"
                        id="markAllReadBtn"
                        <?= $notif_total_unread === 0 ? "disabled" : ""; ?>>
                    Mark all as read
                </button>

                <button type="button"
                        class="comm-btn danger small"
                        id="clearAllBtn"
                        <?= $notif_total_all === 0 ? "disabled" : ""; ?>>
                    Clear all
                </button>

            </div>

        </div>

        <?php if (count($notifications_data) > 0): ?>

            <div class="comm-card notif-list" id="notifList">

                <?php foreach ($notifications_data as $n): ?>

                    <?php
                        $is_unread = $n["is_read"] === "N";
                        $icon      = $notif_icons[$n["type"]] ?? $notif_icons["general"];
                        $safe_link = (
                            !empty($n["link"]) &&
                            preg_match('/^[a-z_]+\.php(\?[a-z0-9_=&]*)?$/i', $n["link"])
                        ) ? $n["link"] : "";
                    ?>

                    <div class="notif-item <?= $is_unread ? "unread" : ""; ?>"
                         data-id="<?= (int) $n["id"]; ?>"
                         data-link="<?= htmlspecialchars($safe_link); ?>">

                        <span class="notif-icon type-<?= htmlspecialchars($n["type"]); ?>" aria-hidden="true">
                            <?= $icon; ?>
                        </span>

                        <button type="button" class="notif-main">

                            <span class="notif-title">
                                <?= htmlspecialchars($n["title"] ?? "Notification"); ?>
                                <?php if ($is_unread): ?>
                                    <span class="notif-dot" title="Unread"></span>
                                <?php endif; ?>
                            </span>

                            <span class="notif-message">
                                <?= htmlspecialchars($n["message"]); ?>
                            </span>

                            <span class="notif-time">
                                <?= htmlspecialchars(formatAge((int) $n["age_seconds"], $n["created_at"])); ?>
                            </span>

                        </button>

                        <div class="notif-item-actions">

                            <?php if ($is_unread): ?>
                                <button type="button"
                                        class="comm-icon-btn js-notif-read"
                                        title="Mark as read"
                                        aria-label="Mark as read">✓</button>
                            <?php endif; ?>

                            <button type="button"
                                    class="comm-icon-btn js-notif-delete"
                                    title="Delete"
                                    aria-label="Delete notification">✕</button>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <?php if ($notif_total_pages > 1): ?>

                <div class="notif-pagination">

                    <?php if ($notif_page > 1): ?>
                        <a class="comm-btn ghost small"
                           href="<?= notifPageUrl($notif_filter, $notif_page - 1); ?>">Newer</a>
                    <?php endif; ?>

                    <span>Page <?= $notif_page; ?> of <?= $notif_total_pages; ?></span>

                    <?php if ($notif_page < $notif_total_pages): ?>
                        <a class="comm-btn ghost small"
                           href="<?= notifPageUrl($notif_filter, $notif_page + 1); ?>">Older</a>
                    <?php endif; ?>

                </div>

            <?php endif; ?>

        <?php else: ?>

            <div class="comm-card notif-empty">

                <div class="notif-empty-icon" aria-hidden="true">♢</div>

                <h2><?= $notif_filter === "unread" ? "You're all caught up" : "Nothing here yet"; ?></h2>

                <p>
                    <?= $notif_filter === "unread"
                        ? "No unread notifications right now."
                        : "When a designer applies to one of your jobs or sends you a message, it will show up here."; ?>
                </p>

            </div>

        <?php endif; ?>

    </main>

</div>

<script>window.FTDP_API = "../../../backend/company/";</script>
<script src="../../../js/comm_common.js"></script>
<script src="../../../js/notifications.js"></script>

</body>
</html>
