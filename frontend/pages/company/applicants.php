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

require_once __DIR__ . "/../../../backend/company/applicants.php";

$csrf_token = csrfToken();

function applicantsUrl(int $job, string $status, int $page = 1): string
{
    $params = [];

    if ($job > 0) {
        $params["job"] = $job;
    }

    if ($status !== "all") {
        $params["status"] = $status;
    }

    if ($page > 1) {
        $params["page"] = $page;
    }

    return "applicants.php" . (count($params) ? "?" . http_build_query($params) : "");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token); ?>">

    <title>Applicants | FTDP</title>

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
            <h1>Applicants</h1>
            <p>Review who applied, open their full portfolio, and move them through your hiring steps.</p>
        </div>

        <div class="co-toolbar">

            <div class="co-tabs">

                <a href="<?= applicantsUrl($filter_job, "all"); ?>"
                   class="co-tab <?= $filter_status === "all" ? "active" : ""; ?>">
                    All <span class="co-tab-count"><?= (int) $status_counts["all"]; ?></span>
                </a>

                <?php foreach ($statuses as $status_name): ?>
                    <a href="<?= applicantsUrl($filter_job, $status_name); ?>"
                       class="co-tab <?= $filter_status === $status_name ? "active" : ""; ?>">
                        <?= htmlspecialchars(ucfirst($status_name)); ?>
                        <span class="co-tab-count"><?= (int) $status_counts[$status_name]; ?></span>
                    </a>
                <?php endforeach; ?>

            </div>

            <form method="GET" action="applicants.php">

                <?php if ($filter_status !== "all"): ?>
                    <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status); ?>">
                <?php endif; ?>

                <select name="job" class="co-select" onchange="this.form.submit()" aria-label="Filter by job">
                    <option value="0">All jobs</option>
                    <?php foreach ($company_jobs as $job): ?>
                        <option value="<?= (int) $job["id"]; ?>" <?= $filter_job === (int) $job["id"] ? "selected" : ""; ?>>
                            <?= htmlspecialchars($job["title"]); ?><?= $job["status"] === "closed" ? " (closed)" : ""; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

            </form>

        </div>

        <?php if (count($applicants_data) > 0): ?>

            <div class="co-card co-applicants">

                <div class="co-applicant head" aria-hidden="true">
                    <div>Designer</div>
                    <div>Applied for</div>
                    <div>Portfolio</div>
                    <div>Status</div>
                    <div></div>
                </div>

                <?php foreach ($applicants_data as $a): ?>

                    <?php
                        $profile_url = "designer_profile.php?id=" . (int) $a["designer_user_id"]
                                     . "&application=" . (int) $a["application_id"];
                    ?>

                    <div class="co-applicant">

                        <div class="co-person">

                            <div class="comm-avatar">
                                <?php if (!empty($a["profile_picture"])): ?>
                                    <img src="../../../<?= htmlspecialchars($a["profile_picture"]); ?>" alt="">
                                <?php else: ?>
                                    <?= htmlspecialchars(strtoupper(mb_substr($a["full_name"], 0, 1))); ?>
                                <?php endif; ?>
                            </div>

                            <div class="co-person-text">
                                <a class="co-person-name" href="<?= $profile_url; ?>">
                                    <?= htmlspecialchars($a["full_name"]); ?>
                                </a>
                                <div class="co-person-sub">
                                    <?= htmlspecialchars($a["headline"] ?: "Fashion designer"); ?>
                                </div>
                            </div>

                        </div>

                        <div class="co-applicant-job">
                            <?= htmlspecialchars($a["job_title"]); ?>
                            <small>Applied <?= date("M d, Y", strtotime($a["applied_at"])); ?></small>
                        </div>

                        <div class="co-mini-stats">
                            <span><b><?= (int) $a["design_count"]; ?></b> designs</span>
                            <span><b>♥ <?= (int) $a["like_count"]; ?></b> total likes</span>
                        </div>

                        <div class="co-status-cell">

                            <span class="status-badge status-<?= htmlspecialchars(strtolower($a["application_status"])); ?>"
                                  data-application-id="<?= (int) $a["application_id"]; ?>">
                                <?= htmlspecialchars(ucfirst($a["application_status"])); ?>
                            </span>

                            <select class="co-status-select"
                                    data-application-id="<?= (int) $a["application_id"]; ?>"
                                    aria-label="Change status for <?= htmlspecialchars($a["full_name"]); ?>">
                                <?php foreach ($statuses as $status_name): ?>
                                    <option value="<?= $status_name; ?>"
                                        <?= $a["application_status"] === $status_name ? "selected" : ""; ?>>
                                        <?= ucfirst($status_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                        <div class="co-applicant-actions">
                            <a href="<?= $profile_url; ?>" class="comm-btn small co-btn-link">Portfolio</a>
                            <a href="messages.php?with=<?= (int) $a["designer_user_id"]; ?>"
                               class="comm-btn ghost small co-btn-link">Message</a>
                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <?php if ($total_pages > 1): ?>

                <div class="co-pagination">

                    <?php if ($page > 1): ?>
                        <a class="comm-btn ghost small co-btn-link"
                           href="<?= applicantsUrl($filter_job, $filter_status, $page - 1); ?>">Newer</a>
                    <?php endif; ?>

                    <span>Page <?= $page; ?> of <?= $total_pages; ?></span>

                    <?php if ($page < $total_pages): ?>
                        <a class="comm-btn ghost small co-btn-link"
                           href="<?= applicantsUrl($filter_job, $filter_status, $page + 1); ?>">Older</a>
                    <?php endif; ?>

                </div>

            <?php endif; ?>

        <?php else: ?>

            <div class="empty-state">
                <h3>No applicants here</h3>
                <p>
                    <?= $status_counts["all"] === 0
                        ? "When designers apply to your jobs they will appear here."
                        : "Nothing matches this filter."; ?>
                </p>
                <?php if ($status_counts["all"] === 0): ?>
                    <a href="job_form.php" class="upload-design-btn">Post a job</a>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </main>

</div>

<script>window.FTDP_API = "../../../backend/company/";</script>
<script src="../../../js/comm_common.js"></script>
<script src="../../../js/company_applications.js"></script>

</body>
</html>
