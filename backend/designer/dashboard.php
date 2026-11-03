<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true
) {

    header("Location: ../../frontend/auth/login.php");
    exit;

}

if (
    !isset($_SESSION["user_role"]) ||
    $_SESSION["user_role"] !== "designer"
) {

    header("Location: ../../frontend/auth/login.php");
    exit;

}

$user_id = (int) $_SESSION["user_id"];

$user_name = $_SESSION["user_name"] ?? "Designer";

$designer_sql = "
    SELECT id
    FROM designers
    WHERE user_id = ?
    LIMIT 1
";

$designer_stmt = $conn->prepare($designer_sql);

$designer_stmt->bind_param(
    "i",
    $user_id
);

$designer_stmt->execute();

$designer_result = $designer_stmt->get_result();

$designer = $designer_result->fetch_assoc();

$designer_stmt->close();

if (!$designer) {

    die("Designer profile not found.");

}

$designer_id = (int) $designer["id"];

$design_sql = "
    SELECT COUNT(*) AS total_designs
    FROM designs
    WHERE user_id = ?
";

$design_stmt = $conn->prepare($design_sql);

$design_stmt->bind_param(
    "i",
    $user_id
);

$design_stmt->execute();

$design_result = $design_stmt->get_result();

$design_data = $design_result->fetch_assoc();

$total_designs = (int) $design_data["total_designs"];

$design_stmt->close();

$likes_sql = "
    SELECT COUNT(*) AS total_likes

    FROM design_likes dl

    INNER JOIN designs d
        ON dl.design_id = d.id

    WHERE d.user_id = ?
";

$likes_stmt = $conn->prepare($likes_sql);

$likes_stmt->bind_param(
    "i",
    $user_id
);

$likes_stmt->execute();

$likes_result = $likes_stmt->get_result();

$likes_data = $likes_result->fetch_assoc();

$total_likes = (int) $likes_data["total_likes"];

$likes_stmt->close();

$jobs_count_sql = "
    SELECT COUNT(*) AS total_jobs
    FROM job_applications
    WHERE designer_id = ?
";

$jobs_count_stmt = $conn->prepare(
    $jobs_count_sql
);

$jobs_count_stmt->bind_param(
    "i",
    $designer_id
);

$jobs_count_stmt->execute();

$jobs_count_result =
    $jobs_count_stmt->get_result();

$jobs_count_data =
    $jobs_count_result->fetch_assoc();

$total_jobs =
    (int) $jobs_count_data["total_jobs"];

$jobs_count_stmt->close();

$jobs_sql = "
    SELECT

        j.id,

        j.title,

        j.location,

        j.salary,

        j.employment_type,

        j.deadline,

        ja.application_status,

        ja.applied_at,

        c.company_name

    FROM job_applications ja

    INNER JOIN jobs j
        ON ja.job_id = j.id

    INNER JOIN companies c
        ON j.company_id = c.id

    WHERE ja.designer_id = ?

    ORDER BY ja.applied_at DESC

    LIMIT 10
";

$jobs_stmt = $conn->prepare(
    $jobs_sql
);

$jobs_stmt->bind_param(
    "i",
    $designer_id
);

$jobs_stmt->execute();

$jobs_result = $jobs_stmt->get_result();

$my_jobs = [];

while ($job = $jobs_result->fetch_assoc()) {

    $my_jobs[] = $job;

}

$jobs_stmt->close();

$dashboard_data = [

    "user_name" => $user_name,

    "total_designs" => $total_designs,

    "total_likes" => $total_likes,

    "total_jobs" => $total_jobs,

    "my_jobs" => $my_jobs

];

?>
