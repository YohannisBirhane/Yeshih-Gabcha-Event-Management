<?php
// backend-php/controllers/NotificationController.php

require_once __DIR__ . '/../models/Notification.php';

class NotificationController {
    private $notification;

    public function __construct($conn) {
        $this->notification = new Notification($conn);
    }

    // Create Notification
    public function create() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['title']) ||
            empty($data['message'])
        ) {
            http_response_code(400);

            echo json_encode([
                "message" => "Title and message are required"
            ]);

            return;
        }

        $created = $this->notification->create($data);

        if ($created) {
            http_response_code(201);

            echo json_encode([
                "message" => "Notification created successfully"
            ]);
        } else {
            http_response_code(500);

            echo json_encode([
                "message" => "Failed to create notification"
            ]);
        }
    }

    // Get All Notifications
    public function getAll() {
        $notifications = $this->notification->getAll();

        echo json_encode([
            "data" => $notifications
        ]);
    }

    // Get Notification By ID
    public function getById($id) {
        $notification = $this->notification->getById($id);

        if ($notification) {
            echo json_encode($notification);
        } else {
            http_response_code(404);

            echo json_encode([
                "message" => "Notification not found"
            ]);
        }
    }

    // Update Notification
    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        $updated = $this->notification->update($id, $data);

        if ($updated) {
            echo json_encode([
                "message" => "Notification updated successfully"
            ]);
        } else {
            http_response_code(500);

            echo json_encode([
                "message" => "Failed to update notification"
            ]);
        }
    }

    // Delete Notification
    public function delete($id) {
        $deleted = $this->notification->delete($id);

        if ($deleted) {
            echo json_encode([
                "message" => "Notification deleted successfully"
            ]);
        } else {
            http_response_code(500);

            echo json_encode([
                "message" => "Failed to delete notification"
            ]);
        }
    }
}