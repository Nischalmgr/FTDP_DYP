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

    <title>FTDP | Company Registration</title>

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
                COMPANY REGISTRATION
            </span>

            <h1>
                Create your <span>company</span> account.
            </h1>

            <p>
                Connect with fashion talent and create
                new opportunities.
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
                value="company"
            >

            <div class="form-grid">

                <div class="form-group">

                    <label>
                        Contact Person <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="full_name"
                        required
                        value="<?= htmlspecialchars($old["full_name"] ?? "") ?>"
                        placeholder="Full name"
                    >

                </div>

                <div class="form-group">

                    <label>
                        Business Email <span>*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        required
                        value="<?= htmlspecialchars($old["email"] ?? "") ?>"
                        placeholder="hello@company.com"
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
                        Company Name <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="company_name"
                        required
                        value="<?= htmlspecialchars($old["company_name"] ?? "") ?>"
                        placeholder="Your company name"
                    >

                </div>

                <div class="form-group">

                    <label>
                        Industry
                    </label>

                    <input
                        type="text"
                        name="industry"
                        value="<?= htmlspecialchars($old["industry"] ?? "") ?>"
                        placeholder="Fashion / Apparel"
                    >

                </div>

                <div class="form-group">

                    <label>
                        Company Size
                    </label>

                    <select name="company_size">

                        <option value="">
                            Select company size
                        </option>

                        <option value="1-10">
                            1–10 employees
                        </option>

                        <option value="11-50">
                            11–50 employees
                        </option>

                        <option value="51-200">
                            51–200 employees
                        </option>

                        <option value="201-500">
                            201–500 employees
                        </option>

                        <option value="500+">
                            500+ employees
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label>
                        Website
                    </label>

                    <input
                        type="url"
                        name="website"
                        placeholder="https://yourcompany.com"
                    >

                </div>

                <div class="form-group full">

                    <label>
                        Company Description
                    </label>

                    <textarea
                        name="description"
                        rows="5"
                        placeholder="Tell the FTDP community about your company..."
                    ></textarea>

                </div>

                <div class="form-group full">

                    <label>
                        Password <span>*</span>
                    </label>

                    <div class="password-box">

                        <input
                            id="company-password"
                            type="password"
                            name="password"
                            minlength="8"
                            required
                            placeholder="Minimum 8 characters"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            data-target="company-password"
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

                Create Company Account

                <i class="fa-solid fa-arrow-right"></i>

            </button>

        </form>

        <div class="auth-footer">

            Registering as a designer?

            <a href="register_designer.php">
                Designer Registration
            </a>

        </div>

    </div>

</div>

<script src="../../js/auth.js"></script>

</body>
</html>
