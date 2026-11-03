<?php

session_start();

if (
    !isset($_SESSION["registration_success"]) ||
    !$_SESSION["registration_success"]
) {

    header("Location: register.php");
    exit;

}

$user =
    $_SESSION["registered_user"];

unset(
    $_SESSION["registration_success"],
    $_SESSION["registered_user"]
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

    <title>FTDP | Registration Complete</title>

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

        <a href="../pages/home.php">
            FTDP
        </a>

        <span>
            FASHION TALENT DISCOVERY PLATFORM
        </span>

    </div>

    <div class="auth-card success-card">

        <div class="success-icon">

            <i class="fa-solid fa-check"></i>

        </div>

        <span class="auth-label">
            WELCOME TO FTDP
        </span>

        <h1>
            Registration <span>complete.</span>
        </h1>

        <p>

            Your

            <?= htmlspecialchars(
                ucfirst($user["role"])
            ) ?>

            account has been successfully created.

        </p>

        <div class="success-info">

            <strong>
                <?= htmlspecialchars(
                    $user["name"]
                ) ?>
            </strong>

            <span>
                <?= htmlspecialchars(
                    $user["email"]
                ) ?>
            </span>

        </div>

        <a
            href="login.php"
            class="auth-button"
        >

            Continue to Sign In

            <i class="fa-solid fa-arrow-right"></i>

        </a>

    </div>

</div>

</body>

</html>
