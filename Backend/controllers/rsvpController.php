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


        $rsvps = [];

        while ($row = $result->fetch_assoc()) {
            $rsvps[] = $row;
        }

        // Return JSON response
        echo json_encode($rsvps);

        $stmt->close();
        $conn->close();

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

?>


