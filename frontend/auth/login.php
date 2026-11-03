<?php

session_start();

$error =
    $_SESSION["login_error"]
    ?? "";

unset(
    $_SESSION["login_error"]
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

    <title>FTDP | Sign In</title>

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

    <div class="auth-card login-card">

        <div class="auth-heading">

            <span class="auth-label">
                WELCOME BACK
            </span>

            <h1>
                Sign in to   <a href="../pages/home.php" class="logo">
        <img src="../assets/icons/logo.jpg" alt="FTDP" class="logo-img">
    </a>
            </h1>

            <p>
                Access your account and continue
                connecting with the fashion industry.
            </p>

        </div>

        <?php if ($error): ?>

            <div class="auth-error">

                <i
                    class="fa-solid fa-circle-exclamation"
                ></i>

                <span>
                    <?= htmlspecialchars($error) ?>
                </span>

            </div>

        <?php endif; ?>

        <form
            action="../../backend/process/login.php"
            method="POST"
            class="auth-form"
        >

            <div class="form-group">

                <label>
                    Email Address
                    <span>*</span>
                </label>

                <input
                    type="email"
                    name="email"
                    required
                    autocomplete="email"
                    placeholder="you@example.com"
                >

            </div>

            <div class="form-group">

                <label>
                    Password
                    <span>*</span>
                </label>

                <div class="password-box">

                    <input
                        id="login-password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        data-target="login-password"
                    >

                        <i
                            class="fa-regular fa-eye"
                        ></i>

                    </button>

                </div>

            </div>

            <div class="login-options">

                <label class="remember-me">

                    <input
                        type="checkbox"
                        name="remember"
                    >

                    <span>
                        Remember me
                    </span>

                </label>

            </div>

            <button
                type="submit"
                class="auth-button"
            >

                Sign In

                <i
                    class="fa-solid fa-arrow-right"
                ></i>

            </button>

        </form>

        <div class="auth-footer">

            Don't have an account?

            <a href="register.php">
                Create an account
            </a>

        </div>

    </div>

</div>

<script src="../../js/auth.js"></script>

</body>

</html>
