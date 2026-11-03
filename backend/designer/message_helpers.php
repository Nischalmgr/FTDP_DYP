<?php

require_once __DIR__ . "/comm_helpers.php";

function roleFilterSql(?string $only_role): string
{

    return $only_role === "designer" ? "'designer'" : "'designer', 'company'";
}

function getMessageablePartner(mysqli $conn, int $partner_id, ?string $only_role = null): ?array
{
    $roles = roleFilterSql($only_role);

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
            ) AS company_name,
            (
                SELECT d.headline
                FROM designers d
                WHERE d.user_id = u.id
                LIMIT 1
            ) AS headline
        FROM users u
        WHERE u.id = ?
          AND u.role IN ($roles)
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $partner_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    return $row ? formatPartner($row) : null;
}

function formatPartner(array $row): array
{
    $name = $row["full_name"];

    if ($row["role"] === "company" && !empty($row["company_name"])) {
        $name = $row["company_name"];
    }

    return [
        "id"              => (int) ($row["id"] ?? $row["partner_id"]),
        "name"            => $name,
        "role"            => $row["role"],
        "headline"        => $row["headline"] ?? null,
        "profile_picture" => !empty($row["profile_picture"]) ? $row["profile_picture"] : null,
    ];
}

function searchMessageableUsers(mysqli $conn, int $me, string $query, ?string $only_role = null): array
{
    $roles = roleFilterSql($only_role);

    $like = "%" . addcslashes($query, "%_\\") . "%";

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
            ) AS company_name,
            (
                SELECT d.headline
                FROM designers d
                WHERE d.user_id = u.id
                LIMIT 1
            ) AS headline
        FROM users u
        WHERE u.id <> ?
          AND u.role IN ($roles)
          AND (
                u.full_name LIKE ?
                OR EXISTS (
                    SELECT 1
                    FROM companies c2
                    WHERE c2.user_id = u.id
                      AND c2.company_name LIKE ?
                )
          )
        ORDER BY u.full_name ASC
        LIMIT 8
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("iss", $me, $like, $like);
    $stmt->execute();

    $result = $stmt->get_result();

    $people = [];

    while ($row = $result->fetch_assoc()) {
        $people[] = formatPartner($row);
    }

    $stmt->close();

    return $people;
}

function getConversations(mysqli $conn, int $me): array
{
    $me = (int) $me;

    $sql = "
        SELECT
            u.id AS partner_id,
            u.full_name,
            u.role,
            u.profile_picture,
            (
                SELECT c.company_name
                FROM companies c
                WHERE c.user_id = u.id
                LIMIT 1
            ) AS company_name,
            m.message AS last_message,
            m.sender_id AS last_sender_id,
            TIMESTAMPDIFF(SECOND, m.sent_at, NOW()) AS age_seconds,
            (
                SELECT COUNT(*)
                FROM messages x
                WHERE x.sender_id = u.id
                  AND x.receiver_id = $me
                  AND x.is_read = 'N'
            ) AS unread
        FROM (
            SELECT
                IF(sender_id = $me, receiver_id, sender_id) AS partner_id,
                MAX(id) AS last_id
            FROM messages
            WHERE sender_id = $me OR receiver_id = $me
            GROUP BY partner_id
        ) t
        JOIN messages m ON m.id = t.last_id
        JOIN users u    ON u.id = t.partner_id
        ORDER BY m.id DESC
        LIMIT 100
    ";

    $result = $conn->query($sql);

    $conversations = [];

    if (!$result) {
        return $conversations;
    }

    while ($row = $result->fetch_assoc()) {

        $partner = formatPartner($row);

        $preview = mb_strimwidth(
            preg_replace("/\s+/", " ", $row["last_message"]),
            0,
            80,
            "…"
        );

        $conversations[] = [
            "id"              => $partner["id"],
            "name"            => $partner["name"],
            "role"            => $partner["role"],
            "profile_picture" => $partner["profile_picture"],
            "last_message"    => $preview,
            "last_is_mine"    => ((int) $row["last_sender_id"] === $me),
            "age_seconds"     => (int) $row["age_seconds"],
            "unread"          => (int) $row["unread"],
        ];
    }

    return $conversations;
}

function getConversationMessages(
    mysqli $conn,
    int $me,
    int $partner_id,
    int $after_id = 0,
    int $limit = 200
): array {

    if ($after_id > 0) {

        $sql = "
            SELECT
                id,
                sender_id,
                message,
                sent_at,
                DATEDIFF(CURDATE(), DATE(sent_at)) AS days_ago
            FROM messages
            WHERE (
                    (sender_id = ? AND receiver_id = ?)
                 OR (sender_id = ? AND receiver_id = ?)
                  )
              AND id > ?
            ORDER BY id ASC
            LIMIT ?
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("iiiiii", $me, $partner_id, $partner_id, $me, $after_id, $limit);

    } else {

        $sql = "
            SELECT * FROM (
                SELECT
                    id,
                    sender_id,
                    message,
                    sent_at,
                    DATEDIFF(CURDATE(), DATE(sent_at)) AS days_ago
                FROM messages
                WHERE (sender_id = ? AND receiver_id = ?)
                   OR (sender_id = ? AND receiver_id = ?)
                ORDER BY id DESC
                LIMIT ?
            ) recent
            ORDER BY id ASC
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("iiiii", $me, $partner_id, $partner_id, $me, $limit);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $messages = [];

    while ($row = $result->fetch_assoc()) {
        $messages[] = formatMessageRow($row, $me);
    }

    $stmt->close();

    return $messages;
}

function formatMessageRow(array $row, int $me): array
{
    $days_ago = (int) $row["days_ago"];
    $ts       = strtotime($row["sent_at"]);

    if ($days_ago <= 0) {
        $day_label = "Today";
    } elseif ($days_ago === 1) {
        $day_label = "Yesterday";
    } else {
        $day_label = date("M j, Y", $ts);
    }

    return [
        "id"        => (int) $row["id"],
        "mine"      => ((int) $row["sender_id"] === $me),
        "message"   => $row["message"],
        "time"      => date("g:i A", $ts),
        "day_label" => $day_label,
    ];
}

function markConversationRead(mysqli $conn, int $me, int $partner_id): void
{
    $stmt = $conn->prepare("
        UPDATE messages
        SET is_read = 'Y'
        WHERE receiver_id = ? AND sender_id = ? AND is_read = 'N'
    ");

    $stmt->bind_param("ii", $me, $partner_id);
    $stmt->execute();
    $stmt->close();

    $link = "messages.php?with=" . $partner_id;

    $stmt = $conn->prepare("
        UPDATE notifications
        SET is_read = 'Y'
        WHERE user_id = ? AND type = 'message' AND link = ? AND is_read = 'N'
    ");

    $stmt->bind_param("is", $me, $link);
    $stmt->execute();
    $stmt->close();
}

?>
