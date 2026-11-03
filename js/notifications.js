document.addEventListener("DOMContentLoaded", function () {

    var list          = document.getElementById("notifList");
    var markAllBtn    = document.getElementById("markAllReadBtn");
    var clearAllBtn   = document.getElementById("clearAllBtn");

    var countAllEls    = document.querySelectorAll(".js-count-all");
    var countUnreadEls = document.querySelectorAll(".js-count-unread");

    function send(action, id) {

        var payload = { action: action };

        if (id) {
            payload.id = id;
        }

        return FTDP.post("notification_action.php", payload).then(function (data) {

            if (!data.success) {
                FTDP.toast(data.message || "Something went wrong.", "error");
                return null;
            }

            countAllEls.forEach(function (el) { el.textContent = data.total; });
            countUnreadEls.forEach(function (el) { el.textContent = data.unread; });

            FTDP.setBadges(undefined, data.unread);

            if (markAllBtn) {
                markAllBtn.disabled = data.unread === 0;
            }

            if (clearAllBtn) {
                clearAllBtn.disabled = data.total === 0;
            }

            return data;
        }).catch(function () {
            FTDP.toast("Network problem. Please try again.", "error");
            return null;
        });
    }

    function markItemRead(item) {

        item.classList.remove("unread");

        var dot = item.querySelector(".notif-dot");
        var btn = item.querySelector(".js-notif-read");

        if (dot) { dot.remove(); }
        if (btn) { btn.remove(); }
    }

    function removeItem(item, data) {

        item.classList.add("removing");

        setTimeout(function () {

            item.remove();

            if (list && list.querySelectorAll(".notif-item").length === 0) {
                window.location.reload();
            }
        }, 250);
    }

    if (list) {

        list.addEventListener("click", function (event) {

            var item = event.target.closest(".notif-item");

            if (!item) {
                return;
            }

            var id = item.dataset.id;

            if (event.target.closest(".js-notif-delete")) {

                send("delete", id).then(function (data) {

                    if (!data) { return; }

                    removeItem(item, data);
                });

                return;
            }

            if (event.target.closest(".js-notif-read")) {

                send("mark_read", id).then(function (data) {

                    if (!data) { return; }

                    markItemRead(item);

                    if (new URLSearchParams(window.location.search).get("filter") === "unread") {
                        removeItem(item, data);
                    }
                });

                return;
            }

            if (event.target.closest(".notif-main")) {

                var link = item.dataset.link;

                if (!item.classList.contains("unread")) {

                    if (link) { window.location.href = link; }

                    return;
                }

                send("mark_read", id).then(function () {

                    if (link) {
                        window.location.href = link;
                        return;
                    }

                    markItemRead(item);
                });
            }
        });
    }

    if (markAllBtn) {

        markAllBtn.addEventListener("click", function () {

            send("mark_all_read").then(function (data) {

                if (!data) { return; }

                if (new URLSearchParams(window.location.search).get("filter") === "unread") {
                    window.location.reload();
                    return;
                }

                document.querySelectorAll(".notif-item.unread").forEach(markItemRead);

                FTDP.toast("All notifications marked as read.");
            });
        });
    }

    if (clearAllBtn) {

        clearAllBtn.addEventListener("click", function () {

            if (!window.confirm("Delete all notifications? This can't be undone.")) {
                return;
            }

            send("clear_all").then(function (data) {

                if (data) {
                    window.location.href = "notifications.php";
                }
            });
        });
    }
});
