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

require_once __DIR__ . "/../../../backend/company/saved_designs.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Saved Designs | FTDP</title>

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
            <h1>Saved Designs</h1>
            <p>Designs you've bookmarked while scouting talent.</p>
        </div>

        <?php if (count($saved_designs_data) > 0): ?>

            <div class="feed-grid">

                <?php foreach ($saved_designs_data as $design): ?>

                    <?php
                        $profile_url = "designer_profile.php?id=" . (int) $design["designer_user_id"]
                                     . "&design=" . (int) $design["id"]
                                     . "#design-" . (int) $design["id"];
                    ?>

                    <div class="feed-card">

                        <a href="<?= $profile_url; ?>" class="feed-thumb-link">
                            <img
                                src="../../../<?= htmlspecialchars($design["image"]); ?>"
                                alt="<?= htmlspecialchars($design["title"]); ?>"
                                class="feed-thumb"
                            >
                        </a>

                        <button
                            type="button"
                            class="feed-save-btn saved"
                            data-design-id="<?= (int) $design["id"]; ?>"
                            data-remove-on-unsave="true"
                            title="Remove from saved"
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
                                <div class="my-design-likes">♡ <?= (int) $design["like_count"]; ?> likes</div>
                                <div class="feed-date">
                                    <?= date("M d, Y", strtotime($design["created_at"])); ?>
                                </div>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">
                <h3>No saved designs yet</h3>
                <p>Explore designers and save the work that stands out.</p>
                <a href="design_feed.php" class="upload-design-btn">Explore Designers</a>
            </div>

        <?php endif; ?>

    </main>

</div>

<script src="../../../js/company_feed.js"></script>

</body>
</html>
