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
