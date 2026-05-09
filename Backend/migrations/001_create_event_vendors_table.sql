-- Migration: Create event_vendors table for vendor-event assignments
-- Run this SQL in your MySQL database to create the necessary table

CREATE TABLE IF NOT EXISTS event_vendors (
    id VARCHAR(36) PRIMARY KEY,
    eventId VARCHAR(36) NOT NULL,
    vendorId VARCHAR(36) NOT NULL,
    notes LONGTEXT NULL,
    assignedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (eventId) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (vendorId) REFERENCES vendors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_vendor (eventId, vendorId),
    KEY idx_eventId (eventId),
    KEY idx_vendorId (vendorId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
