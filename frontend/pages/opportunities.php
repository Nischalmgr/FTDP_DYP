<?php

$pub_title = "Opportunities";
$pub_nav   = "opportunities";

include "../include/public_head.php";

$q        = pubText("q");
$type     = pubText("type", 20);
$location = pubText("location");
$page     = pubPage();
$per_page = 10;

$result = pubJobs($conn, $q, $type, $location, $page, $per_page);
$jobs   = $result["rows"];
$total  = $result["total"];

$form = '
    <form class="pub-filters" method="get">
        <div class="pub-field grow">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="q" value="' . pubEsc($q) . '" placeholder="Job title, company or keyword" maxlength="80">
        </div>
        <div class="pub-field">
            <i class="fa-solid fa-location-dot"></i>
            <input type="text" name="location" value="' . pubEsc($location) . '" placeholder="Location" maxlength="80">
        </div>
        <select name="type" aria-label="Job type">
            <option value="">All types</option>';

foreach (["Full-Time", "Part-Time", "Internship", "Contract", "Freelance"] as $option) {
    $form .= '<option value="' . $option . '"' . ($type === $option ? " selected" : "") . ">" . $option . "</option>";
}

$form .= '
        </select>
        <button type="submit" class="pub-btn">Search</button>
    </form>';

pubHero("OPPORTUNITIES", "Find your next fashion role", "Browse the jobs posted by fashion brands and studios.", $form);

?>

<section class="pub-section">

    <div class="pub-container">

        <p class="pub-count">
            <?= $total; ?> open <?= $total === 1 ? "job" : "jobs"; ?>
            <?php if ($q !== "" || $type !== "" || $location !== "") { ?>
                <a href="opportunities.php" class="pub-clear">Clear filters</a>
            <?php } ?>
        </p>

        <?php if (!$jobs) { ?>

            <?php pubEmpty("No jobs found", "Try a different search or check back soon for new roles."); ?>

        <?php } else { ?>

            <div class="pub-list">

                <?php foreach ($jobs as $job) { pubJobCard($job, $pub_role); } ?>

            </div>

            <?= pubPager($total, $per_page, $page); ?>

        <?php } ?>

    </div>

</section>

<?php include "../include/public_foot.php"; ?>
