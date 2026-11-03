<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>FTDP | Discover Fashion Talent</title>

    <link
        rel="stylesheet"
        href="../css/style.css">

    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/public.css">
    <link

        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<body>

<?php

include_once("../include/header.php");

?>

<section class="hero-video-section">

    <video
        class="hero-video"
        id="heroVideo"
        autoplay
        muted
        loop
        playsinline
        preload="auto">

        <source
            src="../assets/videos/ftdp-fashion.mp4"
            type="video/mp4">

        Your browser does not support HTML5 video.

    </video>

    <div class="hero-overlay"></div>

    <div class="hero-content">

        <div class="hero-content-inner">

            <span class="hero-small-text">
                DISCOVER THE FUTURE OF FASHION
            </span>

            <h1>
                Where <span>fashion talent</span><br>
                gets discovered.
            </h1>

            <p>
                Connect, collaborate and create opportunities
                with the people shaping the future of fashion.
            </p>

            <div class="hero-buttons">

                <a
                    href="../pages/talent.php"
                    class="hero-btn hero-btn-primary">

                    Explore Talent

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

                <a
                    href="../auth/register.php"
                    class="hero-btn hero-btn-outline">

                    Join FTDP

                </a>

            </div>

        </div>

    </div>

</section>

<section class="intro-section">

    <div class="section-container">

        <span class="section-label">
            FASHION TALENT DISCOVERY
        </span>

        <h2>FTDP</h2>

        <p>
            FTDP is a fashion talent discovery platform that connects
            emerging talent with industry professionals.
        </p>

        <p>
            Showcase your work, discover talent, and create opportunities.
        </p>

        <div class="intro-buttons">

            <a
                href="../auth/register.php"
                class="main-btn main-btn-primary">

                Join FTDP

            </a>

            <a
                href="../pages/talent.php"
                class="main-btn main-btn-outline">

                Explore Talent

            </a>

        </div>

    </div>

</section>

<section
    class="discover-section"
    id="discover">

    <div class="section-container">

        <span class="section-label">
            WHAT YOU CAN DO
        </span>

        <h2 class="discover-title">

            Discover <span>the FTDP</span>

        </h2>

        <div class="discover-grid">

            <article class="discover-item">

                <div class="discover-icon">

                    <i class="fa-solid fa-users"></i>

                </div>

                <h3>
                    Discover Talent
                </h3>

                <p>
                    Find unique and emerging fashion talent across different disciplines.
                </p>

            </article>

            <article class="discover-item">

                <div class="discover-icon">

                    <i class="fa-solid fa-link"></i>

                </div>

                <h3>
                    Connect &amp; Collaborate
                </h3>

                <p>
                    Build meaningful connections and collaborate on exciting projects.
                </p>

            </article>

            <article class="discover-item">

                <div class="discover-icon">

                    <i class="fa-solid fa-briefcase"></i>

                </div>

                <h3>
                    Create Opportunities
                </h3>

                <p>
                    Explore jobs, internships and opportunities to grow your fashion career.
                </p>

            </article>

            <article class="discover-item">

                <div class="discover-icon">

                    <i class="fa-solid fa-globe"></i>

                </div>

                <h3>
                    Join Community
                </h3>

                <p>
                    Be part of a global fashion community that inspires and supports.
                </p>

            </article>

            <article class="discover-item">

                <div class="discover-icon">

                    <i class="fa-regular fa-star"></i>

                </div>

                <h3>
                    Showcase Your Work
                </h3>

                <p>
                    Build your profile, showcase your portfolio and get discovered.
                </p>

            </article>

        </div>

    </div>

</section>

<section
    class="network-section"
    id="network">

    <div class="network-content">

        <div class="network-text">

            <span class="network-label">
                JOIN THE COMMUNITY
            </span>

            <h2>
                Your network.
                <br>
                Your <span>fashion.</span>
            </h2>

            <p>
                Join FTDP today and connect with the fashion world.
            </p>

            <form
                class="network-form"
                id="joinForm">

                <input
                    type="email"
                    id="emailInput"
                    placeholder="Enter your email"
                    required>

                <button
                    type="submit"
                    class="main-btn main-btn-primary">

                    Get Started

                </button>

            </form>

            <p
                class="form-message"
                id="formMessage">
            </p>

        </div>

        <div class="network-image">

            <img
                src="../assets/images/network-fashion.jpg"
                alt="Fashion talent and design">

        </div>

    </div>

</section>

<?php

include_once("../include/footer.php");

?>

<script src="../../js/home.js"></script>

</body>
</html>
