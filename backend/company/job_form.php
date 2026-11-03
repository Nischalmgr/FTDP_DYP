<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/company_helpers.php";

requireCompanyLogin();

$user_id    = (int) $_SESSION["user_id"];
$company_id = getCompanyId($conn, $user_id);

$employment_types = ["Full-Time", "Part-Time", "Internship", "Contract", "Freelance"];

$edit_id = isset($_GET["id"]) ? (int) $_GET["id"] : (int) ($_POST["id"] ?? 0);

$job = [
    "title"           => "",
    "description"     => "",
    "location"        => "",
    "salary"          => "",
    "employment_type" => "",
    "deadline"        => "",
];

if ($edit_id > 0) {

    $stmt = $conn->prepare("
        SELECT title, description, location, salary, employment_type, deadline
        FROM jobs
        WHERE id = ? AND company_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("ii", $edit_id, $company_id);
    $stmt->execute();

    $existing = $stmt->get_result()->fetch_assoc();

    $stmt->close();

    if (!$existing) {
        $_SESSION["jobs_flash"] = ["type" => "error", "message" => "That job wasn't found."];
        header("Location: jobs.php");
        exit;
    }

    foreach ($existing as $key => $value) {
        $job[$key] = $value ?? "";
    }
}

$is_edit     = $edit_id > 0;
$form_errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $job = [
        "title"           => trim($_POST["title"] ?? ""),
        "description"     => trim($_POST["description"] ?? ""),
        "location"        => trim($_POST["location"] ?? ""),
        "salary"          => trim($_POST["salary"] ?? ""),
        "employment_type" => trim($_POST["employment_type"] ?? ""),
        "deadline"        => trim($_POST["deadline"] ?? ""),
    ];

    if (!verifyCsrf($_POST["csrf"] ?? "")) {
        $form_errors[] = "Your session expired. Please try again.";
    }

    if (mb_strlen($job["title"]) < 3 || mb_strlen($job["title"]) > 200) {
        $form_errors[] = "Job title must be between 3 and 200 characters.";
    }

    if (mb_strlen($job["description"]) < 20) {
        $form_errors[] = "Please describe the role in at least 20 characters.";
    } elseif (mb_strlen($job["description"]) > 5000) {
        $form_errors[] = "Description can be up to 5000 characters.";
    }

    if (mb_strlen($job["location"]) > 150) {
        $form_errors[] = "Location can be up to 150 characters.";
    }

    if (mb_strlen($job["salary"]) > 100) {
        $form_errors[] = "Salary can be up to 100 characters.";
    }

    if ($job["employment_type"] !== "" && !in_array($job["employment_type"], $employment_types, true)) {
        $form_errors[] = "Please choose a valid employment type.";
    }

    if ($job["deadline"] !== "") {

        $d = DateTime::createFromFormat("Y-m-d", $job["deadline"]);

        if (!$d || $d->format("Y-m-d") !== $job["deadline"]) {
            $form_errors[] = "Please enter a valid deadline date.";
        }
    }

    if (count($form_errors) === 0) {

        $location_db = $job["location"]        !== "" ? $job["location"]        : null;
        $salary_db   = $job["salary"]          !== "" ? $job["salary"]          : null;
        $type_db     = $job["employment_type"] !== "" ? $job["employment_type"] : null;
        $deadline_db = $job["deadline"]        !== "" ? $job["deadline"]        : null;

        if ($is_edit) {

            $stmt = $conn->prepare("
                UPDATE jobs
                SET title = ?, description = ?, location = ?, salary = ?,
                    employment_type = ?, deadline = ?
                WHERE id = ? AND company_id = ?
            ");

            $stmt->bind_param(
                "ssssssii",
                $job["title"],
                $job["description"],
                $location_db,
                $salary_db,
                $type_db,
                $deadline_db,
                $edit_id,
                $company_id
            );

            $message = "Job updated.";

        } else {

            $stmt = $conn->prepare("
                INSERT INTO jobs
                    (company_id, title, description, location, salary, employment_type, deadline)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "issssss",
                $company_id,
                $job["title"],
                $job["description"],
                $location_db,
                $salary_db,
                $type_db,
                $deadline_db
            );

            $message = "Job posted. Designers can now see and apply to it.";
        }

        if ($stmt->execute()) {

            $stmt->close();

            $_SESSION["jobs_flash"] = ["type" => "success", "message" => $message];

            header("Location: jobs.php");
            exit;
        }

        $stmt->close();

        $form_errors[] = "Something went wrong while saving. Please try again.";
    }
}

?>
