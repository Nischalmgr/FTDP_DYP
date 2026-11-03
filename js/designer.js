document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.querySelector(".designer-sidebar");
    const toggleButton = document.getElementById("sidebarToggle");
    const overlay = document.getElementById("sidebarOverlay");

    if (!sidebar || !toggleButton || !overlay) {
        return;
    }

    function openSidebar() {

        sidebar.classList.add("mobile-open");
        overlay.classList.add("active");

        document.body.classList.add("sidebar-open");

        toggleButton.setAttribute("aria-expanded", "true");
        toggleButton.setAttribute("aria-label", "Close navigation");

        toggleButton.innerHTML = "×";
    }

    function closeSidebar() {

        sidebar.classList.remove("mobile-open");
        overlay.classList.remove("active");

        document.body.classList.remove("sidebar-open");

        toggleButton.setAttribute("aria-expanded", "false");
        toggleButton.setAttribute("aria-label", "Open navigation");

        toggleButton.innerHTML = "☰";
    }

    toggleButton.addEventListener("click", function () {

        if (sidebar.classList.contains("mobile-open")) {
            closeSidebar();
        } else {
            openSidebar();
        }

    });

    overlay.addEventListener("click", function () {
        closeSidebar();
    });

    const navLinks = sidebar.querySelectorAll(".designer-nav-link");

    navLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            if (window.innerWidth <= 768) {
                closeSidebar();
            }

        });

    });

    document.addEventListener("keydown", function (event) {

        if (event.key === "Escape") {

            if (sidebar.classList.contains("mobile-open")) {
                closeSidebar();
            }

        }

    });

    window.addEventListener("resize", function () {

        if (window.innerWidth > 768) {
            closeSidebar();
        }

    });

});
