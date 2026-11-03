<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";
require_once __DIR__ . "/comm_helpers.php";
require_once __DIR__ . "/profile_helpers.php";

requireDesignerLogin();

$user_id     = (int) $_SESSION["user_id"];
$designer_id = getDesignerId($conn, $user_id);

if ($designer_id === null) {

    $create = $conn->prepare("INSERT INTO designers (user_id) VALUES (?)");

    $create->bind_param("i", $user_id);
    $create->execute();

    $designer_id = (int) $conn->insert_id;

    $create->close();
}

function parseSpecializations(string $raw): array
{
    $items = [];
    $seen  = [];

    foreach (explode(",", $raw) as $part) {

        $name = trim(preg_replace("/\s+/", " ", $part));

        if ($name === "") {
            continue;
        }

        $name = mb_substr($name, 0, 100);
        $key  = mb_strtolower($name);

        if (isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $items[]    = $name;

        if (count($items) >= 15) {
            break;
        }
    }

    return $items;
}

function loadDesignerProfile(mysqli $conn, int $user_id, int $designer_id): array
{
    $sql = "
        SELECT
            u.full_name,
            u.email,
            u.profile_picture,
            u.cover_picture,
            u.created_at,
            d.headline,
            d.bio,
            d.location,
            d.website,
            d.portfolio_link,
            d.experience_years
        FROM users u
        LEFT JOIN designers d ON d.id = ?
        WHERE u.id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ii", $designer_id, $user_id);
    $stmt->execute();

    $profile = $stmt->get_result()->fetch_assoc() ?: [];

    $stmt->close();

    $spec_stmt = $conn->prepare("
        SELECT specialization_name
        FROM designer_specializations
        WHERE designer_id = ?
        ORDER BY id ASC
    ");

    $spec_stmt->bind_param("i", $designer_id);
    $spec_stmt->execute();

    $spec_result = $spec_stmt->get_result();

    $profile["specializations"] = [];

    while ($row = $spec_result->fetch_assoc()) {
        $profile["specializations"][] = $row["specialization_name"];
    }

    $spec_stmt->close();

    return $profile;
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

        $full_name  = trim($_POST["full_name"] ?? "");
        $headline   = trim($_POST["headline"] ?? "");
        $bio        = trim($_POST["bio"] ?? "");
        $location   = trim($_POST["location"] ?? "");
        $website    = normalizeUrl($_POST["website"] ?? "");
        $portfolio  = normalizeUrl($_POST["portfolio_link"] ?? "");
        $exp_raw    = trim($_POST["experience_years"] ?? "");
        $spec_list  = parseSpecializations($_POST["specializations"] ?? "");

        $posted_profile = [
            "full_name"        => $full_name,
            "headline"         => $headline,
            "bio"              => $bio,
            "location"         => $location,
            "website"          => $website,
            "portfolio_link"   => $portfolio,
            "experience_years" => $exp_raw,
            "specializations"  => $spec_list,
        ];

        if (mb_strlen($full_name) < 2 || mb_strlen($full_name) > 100) {
            $profile_errors[] = "Full name must be between 2 and 100 characters.";
        }

        if (mb_strlen($headline) > 255) {
            $profile_errors[] = "Headline can be up to 255 characters.";
        }

        if (mb_strlen($bio) > 2000) {
            $profile_errors[] = "Bio can be up to 2000 characters.";
        }

        if (mb_strlen($location) > 150) {
            $profile_errors[] = "Location can be up to 150 characters.";
        }

        if ($website !== "" && (!isValidWebUrl($website) || strlen($website) > 255)) {
            $profile_errors[] = "Please enter a valid website address.";
        }

        if ($portfolio !== "" && (!isValidWebUrl($portfolio) || strlen($portfolio) > 255)) {
            $profile_errors[] = "Please enter a valid portfolio link.";
        }

        $experience = 0;

        if ($exp_raw !== "") {

            if (!ctype_digit($exp_raw) || (int) $exp_raw > 60) {
                $profile_errors[] = "Experience must be a whole number between 0 and 60.";
            } else {
                $experience = (int) $exp_raw;
            }
        }

        if (count($profile_errors) === 0) {

            $headline_db  = $headline  !== "" ? $headline  : null;
            $bio_db       = $bio       !== "" ? $bio       : null;
            $location_db  = $location  !== "" ? $location  : null;
            $website_db   = $website   !== "" ? $website   : null;
            $portfolio_db = $portfolio !== "" ? $portfolio : null;

            try {

                $conn->begin_transaction();

                $u = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
                $u->bind_param("si", $full_name, $user_id);
                $u->execute();
                $u->close();

                $d = $conn->prepare("
                    UPDATE designers
                    SET headline = ?,
                        bio = ?,
                        location = ?,
                        website = ?,
                        portfolio_link = ?,
                        experience_years = ?
                    WHERE id = ?
                ");

                $d->bind_param(
                    "sssssii",
                    $headline_db,
                    $bio_db,
                    $location_db,
                    $website_db,
                    $portfolio_db,
                    $experience,
                    $designer_id
                );

                $d->execute();
                $d->close();

                $del = $conn->prepare("DELETE FROM designer_specializations WHERE designer_id = ?");
                $del->bind_param("i", $designer_id);
                $del->execute();
                $del->close();

                if (count($spec_list) > 0) {

                    $ins = $conn->prepare("
                        INSERT INTO designer_specializations (designer_id, specialization_name)
                        VALUES (?, ?)
                    ");

                    foreach ($spec_list as $spec_name) {
                        $ins->bind_param("is", $designer_id, $spec_name);
                        $ins->execute();
                    }

                    $ins->close();
                }

                $conn->commit();

                $_SESSION["user_name"] = $full_name;

                $_SESSION["profile_flash"] = [
                    "type"    => "success",
                    "message" => "Profile updated.",
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
            "avatar",
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
            $photo_errors[] = "Profile photo: " . $avatar_error;
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

$profile_data = loadDesignerProfile($conn, $user_id, $designer_id);

$form_values = $profile_data;

if ($posted_profile !== null) {
    $form_values = array_merge($profile_data, $posted_profile);
}

$specialization_suggestions = array_column(getAllDesignTypes($conn), "name");

?>
