<?php

require_once __DIR__ . "/../designer/db_helpers.php";
require_once __DIR__ . "/../designer/comm_helpers.php";

function requireAdminLogin(): void
{
    requireRoleLogin("admin");
}

function adminTakeFlash(): ?array
{
    $flash = $_SESSION["admin_flash"] ?? null;

    unset($_SESSION["admin_flash"]);

    return $flash;
}

function adminSort(): string
{
    $sort = $_GET["sort"] ?? "new";

    return $sort === "old" ? "old" : "new";
}

function adminSortDir(string $sort): string
{
    return $sort === "old" ? "ASC" : "DESC";
}

function adminSortControl(string $sort): string
{
    return '
        <form method="GET" class="ad-sort">
            <label for="adSort">Sort by</label>
            <select name="sort" id="adSort" class="co-select" onchange="this.form.submit();">
                <option value="new"' . ($sort === "new" ? " selected" : "") . '>Newest first</option>
                <option value="old"' . ($sort === "old" ? " selected" : "") . '>Oldest first</option>
            </select>
        </form>
    ';
}

function adminPages(): array
{
    return [
        "dashboard.php",
        "users.php",
        "designs.php",
        "comments.php",
        "jobs.php",
        "applications.php",
        "design_types.php",
        "announcements.php",
        "profile.php",
    ];
}

function adminBackUrl(string $page, string $sort): string
{
    if (!in_array($page, adminPages(), true)) {
        $page = "dashboard.php";
    }

    $url = "../../frontend/pages/admin/" . $page;

    if ($sort === "old") {
        $url .= "?sort=old";
    }

    return $url;
}

function adminActionForm(
    string $csrf,
    string $action,
    array $fields,
    string $label,
    string $class,
    string $back,
    string $sort = "new",
    string $confirm = "",
    bool $askReason = false
): string {

    $js = "";

    if ($askReason) {
        $js = "var r = prompt(" . json_encode($confirm) . "); "
            . "if (r === null || r.trim() === '') { return false; } "
            . "this.form.reason.value = r;";
    } elseif ($confirm !== "") {
        $js = "return confirm(" . json_encode($confirm) . ");";
    }

    $html  = '<form method="POST" action="../../../backend/admin/admin_action.php">';
    $html .= '<input type="hidden" name="csrf" value="' . htmlspecialchars($csrf) . '">';
    $html .= '<input type="hidden" name="action" value="' . htmlspecialchars($action) . '">';
    $html .= '<input type="hidden" name="back" value="' . htmlspecialchars($back) . '">';
    $html .= '<input type="hidden" name="sort" value="' . htmlspecialchars($sort) . '">';

    foreach ($fields as $name => $value) {
        $html .= '<input type="hidden" name="' . htmlspecialchars($name) . '" value="' . htmlspecialchars((string) $value) . '">';
    }

    if ($askReason) {
        $html .= '<input type="hidden" name="reason" value="">';
    }

    $html .= '<button type="submit" class="comm-btn ' . htmlspecialchars($class) . ' small"';

    if ($js !== "") {
        $html .= ' onclick="' . htmlspecialchars($js, ENT_QUOTES) . '"';
    }

    $html .= '>' . htmlspecialchars($label) . '</button>';
    $html .= '</form>';

    return $html;
}

function adminFetchAll(mysqli_result $result): array
{
    $rows = [];

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    return $rows;
}

?>
