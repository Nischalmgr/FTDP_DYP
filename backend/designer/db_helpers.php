<?php

function getDesignerId(mysqli $conn, int $user_id): ?int
{
    $sql = "
        SELECT id
        FROM designers
        WHERE user_id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $user_id);

    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    $stmt->close();

    return $row ? (int) $row["id"] : null;
}

function getAllDesignTypes(mysqli $conn): array
{
    $sql = "
        SELECT id, name
        FROM design_types
        ORDER BY name ASC
    ";

    $result = $conn->query($sql);

    $types = [];

    while ($row = $result->fetch_assoc()) {
        $types[] = $row;
    }

    return $types;
}

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}

function loginPageUrl(): string
{
    $script = str_replace("\\", "/", $_SERVER["SCRIPT_NAME"] ?? "");

    foreach (["/frontend/", "/backend/"] as $marker) {

        $pos = strpos($script, $marker);

        if ($pos !== false) {
            return substr($script, 0, $pos) . "/frontend/auth/login.php";
        }
    }

    return "/frontend/auth/login.php";
}

function requireRoles(array $roles): void
{
    if (
        !isset($_SESSION["logged_in"]) ||
        $_SESSION["logged_in"] !== true ||
        !isset($_SESSION["user_role"]) ||
        !in_array($_SESSION["user_role"], $roles, true)
    ) {
        header("Location: " . loginPageUrl());
        exit;
    }
}

function requireRolesJson(array $roles): void
{
    if (
        !isset($_SESSION["logged_in"]) ||
        $_SESSION["logged_in"] !== true ||
        !isset($_SESSION["user_role"]) ||
        !in_array($_SESSION["user_role"], $roles, true)
    ) {
        jsonResponse(["success" => false, "message" => "Not logged in"], 401);
    }
}

function requireRoleLogin(string $role): void
{
    requireRoles([$role]);
}

function requireRoleLoginJson(array $roles): void
{
    requireRolesJson($roles);
}

function requireDesignerLogin(): void
{
    requireRoles(["designer"]);
}

function requireDesignerLoginJson(): void
{
    requireRolesJson(["designer"]);
}

?>
