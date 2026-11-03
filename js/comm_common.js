(function () {

    var FTDP = window.FTDP = window.FTDP || {};

    var meta = document.querySelector('meta[name="csrf-token"]');

    FTDP.csrf   = meta ? meta.getAttribute("content") : "";

    FTDP.api    = window.FTDP_API || "../../../backend/designer/";
    FTDP.root   = "../../../";

    function parseJson(res) {
        return res.json().catch(function () {
            return { success: false, message: "Unexpected server response." };
        });
    }

    FTDP.post = function (endpoint, data) {

        var body = new URLSearchParams();

        Object.keys(data).forEach(function (key) {
            body.append(key, data[key]);
        });

        body.append("csrf", FTDP.csrf);

        return fetch(FTDP.api + endpoint, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            credentials: "same-origin",
            body: body.toString()
        }).then(parseJson);
    };

    FTDP.get = function (endpoint, params) {

        var query = new URLSearchParams(params || {}).toString();

        return fetch(FTDP.api + endpoint + "?" + query, {
            credentials: "same-origin",
            headers: { "Accept": "application/json" }
        }).then(parseJson);
    };

    function paintBadge(selector, count) {

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

    FTDP.setBadges = function (messages, notifications) {

        if (typeof messages === "number") {
            paintBadge(".js-msg-badge", messages);
        }

        if (typeof notifications === "number") {
            paintBadge(".js-notif-badge", notifications);
        }
    };

    FTDP.toast = function (message, type) {

        var wrap = document.querySelector(".comm-toast-wrap");

        if (!wrap) {
            wrap = document.createElement("div");
            wrap.className = "comm-toast-wrap";
            wrap.setAttribute("role", "status");
            wrap.setAttribute("aria-live", "polite");
            document.body.appendChild(wrap);
        }

        var toast = document.createElement("div");

        toast.className = "comm-toast" + (type === "error" ? " error" : "");
        toast.textContent = message;

        wrap.appendChild(toast);

        setTimeout(function () {
            toast.remove();
        }, 3500);
    };

    FTDP.initial = function (name) {
        return (name || "?").trim().charAt(0).toUpperCase() || "?";
    };

    FTDP.avatar = function (name, picture, role, size) {

        var el = document.createElement("div");

        el.className = "comm-avatar" + (role === "company" ? " company" : "");

        if (size) {
            el.style.width  = size + "px";
            el.style.height = size + "px";
        }

        if (picture) {

            var img = document.createElement("img");

            img.src = FTDP.root + picture;
            img.alt = "";
            img.loading = "lazy";

            img.onerror = function () {
                img.remove();
                el.textContent = FTDP.initial(name);
            };

            el.appendChild(img);

        } else {
            el.textContent = FTDP.initial(name);
        }

        return el;
    };

})();
