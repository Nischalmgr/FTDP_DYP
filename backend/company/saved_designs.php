<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/company_helpers.php";

requireCompanyLogin();

$user_id = (int) $_SESSION["user_id"];

$saved_stmt = $conn->prepare("
    SELECT
        d.id,
        d.title,
        d.image,
        d.created_at,
        u.id AS designer_user_id,
        u.full_name AS designer_name,
        COUNT(DISTINCT dl.id) AS like_count,
        GROUP_CONCAT(DISTINCT dt.name SEPARATOR ', ') AS type_names
    FROM saved_designs sd
    INNER JOIN designs d ON d.id = sd.design_id
    INNER JOIN users u   ON u.id = d.user_id
    LEFT JOIN design_likes dl ON dl.design_id = d.id
    LEFT JOIN design_type_mapping dtm ON dtm.design_id = d.id
    LEFT JOIN design_types dt ON dt.id = dtm.design_type_id
    WHERE sd.user_id = ?
    GROUP BY d.id
    ORDER BY MAX(sd.saved_at) DESC
");

$saved_stmt->bind_param("i", $user_id);
$saved_stmt->execute();

$saved_result = $saved_stmt->get_result();

$saved_designs_data = [];

while ($design = $saved_result->fetch_assoc()) {
    $saved_designs_data[] = $design;
}

$saved_stmt->close();

?>
