<?php
// models/EventVendor.php — manages vendor assignments to events

class EventVendor {
    private PDO $conn;
    private string $table = 'event_vendors';

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Assign a vendor to an event
     */
    public function assignVendor(string $eventId, string $vendorId, ?string $notes = null): array {
        $id  = $this->uuid();
        $sql = "INSERT INTO {$this->table} (id, eventId, vendorId, notes, assignedAt)
                VALUES (?, ?, ?, ?, NOW())";

        $this->conn->prepare($sql)->execute([
            $id,
            $eventId,
            $vendorId,
            $notes,
        ]);

        return $this->findById($id);
    }

    /**
     * Get all vendors assigned to an event
     */
    public function getByEventId(string $eventId): array {
        $sql = "SELECT ev.id, ev.eventId, ev.vendorId, ev.notes, ev.assignedAt,
                        v.id as vendor_id, v.name, v.serviceType, v.phone, v.email, v.address
                FROM {$this->table} ev
                JOIN vendors v ON ev.vendorId = v.id
                WHERE ev.eventId = ?
                ORDER BY ev.assignedAt DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Get all events assigned to a vendor
     */
    public function getByVendorId(string $vendorId): array {
        $sql = "SELECT ev.id, ev.eventId, ev.vendorId, ev.notes, ev.assignedAt,
                        e.id as event_id, e.title, e.eventType, e.eventDate, e.location
                FROM {$this->table} ev
                JOIN events e ON ev.eventId = e.id
                WHERE ev.vendorId = ?
                ORDER BY ev.assignedAt DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$vendorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Find assignment by ID
     */
    public function findById(string $id): ?array {
        $sql = "SELECT ev.id, ev.eventId, ev.vendorId, ev.notes, ev.assignedAt,
                        v.id as vendor_id, v.name, v.serviceType, v.phone, v.email, v.address
                FROM {$this->table} ev
                JOIN vendors v ON ev.vendorId = v.id
                WHERE ev.id = ? LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Check if vendor is already assigned to event
     */
    public function isAssigned(string $eventId, string $vendorId): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE eventId = ? AND vendorId = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId, $vendorId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Unassign vendor from event
     */
    public function unassignVendor(string $eventId, string $vendorId): bool {
        $sql = "DELETE FROM {$this->table} WHERE eventId = ? AND vendorId = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$eventId, $vendorId]);
    }

    /**
     * Delete assignment by ID
     */
    public function delete(string $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    /**
     * Update assignment notes
     */
    public function updateNotes(string $id, string $notes): ?array {
        $sql = "UPDATE {$this->table} SET notes = ? WHERE id = ?";
        $this->conn->prepare($sql)->execute([$notes, $id]);
        return $this->findById($id);
    }

    /**
     * Get all assignments (with pagination)
     */
    public function getAll(int $limit = 50, int $offset = 0): array {
        $sql = "SELECT ev.id, ev.eventId, ev.vendorId, ev.notes, ev.assignedAt,
                        v.id as vendor_id, v.name, v.serviceType, v.phone, v.email,
                        e.id as event_id, e.title, e.eventDate
                FROM {$this->table} ev
                JOIN vendors v ON ev.vendorId = v.id
                JOIN events e ON ev.eventId = e.id
                ORDER BY ev.assignedAt DESC
                LIMIT ? OFFSET ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Count all assignments
     */
    public function countAll(): int {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table}");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Generate UUID
     */
    private function uuid(): string {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff),
            mt_rand(0,0x0fff)|0x4000, mt_rand(0,0x3fff)|0x8000,
            mt_rand(0,0xffff), mt_rand(0,0xffff), mt_rand(0,0xffff));
    }
}
