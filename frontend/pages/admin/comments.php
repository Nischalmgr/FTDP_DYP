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
    $_SESSION["user_role"] !== "admin"
) {
    header("Location: ../../auth/login.php");
    exit;
}

require_once __DIR__ . "/../../../backend/admin/comments.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Comments | FTDP Admin</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/designer_comm.css">
    <link rel="stylesheet" href="../../css/company.css">
    <link rel="stylesheet" href="../../css/admin.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/admin_sidbar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>Comments</h1>
            <p>Remove comments that break the rules. The author is told why.</p>
        </div>

        <?php if ($admin_flash): ?>
            <div class="comm-alert co-flash <?= $admin_flash["type"] === "success" ? "success" : "error"; ?>">
                <?= htmlspecialchars($admin_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <div class="co-toolbar">
            <span class="ad-count"><?= count($admin_comments); ?> <?= count($admin_comments) === 1 ? "comment" : "comments"; ?></span>
            <?= adminSortControl($sort); ?>
        </div>

        <div class="ad-table-wrap">

            <table class="ad-table">

                <thead>
                    <tr>
                        <th>Comment</th>
                        <th>Written by</th>
                        <th>On design</th>
                        <th>Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($admin_comments as $comment): ?>

                    <tr>

                        <td class="ad-text-cell"><?= nl2br(htmlspecialchars($comment["comment"])); ?></td>

                        <td class="ad-strong"><?= htmlspecialchars($comment["commenter_name"]); ?></td>

                        <td><?= htmlspecialchars($comment["design_title"]); ?></td>

                        <td class="ad-nowrap"><?= date("d M Y", strtotime($comment["created_at"])); ?></td>

                        <td>
                            <div class="ad-actions">
                                <?= adminActionForm(
                                    $csrf_token, "delete_comment", ["comment_id" => $comment["id"]],
                                    "Remove", "danger", "comments.php", $sort,
                                    "Why are you removing this comment? The author will see your reason.",
                                    true
                                ); ?>
                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

            <?php if (count($admin_comments) === 0): ?>
                <div class="ad-empty">No comments yet.</div>
            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>
