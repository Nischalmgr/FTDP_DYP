<?php
$current_page = basename($_SERVER["PHP_SELF"]);
$user_name = $_SESSION["user_name"] ?? "Designer";

$sb_unread_messages      = 0;
$sb_unread_notifications = 0;
$sb_avatar               = null;

if (
    isset($conn) &&
    $conn instanceof mysqli &&
    isset($_SESSION["user_id"])
) {
    require_once __DIR__ . "/../../backend/designer/comm_helpers.php";

    $sb_uid                  = (int) $_SESSION["user_id"];
    $sb_unread_messages      = getUnreadMessageCount($conn, $sb_uid);
    $sb_unread_notifications = getUnreadNotificationCount($conn, $sb_uid);
    $sb_avatar               = getUserProfilePicture($conn, $sb_uid);
}
?>

<button
    type="button"
    class="sidebar-toggle"
    id="sidebarToggle"
    aria-label="Open navigation"
    aria-expanded="false"
>
    ☰
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="designer-sidebar">

    <a href="dashboard.php" class="logo">
        <img src="../../assets/icons/logo.jpg" alt="FTDP" class="logo-img">
    </a>

    <div class="sidebar-profile">

        <div class="sidebar-avatar">
            <?php if ($sb_avatar): ?>
                <img src="../../../<?= htmlspecialchars($sb_avatar); ?>" alt="">
            <?php else: ?>
                <?= strtoupper(mb_substr($user_name, 0, 1)); ?>
            <?php endif; ?>
        </div>

        <div class="sidebar-user">
            <h3><?= htmlspecialchars($user_name); ?></h3>
            <span>Fashion Designer</span>
        </div>

    </div>

    <nav class="designer-navigation">

        <div class="navigation-label">
            MAIN MENU
        </div>

        <a href="dashboard.php"
           class="designer-nav-link <?= $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <span class="nav-icon">⌂</span>
            <span>Dashboard</span>
        </a>

        <a href="my_designs.php"
           class="designer-nav-link <?= $current_page == 'my_designs.php' ? 'active' : ''; ?>">
            <span class="nav-icon">◆</span>
            <span>My Designs</span>
        </a>

        <a href="design_feed.php"
           class="designer-nav-link <?= $current_page == 'design_feed.php' ? 'active' : ''; ?>">
            <span class="nav-icon">✺</span>
            <span>Explore Designs</span>
        </a>

        <a href="upload_design.php"
           class="designer-nav-link <?= $current_page == 'upload_design.php' ? 'active' : ''; ?>">
            <span class="nav-icon">+</span>
            <span>Design</span>
        </a>

        <a href="jobs.php"
           class="designer-nav-link <?= $current_page == 'jobs.php' ? 'active' : ''; ?>">
            <span class="nav-icon">▣</span>
            <span>Find Jobs</span>
        </a>

        <a href="my_jobs.php"
           class="designer-nav-link <?= $current_page == 'my_jobs.php' ? 'active' : ''; ?>">
            <span class="nav-icon">✓</span>
            <span>My Jobs</span>
        </a>

        <a href="saved_designs.php"
           class="designer-nav-link <?= $current_page == 'saved_designs.php' ? 'active' : ''; ?>">
            <span class="nav-icon">♡</span>
            <span>Saved Designs</span>
        </a>

        <a href="saved_jobs.php"
           class="designer-nav-link <?= $current_page == 'saved_jobs.php' ? 'active' : ''; ?>">
            <span class="nav-icon">☆</span>
            <span>Saved Jobs</span>
        </a>

        <div class="navigation-label navigation-spacing">
            COMMUNICATION
        </div>

        <a href="messages.php"
           class="designer-nav-link <?= $current_page == 'messages.php' ? 'active' : ''; ?>">
            <span class="nav-icon">✉</span>
            <span>Messages</span>
            <span class="nav-badge js-msg-badge" <?= $sb_unread_messages > 0 ? "" : "hidden"; ?>><?= $sb_unread_messages > 99 ? "99+" : ($sb_unread_messages ?: ""); ?></span>
        </a>

        <a href="notifications.php"
           class="designer-nav-link <?= $current_page == 'notifications.php' ? 'active' : ''; ?>">
            <span class="nav-icon">♢</span>
            <span>Notifications</span>
            <span class="nav-badge js-notif-badge" <?= $sb_unread_notifications > 0 ? "" : "hidden"; ?>><?= $sb_unread_notifications > 99 ? "99+" : ($sb_unread_notifications ?: ""); ?></span>
        </a>

        <div class="navigation-label navigation-spacing">
            ACCOUNT
        </div>

        <a href="profile.php"
           class="designer-nav-link <?= $current_page == 'profile.php' ? 'active' : ''; ?>">
            <span class="nav-icon">○</span>
            <span>My Profile</span>
        </a>

        <a href="../../auth/logout.php"
           class="designer-nav-link logout-link">
            <span class="nav-icon">↪</span>
            <span>Logout</span>
        </a>

    </nav>

</aside>

<script>

(function () {

    function paint(selector, count) {

        document.querySelectorAll(selector).forEach(function (el) {

            if (count > 0) {
                el.textContent = count > 99 ? "99+" : String(count);
                el.hidden = false;
            } else {
                el.textContent = "";
                el.hidden = true;
            }
        });
    }

    function refresh() {

        if (document.hidden) {
            return;
        }

        fetch("../../../backend/designer/message_action.php?action=unread_counts", {
            credentials: "same-origin",
            headers: { "Accept": "application/json" }
        })
            .then(function (res) { return res.json(); })
            .then(function (data) {

                if (!data || !data.success) {
                    return;
                }

                paint(".js-msg-badge", data.unread_messages);
                paint(".js-notif-badge", data.unread_notifications);
            })
            .catch(function () {});
    }

    refresh();
    setInterval(refresh, 30000);

    document.addEventListener("visibilitychange", refresh);
})();
</script>
