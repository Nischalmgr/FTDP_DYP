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
        content="width=device-width, initial-scale=1.0"
    >

    <title>Contact Us | FTDP</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >
     <link
        rel="stylesheet"
        href="../css/style.css">

    <link rel="stylesheet" href="../css/contact.css">
    <link rel="stylesheet" href="../css/public.css">

</head>

<body>

<?php include '../include/header.php'; ?>

<section class="contact-hero">

    <div class="contact-hero-content">

        <span class="contact-label">
            GET IN TOUCH
        </span>

        <h1>
            Let's create
            <span>something</span>
            together.
        </h1>

        <p>
            Whether you are a designer looking for
            opportunities or a company searching for
            creative talent, we'd love to hear from you.
        </p>

    </div>

    <div class="contact-hero-number">
        04
    </div>

</section>

<section class="contact-section">

    <div class="contact-container">

        <div class="contact-info">

            <span class="section-label">
                CONTACT FTDP
            </span>

            <h2>
                Have a question?
                <span>Talk to us.</span>
            </h2>

            <p class="contact-description">

                We are here to help you connect,
                discover and grow within the fashion
                community.

            </p>

            <div class="contact-detail">

                <div class="contact-detail-icon">

                    <i class="fa-regular fa-envelope"></i>

                </div>

                <div>

                    <span>
                        EMAIL
                    </span>

                    <a href="mailto:hello@ftdp.com">
                        hello@ftdp.com
                    </a>

                </div>

            </div>

            <div class="contact-detail">

                <div class="contact-detail-icon">

                    <i class="fa-solid fa-phone"></i>

                </div>

                <div>

                    <span>
                        PHONE
                    </span>

                    <a href="tel:+9779800000000">
                        +977 9800000000
                    </a>

                </div>

            </div>

            <div class="contact-detail">

                <div class="contact-detail-icon">

                    <i class="fa-solid fa-location-dot"></i>

                </div>

                <div>

                    <span>
                        LOCATION
                    </span>

                    <p>
                        Kathmandu, Nepal
                    </p>

                </div>

            </div>

            <div class="contact-social">

                <span>
                    FOLLOW US
                </span>

                <div class="contact-social-icons">

                    <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a href="https://www.linkedin.com" target="_blank" rel="noopener noreferrer" aria-label="Linkedin">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>

                    <a href="https://www.tiktok.com" target="_blank" rel="noopener noreferrer" aria-label="Tiktok">
                        <i class="fa-brands fa-tiktok"></i>
                    </a>

                </div>

            </div>

        </div>

        <div class="contact-form-wrapper">

            <div class="contact-form-header">

                <span>
                    SEND A MESSAGE
                </span>

                <h3>
                    We'd love to hear
                    <em>from you.</em>
                </h3>

            </div>

            <form
                action=""
                method="POST"
                class="contact-form"
            >

                <div class="contact-form-group">

                    <label for="name">
                        YOUR NAME
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your name"
                        required
                    >

                </div>

                <div class="contact-form-group">

                    <label for="email">
                        EMAIL ADDRESS
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>

                <div class="contact-form-group">

                    <label for="subject">
                        SUBJECT
                    </label>

                    <select
                        id="subject"
                        name="subject"
                        required
                    >

                        <option value="">
                            Select a subject
                        </option>

                        <option value="general">
                            General enquiry
                        </option>

                        <option value="designer">
                            Designer support
                        </option>

                        <option value="company">
                            Company enquiry
                        </option>

                        <option value="partnership">
                            Partnership
                        </option>

                        <option value="other">
                            Other
                        </option>

                    </select>

                </div>

                <div class="contact-form-group">

                    <label for="message">
                        MESSAGE
                    </label>

                    <textarea
                        id="message"
                        name="message"
                        rows="6"
                        placeholder="Tell us how we can help..."
                        required
                    ></textarea>

                </div>

                <button
                    type="submit"
                    class="contact-submit"
                >

                    Send Message

                    <i class="fa-solid fa-arrow-right"></i>

                </button>

                <p class="form-note">

                    By submitting this form, you agree
                    to be contacted by the FTDP team.

                </p>

            </form>

        </div>

    </div>

</section>

<section class="contact-strip">

    <div class="contact-strip-inner">

        <div>

            <span>
                FOR DESIGNERS
            </span>

            <h3>
                Ready to showcase your work?
            </h3>

        </div>

        <a
            href="../auth/register_designer.php"
            class="main-btn main-btn-primary"
        >
            Join as Designer
            <i class="fa-solid fa-arrow-right"></i>
        </a>

    </div>

</section>

<?php include '../include/footer.php'; ?>

</body>

</html>
