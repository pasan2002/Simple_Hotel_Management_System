<?php
require_once __DIR__ . '/../classes/RoomManager.php';

$roomManager = new RoomManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
    $roomNumber = filter_input(INPUT_POST, 'room_number', FILTER_SANITIZE_STRING);
    $roomType = filter_input(INPUT_POST, 'room_type', FILTER_SANITIZE_STRING);
    $pricePerNight = filter_input(INPUT_POST, 'price_per_night', FILTER_VALIDATE_FLOAT);

    if ($roomId && $roomNumber && $roomType && $pricePerNight) {
        // Update room details
        $result = $roomManager->updateRoom($roomId, $roomNumber, $roomType, $pricePerNight);
        
        if ($result) {
            header('Location: index.php');  // Redirect after successful update
            exit();
        } else {
            echo "Error updating room.";
        }
    } else {
        echo "Invalid input data.";
    }
}
?>
