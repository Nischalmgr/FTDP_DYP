<?php

$pub_title = "Terms of Service";
$pub_nav   = "";

include "../include/public_head.php";

pubLegal(
    "LEGAL",
    "Terms of Service",
    "By creating an account or using FTDP you agree to these terms.",
    [
        "Using FTDP" => [
            "FTDP is a platform where fashion designers share their work and companies post jobs. You must provide accurate information and keep your login details safe.",
        ],
        "Your content" => [
            "You keep ownership of the designs, text and images you upload. By posting them you allow FTDP to display them on the platform. Only upload work you created or have permission to share.",
        ],
        "Acceptable behaviour" => [
            "Do not post illegal, misleading, hateful or infringing content, do not harass other members, and do not attempt to disrupt the platform. We may remove content or suspend accounts that break these rules.",
        ],
        "Jobs and applications" => [
            "Companies are responsible for the jobs they post, and designers are responsible for their applications. FTDP does not employ designers and is not a party to any agreement between members.",
        ],
        "Changes and availability" => [
            "We may update these terms or change the platform from time to time. We do not guarantee that the service will always be available or free of errors.",
        ],
    ]
);

include "../include/public_foot.php";

?>
