<?php

function processPayment($req, $res)
{
    $amount = $req['body']['amount'] ?? null;

    if (!$amount || $amount <= 0) {
        http_response_code(400);
        echo json_encode([
            "message" => "Invalid payment"
        ]);
        return;
    }

    // MOCK PAYMENT SUCCESS
    echo json_encode([
        "message" => "Payment successful",
        "status" => "paid"
    ]);
}