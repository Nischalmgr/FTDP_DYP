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

require_once __DIR__ . "/../../../backend/admin/profile.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Profile | FTDP Admin</title>

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
            <h1>Admin Profile</h1>
            <p>Your details and password.</p>
        </div>

        <?php if ($admin_flash): ?>
            <div class="comm-alert co-flash <?= $admin_flash["type"] === "success" ? "success" : "error"; ?>">
                <?= htmlspecialchars($admin_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <div class="comm-card ad-form-card">

            <h2>Details</h2>

            <form method="POST" action="../../../backend/admin/admin_action.php">

                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="action" value="update_profile">
                <input type="hidden" name="back" value="profile.php">

                <div class="comm-field">
                    <label for="fullName">Full name</label>
                    <input type="text" id="fullName" name="full_name" class="comm-input" maxlength="100"
                           value="<?= htmlspecialchars($admin_user["full_name"]); ?>" required>
                </div>

                <div class="comm-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="comm-input" maxlength="150"
                           value="<?= htmlspecialchars($admin_user["email"]); ?>" required>
                </div>

                <button type="submit" class="comm-btn">Save changes</button>

            </form>

        </div>

        <div class="comm-card ad-form-card">

            <h2>Change password</h2>

            <form method="POST" action="../../../backend/admin/admin_action.php">

                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="action" value="change_password">
                <input type="hidden" name="back" value="profile.php">

                <div class="comm-field">
                    <label for="currentPassword">Current password</label>
                    <input type="password" id="currentPassword" name="current_password" class="comm-input" autocomplete="current-password" required>
                </div>

                <div class="comm-field">
                    <label for="newPassword">New password</label>
                    <input type="password" id="newPassword" name="new_password" class="comm-input" minlength="8" autocomplete="new-password" required>
                    <span class="hint">At least 8 characters.</span>
                </div>

                <div class="comm-field">
                    <label for="confirmPassword">Confirm new password</label>
                    <input type="password" id="confirmPassword" name="confirm_password" class="comm-input" minlength="8" autocomplete="new-password" required>
                </div>

                <button type="submit" class="comm-btn">Change password</button>

            </form>

        </div>

    </main>

</div>

</body>
</html>
