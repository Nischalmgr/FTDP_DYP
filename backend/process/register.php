<?php

session_start();

require_once __DIR__ . "/../config/config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../../frontend/auth/register.php");
    exit;

}

$role = isset($_POST["role"])
    ? trim($_POST["role"])
    : "";

$allowed_roles = [
    "designer",
    "company"
];

if (!in_array($role, $allowed_roles, true)) {

    $_SESSION["register_error"] = "Invalid registration type.";

    header("Location: ../../frontend/auth/register.php");
    exit;

}

$full_name = trim($_POST["full_name"] ?? "");
$email     = trim($_POST["email"] ?? "");
$location  = trim($_POST["location"] ?? "");
$password  = $_POST["password"] ?? "";

if (
    $full_name === "" ||
    $email === "" ||
    $password === ""
) {

    $_SESSION["register_error"] =
        "Please fill in all required fields.";

    $_SESSION["register_old"] = $_POST;

    $redirect =
        ($role === "company")
        ? "../../frontend/auth/register_company.php"
        : "../../frontend/auth/register_designer.php";

    header("Location: " . $redirect);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION["register_error"] =
        "Please enter a valid email address.";

    $_SESSION["register_old"] = $_POST;

    $redirect =
        ($role === "company")
        ? "../../frontend/auth/register_company.php"
        : "../../frontend/auth/register_designer.php";

    header("Location: " . $redirect);
    exit;
}

if (strlen($password) < 8) {

    $_SESSION["register_error"] =
        "Password must contain at least 8 characters.";

    $_SESSION["register_old"] = $_POST;

    $redirect =
        ($role === "company")
        ? "../../frontend/auth/register_company.php"
        : "../../frontend/auth/register_designer.php";

    header("Location: " . $redirect);
    exit;
}

$check_sql = "
    SELECT id
    FROM users
    WHERE email = ?
    LIMIT 1
";

$check_stmt = $conn->prepare($check_sql);

$check_stmt->bind_param(
    "s",
    $email
);

$check_stmt->execute();

$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {

    $check_stmt->close();

    $_SESSION["register_error"] =
        "Email is already registered.";

    $_SESSION["register_old"] = $_POST;

    $redirect =
        ($role === "company")
        ? "../../frontend/auth/register_company.php"
        : "../../frontend/auth/register_designer.php";

    header("Location: " . $redirect);
    exit;
}

$check_stmt->close();

$password_hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$conn->begin_transaction();

try {

    $user_sql = "
        INSERT INTO users
        (
            full_name,
            email,
            password,
            role,
            is_verified
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            'Y'
        )
    ";

    $user_stmt = $conn->prepare($user_sql);

    $user_stmt->bind_param(
        "ssss",
        $full_name,
        $email,
        $password_hash,
        $role
    );

    $user_stmt->execute();

    $user_id = $conn->insert_id;

    $user_stmt->close();

    if ($role === "designer") {

        $headline =
            trim($_POST["headline"] ?? "");

        $years_experience =
            intval($_POST["years_experience"] ?? 0);

        $website =
            trim($_POST["website"] ?? "");

        $portfolio_url =
            trim($_POST["portfolio_url"] ?? "");

        $designer_sql = "
            INSERT INTO designers
            (
                user_id,
                headline,
                location,
                website,
                portfolio_link,
                experience_years
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ";

        $designer_stmt =
            $conn->prepare($designer_sql);

        $designer_stmt->bind_param(
            "issssi",
            $user_id,
            $headline,
            $location,
            $website,
            $portfolio_url,
            $years_experience
        );

        $designer_stmt->execute();

        $designer_stmt->close();
    }

    if ($role === "company") {

        $company_name =
            trim($_POST["company_name"] ?? "");

        $industry =
            trim($_POST["industry"] ?? "");

        $company_size =
            trim($_POST["company_size"] ?? "");

        $description =
            trim($_POST["description"] ?? "");

        $website =
            trim($_POST["website"] ?? "");

        $company_sql = "
            INSERT INTO companies
            (
                user_id,
                company_name,
                industry,
                website,
                company_size,
                location,
                description
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ";

        $company_stmt =
            $conn->prepare($company_sql);

        $company_stmt->bind_param(
            "issssss",
            $user_id,
            $company_name,
            $industry,
            $website,
            $company_size,
            $location,
            $description
        );

        $company_stmt->execute();

        $company_stmt->close();
    }

    $conn->commit();

    $_SESSION["registration_success"] = true;

    $_SESSION["registered_user"] = [
        "id" => $user_id,
        "name" => $full_name,
        "email" => $email,
        "role" => $role
    ];

    header(
        "Location: ../../frontend/auth/registration_success.php"
    );

    exit;

} catch (Exception $e) {

    $conn->rollback();

    $_SESSION["register_error"] =
        "Registration failed. Please try again.";

    $_SESSION["register_old"] = $_POST;

    $redirect =
        ($role === "company")
        ? "../../frontend/auth/register_company.php"
        : "../../frontend/auth/register_designer.php";

    header("Location: " . $redirect);

    exit;
}

?>
