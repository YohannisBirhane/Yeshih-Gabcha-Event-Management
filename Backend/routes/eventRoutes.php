<?php
// routes/eventRoutes.php
// GET    /events
// GET    /events/{id}
// POST   /events/{id}/proceed-payment
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
$seg2   = $segments[2] ?? '';   // proceed-payment, vendors, or {vendorId}
$seg3   = $segments[3] ?? '';   // bulk-assign

switch (true) {

    // POST /events/vendors/bulk-assign
    case $method === 'POST' && $seg1 === 'vendors' && $seg2 === 'bulk-assign':
        EventController::bulkAssignVendors();
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
