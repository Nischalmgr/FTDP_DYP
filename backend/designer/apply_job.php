<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";
require_once __DIR__ . "/comm_helpers.php";

requireDesignerLogin();

$user_id     = (int) $_SESSION["user_id"];
$designer_id = getDesignerId($conn, $user_id);

$job_id       = isset($_POST["job_id"]) ? (int) $_POST["job_id"] : 0;
$cover_letter = trim($_POST["cover_letter"] ?? "");
$resume_path  = null;

$job_open = false;

if ($job_id > 0 && $designer_id) {

    $job_stmt = $conn->prepare("
        SELECT id
        FROM jobs
        WHERE id = ?
          AND status = 'open'
          AND (deadline IS NULL OR deadline >= CURDATE())
        LIMIT 1
    ");

    $job_stmt->bind_param("i", $job_id);
    $job_stmt->execute();

    $job_open = $job_stmt->get_result()->num_rows > 0;

    $job_stmt->close();
}

if ($job_open) {

    $check_stmt = $conn->prepare("
        SELECT id
        FROM job_applications
        WHERE job_id = ? AND designer_id = ?
    ");

    $check_stmt->bind_param("ii", $job_id, $designer_id);
    $check_stmt->execute();

    $already_applied = $check_stmt->get_result()->fetch_assoc();

    $check_stmt->close();

    if (!$already_applied) {

        if (!empty($_FILES["resume"]["name"])) {

            $ext = strtolower(pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION));

            if (
                in_array($ext, ["pdf", "doc", "docx"], true) &&
                $_FILES["resume"]["error"] === UPLOAD_ERR_OK &&
                $_FILES["resume"]["size"] <= 5 * 1048576
            ) {

                $dir = __DIR__ . "/../../uploads/resumes/";

                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }

                $filename = "resume_" . $user_id . "_" . time() . "." . $ext;

                if (move_uploaded_file($_FILES["resume"]["tmp_name"], $dir . $filename)) {
                    $resume_path = "uploads/resumes/" . $filename;
                }
            }
        }

        $insert_stmt = $conn->prepare("
            INSERT INTO job_applications (job_id, designer_id, cover_letter, resume)
            VALUES (?, ?, ?, ?)
        ");

        $insert_stmt->bind_param("iiss", $job_id, $designer_id, $cover_letter, $resume_path);
        $insert_stmt->execute();

        $application_id = (int) $conn->insert_id;

        $insert_stmt->close();

        notifyNewApplication($conn, $application_id);
    }
}

header("Location: ../../frontend/pages/designer/jobs.php");
exit;

?>
