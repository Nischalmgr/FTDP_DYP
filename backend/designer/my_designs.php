<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLogin();

$user_id = (int) $_SESSION["user_id"];

$my_designs_sql = "
    SELECT

        d.id,

        d.title,

        d.description,

        d.image,

        d.created_at,

        COUNT(DISTINCT dl.id) AS like_count,

        GROUP_CONCAT(DISTINCT dt.name SEPARATOR ', ') AS type_names

    FROM designs d

    LEFT JOIN design_likes dl
        ON dl.design_id = d.id

    LEFT JOIN design_type_mapping dtm
        ON dtm.design_id = d.id

    LEFT JOIN design_types dt
        ON dt.id = dtm.design_type_id

    WHERE d.user_id = ?

    GROUP BY d.id

    ORDER BY d.created_at DESC
";

$my_designs_stmt = $conn->prepare($my_designs_sql);

$my_designs_stmt->bind_param(
    "i",
    $user_id
);

$my_designs_stmt->execute();

$my_designs_result = $my_designs_stmt->get_result();

$my_designs_data = [];

while ($design = $my_designs_result->fetch_assoc()) {

    $my_designs_data[] = $design;

}

$my_designs_stmt->close();

?>
