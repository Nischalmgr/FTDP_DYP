<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";
require_once __DIR__ . "/comm_helpers.php";

requireRoleLoginJson(["designer", "company"]);

$user_id = (int) $_SESSION["user_id"];
$is_post = $_SERVER["REQUEST_METHOD"] === "POST";

$design_id = (int) ($is_post ? ($_POST["design_id"] ?? 0) : ($_GET["design_id"] ?? 0));

if ($design_id <= 0) {
    jsonResponse(["success" => false, "message" => "Invalid design"], 400);
}

$exists = $conn->prepare("SELECT id FROM designs WHERE id = ? LIMIT 1");

$exists->bind_param("i", $design_id);
$exists->execute();

$found = $exists->get_result()->num_rows > 0;

$exists->close();

if (!$found) {
    jsonResponse(["success" => false, "message" => "This design is no longer available."], 404);
}

function commentRow(array $row, int $me): array
{
    $name = $row["full_name"];

    if ($row["role"] === "company" && !empty($row["company_name"])) {
        $name = $row["company_name"];
    }

    return [
        "id"              => (int) $row["id"],
        "name"            => $name,
        "role"            => $row["role"],
        "profile_picture" => !empty($row["profile_picture"]) ? $row["profile_picture"] : null,
        "comment"         => $row["comment"],
        "time"            => formatAge((int) $row["age_seconds"], $row["created_at"]),
        "mine"            => (int) $row["user_id"] === $me,
    ];
}

$select = "
    SELECT
        c.id,
        c.user_id,
        c.comment,
        c.created_at,
        TIMESTAMPDIFF(SECOND, c.created_at, NOW()) AS age_seconds,
        u.full_name,
        u.role,
        u.profile_picture,
        (
            SELECT co.company_name
            FROM companies co
            WHERE co.user_id = u.id
            LIMIT 1
        ) AS company_name
    FROM design_comments c
    JOIN users u ON u.id = c.user_id
";

if (!$is_post) {

    $stmt = $conn->prepare($select . " WHERE c.design_id = ? ORDER BY c.id DESC LIMIT 100");

    $stmt->bind_param("i", $design_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $comments = [];

    while ($row = $result->fetch_assoc()) {
        $comments[] = commentRow($row, $user_id);
    }

    $stmt->close();

    jsonResponse(["success" => true, "comments" => array_reverse($comments)]);
}

if (!verifyCsrf($_POST["csrf"] ?? "")) {
    jsonResponse(["success" => false, "message" => "Session expired. Please refresh the page."], 403);
}

$comment = trim((string) ($_POST["comment"] ?? ""));

if ($comment === "") {
    jsonResponse(["success" => false, "message" => "Write a comment first."], 400);
}

if (mb_strlen($comment) > 1000) {
    jsonResponse(["success" => false, "message" => "Comments can be up to 1000 characters."], 400);
}

$insert = $conn->prepare("
    INSERT INTO design_comments (design_id, user_id, comment)
    VALUES (?, ?, ?)
");

$insert->bind_param("iis", $design_id, $user_id, $comment);

if (!$insert->execute()) {
    $insert->close();
    jsonResponse(["success" => false, "message" => "Could not save the comment."], 500);
}

$comment_id = (int) $conn->insert_id;

$insert->close();

notifyDesignComment($conn, $design_id, $user_id, $comment);

$stmt = $conn->prepare($select . " WHERE c.id = ? LIMIT 1");

$stmt->bind_param("i", $comment_id);
$stmt->execute();

$row = $stmt->get_result()->fetch_assoc();

$stmt->close();

$count_stmt = $conn->prepare("SELECT COUNT(*) AS c FROM design_comments WHERE design_id = ?");

$count_stmt->bind_param("i", $design_id);
$count_stmt->execute();

$count = (int) $count_stmt->get_result()->fetch_assoc()["c"];

$count_stmt->close();

jsonResponse([
    "success"       => true,
    "comment"       => commentRow($row, $user_id),
    "comment_count" => $count,
]);

?>
