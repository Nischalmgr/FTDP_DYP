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

require_once __DIR__ . "/../../../backend/company/design_feed.php";

$csrf_token = csrfToken();

$feed_flash = $_SESSION["feed_flash"] ?? null;
unset($_SESSION["feed_flash"]);

function feedUrl(int $type, string $q): string
{
    $params = [];

    if ($type > 0) {
        $params["type"] = $type;
    }

    if ($q !== "") {
        $params["q"] = $q;
    }

    return "design_feed.php" . (count($params) ? "?" . http_build_query($params) : "");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token); ?>">

    <title>Explore Designers | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/design_feed.css">
    <link rel="stylesheet" href="../../css/designer_comm.css">
    <link rel="stylesheet" href="../../css/company.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/company_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>Explore Designers</h1>
            <p>Browse work from designers on FTDP. Open a design to see the designer's full portfolio.</p>
        </div>

        <?php if ($feed_flash): ?>
            <div class="comm-alert error co-flash"><?= htmlspecialchars($feed_flash["message"]); ?></div>
        <?php endif; ?>

        <form method="GET" action="design_feed.php" class="co-search">

            <?php if ($filter_type > 0): ?>
                <input type="hidden" name="type" value="<?= (int) $filter_type; ?>">
            <?php endif; ?>

            <input type="search"
                   name="q"
                   class="comm-input"
                   placeholder="Search by design or designer name"
                   value="<?= htmlspecialchars($search); ?>"
                   aria-label="Search designs">

            <button type="submit" class="comm-btn">Search</button>

        </form>

        <div class="filter-bar">

            <a href="<?= feedUrl(0, $search); ?>" class="filter-pill <?= $filter_type === 0 ? 'active' : ''; ?>">
                All
            </a>

            <?php foreach ($all_types as $type): ?>
                <a
                    href="<?= feedUrl((int) $type["id"], $search); ?>"
                    class="filter-pill <?= $filter_type === (int) $type["id"] ? 'active' : ''; ?>"
                >
                    <?= htmlspecialchars($type["name"]); ?>
                </a>
            <?php endforeach; ?>

        </div>

        <?php if (count($design_feed_data) > 0): ?>

            <div class="feed-grid">

                <?php foreach ($design_feed_data as $design): ?>

                    <?php
                        $profile_url = "designer_profile.php?id=" . (int) $design["designer_user_id"]
                                     . "&design=" . (int) $design["id"]
                                     . "#design-" . (int) $design["id"];
                    ?>

                    <div class="feed-card">

                        <a href="<?= $profile_url; ?>" class="feed-thumb-link" aria-label="Open <?= htmlspecialchars($design["designer_name"]); ?>'s portfolio">
                            <img
                                src="../../../<?= htmlspecialchars($design["image"]); ?>"
                                alt="<?= htmlspecialchars($design["title"]); ?>"
                                class="feed-thumb"
                            >
                        </a>

                        <button
                            type="button"
                            class="feed-save-btn <?= $design["saved_by_me"] ? 'saved' : ''; ?>"
                            data-design-id="<?= (int) $design["id"]; ?>"
                            data-remove-on-unsave="false"
                            title="Save"
                        >
                            ☆
                        </button>

                        <div class="feed-body">

                            <h3><?= htmlspecialchars($design["title"]); ?></h3>
                            <div class="feed-designer">
                                by <a href="<?= $profile_url; ?>"><?= htmlspecialchars($design["designer_name"]); ?></a>
                            </div>

                            <?php if (!empty($design["type_names"])): ?>
                                <div class="feed-types">
                                    <?php foreach (explode(", ", $design["type_names"]) as $type): ?>
                                        <span class="type-chip"><?= htmlspecialchars($type); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="feed-footer">

                                <button
                                    type="button"
                                    class="like-btn <?= $design["liked_by_me"] ? 'liked' : ''; ?>"
                                    data-design-id="<?= (int) $design["id"]; ?>"
                                >
                                    <span class="heart">♥</span>
                                    <span class="like-count"><?= (int) $design["like_count"]; ?></span>
                                </button>

                                <button type="button" class="comment-btn" data-design-id="<?= (int) $design["id"]; ?>" aria-expanded="false">
                                    <span class="bubble">✎</span>
                                    <span class="comment-count"><?= (int) $design["comment_count"]; ?></span>
                                </button>

                                <div class="feed-date">
                                    <?= date("M d, Y", strtotime($design["created_at"])); ?>
                                </div>

                            </div>

                            <div class="comment-panel" data-design-id="<?= (int) $design["id"]; ?>" hidden>
                                <div class="comment-list"></div>
                                <div class="comment-form">
                                    <input type="text" class="comment-input" maxlength="1000" placeholder="Write a comment…" aria-label="Write a comment">
                                    <button type="button" class="comment-send">Post</button>
                                </div>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">
                <h3>No designs found</h3>
                <p>Try a different type filter or search.</p>
            </div>

        <?php endif; ?>

    </main>

</div>

<script>window.FTDP_API = "../../../backend/company/";</script>
<script src="../../../js/company_feed.js"></script>
<script src="../../../js/comments.js"></script>

</body>
</html>
