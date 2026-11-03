<?php

header(
    "Content-Type: application/json"
);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "reply" => "Invalid request."
    ]);

    exit;

}

$input =
    json_decode(
        file_get_contents("php://input"),
        true
    );

$message =
    trim(
        $input["message"] ?? ""
    );

if ($message === "") {

    echo json_encode([
        "success" => false,
        "reply" => "Please enter a message."
    ]);

    exit;

}

$lowerMessage =
    strtolower($message);

$reply =
    "Thanks for contacting FTDP. " .
    "Could you tell me a little more about what you need help with?";

if (
    strpos(
        $lowerMessage,
        "account"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "register"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "signup"
    ) !== false
) {

    $reply =
        "You can create an FTDP account by clicking " .
        "'Join FTDP' in the navigation menu. " .
        "You can register as a designer or company.";

}

elseif (
    strpos(
        $lowerMessage,
        "designer"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "designers"
    ) !== false
) {

    $reply =
        "Designers can create a profile, showcase " .
        "their creative work and connect with " .
        "fashion opportunities through FTDP.";

}

elseif (
    strpos(
        $lowerMessage,
        "company"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "companies"
    ) !== false
) {

    $reply =
        "Companies can use FTDP to discover " .
        "fashion designers and creative talent. " .
        "You can join FTDP as a company from " .
        "the registration page.";

}

elseif (
    strpos(
        $lowerMessage,
        "login"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "log in"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "password"
    ) !== false
) {

    $reply =
        "If you are having trouble logging in, " .

        "If the problem continues, contact our support team.";

}

elseif (
    strpos(
        $lowerMessage,
        "contact"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "support"
    ) !== false
) {

    $reply =
        "You can contact the FTDP team through " .
        "our Contact Us page. We'll be happy to help.";

}

elseif (
    strpos(
        $lowerMessage,
        "what is ftdp"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "ftdp"
    ) !== false
) {

    $reply =
        "FTDP is a fashion talent discovery platform " .
        "designed to connect creative talent with " .
        "fashion opportunities and industry connections.";

}

elseif (
    strpos(
        $lowerMessage,
        "hello"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "hi"
    ) !== false
    ||
    strpos(
        $lowerMessage,
        "hey"
    ) !== false
) {

    $reply =
        "Hello! 👋 Welcome to FTDP. " .
        "You can ask me about accounts, designers, " .
        "companies, login problems or FTDP.";

}

elseif (
    strpos(
        $lowerMessage,
        "thank"
    ) !== false
) {

    $reply =
        "You're welcome! " .
        "I'm always here if you need help.";

}

$logDirectory =
    __DIR__ . "/data";

$logFile =
    $logDirectory . "/chat_logs.json";

if (!is_dir($logDirectory)) {

    mkdir(
        $logDirectory,
        0755,
        true
    );

}

$logs = [];

if (file_exists($logFile)) {

    $existingData =
        file_get_contents(
            $logFile
        );

    $logs =
        json_decode(
            $existingData,
            true
        );

    if (!is_array($logs)) {

        $logs = [];

    }

}

$logs[] = [

    "id" =>
        uniqid(
            "chat_",
            true
        ),

    "message" =>
        $message,

    "reply" =>
        $reply,

    "time" =>
        date(
            "Y-m-d H:i:s"
        )

];

file_put_contents(
    $logFile,
    json_encode(
        $logs,
        JSON_PRETTY_PRINT
    )
);

echo json_encode([

    "success" => true,

    "reply" => $reply

]);

exit;

?>
