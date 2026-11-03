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

require_once __DIR__ . "/../../../backend/company/dashboard.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | FTDP</title>

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
            <h1>Dashboard</h1>
            <p>Your hiring at a glance.</p>
        </div>

        <div class="co-quick">
            <a href="job_form.php" class="comm-btn co-btn-link">Post a job</a>
            <a href="applicants.php?status=pending" class="comm-btn ghost co-btn-link">Review pending applicants</a>
            <a href="design_feed.php" class="comm-btn ghost co-btn-link">Explore designers</a>
        </div>

        <div class="co-stats">

            <div class="co-stat <?= (int) $dash_stats["pending"] > 0 ? "attention" : ""; ?>">
                <div class="co-stat-value"><?= (int) $dash_stats["pending"]; ?></div>
                <div class="co-stat-label">Applicants waiting for review</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["open_jobs"]; ?></div>
                <div class="co-stat-label">Open jobs</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["total_applicants"]; ?></div>
                <div class="co-stat-label">Total applications</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["shortlisted"]; ?></div>
                <div class="co-stat-label">Shortlisted</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["hired"]; ?></div>
                <div class="co-stat-label">Hired</div>
            </div>

        </div>

        <div class="co-dash-grid">

            <section class="co-panel">

                <div class="co-panel-head">
                    <h2>Latest applications</h2>
                    <a href="applicants.php" class="co-link">View all</a>
                </div>

                <?php if (count($recent_applications) > 0): ?>

                    <?php foreach ($recent_applications as $app): ?>

                        <div class="co-row">

                            <div class="co-person">

                                <div class="comm-avatar">
                                    <?php if (!empty($app["profile_picture"])): ?>
                                        <img src="../../../<?= htmlspecialchars($app["profile_picture"]); ?>" alt="">
                                    <?php else: ?>
                                        <?= htmlspecialchars(strtoupper(mb_substr($app["full_name"], 0, 1))); ?>
                                    <?php endif; ?>
                                </div>

                                <div class="co-person-text">
                                    <a class="co-person-name"
                                       href="designer_profile.php?id=<?= (int) $app["designer_user_id"]; ?>&application=<?= (int) $app["application_id"]; ?>">
                                        <?= htmlspecialchars($app["full_name"]); ?>
                                    </a>
                                    <div class="co-person-sub">
                                        Applied for <?= htmlspecialchars($app["job_title"]); ?>
                                        · <?= htmlspecialchars(formatAge((int) $app["age_seconds"], $app["applied_at"])); ?>
                                    </div>
                                </div>

                            </div>

                            <span class="status-badge status-<?= htmlspecialchars(strtolower($app["application_status"])); ?>">
                                <?= htmlspecialchars(ucfirst($app["application_status"])); ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p class="co-muted">No applications yet. Once designers apply to your jobs they'll show up here.</p>

                <?php endif; ?>

            </section>

            <section class="co-panel">

                <div class="co-panel-head">
                    <h2>Open jobs</h2>
                    <a href="jobs.php" class="co-link">Manage</a>
                </div>

                <?php if (count($dash_jobs) > 0): ?>

                    <?php foreach ($dash_jobs as $job): ?>

                        <div class="co-row">

                            <a class="co-row-title" href="applicants.php?job=<?= (int) $job["id"]; ?>">
                                <?= htmlspecialchars($job["title"]); ?>
                            </a>

                            <span class="co-row-count" title="Applicants">
                                <?= (int) $job["applicant_count"]; ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p class="co-muted">You have no open jobs. <a class="co-link" href="job_form.php">Post your first job</a>.</p>

                <?php endif; ?>

            </section>

        </div>

        <section class="co-panel">

            <div class="co-panel-head">
                <h2>Fresh from designers</h2>
                <a href="design_feed.php" class="co-link">Explore all</a>
            </div>

            <?php if (count($fresh_designs) > 0): ?>

                <div class="co-fresh-grid">

                    <?php foreach ($fresh_designs as $design): ?>

                        <a class="co-fresh"
                           href="designer_profile.php?id=<?= (int) $design["designer_user_id"]; ?>&design=<?= (int) $design["id"]; ?>#design-<?= (int) $design["id"]; ?>">

                            <img src="../../../<?= htmlspecialchars($design["image"]); ?>"
                                 alt="<?= htmlspecialchars($design["title"]); ?>">

                            <div class="co-fresh-body">
                                <div class="co-fresh-title"><?= htmlspecialchars($design["title"]); ?></div>
                                <div class="co-fresh-sub">
                                    <?= htmlspecialchars($design["designer_name"]); ?> · ♥ <?= (int) $design["like_count"]; ?>
                                </div>
                            </div>

                        </a>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <p class="co-muted">No designs have been shared yet.</p>

            <?php endif; ?>

        </section>

    </main>

</div>

</body>
</html>
