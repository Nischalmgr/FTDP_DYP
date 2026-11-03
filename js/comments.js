document.addEventListener("DOMContentLoaded", function () {

    var meta = document.querySelector('meta[name="csrf-token"]');
    var csrf = meta ? meta.getAttribute("content") : "";
    var api  = (window.FTDP_API || "../../../backend/designer/") + "comment_action.php";
    var root = "../../../";

    function parse(res) {
        return res.json().catch(function () {
            return { success: false, message: "Unexpected server response." };
        });
    }

    function avatar(item) {

        var el = document.createElement("div");

        el.className = "comment-avatar";

        if (item.profile_picture) {

            var img = document.createElement("img");

            img.src = root + item.profile_picture;
            img.alt = "";

            el.appendChild(img);

        } else {
            el.textContent = (item.name || "?").trim().charAt(0).toUpperCase();
        }

        return el;
    }

    function renderComment(item) {

        var row  = document.createElement("div");
        var body = document.createElement("div");
        var head = document.createElement("div");
        var name = document.createElement("strong");
        var time = document.createElement("span");
        var text = document.createElement("p");

        row.className  = "comment-item";
        body.className = "comment-body";
        head.className = "comment-head";
        time.className = "comment-time";

        name.textContent = item.name;
        time.textContent = item.time;
        text.textContent = item.comment;

        head.appendChild(name);
        head.appendChild(time);

        body.appendChild(head);
        body.appendChild(text);

        row.appendChild(avatar(item));
        row.appendChild(body);

        return row;
    }

    function setMessage(list, message) {

        list.innerHTML = "";

        var note = document.createElement("p");

        note.className   = "comment-empty";
        note.textContent = message;

        list.appendChild(note);
    }

    function load(panel) {

        var list = panel.querySelector(".comment-list");

        setMessage(list, "Loading…");

        fetch(api + "?design_id=" + encodeURIComponent(panel.dataset.designId), {
            credentials: "same-origin",
            headers: { "Accept": "application/json" }
        })
            .then(parse)
            .then(function (data) {

                if (!data.success) {
                    setMessage(list, data.message || "Could not load comments.");
                    return;
                }

                if (data.comments.length === 0) {
                    setMessage(list, "No comments yet. Be the first.");
                    return;
                }

                list.innerHTML = "";

                data.comments.forEach(function (item) {
                    list.appendChild(renderComment(item));
                });

                list.scrollTop = list.scrollHeight;
            })
            .catch(function () {
                setMessage(list, "Could not load comments.");
            });
    }

    function send(panel, button) {

        var input = panel.querySelector(".comment-input");
        var list  = panel.querySelector(".comment-list");
        var text  = input.value.trim();

        if (text === "") {
            return;
        }

        var body = new URLSearchParams();

        body.append("design_id", panel.dataset.designId);
        body.append("comment", text);
        body.append("csrf", csrf);

        button.disabled = true;

        fetch(api, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            credentials: "same-origin",
            body: body.toString()
        })
            .then(parse)
            .then(function (data) {

                button.disabled = false;

                if (!data.success) {
                    alert(data.message || "Could not post the comment.");
                    return;
                }

                if (list.querySelector(".comment-empty")) {
                    list.innerHTML = "";
                }

                list.appendChild(renderComment(data.comment));
                list.scrollTop = list.scrollHeight;

                input.value = "";

                var card  = panel.closest(".feed-card");
                var count = card ? card.querySelector(".comment-count") : null;

                if (count) {
                    count.textContent = data.comment_count;
                }
            })
            .catch(function () {
                button.disabled = false;
                alert("Network problem. Please try again.");
            });
    }

    document.querySelectorAll(".comment-btn").forEach(function (btn) {

        btn.addEventListener("click", function () {

            var card  = btn.closest(".feed-card");
            var panel = card ? card.querySelector(".comment-panel") : null;

            if (!panel) {
                return;
            }

            var open = panel.hidden;

            panel.hidden = !open;

            btn.setAttribute("aria-expanded", open ? "true" : "false");

            if (open) {
                load(panel);
                panel.querySelector(".comment-input").focus();
            }
        });
    });

    document.querySelectorAll(".comment-panel").forEach(function (panel) {

        var button = panel.querySelector(".comment-send");
        var input  = panel.querySelector(".comment-input");

        button.addEventListener("click", function () {
            send(panel, button);
        });

        input.addEventListener("keydown", function (event) {

            if (event.key === "Enter") {
                event.preventDefault();
                send(panel, button);
            }
        });
    });
});
