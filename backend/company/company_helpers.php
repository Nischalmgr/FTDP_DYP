<?php

require_once __DIR__ . "/../designer/db_helpers.php";
require_once __DIR__ . "/../designer/comm_helpers.php";

function requireCompanyLogin(): void
{
    requireRoleLogin("company");
}

function requireCompanyLoginJson(): void
{
    requireRoleLoginJson(["company"]);
}

function getCompanyId(mysqli $conn, int $user_id): int
{
    $stmt = $conn->prepare("SELECT id FROM companies WHERE user_id = ? LIMIT 1");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if ($row) {
        return (int) $row["id"];
    }

    $create = $conn->prepare("
        INSERT INTO companies (user_id, company_name)
        SELECT id, full_name FROM users WHERE id = ?
    ");

    $create->bind_param("i", $user_id);
    $create->execute();

    $new_id = (int) $conn->insert_id;

    $create->close();

    return $new_id;
}

function companyApplicationStatuses(): array
{
    return ["pending", "reviewed", "shortlisted", "rejected", "hired"];
}

function getDesignerPortfolioStats(mysqli $conn, int $designer_user_id): array
{
    $sql = "
        SELECT
            (SELECT COUNT(*) FROM designs WHERE user_id = ?) AS designs,
            (
                SELECT COUNT(*)
                FROM design_likes l
                JOIN designs d ON d.id = l.design_id
                WHERE d.user_id = ?
            ) AS likes,
            (
                SELECT COUNT(*)
                FROM saved_designs s
                JOIN designs d ON d.id = s.design_id
                WHERE d.user_id = ?
            ) AS saves
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("iii", $designer_user_id, $designer_user_id, $designer_user_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    return [
        "designs" => (int) $row["designs"],
        "likes"   => (int) $row["likes"],
        "saves"   => (int) $row["saves"],
    ];
}

?>
