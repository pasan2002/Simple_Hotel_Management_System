<?php
require_once __DIR__ . '/../classes/RoomManager.php';
require_once __DIR__ . '/../classes/GuestManager.php';
require_once __DIR__ . '/../includes/header.php';

$roomManager = new RoomManager();
$guestManager = new GuestManager();

$availableRooms = [];
$successMessage = '';
$errorMessage = '';

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $roomId = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
    $checkIn = filter_input(INPUT_POST, 'check_in', FILTER_SANITIZE_STRING);
    $checkOut = filter_input(INPUT_POST, 'check_out', FILTER_SANITIZE_STRING);

    // Validate inputs
    if ($firstName && $lastName && $email && $phone && $roomId && $checkIn && $checkOut) {
        // Create or find guest
        $guest = $guestManager->getGuestByEmail($email);
        if (!$guest) {
            $guestId = $guestManager->createGuest($firstName, $lastName, $email, $phone);
        } else {
            $guestId = $guest['GuestID'];
        }

        // Create booking
        $bookingResult = $roomManager->createBooking($guestId, $roomId, $checkIn, $checkOut);

        if ($bookingResult) {
            $successMessage = "Booking successful!";
        } else {
            $errorMessage = "Booking failed. Please try again.";
        }
    } else {
        $errorMessage = "Invalid input. Please check all fields.";
    }
}

// Get available rooms
$checkIn = filter_input(INPUT_GET, 'check_in', FILTER_SANITIZE_STRING);
$checkOut = filter_input(INPUT_GET, 'check_out', FILTER_SANITIZE_STRING);

if ($checkIn && $checkOut) {
    $availableRooms = $roomManager->getAvailableRooms($checkIn, $checkOut);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hotel Room Booking</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Hotel Room Booking</h1>

        <!-- Success and error messages -->
        <?php if ($successMessage): ?>
            <div class="alert success"><?php echo $successMessage; ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="alert error"><?php echo $errorMessage; ?></div>
        <?php endif; ?>

        <!-- Link to Add New Room -->
        <div class="add-room-link">
            <a href="addRoom.php">Add New Room</a>
        </div>

        <!-- Room Availability Search -->
        <div class="search-rooms">
            <h2>Check Room Availability</h2>
            <form id="availabilityForm" method="GET">
                <div class="form-group">
                    <label for="check_in">Check-in Date</label>
                    <input type="date" id="check_in" name="check_in" required>
                </div>
                <div class="form-group">
                    <label for="check_out">Check-out Date</label>
                    <input type="date" id="check_out" name="check_out" required>
                </div>
                <button type="submit">Search Rooms</button>
            </form>
        </div>

        <!-- Available Rooms Display -->
        <div class="available-rooms">
            <h2>Available Rooms</h2>
            <?php if (!empty($availableRooms)): ?>
                <div class="rooms-grid">
                    <?php foreach ($availableRooms as $room): ?>
                        <div class="room-card">
                            <h3><?php echo htmlspecialchars($room['RoomNumber']); ?> - <?php echo htmlspecialchars($room['RoomType']); ?></h3>
                            <p>Price: $<?php echo number_format($room['PricePerNight'], 2); ?> per night</p>
                            <button class="book-room" data-room-id="<?php echo $room['RoomID']; ?>">Book Now</button>

                            <!-- Link to Edit Room -->
                            <button class="edit-room" data-room-id="<?php echo $room['RoomID']; ?>">Edit Room</button>


                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No rooms available for the selected dates.</p>
            <?php endif; ?>
        </div>

        <!-- Booking Form Modal -->
        <div id="bookingForm" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Book Your Room</h2>
                <form id="roomBooking" method="POST">
                    <input type="hidden" id="booking_room_id" name="room_id">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="check_in_date">Check-in Date</label>
                        <input type="date" id="check_in_date" name="check_in" required>
                    </div>
                    <div class="form-group">
                        <label for="check_out_date">Check-out Date</label>
                        <input type="date" id="check_out_date" name="check_out" required>
                    </div>
                    <button type="submit">Confirm Booking</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Room Modal -->
    <div id="addRoomModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Room</h2>
            <form id="addRoomForm" method="POST" action="addRoom.php">
                <div class="form-group">
                    <label for="room_number">Room Number</label>
                    <input type="text" id="room_number" name="room_number" required>
                </div>
                <div class="form-group">
                    <label for="room_type">Room Type</label>
                    <input type="text" id="room_type" name="room_type" required>
                </div>
                <div class="form-group">
                    <label for="price_per_night">Price per Night</label>
                    <input type="number" id="price_per_night" name="price_per_night" required>
                </div>
                <button type="submit">Add Room</button>
            </form>
        </div>
    </div>

    <!-- Edit Room Modal (This will dynamically load the room details) -->
<div id="editRoomModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Edit Room</h2>
        <form id="editRoomForm" method="POST" action="updateRoom.php">
            <input type="hidden" id="edit_room_id" name="room_id">
            <div class="form-group">
                <label for="edit_room_number">Room Number</label>
                <input type="text" id="edit_room_number" name="room_number" required>
            </div>
            <div class="form-group">
                <label for="edit_room_type">Room Type</label>
                <input type="text" id="edit_room_type" name="room_type" required>
            </div>
            <div class="form-group">
                <label for="edit_price_per_night">Price per Night</label>
                <input type="number" id="edit_price_per_night" name="price_per_night" required>
            </div>
            <button type="submit">Update Room</button>
        </form>
    </div>
</div>



    <script src="js/script.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
