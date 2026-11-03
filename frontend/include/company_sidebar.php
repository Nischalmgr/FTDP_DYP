<?php
$current_page = basename($_SERVER["PHP_SELF"]);
$user_name    = $_SESSION["user_name"] ?? "Company";

$sb_company_name         = $user_name;
$sb_unread_messages      = 0;
$sb_unread_notifications = 0;
$sb_pending_applicants   = 0;
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

    $sb_stmt = $conn->prepare("
        SELECT
            c.company_name,
            (
                SELECT COUNT(*)
                FROM job_applications ja
                JOIN jobs j ON j.id = ja.job_id
                WHERE j.company_id = c.id AND ja.application_status = 'pending'
            ) AS pending
        FROM companies c
        WHERE c.user_id = ?
        LIMIT 1
    ");

    $sb_stmt->bind_param("i", $sb_uid);
    $sb_stmt->execute();

    $sb_row = $sb_stmt->get_result()->fetch_assoc();

    $sb_stmt->close();

    if ($sb_row) {
        $sb_company_name       = $sb_row["company_name"] ?: $user_name;
        $sb_pending_applicants = (int) $sb_row["pending"];
    }
}

$nav_active = $current_page;

if ($current_page === "designer_profile.php") {
    $nav_active = "design_feed.php";
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
                <?= strtoupper(mb_substr($sb_company_name, 0, 1)); ?>
            <?php endif; ?>
        </div>

        <div class="sidebar-user">
            <h3><?= htmlspecialchars($sb_company_name); ?></h3>
            <span>Hiring Company</span>
        </div>

    </div>

    <nav class="designer-navigation">

        <div class="navigation-label">
            MAIN MENU
        </div>

        <a href="dashboard.php"
           class="designer-nav-link <?= $nav_active == 'dashboard.php' ? 'active' : ''; ?>">
            <span class="nav-icon">⌂</span>
            <span>Dashboard</span>
        </a>

        <a href="design_feed.php"
           class="designer-nav-link <?= $nav_active == 'design_feed.php' ? 'active' : ''; ?>">
            <span class="nav-icon">✺</span>
            <span>Explore Designers</span>
        </a>

        <a href="saved_designs.php"
           class="designer-nav-link <?= $nav_active == 'saved_designs.php' ? 'active' : ''; ?>">
            <span class="nav-icon">♡</span>
            <span>Saved Designs</span>
        </a>

        <div class="navigation-label navigation-spacing">
            HIRING
        </div>

        <a href="jobs.php"
           class="designer-nav-link <?= $nav_active == 'jobs.php' ? 'active' : ''; ?>">
            <span class="nav-icon">▣</span>
            <span>My Job Posts</span>
        </a>

        <a href="job_form.php"
           class="designer-nav-link <?= ($nav_active == 'job_form.php' && empty($_GET['id'])) ? 'active' : ''; ?>">
            <span class="nav-icon">+</span>
            <span>Post a Job</span>
        </a>

        <a href="applicants.php"
           class="designer-nav-link <?= $nav_active == 'applicants.php' ? 'active' : ''; ?>">
            <span class="nav-icon">✓</span>
            <span>Applicants</span>
            <span class="nav-badge"
                  <?= $sb_pending_applicants > 0 ? "" : "hidden"; ?>
                  title="Pending applicants"><?= $sb_pending_applicants > 99 ? "99+" : ($sb_pending_applicants ?: ""); ?></span>
        </a>

        <div class="navigation-label navigation-spacing">
            COMMUNICATION
        </div>

        <a href="messages.php"
           class="designer-nav-link <?= $nav_active == 'messages.php' ? 'active' : ''; ?>">
            <span class="nav-icon">✉</span>
            <span>Messages</span>
            <span class="nav-badge js-msg-badge" <?= $sb_unread_messages > 0 ? "" : "hidden"; ?>><?= $sb_unread_messages > 99 ? "99+" : ($sb_unread_messages ?: ""); ?></span>
        </a>

        <a href="notifications.php"
           class="designer-nav-link <?= $nav_active == 'notifications.php' ? 'active' : ''; ?>">
            <span class="nav-icon">♢</span>
            <span>Notifications</span>
            <span class="nav-badge js-notif-badge" <?= $sb_unread_notifications > 0 ? "" : "hidden"; ?>><?= $sb_unread_notifications > 99 ? "99+" : ($sb_unread_notifications ?: ""); ?></span>
        </a>

        <div class="navigation-label navigation-spacing">
            ACCOUNT
        </div>

        <a href="profile.php"
           class="designer-nav-link <?= $nav_active == 'profile.php' ? 'active' : ''; ?>">
            <span class="nav-icon">○</span>
            <span>Company Profile</span>
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

    var toggle  = document.getElementById("sidebarToggle");
    var sidebar = document.querySelector(".designer-sidebar");
    var overlay = document.getElementById("sidebarOverlay");

    function setOpen(open) {
        sidebar.classList.toggle("mobile-open", open);
        overlay.classList.toggle("active", open);
        document.body.classList.toggle("sidebar-open", open);
        toggle.setAttribute("aria-expanded", open ? "true" : "false");
    }

    if (toggle && sidebar && overlay) {

        toggle.addEventListener("click", function () {
            setOpen(!sidebar.classList.contains("mobile-open"));
        });

        overlay.addEventListener("click", function () {
            setOpen(false);
        });

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") { setOpen(false); }
        });
    }

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

        fetch("../../../backend/company/message_action.php?action=unread_counts", {
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
