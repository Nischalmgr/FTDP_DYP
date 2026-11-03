document.addEventListener("DOMContentLoaded", function () {

    var POLL_MS = 4000;

    var boot = JSON.parse(document.getElementById("chatData").textContent);

    var shell         = document.getElementById("chatShell");
    var listEl        = document.getElementById("conversationList");
    var searchInput   = document.getElementById("userSearch");
    var searchResults = document.getElementById("searchResults");
    var emptyEl       = document.getElementById("chatEmpty");
    var panelEl       = document.getElementById("chatPanel");
    var headerAvatar  = document.getElementById("chatHeaderAvatar");
    var headerName    = document.getElementById("chatHeaderName");
    var headerSub     = document.getElementById("chatHeaderSub");
    var messagesEl    = document.getElementById("messageList");
    var input         = document.getElementById("messageInput");
    var sendBtn       = document.getElementById("sendBtn");
    var backBtn       = document.getElementById("chatBack");

    var state = {
        me:            boot.me,
        partner:       null,
        conversations: boot.conversations || [],
        lastId:        0,
        lastDay:       null,
        seen:          {},
        noMessagesEl:  null,
        polling:       false,
        sending:       false,
        openToken:     0
    };

    function roleLabel(role) {
        return role === "company" ? "Company" : "Designer";
    }

    function shortAgo(seconds) {

        if (seconds < 60)       { return "now"; }
        if (seconds < 3600)     { return Math.floor(seconds / 60) + "m"; }
        if (seconds < 86400)    { return Math.floor(seconds / 3600) + "h"; }
        if (seconds < 604800)   { return Math.floor(seconds / 86400) + "d"; }

        return Math.floor(seconds / 604800) + "w";
    }

    function el(tag, className, text) {

        var node = document.createElement(tag);

        if (className) { node.className = className; }
        if (text !== undefined) { node.textContent = text; }

        return node;
    }

    function autosize() {
        input.style.height = "auto";
        input.style.height = Math.min(input.scrollHeight, 130) + "px";
    }

    function applyExtras(data) {

        if (data.conversations) {
            state.conversations = data.conversations;
            renderConversations();
        }

        FTDP.setBadges(data.unread_messages, data.unread_notifications);
    }

    function renderConversations() {

        listEl.innerHTML = "";

        if (state.conversations.length === 0) {

            listEl.appendChild(el(
                "div",
                "chat-list-empty",
                "No conversations yet. Search above to message a designer or company."
            ));

            return;
        }

        state.conversations.forEach(function (c) {

            var isActive = state.partner && state.partner.id === c.id;

            var btn = el(
                "button",
                "chat-convo" + (isActive ? " active" : "") + (c.unread > 0 ? " has-unread" : "")
            );

            btn.type = "button";
            btn.dataset.id = c.id;

            btn.appendChild(FTDP.avatar(c.name, c.profile_picture, c.role, 42));

            var body   = el("div", "chat-convo-body");
            var top    = el("div", "chat-convo-top");
            var bottom = el("div", "chat-convo-bottom");

            top.appendChild(el("span", "chat-convo-name", c.name));
            top.appendChild(el("span", "chat-convo-time", shortAgo(c.age_seconds)));

            bottom.appendChild(el(
                "span",
                "chat-convo-preview",
                (c.last_is_mine ? "You: " : "") + c.last_message
            ));

            if (c.unread > 0) {
                bottom.appendChild(el("span", "chat-unread", c.unread > 99 ? "99+" : String(c.unread)));
            }

            body.appendChild(top);
            body.appendChild(bottom);

            btn.appendChild(body);

            btn.addEventListener("click", function () {
                openConversation(c.id, true);
            });

            listEl.appendChild(btn);
        });
    }

    function setPartner(partner) {

        state.partner = partner;

        emptyEl.hidden = true;
        panelEl.hidden = false;

        headerAvatar.innerHTML = "";
        headerAvatar.appendChild(FTDP.avatar(partner.name, partner.profile_picture, partner.role, 42));

        headerName.textContent = partner.name;

        headerSub.textContent = partner.headline
            ? roleLabel(partner.role) + " · " + partner.headline
            : roleLabel(partner.role);
    }

    function resetMessages() {

        messagesEl.innerHTML = "";

        state.lastId       = 0;
        state.lastDay      = null;
        state.seen         = {};
        state.noMessagesEl = null;
    }

    function isNearBottom() {
        return messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight < 90;
    }

    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function appendMessage(m) {

        if (state.seen[m.id]) {
            return;
        }

        state.seen[m.id] = true;

        if (state.noMessagesEl) {
            state.noMessagesEl.remove();
            state.noMessagesEl = null;
        }

        if (m.day_label !== state.lastDay) {
            messagesEl.appendChild(el("div", "chat-day", m.day_label));
            state.lastDay = m.day_label;
        }

        var bubble = el("div", "chat-bubble " + (m.mine ? "mine" : "theirs"));

        bubble.appendChild(document.createTextNode(m.message));
        bubble.appendChild(el("span", "chat-bubble-time", m.time));

        messagesEl.appendChild(bubble);

        if (m.id > state.lastId) {
            state.lastId = m.id;
        }
    }

    function showEmptyThreadHint() {

        if (Object.keys(state.seen).length > 0 || !state.partner) {
            return;
        }

        state.noMessagesEl = el(
            "div",
            "chat-no-messages",
            "No messages yet. Say hello to " + state.partner.name + "!"
        );

        messagesEl.appendChild(state.noMessagesEl);
    }

    function showThreadPane() {
        shell.classList.add("show-thread");
    }

    function closeConversation(pushHistory) {

        state.openToken++;
        state.partner = null;

        resetMessages();

        panelEl.hidden = true;
        emptyEl.hidden = false;

        shell.classList.remove("show-thread");

        renderConversations();

        if (pushHistory) {
            history.pushState({}, "", "messages.php");
        }
    }

    function openConversation(partnerId, pushHistory) {

        var token = ++state.openToken;

        FTDP.get("message_action.php", {
            action:   "poll",
            "with":   partnerId,
            after_id: 0
        }).then(function (data) {

            if (token !== state.openToken) {
                return;
            }

            if (!data.success || !data.partner) {
                FTDP.toast("Couldn't open that conversation.", "error");
                closeConversation(false);
                return;
            }

            resetMessages();
            setPartner(data.partner);

            (data.messages || []).forEach(appendMessage);

            showEmptyThreadHint();
            scrollToBottom();
            showThreadPane();

            applyExtras(data);

            if (pushHistory) {
                history.pushState({ "with": partnerId }, "", "messages.php?with=" + partnerId);
            }

            if (window.innerWidth > 800) {
                input.focus();
            }

        }).catch(function () {
            FTDP.toast("Network problem. Please try again.", "error");
        });
    }

    function send() {

        var text = input.value.trim();

        if (!text || !state.partner || state.sending) {
            return;
        }

        state.sending = true;
        sendBtn.disabled = true;

        FTDP.post("message_action.php", {
            action:      "send",
            receiver_id: state.partner.id,
            message:     text
        }).then(function (data) {

            if (!data.success) {
                FTDP.toast(data.message || "Could not send message.", "error");
                return;
            }

            input.value = "";
            autosize();

            appendMessage(data.message);
            scrollToBottom();

            poll();

        }).catch(function () {
            FTDP.toast("Network problem. Message not sent.", "error");
        }).then(function () {

            state.sending = false;
            sendBtn.disabled = false;
            input.focus();
        });
    }

    sendBtn.addEventListener("click", send);

    input.addEventListener("keydown", function (event) {

        if (event.key === "Enter" && !event.shiftKey && !event.isComposing) {
            event.preventDefault();
            send();
        }
    });

    input.addEventListener("input", autosize);

    backBtn.addEventListener("click", function () {
        closeConversation(true);
    });

    function poll() {

        if (state.polling || document.hidden) {
            return;
        }

        state.polling = true;

        var token  = state.openToken;
        var params = { action: "poll" };

        if (state.partner) {
            params["with"]   = state.partner.id;
            params.after_id  = state.lastId;
        }

        FTDP.get("message_action.php", params).then(function (data) {

            state.polling = false;

            if (!data.success || token !== state.openToken) {
                return;
            }

            if (state.partner && data.messages && data.messages.length) {

                var stick = isNearBottom();

                data.messages.forEach(appendMessage);

                if (stick) {
                    scrollToBottom();
                }
            }

            applyExtras(data);

        }).catch(function () {
            state.polling = false;
        });
    }

    setInterval(poll, POLL_MS);

    document.addEventListener("visibilitychange", function () {

        if (!document.hidden) {
            poll();
        }
    });

    var searchTimer = null;

    function hideResults() {
        searchResults.hidden = true;
        searchResults.innerHTML = "";
    }

    function renderResults(people) {

        searchResults.innerHTML = "";

        if (people.length === 0) {
            searchResults.appendChild(el("div", "chat-search-empty", "No one found."));
            searchResults.hidden = false;
            return;
        }

        people.forEach(function (p) {

            var btn = el("button", "chat-person");

            btn.type = "button";

            btn.appendChild(FTDP.avatar(p.name, p.profile_picture, p.role, 38));

            var text = el("div", "chat-person-text");

            text.appendChild(el("div", "chat-person-name", p.name));
            text.appendChild(el(
                "div",
                "chat-person-sub",
                p.headline ? roleLabel(p.role) + " · " + p.headline : roleLabel(p.role)
            ));

            btn.appendChild(text);

            btn.addEventListener("click", function () {

                hideResults();
                searchInput.value = "";

                openConversation(p.id, true);
            });

            searchResults.appendChild(btn);
        });

        searchResults.hidden = false;
    }

    searchInput.addEventListener("input", function () {

        clearTimeout(searchTimer);

        var q = searchInput.value.trim();

        if (q.length < 2) {
            hideResults();
            return;
        }

        searchTimer = setTimeout(function () {

            FTDP.get("message_action.php", { action: "search", q: q }).then(function (data) {

                if (searchInput.value.trim() !== q) {
                    return;
                }

                renderResults(data.people || []);
            });

        }, 250);
    });

    searchInput.addEventListener("keydown", function (event) {

        if (event.key === "Escape") {
            hideResults();
            searchInput.blur();
        }
    });

    document.addEventListener("click", function (event) {

        if (!event.target.closest(".chat-search")) {
            hideResults();
        }
    });

    window.addEventListener("popstate", function () {

        var id = parseInt(new URLSearchParams(window.location.search).get("with"), 10);

        if (id > 0) {
            openConversation(id, false);
        } else {
            closeConversation(false);
        }
    });

    if (boot.partner) {

        setPartner(boot.partner);

        (boot.messages || []).forEach(appendMessage);

        showEmptyThreadHint();
        scrollToBottom();
        showThreadPane();
    }

    renderConversations();

    poll();
});
