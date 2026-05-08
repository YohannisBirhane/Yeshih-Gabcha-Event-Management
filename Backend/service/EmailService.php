<?php

function sendEmail($to, $subject, $message) {

    $headers = "From: noreply@eventsystem.com";

    if (mail($to, $subject, $message, $headers)) {

        return [
            "success" => true,
            "message" => "Email sent successfully"
        ];

    } else {

        return [
            "success" => false,
            "message" => "Email sending failed"
        ];
    }
}

// Example usage
$response = sendEmail(
    "user@gmail.com",
    "Event Registration",
    "You registered successfully."
);

echo json_encode($response);

?>