<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/admin_helpers.php";

requireAdminLogin();

$admin_id = (int) $_SESSION["user_id"];

$action = $_POST["action"] ?? "";

$back = adminBackUrl(
    (string) ($_POST["back"] ?? "dashboard.php"),
    (string) ($_POST["sort"] ?? "new")
);

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !verifyCsrf($_POST["csrf"] ?? "")) {

    $_SESSION["admin_flash"] = ["type" => "error", "message" => "Your session expired. Please try again."];

    header("Location: " . $back);
    exit;
}

$ok      = false;
$message = "";
$error   = "Unknown action.";

$reason = mb_substr(trim((string) ($_POST["reason"] ?? "")), 0, 500);

function postId(string $key): int
{
    return isset($_POST[$key]) ? (int) $_POST[$key] : 0;
}

function removeUploadedFile(?string $relative_path): void
{
    if (!$relative_path) {
        return;
    }

    $uploads = realpath(__DIR__ . "/../../uploads");
    $file    = realpath(__DIR__ . "/../../" . $relative_path);

    if ($uploads && $file && strpos($file, $uploads . DIRECTORY_SEPARATOR) === 0 && is_file($file)) {
        @unlink($file);
    }
}

switch ($action) {

    case "verify":
    case "unverify":

        $user_id = postId("user_id");
        $flag    = ($action === "verify") ? "Y" : "N";

        $stmt = $conn->prepare("UPDATE users SET is_verified = ? WHERE id = ? AND role <> 'admin'");

        $stmt->bind_param("si", $flag, $user_id);
        $stmt->execute();

        $ok = $stmt->errno === 0;

        $stmt->close();

        $message = ($action === "verify")
            ? "User verified. They can log in."
            : "User unverified. They can no longer log in.";

        break;

    case "delete_user":

        $user_id = postId("user_id");

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role <> 'admin'");

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $ok = $stmt->affected_rows > 0;

        $stmt->close();

        $message = "User deleted along with their designs, jobs and messages.";
        $error   = "That user could not be deleted.";

        break;

    case "delete_design":

        $design_id = postId("design_id");

        if ($reason === "") {
            $error = "Please give a reason.";
            break;
        }

        $stmt = $conn->prepare("SELECT user_id, title, image FROM designs WHERE id = ? LIMIT 1");

        $stmt->bind_param("i", $design_id);
        $stmt->execute();

        $design = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        if (!$design) {
            $error = "That design no longer exists.";
            break;
        }

        $stmt = $conn->prepare("DELETE FROM designs WHERE id = ?");

        $stmt->bind_param("i", $design_id);
        $stmt->execute();

        $ok = $stmt->affected_rows > 0;

        $stmt->close();

        if ($ok) {

            removeUploadedFile($design["image"]);

            createNotification(
                $conn,
                (int) $design["user_id"],
                "removal",
                "Your design was removed",
                "Your design \"" . $design["title"] . "\" was removed by an admin." . ($reason !== "" ? " Reason: " . $reason : "")
            );
        }

        $message = "Design removed and the designer was notified.";
        $error   = "The design could not be removed.";

        break;

    case "delete_comment":

        $comment_id = postId("comment_id");

        if ($reason === "") {
            $error = "Please give a reason.";
            break;
        }

        $stmt = $conn->prepare("
            SELECT c.user_id, d.title
            FROM design_comments c
            JOIN designs d ON d.id = c.design_id
            WHERE c.id = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $comment_id);
        $stmt->execute();

        $comment = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        if (!$comment) {
            $error = "That comment no longer exists.";
            break;
        }

        $stmt = $conn->prepare("DELETE FROM design_comments WHERE id = ?");

        $stmt->bind_param("i", $comment_id);
        $stmt->execute();

        $ok = $stmt->affected_rows > 0;

        $stmt->close();

        if ($ok) {

            createNotification(
                $conn,
                (int) $comment["user_id"],
                "removal",
                "Your comment was removed",
                "Your comment on \"" . $comment["title"] . "\" was removed by an admin." . ($reason !== "" ? " Reason: " . $reason : "")
            );
        }

        $message = "Comment removed and the author was notified.";
        $error   = "The comment could not be removed.";

        break;

    case "close_job":
    case "reopen_job":
    case "delete_job":

        $job_id = postId("job_id");

        if ($action !== "reopen_job" && $reason === "") {
            $error = "Please give a reason.";
            break;
        }

        $stmt = $conn->prepare("
            SELECT j.title, c.user_id
            FROM jobs j
            JOIN companies c ON c.id = j.company_id
            WHERE j.id = ?
            LIMIT 1
        ");

        $stmt->bind_param("i", $job_id);
        $stmt->execute();

        $job = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        if (!$job) {
            $error = "That job no longer exists.";
            break;
        }

        if ($action === "delete_job") {

            $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
            $stmt->bind_param("i", $job_id);

        } else {

            $new_status = ($action === "close_job") ? "closed" : "open";

            $stmt = $conn->prepare("UPDATE jobs SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $new_status, $job_id);
        }

        $stmt->execute();

        $ok = $stmt->errno === 0;

        $stmt->close();

        if ($ok) {

            $company_user = (int) $job["user_id"];

            if ($action === "close_job") {

                createNotification(
                    $conn, $company_user, "general", "Your job was closed",
                    "Your job \"" . $job["title"] . "\" was closed by an admin. Reason: " . $reason
                );

                $message = "Job closed and the company was notified.";

            } elseif ($action === "reopen_job") {

                createNotification(
                    $conn, $company_user, "general", "Your job was reopened",
                    "Your job \"" . $job["title"] . "\" was reopened by an admin and is visible to designers again."
                );

                $message = "Job reopened and the company was notified.";

            } else {

                createNotification(
                    $conn, $company_user, "general", "Your job was removed",
                    "Your job \"" . $job["title"] . "\" was removed by an admin. Reason: " . $reason
                );

                $message = "Job deleted and the company was notified.";
            }

        } else {

            $error = "The job could not be changed.";
        }

        break;

    case "add_type":
    case "edit_type":

        $type_id     = postId("type_id");
        $name        = trim((string) ($_POST["name"] ?? ""));
        $description = trim((string) ($_POST["description"] ?? ""));

        if ($name === "" || mb_strlen($name) > 100) {
            $error = "Type name is required (max 100 characters).";
            break;
        }

        $stmt = $conn->prepare("SELECT id FROM design_types WHERE name = ? AND id <> ? LIMIT 1");

        $stmt->bind_param("si", $name, $type_id);
        $stmt->execute();

        $exists = $stmt->get_result()->num_rows > 0;

        $stmt->close();

        if ($exists) {
            $error = "A design type with that name already exists.";
            break;
        }

        if ($action === "add_type") {

            $stmt = $conn->prepare("INSERT INTO design_types (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            $stmt->execute();

            $ok = $stmt->affected_rows > 0;

            $stmt->close();

            $message = "Design type added.";

        } else {

            $stmt = $conn->prepare("UPDATE design_types SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $description, $type_id);
            $stmt->execute();

            $ok = $stmt->errno === 0;

            $stmt->close();

            $message = "Design type updated.";
        }

        $error = "The design type could not be saved.";

        break;

    case "delete_type":

        $type_id = postId("type_id");

        $stmt = $conn->prepare("DELETE FROM design_types WHERE id = ?");

        $stmt->bind_param("i", $type_id);
        $stmt->execute();

        $ok = $stmt->affected_rows > 0;

        $stmt->close();

        $message = "Design type deleted. Designs keep existing, they just lose this tag.";
        $error   = "That design type could not be deleted.";

        break;

    case "announce":

        $audience = $_POST["audience"] ?? "all";
        $title    = trim((string) ($_POST["title"] ?? ""));
        $body     = trim((string) ($_POST["message"] ?? ""));

        if (!in_array($audience, ["all", "designer", "company"], true)) {
            $audience = "all";
        }

        $_SESSION["announce_old"] = ["audience" => $audience, "title" => $title, "message" => $body];

        if ($title === "" || $body === "") {
            $error = "Please write a title and a message.";
            break;
        }

        if (mb_strlen($title) > 255) {
            $error = "The title is too long (max 255 characters).";
            break;
        }

        if ($audience === "all") {

            $stmt = $conn->prepare("
                INSERT INTO notifications (user_id, type, title, message)
                SELECT id, 'announcement', ?, ?
                FROM users
                WHERE role IN ('designer', 'company')
            ");

            $stmt->bind_param("ss", $title, $body);

        } else {

            $stmt = $conn->prepare("
                INSERT INTO notifications (user_id, type, title, message)
                SELECT id, 'announcement', ?, ?
                FROM users
                WHERE role = ?
            ");

            $stmt->bind_param("sss", $title, $body, $audience);
        }

        $stmt->execute();

        $sent = $stmt->affected_rows;
        $ok   = $sent > 0;

        $stmt->close();

        if ($ok) {
            unset($_SESSION["announce_old"]);
            $message = "Announcement sent to " . $sent . ($sent === 1 ? " user." : " users.");
        } else {
            $error = "There is nobody in that group yet.";
        }

        break;

    case "update_profile":

        $full_name = trim((string) ($_POST["full_name"] ?? ""));
        $email     = trim((string) ($_POST["email"] ?? ""));

        if ($full_name === "" || mb_strlen($full_name) > 100) {
            $error = "Name is required (max 100 characters).";
            break;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            $error = "Please enter a valid email address.";
            break;
        }

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1");

        $stmt->bind_param("si", $email, $admin_id);
        $stmt->execute();

        $taken = $stmt->get_result()->num_rows > 0;

        $stmt->close();

        if ($taken) {
            $error = "That email is already used by another account.";
            break;
        }

        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");

        $stmt->bind_param("ssi", $full_name, $email, $admin_id);
        $stmt->execute();

        $ok = $stmt->errno === 0;

        $stmt->close();

        if ($ok) {
            $_SESSION["user_name"]  = $full_name;
            $_SESSION["username"]   = $full_name;
            $_SESSION["user_email"] = $email;
        }

        $message = "Profile updated.";
        $error   = "Your profile could not be updated.";

        break;

    case "change_password":

        $current = (string) ($_POST["current_password"] ?? "");
        $new     = (string) ($_POST["new_password"] ?? "");
        $confirm = (string) ($_POST["confirm_password"] ?? "");

        if ($current === "" || $new === "" || $confirm === "") {
            $error = "Please fill in all password fields.";
            break;
        }

        if (strlen($new) < 8) {
            $error = "The new password must be at least 8 characters.";
            break;
        }

        if ($new !== $confirm) {
            $error = "The new passwords do not match.";
            break;
        }

        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");

        $stmt->bind_param("i", $admin_id);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        if (!$row || !password_verify($current, $row["password"])) {
            $error = "Your current password is not correct.";
            break;
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");

        $stmt->bind_param("si", $hash, $admin_id);
        $stmt->execute();

        $ok = $stmt->errno === 0;

        $stmt->close();

        $message = "Password changed.";
        $error   = "Your password could not be changed.";

        break;
}

$_SESSION["admin_flash"] = [
    "type"    => $ok ? "success" : "error",
    "message" => $ok ? $message : $error,
];

header("Location: " . $back);
exit;

?>
