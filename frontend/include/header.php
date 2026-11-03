<?php

$pub_nav  = $pub_nav ?? "";
$pub_role = (
    isset($_SESSION["logged_in"]) &&
    $_SESSION["logged_in"] === true &&
    isset($_SESSION["user_role"]) &&
    in_array($_SESSION["user_role"], ["designer", "company", "admin"], true)
) ? $_SESSION["user_role"] : "";

$pub_search_value = ($pub_nav === "search") ? htmlspecialchars(trim((string) ($_GET["q"] ?? "")), ENT_QUOTES, "UTF-8") : "";

?>
<header class="navbar">

    <a href="../pages/home.php" class="logo">

        <img src="../assets/icons/logo.jpg" alt="FTDP" class="logo-img">

    </a>

    <nav
        class="nav-menu"
        id="navMenu">

        <a
            href="../pages/talent.php"
            class="<?= $pub_nav === "talent" ? "active" : ""; ?>">

            Discover Talent

        </a>

        <a
            href="../pages/opportunities.php"
            class="<?= $pub_nav === "opportunities" ? "active" : ""; ?>">

            Opportunities

        </a>

        <a
            href="../pages/community.php"
            class="<?= $pub_nav === "community" ? "active" : ""; ?>">

            Community

        </a>

        <a
            href="../pages/companies.php"
            class="<?= $pub_nav === "companies" ? "active" : ""; ?>">

            Companies

        </a>

        <div class="nav-mobile-extra">

            <form
                action="../pages/search.php"
                method="get"
                class="mobile-search"
                role="search">

                <input
                    type="text"
                    name="q"
                    maxlength="80"
                    placeholder="Search talent, jobs, companies..."
                    aria-label="Search">

            </form>

            <?php if ($pub_role !== "") { ?>

                <a href="../pages/<?= $pub_role; ?>/dashboard.php">Dashboard</a>

            <?php } else { ?>

                <a href="../auth/login.php">Sign In</a>
                <a href="../auth/register.php">Join FTDP</a>

            <?php } ?>

        </div>

    </nav>

    <div class="nav-right">

        <form
            action="../pages/search.php"
            method="get"
            class="search-box"
            role="search">

            <i class="fa-solid fa-magnifying-glass"></i>

            <input
                type="text"
                id="talentSearch"
                name="q"
                maxlength="80"
                value="<?= $pub_search_value; ?>"
                placeholder="Search talent, jobs, companies..."
                aria-label="Search">

        </form>

        <?php if ($pub_role !== "") { ?>

            <a
                href="../pages/<?= $pub_role; ?>/dashboard.php"
                class="btn nav-join">

                Dashboard

            </a>

        <?php } else { ?>

            <a
                href="../auth/login.php"
                class="btn nav-signin">

                Sign In

            </a>

            <a
                href="../auth/register.php"
                class="btn nav-join">

                Join FTDP

            </a>

        <?php } ?>

    </div>

    <button
        type="button"
        class="mobile-menu-btn"
        id="mobileMenuBtn"
        aria-label="Open menu">

        <i class="fa-solid fa-bars"></i>

    </button>

</header>

<script src="../../js/public.js" defer></script>
