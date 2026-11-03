<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/company_helpers.php";

requireCompanyLogin();

$user_id    = (int) $_SESSION["user_id"];
$company_id = getCompanyId($conn, $user_id);

$stats_sql = "
    SELECT
        (SELECT COUNT(*) FROM jobs WHERE company_id = ? AND status = 'open')   AS open_jobs,
        (SELECT COUNT(*) FROM jobs WHERE company_id = ? AND status = 'closed') AS closed_jobs,
        COUNT(ja.id) AS total_applicants,
        COALESCE(SUM(CASE WHEN ja.application_status = 'pending'     THEN 1 ELSE 0 END), 0) AS pending,
        COALESCE(SUM(CASE WHEN ja.application_status = 'shortlisted' THEN 1 ELSE 0 END), 0) AS shortlisted,
        COALESCE(SUM(CASE WHEN ja.application_status = 'hired'       THEN 1 ELSE 0 END), 0) AS hired
    FROM jobs j
    LEFT JOIN job_applications ja ON ja.job_id = j.id
    WHERE j.company_id = ?
";

$stats_stmt = $conn->prepare($stats_sql);

$stats_stmt->bind_param("iii", $company_id, $company_id, $company_id);
$stats_stmt->execute();

$dash_stats = $stats_stmt->get_result()->fetch_assoc();

$stats_stmt->close();

$recent_sql = "
    SELECT
        ja.id AS application_id,
        ja.application_status,
        ja.applied_at,
        TIMESTAMPDIFF(SECOND, ja.applied_at, NOW()) AS age_seconds,
        j.id AS job_id,
        j.title AS job_title,
        u.id AS designer_user_id,
        u.full_name,
        u.profile_picture,
        d.headline
    FROM job_applications ja
    JOIN jobs j      ON j.id = ja.job_id
    JOIN designers d ON d.id = ja.designer_id
    JOIN users u     ON u.id = d.user_id
    WHERE j.company_id = ?
    ORDER BY ja.applied_at DESC, ja.id DESC
    LIMIT 5
";

$recent_stmt = $conn->prepare($recent_sql);

$recent_stmt->bind_param("i", $company_id);
$recent_stmt->execute();

$recent_result = $recent_stmt->get_result();

$recent_applications = [];

while ($row = $recent_result->fetch_assoc()) {
    $recent_applications[] = $row;
}

$recent_stmt->close();

$jobs_sql = "
    SELECT
        j.id,
        j.title,
        j.deadline,
        COUNT(ja.id) AS applicant_count
    FROM jobs j
    LEFT JOIN job_applications ja ON ja.job_id = j.id
    WHERE j.company_id = ? AND j.status = 'open'
    GROUP BY j.id
    ORDER BY applicant_count DESC, j.created_at DESC
    LIMIT 5
";

$jobs_stmt = $conn->prepare($jobs_sql);

$jobs_stmt->bind_param("i", $company_id);
$jobs_stmt->execute();

$jobs_result = $jobs_stmt->get_result();

$dash_jobs = [];

while ($row = $jobs_result->fetch_assoc()) {
    $dash_jobs[] = $row;
}

$jobs_stmt->close();

$fresh_sql = "
    SELECT
        d.id,
        d.title,
        d.image,
        u.id AS designer_user_id,
        u.full_name AS designer_name,
        (SELECT COUNT(*) FROM design_likes l WHERE l.design_id = d.id) AS like_count
    FROM designs d
    JOIN users u ON u.id = d.user_id
    ORDER BY d.created_at DESC
    LIMIT 4
";

$fresh_result = $conn->query($fresh_sql);

$fresh_designs = [];

while ($row = $fresh_result->fetch_assoc()) {
    $fresh_designs[] = $row;
}

?>
