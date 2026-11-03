<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/db_helpers.php";

requireDesignerLogin();

$user_id   = (int) $_SESSION["user_id"];
$design_id = isset($_POST["design_id"]) ? (int) $_POST["design_id"] : 0;

if ($design_id > 0) {

    $delete_sql = "
        DELETE FROM designs
        WHERE id = ? AND user_id = ?
    ";

    $delete_stmt = $conn->prepare($delete_sql);

    $delete_stmt->bind_param(
        "ii",
        $design_id,
        $user_id
    );

    $delete_stmt->execute();

    $delete_stmt->close();

}

header("Location: ../../frontend/pages/designer/my_designs.php");
exit;

?>
