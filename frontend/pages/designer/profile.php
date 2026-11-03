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

require_once __DIR__ . "/../../../backend/designer/profile.php";

$csrf_token = csrfToken();

$display_name = $form_values["full_name"] ?? ($profile_data["full_name"] ?? "Designer");

$saved_name   = $profile_data["full_name"] ?? "Designer";
$saved_avatar = $profile_data["profile_picture"] ?? null;
$saved_cover  = $profile_data["cover_picture"] ?? null;

$member_since = !empty($profile_data["created_at"])
    ? date("F Y", strtotime($profile_data["created_at"]))
    : "";

$spec_value = implode(",", $form_values["specializations"] ?? []);

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token); ?>">

    <title>My Profile | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/designer_comm.css">
    <link rel="stylesheet" href="../../css/profile.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/designer_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>My Profile</h1>
            <p>How you appear to companies and other designers on FTDP.</p>
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

                        <?php if (!empty($profile_data["headline"])): ?>
                            <p class="profile-headline"><?= htmlspecialchars($profile_data["headline"]); ?></p>
                        <?php else: ?>
                            <p class="profile-headline muted">Add a headline so people know what you do.</p>
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

                <h2>Photos</h2>
                <p class="comm-card-sub">Choose a new photo to preview it above, then save.</p>

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
                            <label for="profile_picture">Profile photo</label>
                            <input type="file"
                                   class="comm-input"
                                   id="profile_picture"
                                   name="profile_picture"
                                   accept="image/jpeg,image/png,image/webp">
                            <span class="hint">JPG, PNG or WEBP · up to 3 MB · square works best</span>

                            <?php if ($saved_avatar): ?>
                                <label class="profile-check">
                                    <input type="checkbox" name="remove_profile_picture" value="1">
                                    Remove current profile photo
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

                <h2>Profile details</h2>
                <p class="comm-card-sub">Tell people who you are and what you design.</p>

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

                        <div class="comm-field">
                            <label for="full_name">Full name</label>
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

                        <div class="comm-field full">
                            <label for="headline">Headline</label>
                            <input type="text"
                                   class="comm-input"
                                   id="headline"
                                   name="headline"
                                   maxlength="255"
                                   placeholder="e.g. Sustainable knitwear designer"
                                   value="<?= htmlspecialchars($form_values["headline"] ?? ""); ?>">
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
                            <label for="experience_years">Years of experience</label>
                            <input type="number"
                                   class="comm-input"
                                   id="experience_years"
                                   name="experience_years"
                                   min="0"
                                   max="60"
                                   step="1"
                                   value="<?= htmlspecialchars((string) ($form_values["experience_years"] ?? "0")); ?>">
                        </div>

                        <div class="comm-field">
                            <label for="website">Website</label>
                            <input type="text"
                                   class="comm-input"
                                   id="website"
                                   name="website"
                                   maxlength="255"
                                   placeholder="yourname.com"
                                   value="<?= htmlspecialchars($form_values["website"] ?? ""); ?>">
                        </div>

                        <div class="comm-field">
                            <label for="portfolio_link">Portfolio link</label>
                            <input type="text"
                                   class="comm-input"
                                   id="portfolio_link"
                                   name="portfolio_link"
                                   maxlength="255"
                                   placeholder="behance.net/yourname"
                                   value="<?= htmlspecialchars($form_values["portfolio_link"] ?? ""); ?>">
                        </div>

                        <div class="comm-field full">
                            <label for="bio">Bio</label>
                            <textarea class="comm-input"
                                      id="bio"
                                      name="bio"
                                      maxlength="2000"
                                      placeholder="Your story, your style, what you're looking for…"><?= htmlspecialchars($form_values["bio"] ?? ""); ?></textarea>
                            <span class="hint"><span id="bioCount">0</span> / 2000</span>
                        </div>

                        <div class="comm-field full">
                            <label for="specInput">Specializations</label>

                            <div class="tag-input" id="specBox">
                                <div class="tag-list" id="specChips"></div>
                                <input type="text"
                                       class="tag-text"
                                       id="specInput"
                                       maxlength="100"
                                       list="specSuggestions"
                                       placeholder="Type and press Enter to add"
                                       autocomplete="off">
                            </div>

                            <datalist id="specSuggestions">
                                <?php foreach ($specialization_suggestions as $suggestion): ?>
                                    <option value="<?= htmlspecialchars($suggestion); ?>"></option>
                                <?php endforeach; ?>
                            </datalist>

                            <input type="hidden"
                                   name="specializations"
                                   id="specValue"
                                   value="<?= htmlspecialchars($spec_value); ?>">

                            <span class="hint">Up to 15. Press Enter or comma after each one.</span>
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

<script src="../../../js/comm_common.js"></script>
<script src="../../../js/profile.js"></script>

</body>
</html>
