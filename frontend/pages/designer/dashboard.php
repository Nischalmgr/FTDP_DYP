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

require_once __DIR__ .
    "/../../../backend/designer/dashboard.php";

$user_name =
    $dashboard_data["user_name"];

$total_designs =
    $dashboard_data["total_designs"];

$total_likes =
    $dashboard_data["total_likes"];

$total_jobs =
    $dashboard_data["total_jobs"];

$my_jobs =
    $dashboard_data["my_jobs"];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Designer Dashboard | FTDP
    </title>

    <link
        rel="stylesheet"
        href="../../css/designer_dashboard.css"

    >
    <link
        rel="stylesheet"
        href="../../css/designer.css"

    >

</head>

<body>

<div class="designer-dashboard">

    <?php

    require_once __DIR__ .
        "/../../include/designer_sidebar.php";

    ?>

    <main class="designer-main">

        <header class="dashboard-header">

            <div class="dashboard-heading">

                <p class="dashboard-label">
                    DESIGNER DASHBOARD
                </p>

                <h1>

                    Welcome back,
                    <?= htmlspecialchars($user_name); ?>

                </h1>

                <p class="dashboard-description">

                    Manage your designs, jobs and
                    professional profile.

                </p>

            </div>

            <div class="dashboard-header-action">

                <a
                    href="upload_design.php"
                    class="upload-design-btn"
                >

                    <span>+</span>

                    Upload Design

                </a>

            </div>

        </header>

        <section class="dashboard-stats">

            <div class="stat-card">

                <div class="stat-icon">
                    ◆
                </div>

                <div class="stat-information">

                    <span>
                        Total Designs
                    </span>

                    <strong>
                        <?= $total_designs; ?>
                    </strong>

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon">
                    ♡
                </div>

                <div class="stat-information">

                    <span>
                        Total Likes
                    </span>

                    <strong>
                        <?= $total_likes; ?>
                    </strong>

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-icon">
                    ✓
                </div>

                <div class="stat-information">

                    <span>
                        My Jobs
                    </span>

                    <strong>
                        <?= $total_jobs; ?>
                    </strong>

                </div>

            </div>

        </section>

        <section class="dashboard-grid">

            <div class="dashboard-panel jobs-panel">

                <div class="panel-header">

                    <div>

                        <h2>
                            My Jobs
                        </h2>

                        <p>
                            Your recent job applications
                        </p>

                    </div>

                    <a
                        href="my_jobs.php"
                        class="view-all"
                    >

                        View All

                    </a>

                </div>

                <?php if (count($my_jobs) > 0): ?>

                    <div class="jobs-table-container">

                        <table class="jobs-table">

                            <thead>

                                <tr>

                                    <th>
                                        Job
                                    </th>

                                    <th>
                                        Company
                                    </th>

                                    <th>
                                        Type
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th>
                                        Applied
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach (
                                    $my_jobs
                                    as $job
                                ): ?>

                                    <tr>

                                        <td>

                                            <div class="job-name">

                                                <?= htmlspecialchars(
                                                    $job["title"]
                                                ); ?>

                                            </div>

                                            <?php if (
                                                !empty(
                                                    $job["location"]
                                                )
                                            ): ?>

                                                <div class="job-location">

                                                    <?= htmlspecialchars(
                                                        $job["location"]
                                                    ); ?>

                                                </div>

                                            <?php endif; ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $job["company_name"]
                                            ); ?>

                                        </td>

                                        <td>

                                            <?= htmlspecialchars(
                                                $job[
                                                    "employment_type"
                                                ]
                                                ?? "Not specified"
                                            ); ?>

                                        </td>

                                        <td>

                                            <?php

                                            $status =
                                                $job[
                                                    "application_status"
                                                ];

                                            ?>

                                            <span
                                                class="
                                                status-badge
                                                status-<?= strtolower(
                                                    $status
                                                ); ?>
                                                "
                                            >

                                                <?= htmlspecialchars(
                                                    ucfirst($status)
                                                ); ?>

                                            </span>

                                        </td>

                                        <td>

                                            <?= date(
                                                "M d, Y",
                                                strtotime(
                                                    $job["applied_at"]
                                                )
                                            ); ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php else: ?>

                    <div class="empty-jobs">

                        <div class="empty-jobs-icon">
                            ▣
                        </div>

                        <h3>
                            No job applications yet
                        </h3>

                        <p>
                            Start exploring fashion opportunities.
                        </p>

                        <a
                            href="jobs.php"
                            class="explore-jobs-btn"
                        >

                            Explore Jobs

                        </a>

                    </div>

                <?php endif; ?>

            </div>

            <div class="dashboard-panel quick-panel">

                <div class="panel-header">

                    <div>

                        <h2>
                            Quick Actions
                        </h2>

                        <p>
                            Manage your account
                        </p>

                    </div>

                </div>

                <div class="quick-actions">

                    <a
                        href="upload_design.php"
                        class="quick-action"
                    >

                        <div class="quick-icon">
                            +
                        </div>

                        <div>

                            <strong>
                                Upload Design
                            </strong>

                            <span>
                                Share your latest work
                            </span>

                        </div>

                    </a>

                    <a
                        href="my_designs.php"
                        class="quick-action"
                    >

                        <div class="quick-icon">
                            ◆
                        </div>

                        <div>

                            <strong>
                                My Designs
                            </strong>

                            <span>
                                Manage your portfolio
                            </span>

                        </div>

                    </a>

                    <a
                        href="jobs.php"
                        class="quick-action"
                    >

                        <div class="quick-icon">
                            ▣
                        </div>

                        <div>

                            <strong>
                                Find Jobs
                            </strong>

                            <span>
                                Explore opportunities
                            </span>

                        </div>

                    </a>

                    <a
                        href="profile.php"
                        class="quick-action"
                    >

                        <div class="quick-icon">
                            ○
                        </div>

                        <div>

                            <strong>
                                My Profile
                            </strong>

                            <span>
                                Update your profile
                            </span>

                        </div>

                    </a>

                </div>

            </div>

        </section>

    </main>

</div>
<script src="../../../js/designer.js"></script>

</body>

</html>
