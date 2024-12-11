<?php
require_once __DIR__ . '/Database.php';

class GuestManager {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function createGuest($firstName, $lastName, $email, $phone) {
        try {
            $query = "INSERT INTO Guests (FirstName, LastName, Email, Phone) 
                      VALUES (:firstName, :lastName, :email, :phone)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Guest Creation Error: " . $e->getMessage());
            return false;
        }
    }

    public function getGuestByEmail($email) {
        try {
            $query = "SELECT * FROM Guests WHERE Email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Guest Retrieval Error: " . $e->getMessage());
            return false;
        }
    }

    public function getGuestBookings($guestId) {
        try {
            $query = "SELECT b.*, r.RoomNumber, r.RoomType 
                      FROM Bookings b
                      JOIN Rooms r ON b.RoomID = r.RoomID
                      WHERE b.GuestID = :guestId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':guestId', $guestId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Guest Bookings Retrieval Error: " . $e->getMessage());
            return false;
        }
    }

    public function removeBooking($bookingID){
        try {
            $query = "DELETE FROM bookings WHERE BookingID = :bookingID";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':bookingID', $bookingID, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('Error removing booking: ' . $e->getMessage());
            throw new Exception('Unable to remove booking. Please try again later.');
        }
    }

}
?>