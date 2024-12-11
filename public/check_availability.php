<?php
require_once __DIR__ . '/../classes/RoomManager.php';

// Ensure proper input validation and sanitization
$checkIn = filter_input(INPUT_GET, 'check_in', FILTER_SANITIZE_STRING);
$checkOut = filter_input(INPUT_GET, 'check_out', FILTER_SANITIZE_STRING);

// Initialize response array
$response = [
    'status' => 'error',
    'message' => 'Invalid input',
    'rooms' => []
];

// Validate dates
if ($checkIn && $checkOut) {
    try {
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);

        // Ensure check-out is after check-in
        if ($checkOutDate > $checkInDate) {
            $roomManager = new RoomManager();
            $availableRooms = $roomManager->getAvailableRooms($checkIn, $checkOut);

            $response = [
                'status' => 'success',
                'message' => count($availableRooms) . ' rooms available',
                'rooms' => $availableRooms
            ];
        } else {
            $response['message'] = 'Check-out date must be after check-in date';
        }
    } catch (Exception $e) {
        $response['message'] = 'Invalid date format';
        error_log('Date Validation Error: ' . $e->getMessage());
    }
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>