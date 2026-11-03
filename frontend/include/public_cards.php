<?php

function pubAvatar(string $name, ?string $picture, string $class = "pub-avatar"): void
{
    $src = pubImage($picture);

    echo '<span class="' . pubEsc($class) . '">';

    if ($src !== "") {
        echo '<img src="' . pubEsc($src) . '" alt="">';
    } else {
        echo pubEsc(pubInitial($name));
    }

    echo "</span>";
}

function pubChips(array $items): void
{
    if (!$items) {
        return;
    }

    echo '<div class="pub-chips">';

    foreach ($items as $item) {
        echo '<span class="pub-chip">' . pubEsc($item) . "</span>";
    }

    echo "</div>";
}

function pubHero(string $label, string $title, string $text, string $form = ""): void
{
    echo '<section class="pub-hero"><div class="pub-hero-inner">';
    echo '<span class="pub-eyebrow">' . pubEsc($label) . "</span>";
    echo "<h1>" . pubEsc($title) . "</h1>";
    echo "<p>" . pubEsc($text) . "</p>";
    echo $form;
    echo "</div></section>";
}

function pubEmpty(string $title, string $text): void
{
    echo '<div class="pub-empty"><i class="fa-regular fa-face-smile"></i>';
    echo "<h3>" . pubEsc($title) . "</h3><p>" . pubEsc($text) . "</p></div>";
}

function pubJobCard(array $job, string $role): void
{
    $meta = [];

    if (!empty($job["location"])) {
        $meta[] = '<span><i class="fa-solid fa-location-dot"></i> ' . pubEsc($job["location"]) . "</span>";
    }

    if (!empty($job["employment_type"])) {
        $meta[] = '<span><i class="fa-regular fa-clock"></i> ' . pubEsc($job["employment_type"]) . "</span>";
    }

    if (!empty($job["salary"])) {
        $meta[] = '<span><i class="fa-solid fa-coins"></i> ' . pubEsc($job["salary"]) . "</span>";
    }

    if (!empty($job["deadline"])) {
        $meta[] = '<span><i class="fa-regular fa-calendar"></i> Apply by ' . pubEsc(pubDate($job["deadline"])) . "</span>";
    }

    echo '<article class="pub-job">';
    echo '<div class="pub-job-top">';

    pubAvatar($job["company_name"], $job["company_logo"]);

    echo '<div class="pub-job-title">';
    echo "<h3>" . pubEsc($job["title"]) . "</h3>";
    echo '<a href="../pages/companies.php?id=' . (int) $job["company_id"] . '">' . pubEsc($job["company_name"]) . "</a>";
    echo "</div>";
    echo '<span class="pub-ago">' . pubEsc(pubAgo($job["created_at"])) . "</span>";
    echo "</div>";

    echo '<div class="pub-meta">' . implode("", $meta) . "</div>";
    echo "<p>" . pubEsc(pubExcerpt($job["description"], 220)) . "</p>";

    if ($role === "" || $role === "designer") {
        echo '<a href="' . pubEsc(pubApplyUrl($role)) . '" class="pub-btn">Apply <i class="fa-solid fa-arrow-right"></i></a>';
    }

    echo "</article>";
}

function pubDesignCard(array $design, string $role): void
{
    $image = pubImage($design["image"]);
    $types = $design["type_names"] ? array_map("trim", explode(",", $design["type_names"])) : [];

    echo '<article class="pub-design">';
    echo '<div class="pub-design-image">';

    if ($image !== "") {
        echo '<img src="' . pubEsc($image) . '" alt="' . pubEsc($design["title"]) . '" loading="lazy">';
    } else {
        echo '<i class="fa-regular fa-image"></i>';
    }

    echo "</div>";
    echo '<div class="pub-design-body">';
    echo "<h3>" . pubEsc($design["title"]) . "</h3>";

    echo '<div class="pub-design-author">';

    pubAvatar($design["designer_name"], $design["designer_picture"], "pub-avatar small");

    if (!empty($design["designer_id"])) {
        echo '<a href="../pages/talent.php?id=' . (int) $design["designer_id"] . '">' . pubEsc($design["designer_name"]) . "</a>";
    } else {
        echo "<span>" . pubEsc($design["designer_name"]) . "</span>";
    }

    echo "</div>";

    if (!empty($design["description"])) {
        echo "<p>" . pubEsc(pubExcerpt($design["description"], 110)) . "</p>";
    }

    pubChips($types);

    echo '<div class="pub-design-footer">';

    if ($role === "") {
        echo '<button type="button" class="pub-action" data-login-required data-message="Please log in to like designs.">';
        echo '<i class="fa-regular fa-heart"></i> ' . (int) $design["like_count"] . "</button>";
        echo '<button type="button" class="pub-action" data-login-required data-message="Please log in to comment on designs.">';
        echo '<i class="fa-regular fa-comment"></i> ' . (int) $design["comment_count"] . "</button>";
    } else {
        $feed = pubEsc(pubFeedUrl($role));
        echo '<a href="' . $feed . '" class="pub-action"><i class="fa-regular fa-heart"></i> ' . (int) $design["like_count"] . "</a>";
        echo '<a href="' . $feed . '" class="pub-action"><i class="fa-regular fa-comment"></i> ' . (int) $design["comment_count"] . "</a>";
    }

    echo '<span class="pub-ago">' . pubEsc(pubDate($design["created_at"])) . "</span>";
    echo "</div></div></article>";
}

function pubCompanyCard(array $company): void
{
    $open = (int) $company["open_jobs"];

    echo '<a class="pub-company" href="../pages/companies.php?id=' . (int) $company["id"] . '">';
    echo '<div class="pub-company-top">';

    pubAvatar($company["company_name"], $company["logo"], "pub-avatar large");

    echo "<div><h3>" . pubEsc($company["company_name"]) . "</h3>";

    if (!empty($company["industry"])) {
        echo "<span>" . pubEsc($company["industry"]) . "</span>";
    }

    echo "</div></div>";

    $meta = [];

    if (!empty($company["location"])) {
        $meta[] = '<span><i class="fa-solid fa-location-dot"></i> ' . pubEsc($company["location"]) . "</span>";
    }

    if (!empty($company["company_size"])) {
        $meta[] = '<span><i class="fa-solid fa-users"></i> ' . pubEsc($company["company_size"]) . "</span>";
    }

    echo '<div class="pub-meta">' . implode("", $meta) . "</div>";

    if (!empty($company["description"])) {
        echo "<p>" . pubEsc(pubExcerpt($company["description"], 140)) . "</p>";
    }

    echo '<span class="pub-company-jobs">' . $open . " open " . ($open === 1 ? "job" : "jobs") . ' <i class="fa-solid fa-arrow-right"></i></span>';
    echo "</a>";
}

function pubDesignerCard(array $designer): void
{
    $specs = array_slice(pubSpecializations($designer["specializations"]), 0, 3);

    echo '<a class="pub-designer" href="../pages/talent.php?id=' . (int) $designer["id"] . '">';

    pubAvatar($designer["full_name"], $designer["profile_picture"], "pub-avatar xlarge");

    echo "<h3>" . pubEsc($designer["full_name"]) . "</h3>";

    if (!empty($designer["headline"])) {
        echo '<p class="pub-headline">' . pubEsc($designer["headline"]) . "</p>";
    }

    if (!empty($designer["location"])) {
        echo '<span class="pub-location"><i class="fa-solid fa-location-dot"></i> ' . pubEsc($designer["location"]) . "</span>";
    }

    pubChips($specs);

    echo '<span class="pub-designer-stats">';
    echo (int) $designer["design_count"] . " designs · " . (int) $designer["experience_years"] . " yrs experience";
    echo "</span></a>";
}

function pubLegal(string $label, string $title, string $intro, array $sections): void
{
    pubHero($label, $title, "Last updated: 3 November 2026");

    echo '<section class="pub-section"><div class="pub-container pub-legal">';
    echo "<p class=\"pub-lead\">" . pubEsc($intro) . "</p>";

    foreach ($sections as $heading => $paragraphs) {
        echo "<h2>" . pubEsc($heading) . "</h2>";

        foreach ((array) $paragraphs as $paragraph) {
            echo "<p>" . pubEsc($paragraph) . "</p>";
        }
    }

    echo '<p class="pub-legal-contact">Questions about this page? <a href="contact.php">Contact us</a>.</p>';
    echo "</div></section>";
}

?>
