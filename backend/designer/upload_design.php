<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLogin();

$user_id = (int) $_SESSION["user_id"];

$all_design_types = getAllDesignTypes($conn);

$upload_error   = "";
$upload_success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title          = trim($_POST["title"] ?? "");
    $description    = trim($_POST["description"] ?? "");
    $selected_types = $_POST["types"] ?? [];
    $new_type_name  = trim($_POST["new_type"] ?? "");

    if ($title === "") {

        $upload_error = "Title is required.";

    } elseif (empty($_FILES["image"]["name"])) {

        $upload_error = "Please choose an image to upload.";

    } else {

        $allowed_ext = ["jpg", "jpeg", "png", "webp"];

        $ext = strtolower(
            pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION)
        );

        if (!in_array($ext, $allowed_ext)) {

            $upload_error = "Only JPG, PNG or WEBP images are allowed.";

        } else {

            $upload_dir = __DIR__ . "/../../uploads/designs/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $filename    = "design_" . $user_id . "_" . time() . "." . $ext;
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {

                $image_path = "uploads/designs/" . $filename;

                $conn->begin_transaction();

                try {

                    $insert_sql = "
                        INSERT INTO designs
                            (user_id, title, description, image)
                        VALUES
                            (?, ?, ?, ?)
                    ";

                    $insert_stmt = $conn->prepare($insert_sql);

                    $insert_stmt->bind_param(
                        "isss",
                        $user_id,
                        $title,
                        $description,
                        $image_path
                    );

                    $insert_stmt->execute();

                    $design_id = (int) $conn->insert_id;

                    $insert_stmt->close();

                    if (!empty($selected_types)) {

                        $map_sql = "
                            INSERT IGNORE INTO design_type_mapping
                                (design_id, design_type_id)
                            VALUES
                                (?, ?)
                        ";

                        $map_stmt = $conn->prepare($map_sql);

                        foreach ($selected_types as $type_id) {

                            $type_id = (int) $type_id;

                            $map_stmt->bind_param(
                                "ii",
                                $design_id,
                                $type_id
                            );

                            $map_stmt->execute();

                        }

                        $map_stmt->close();

                    }

                    if ($new_type_name !== "") {

                        $find_type_sql = "
                            SELECT id
                            FROM design_types
                            WHERE name = ?
                            LIMIT 1
                        ";

                        $find_type_stmt = $conn->prepare($find_type_sql);

                        $find_type_stmt->bind_param(
                            "s",
                            $new_type_name
                        );

                        $find_type_stmt->execute();

                        $find_type_result = $find_type_stmt->get_result();

                        $type_row = $find_type_result->fetch_assoc();

                        $find_type_stmt->close();

                        if ($type_row) {

                            $new_type_id = (int) $type_row["id"];

                        } else {

                            $create_type_sql = "
                                INSERT INTO design_types (name)
                                VALUES (?)
                            ";

                            $create_type_stmt = $conn->prepare($create_type_sql);

                            $create_type_stmt->bind_param(
                                "s",
                                $new_type_name
                            );

                            $create_type_stmt->execute();

                            $new_type_id = (int) $conn->insert_id;

                            $create_type_stmt->close();

                        }

                        $map_new_sql = "
                            INSERT IGNORE INTO design_type_mapping
                                (design_id, design_type_id)
                            VALUES
                                (?, ?)
                        ";

                        $map_new_stmt = $conn->prepare($map_new_sql);

                        $map_new_stmt->bind_param(
                            "ii",
                            $design_id,
                            $new_type_id
                        );

                        $map_new_stmt->execute();

                        $map_new_stmt->close();

                    }

                    $conn->commit();

                    $upload_success   = true;
                    $all_design_types = getAllDesignTypes($conn);

                } catch (Exception $e) {

                    $conn->rollback();

                    $upload_error = "Something went wrong while saving the design.";

                }

            } else {

                $upload_error = "Failed to upload the image. Try again.";

            }
        }
    }
}

?>
