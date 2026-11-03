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

require_once __DIR__ . "/../../../backend/company/profile.php";

$csrf_token = csrfToken();

$saved_name   = $profile_data["company_name"] ?? ($profile_data["full_name"] ?? "Company");
$saved_avatar = $profile_data["profile_picture"] ?? null;
$saved_cover  = $profile_data["cover_picture"] ?? null;

$member_since = !empty($profile_data["created_at"])
    ? date("F Y", strtotime($profile_data["created_at"]))
    : "";

$current_size = $form_values["company_size"] ?? "";

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token); ?>">

    <title>Company Profile | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/designer_comm.css">
    <link rel="stylesheet" href="../../css/profile.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/company_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>Company Profile</h1>
            <p>How your company appears to designers on FTDP.</p>
        </div>

        <div class="profile-wrap">

            <?php if ($profile_flash): ?>
                <div class="comm-alert <?= $profile_flash["type"] === "success" ? "success" : "error"; ?>" role="status">
                    <?= htmlspecialchars($profile_flash["message"]); ?>
                </div>
            <?php endif; ?>

            <section class="comm-card profile-hero">

                <div class="profile-cover" id="coverBox">

                    <img id="coverImg"
                         alt=""
                         <?= $saved_cover ? 'src="../../../' . htmlspecialchars($saved_cover) . '"' : "hidden"; ?>>

                </div>

                <div class="profile-hero-body">

                    <div class="profile-avatar" id="avatarBox">

                        <img id="avatarImg"
                             alt=""
                             <?= $saved_avatar ? 'src="../../../' . htmlspecialchars($saved_avatar) . '"' : "hidden"; ?>>

                        <span id="avatarInitial" <?= $saved_avatar ? "hidden" : ""; ?>>
                            <?= htmlspecialchars(strtoupper(mb_substr($saved_name, 0, 1))); ?>
                        </span>

                    </div>

                    <div class="profile-hero-text">

                        <h2><?= htmlspecialchars($saved_name); ?></h2>

                        <?php if (!empty($profile_data["industry"])): ?>
                            <p class="profile-headline"><?= htmlspecialchars($profile_data["industry"]); ?></p>
                        <?php else: ?>
                            <p class="profile-headline muted">Add your industry so designers know who you are.</p>
                        <?php endif; ?>

                        <p class="profile-meta">
                            <?= htmlspecialchars($profile_data["email"] ?? ""); ?>
                            <?php if ($member_since): ?>
                                &nbsp;•&nbsp; Member since <?= htmlspecialchars($member_since); ?>
                            <?php endif; ?>
                        </p>

                    </div>

                </div>

            </section>

            <section class="comm-card profile-section" id="photosCard">

                <h2>Logo &amp; cover</h2>
                <p class="comm-card-sub">Choose a new image to preview it above, then save.</p>

                <?php if (count($photo_errors) > 0): ?>
                    <div class="comm-alert error">
                        <ul>
                            <?php foreach ($photo_errors as $err): ?>
                                <li><?= htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" id="photosForm">

                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="form_action" value="photos">

                    <div class="profile-grid">

                        <div class="comm-field">
                            <label for="profile_picture">Company logo</label>
                            <input type="file"
                                   class="comm-input"
                                   id="profile_picture"
                                   name="profile_picture"
                                   accept="image/jpeg,image/png,image/webp">
                            <span class="hint">JPG, PNG or WEBP · up to 3 MB · square works best</span>

                            <?php if ($saved_avatar): ?>
                                <label class="profile-check">
                                    <input type="checkbox" name="remove_profile_picture" value="1">
                                    Remove current logo
                                </label>
                            <?php endif; ?>
                        </div>

                        <div class="comm-field">
                            <label for="cover_picture">Cover photo</label>
                            <input type="file"
                                   class="comm-input"
                                   id="cover_picture"
                                   name="cover_picture"
                                   accept="image/jpeg,image/png,image/webp">
                            <span class="hint">JPG, PNG or WEBP · up to 5 MB · wide images (3:1) work best</span>

                            <?php if ($saved_cover): ?>
                                <label class="profile-check">
                                    <input type="checkbox" name="remove_cover_picture" value="1">
                                    Remove current cover photo
                                </label>
                            <?php endif; ?>
                        </div>

                    </div>

                    <div class="profile-actions">
                        <button type="submit" class="comm-btn">Save photos</button>
                    </div>

                </form>

            </section>

            <section class="comm-card profile-section" id="detailsCard">

                <h2>Company details</h2>
                <p class="comm-card-sub">Designers see this when they view your job posts.</p>

                <?php if (count($profile_errors) > 0): ?>
                    <div class="comm-alert error">
                        <ul>
                            <?php foreach ($profile_errors as $err): ?>
                                <li><?= htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" id="detailsForm">

                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="form_action" value="profile">

                    <div class="profile-grid">

                        <div class="comm-field full">
                            <label for="company_name">Company name</label>
                            <input type="text"
                                   class="comm-input"
                                   id="company_name"
                                   name="company_name"
                                   maxlength="150"
                                   required
                                   value="<?= htmlspecialchars($form_values["company_name"] ?? ""); ?>">
                        </div>

                        <div class="comm-field">
                            <label for="full_name">Contact person</label>
                            <input type="text"
                                   class="comm-input"
                                   id="full_name"
                                   name="full_name"
                                   maxlength="100"
                                   required
                                   value="<?= htmlspecialchars($form_values["full_name"] ?? ""); ?>">
                        </div>

                        <div class="comm-field">
                            <label for="email">Email</label>
                            <input type="email"
                                   class="comm-input"
                                   id="email"
                                   readonly
                                   value="<?= htmlspecialchars($profile_data["email"] ?? ""); ?>">
                            <span class="hint">Email is your login and can't be changed here.</span>
                        </div>

                        <div class="comm-field">
                            <label for="industry">Industry</label>
                            <input type="text"
                                   class="comm-input"
                                   id="industry"
                                   name="industry"
                                   maxlength="100"
                                   placeholder="e.g. Fashion retail, Textile manufacturing"
                                   value="<?= htmlspecialchars($form_values["industry"] ?? ""); ?>">
                        </div>

                        <div class="comm-field">
                            <label for="company_size">Company size</label>
                            <select class="comm-input" id="company_size" name="company_size">
                                <option value="">Not specified</option>
                                <?php foreach ($company_sizes as $size_option): ?>
                                    <option value="<?= htmlspecialchars($size_option); ?>"
                                        <?= $current_size === $size_option ? "selected" : ""; ?>>
                                        <?= htmlspecialchars($size_option); ?> employees
                                    </option>
                                <?php endforeach; ?>
                                <?php if ($current_size !== "" && !in_array($current_size, $company_sizes, true)): ?>
                                    <option value="<?= htmlspecialchars($current_size); ?>" selected>
                                        <?= htmlspecialchars($current_size); ?>
                                    </option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="comm-field">
                            <label for="location">Location</label>
                            <input type="text"
                                   class="comm-input"
                                   id="location"
                                   name="location"
                                   maxlength="150"
                                   placeholder="City, Country"
                                   value="<?= htmlspecialchars($form_values["location"] ?? ""); ?>">
                        </div>

                        <div class="comm-field">
                            <label for="website">Website</label>
                            <input type="text"
                                   class="comm-input"
                                   id="website"
                                   name="website"
                                   maxlength="255"
                                   placeholder="yourcompany.com"
                                   value="<?= htmlspecialchars($form_values["website"] ?? ""); ?>">
                        </div>

                        <div class="comm-field full">
                            <label for="bio">About the company</label>
                            <textarea class="comm-input"
                                      id="bio"
                                      name="description"
                                      maxlength="3000"
                                      placeholder="What you make, who you hire, and what it's like to work with you…"><?= htmlspecialchars($form_values["description"] ?? ""); ?></textarea>
                            <span class="hint"><span id="bioCount">0</span> / 3000</span>
                        </div>

                    </div>

                    <div class="profile-actions">
                        <button type="submit" class="comm-btn">Save details</button>
                    </div>

                </form>

            </section>

            <section class="comm-card profile-section" id="passwordCard">

                <h2>Change password</h2>
                <p class="comm-card-sub">Use at least 8 characters with a letter and a number.</p>

                <?php if (count($password_errors) > 0): ?>
                    <div class="comm-alert error">
                        <ul>
                            <?php foreach ($password_errors as $err): ?>
                                <li><?= htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" id="passwordForm">

                    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="form_action" value="password">

                    <div class="profile-grid">

                        <div class="comm-field full">
                            <label for="current_password">Current password</label>
                            <input type="password"
                                   class="comm-input"
                                   id="current_password"
                                   name="current_password"
                                   autocomplete="current-password"
                                   required>
                        </div>

                        <div class="comm-field">
                            <label for="new_password">New password</label>
                            <input type="password"
                                   class="comm-input"
                                   id="new_password"
                                   name="new_password"
                                   autocomplete="new-password"
                                   minlength="8"
                                   maxlength="72"
                                   required>
                        </div>

                        <div class="comm-field">
                            <label for="confirm_password">Confirm new password</label>
                            <input type="password"
                                   class="comm-input"
                                   id="confirm_password"
                                   name="confirm_password"
                                   autocomplete="new-password"
                                   minlength="8"
                                   maxlength="72"
                                   required>
                            <span class="hint" id="pwMatchHint"></span>
                        </div>

                    </div>

                    <div class="profile-actions">

                        <label class="profile-check">
                            <input type="checkbox" id="showPasswords">
                            Show passwords
                        </label>

                        <button type="submit" class="comm-btn">Update password</button>

                    </div>

                </form>

            </section>

        </div>

    </main>

</div>

<script>window.FTDP_API = "../../../backend/company/";</script>
<script src="../../../js/comm_common.js"></script>
<script src="../../../js/profile.js"></script>

</body>
</html>
