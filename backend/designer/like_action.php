<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";
require_once __DIR__ . "/comm_helpers.php";

requireRoleLoginJson(["designer", "company"]);

$user_id   = (int) $_SESSION["user_id"];
$design_id = isset($_POST["design_id"]) ? (int) $_POST["design_id"] : 0;

if ($design_id <= 0) {
    jsonResponse(["success" => false, "message" => "Invalid design"], 400);
}

$exists_stmt = $conn->prepare("SELECT id FROM designs WHERE id = ? LIMIT 1");

$exists_stmt->bind_param("i", $design_id);
$exists_stmt->execute();

$design_found = $exists_stmt->get_result()->num_rows > 0;

$exists_stmt->close();

if (!$design_found) {
    jsonResponse(["success" => false, "message" => "This design is no longer available."], 404);
}

$check_sql = "
    SELECT id
    FROM design_likes
    WHERE design_id = ? AND user_id = ?
";

$check_stmt = $conn->prepare($check_sql);

$check_stmt->bind_param(
    "ii",
    $design_id,
    $user_id
);

$check_stmt->execute();

$check_result = $check_stmt->get_result();

$existing = $check_result->fetch_assoc();

$check_stmt->close();

if ($existing) {

    $delete_sql = "DELETE FROM design_likes WHERE id = ?";

    $delete_stmt = $conn->prepare($delete_sql);

    $existing_id = (int) $existing["id"];

    $delete_stmt->bind_param("i", $existing_id);

    $delete_stmt->execute();

    $delete_stmt->close();

    $liked = false;

} else {

    $insert_sql = "
        INSERT INTO design_likes (design_id, user_id)
        VALUES (?, ?)
    ";

    $insert_stmt = $conn->prepare($insert_sql);

    $insert_stmt->bind_param(
        "ii",
        $design_id,
        $user_id
    );

    $insert_stmt->execute();

    $insert_stmt->close();

    $liked = true;

    notifyDesignLike($conn, $design_id, $user_id);

}

$count_sql = "
    SELECT COUNT(*) AS c
    FROM design_likes
    WHERE design_id = ?
";

$count_stmt = $conn->prepare($count_sql);

$count_stmt->bind_param("i", $design_id);

$count_stmt->execute();

$count_result = $count_stmt->get_result();

$like_count = (int) $count_result->fetch_assoc()["c"];

$count_stmt->close();

jsonResponse([
    "success"    => true,
    "liked"      => $liked,
    "like_count" => $like_count,
]);

?>
