<?php
require_once __DIR__ . '/../classes/GuestManager.php';
require_once __DIR__ . '/../includes/header.php';

// Initialize variables
$bookings = [];
$errorMessage = '';

// Handle guest booking retrieval and removal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guestManager = new GuestManager();

    if (isset($_POST['email'])) {
        // Retrieve bookings by email
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        if ($email) {
            try {
                $guest = $guestManager->getGuestByEmail($email);

                if ($guest) {
                    // Retrieve guest's bookings
                    $bookings = $guestManager->getGuestBookings($guest['GuestID']);

                    if (empty($bookings)) {
                        $errorMessage = 'No bookings found for this email.';
                    }
                } else {
                    $errorMessage = 'No guest found with this email address.';
                }
            } catch (Exception $e) {
                error_log('Guest Bookings Retrieval Error: ' . $e->getMessage());
                $errorMessage = 'An error occurred while retrieving bookings.';
            }
        } else {
            $errorMessage = 'Please enter a valid email address.';
        }
    } elseif (isset($_POST['removeBookingID'])) {
        // Remove booking by ID
        $bookingID = filter_input(INPUT_POST, 'removeBookingID', FILTER_SANITIZE_NUMBER_INT);

        if ($bookingID) {
            try {
                if ($guestManager->removeBooking($bookingID)) {
                    $errorMessage = 'Booking removed successfully.';
                } else {
                    $errorMessage = 'Failed to remove the booking.';
                }
            } catch (Exception $e) {
                error_log('Booking Removal Error: ' . $e->getMessage());
                $errorMessage = 'An error occurred while removing the booking.';
            }
        } else {
            $errorMessage = 'Invalid booking ID.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>My Bookings</h1>

        <!-- Guest Booking Search Form -->
        <div class="booking-search">
            <form method="POST">
                <div class="form-group">
                    <label for="email">Enter Your Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit">View My Bookings</button>
            </form>
        </div>

        <!-- Bookings Display -->
        <div class="bookings-display">
            <?php if ($errorMessage): ?>
                <div class="alert error"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <?php if (!empty($bookings)): ?>
                <h2>Your Bookings</h2>
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th class="booking-id">Booking ID</th>
                            <th class="room-number">Room Number</th>
                            <th class="room-type">Room Type</th>
                            <th class="check-in">Check-In</th>
                            <th class="check-out">Check-Out</th>
                            <th class="total-price">Total Price</th>
                            <th class="status">Status</th>
                            <th class="actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['BookingID']); ?></td>
                                <td><?php echo htmlspecialchars($booking['RoomNumber']); ?></td>
                                <td><?php echo htmlspecialchars($booking['RoomType']); ?></td>
                                <td><?php echo htmlspecialchars($booking['CheckInDate']); ?></td>
                                <td><?php echo htmlspecialchars($booking['CheckOutDate']); ?></td>
                                <td>$<?php echo number_format($booking['TotalPrice'], 2); ?></td>
                                <td class="<?php 
                                    $status = strtolower($booking['BookingStatus'] ?? 'confirmed');
                                    echo "status-$status";
                                ?>"><?php echo htmlspecialchars($booking['BookingStatus'] ?? 'Confirmed'); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="removeBookingID" value="<?php echo htmlspecialchars($booking['BookingID']); ?>">
                                        <button type="submit" class="remove-button">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script src="js/guest_bookings.js"></script>
</body>
</html>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
