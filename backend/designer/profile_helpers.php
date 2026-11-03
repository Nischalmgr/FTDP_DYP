<?php

function normalizeUrl(string $url): string
{
    $url = trim($url);

    if ($url === "") {
        return "";
    }

    if (!preg_match("#^https?://#i", $url)) {
        $url = "https://" . $url;
    }

    return $url;
}

function isValidWebUrl(string $url): bool
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

    return in_array($scheme, ["http", "https"], true);
}

function saveProfileImage(
    array $file,
    string $folder,
    string $prefix,
    int $user_id,
    int $max_bytes,
    ?string &$error
): ?string {

    if (($file["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file["error"] === UPLOAD_ERR_INI_SIZE || $file["error"] === UPLOAD_ERR_FORM_SIZE) {
        $error = "That image is too large. Maximum size is " . round($max_bytes / 1048576) . " MB.";
        return null;
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        $error = "The image could not be uploaded. Please try again.";
        return null;
    }

    if ($file["size"] > $max_bytes) {
        $error = "That image is too large. Maximum size is " . round($max_bytes / 1048576) . " MB.";
        return null;
    }

    $info = @getimagesize($file["tmp_name"]);

    $allowed = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp",
    ];

    if (!$info || !isset($allowed[$info["mime"]])) {
        $error = "Only JPG, PNG or WEBP images are allowed.";
        return null;
    }

    $dir = __DIR__ . "/../../uploads/" . $folder . "/";

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $filename = $prefix . "_" . $user_id . "_" . bin2hex(random_bytes(6)) . "." . $allowed[$info["mime"]];

    if (!move_uploaded_file($file["tmp_name"], $dir . $filename)) {
        $error = "The image could not be saved on the server.";
        return null;
    }

    return "uploads/" . $folder . "/" . $filename;
}

function deleteProfileUpload(?string $path): void
{
    if (!$path || strpos($path, "..") !== false) {
        return;
    }

    if (
        strpos($path, "uploads/profiles/") !== 0 &&
        strpos($path, "uploads/covers/") !== 0
    ) {
        return;
    }

    $full = __DIR__ . "/../../" . $path;

    if (is_file($full)) {
        @unlink($full);
    }
}

?>
