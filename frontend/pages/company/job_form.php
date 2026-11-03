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

require_once __DIR__ . "/../../../backend/company/job_form.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $is_edit ? "Edit Job" : "Post a Job"; ?> | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/designer_comm.css">
    <link rel="stylesheet" href="../../css/upload_design.css">
    <link rel="stylesheet" href="../../css/company.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/company_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1><?= $is_edit ? "Edit Job" : "Post a Job"; ?></h1>
            <p><?= $is_edit
                ? "Changes are visible to designers straight away."
                : "Describe the role. Designers see it as soon as you post."; ?></p>
        </div>

        <div class="co-form-card">

            <?php if (count($form_errors) > 0): ?>
                <div class="comm-alert error">
                    <ul>
                        <?php foreach ($form_errors as $err): ?>
                            <li><?= htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="job_form.php<?= $is_edit ? "?id=" . (int) $edit_id : ""; ?>">

                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">

                <div class="co-form-grid">

                    <div class="form-group full">
                        <label for="title">Job title</label>
                        <input type="text"
                               id="title"
                               name="title"
                               maxlength="200"
                               required
                               placeholder="e.g. Junior Knitwear Designer"
                               value="<?= htmlspecialchars($job["title"]); ?>">
                    </div>

                    <div class="form-group">
                        <label for="employment_type">Employment type</label>
                        <select id="employment_type" name="employment_type">
                            <option value="">Not specified</option>
                            <?php foreach ($employment_types as $type): ?>
                                <option value="<?= htmlspecialchars($type); ?>"
                                    <?= $job["employment_type"] === $type ? "selected" : ""; ?>>
                                    <?= htmlspecialchars($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text"
                               id="location"
                               name="location"
                               maxlength="150"
                               placeholder="City, Country or Remote"
                               value="<?= htmlspecialchars($job["location"]); ?>">
                    </div>

                    <div class="form-group">
                        <label for="salary">Salary</label>
                        <input type="text"
                               id="salary"
                               name="salary"
                               maxlength="100"
                               placeholder="e.g. $2,500 / month"
                               value="<?= htmlspecialchars($job["salary"]); ?>">
                    </div>

                    <div class="form-group">
                        <label for="deadline">Application deadline</label>
                        <input type="date"
                               id="deadline"
                               name="deadline"
                               value="<?= htmlspecialchars($job["deadline"]); ?>">
                    </div>

                    <div class="form-group full">
                        <label for="description">Description</label>
                        <textarea id="description"
                                  name="description"
                                  maxlength="5000"
                                  required
                                  placeholder="What will they work on? What skills and experience are you looking for?"><?= htmlspecialchars($job["description"]); ?></textarea>
                        <div class="co-form-hint">At least 20 characters, up to 5000.</div>
                    </div>

                </div>

                <div class="co-form-actions">

                    <button type="submit" class="submit-btn">
                        <?= $is_edit ? "Save changes" : "Post job"; ?>
                    </button>

                    <a href="jobs.php" class="comm-btn ghost co-btn-link">Cancel</a>

                </div>

            </form>

        </div>

    </main>

</div>

</body>
</html>
