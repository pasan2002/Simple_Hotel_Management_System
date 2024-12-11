<?php
require_once __DIR__ . '/../classes/RoomManager.php';

$roomManager = new RoomManager();

if (isset($_GET['room_id'])) {
    $roomId = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT);
    
    if ($roomId) {
        $roomDetails = $roomManager->getRoomById($roomId);
        echo json_encode($roomDetails);
    } else {
        echo json_encode(['error' => 'Invalid room ID']);
    }
} else {
    echo json_encode(['error' => 'Room ID not provided']);
}
?>
