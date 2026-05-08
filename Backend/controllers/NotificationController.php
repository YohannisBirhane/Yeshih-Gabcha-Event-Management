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
