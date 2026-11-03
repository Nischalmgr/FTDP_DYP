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
    $_SESSION["user_role"] !== "designer"
) {
    header("Location: ../../auth/login.php");
    exit;
}

require_once __DIR__ . "/../../../backend/designer/messages.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token); ?>">

    <title>Messages | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/designer_comm.css">
    <link rel="stylesheet" href="../../css/messages.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/designer_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>Messages</h1>
            <p>Talk to designers and companies on FTDP.</p>
        </div>

        <div class="chat-shell comm-card <?= $active_partner ? "show-thread" : ""; ?>" id="chatShell">

            <aside class="chat-list" aria-label="Conversations">

                <div class="chat-search">

                    <input type="search"
                           class="comm-input"
                           id="userSearch"
                           placeholder="Find a designer or company"
                           autocomplete="off"
                           aria-label="Search people to message">

                    <div class="chat-search-results" id="searchResults" hidden></div>

                </div>

                <div class="chat-conversations" id="conversationList"></div>

            </aside>

            <section class="chat-thread" aria-label="Conversation">

                <div class="chat-empty" id="chatEmpty">

                    <div class="chat-empty-icon" aria-hidden="true">✉</div>

                    <h2>Your messages</h2>

                    <p>Pick a conversation, or search for a designer or company to start a new one.</p>

                </div>

                <div class="chat-panel" id="chatPanel" hidden>

                    <header class="chat-header">

                        <button type="button"
                                class="comm-icon-btn chat-back"
                                id="chatBack"
                                aria-label="Back to conversations">←</button>

                        <div id="chatHeaderAvatar"></div>

                        <div class="chat-header-text">
                            <div class="chat-header-name" id="chatHeaderName"></div>
                            <div class="chat-header-sub" id="chatHeaderSub"></div>
                        </div>

                    </header>

                    <div class="chat-messages" id="messageList" aria-live="polite"></div>

                    <div class="chat-composer">

                        <textarea id="messageInput"
                                  class="comm-input"
                                  rows="1"
                                  maxlength="2000"
                                  placeholder="Write a message…"
                                  aria-label="Message"></textarea>

                        <button type="button" class="comm-btn" id="sendBtn">Send</button>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

<script type="application/json" id="chatData"><?= $chat_boot_json; ?></script>

<script src="../../../js/comm_common.js"></script>
<script src="../../../js/messages.js"></script>

</body>
</html>
