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

require_once __DIR__ . "/../../../backend/designer/upload_design.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Upload Design | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/upload_design.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/designer_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>Upload Design</h1>
            <p>Share your latest work with the community.</p>
        </div>

        <?php if ($upload_error !== ""): ?>
            <div class="form-alert error"><?= htmlspecialchars($upload_error); ?></div>
        <?php endif; ?>

        <?php if ($upload_success): ?>
            <div class="form-alert success">
                Design uploaded successfully.
                <a href="my_designs.php">View it in My Designs</a>.
            </div>
        <?php endif; ?>

        <div class="upload-card">

            <form
                method="POST"
                action="upload_design.php"
                enctype="multipart/form-data"
            >

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" required>
                </div>

                <div class="form-group">
                    <label>Design Types</label>
                    <div class="types-checkbox-grid">
                        <?php foreach ($all_design_types as $type): ?>
                            <label class="type-checkbox">
                                <input type="checkbox" name="types[]" value="<?= (int) $type["id"]; ?>">
                                <?= htmlspecialchars($type["name"]); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_type">Don't see your type? Add a new one</label>
                    <input type="text" id="new_type" name="new_type" placeholder="e.g. Bridal Wear">
                </div>

                <button type="submit" class="submit-btn">Upload Design</button>

            </form>

        </div>

    </main>

</div>
<script src="../../../js/designer.js"></script>

</body>
</html>
