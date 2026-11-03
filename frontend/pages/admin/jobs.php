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

require_once __DIR__ . "/../../../backend/admin/jobs.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Jobs | FTDP Admin</title>

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
            <h1>Jobs</h1>
            <p>Close, reopen or delete job posts. The company is told why.</p>
        </div>

        <?php if ($admin_flash): ?>
            <div class="comm-alert co-flash <?= $admin_flash["type"] === "success" ? "success" : "error"; ?>">
                <?= htmlspecialchars($admin_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <div class="co-toolbar">
            <span class="ad-count"><?= count($admin_jobs); ?> <?= count($admin_jobs) === 1 ? "job" : "jobs"; ?></span>
            <?= adminSortControl($sort); ?>
        </div>

        <div class="ad-table-wrap">

            <table class="ad-table">

                <thead>
                    <tr>
                        <th>Job</th>
                        <th>Company</th>
                        <th>Type</th>
                        <th>Applicants</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($admin_jobs as $job): ?>

                    <tr>

                        <td>
                            <div class="ad-strong"><?= htmlspecialchars($job["title"]); ?></div>
                            <?php if (!empty($job["location"])): ?>
                                <div class="ad-sub"><?= htmlspecialchars($job["location"]); ?></div>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($job["company_name"]); ?></td>

                        <td class="ad-nowrap"><?= htmlspecialchars($job["employment_type"]); ?></td>

                        <td><?= (int) $job["applicant_count"]; ?></td>

                        <td class="ad-nowrap">
                            <?= !empty($job["deadline"]) ? date("d M Y", strtotime($job["deadline"])) : "—"; ?>
                        </td>

                        <td>
                            <span class="ad-chip <?= $job["status"] === "open" ? "open" : "closed"; ?>">
                                <?= htmlspecialchars(ucfirst($job["status"])); ?>
                            </span>
                        </td>

                        <td class="ad-nowrap"><?= date("d M Y", strtotime($job["created_at"])); ?></td>

                        <td>
                            <div class="ad-actions">

                                <?php if ($job["status"] === "open"): ?>

                                    <?= adminActionForm(
                                        $csrf_token, "close_job", ["job_id" => $job["id"]],
                                        "Close", "ghost", "jobs.php", $sort,
                                        "Why are you closing this job? The company will see your reason.",
                                        true
                                    ); ?>

                                <?php else: ?>

                                    <?= adminActionForm(
                                        $csrf_token, "reopen_job", ["job_id" => $job["id"]],
                                        "Reopen", "ghost", "jobs.php", $sort
                                    ); ?>

                                <?php endif; ?>

                                <?= adminActionForm(
                                    $csrf_token, "delete_job", ["job_id" => $job["id"]],
                                    "Delete", "danger", "jobs.php", $sort,
                                    "Why are you deleting this job? The company will see your reason.",
                                    true
                                ); ?>

                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

            <?php if (count($admin_jobs) === 0): ?>
                <div class="ad-empty">No jobs have been posted yet.</div>
            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>
