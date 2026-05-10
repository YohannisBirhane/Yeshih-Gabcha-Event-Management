<?php
// routes/eventRoutes.php
// GET    /events
// GET    /events/{id}
// POST   /events/{id}/proceed-payment
// GET    /events/{id}/guests
// GET    /events/{id}/guests/stats
// GET    /events/{id}/guests/{userId}
// POST   /events/{id}/vendors
// GET    /events/{id}/vendors
// DELETE /events/{id}/vendors/{vendorId}
// POST   /events/vendors/bulk-assign
// POST   /events
// PUT    /events/{id}
// DELETE /events/{id}

require_once __DIR__ . '/../controllers/EventController.php';

$method = $_SERVER['REQUEST_METHOD'];
$seg1   = $segments[1] ?? '';   // {id} or "vendors"
$seg2   = $segments[2] ?? '';   // proceed-payment, vendors, guests, or {vendorId}
$seg3   = $segments[3] ?? '';   // bulk-assign, stats, or {userId}

switch (true) {

    // POST /events/vendors/bulk-assign
    case $method === 'POST' && $seg1 === 'vendors' && $seg2 === 'bulk-assign':
        EventController::bulkAssignVendors();
        break;

    // GET /events/{id}/guests/{userId} - Get specific guest details
    case $method === 'GET' && $seg1 !== '' && $seg1 !== 'vendors' && $seg2 === 'guests' && $seg3 !== '' && $seg3 !== 'stats':
        EventController::getEventGuestDetail($seg1, $seg3);
        break;

    // GET /events/{id}/guests/stats - Get guest statistics
    case $method === 'GET' && $seg1 !== '' && $seg1 !== 'vendors' && $seg2 === 'guests' && $seg3 === 'stats':
        EventController::getEventGuestStats($seg1);
        break;

    // GET /events/{id}/guests - Get guests for event
    case $method === 'GET' && $seg1 !== '' && $seg1 !== 'vendors' && $seg2 === 'guests':
        EventController::getEventGuests($seg1);
        break;

    // POST /events/{id}/vendors - Assign vendor to event
    case $method === 'POST' && $seg1 !== '' && $seg1 !== 'vendors' && $seg2 === 'vendors':
        EventController::assignVendor($seg1);
        break;

    // GET /events/{id}/vendors - Get vendors for event
    case $method === 'GET' && $seg1 !== '' && $seg1 !== 'vendors' && $seg2 === 'vendors':
        EventController::getEventVendors($seg1);
        break;

    // DELETE /events/{id}/vendors/{vendorId} - Remove vendor from event
    case $method === 'DELETE' && $seg1 !== '' && $seg1 !== 'vendors' && $seg2 === 'vendors' && $seg3 !== '':
        EventController::removeVendor($seg1, $seg3);
        break;

    // POST /events/{id}/proceed-payment
    case $method === 'POST' && $seg1 !== '' && $seg2 === 'proceed-payment':
        EventController::proceedPayment($seg1);
        break;

    // GET /events/{id}
    case $method === 'GET' && $seg1 !== '':
        EventController::getById($seg1);
        break;

    // GET /events
    case $method === 'GET':
        EventController::getAll();
        break;

    // POST /events
    case $method === 'POST' && $seg1 === '':
        EventController::create();
        break;

    // PUT /events/{id}
    case $method === 'PUT' && $seg1 !== '':
        EventController::update($seg1);
        break;

    // DELETE /events/{id}
    case $method === 'DELETE' && $seg1 !== '':
        EventController::delete($seg1);
        break;

    default:
        sendResponse(404, false, 'Event endpoint not found');
}
