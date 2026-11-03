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

require_once __DIR__ . "/../../../backend/admin/announcements.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Announcements | FTDP Admin</title>

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
            <h1>Announcements</h1>
            <p>Send a notification to many users at once.</p>
        </div>

        <?php if ($admin_flash): ?>
            <div class="comm-alert co-flash <?= $admin_flash["type"] === "success" ? "success" : "error"; ?>">
                <?= htmlspecialchars($admin_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <div class="comm-card ad-form-card">

            <form method="POST" action="../../../backend/admin/admin_action.php">

                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="action" value="announce">
                <input type="hidden" name="back" value="announcements.php">

                <div class="comm-field">
                    <label for="audience">Send to</label>
                    <select name="audience" id="audience" class="comm-input">
                        <option value="all" <?= $announce_old["audience"] === "all" ? "selected" : ""; ?>>
                            Everyone (<?= (int) $audience_counts["designers"] + (int) $audience_counts["companies"]; ?>)
                        </option>
                        <option value="designer" <?= $announce_old["audience"] === "designer" ? "selected" : ""; ?>>
                            Designers only (<?= (int) $audience_counts["designers"]; ?>)
                        </option>
                        <option value="company" <?= $announce_old["audience"] === "company" ? "selected" : ""; ?>>
                            Companies only (<?= (int) $audience_counts["companies"]; ?>)
                        </option>
                    </select>
                </div>

                <div class="comm-field">
                    <label for="annTitle">Title</label>
                    <input type="text" id="annTitle" name="title" class="comm-input" maxlength="255"
                           value="<?= htmlspecialchars($announce_old["title"]); ?>" required>
                </div>

                <div class="comm-field">
                    <label for="annMessage">Message</label>
                    <textarea id="annMessage" name="message" class="comm-input" required><?= htmlspecialchars($announce_old["message"]); ?></textarea>
                    <span class="hint">Shown in each user's Notifications page.</span>
                </div>

                <button type="submit" class="comm-btn"
                        onclick="return confirm('Send this announcement now?');">Send announcement</button>

            </form>

        </div>

    </main>

</div>

</body>
</html>
