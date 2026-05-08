<?php
// backend-php/models/Notification.php

class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Create Notification
    public function create($data) {
        $sql = "INSERT INTO {$this->table}
                (title, message, type, receiver, status)
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $data['title'] ?? null,
            $data['message'] ?? null,
            $data['type'] ?? 'general',
            $data['receiver'] ?? null,
            $data['status'] ?? 'unread'
        ]);
    }

    // Get All Notifications
    public function getAll($limit = 50, $offset = 0) {
        $sql = "SELECT * FROM {$this->table}
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limit, $offset]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get Notification By ID
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update Notification
    public function update($id, $data) {
        $sql = "UPDATE {$this->table}
                SET title = ?, message = ?, type = ?, receiver = ?, status = ?
                WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            $data['title'] ?? null,
            $data['message'] ?? null,
            $data['type'] ?? 'general',
            $data['receiver'] ?? null,
            $data['status'] ?? 'unread',
            $id
        ]);
    }

    // Delete Notification
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([$id]);
    }
}