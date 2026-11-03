<?php

$pub_title = "Privacy Policy";
$pub_nav   = "";

include "../include/public_head.php";

pubLegal(
    "LEGAL",
    "Privacy Policy",
    "This policy explains what information FTDP collects, why we collect it and how it is used.",
    [
        "Information we collect" => [
            "When you create an account we collect your name, email address, password (stored as a secure hash) and the role you choose: designer or company.",
            "You may also add profile details such as a headline, biography, location, website, profile pictures, designs, job posts, applications and messages.",
        ],
        "How we use your information" => [
            "We use your information to run your account, show your profile and designs to other visitors, connect designers with companies, and send you notifications about activity on the platform.",
            "We do not sell your personal information.",
        ],
        "What other people can see" => [
            "Designer profiles, uploaded designs, company profiles and job posts are public and can be viewed without an account. Messages, notifications and applications are private to the people involved.",
        ],
        "Your choices" => [
            "You can edit your profile and delete your designs or job posts at any time from your dashboard. To remove your account completely, contact us and we will delete it along with your content.",
        ],
        "Security" => [
            "We take reasonable steps to protect your data, but no online service can be completely secure. Please use a strong, unique password.",
        ],
    ]
);

include "../include/public_foot.php";

?>
