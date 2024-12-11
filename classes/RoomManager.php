<?php
require_once __DIR__ . '/Database.php';

class RoomManager {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllRooms() {
        $query = "SELECT * FROM Rooms";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableRooms($checkIn, $checkOut) {
        $query = "SELECT * FROM Rooms 
                  WHERE RoomID NOT IN (
                      SELECT RoomID FROM Bookings 
                      WHERE (CheckInDate < :checkOut AND CheckOutDate > :checkIn)
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':checkIn', $checkIn);
        $stmt->bindParam(':checkOut', $checkOut);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createBooking($guestId, $roomId, $checkIn, $checkOut) {
        try {
            // Calculate total price
            $roomQuery = "SELECT PricePerNight FROM Rooms WHERE RoomID = :roomId";
            $roomStmt = $this->db->prepare($roomQuery);
            $roomStmt->bindParam(':roomId', $roomId);
            $roomStmt->execute();
            $room = $roomStmt->fetch(PDO::FETCH_ASSOC);

            $nights = ceil((strtotime($checkOut) - strtotime($checkIn)) / (60 * 60 * 24));
            $totalPrice = $room['PricePerNight'] * $nights;

            // Insert booking
            $query = "INSERT INTO Bookings (GuestID, RoomID, CheckInDate, CheckOutDate, TotalPrice) 
                      VALUES (:guestId, :roomId, :checkIn, :checkOut, :totalPrice)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':guestId', $guestId);
            $stmt->bindParam(':roomId', $roomId);
            $stmt->bindParam(':checkIn', $checkIn);
            $stmt->bindParam(':checkOut', $checkOut);
            $stmt->bindParam(':totalPrice', $totalPrice);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Booking Creation Error: " . $e->getMessage());
            return false;
        }
    }

    public function addRoom($roomNumber, $roomType, $pricePerNight) {
        try {
            // Prepare SQL query to insert the new room
            $query = "INSERT INTO rooms (RoomNumber, RoomType, PricePerNight) 
                      VALUES (:roomNumber, :roomType, :pricePerNight)";
            $stmt = $this->db->prepare($query);
            
            // Bind the parameters
            $stmt->bindParam(':roomNumber', $roomNumber, PDO::PARAM_STR);
            $stmt->bindParam(':roomType', $roomType, PDO::PARAM_STR);
            $stmt->bindParam(':pricePerNight', $pricePerNight, PDO::PARAM_STR);
    
            // Execute the query and return the result
            return $stmt->execute();
        } catch (Exception $e) {
            // Log error and throw exception
            error_log('Error adding new room: ' . $e->getMessage());
            throw new Exception('Unable to add the room. Please try again.');
        }
    }
    
    public function updateRoom($roomId, $roomNumber, $roomType, $pricePerNight) {
        try {
            // Prepare SQL query to update the room's details
            $query = "UPDATE rooms 
                      SET RoomNumber = :roomNumber, RoomType = :roomType, PricePerNight = :pricePerNight 
                      WHERE RoomID = :roomId";
            $stmt = $this->db->prepare($query);
            
            // Bind the parameters
            $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
            $stmt->bindParam(':roomNumber', $roomNumber, PDO::PARAM_STR);
            $stmt->bindParam(':roomType', $roomType, PDO::PARAM_STR);
            $stmt->bindParam(':pricePerNight', $pricePerNight, PDO::PARAM_STR);
    
            // Execute the query and return the result
            return $stmt->execute();
        } catch (Exception $e) {
            // Log error and throw exception
            error_log('Error updating room: ' . $e->getMessage());
            throw new Exception('Unable to update the room. Please try again.');
        }
    }
    

    public function getRoomById($roomId) {
        try {
            // Prepare SQL query to get room details by RoomID
            $query = "SELECT * FROM Rooms WHERE RoomID = :roomId";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':roomId', $roomId, PDO::PARAM_INT);
            $stmt->execute();

            // Return room details if found
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            // Log error and throw exception
            error_log('Error fetching room by ID: ' . $e->getMessage());
            throw new Exception('Unable to fetch the room details. Please try again.');
        }
    }
}
?>