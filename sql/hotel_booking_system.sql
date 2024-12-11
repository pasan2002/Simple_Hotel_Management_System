-- Create database
CREATE DATABASE hotel_booking_system;
USE hotel_booking_system;

-- Rooms Table
CREATE TABLE Rooms (
    RoomID INT PRIMARY KEY AUTO_INCREMENT,
    RoomNumber VARCHAR(10) NOT NULL,
    RoomType VARCHAR(50) NOT NULL,
    PricePerNight DECIMAL(10,2) NOT NULL,
    IsAvailable BOOLEAN DEFAULT TRUE
);

-- Guests Table
CREATE TABLE Guests (
    GuestID INT PRIMARY KEY AUTO_INCREMENT,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Phone VARCHAR(20),
    Address TEXT
);

-- Bookings Table
CREATE TABLE Bookings (
    BookingID INT PRIMARY KEY AUTO_INCREMENT,
    GuestID INT,
    RoomID INT,
    CheckInDate DATE NOT NULL,
    CheckOutDate DATE NOT NULL,
    TotalPrice DECIMAL(10,2) NOT NULL,
    BookingStatus ENUM('Confirmed', 'Cancelled', 'Completed') DEFAULT 'Confirmed',
    FOREIGN KEY (GuestID) REFERENCES Guests(GuestID),
    FOREIGN KEY (RoomID) REFERENCES Rooms(RoomID)
);

-- Sample Data Insertion
INSERT INTO Rooms (RoomNumber, RoomType, PricePerNight) VALUES 
('101', 'Single', 75.00),
('102', 'Single', 75.00),
('201', 'Double', 120.00),
('202', 'Double', 120.00),
('301', 'Suite', 200.00);

INSERT INTO Guests (FirstName, LastName, Email, Phone) VALUES 
('John', 'Doe', 'john.doe@example.com', '555-1234'),
('Jane', 'Smith', 'jane.smith@example.com', '555-5678');