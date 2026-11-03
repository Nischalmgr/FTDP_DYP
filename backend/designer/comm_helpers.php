<?php

require_once __DIR__ . "/db_helpers.php";

function csrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["csrf_token"];
}

function verifyCsrf($token): bool
{
    return
        isset($_SESSION["csrf_token"]) &&
        is_string($token) &&
        $token !== "" &&
        hash_equals($_SESSION["csrf_token"], $token);
}

function formatAge(int $seconds, ?string $datetime = null): string
{
    if ($seconds < 60) {
        return "Just now";
    }

    if ($seconds < 3600) {
        return floor($seconds / 60) . " min ago";
    }

    if ($seconds < 86400) {
        $h = (int) floor($seconds / 3600);
        return $h . ($h === 1 ? " hour ago" : " hours ago");
    }

    if ($seconds < 604800) {
        $d = (int) floor($seconds / 86400);
        return $d . ($d === 1 ? " day ago" : " days ago");
    }

    return $datetime ? date("M j, Y", strtotime($datetime)) : "Long ago";
}

function getUserBasic(mysqli $conn, int $user_id): ?array
{
    $sql = "
        SELECT
            u.id,
            u.full_name,
            u.role,
            u.profile_picture,
            (
                SELECT c.company_name
                FROM companies c
                WHERE c.user_id = u.id
                LIMIT 1
            ) AS company_name
        FROM users u
        WHERE u.id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if (!$row) {
        return null;
    }

    $name = $row["full_name"];

    if ($row["role"] === "company" && !empty($row["company_name"])) {
        $name = $row["company_name"];
    }

    return [
        "id"              => (int) $row["id"],
        "name"            => $name,
        "role"            => $row["role"],
        "profile_picture" => $row["profile_picture"],
    ];
}

function createNotification(
    mysqli $conn,
    int $user_id,
    string $type,
    string $title,
    string $message,
    ?string $link = null
): bool {

    $sql = "
        INSERT INTO notifications (user_id, type, title, message, link)
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $title = mb_substr($title, 0, 255);

    $stmt->bind_param("issss", $user_id, $type, $title, $message, $link);

    $ok = $stmt->execute();

    $stmt->close();

    return $ok;
}

function notificationExists(
    mysqli $conn,
    int $user_id,
    string $type,
    string $message
): bool {

    $sql = "
        SELECT id
        FROM notifications
        WHERE user_id = ? AND type = ? AND message = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("iss", $user_id, $type, $message);
    $stmt->execute();

    $exists = $stmt->get_result()->num_rows > 0;

    $stmt->close();

    return $exists;
}

function notifyDesignLike(mysqli $conn, int $design_id, int $liker_id): void
{
    $stmt = $conn->prepare("SELECT user_id, title FROM designs WHERE id = ? LIMIT 1");

    $stmt->bind_param("i", $design_id);
    $stmt->execute();

    $design = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if (!$design) {
        return;
    }

    $owner_id = (int) $design["user_id"];

    if ($owner_id === $liker_id) {
        return;
    }

    $liker = getUserBasic($conn, $liker_id);

    if (!$liker) {
        return;
    }

    $message = $liker["name"] . " liked your design \"" . $design["title"] . "\".";

    if (notificationExists($conn, $owner_id, "like", $message)) {
        return;
    }

    createNotification(
        $conn,
        $owner_id,
        "like",
        "New like on your design",
        $message,
        "my_designs.php"
    );
}

function notifyDesignComment(
    mysqli $conn,
    int $design_id,
    int $commenter_id,
    string $comment
): void {

    $stmt = $conn->prepare("SELECT user_id, title FROM designs WHERE id = ? LIMIT 1");

    $stmt->bind_param("i", $design_id);
    $stmt->execute();

    $design = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if (!$design) {
        return;
    }

    $owner_id = (int) $design["user_id"];

    if ($owner_id === $commenter_id) {
        return;
    }

    $commenter = getUserBasic($conn, $commenter_id);

    if (!$commenter) {
        return;
    }

    $snippet = mb_strimwidth(
        preg_replace("/\s+/", " ", $comment),
        0,
        100,
        "…"
    );

    $message =
        $commenter["name"] . " commented on \"" . $design["title"] . "\": " . $snippet;

    createNotification(
        $conn,
        $owner_id,
        "comment",
        "New comment on your design",
        $message,
        "my_designs.php"
    );
}

function notifyApplicationStatus(
    mysqli $conn,
    int $application_id,
    string $new_status
): void {

    if (!in_array($new_status, ["reviewed", "shortlisted", "rejected", "hired"], true)) {
        return;
    }

    $sql = "
        SELECT
            d.user_id,
            j.title,
            c.company_name
        FROM job_applications ja
        JOIN designers d ON d.id = ja.designer_id
        JOIN jobs j      ON j.id = ja.job_id
        JOIN companies c ON c.id = j.company_id
        WHERE ja.id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $application_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if (!$row) {
        return;
    }

    $job     = "\"" . $row["title"] . "\" at " . $row["company_name"];

    $titles = [
        "reviewed"    => "Application reviewed",
        "shortlisted" => "You've been shortlisted",
        "rejected"    => "Application update",
        "hired"       => "You're hired!",
    ];

    $messages = [
        "reviewed"    => "Your application for $job has been reviewed.",
        "shortlisted" => "Good news! You've been shortlisted for $job.",
        "rejected"    => "Your application for $job was not successful this time. Keep going — more roles are waiting.",
        "hired"       => "Congratulations! You've been hired for $job.",
    ];

    createNotification(
        $conn,
        (int) $row["user_id"],
        "application",
        $titles[$new_status],
        $messages[$new_status],
        "my_jobs.php"
    );
}

function notifyNewApplication(mysqli $conn, int $application_id): void
{
    $sql = "
        SELECT
            j.id AS job_id,
            j.title,
            c.user_id AS company_user_id,
            u.full_name AS designer_name
        FROM job_applications ja
        JOIN jobs j       ON j.id = ja.job_id
        JOIN companies c  ON c.id = j.company_id
        JOIN designers d  ON d.id = ja.designer_id
        JOIN users u      ON u.id = d.user_id
        WHERE ja.id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $application_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if (!$row) {
        return;
    }

    createNotification(
        $conn,
        (int) $row["company_user_id"],
        "application",
        "New application received",
        $row["designer_name"] . " applied for \"" . $row["title"] . "\".",
        "applicants.php?job=" . (int) $row["job_id"]
    );
}

function notifyNewMessage(
    mysqli $conn,
    int $sender_id,
    int $receiver_id,
    string $message
): void {

    $sender   = getUserBasic($conn, $sender_id);
    $receiver = getUserBasic($conn, $receiver_id);

    if (!$sender || !$receiver) {
        return;
    }

    $link = "messages.php?with=" . $sender_id;

    $title   = "New message from " . $sender["name"];
    $snippet = mb_strimwidth(preg_replace("/\s+/", " ", $message), 0, 120, "…");

    if ($link !== null) {

        $update_sql = "
            UPDATE notifications
            SET title = ?, message = ?, created_at = NOW()
            WHERE user_id = ?
              AND type = 'message'
              AND link = ?
              AND is_read = 'N'
        ";

        $stmt = $conn->prepare($update_sql);

        $title_cut = mb_substr($title, 0, 255);

        $stmt->bind_param("ssis", $title_cut, $snippet, $receiver_id, $link);
        $stmt->execute();

        $updated = $stmt->affected_rows > 0;

        $stmt->close();

        if ($updated) {
            return;
        }
    }

    createNotification($conn, $receiver_id, "message", $title, $snippet, $link);
}

function getUnreadNotificationCount(mysqli $conn, int $user_id): int
{
    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS c FROM notifications WHERE user_id = ? AND is_read = 'N'"
    );

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $count = (int) $stmt->get_result()->fetch_assoc()["c"];

    $stmt->close();

    return $count;
}

function getUnreadMessageCount(mysqli $conn, int $user_id): int
{
    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS c FROM messages WHERE receiver_id = ? AND is_read = 'N'"
    );

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $count = (int) $stmt->get_result()->fetch_assoc()["c"];

    $stmt->close();

    return $count;
}

function getUserProfilePicture(mysqli $conn, int $user_id): ?string
{
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ? LIMIT 1");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    return $row && !empty($row["profile_picture"]) ? $row["profile_picture"] : null;
}

?>
