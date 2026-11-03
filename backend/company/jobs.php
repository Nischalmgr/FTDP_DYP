<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/company_helpers.php";

requireCompanyLogin();

$user_id    = (int) $_SESSION["user_id"];
$company_id = getCompanyId($conn, $user_id);

$jobs_flash = $_SESSION["jobs_flash"] ?? null;
unset($_SESSION["jobs_flash"]);

$job_filter = $_GET["status"] ?? "all";

if (!in_array($job_filter, ["all", "open", "closed"], true)) {
    $job_filter = "all";
}

$count_stmt = $conn->prepare("
    SELECT
        COUNT(*) AS total,
        COALESCE(SUM(CASE WHEN status = 'open'   THEN 1 ELSE 0 END), 0) AS open_count,
        COALESCE(SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END), 0) AS closed_count
    FROM jobs
    WHERE company_id = ?
");

$count_stmt->bind_param("i", $company_id);
$count_stmt->execute();

$job_counts = $count_stmt->get_result()->fetch_assoc();

$count_stmt->close();

$sql = "
    SELECT
        j.id,
        j.title,
        j.location,
        j.salary,
        j.employment_type,
        j.deadline,
        j.status,
        j.created_at,
        COUNT(ja.id) AS applicant_count,
        COALESCE(SUM(CASE WHEN ja.application_status = 'pending' THEN 1 ELSE 0 END), 0) AS pending_count
    FROM jobs j
    LEFT JOIN job_applications ja ON ja.job_id = j.id
    WHERE j.company_id = ?
";

if ($job_filter !== "all") {
    $sql .= " AND j.status = ?";
}

$sql .= "
    GROUP BY j.id
    ORDER BY j.created_at DESC
";

$stmt = $conn->prepare($sql);

if ($job_filter !== "all") {
    $stmt->bind_param("is", $company_id, $job_filter);
} else {
    $stmt->bind_param("i", $company_id);
}

$stmt->execute();

$result = $stmt->get_result();

$company_jobs_data = [];

while ($row = $result->fetch_assoc()) {
    $company_jobs_data[] = $row;
}

$stmt->close();

?>
