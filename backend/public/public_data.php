<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";

const PUB_ROOT = "../../";

function pubEsc($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function pubRole(): string
{
    if (
        isset($_SESSION["logged_in"]) &&
        $_SESSION["logged_in"] === true &&
        isset($_SESSION["user_role"]) &&
        in_array($_SESSION["user_role"], ["designer", "company", "admin"], true)
    ) {
        return $_SESSION["user_role"];
    }

    return "";
}

function pubDashboardUrl(string $role): string
{
    return "../pages/" . $role . "/dashboard.php";
}

function pubFeedUrl(string $role): string
{
    if ($role === "designer" || $role === "company") {
        return "../pages/" . $role . "/design_feed.php";
    }

    return "../pages/admin/designs.php";
}

function pubApplyUrl(string $role): string
{
    return $role === "designer" ? "../pages/designer/jobs.php" : "../auth/login.php";
}

function pubImage(?string $path): string
{
    return $path ? PUB_ROOT . ltrim($path, "/") : "";
}

function pubInitial(string $name): string
{
    $name = trim($name);

    return $name === "" ? "?" : mb_strtoupper(mb_substr($name, 0, 1));
}

function pubExcerpt(?string $text, int $length = 180): string
{
    $text = trim(preg_replace("/\s+/", " ", (string) $text));

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, $length - 1)) . "…";
}

function pubLike(string $term): string
{
    return "%" . str_replace(["\\", "%", "_"], ["\\\\", "\\%", "\\_"], $term) . "%";
}

function pubText(string $key, int $max = 80): string
{
    return mb_substr(trim((string) ($_GET[$key] ?? "")), 0, $max);
}

function pubPage(): int
{
    return max(1, (int) ($_GET["page"] ?? 1));
}

function pubQuery(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);

    foreach ($params as $key => $value) {
        if ($value === "" || $value === null || ($key === "page" && (int) $value <= 1)) {
            unset($params[$key]);
        }
    }

    return $params ? "?" . http_build_query($params) : "?";
}

function pubPager(int $total, int $per_page, int $page): string
{
    $pages = (int) ceil($total / $per_page);

    if ($pages <= 1) {
        return "";
    }

    $html = '<nav class="pub-pager" aria-label="Pages">';

    if ($page > 1) {
        $html .= '<a href="' . pubEsc(pubQuery(["page" => $page - 1])) . '">Previous</a>';
    }

    for ($i = 1; $i <= $pages; $i++) {

        if ($i === $page) {
            $html .= '<span class="current">' . $i . "</span>";
        } else {
            $html .= '<a href="' . pubEsc(pubQuery(["page" => $i])) . '">' . $i . "</a>";
        }
    }

    if ($page < $pages) {
        $html .= '<a href="' . pubEsc(pubQuery(["page" => $page + 1])) . '">Next</a>';
    }

    return $html . "</nav>";
}

function pubFetchAll(mysqli $conn, string $sql, string $types = "", array $params = []): array
{
    $stmt = $conn->prepare($sql);

    if ($types !== "") {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    return $rows;
}

function pubFetchOne(mysqli $conn, string $sql, string $types = "", array $params = []): ?array
{
    $rows = pubFetchAll($conn, $sql, $types, $params);

    return $rows[0] ?? null;
}

function pubDesignTypes(mysqli $conn): array
{
    return pubFetchAll($conn, "SELECT id, name FROM design_types ORDER BY name ASC");
}

function pubJobSelect(): string
{
    return "
        SELECT
            j.id,
            j.title,
            j.description,
            j.location,
            j.salary,
            j.employment_type,
            j.deadline,
            j.created_at,
            c.id AS company_id,
            c.company_name,
            u.profile_picture AS company_logo
        FROM jobs j
        JOIN companies c ON c.id = j.company_id
        JOIN users u ON u.id = c.user_id
        WHERE j.status = 'open'
          AND (j.deadline IS NULL OR j.deadline >= CURDATE())
    ";
}

function pubJobs(mysqli $conn, string $q, string $type, string $location, int $page, int $per_page, ?int $company_id = null): array
{
    $where  = "";
    $types  = "";
    $params = [];

    if ($q !== "") {
        $where   .= " AND (j.title LIKE ? OR j.description LIKE ? OR j.location LIKE ? OR c.company_name LIKE ?)";
        $types   .= "ssss";
        $like     = pubLike($q);
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if (in_array($type, ["Full-Time", "Part-Time", "Internship", "Contract", "Freelance"], true)) {
        $where   .= " AND j.employment_type = ?";
        $types   .= "s";
        $params[] = $type;
    }

    if ($location !== "") {
        $where   .= " AND j.location LIKE ?";
        $types   .= "s";
        $params[] = pubLike($location);
    }

    if ($company_id !== null) {
        $where   .= " AND c.id = ?";
        $types   .= "i";
        $params[] = $company_id;
    }

    $count = pubFetchOne(
        $conn,
        "SELECT COUNT(*) AS total FROM (" . pubJobSelect() . $where . ") counted",
        $types,
        $params
    );

    $offset = ($page - 1) * $per_page;

    $rows = pubFetchAll(
        $conn,
        pubJobSelect() . $where . " ORDER BY j.created_at DESC LIMIT " . (int) $per_page . " OFFSET " . (int) $offset,
        $types,
        $params
    );

    return ["rows" => $rows, "total" => (int) ($count["total"] ?? 0)];
}

function pubDesignSelect(): string
{
    return "
        SELECT
            d.id,
            d.title,
            d.description,
            d.image,
            d.created_at,
            u.id AS user_id,
            u.full_name AS designer_name,
            u.profile_picture AS designer_picture,
            dg.id AS designer_id,
            (SELECT COUNT(*) FROM design_likes dl WHERE dl.design_id = d.id) AS like_count,
            (SELECT COUNT(*) FROM design_comments dc WHERE dc.design_id = d.id) AS comment_count,
            (
                SELECT GROUP_CONCAT(dt.name ORDER BY dt.name SEPARATOR ', ')
                FROM design_type_mapping m
                JOIN design_types dt ON dt.id = m.design_type_id
                WHERE m.design_id = d.id
            ) AS type_names
        FROM designs d
        JOIN users u ON u.id = d.user_id
        LEFT JOIN designers dg ON dg.user_id = u.id
        WHERE 1 = 1
    ";
}

function pubDesigns(mysqli $conn, string $q, int $type_id, int $page, int $per_page, ?int $user_id = null): array
{
    $where  = "";
    $types  = "";
    $params = [];

    if ($q !== "") {
        $where .= "
            AND (
                d.title LIKE ?
                OR d.description LIKE ?
                OR u.full_name LIKE ?
                OR EXISTS (
                    SELECT 1
                    FROM design_type_mapping m2
                    JOIN design_types dt2 ON dt2.id = m2.design_type_id
                    WHERE m2.design_id = d.id AND dt2.name LIKE ?
                )
            )
        ";
        $types   .= "ssss";
        $like     = pubLike($q);
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if ($type_id > 0) {
        $where   .= " AND EXISTS (SELECT 1 FROM design_type_mapping m3 WHERE m3.design_id = d.id AND m3.design_type_id = ?)";
        $types   .= "i";
        $params[] = $type_id;
    }

    if ($user_id !== null) {
        $where   .= " AND d.user_id = ?";
        $types   .= "i";
        $params[] = $user_id;
    }

    $count = pubFetchOne(
        $conn,
        "SELECT COUNT(*) AS total FROM (" . pubDesignSelect() . $where . ") counted",
        $types,
        $params
    );

    $offset = ($page - 1) * $per_page;

    $rows = pubFetchAll(
        $conn,
        pubDesignSelect() . $where . " ORDER BY d.created_at DESC, d.id DESC LIMIT " . (int) $per_page . " OFFSET " . (int) $offset,
        $types,
        $params
    );

    return ["rows" => $rows, "total" => (int) ($count["total"] ?? 0)];
}

function pubCompanySelect(): string
{
    return "
        SELECT
            c.id,
            c.company_name,
            c.industry,
            c.website,
            c.company_size,
            c.location,
            c.description,
            u.profile_picture AS logo,
            u.cover_picture AS cover,
            (
                SELECT COUNT(*)
                FROM jobs j
                WHERE j.company_id = c.id
                  AND j.status = 'open'
                  AND (j.deadline IS NULL OR j.deadline >= CURDATE())
            ) AS open_jobs
        FROM companies c
        JOIN users u ON u.id = c.user_id
        WHERE 1 = 1
    ";
}

function pubCompanies(mysqli $conn, string $q, string $industry, int $page, int $per_page): array
{
    $where  = "";
    $types  = "";
    $params = [];

    if ($q !== "") {
        $where   .= " AND (c.company_name LIKE ? OR c.industry LIKE ? OR c.location LIKE ? OR c.description LIKE ?)";
        $types   .= "ssss";
        $like     = pubLike($q);
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    if ($industry !== "") {
        $where   .= " AND c.industry = ?";
        $types   .= "s";
        $params[] = $industry;
    }

    $count = pubFetchOne(
        $conn,
        "SELECT COUNT(*) AS total FROM (" . pubCompanySelect() . $where . ") counted",
        $types,
        $params
    );

    $offset = ($page - 1) * $per_page;

    $rows = pubFetchAll(
        $conn,
        pubCompanySelect() . $where . " ORDER BY c.company_name ASC LIMIT " . (int) $per_page . " OFFSET " . (int) $offset,
        $types,
        $params
    );

    return ["rows" => $rows, "total" => (int) ($count["total"] ?? 0)];
}

function pubCompany(mysqli $conn, int $id): ?array
{
    return pubFetchOne($conn, pubCompanySelect() . " AND c.id = ? LIMIT 1", "i", [$id]);
}

function pubIndustries(mysqli $conn): array
{
    $rows = pubFetchAll(
        $conn,
        "SELECT DISTINCT industry FROM companies WHERE industry IS NOT NULL AND industry <> '' ORDER BY industry ASC"
    );

    return array_column($rows, "industry");
}

function pubDesignerSelect(): string
{
    return "
        SELECT
            dg.id,
            dg.headline,
            dg.bio,
            dg.location,
            dg.website,
            dg.portfolio_link,
            dg.experience_years,
            u.id AS user_id,
            u.full_name,
            u.profile_picture,
            u.cover_picture,
            (SELECT COUNT(*) FROM designs d WHERE d.user_id = u.id) AS design_count,
            (
                SELECT GROUP_CONCAT(s.specialization_name ORDER BY s.id SEPARATOR '|')
                FROM designer_specializations s
                WHERE s.designer_id = dg.id
            ) AS specializations
        FROM designers dg
        JOIN users u ON u.id = dg.user_id
        WHERE u.is_verified = 'Y'
    ";
}

function pubDesigners(mysqli $conn, string $q, int $page, int $per_page): array
{
    $where  = "";
    $types  = "";
    $params = [];

    if ($q !== "") {
        $where .= "
            AND (
                u.full_name LIKE ?
                OR dg.headline LIKE ?
                OR dg.bio LIKE ?
                OR dg.location LIKE ?
                OR EXISTS (
                    SELECT 1 FROM designer_specializations s2
                    WHERE s2.designer_id = dg.id AND s2.specialization_name LIKE ?
                )
            )
        ";
        $types   .= "sssss";
        $like     = pubLike($q);

        for ($i = 0; $i < 5; $i++) {
            $params[] = $like;
        }
    }

    $count = pubFetchOne(
        $conn,
        "SELECT COUNT(*) AS total FROM (" . pubDesignerSelect() . $where . ") counted",
        $types,
        $params
    );

    $offset = ($page - 1) * $per_page;

    $rows = pubFetchAll(
        $conn,
        pubDesignerSelect() . $where . " ORDER BY design_count DESC, u.full_name ASC LIMIT " . (int) $per_page . " OFFSET " . (int) $offset,
        $types,
        $params
    );

    return ["rows" => $rows, "total" => (int) ($count["total"] ?? 0)];
}

function pubDesigner(mysqli $conn, int $id): ?array
{
    return pubFetchOne($conn, pubDesignerSelect() . " AND dg.id = ? LIMIT 1", "i", [$id]);
}

function pubSpecializations(?string $joined): array
{
    return $joined ? array_filter(explode("|", $joined)) : [];
}

function pubWebUrl(?string $url): string
{
    $url = trim((string) $url);

    if ($url === "") {
        return "";
    }

    return preg_match("#^https?://#i", $url) ? $url : "https://" . $url;
}

function pubDate(?string $date): string
{
    return $date ? date("M j, Y", strtotime($date)) : "";
}

function pubAgo(?string $date): string
{
    if (!$date) {
        return "";
    }

    $seconds = max(0, time() - strtotime($date));

    if ($seconds < 3600) {
        return max(1, (int) floor($seconds / 60)) . " min ago";
    }

    if ($seconds < 86400) {
        return (int) floor($seconds / 3600) . " h ago";
    }

    if ($seconds < 2592000) {
        return (int) floor($seconds / 86400) . " d ago";
    }

    return pubDate($date);
}

?>
