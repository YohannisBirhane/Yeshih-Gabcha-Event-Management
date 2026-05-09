# Vendor Assignment API Endpoints

## Overview
These endpoints manage the assignment of vendors to events in the Yeshih-Gabcha Event Management System.

---

## Endpoints

### 1. POST /events/{id}/vendors
**Assign a single vendor to an event**

**Authentication:** ✅ Admin Required

**Request Body:**
```json
{
    "vendorId": "vendor-uuid",
    "notes": "Optional notes about this assignment"
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Vendor assigned to event",
    "data": {
        "id": "assignment-uuid",
        "eventId": "event-uuid",
        "vendorId": "vendor-uuid",
        "vendor_id": "vendor-uuid",
        "name": "Vendor Name",
        "serviceType": "Catering",
        "phone": "1234567890",
        "email": "vendor@example.com",
        "address": "123 Main St",
        "notes": "Assignment notes",
        "assignedAt": "2026-05-09 10:30:00"
    }
}
```

**Error Responses:**
- 404: Event not found
- 404: Vendor not found
- 400: Vendor is already assigned to this event
- 400: vendorId is required

---

### 2. GET /events/{id}/vendors
**Get all vendors assigned to an event**

**Authentication:** ❌ Public

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Event vendors retrieved",
    "data": {
        "eventId": "event-uuid",
        "count": 2,
        "vendors": [
            {
                "id": "assignment-uuid",
                "eventId": "event-uuid",
                "vendorId": "vendor-uuid",
                "vendor_id": "vendor-uuid",
                "name": "Catering Co.",
                "serviceType": "Catering",
                "phone": "1234567890",
                "email": "catering@example.com",
                "address": "123 Main St",
                "notes": "Notes about this vendor",
                "assignedAt": "2026-05-09 10:30:00"
            },
            {
                "id": "assignment-uuid-2",
                "eventId": "event-uuid",
                "vendorId": "vendor-uuid-2",
                "vendor_id": "vendor-uuid-2",
                "name": "Decoration Studio",
                "serviceType": "Decoration",
                "phone": "9876543210",
                "email": "decoration@example.com",
                "address": "456 Oak Ave",
                "notes": null,
                "assignedAt": "2026-05-09 11:00:00"
            }
        ]
    }
}
```

**Error Responses:**
- 404: Event not found

---

### 3. DELETE /events/{id}/vendors/{vendorId}
**Remove a vendor from an event**

**Authentication:** ✅ Admin Required

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Vendor removed from event"
}
```

**Error Responses:**
- 404: Event not found
- 404: Vendor assignment not found

---

### 4. POST /events/vendors/bulk-assign
**Assign multiple vendors to an event at once**

**Authentication:** ✅ Admin Required

**Request Body:**
```json
{
    "eventId": "event-uuid",
    "vendorIds": [
        "vendor-uuid-1",
        "vendor-uuid-2",
        "vendor-uuid-3"
    ],
    "notes": "Optional notes for all assignments"
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Vendors assigned",
    "data": {
        "total": 3,
        "success": 2,
        "assigned": [
            {
                "id": "assignment-uuid-1",
                "eventId": "event-uuid",
                "vendorId": "vendor-uuid-1",
                "vendor_id": "vendor-uuid-1",
                "name": "Vendor One",
                "serviceType": "Catering",
                "phone": "1111111111",
                "email": "vendor1@example.com",
                "address": "Address 1",
                "notes": null,
                "assignedAt": "2026-05-09 10:30:00"
            },
            {
                "id": "assignment-uuid-2",
                "eventId": "event-uuid",
                "vendorId": "vendor-uuid-2",
                "vendor_id": "vendor-uuid-2",
                "name": "Vendor Two",
                "serviceType": "Photography",
                "phone": "2222222222",
                "email": "vendor2@example.com",
                "address": "Address 2",
                "notes": null,
                "assignedAt": "2026-05-09 10:30:00"
            }
        ],
        "failed": [
            {
                "vendorId": "vendor-uuid-3",
                "reason": "Vendor not found"
            }
        ]
    }
}
```

**Error Responses:**
- 400: eventId is required
- 400: vendorIds array is required
- 404: Event not found

---

## Database Setup

To use these endpoints, you must create the `event_vendors` table:

```sql
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
```

Run this query in phpMyAdmin or your MySQL client.

---

## Usage Examples

### Assign a vendor to an event (Admin)
```bash
curl -X POST http://localhost/api/events/{event-id}/vendors \
  -H "Authorization: Bearer {your_jwt_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "vendorId": "vendor-123",
    "notes": "Primary catering vendor"
  }'
```

### Get vendors for an event
```bash
curl -X GET http://localhost/api/events/{event-id}/vendors
```

### Remove a vendor from an event (Admin)
```bash
curl -X DELETE http://localhost/api/events/{event-id}/vendors/{vendor-id} \
  -H "Authorization: Bearer {your_jwt_token}"
```

### Assign multiple vendors at once (Admin)
```bash
curl -X POST http://localhost/api/events/vendors/bulk-assign \
  -H "Authorization: Bearer {your_jwt_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "eventId": "event-123",
    "vendorIds": ["vendor-1", "vendor-2", "vendor-3"]
  }'
```

---

## Error Handling

All endpoints return standard error responses:

```json
{
    "success": false,
    "message": "Error description",
    "data": null
}
```

Common HTTP status codes:
- `200`: Success
- `201`: Created
- `400`: Bad Request (validation error)
- `401`: Unauthorized (authentication failed)
- `403`: Forbidden (insufficient permissions)
- `404`: Not Found
- `500`: Server Error

---

## Implementation Notes

- All vendor assignments are unique per event (duplicate assignments are prevented)
- Bulk assign returns partial success (some vendors may fail while others succeed)
- When an event is deleted, all associated vendor assignments are automatically deleted
- When a vendor is deleted, all assignments for that vendor are automatically deleted
- Timestamps are recorded in the server's timezone (UTC by default)
