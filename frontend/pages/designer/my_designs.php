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

require_once __DIR__ . "/../../../backend/designer/my_designs.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Designs | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/my_designs.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/designer_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>My Designs</h1>
            <p>Everything you've posted, in one place.</p>
        </div>

        <?php if (count($my_designs_data) > 0): ?>

            <div class="designs-grid">

                <?php foreach ($my_designs_data as $design): ?>

                    <div class="my-design-card">

                        <img
                            src="../../../<?= htmlspecialchars($design["image"]); ?>"
                            alt="<?= htmlspecialchars($design["title"]); ?>"
                            class="my-design-thumb"
                        >

                        <div class="my-design-body">

                            <h3><?= htmlspecialchars($design["title"]); ?></h3>

                            <div class="my-design-date">
                                <?= date("M d, Y", strtotime($design["created_at"])); ?>
                            </div>

                            <?php if (!empty($design["type_names"])): ?>
                                <div class="my-design-types">
                                    <?php foreach (explode(", ", $design["type_names"]) as $type): ?>
                                        <span class="type-chip"><?= htmlspecialchars($type); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="my-design-meta">

                                <div class="my-design-likes">
                                    ♡ <?= (int) $design["like_count"]; ?> likes
                                </div>

                                <div class="my-design-actions">

                                    <form
                                        method="POST"
                                        action="../../../backend/designer/delete_design.php"
                                        onsubmit="return confirm('Delete this design? This cannot be undone.');"
                                    >
                                        <input type="hidden" name="design_id" value="<?= (int) $design["id"]; ?>">
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">
                <h3>You haven't uploaded any designs yet</h3>
                <p>Share your first piece with the community.</p>
                <a href="upload_design.php" class="upload-design-btn">+ Upload Design</a>
            </div>

        <?php endif; ?>

    </main>

</div>
<script src="../../../js/designer.js"></script>

</body>
</html>
