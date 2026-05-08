<?php

require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

function sendSMS($phone, $message) {

    $sid = "YOUR_TWILIO_SID";
    $token = "YOUR_TWILIO_TOKEN";

    $twilio = new Client($sid, $token);

    try {

        $twilio->messages->create(
            $phone,
            [
                "from" => "+123456789",
                "body" => $message
            ]
        );

        return [
            "success" => true,
            "message" => "SMS sent successfully"
        ];

    } catch (Exception $e) {

        return [
            "success" => false,
            "message" => $e->getMessage()
        ];
    }
}

// Example
$response = sendSMS(
    "+2519XXXXXXXX",
    "Your registration was successful."
);

echo json_encode($response);

?>