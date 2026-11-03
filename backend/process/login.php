<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../../frontend/auth/login.php");
    exit;

}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {

    $_SESSION["login_error"] =
        "Please enter your email and password.";

    header("Location: ../../frontend/auth/login.php");
    exit;

}

$sql = "
    SELECT
        id,
        role,
        full_name,
        email,
        password,
        is_verified
    FROM users
    WHERE email = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "s",
    $email
);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();

if (!$user) {

    $_SESSION["login_error"] =
        "Invalid email or password.";

    header("Location: ../../frontend/auth/login.php");
    exit;

}

if (
    !password_verify(
        $password,
        $user["password"]
    )
) {

    $_SESSION["login_error"] =
        "Invalid email or password.";

    header("Location: ../../frontend/auth/login.php");
    exit;

}

if ($user["is_verified"] !== "Y") {

    $_SESSION["login_error"] =
        "Your account has not been verified yet.";

    header("Location: ../../frontend/auth/login.php");
    exit;

}

session_regenerate_id(true);

$_SESSION["logged_in"] = true;

$_SESSION["user_id"] =
    (int)$user["id"];

$_SESSION["user_role"] =
    $user["role"];

$_SESSION["user_name"] =
    $user["full_name"];

$_SESSION["username"] =
    $user["full_name"];

$_SESSION["user_email"] =
    $user["email"];

if ($user["role"] === "designer") {

    header(
        "Location: ../../frontend/pages/designer/dashboard.php"
    );

    exit;

}

if ($user["role"] === "company") {

    header(
        "Location: ../../frontend/pages/company/dashboard.php"
    );

    exit;

}

if ($user["role"] === "admin") {

    header(
        "Location: ../../frontend/pages/admin/dashboard.php"
    );

    exit;

}

session_unset();
session_destroy();

session_start();

$_SESSION["login_error"] =
    "Your account has an invalid role.";

header("Location: ../../frontend/auth/login.php");

exit;

?>
