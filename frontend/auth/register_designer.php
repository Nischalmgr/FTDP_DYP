 <?php

session_start();

$error =
    $_SESSION["register_error"]
    ?? "";

$old =
    $_SESSION["register_old"]
    ?? [];

unset(
    $_SESSION["register_error"],
    $_SESSION["register_old"]
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>FTDP | Designer Registration</title>

    <link
        rel="stylesheet"
        href="../css/auth.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    >

</head>

<body class="auth-page">

<div class="auth-container">

    <div class="auth-brand">

            <a href="../pages/home.php" class="logo">
           <img src="../assets/icons/logo.jpg" alt="FTDP" class="logo-img">
        </a>

        <span>
            FASHION TALENT DISCOVERY PLATFORM
        </span>

    </div>

    <div class="auth-card auth-wide">

        <div class="auth-heading">

            <span class="auth-label">
                DESIGNER REGISTRATION
            </span>

            <h1>
                Create your <span>designer</span> account.
            </h1>

            <p>
                Showcase your work and connect with
                the fashion industry.
            </p>

        </div>

        <?php if ($error): ?>

            <div class="auth-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>

        <form
            action="../../backend/process/register.php"
            method="POST"
            class="auth-form"
        >

            <input
                type="hidden"
                name="role"
                value="designer"
            >

            <div class="form-grid">

                <div class="form-group">

                    <label>
                        Full Name <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="full_name"
                        required
                        value="<?= htmlspecialchars($old["full_name"] ?? "") ?>"
                        placeholder="Your full name"
                    >

                </div>

                <div class="form-group">

                    <label>
                        Email Address <span>*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        required
                        value="<?= htmlspecialchars($old["email"] ?? "") ?>"
                        placeholder="you@example.com"
                    >

                </div>

                <div class="form-group">

                    <label>
                        Location
                    </label>

                    <input
                        type="text"
                        name="location"
                        value="<?= htmlspecialchars($old["location"] ?? "") ?>"
                        placeholder="Kathmandu, Nepal"
                    >

                </div>

                <div class="form-group">

                    <label>
                        Professional Headline
                    </label>

                    <input
                        type="text"
                        name="headline"
                        value="<?= htmlspecialchars($old["headline"] ?? "") ?>"
                        placeholder="Fashion Designer"
                    >

                </div>

                <div class="form-group">

                    <label>
                        Years of Experience
                    </label>

                    <input
                        type="number"
                        name="years_experience"
                        min="0"
                        max="100"
                        value="<?= htmlspecialchars($old["years_experience"] ?? "0") ?>"
                    >

                </div>

                <div class="form-group">

                    <label>
                        Portfolio URL
                    </label>

                    <input
                        type="url"
                        name="portfolio_url"
                        placeholder="https://..."
                    >

                </div>

                <div class="form-group full">

                    <label>
                        Password <span>*</span>
                    </label>

                    <div class="password-box">

                        <input
                            id="designer-password"
                            type="password"
                            name="password"
                            minlength="8"
                            required
                            placeholder="Minimum 8 characters"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            data-target="designer-password"
                        >

                            <i class="fa-regular fa-eye"></i>

                        </button>

                    </div>

                </div>

            </div>

            <label class="terms">

                <input
                    type="checkbox"
                    required
                >

                <span>
                    I agree to the FTDP terms and privacy policy.
                </span>

            </label>

            <button
                type="submit"
                class="auth-button"
            >

                Create Designer Account

                <i class="fa-solid fa-arrow-right"></i>

            </button>

        </form>

        <div class="auth-footer">

            Registering as a company?

            <a href="register_company.php">
                Company Registration
            </a>

        </div>

    </div>

</div>

<script src="../../js/auth.js"></script>

</body>
</html>
