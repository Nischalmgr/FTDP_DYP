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

require_once __DIR__ . "/../../../backend/admin/dashboard.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | FTDP Admin</title>

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
            <h1>Dashboard</h1>
            <p>The whole platform at a glance.</p>
        </div>

        <?php if ($admin_flash): ?>
            <div class="comm-alert co-flash <?= $admin_flash["type"] === "success" ? "success" : "error"; ?>">
                <?= htmlspecialchars($admin_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <div class="co-quick">
            <a href="users.php" class="comm-btn co-btn-link">Manage users</a>
            <a href="announcements.php" class="comm-btn ghost co-btn-link">Send announcement</a>
            <a href="design_types.php" class="comm-btn ghost co-btn-link">Design types</a>
        </div>

        <div class="co-stats">

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["designers"]; ?></div>
                <div class="co-stat-label">Designers</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["companies"]; ?></div>
                <div class="co-stat-label">Companies</div>
            </div>

            <div class="co-stat <?= (int) $dash_stats["unverified"] > 0 ? "attention" : ""; ?>">
                <div class="co-stat-value"><?= (int) $dash_stats["unverified"]; ?></div>
                <div class="co-stat-label">Unverified users</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["designs"]; ?></div>
                <div class="co-stat-label">Designs</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["comments"]; ?></div>
                <div class="co-stat-label">Comments</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["open_jobs"]; ?></div>
                <div class="co-stat-label">Open jobs</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["closed_jobs"]; ?></div>
                <div class="co-stat-label">Closed jobs</div>
            </div>

            <div class="co-stat">
                <div class="co-stat-value"><?= (int) $dash_stats["applications"]; ?></div>
                <div class="co-stat-label">Applications</div>
            </div>

        </div>

        <div class="co-dash-grid">

            <section class="co-panel">

                <div class="co-panel-head">
                    <h2>Newest users</h2>
                    <a href="users.php" class="co-link">View all</a>
                </div>

                <?php if (count($recent_users) > 0): ?>

                    <?php foreach ($recent_users as $person): ?>

                        <div class="co-row">

                            <div class="co-person">

                                <div class="comm-avatar <?= $person["role"] === "company" ? "company" : ""; ?>">
                                    <?php if (!empty($person["profile_picture"])): ?>
                                        <img src="../../../<?= htmlspecialchars($person["profile_picture"]); ?>" alt="">
                                    <?php else: ?>
                                        <?= htmlspecialchars(strtoupper(mb_substr($person["full_name"], 0, 1))); ?>
                                    <?php endif; ?>
                                </div>

                                <div class="co-person-text">
                                    <span class="co-person-name"><?= htmlspecialchars($person["full_name"]); ?></span>
                                    <div class="co-person-sub">
                                        <?= htmlspecialchars(ucfirst($person["role"])); ?>
                                        · <?= htmlspecialchars(formatAge((int) $person["age_seconds"], $person["created_at"])); ?>
                                    </div>
                                </div>

                            </div>

                            <span class="ad-chip <?= $person["is_verified"] === "Y" ? "good" : "warn"; ?>">
                                <?= $person["is_verified"] === "Y" ? "Verified" : "Unverified"; ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p class="co-muted">Nobody has registered yet.</p>

                <?php endif; ?>

            </section>

            <section class="co-panel">

                <div class="co-panel-head">
                    <h2>Newest jobs</h2>
                    <a href="jobs.php" class="co-link">Manage</a>
                </div>

                <?php if (count($recent_jobs) > 0): ?>

                    <?php foreach ($recent_jobs as $job): ?>

                        <div class="co-row">

                            <div>
                                <span class="co-row-title"><?= htmlspecialchars($job["title"]); ?></span>
                                <div class="co-person-sub"><?= htmlspecialchars($job["company_name"]); ?></div>
                            </div>

                            <span class="ad-chip <?= $job["status"] === "open" ? "open" : "closed"; ?>">
                                <?= htmlspecialchars(ucfirst($job["status"])); ?>
                            </span>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p class="co-muted">No jobs have been posted yet.</p>

                <?php endif; ?>

            </section>

        </div>

        <section class="co-panel">

            <div class="co-panel-head">
                <h2>Newest designs</h2>
                <a href="designs.php" class="co-link">Manage</a>
            </div>

            <?php if (count($recent_designs) > 0): ?>

                <div class="co-fresh-grid">

                    <?php foreach ($recent_designs as $design): ?>

                        <a class="co-fresh" href="designs.php">

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
