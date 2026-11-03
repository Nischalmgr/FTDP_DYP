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

require_once __DIR__ . "/../../../backend/admin/designs.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Designs | FTDP Admin</title>

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
            <h1>Designs</h1>
            <p>Remove designs that break the rules. The designer is told why.</p>
        </div>

        <?php if ($admin_flash): ?>
            <div class="comm-alert co-flash <?= $admin_flash["type"] === "success" ? "success" : "error"; ?>">
                <?= htmlspecialchars($admin_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <div class="co-toolbar">
            <span class="ad-count"><?= count($admin_designs); ?> <?= count($admin_designs) === 1 ? "design" : "designs"; ?></span>
            <?= adminSortControl($sort); ?>
        </div>

        <div class="ad-table-wrap">

            <table class="ad-table">

                <thead>
                    <tr>
                        <th>Design</th>
                        <th>Designer</th>
                        <th>Likes</th>
                        <th>Comments</th>
                        <th>Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($admin_designs as $design): ?>

                    <tr>

                        <td>
                            <div class="ad-user">
                                <img class="ad-thumb"
                                     src="../../../<?= htmlspecialchars($design["image"]); ?>"
                                     alt="<?= htmlspecialchars($design["title"]); ?>">
                                <span class="ad-strong"><?= htmlspecialchars($design["title"]); ?></span>
                            </div>
                        </td>

                        <td><?= htmlspecialchars($design["designer_name"]); ?></td>

                        <td>♥ <?= (int) $design["like_count"]; ?></td>

                        <td><?= (int) $design["comment_count"]; ?></td>

                        <td class="ad-nowrap"><?= date("d M Y", strtotime($design["created_at"])); ?></td>

                        <td>
                            <div class="ad-actions">
                                <?= adminActionForm(
                                    $csrf_token, "delete_design", ["design_id" => $design["id"]],
                                    "Remove", "danger", "designs.php", $sort,
                                    "Why are you removing this design? The designer will see your reason.",
                                    true
                                ); ?>
                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

            <?php if (count($admin_designs) === 0): ?>
                <div class="ad-empty">No designs have been shared yet.</div>
            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>
