<?php
$current_page = basename($_SERVER["PHP_SELF"]);
$user_name    = $_SESSION["user_name"] ?? "Admin";
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
            <?= htmlspecialchars(strtoupper(mb_substr($user_name, 0, 1))); ?>
        </div>

        <div class="sidebar-user">
            <h3><?= htmlspecialchars($user_name); ?></h3>
            <span>Administrator</span>
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

        <a href="users.php"
           class="designer-nav-link <?= $current_page == 'users.php' ? 'active' : ''; ?>">
            <span class="nav-icon">○</span>
            <span>Users</span>
        </a>

        <div class="navigation-label navigation-spacing">
            CONTENT
        </div>

        <a href="designs.php"
           class="designer-nav-link <?= $current_page == 'designs.php' ? 'active' : ''; ?>">
            <span class="nav-icon">✺</span>
            <span>Designs</span>
        </a>

        <a href="comments.php"
           class="designer-nav-link <?= $current_page == 'comments.php' ? 'active' : ''; ?>">
            <span class="nav-icon">✉</span>
            <span>Comments</span>
        </a>

        <a href="design_types.php"
           class="designer-nav-link <?= $current_page == 'design_types.php' ? 'active' : ''; ?>">
            <span class="nav-icon">◇</span>
            <span>Design Types</span>
        </a>

        <div class="navigation-label navigation-spacing">
            HIRING
        </div>

        <a href="jobs.php"
           class="designer-nav-link <?= $current_page == 'jobs.php' ? 'active' : ''; ?>">
            <span class="nav-icon">▣</span>
            <span>Jobs</span>
        </a>

        <a href="applications.php"
           class="designer-nav-link <?= $current_page == 'applications.php' ? 'active' : ''; ?>">
            <span class="nav-icon">✓</span>
            <span>Applications</span>
        </a>

        <div class="navigation-label navigation-spacing">
            COMMUNICATION
        </div>

        <a href="announcements.php"
           class="designer-nav-link <?= $current_page == 'announcements.php' ? 'active' : ''; ?>">
            <span class="nav-icon">♢</span>
            <span>Announcements</span>
        </a>

        <div class="navigation-label navigation-spacing">
            ACCOUNT
        </div>

        <a href="profile.php"
           class="designer-nav-link <?= $current_page == 'profile.php' ? 'active' : ''; ?>">
            <span class="nav-icon">◍</span>
            <span>Admin Profile</span>
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
})();
</script>
