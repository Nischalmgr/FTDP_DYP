<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLogin();

$user_id     = (int) $_SESSION["user_id"];
$filter_type = isset($_GET["type"]) ? (int) $_GET["type"] : 0;

$all_types = getAllDesignTypes($conn);

$base_sql = "
    SELECT

        d.id,

        d.title,

        d.image,

        d.created_at,

        u.full_name AS designer_name,

        COUNT(DISTINCT dl.id) AS like_count,
        (SELECT COUNT(*) FROM design_comments dc WHERE dc.design_id = d.id) AS comment_count,

        GROUP_CONCAT(DISTINCT dt.name SEPARATOR ', ') AS type_names,

        MAX(CASE WHEN dl2.user_id = ? THEN 1 ELSE 0 END) AS liked_by_me,

        MAX(CASE WHEN sd.user_id = ? THEN 1 ELSE 0 END) AS saved_by_me

    FROM designs d

    INNER JOIN users u
        ON u.id = d.user_id

    LEFT JOIN design_likes dl
        ON dl.design_id = d.id

    LEFT JOIN design_likes dl2
        ON dl2.design_id = d.id AND dl2.user_id = ?

    LEFT JOIN saved_designs sd
        ON sd.design_id = d.id AND sd.user_id = ?

    LEFT JOIN design_type_mapping dtm
        ON dtm.design_id = d.id

    LEFT JOIN design_types dt
        ON dt.id = dtm.design_type_id
";

if ($filter_type > 0) {

    $sql = $base_sql . "
        WHERE d.id IN (
            SELECT design_id
            FROM design_type_mapping
            WHERE design_type_id = ?
        )
        GROUP BY d.id
        ORDER BY d.created_at DESC
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iiiii",
        $user_id,
        $user_id,
        $user_id,
        $user_id,
        $filter_type
    );

} else {

    $sql = $base_sql . "
        GROUP BY d.id
        ORDER BY d.created_at DESC
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iiii",
        $user_id,
        $user_id,
        $user_id,
        $user_id
    );

}

$stmt->execute();

$result = $stmt->get_result();

$design_feed_data = [];

while ($design = $result->fetch_assoc()) {

    $design_feed_data[] = $design;

}

$stmt->close();

?>
