<?php
require_once __DIR__ . '/../classes/RoomManager.php';

$roomManager = new RoomManager();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomNumber = filter_input(INPUT_POST, 'room_number', FILTER_SANITIZE_STRING);
    $roomType = filter_input(INPUT_POST, 'room_type', FILTER_SANITIZE_STRING);
    $pricePerNight = filter_input(INPUT_POST, 'price_per_night', FILTER_VALIDATE_FLOAT);

    if ($roomNumber && $roomType && $pricePerNight) {
        $result = $roomManager->addRoom($roomNumber, $roomType, $pricePerNight);
        if ($result) {
            header('Location: index.php');
            exit();
        } else {
            echo "Error adding room.";
        }
    }
}
?>
