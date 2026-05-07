<?php

require_once '../models/Rsvp.php';

function respondToEvent($conn)
{
    try {

        // Get JSON data from request body
        $data = json_decode(file_get_contents("php://input"), true);

        $guestId = $data['guestId'] ?? null;
        $eventId = $data['eventId'] ?? null;
        $status  = $data['status'] ?? null;
        $message = $data['message'] ?? '';

        // Validate required fields
        if (!$guestId || !$eventId || !$status) {
            http_response_code(400);

            echo json_encode([
                "message" => "Missing fields"
            ]);

            return;
        }

        // Validate RSVP status
        if (!in_array($status, ['accepted', 'declined'])) {
            http_response_code(400);

            echo json_encode([
                "message" => "Invalid RSVP status"
            ]);

            return;
        }

        // Check existing RSVP
        $checkQuery = "SELECT * FROM rsvps WHERE guestId = ? AND eventId = ?";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("ii", $guestId, $eventId);
        $stmt->execute();

        $result = $stmt->get_result();

        // If RSVP already exists -> update
        if ($result->num_rows > 0) {

            $updateQuery = "UPDATE rsvps 
                            SET status = ?, message = ?
                            WHERE guestId = ? AND eventId = ?";

            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param(
                "ssii",
                $status,
                $message,
                $guestId,
                $eventId
            );

            $updateStmt->execute();

            http_response_code(200);

            echo json_encode([
                "message" => "RSVP updated"
            ]);

            return;
        }

        // Create new RSVP
        $insertQuery = "INSERT INTO rsvps (guestId, eventId, status, message)
                        VALUES (?, ?, ?, ?)";

        $insertStmt = $conn->prepare($insertQuery);

        $insertStmt->bind_param(
            "iiss",
            $guestId,
            $eventId,
            $status,
            $message
        );

        $insertStmt->execute();

        http_response_code(201);

        echo json_encode([
            "message" => "RSVP created"
        ]);

    } catch (Exception $err) {

        http_response_code(500);

        echo json_encode([
            "message" => $err->getMessage()
        ]);
    }
}
?>

