<?php

$pub_title = "Cookies Policy";
$pub_nav   = "";

include "../include/public_head.php";

pubLegal(
    "LEGAL",
    "Cookies Policy",
    "This page explains how FTDP uses cookies.",
    [
        "What are cookies" => [
            "Cookies are small text files stored in your browser by the websites you visit.",
        ],
        "Cookies we use" => [
            "FTDP uses a session cookie to keep you signed in and to protect forms from misuse. It is needed for the platform to work and is removed when your session ends or you sign out.",
            "Fonts and icons on our pages are loaded from third-party services, which may set their own cookies.",
        ],
        "Managing cookies" => [
            "You can block or delete cookies in your browser settings. If you block the session cookie you will not be able to sign in.",
        ],
    ]
);

include "../include/public_foot.php";

?>
