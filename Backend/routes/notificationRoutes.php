<?php
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