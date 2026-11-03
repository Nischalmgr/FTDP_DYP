<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/company_helpers.php";
require_once __DIR__ . "/../designer/comm_helpers.php";
require_once __DIR__ . "/../designer/profile_helpers.php";

requireCompanyLogin();

$user_id    = (int) $_SESSION["user_id"];
$company_id = getCompanyId($conn, $user_id);

$company_sizes = ["1-10", "11-50", "51-200", "201-500", "501-1000", "1000+"];

function loadCompanyProfile(mysqli $conn, int $user_id, int $company_id): array
{
    $stmt = $conn->prepare("
        SELECT
            u.full_name,
            u.email,
            u.profile_picture,
            u.cover_picture,
            u.created_at,
            c.company_name,
            c.industry,
            c.website,
            c.company_size,
            c.location,
            c.description
        FROM users u
        LEFT JOIN companies c ON c.id = ?
        WHERE u.id = ?
        LIMIT 1
    ");

    $stmt->bind_param("ii", $company_id, $user_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc() ?: [];

    $stmt->close();

    return $row;
}

$profile_errors  = [];
$photo_errors    = [];
$password_errors = [];

$profile_flash = $_SESSION["profile_flash"] ?? null;
unset($_SESSION["profile_flash"]);

$posted_profile = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $form_action = $_POST["form_action"] ?? "";

    if (empty($_POST) && (int) ($_SERVER["CONTENT_LENGTH"] ?? 0) > 0) {

        $profile_flash = [
            "type"    => "error",
            "message" => "The images are too large for the server. Please choose smaller images.",
        ];

    } elseif (!verifyCsrf($_POST["csrf"] ?? "")) {

        $profile_flash = [
            "type"    => "error",
            "message" => "Your session expired. Please try again.",
        ];

    } elseif ($form_action === "profile") {

        $company_name = trim($_POST["company_name"] ?? "");
        $contact_name = trim($_POST["full_name"] ?? "");
        $industry     = trim($_POST["industry"] ?? "");
        $size         = trim($_POST["company_size"] ?? "");
        $location     = trim($_POST["location"] ?? "");
        $website      = normalizeUrl($_POST["website"] ?? "");
        $description  = trim($_POST["description"] ?? "");

        $posted_profile = [
            "company_name" => $company_name,
            "full_name"    => $contact_name,
            "industry"     => $industry,
            "company_size" => $size,
            "location"     => $location,
            "website"      => $website,
            "description"  => $description,
        ];

        if (mb_strlen($company_name) < 2 || mb_strlen($company_name) > 150) {
            $profile_errors[] = "Company name must be between 2 and 150 characters.";
        }

        if (mb_strlen($contact_name) < 2 || mb_strlen($contact_name) > 100) {
            $profile_errors[] = "Contact name must be between 2 and 100 characters.";
        }

        if (mb_strlen($industry) > 100) {
            $profile_errors[] = "Industry can be up to 100 characters.";
        }

        if ($size !== "" && mb_strlen($size) > 50) {
            $profile_errors[] = "Company size is too long.";
        }

        if (mb_strlen($location) > 150) {
            $profile_errors[] = "Location can be up to 150 characters.";
        }

        if ($website !== "" && (!isValidWebUrl($website) || strlen($website) > 255)) {
            $profile_errors[] = "Please enter a valid website address.";
        }

        if (mb_strlen($description) > 3000) {
            $profile_errors[] = "Description can be up to 3000 characters.";
        }

        if (count($profile_errors) === 0) {

            $industry_db = $industry    !== "" ? $industry    : null;
            $size_db     = $size        !== "" ? $size        : null;
            $location_db = $location    !== "" ? $location    : null;
            $website_db  = $website     !== "" ? $website     : null;
            $desc_db     = $description !== "" ? $description : null;

            try {

                $conn->begin_transaction();

                $u = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
                $u->bind_param("si", $contact_name, $user_id);
                $u->execute();
                $u->close();

                $c = $conn->prepare("
                    UPDATE companies
                    SET company_name = ?,
                        industry = ?,
                        website = ?,
                        company_size = ?,
                        location = ?,
                        description = ?
                    WHERE id = ?
                ");

                $c->bind_param(
                    "ssssssi",
                    $company_name,
                    $industry_db,
                    $website_db,
                    $size_db,
                    $location_db,
                    $desc_db,
                    $company_id
                );

                $c->execute();
                $c->close();

                $conn->commit();

                $_SESSION["user_name"] = $contact_name;

                $_SESSION["profile_flash"] = [
                    "type"    => "success",
                    "message" => "Company profile updated.",
                ];

                header("Location: profile.php");
                exit;

            } catch (Throwable $e) {

                $conn->rollback();

                $profile_errors[] = "Something went wrong while saving. Please try again.";
            }
        }

    } elseif ($form_action === "photos") {

        $cur_stmt = $conn->prepare("SELECT profile_picture, cover_picture FROM users WHERE id = ? LIMIT 1");
        $cur_stmt->bind_param("i", $user_id);
        $cur_stmt->execute();

        $current = $cur_stmt->get_result()->fetch_assoc() ?: [
            "profile_picture" => null,
            "cover_picture"   => null,
        ];

        $cur_stmt->close();

        $no_file = ["error" => UPLOAD_ERR_NO_FILE];

        $avatar_error = null;
        $cover_error  = null;

        $new_avatar = saveProfileImage(
            $_FILES["profile_picture"] ?? $no_file,
            "profiles",
            "logo",
            $user_id,
            3 * 1048576,
            $avatar_error
        );

        $new_cover = saveProfileImage(
            $_FILES["cover_picture"] ?? $no_file,
            "covers",
            "cover",
            $user_id,
            5 * 1048576,
            $cover_error
        );

        if ($avatar_error) {
            $photo_errors[] = "Logo: " . $avatar_error;
        }

        if ($cover_error) {
            $photo_errors[] = "Cover photo: " . $cover_error;
        }

        $remove_avatar = !empty($_POST["remove_profile_picture"]);
        $remove_cover  = !empty($_POST["remove_cover_picture"]);

        if (count($photo_errors) === 0 && !$new_avatar && !$new_cover && !$remove_avatar && !$remove_cover) {
            $photo_errors[] = "Choose a photo to upload, or tick a remove option.";
        }

        if (count($photo_errors) > 0) {

            deleteProfileUpload($new_avatar);
            deleteProfileUpload($new_cover);

        } else {

            $final_avatar = $new_avatar ?? ($remove_avatar ? null : $current["profile_picture"]);
            $final_cover  = $new_cover  ?? ($remove_cover  ? null : $current["cover_picture"]);

            $save = $conn->prepare("UPDATE users SET profile_picture = ?, cover_picture = ? WHERE id = ?");
            $save->bind_param("ssi", $final_avatar, $final_cover, $user_id);
            $ok = $save->execute();
            $save->close();

            if ($ok) {

                if ($final_avatar !== $current["profile_picture"]) {
                    deleteProfileUpload($current["profile_picture"]);
                }

                if ($final_cover !== $current["cover_picture"]) {
                    deleteProfileUpload($current["cover_picture"]);
                }

                $_SESSION["profile_flash"] = [
                    "type"    => "success",
                    "message" => "Photos updated.",
                ];

                header("Location: profile.php");
                exit;
            }

            deleteProfileUpload($new_avatar);
            deleteProfileUpload($new_cover);

            $photo_errors[] = "Something went wrong while saving. Please try again.";
        }

    } elseif ($form_action === "password") {

        $current_pw = (string) ($_POST["current_password"] ?? "");
        $new_pw     = (string) ($_POST["new_password"] ?? "");
        $confirm_pw = (string) ($_POST["confirm_password"] ?? "");

        $pw_stmt = $conn->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
        $pw_stmt->bind_param("i", $user_id);
        $pw_stmt->execute();

        $pw_row = $pw_stmt->get_result()->fetch_assoc();

        $pw_stmt->close();

        if (!$pw_row || !password_verify($current_pw, $pw_row["password"])) {
            $password_errors[] = "Your current password is incorrect.";
        }

        if (strlen($new_pw) < 8) {
            $password_errors[] = "New password must be at least 8 characters.";
        } elseif (strlen($new_pw) > 72) {
            $password_errors[] = "New password can be at most 72 characters.";
        } elseif (!preg_match("/[A-Za-z]/", $new_pw) || !preg_match("/[0-9]/", $new_pw)) {
            $password_errors[] = "New password needs at least one letter and one number.";
        }

        if ($new_pw !== $confirm_pw) {
            $password_errors[] = "New password and confirmation don't match.";
        }

        if (count($password_errors) === 0 && $new_pw === $current_pw) {
            $password_errors[] = "Your new password must be different from the current one.";
        }

        if (count($password_errors) === 0) {

            $hash = password_hash($new_pw, PASSWORD_DEFAULT);

            $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->bind_param("si", $hash, $user_id);
            $ok = $upd->execute();
            $upd->close();

            if ($ok) {

                session_regenerate_id(true);

                $_SESSION["profile_flash"] = [
                    "type"    => "success",
                    "message" => "Password changed.",
                ];

                header("Location: profile.php");
                exit;
            }

            $password_errors[] = "Something went wrong while saving. Please try again.";
        }
    }
}

$profile_data = loadCompanyProfile($conn, $user_id, $company_id);

$form_values = $profile_data;

if ($posted_profile !== null) {
    $form_values = array_merge($profile_data, $posted_profile);
}

?>
