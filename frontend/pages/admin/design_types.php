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

require_once __DIR__ . "/../../../backend/admin/design_types.php";

$csrf_token = csrfToken();

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Design Types | FTDP Admin</title>

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
            <h1>Design Types</h1>
            <p>The categories designers can tag their designs with.</p>
        </div>

        <?php if ($admin_flash): ?>
            <div class="comm-alert co-flash <?= $admin_flash["type"] === "success" ? "success" : "error"; ?>">
                <?= htmlspecialchars($admin_flash["message"]); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="../../../backend/admin/admin_action.php" class="ad-add-type">

            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
            <input type="hidden" name="action" value="add_type">
            <input type="hidden" name="back" value="design_types.php">
            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort); ?>">

            <div class="comm-field">
                <label for="newTypeName">Name</label>
                <input type="text" id="newTypeName" name="name" class="comm-input" maxlength="100" placeholder="e.g. Streetwear" required>
            </div>

            <div class="comm-field">
                <label for="newTypeDesc">Description</label>
                <input type="text" id="newTypeDesc" name="description" class="comm-input" placeholder="Optional short description">
            </div>

            <button type="submit" class="comm-btn">Add type</button>

        </form>

        <div class="co-toolbar">
            <span class="ad-count"><?= count($admin_types); ?> <?= count($admin_types) === 1 ? "type" : "types"; ?></span>
            <?= adminSortControl($sort); ?>
        </div>

        <div class="ad-table-wrap">

            <table class="ad-table">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Designs</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($admin_types as $type): ?>

                    <tr>

                        <td>
                            <input type="text" class="ad-inline-input" name="name"
                                   form="typeForm<?= (int) $type["id"]; ?>"
                                   value="<?= htmlspecialchars($type["name"]); ?>" maxlength="100" required>
                        </td>

                        <td>
                            <input type="text" class="ad-inline-input" name="description"
                                   form="typeForm<?= (int) $type["id"]; ?>"
                                   value="<?= htmlspecialchars((string) $type["description"]); ?>">
                        </td>

                        <td><?= (int) $type["design_count"]; ?></td>

                        <td class="ad-nowrap"><?= date("d M Y", strtotime($type["created_at"])); ?></td>

                        <td>
                            <div class="ad-actions">

                                <form method="POST" id="typeForm<?= (int) $type["id"]; ?>"
                                      action="../../../backend/admin/admin_action.php">
                                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
                                    <input type="hidden" name="action" value="edit_type">
                                    <input type="hidden" name="type_id" value="<?= (int) $type["id"]; ?>">
                                    <input type="hidden" name="back" value="design_types.php">
                                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort); ?>">
                                    <button type="submit" class="comm-btn ghost small">Save</button>
                                </form>

                                <?= adminActionForm(
                                    $csrf_token, "delete_type", ["type_id" => $type["id"]],
                                    "Delete", "danger", "design_types.php", $sort,
                                    "Delete the type \"" . $type["name"] . "\"? Designs keep existing but lose this tag."
                                ); ?>

                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

            <?php if (count($admin_types) === 0): ?>
                <div class="ad-empty">No design types yet. Add the first one above.</div>
            <?php endif; ?>

        </div>

    </main>

</div>

</body>
</html>
