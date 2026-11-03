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

require_once __DIR__ . "/../../../backend/designer/jobs.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Find Jobs | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/jobs.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/designer_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>Find Jobs</h1>
            <p>Fashion opportunities from companies on FTDP.</p>
        </div>

        <?php if (count($jobs_data) > 0): ?>

            <div class="jobs-list">

                <?php foreach ($jobs_data as $job): ?>

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

                                <?php if (!empty($job["deadline"])): ?>
                                    <span>Deadline: <?= date("M d, Y", strtotime($job["deadline"])); ?></span>
                                <?php endif; ?>

                            </div>

                        </div>

                        <div class="job-card-actions">

                            <button
                                type="button"
                                class="save-job-btn <?= $job["is_saved"] ? 'saved' : ''; ?>"
                                data-job-id="<?= (int) $job["id"]; ?>"
                                data-remove-on-unsave="false"
                                title="Save job"
                            >
                                ☆
                            </button>

                            <?php if ($job["has_applied"]): ?>
                                <button type="button" class="apply-btn applied" disabled>
                                    <?= htmlspecialchars(ucfirst($job["application_status"])); ?>
                                </button>
                            <?php else: ?>
                                <button
                                    type="button"
                                    class="apply-btn"
                                    data-job-id="<?= (int) $job["id"]; ?>"
                                    data-job-title="<?= htmlspecialchars($job["title"]); ?>"
                                >
                                    Apply
                                </button>
                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">
                <h3>No jobs available right now</h3>
                <p>Check back soon for new opportunities.</p>
            </div>

        <?php endif; ?>

    </main>

</div>

<div class="apply-modal-overlay" id="applyModalOverlay">

    <div class="apply-modal">

        <h3>Apply to <span id="applyModalJobTitle"></span></h3>

        <form
            method="POST"
            action="../../../backend/designer/apply_job.php"
            enctype="multipart/form-data"
        >

            <input type="hidden" name="job_id" id="applyModalJobId">

            <textarea name="cover_letter" placeholder="Cover letter (optional)"></textarea>

            <label style="font-size: 12px; color: var(--light-text);">
                Resume (PDF, DOC, DOCX — optional)
            </label>
            <input type="file" name="resume" accept=".pdf,.doc,.docx">

            <div class="apply-modal-actions">
                <button type="button" class="apply-modal-cancel" id="applyModalCancel">Cancel</button>
                <button type="submit" class="apply-modal-submit">Submit Application</button>
            </div>

        </form>

    </div>

</div>

<script src="../../../js/jobs.js"></script>
<script src="../../../js/designer.js"></script>

</body>
</html>
