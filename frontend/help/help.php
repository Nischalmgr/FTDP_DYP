<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Help Center | FTDP</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
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

    <link rel="stylesheet" href="../css/help.css">

</head>

<body>

<?php include '../include/header.php'; ?>

<section class="help-hero">

    <div class="help-hero-content">

        <span class="help-label">
            FTDP HELP CENTER
        </span>

        <h1>
            How can we
            <span>help?</span>
        </h1>

        <p>
            Find answers, learn more about FTDP,
            or chat with our support assistant.
        </p>

    </div>

    <div class="help-number">
        05
    </div>

</section>

<section class="help-section">

    <div class="help-container">

        <div class="help-content">

            <span class="section-label">
                SUPPORT
            </span>

            <h2>
                Everything you need,
                <span>in one place.</span>
            </h2>

            <p>
                Whether you are a designer, fashion
                company or creative professional,
                our Help Center is here to guide you
                through FTDP.
            </p>

            <div class="help-cards">

                <div class="help-card">

                    <div class="help-card-icon">
                        <i class="fa-solid fa-user-plus"></i>
                    </div>

                    <div>

                        <h3>
                            Getting Started
                        </h3>

                        <p>
                            Learn how to create your
                            FTDP account and get started.
                        </p>

                    </div>

                </div>

                <div class="help-card">

                    <div class="help-card-icon">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>

                    <div>

                        <h3>
                            Designer Help
                        </h3>

                        <p>
                            Learn how to create and
                            showcase your designer profile.
                        </p>

                    </div>

                </div>

                <div class="help-card">

                    <div class="help-card-icon">
                        <i class="fa-solid fa-building"></i>
                    </div>

                    <div>

                        <h3>
                            Company Help
                        </h3>

                        <p>
                            Find out how companies can
                            discover fashion talent.
                        </p>

                    </div>

                </div>

                <div class="help-card">

                    <div class="help-card-icon">
                        <i class="fa-solid fa-lock"></i>
                    </div>

                    <div>

                        <h3>
                            Account & Security
                        </h3>

                        <p>
                            Get help with account,
                            login and security questions.
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <div class="help-chat">

            <div class="chat-header">

                <div class="chat-profile">

                    <div class="chat-avatar">
                        <i class="fa-solid fa-sparkles"></i>
                    </div>

                    <div>

                        <strong>
                            FTDP Assistant
                        </strong>

                        <span>
                            <i class="fa-solid fa-circle"></i>
                            Online
                        </span>

                    </div>

                </div>

            </div>

            <div
                class="chat-messages"
                id="chatMessages"
            >

                <div class="message bot-message">

                    <div class="message-avatar">
                        <i class="fa-solid fa-sparkles"></i>
                    </div>

                    <div class="message-content">

                        <p>
                            Hi! 👋 Welcome to FTDP.
                            How can I help you today?
                        </p>

                        <span>
                            FTDP Assistant
                        </span>

                    </div>

                </div>

            </div>

            <div
                class="quick-questions"
                id="quickQuestions"
            >

                <button data-message="How do I create an account?">
                    Create account
                </button>

                <button data-message="How can I join as a designer?">
                    Join as designer
                </button>

                <button data-message="How can companies find designers?">
                    For companies
                </button>

                <button data-message="I need help with my account.">
                    Account help
                </button>

            </div>

            <form
                class="chat-input-area"
                id="chatForm"
            >

                <input
                    type="text"
                    id="chatInput"
                    placeholder="Type your question..."
                    autocomplete="off"
                >

                <button
                    type="submit"
                    aria-label="Send message"
                >

                    <i class="fa-solid fa-arrow-up"></i>

                </button>

            </form>

            <div class="chat-footer">

                FTDP Support Assistant

            </div>

        </div>

    </div>

</section>

<section class="help-cta">

    <div>

        <span>
            STILL NEED HELP?
        </span>

        <h2>
            Talk to our
            <em>team.</em>
        </h2>

        <p>
            Can't find what you're looking for?
            Send us a message and our team will
            get back to you.
        </p>

        <a
            href="../pages/contact.php"
            class="main-btn main-btn-primary"
        >
            Contact Us
            <i class="fa-solid fa-arrow-right"></i>
        </a>

    </div>

</section>

<?php include '../include/footer.php'; ?>

<script src="../../js/help.js"></script>

</body>
</html>
