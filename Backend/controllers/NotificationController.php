<?php

// controllers/NotificationController.php

require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class NotificationController {

    // GET /notifications/admin
    public static function getAdmin(): void {
        global $conn, $segments;
        $auth = authorizeAdmin();
        $userId = $auth['id'];

        $limit  = (int)($_GET['limit']  ?? 20);
        $offset = (int)($_GET['offset'] ?? 0);

        $sql = "SELECT * FROM notifications WHERE (userId = ? OR userId IS NULL) ORDER BY createdAt DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendResponse(200, true, 'Notifications retrieved', [
            'notifications' => $rows,
        ]);
    }

    // GET /notifications/admin/unread-count
    public static function getAdminUnreadCount(): void {
        global $conn;
        $auth = authorizeAdmin();
        $userId = $auth['id'];

        $sql = "SELECT COUNT(*) as count FROM notifications WHERE (userId = ? OR userId IS NULL) AND isRead = FALSE";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        sendResponse(200, true, 'Unread count retrieved', ['count' => (int)$row['count']]);
    }

    // PATCH /notifications/admin/{id}/read
    public static function markAsRead(string $id): void {
        global $conn;
        authorizeAdmin();
        $model = new Notification($conn);
        $found = $model->findById($id);
        if (!$found) sendResponse(404, false, 'Notification not found');
        $model->markAsRead($id);
        sendResponse(200, true, 'Notification marked as read');
    }

    // PATCH /notifications/admin/read-all
    public static function markAllAsRead(): void {
        global $conn;
        $auth = authorizeAdmin();
        $userId = $auth['id'];

        $sql = "UPDATE notifications SET isRead = TRUE, readAt = NOW() WHERE (userId = ? OR userId IS NULL)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);

        sendResponse(200, true, 'All notifications marked as read');
    }
}

?>

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

