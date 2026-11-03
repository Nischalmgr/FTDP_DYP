<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/company_helpers.php";

requireCompanyLogin();

$user_id    = (int) $_SESSION["user_id"];
$company_id = getCompanyId($conn, $user_id);

$designer_user_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$focus_design     = isset($_GET["design"]) ? (int) $_GET["design"] : 0;
$focus_application = isset($_GET["application"]) ? (int) $_GET["application"] : 0;

$profile_stmt = $conn->prepare("
    SELECT
        u.id AS user_id,
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
        d.experience_years,
        d.id AS designer_id
    FROM users u
    LEFT JOIN designers d ON d.user_id = u.id
    WHERE u.id = ? AND u.role = 'designer'
    LIMIT 1
");

$profile_stmt->bind_param("i", $designer_user_id);
$profile_stmt->execute();

$designer = $profile_stmt->get_result()->fetch_assoc();

$profile_stmt->close();

if (!$designer) {

    $_SESSION["feed_flash"] = ["type" => "error", "message" => "That designer wasn't found."];

    header("Location: design_feed.php");
    exit;
}

$designer_specs = [];

if (!empty($designer["designer_id"])) {

    $spec_stmt = $conn->prepare("
        SELECT specialization_name
        FROM designer_specializations
        WHERE designer_id = ?
        ORDER BY id ASC
    ");

    $spec_stmt->bind_param("i", $designer["designer_id"]);
    $spec_stmt->execute();

    $spec_result = $spec_stmt->get_result();

    while ($row = $spec_result->fetch_assoc()) {
        $designer_specs[] = $row["specialization_name"];
    }

    $spec_stmt->close();
}

$portfolio_stats = getDesignerPortfolioStats($conn, $designer_user_id);

$designs_sql = "
    SELECT
        d.id,
        d.title,
        d.description,
        d.image,
        d.created_at,
        COUNT(DISTINCT dl.id) AS like_count,
        (SELECT COUNT(*) FROM design_comments dc WHERE dc.design_id = d.id) AS comment_count,
        GROUP_CONCAT(DISTINCT dt.name SEPARATOR ', ') AS type_names,
        MAX(CASE WHEN dl2.user_id = ? THEN 1 ELSE 0 END) AS liked_by_me,
        MAX(CASE WHEN sd.user_id = ? THEN 1 ELSE 0 END)  AS saved_by_me
    FROM designs d
    LEFT JOIN design_likes dl  ON dl.design_id = d.id
    LEFT JOIN design_likes dl2 ON dl2.design_id = d.id AND dl2.user_id = ?
    LEFT JOIN saved_designs sd ON sd.design_id = d.id AND sd.user_id = ?
    LEFT JOIN design_type_mapping dtm ON dtm.design_id = d.id
    LEFT JOIN design_types dt ON dt.id = dtm.design_type_id
    WHERE d.user_id = ?
    GROUP BY d.id
    ORDER BY d.created_at DESC
";

$designs_stmt = $conn->prepare($designs_sql);

$designs_stmt->bind_param("iiiii", $user_id, $user_id, $user_id, $user_id, $designer_user_id);
$designs_stmt->execute();

$designs_result = $designs_stmt->get_result();

$designer_designs = [];

while ($row = $designs_result->fetch_assoc()) {
    $designer_designs[] = $row;
}

$designs_stmt->close();

$apps_stmt = $conn->prepare("
    SELECT
        ja.id AS application_id,
        ja.application_status,
        ja.applied_at,
        ja.cover_letter,
        ja.resume,
        j.id AS job_id,
        j.title AS job_title
    FROM job_applications ja
    JOIN jobs j      ON j.id = ja.job_id
    JOIN designers d ON d.id = ja.designer_id
    WHERE j.company_id = ? AND d.user_id = ?
    ORDER BY ja.applied_at DESC, ja.id DESC
");

$apps_stmt->bind_param("ii", $company_id, $designer_user_id);
$apps_stmt->execute();

$apps_result = $apps_stmt->get_result();

$designer_applications = [];

while ($row = $apps_result->fetch_assoc()) {
    $designer_applications[] = $row;
}

$apps_stmt->close();

?>
