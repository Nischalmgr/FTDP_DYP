<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/admin_helpers.php";

requireAdminLogin();

$admin_flash = adminTakeFlash();

$stats_sql = "
    SELECT
        (SELECT COUNT(*) FROM users WHERE role = 'designer')       AS designers,
        (SELECT COUNT(*) FROM users WHERE role = 'company')        AS companies,
        (SELECT COUNT(*) FROM users WHERE is_verified = 'N')       AS unverified,
        (SELECT COUNT(*) FROM designs)                             AS designs,
        (SELECT COUNT(*) FROM design_comments)                     AS comments,
        (SELECT COUNT(*) FROM jobs WHERE status = 'open')          AS open_jobs,
        (SELECT COUNT(*) FROM jobs WHERE status = 'closed')        AS closed_jobs,
        (SELECT COUNT(*) FROM job_applications)                    AS applications
";

$dash_stats = $conn->query($stats_sql)->fetch_assoc();

$recent_users = adminFetchAll($conn->query("
    SELECT
        id,
        full_name,
        email,
        role,
        is_verified,
        profile_picture,
        TIMESTAMPDIFF(SECOND, created_at, NOW()) AS age_seconds,
        created_at
    FROM users
    WHERE role <> 'admin'
    ORDER BY created_at DESC, id DESC
    LIMIT 5
"));

$recent_jobs = adminFetchAll($conn->query("
    SELECT
        j.id,
        j.title,
        j.status,
        c.company_name
    FROM jobs j
    JOIN companies c ON c.id = j.company_id
    ORDER BY j.created_at DESC, j.id DESC
    LIMIT 5
"));

$recent_designs = adminFetchAll($conn->query("
    SELECT
        d.id,
        d.title,
        d.image,
        u.full_name AS designer_name,
        (SELECT COUNT(*) FROM design_likes l WHERE l.design_id = d.id) AS like_count
    FROM designs d
    JOIN users u ON u.id = d.user_id
    ORDER BY d.created_at DESC, d.id DESC
    LIMIT 4
"));

?>
