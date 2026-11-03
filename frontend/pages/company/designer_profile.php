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

require_once __DIR__ . "/../../../backend/company/designer_profile.php";

$csrf_token = csrfToken();

$statuses = companyApplicationStatuses();

function safeWebLink(?string $url): string
{
    return ($url && preg_match("#^https?://#i", $url)) ? $url : "";
}

$website_link   = safeWebLink($designer["website"] ?? null);
$portfolio_link = safeWebLink($designer["portfolio_link"] ?? null);

$member_since = !empty($designer["created_at"])
    ? date("F Y", strtotime($designer["created_at"]))
    : "";

$experience = (int) ($designer["experience_years"] ?? 0);

$back_url   = $focus_application > 0 ? "applicants.php" : "design_feed.php";
$back_label = $focus_application > 0 ? "Back to applicants" : "Back to explore";

?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrf_token); ?>">

    <title><?= htmlspecialchars($designer["full_name"]); ?> | FTDP</title>

    <link rel="stylesheet" href="../../css/designer_dashboard.css">
    <link rel="stylesheet" href="../../css/designer.css">
    <link rel="stylesheet" href="../../css/design_feed.css">
    <link rel="stylesheet" href="../../css/designer_comm.css">
    <link rel="stylesheet" href="../../css/profile.css">
    <link rel="stylesheet" href="../../css/company.css">

</head>
<body>

<div class="designer-dashboard">

    <?php require_once __DIR__ . "/../../include/company_sidebar.php"; ?>

    <main class="designer-main">

        <div class="page-heading">
            <h1>Designer Portfolio</h1>
            <p><a class="co-link" href="<?= $back_url; ?>">← <?= $back_label; ?></a></p>
        </div>

        <div class="dp-wrap">

            <section class="comm-card profile-hero">

                <div class="profile-cover">
                    <?php if (!empty($designer["cover_picture"])): ?>
                        <img src="../../../<?= htmlspecialchars($designer["cover_picture"]); ?>" alt="">
                    <?php endif; ?>
                </div>

                <div class="profile-hero-body">

                    <div class="profile-avatar">
                        <?php if (!empty($designer["profile_picture"])): ?>
                            <img src="../../../<?= htmlspecialchars($designer["profile_picture"]); ?>" alt="">
                        <?php else: ?>
                            <span><?= htmlspecialchars(strtoupper(mb_substr($designer["full_name"], 0, 1))); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="profile-hero-text">

                        <h2><?= htmlspecialchars($designer["full_name"]); ?></h2>

                        <p class="profile-headline <?= empty($designer["headline"]) ? "muted" : ""; ?>">
                            <?= !empty($designer["headline"])
                                ? htmlspecialchars($designer["headline"])
                                : "Fashion designer"; ?>
                        </p>

                        <p class="profile-meta">
                            <?php if (!empty($designer["location"])): ?>
                                <?= htmlspecialchars($designer["location"]); ?> &nbsp;•&nbsp;
                            <?php endif; ?>
                            <?= $experience; ?> year<?= $experience === 1 ? "" : "s"; ?> experience
                            <?php if ($member_since): ?>
                                &nbsp;•&nbsp; On FTDP since <?= htmlspecialchars($member_since); ?>
                            <?php endif; ?>
                        </p>

                    </div>

                    <div class="dp-hero-actions">
                        <a href="messages.php?with=<?= (int) $designer["user_id"]; ?>" class="comm-btn co-btn-link">
                            Message
                        </a>
                    </div>

                </div>

            </section>

            <div class="dp-stats">

                <div class="co-stat">
                    <div class="co-stat-value"><?= $portfolio_stats["designs"]; ?></div>
                    <div class="co-stat-label">Designs</div>
                </div>

                <div class="co-stat">
                    <div class="co-stat-value">♥ <?= $portfolio_stats["likes"]; ?></div>
                    <div class="co-stat-label">Total likes</div>
                </div>

                <div class="co-stat">
                    <div class="co-stat-value"><?= $portfolio_stats["saves"]; ?></div>
                    <div class="co-stat-label">Times saved</div>
                </div>

                <div class="co-stat">
                    <div class="co-stat-value"><?= $experience; ?></div>
                    <div class="co-stat-label">Years of experience</div>
                </div>

            </div>

            <?php if (count($designer_applications) > 0): ?>

                <section class="co-panel" id="applications">

                    <h2 class="dp-section-title">
                        Application<?= count($designer_applications) === 1 ? "" : "s"; ?> to your jobs
                    </h2>

                    <?php foreach ($designer_applications as $app): ?>

                        <div class="dp-app <?= (int) $app["application_id"] === $focus_application ? "focus" : ""; ?>">

                            <div class="dp-app-head">

                                <div>
                                    <div class="dp-app-title"><?= htmlspecialchars($app["job_title"]); ?></div>
                                    <div class="co-muted">Applied <?= date("M d, Y", strtotime($app["applied_at"])); ?></div>
                                </div>

                                <div class="dp-app-status">

                                    <span class="status-badge status-<?= htmlspecialchars(strtolower($app["application_status"])); ?>"
                                          data-application-id="<?= (int) $app["application_id"]; ?>">
                                        <?= htmlspecialchars(ucfirst($app["application_status"])); ?>
                                    </span>

                                    <select class="co-status-select"
                                            data-application-id="<?= (int) $app["application_id"]; ?>"
                                            aria-label="Change application status">
                                        <?php foreach ($statuses as $status_name): ?>
                                            <option value="<?= $status_name; ?>"
                                                <?= $app["application_status"] === $status_name ? "selected" : ""; ?>>
                                                <?= ucfirst($status_name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>

                            </div>

                            <?php if (!empty($app["cover_letter"]) || !empty($app["resume"])): ?>

                                <details <?= (int) $app["application_id"] === $focus_application ? "open" : ""; ?>>

                                    <summary>Cover letter &amp; resume</summary>

                                    <?php if (!empty($app["cover_letter"])): ?>
                                        <p class="dp-cover-letter"><?= htmlspecialchars($app["cover_letter"]); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($app["resume"])): ?>
                                        <p>
                                            <a class="co-link"
                                               href="../../../<?= htmlspecialchars($app["resume"]); ?>"
                                               target="_blank"
                                               rel="noopener noreferrer">Download resume</a>
                                        </p>
                                    <?php endif; ?>

                                </details>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                </section>

            <?php endif; ?>

            <section class="co-panel">

                <div class="dp-about">

                    <div>

                        <h2 class="dp-section-title">About</h2>

                        <?php if (!empty($designer["bio"])): ?>
                            <p class="dp-bio"><?= htmlspecialchars($designer["bio"]); ?></p>
                        <?php else: ?>
                            <p class="co-muted">This designer hasn't written a bio yet.</p>
                        <?php endif; ?>

                        <?php if (count($designer_specs) > 0): ?>
                            <div class="dp-tags">
                                <?php foreach ($designer_specs as $spec): ?>
                                    <span class="type-chip"><?= htmlspecialchars($spec); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>

                    <ul class="dp-facts">

                        <?php if (!empty($designer["location"])): ?>
                            <li><b>Location</b><?= htmlspecialchars($designer["location"]); ?></li>
                        <?php endif; ?>

                        <li><b>Experience</b><?= $experience; ?> year<?= $experience === 1 ? "" : "s"; ?></li>

                        <?php if ($portfolio_link): ?>
                            <li>
                                <b>Portfolio</b>
                                <a href="<?= htmlspecialchars($portfolio_link); ?>" target="_blank" rel="noopener noreferrer">
                                    <?= htmlspecialchars($portfolio_link); ?>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($website_link): ?>
                            <li>
                                <b>Website</b>
                                <a href="<?= htmlspecialchars($website_link); ?>" target="_blank" rel="noopener noreferrer">
                                    <?= htmlspecialchars($website_link); ?>
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>

                </div>

            </section>

            <section>

                <h2 class="dp-section-title">
                    Designs <span><?= count($designer_designs); ?> · ♥ <?= $portfolio_stats["likes"]; ?> total likes</span>
                </h2>

                <?php if (count($designer_designs) > 0): ?>

                    <div class="feed-grid">

                        <?php foreach ($designer_designs as $design): ?>

                            <div class="feed-card <?= (int) $design["id"] === $focus_design ? "focus" : ""; ?>"
                                 id="design-<?= (int) $design["id"]; ?>">

                                <img
                                    src="../../../<?= htmlspecialchars($design["image"]); ?>"
                                    alt="<?= htmlspecialchars($design["title"]); ?>"
                                    class="feed-thumb"
                                >

                                <button
                                    type="button"
                                    class="feed-save-btn <?= $design["saved_by_me"] ? 'saved' : ''; ?>"
                                    data-design-id="<?= (int) $design["id"]; ?>"
                                    data-remove-on-unsave="false"
                                    title="Save"
                                >
                                    ☆
                                </button>

                                <div class="feed-body">

                                    <h3><?= htmlspecialchars($design["title"]); ?></h3>

                                    <?php if (!empty($design["description"])): ?>
                                        <div class="feed-designer">
                                            <?= htmlspecialchars(mb_strimwidth($design["description"], 0, 90, "…")); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($design["type_names"])): ?>
                                        <div class="feed-types">
                                            <?php foreach (explode(", ", $design["type_names"]) as $type): ?>
                                                <span class="type-chip"><?= htmlspecialchars($type); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="feed-footer">

                                        <button
                                            type="button"
                                            class="like-btn <?= $design["liked_by_me"] ? 'liked' : ''; ?>"
                                            data-design-id="<?= (int) $design["id"]; ?>"
                                        >
                                            <span class="heart">♥</span>
                                            <span class="like-count"><?= (int) $design["like_count"]; ?></span>
                                        </button>

                                        <button type="button" class="comment-btn" data-design-id="<?= (int) $design["id"]; ?>" aria-expanded="false">
                                            <span class="bubble">✎</span>
                                            <span class="comment-count"><?= (int) $design["comment_count"]; ?></span>
                                        </button>

                                        <div class="feed-date">
                                            <?= date("M d, Y", strtotime($design["created_at"])); ?>
                                        </div>

                                    </div>

                                    <div class="comment-panel" data-design-id="<?= (int) $design["id"]; ?>" hidden>
                                        <div class="comment-list"></div>
                                        <div class="comment-form">
                                            <input type="text" class="comment-input" maxlength="1000" placeholder="Write a comment…" aria-label="Write a comment">
                                            <button type="button" class="comment-send">Post</button>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="empty-state">
                        <h3>No designs yet</h3>
                        <p>This designer hasn't uploaded any work.</p>
                    </div>

                <?php endif; ?>

            </section>

        </div>

    </main>

</div>

<script>window.FTDP_API = "../../../backend/company/";</script>
<script src="../../../js/comm_common.js"></script>
<script src="../../../js/company_feed.js"></script>
<script src="../../../js/comments.js"></script>
<script src="../../../js/company_applications.js"></script>

</body>
</html>
