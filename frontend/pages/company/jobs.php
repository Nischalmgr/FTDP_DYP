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

require_once __DIR__ . "/../../../backend/company/jobs.php";

$csrf_token = csrfToken();

function jobsTabUrl(string $status): string
{
    return $status === "all" ? "jobs.php" : "jobs.php?status=" . $status;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Job Posts | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/designer_comm.css">
    <link rel="stylesheet" href="../../css/company.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/company_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>My Job Posts</h1>
            <p>Post roles, see who applied, and close them when they're filled.</p>
        </div>

        <?php if ($jobs_flash): ?>
            <div class="comm-alert co-flash <?= $jobs_flash["type"] === "success" ? "success" : "error"; ?>" role="status">
                <?= htmlspecialchars($jobs_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <div class="co-toolbar">

            <div class="co-tabs">

                <a href="<?= jobsTabUrl("all"); ?>" class="co-tab <?= $job_filter === "all" ? "active" : ""; ?>">
                    All <span class="co-tab-count"><?= (int) $job_counts["total"]; ?></span>
                </a>

                <a href="<?= jobsTabUrl("open"); ?>" class="co-tab <?= $job_filter === "open" ? "active" : ""; ?>">
                    Open <span class="co-tab-count"><?= (int) $job_counts["open_count"]; ?></span>
                </a>

                <a href="<?= jobsTabUrl("closed"); ?>" class="co-tab <?= $job_filter === "closed" ? "active" : ""; ?>">
                    Closed <span class="co-tab-count"><?= (int) $job_counts["closed_count"]; ?></span>
                </a>

            </div>

            <a href="job_form.php" class="comm-btn co-btn-link">Post a job</a>

        </div>

        <?php if (count($company_jobs_data) > 0): ?>

            <div class="co-jobs">

                <?php foreach ($company_jobs_data as $job): ?>

                    <?php $is_open = $job["status"] === "open"; ?>

                    <div class="co-job <?= $is_open ? "" : "closed"; ?>">

                        <div class="co-job-main">

                            <h3 class="co-job-title">
                                <?= htmlspecialchars($job["title"]); ?>
                                <span class="co-chip <?= $is_open ? "open" : "closed"; ?>">
                                    <?= $is_open ? "Open" : "Closed"; ?>
                                </span>
                            </h3>

                            <div class="co-job-meta">

                                <?php if (!empty($job["employment_type"])): ?>
                                    <span><?= htmlspecialchars($job["employment_type"]); ?></span>
                                <?php endif; ?>

                                <?php if (!empty($job["location"])): ?>
                                    <span><?= htmlspecialchars($job["location"]); ?></span>
                                <?php endif; ?>

                                <?php if (!empty($job["salary"])): ?>
                                    <span><?= htmlspecialchars($job["salary"]); ?></span>
                                <?php endif; ?>

                                <?php if (!empty($job["deadline"])): ?>
                                    <span>Deadline <?= date("M d, Y", strtotime($job["deadline"])); ?></span>
                                <?php endif; ?>

                                <span>Posted <?= date("M d, Y", strtotime($job["created_at"])); ?></span>

                            </div>

                        </div>

                        <a class="co-job-applicants" href="applicants.php?job=<?= (int) $job["id"]; ?>" title="View applicants">
                            <strong><?= (int) $job["applicant_count"]; ?></strong>
                            <span>applicant<?= (int) $job["applicant_count"] === 1 ? "" : "s"; ?></span>
                            <?php if ((int) $job["pending_count"] > 0): ?>
                                <span class="new"><?= (int) $job["pending_count"]; ?> new</span>
                            <?php endif; ?>
                        </a>

                        <div class="co-job-actions">

                            <a href="applicants.php?job=<?= (int) $job["id"]; ?>" class="comm-btn ghost small co-btn-link">Applicants</a>

                            <a href="job_form.php?id=<?= (int) $job["id"]; ?>" class="comm-btn ghost small co-btn-link">Edit</a>

                            <form method="POST" action="../../../backend/company/job_action.php">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="job_id" value="<?= (int) $job["id"]; ?>">
                                <input type="hidden" name="action" value="<?= $is_open ? "close" : "reopen"; ?>">
                                <button type="submit"
                                        class="comm-btn ghost small"
                                        <?php if ($is_open): ?>
                                            onclick="return confirm('Close this job? Designers will no longer see it or be able to apply.');"
                                        <?php endif; ?>>
                                    <?= $is_open ? "Close" : "Reopen"; ?>
                                </button>
                            </form>

                            <form method="POST" action="../../../backend/company/job_action.php">
                                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
                                <input type="hidden" name="job_id" value="<?= (int) $job["id"]; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit"
                                        class="comm-btn danger small"
                                        onclick="return confirm('Delete this job and all of its applications? This can\'t be undone.');">
                                    Delete
                                </button>
                            </form>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-state">
                <h3><?= $job_filter === "all" ? "No jobs posted yet" : "No " . $job_filter . " jobs"; ?></h3>
                <p>Post a role and designers will be able to apply right away.</p>
                <a href="job_form.php" class="upload-design-btn">Post a job</a>
            </div>

        <?php endif; ?>

    </main>

</div>

</body>
</html>
