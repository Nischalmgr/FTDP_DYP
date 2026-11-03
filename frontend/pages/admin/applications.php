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

require_once __DIR__ . "/../../../backend/admin/applications.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Applications | FTDP Admin</title>

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
            <h1>Applications</h1>
            <p>Every job application on the platform (view only).</p>
        </div>

        <?php if ($admin_flash): ?>
            <div class="comm-alert co-flash <?= $admin_flash["type"] === "success" ? "success" : "error"; ?>">
                <?= htmlspecialchars($admin_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <div class="co-toolbar">
            <span class="ad-count"><?= count($admin_applications); ?> <?= count($admin_applications) === 1 ? "application" : "applications"; ?></span>
            <?= adminSortControl($sort); ?>
        </div>

        <div class="ad-table-wrap">

            <table class="ad-table">

                <thead>
                    <tr>
                        <th>Designer</th>
                        <th>Job</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Applied</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($admin_applications as $app): ?>

                    <tr>

                        <td class="ad-strong"><?= htmlspecialchars($app["designer_name"]); ?></td>

                        <td><?= htmlspecialchars($app["job_title"]); ?></td>

                        <td><?= htmlspecialchars($app["company_name"]); ?></td>

                        <td>
                            <span class="status-badge status-<?= htmlspecialchars(strtolower($app["application_status"])); ?>">
                                <?= htmlspecialchars(ucfirst($app["application_status"])); ?>
                            </span>
                        </td>

                        <td class="ad-nowrap"><?= date("d M Y", strtotime($app["applied_at"])); ?></td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

            <?php if (count($admin_applications) === 0): ?>
                <div class="ad-empty">No applications yet.</div>
            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>
