<?php

$pub_title = "Discover Talent";
$pub_nav   = "talent";

include "../include/public_head.php";

$designer_id = (int) ($_GET["id"] ?? 0);
$designer    = $designer_id > 0 ? pubDesigner($conn, $designer_id) : null;

if ($designer_id > 0 && !$designer) {
    http_response_code(404);
}

if ($designer) {

    $designs = pubDesigns($conn, "", 0, 1, 24, (int) $designer["user_id"])["rows"];
    $specs   = pubSpecializations($designer["specializations"]);

    ?>

    <section class="pub-hero compact">

        <div class="pub-hero-inner">

            <a href="talent.php" class="pub-back"><i class="fa-solid fa-arrow-left"></i> All talent</a>

            <div class="pub-profile-head">

                <?php pubAvatar($designer["full_name"], $designer["profile_picture"], "pub-avatar xlarge"); ?>

                <div>
                    <h1><?= pubEsc($designer["full_name"]); ?></h1>
                    <p><?= pubEsc($designer["headline"] ?: "Fashion designer"); ?></p>
                </div>

            </div>

        </div>

    </section>

    <section class="pub-section">

        <div class="pub-container pub-two-col">

            <aside class="pub-side">

                <h3>About</h3>

                <p><?= $designer["bio"] ? nl2br(pubEsc($designer["bio"])) : "This designer has not added a bio yet."; ?></p>

                <ul class="pub-facts">

                    <?php if (!empty($designer["location"])) { ?>
                        <li><i class="fa-solid fa-location-dot"></i> <?= pubEsc($designer["location"]); ?></li>
                    <?php } ?>

                    <li><i class="fa-solid fa-briefcase"></i> <?= (int) $designer["experience_years"]; ?> years experience</li>

                    <?php if (!empty($designer["website"])) { ?>
                        <li>
                            <i class="fa-solid fa-globe"></i>
                            <a href="<?= pubEsc(pubWebUrl($designer["website"])); ?>" target="_blank" rel="noopener noreferrer"><?= pubEsc($designer["website"]); ?></a>
                        </li>
                    <?php } ?>

                    <?php if (!empty($designer["portfolio_link"])) { ?>
                        <li>
                            <i class="fa-solid fa-link"></i>
                            <a href="<?= pubEsc(pubWebUrl($designer["portfolio_link"])); ?>" target="_blank" rel="noopener noreferrer">Portfolio</a>
                        </li>
                    <?php } ?>

                </ul>

                <?php pubChips($specs); ?>

                <?php if ($pub_role === "") { ?>
                    <a href="../auth/login.php" class="pub-btn">Sign in to contact</a>
                <?php } ?>

            </aside>

            <div>

                <h2 class="pub-subtitle">Designs (<?= count($designs); ?>)</h2>

                <?php if (!$designs) { ?>

                    <?php pubEmpty("No designs yet", "This designer has not uploaded any designs."); ?>

                <?php } else { ?>

                    <div class="pub-grid designs two">

                        <?php foreach ($designs as $design) { pubDesignCard($design, $pub_role); } ?>

                    </div>

                <?php } ?>

            </div>

        </div>

    </section>

    <?php

} elseif ($designer_id > 0) {

    pubHero("TALENT", "Designer not found", "This profile does not exist or is no longer available.");

    echo '<section class="pub-section"><div class="pub-container">';
    pubEmpty("Nothing here", "Go back to the list of designers.");
    echo '<p class="pub-center"><a href="talent.php" class="pub-btn">All talent</a></p></div></section>';

} else {

    $q        = pubText("q");
    $page     = pubPage();
    $per_page = 12;

    $result    = pubDesigners($conn, $q, $page, $per_page);
    $designers = $result["rows"];
    $total     = $result["total"];

    $form = '
        <form class="pub-filters" method="get">
            <div class="pub-field grow">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" value="' . pubEsc($q) . '" placeholder="Search by name, skill, style or city" maxlength="80">
            </div>
            <button type="submit" class="pub-btn">Search</button>
        </form>';

    pubHero("DISCOVER TALENT", "Meet the designers shaping fashion", "Explore portfolios from emerging fashion designers.", $form);

    ?>

    <section class="pub-section">

        <div class="pub-container">

            <p class="pub-count">
                <?= $total; ?> <?= $total === 1 ? "designer" : "designers"; ?>
                <?php if ($q !== "") { ?>
                    <a href="talent.php" class="pub-clear">Clear search</a>
                <?php } ?>
            </p>

            <?php if (!$designers) { ?>

                <?php pubEmpty("No designers found", "Try a different name, skill or city."); ?>

            <?php } else { ?>

                <div class="pub-grid designers">

                    <?php foreach ($designers as $item) { pubDesignerCard($item); } ?>

                </div>

                <?= pubPager($total, $per_page, $page); ?>

            <?php } ?>

        </div>

    </section>

    <?php
}

include "../include/public_foot.php";

?>
