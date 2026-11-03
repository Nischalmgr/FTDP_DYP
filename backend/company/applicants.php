<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/company_helpers.php";

requireCompanyLogin();

$user_id    = (int) $_SESSION["user_id"];
$company_id = getCompanyId($conn, $user_id);

$statuses      = companyApplicationStatuses();
$filter_job    = isset($_GET["job"]) ? (int) $_GET["job"] : 0;
$filter_status = $_GET["status"] ?? "all";

if ($filter_status !== "all" && !in_array($filter_status, $statuses, true)) {
    $filter_status = "all";
}

$per_page = 15;
$page     = max(1, (int) ($_GET["page"] ?? 1));

$jobs_stmt = $conn->prepare("
    SELECT id, title, status
    FROM jobs
    WHERE company_id = ?
    ORDER BY created_at DESC
");

$jobs_stmt->bind_param("i", $company_id);
$jobs_stmt->execute();

$jobs_result = $jobs_stmt->get_result();

$company_jobs = [];

while ($row = $jobs_result->fetch_assoc()) {
    $company_jobs[] = $row;
}

$jobs_stmt->close();

$owned_job_ids = array_column($company_jobs, "id");

if ($filter_job > 0 && !in_array($filter_job, array_map("intval", $owned_job_ids), true)) {
    $filter_job = 0;
}

$counts_sql = "
    SELECT ja.application_status AS status, COUNT(*) AS c
    FROM job_applications ja
    JOIN jobs j ON j.id = ja.job_id
    WHERE j.company_id = ?
";

if ($filter_job > 0) {
    $counts_sql .= " AND j.id = ?";
}

$counts_sql .= " GROUP BY ja.application_status";

$counts_stmt = $conn->prepare($counts_sql);

if ($filter_job > 0) {
    $counts_stmt->bind_param("ii", $company_id, $filter_job);
} else {
    $counts_stmt->bind_param("i", $company_id);
}

$counts_stmt->execute();

$counts_result = $counts_stmt->get_result();

$status_counts = array_fill_keys($statuses, 0);
$status_counts["all"] = 0;

while ($row = $counts_result->fetch_assoc()) {
    $status_counts[$row["status"]] = (int) $row["c"];
    $status_counts["all"]         += (int) $row["c"];
}

$counts_stmt->close();

$total_filtered = $status_counts[$filter_status];
$total_pages    = max(1, (int) ceil($total_filtered / $per_page));

if ($page > $total_pages) {
    $page = $total_pages;
}

$offset = ($page - 1) * $per_page;

$sql = "
    SELECT
        ja.id AS application_id,
        ja.application_status,
        ja.applied_at,
        ja.cover_letter,
        ja.resume,
        j.id AS job_id,
        j.title AS job_title,
        u.id AS designer_user_id,
        u.full_name,
        u.profile_picture,
        d.headline,
        d.location,
        d.experience_years,
        (SELECT COUNT(*) FROM designs x WHERE x.user_id = u.id) AS design_count,
        (
            SELECT COUNT(*)
            FROM design_likes l
            JOIN designs y ON y.id = l.design_id
            WHERE y.user_id = u.id
        ) AS like_count
    FROM job_applications ja
    JOIN jobs j      ON j.id = ja.job_id
    JOIN designers d ON d.id = ja.designer_id
    JOIN users u     ON u.id = d.user_id
    WHERE j.company_id = ?
";

$types  = "i";
$params = [$company_id];

if ($filter_job > 0) {
    $sql     .= " AND j.id = ?";
    $types   .= "i";
    $params[] = $filter_job;
}

if ($filter_status !== "all") {
    $sql     .= " AND ja.application_status = ?";
    $types   .= "s";
    $params[] = $filter_status;
}

$sql .= "
    ORDER BY ja.applied_at DESC, ja.id DESC
    LIMIT ? OFFSET ?
";

$types   .= "ii";
$params[] = $per_page;
$params[] = $offset;

$stmt = $conn->prepare($sql);

$stmt->bind_param($types, ...$params);
$stmt->execute();

$result = $stmt->get_result();

$applicants_data = [];

while ($row = $result->fetch_assoc()) {
    $applicants_data[] = $row;
}

$stmt->close();

?>
