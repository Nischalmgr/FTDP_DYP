<?php

$pub_title = "Companies";
$pub_nav   = "companies";

include "../include/public_head.php";

$company_id = (int) ($_GET["id"] ?? 0);
$company    = $company_id > 0 ? pubCompany($conn, $company_id) : null;

if ($company_id > 0 && !$company) {
    http_response_code(404);
}

if ($company) {

    $pub_title = $company["company_name"];

    $jobs = pubJobs($conn, "", "", "", 1, 50, (int) $company["id"])["rows"];

    $cover = pubImage($company["cover"]);

    ?>

    <section class="pub-hero compact">

        <div class="pub-hero-inner">

            <a href="companies.php" class="pub-back"><i class="fa-solid fa-arrow-left"></i> All companies</a>

            <div class="pub-profile-head">

                <?php pubAvatar($company["company_name"], $company["logo"], "pub-avatar xlarge"); ?>

                <div>
                    <h1><?= pubEsc($company["company_name"]); ?></h1>
                    <p><?= pubEsc($company["industry"] ?: "Fashion"); ?></p>
                </div>

            </div>

        </div>

    </section>

    <section class="pub-section">

        <div class="pub-container pub-two-col">

            <aside class="pub-side">

                <h3>About</h3>

                <p><?= $company["description"] ? nl2br(pubEsc($company["description"])) : "This company has not added a description yet."; ?></p>

                <ul class="pub-facts">

                    <?php if (!empty($company["location"])) { ?>
                        <li><i class="fa-solid fa-location-dot"></i> <?= pubEsc($company["location"]); ?></li>
                    <?php } ?>

                    <?php if (!empty($company["company_size"])) { ?>
                        <li><i class="fa-solid fa-users"></i> <?= pubEsc($company["company_size"]); ?> employees</li>
                    <?php } ?>

                    <?php if (!empty($company["website"])) { ?>
                        <li>
                            <i class="fa-solid fa-globe"></i>
                            <a href="<?= pubEsc(pubWebUrl($company["website"])); ?>" target="_blank" rel="noopener noreferrer"><?= pubEsc($company["website"]); ?></a>
                        </li>
                    <?php } ?>

                </ul>

                <?php if ($pub_role === "") { ?>
                    <a href="../auth/login.php" class="pub-btn">Sign in to contact</a>
                <?php } ?>

            </aside>

            <div>

                <h2 class="pub-subtitle">Open jobs (<?= count($jobs); ?>)</h2>

                <?php if (!$jobs) { ?>

                    <?php pubEmpty("No open jobs", "This company has no open roles right now."); ?>

                <?php } else { ?>

                    <div class="pub-list">

                        <?php foreach ($jobs as $job) { pubJobCard($job, $pub_role); } ?>

                    </div>

                <?php } ?>

            </div>

        </div>

    </section>

    <?php

} elseif ($company_id > 0) {

    pubHero("COMPANIES", "Company not found", "This company does not exist or is no longer available.");

    echo '<section class="pub-section"><div class="pub-container">';
    pubEmpty("Nothing here", "Go back to the list of companies.");
    echo '<p class="pub-center"><a href="companies.php" class="pub-btn">All companies</a></p></div></section>';

} else {

    $q        = pubText("q");
    $industry = pubText("industry", 100);
    $page     = pubPage();
    $per_page = 12;

    $result     = pubCompanies($conn, $q, $industry, $page, $per_page);
    $companies  = $result["rows"];
    $total      = $result["total"];
    $industries = pubIndustries($conn);

    $form = '
        <form class="pub-filters" method="get">
            <div class="pub-field grow">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" value="' . pubEsc($q) . '" placeholder="Search companies" maxlength="80">
            </div>
            <select name="industry" aria-label="Industry">
                <option value="">All industries</option>';

    foreach ($industries as $option) {
        $form .= '<option value="' . pubEsc($option) . '"' . ($industry === $option ? " selected" : "") . ">" . pubEsc($option) . "</option>";
    }

    $form .= '
            </select>
            <button type="submit" class="pub-btn">Search</button>
        </form>';

    pubHero("COMPANIES", "Brands and studios hiring on FTDP", "Meet the companies looking for fashion talent.", $form);

    ?>

    <section class="pub-section">

        <div class="pub-container">

            <p class="pub-count">
                <?= $total; ?> <?= $total === 1 ? "company" : "companies"; ?>
                <?php if ($q !== "" || $industry !== "") { ?>
                    <a href="companies.php" class="pub-clear">Clear filters</a>
                <?php } ?>
            </p>

            <?php if (!$companies) { ?>

                <?php pubEmpty("No companies found", "Try a different search."); ?>

            <?php } else { ?>

                <div class="pub-grid companies">

                    <?php foreach ($companies as $item) { pubCompanyCard($item); } ?>

                </div>

                <?= pubPager($total, $per_page, $page); ?>

            <?php } ?>

        </div>

    </section>

    <?php
}

include "../include/public_foot.php";

?>
