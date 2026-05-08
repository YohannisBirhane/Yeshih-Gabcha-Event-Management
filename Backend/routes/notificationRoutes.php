<?php

// routes/notificationRoutes.php

require_once __DIR__ . '/../controllers/NotificationController.php';

$method = $_SERVER['REQUEST_METHOD'];
$seg1   = $segments[1] ?? '';   // e.g. admin
$seg2   = $segments[2] ?? '';   // e.g. unread-count or {id}
$seg3   = $segments[3] ?? '';   // e.g. read

switch (true) {

    // GET /notifications/admin/unread-count
    case $method === 'GET' && $seg1 === 'admin' && $seg2 === 'unread-count':
        NotificationController::getAdminUnreadCount();
        break;

    // PATCH /notifications/admin/read-all
    case $method === 'PATCH' && $seg1 === 'admin' && $seg2 === 'read-all':
        NotificationController::markAllAsRead();
        break;

    // PATCH /notifications/admin/{id}/read
    case $method === 'PATCH' && $seg1 === 'admin' && $seg3 === 'read' && $seg2 !== '':
        NotificationController::markAsRead($seg2);
        break;

    // GET /notifications/admin
    case $method === 'GET' && $seg1 === 'admin' && $seg2 === '':
        NotificationController::getAdmin();
        break;

    default:
        sendResponse(404, false, 'Notification endpoint not found');
}

// backend-php/routes/notificationRoutes.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/NotificationController.php';

$database = new Database();
$conn = $database->connect();

$controller = new NotificationController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

switch ($method) {

    case 'GET':
        if ($id) {
            $controller->getById($id);
        } else {
            $controller->getAll();
        }
        break;

    case 'POST':
        $controller->create();
        break;

    case 'PUT':
        if ($id) {
            $controller->update($id);
        }
        break;

    case 'DELETE':
        if ($id) {
            $controller->delete($id);
        }
        break;

    default:
        http_response_code(405);

        echo json_encode([
            "message" => "Method not allowed"
        ]);
}

