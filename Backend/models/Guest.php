<?php
// models/Guest.php - Aggregates guest data from payments, bookings, and RSVPs

class Guest {
    private PDO $conn;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Get all guests for an event with their payment and user details
     */
    public function getEventGuests(string $eventId, int $limit = 50, int $offset = 0): array {
        $sql = "SELECT 
                    p.id as paymentId,
                    u.id as userId,
                    u.firstName,
                    u.lastName,
                    u.email,
                    u.phone,
                    u.profileImage,
                    p.amount,
                    p.paymentMethod,
                    p.status as paymentStatus,
                    p.createdAt as bookingDate,
                    e.title as eventTitle,
                    e.eventDate,
                    e.eventTime
                FROM payments p
                JOIN users u ON p.userId = u.id
                JOIN events e ON p.eventId = e.id
                WHERE p.eventId = ?
                ORDER BY p.createdAt DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count total guests for an event
     */
    public function countEventGuests(string $eventId): int {
        $sql = "SELECT COUNT(DISTINCT p.userId) as total 
                FROM payments p 
                WHERE p.eventId = ? AND p.status = 'completed'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId]);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Get guest statistics for an event
     */
    public function getEventGuestStatistics(string $eventId): array {
        $sql = "SELECT 
                    p.status,
                    COUNT(DISTINCT p.userId) as guestCount,
                    COUNT(p.id) as paymentCount,
                    SUM(p.amount) as totalAmount
                FROM payments p
                WHERE p.eventId = ?
                GROUP BY p.status";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get unique guest count by status
     */
    public function getGuestCountByStatus(string $eventId): array {
        $stats = $this->getEventGuestStatistics($eventId);
        $result = [];
        
        foreach ($stats as $stat) {
            $result[$stat['status']] = (int)$stat['guestCount'];
        }
        
        return $result;
    }

    /**
     * Get total revenue from guests
     */
    public function getTotalRevenue(string $eventId): float {
        $sql = "SELECT SUM(amount) as total 
                FROM payments 
                WHERE eventId = ? AND status = 'completed'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (float)($result['total'] ?? 0);
    }

    /**
     * Get guest details by userId and eventId
     */
    public function getGuestDetail(string $eventId, string $userId): ?array {
        $sql = "SELECT 
                    p.id as paymentId,
                    u.id as userId,
                    u.firstName,
                    u.lastName,
                    u.email,
                    u.phone,
                    u.profileImage,
                    p.amount,
                    p.paymentMethod,
                    p.phoneNumber as paymentPhone,
                    p.status as paymentStatus,
                    p.createdAt as bookingDate,
                    e.title as eventTitle,
                    e.eventDate,
                    e.location
                FROM payments p
                JOIN users u ON p.userId = u.id
                JOIN events e ON p.eventId = e.id
                WHERE p.eventId = ? AND p.userId = ?
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId, $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Search guests by name, email, or phone
     */
    public function searchEventGuests(string $eventId, string $search, int $limit = 50): array {
        $sql = "SELECT 
                    p.id as paymentId,
                    u.id as userId,
                    u.firstName,
                    u.lastName,
                    u.email,
                    u.phone,
                    p.amount,
                    p.paymentMethod,
                    p.status as paymentStatus,
                    p.createdAt as bookingDate
                FROM payments p
                JOIN users u ON p.userId = u.id
                WHERE p.eventId = ? 
                AND (u.firstName LIKE ? OR u.lastName LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)
                ORDER BY p.createdAt DESC
                LIMIT ?";
        
        $searchTerm = '%' . $search . '%';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get guests filtered by payment status
     */
    public function getGuestsByPaymentStatus(string $eventId, string $status, int $limit = 50, int $offset = 0): array {
        $sql = "SELECT 
                    p.id as paymentId,
                    u.id as userId,
                    u.firstName,
                    u.lastName,
                    u.email,
                    u.phone,
                    p.amount,
                    p.paymentMethod,
                    p.status as paymentStatus,
                    p.createdAt as bookingDate
                FROM payments p
                JOIN users u ON p.userId = u.id
                WHERE p.eventId = ? AND p.status = ?
                ORDER BY p.createdAt DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$eventId, $status, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>