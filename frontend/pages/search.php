<?php

$pub_title = "Search";
$pub_nav   = "search";

include "../include/public_head.php";

$q = pubText("q");

$designers = $jobs = $companies = $designs = [];
$totals    = ["designers" => 0, "jobs" => 0, "companies" => 0, "designs" => 0];

if ($q !== "") {

    $found = pubDesigners($conn, $q, 1, 6);
    $designers = $found["rows"];
    $totals["designers"] = $found["total"];

    $found = pubDesigns($conn, $q, 0, 1, 6);
    $designs = $found["rows"];
    $totals["designs"] = $found["total"];

    $found = pubJobs($conn, $q, "", "", 1, 6);
    $jobs = $found["rows"];
    $totals["jobs"] = $found["total"];

    $found = pubCompanies($conn, $q, "", 1, 6);
    $companies = $found["rows"];
    $totals["companies"] = $found["total"];
}

$all = array_sum($totals);

$form = '
    <form class="pub-filters" method="get" action="search.php">
        <div class="pub-field grow">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="q" value="' . pubEsc($q) . '" placeholder="Search talent, skills, jobs, companies" maxlength="80" autofocus>
        </div>
        <button type="submit" class="pub-btn">Search</button>
    </form>';

pubHero(
    "SEARCH",
    $q === "" ? "Search FTDP" : "Results for “" . $q . "”",
    $q === "" ? "Look for designers, designs, jobs and companies." : $all . " " . ($all === 1 ? "result" : "results") . " found.",
    $form
);

?>

<section class="pub-section">

    <div class="pub-container">

        <?php if ($q === "") { ?>

            <?php pubEmpty("What are you looking for?", "Type a name, skill, style, job title or company above."); ?>

        <?php } elseif ($all === 0) { ?>

            <?php pubEmpty("No results", "Nothing matched your search. Try a shorter or different word."); ?>

        <?php } else { ?>

            <?php if ($designers) { ?>

                <div class="pub-result-head">
                    <h2>Talent <small><?= $totals["designers"]; ?></small></h2>
                    <a href="talent.php?q=<?= urlencode($q); ?>">See all</a>
                </div>

                <div class="pub-grid designers">
                    <?php foreach ($designers as $item) { pubDesignerCard($item); } ?>
                </div>

            <?php } ?>

            <?php if ($designs) { ?>

                <div class="pub-result-head">
                    <h2>Designs <small><?= $totals["designs"]; ?></small></h2>
                    <a href="community.php?q=<?= urlencode($q); ?>">See all</a>
                </div>

                <div class="pub-grid designs">
                    <?php foreach ($designs as $design) { pubDesignCard($design, $pub_role); } ?>
                </div>

            <?php } ?>

            <?php if ($jobs) { ?>

                <div class="pub-result-head">
                    <h2>Jobs <small><?= $totals["jobs"]; ?></small></h2>
                    <a href="opportunities.php?q=<?= urlencode($q); ?>">See all</a>
                </div>

                <div class="pub-list">
                    <?php foreach ($jobs as $job) { pubJobCard($job, $pub_role); } ?>
                </div>

            <?php } ?>

            <?php if ($companies) { ?>

                <div class="pub-result-head">
                    <h2>Companies <small><?= $totals["companies"]; ?></small></h2>
                    <a href="companies.php?q=<?= urlencode($q); ?>">See all</a>
                </div>

                <div class="pub-grid companies">
                    <?php foreach ($companies as $item) { pubCompanyCard($item); } ?>
                </div>

            <?php } ?>

        <?php } ?>

    </div>

</section>

<?php include "../include/public_foot.php"; ?>
