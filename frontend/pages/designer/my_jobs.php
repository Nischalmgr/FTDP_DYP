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

require_once __DIR__ . "/../../../backend/designer/my_jobs.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Jobs | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/my_jobs.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/designer_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>My Jobs</h1>
            <p>Every job you've applied to, and where it stands.</p>
        </div>

        <?php if (count($my_jobs_full_data) > 0): ?>

            <div class="dashboard-panel">

                <div class="jobs-table-container">

                    <table class="jobs-table">

                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Company</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Applied</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($my_jobs_full_data as $job): ?>

                                <tr>

                                    <td>
                                        <div class="job-name"><?= htmlspecialchars($job["title"]); ?></div>
                                        <?php if (!empty($job["location"])): ?>
                                            <div class="job-location"><?= htmlspecialchars($job["location"]); ?></div>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= htmlspecialchars($job["company_name"]); ?></td>

                                    <td><?= htmlspecialchars($job["employment_type"] ?? "Not specified"); ?></td>

                                    <td>
                                        <span class="status-badge status-<?= strtolower($job["application_status"]); ?>">
                                            <?= htmlspecialchars(ucfirst($job["application_status"])); ?>
                                        </span>
                                    </td>

                                    <td><?= date("M d, Y", strtotime($job["applied_at"])); ?></td>

                                    <td>
                                        <?php if ($job["application_status"] === "pending"): ?>
                                            <form
                                                method="POST"
                                                action="../../../backend/designer/withdraw_application.php"
                                                onsubmit="return confirm('Withdraw this application?');"
                                            >
                                                <input type="hidden" name="application_id" value="<?= (int) $job["application_id"]; ?>">
                                                <button type="submit" class="my-design-actions delete-btn" style="border:1px solid var(--border); background:var(--white); padding:6px 12px; border-radius:7px; font-size:11px; cursor:pointer;">
                                                    Withdraw
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        <?php else: ?>

            <div class="empty-state">
                <h3>No job applications yet</h3>
                <p>Start exploring fashion opportunities.</p>
                <a href="jobs.php" class="upload-design-btn">Explore Jobs</a>
            </div>

        <?php endif; ?>

    </main>

</div>
<script src="../../../js/designer.js"></script>

</body>
</html>
