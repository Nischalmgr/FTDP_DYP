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

require_once __DIR__ . "/../../../backend/designer/saved_jobs.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Saved Jobs | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/jobs.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/designer_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>Saved Jobs</h1>
            <p>Jobs you've bookmarked to apply to later.</p>
        </div>

        <?php if (count($saved_jobs_data) > 0): ?>

            <div class="jobs-list">

                <?php foreach ($saved_jobs_data as $job): ?>

                    <div class="job-card">

                        <div class="job-card-main">

                            <h3><?= htmlspecialchars($job["title"]); ?></h3>
                            <div class="job-card-company"><?= htmlspecialchars($job["company_name"]); ?></div>

                            <div class="job-card-meta">

                                <?php if (!empty($job["location"])): ?>
                                    <span>📍 <?= htmlspecialchars($job["location"]); ?></span>
                                <?php endif; ?>

                                <?php if (!empty($job["employment_type"])): ?>
                                    <span><?= htmlspecialchars($job["employment_type"]); ?></span>
                                <?php endif; ?>

                                <?php if (!empty($job["salary"])): ?>
                                    <span><?= htmlspecialchars($job["salary"]); ?></span>
                                <?php endif; ?>

                            </div>

                        </div>

                        <div class="job-card-actions">

                            <button
                                type="button"
                                class="save-job-btn saved"
                                data-job-id="<?= (int) $job["id"]; ?>"
                                data-remove-on-unsave="true"
                                title="Remove from saved"
                            >
                                ☆
                            </button>

                            <?php if ($job["has_applied"]): ?>
                                <button type="button" class="apply-btn applied" disabled>Applied</button>
                            <?php else: ?>
                                <a href="jobs.php" class="apply-btn" style="display:inline-flex; align-items:center; text-decoration:none;">
                                    Go to Job
                                </a>
                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">
                <h3>No saved jobs yet</h3>
                <p>Browse Find Jobs and save the ones you're interested in.</p>
                <a href="jobs.php" class="upload-design-btn">Find Jobs</a>
            </div>

        <?php endif; ?>

    </main>

</div>

<script src="../../../js/jobs.js"></script>
<script src="../../../js/designer.js"></script>

</body>
</html>
