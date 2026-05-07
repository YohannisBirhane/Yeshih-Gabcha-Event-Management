<?php

// Get Event RSVPs
function getEventRsvps($req, $res)
{
    try {
        // Get eventId from request parameters
        $eventId = $req['params']['eventId'];

        // Database connection
        $conn = new mysqli("localhost", "root", "", "your_database_name");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to get RSVPs with guest information
        $sql = "SELECT rsvps.*, guests.*
                FROM rsvps
                INNER JOIN guests
                ON rsvps.guestId = guests.id
                WHERE rsvps.eventId = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $eventId);
        $stmt->execute();

        $result = $stmt->get_result();

        $rsvps = [];

        while ($row = $result->fetch_assoc()) {
            $rsvps[] = $row;
        }

        // Return JSON response
        echo json_encode($rsvps);

        $stmt->close();
        $conn->close();

    } catch (Exception $err) {

        http_response_code(500);

        echo json_encode([
            "message" => $err->getMessage()
        ]);
    }
}

?>