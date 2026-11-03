<?php

$pub_title = "Community";
$pub_nav   = "community";

include "../include/public_head.php";

$q        = pubText("q");
$type_id  = (int) ($_GET["type"] ?? 0);
$page     = pubPage();
$per_page = 12;

$result  = pubDesigns($conn, $q, $type_id, $page, $per_page);
$designs = $result["rows"];
$total   = $result["total"];
$types   = pubDesignTypes($conn);

$form = '
    <form class="pub-filters" method="get">
        <div class="pub-field grow">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" name="q" value="' . pubEsc($q) . '" placeholder="Search designs, designers or styles" maxlength="80">
        </div>
        <select name="type" aria-label="Design type">
            <option value="">All styles</option>';

foreach ($types as $type) {
    $form .= '<option value="' . (int) $type["id"] . '"' . ($type_id === (int) $type["id"] ? " selected" : "") . ">" . pubEsc($type["name"]) . "</option>";
}

$form .= '
        </select>
        <button type="submit" class="pub-btn">Search</button>
    </form>';

pubHero("COMMUNITY", "See what the community is creating", "Every design shared by our designers, in one place.", $form);

?>

<section class="pub-section">

    <div class="pub-container">

        <p class="pub-count">
            <?= $total; ?> <?= $total === 1 ? "post" : "posts"; ?>
            <?php if ($q !== "" || $type_id > 0) { ?>
                <a href="community.php" class="pub-clear">Clear filters</a>
            <?php } ?>
        </p>

        <?php if (!$designs) { ?>

            <?php pubEmpty("No posts found", "Try a different search or style."); ?>

        <?php } else { ?>

            <div class="pub-grid designs">

                <?php foreach ($designs as $design) { pubDesignCard($design, $pub_role); } ?>

            </div>

            <?= pubPager($total, $per_page, $page); ?>

        <?php } ?>

    </div>

</section>

<?php include "../include/public_foot.php"; ?>
