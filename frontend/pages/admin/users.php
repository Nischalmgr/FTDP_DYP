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

require_once __DIR__ . "/../../../backend/admin/users.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Users | FTDP Admin</title>

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
            <h1>Users</h1>
            <p>Verify, unverify or delete accounts. Unverified users cannot log in.</p>
        </div>

        <?php if ($admin_flash): ?>
            <div class="comm-alert co-flash <?= $admin_flash["type"] === "success" ? "success" : "error"; ?>">
                <?= htmlspecialchars($admin_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <div class="co-toolbar">
            <span class="ad-count"><?= count($admin_users); ?> <?= count($admin_users) === 1 ? "user" : "users"; ?></span>
            <?= adminSortControl($sort); ?>
        </div>

        <div class="ad-table-wrap">

            <table class="ad-table">

                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($admin_users as $person): ?>

                    <tr>

                        <td>
                            <div class="ad-user">

                                <div class="comm-avatar <?= $person["role"] === "company" ? "company" : ""; ?>">
                                    <?php if (!empty($person["profile_picture"])): ?>
                                        <img src="../../../<?= htmlspecialchars($person["profile_picture"]); ?>" alt="">
                                    <?php else: ?>
                                        <?= htmlspecialchars(strtoupper(mb_substr($person["full_name"], 0, 1))); ?>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <div class="ad-strong"><?= htmlspecialchars($person["full_name"]); ?></div>
                                    <?php if (!empty($person["company_name"])): ?>
                                        <div class="ad-sub"><?= htmlspecialchars($person["company_name"]); ?></div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </td>

                        <td><?= htmlspecialchars($person["email"]); ?></td>

                        <td>
                            <span class="ad-chip <?= htmlspecialchars($person["role"]); ?>">
                                <?= htmlspecialchars(ucfirst($person["role"])); ?>
                            </span>
                        </td>

                        <td>
                            <span class="ad-chip <?= $person["is_verified"] === "Y" ? "good" : "warn"; ?>">
                                <?= $person["is_verified"] === "Y" ? "Verified" : "Unverified"; ?>
                            </span>
                        </td>

                        <td class="ad-nowrap"><?= date("d M Y", strtotime($person["created_at"])); ?></td>

                        <td>
                            <?php if ($person["role"] === "admin"): ?>

                                <span class="co-muted">Protected</span>

                            <?php else: ?>

                                <div class="ad-actions">

                                    <?php if ($person["is_verified"] === "Y"): ?>

                                        <?= adminActionForm(
                                            $csrf_token, "unverify", ["user_id" => $person["id"]],
                                            "Unverify", "ghost", "users.php", $sort,
                                            "Unverify " . $person["full_name"] . "? They will not be able to log in."
                                        ); ?>

                                    <?php else: ?>

                                        <?= adminActionForm(
                                            $csrf_token, "verify", ["user_id" => $person["id"]],
                                            "Verify", "", "users.php", $sort
                                        ); ?>

                                    <?php endif; ?>

                                    <?= adminActionForm(
                                        $csrf_token, "delete_user", ["user_id" => $person["id"]],
                                        "Delete", "danger", "users.php", $sort,
                                        "Delete " . $person["full_name"] . " and everything they posted? This cannot be undone."
                                    ); ?>

                                </div>

                            <?php endif; ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

            <?php if (count($admin_users) === 0): ?>
                <div class="ad-empty">No users yet.</div>
            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>
