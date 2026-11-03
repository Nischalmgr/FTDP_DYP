<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLogin();

$user_id     = (int) $_SESSION["user_id"];
$designer_id = getDesignerId($conn, $user_id);

$saved_designs_sql = "
    SELECT

        d.id,

        d.title,

        d.description,

        d.image,

        d.created_at,

        u.full_name AS designer_name,

        u.profile_picture,

        sd.saved_at,

        (
            SELECT COUNT(*)

            FROM design_likes dl_count

            WHERE dl_count.design_id = d.id

        ) AS like_count,

        CASE

            WHEN dl.id IS NULL THEN 0

            ELSE 1

        END AS has_liked,

        (
            SELECT GROUP_CONCAT(
                DISTINCT dt.name
                ORDER BY dt.name
                SEPARATOR ', '
            )

            FROM design_type_mapping dtm

            INNER JOIN design_types dt
                ON dt.id = dtm.design_type_id

            WHERE dtm.design_id = d.id

        ) AS type_names

    FROM saved_designs sd

    INNER JOIN designs d

        ON d.id = sd.design_id

    INNER JOIN users u

        ON u.id = d.user_id

    LEFT JOIN design_likes dl

        ON dl.design_id = d.id

        AND dl.user_id = ?

    WHERE sd.user_id = ?

    ORDER BY sd.saved_at DESC
";

$saved_designs_stmt = $conn->prepare($saved_designs_sql);

$saved_designs_stmt->bind_param(
    "ii",
    $user_id,
    $user_id
);

$saved_designs_stmt->execute();

$saved_designs_result = $saved_designs_stmt->get_result();

$saved_designs_data = [];

while ($design = $saved_designs_result->fetch_assoc()) {

    $saved_designs_data[] = $design;

}

$saved_designs_stmt->close();

?>
