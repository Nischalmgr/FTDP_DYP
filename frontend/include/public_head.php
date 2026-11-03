<?php

require_once __DIR__ . "/../../backend/public/public_data.php";
require_once __DIR__ . "/public_cards.php";

$pub_title = $pub_title ?? "FTDP";
$pub_nav   = $pub_nav ?? "";

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= pubEsc($pub_title); ?> | FTDP</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/public.css">

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

</head>

<body>

<?php include __DIR__ . "/header.php"; ?>
