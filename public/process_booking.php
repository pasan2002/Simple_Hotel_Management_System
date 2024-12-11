<?php
require_once __DIR__ . '/../classes/RoomManager.php';
require_once __DIR__ . '/../classes/GuestManager.php';

// Initialize response
$response = [
    'status' => 'error',
    'message' => 'Booking failed'
];

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
    $checkIn = filter_input(INPUT_POST, 'check_in', FILTER_SANITIZE_STRING);
    $checkOut = filter_input(INPUT_POST, 'check_out', FILTER_SANITIZE_STRING);

    // Validate all inputs
    if ($firstName && $lastName && $email && $phone && $roomId && $checkIn && $checkOut) {
        try {
            $guestManager = new GuestManager();
            $roomManager = new RoomManager();

            // Check if guest exists, if not create new guest
            $guest = $guestManager->getGuestByEmail($email);
            if (!$guest) {
                $guestId = $guestManager->createGuest($firstName, $lastName, $email, $phone);
            } else {
                $guestId = $guest['GuestID'];
            }

            // Validate dates
            $checkInDate = new DateTime($checkIn);
            $checkOutDate = new DateTime($checkOut);

            if ($checkOutDate > $checkInDate) {
                // Attempt to create booking
                $bookingResult = $roomManager->createBooking($guestId, $roomId, $checkIn, $checkOut);

                if ($bookingResult) {
                    $response = [
                        'status' => 'success',
                        'message' => 'Booking successfully created',
                        'booking_details' => [
                            'guest_id' => $guestId,
                            'room_id' => $roomId,
                            'check_in' => $checkIn,
                            'check_out' => $checkOut
                        ]
                    ];
                }
            } else {
                $response['message'] = 'Invalid date range';
            }
        } catch (Exception $e) {
            error_log('Booking Process Error: ' . $e->getMessage());
            $response['message'] = 'An error occurred while processing your booking';
        }
    } else {
        $response['message'] = 'Invalid or missing input data';
    }
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>