-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 06:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_reservation_system_final`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignedtasks`
--

CREATE TABLE `assignedtasks` (
  `TaskID` int(11) NOT NULL,
  `RequestID` int(11) DEFAULT NULL,
  `StaffID` int(11) DEFAULT NULL,
  `AssignmentDateTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `TaskStatus` enum('Pending','InProgress','Completed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignedtasks`
--

INSERT INTO `assignedtasks` (`TaskID`, `RequestID`, `StaffID`, `AssignmentDateTime`, `TaskStatus`) VALUES
(1, 1, 3, '2025-03-30 20:07:50', 'InProgress'),
(2, 2, 4, '2025-03-30 20:07:50', 'InProgress'),
(3, 3, 3, '2025-03-30 20:07:50', 'Pending'),
(5, 2, 3, '2025-05-08 17:03:33', 'Pending'),
(6, 4, 10, '2025-05-09 14:54:48', 'InProgress');

-- --------------------------------------------------------

--
-- Table structure for table `bookingpackages`
--

CREATE TABLE `bookingpackages` (
  `BookingID` int(11) NOT NULL,
  `PackageID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookingpackages`
--

INSERT INTO `bookingpackages` (`BookingID`, `PackageID`) VALUES
(1, 1),
(1, 3),
(2, 3),
(5, 1),
(5, 3),
(8, 1),
(8, 2),
(8, 3),
(9, 1),
(9, 3),
(10, 1),
(10, 2),
(14, 1),
(14, 2);

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `BookingID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `RoomID` int(11) DEFAULT NULL,
  `CheckInDate` date NOT NULL,
  `CheckOutDate` date NOT NULL,
  `NumberOfGuests` int(11) DEFAULT NULL,
  `BookingStatus` enum('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`BookingID`, `UserID`, `RoomID`, `CheckInDate`, `CheckOutDate`, `NumberOfGuests`, `BookingStatus`, `CreatedAt`) VALUES
(1, 1, 1, '2024-01-10', '2024-01-15', 1, 'Pending', '2025-03-30 20:07:50'),
(2, 2, 4, '2024-02-01', '2024-02-05', 3, 'Cancelled', '2025-03-30 20:07:50'),
(3, 1, 2, '2024-03-20', '2024-03-25', 2, 'Cancelled', '2025-03-30 20:07:50'),
(4, 11, 7, '2025-05-17', '2025-05-31', 2, 'Pending', '2025-05-09 04:40:52'),
(5, 11, 5, '2025-05-22', '2025-05-29', 3, 'Confirmed', '2025-05-09 04:56:42'),
(7, 12, 1, '2025-05-11', '2025-05-14', 1, 'Confirmed', '2025-05-10 11:41:36'),
(8, 12, 2, '2025-05-25', '2025-05-27', 2, 'Confirmed', '2025-05-10 13:12:13'),
(9, 12, 1, '2025-06-22', '2025-06-24', 2, 'Completed', '2025-05-10 13:14:31'),
(10, 12, 6, '2025-07-18', '2025-07-26', 2, 'Cancelled', '2025-05-10 13:15:08'),
(14, 17, 3, '2025-06-14', '2025-06-24', 1, 'Confirmed', '2025-06-06 05:46:36');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `PackageID` int(11) NOT NULL,
  `PackageName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`PackageID`, `PackageName`, `Description`, `Price`, `CreatedAt`) VALUES
(1, 'Breakfast Package', 'Daily breakfast for 2 guests', 25.00, '2025-03-30 20:07:50'),
(2, 'Spa Package', 'One-hour massage and sauna access', 80.00, '2025-03-30 20:07:50'),
(3, 'Romance Package', 'Champagne and flower arrangement', 50.00, '2025-03-30 20:07:50');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `RoomID` int(11) NOT NULL,
  `RoomNumber` varchar(50) NOT NULL,
  `RoomType` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `BasePrice` decimal(10,2) NOT NULL,
  `AvailabilityStatus` enum('Available','Occupied','Maintenance') DEFAULT 'Available',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`RoomID`, `RoomNumber`, `RoomType`, `Description`, `BasePrice`, `AvailabilityStatus`, `CreatedAt`) VALUES
(1, '101', 'Suite', 'Cozy single room with basic amenities', 101.02, 'Occupied', '2025-03-30 20:07:49'),
(2, '202', 'Deluxe', 'Spacious room with king-size bed', 150.00, 'Occupied', '2025-03-30 20:07:49'),
(3, '303', 'Suite', 'Luxury suite with living area', 250.00, 'Occupied', '2025-03-30 20:07:49'),
(4, '404', 'Family', 'Two-bedroom suite for families', 200.00, 'Available', '2025-03-30 20:07:49'),
(5, '108', 'Deluxe', 'ghg ghhgjh hgh', 0.76, 'Occupied', '2025-05-03 18:50:03'),
(6, '107', 'Deluxe', 'bv hbhh', 0.22, 'Available', '2025-05-03 19:00:04'),
(7, '96', 'Deluxe', 'thama hariyata danne na room gana', 58.00, 'Available', '2025-05-03 19:33:46'),
(8, '110', 'Deluxe', 'hj hghg', 112.00, 'Available', '2025-05-11 19:16:45');

-- --------------------------------------------------------

--
-- Table structure for table `servicerequests`
--

CREATE TABLE `servicerequests` (
  `RequestID` int(11) NOT NULL,
  `BookingID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `RequestType` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Status` enum('Pending','Assigned','Completed') DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `servicerequests`
--

INSERT INTO `servicerequests` (`RequestID`, `BookingID`, `UserID`, `RequestType`, `Description`, `Status`, `CreatedAt`) VALUES
(1, 1, 1, 'Housekeeping', 'Extra towels needed', 'Completed', '2025-03-30 20:07:50'),
(2, 2, 2, 'Maintenance', 'Leaky faucet in bathroom', 'Assigned', '2025-03-30 20:07:50'),
(3, 3, 1, 'Room Service', 'Late checkout request', 'Assigned', '2025-03-30 20:07:50'),
(4, 3, NULL, 'need food', 'give lunch', 'Pending', '2025-05-09 14:54:47'),
(5, 8, 12, 'Other', 'dadada', 'Pending', '2025-05-10 16:03:16'),
(6, 8, 12, 'Other', 'dadada', '', '2025-05-10 16:09:39'),
(9, 14, 17, 'Housekeeping', 'housekeeping', 'Pending', '2025-06-06 05:46:59');

-- --------------------------------------------------------

--
-- Table structure for table `staffschedule`
--

CREATE TABLE `staffschedule` (
  `ScheduleID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `ScheduleDate` date NOT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staffschedule`
--

INSERT INTO `staffschedule` (`ScheduleID`, `UserID`, `ScheduleDate`, `StartTime`, `EndTime`) VALUES
(1, 3, '2024-01-10', '08:00:00', '16:00:00'),
(2, 4, '2024-01-10', '12:00:00', '20:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `UserType` enum('Customer','Staff','Admin') NOT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastLogin` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FullName`, `Email`, `PasswordHash`, `UserType`, `PhoneNumber`, `Address`, `CreatedAt`, `LastLogin`) VALUES
(1, 'John Does', 'johndoe@example.com', '123', 'Staff', '0123456789', '123 Main St, City, Country', '2025-03-30 20:07:49', NULL),
(2, 'Jane Smith', 'jane@example.com', '123', 'Customer', '555-5678', '456 Oak Ave', '2025-03-30 20:07:49', NULL),
(3, 'Alice Joh', 'alice.staff@example.com', '123', 'Staff', '555-0198', '789 Pine Rd', '2025-03-30 20:07:49', NULL),
(4, 'Bob Brown', 'bob.staff@example.com', '123', 'Staff', '555-4321', '321 Maple Ln', '2025-03-30 20:07:49', NULL),
(5, 'Admin User', 'admin@example.com', '123', 'Admin', '555-9999', 'Hotel Headquarters', '2025-03-30 20:07:49', NULL),
(9, 'Dinusha Madhushani', 'dinu@gmail.com', '123', 'Admin', '+5514194848', 'Kinivita , Galapitamada', '2025-05-08 15:39:02', NULL),
(10, 'Ravindu Lasal', 'mamalasal@gmail.com', '123', 'Staff', '626798575458', 'Aranayaka, Mawanalla', '2025-05-08 18:26:02', NULL),
(11, 'Nirosha Madhuwantha', 'nirosha@gmail.com', '123', 'Customer', '4267646766', 'kiniwita, mahapallegama', '2025-05-09 04:38:01', NULL),
(12, 'Dinushi', 'dinushi@gmail.com', '123', 'Customer', '', '', '2025-05-09 07:44:01', NULL),
(16, 'Nipuni', 'nipuni@gmail.com', '123456', 'Customer', '0123456788', 'kegalle', '2025-06-06 03:38:58', NULL),
(17, 'nipuni madhushika', 'madhu@gmail.com', '123456', 'Customer', '12345678', 'kegalle', '2025-06-06 05:45:10', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignedtasks`
--
ALTER TABLE `assignedtasks`
  ADD PRIMARY KEY (`TaskID`),
  ADD KEY `RequestID` (`RequestID`),
  ADD KEY `StaffID` (`StaffID`);

--
-- Indexes for table `bookingpackages`
--
ALTER TABLE `bookingpackages`
  ADD PRIMARY KEY (`BookingID`,`PackageID`),
  ADD KEY `PackageID` (`PackageID`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `RoomID` (`RoomID`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`PackageID`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`RoomID`),
  ADD UNIQUE KEY `RoomNumber` (`RoomNumber`);

--
-- Indexes for table `servicerequests`
--
ALTER TABLE `servicerequests`
  ADD PRIMARY KEY (`RequestID`),
  ADD KEY `BookingID` (`BookingID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `staffschedule`
--
ALTER TABLE `staffschedule`
  ADD PRIMARY KEY (`ScheduleID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignedtasks`
--
ALTER TABLE `assignedtasks`
  MODIFY `TaskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `PackageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `RoomID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `servicerequests`
--
ALTER TABLE `servicerequests`
  MODIFY `RequestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `staffschedule`
--
ALTER TABLE `staffschedule`
  MODIFY `ScheduleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignedtasks`
--
ALTER TABLE `assignedtasks`
  ADD CONSTRAINT `assignedtasks_ibfk_1` FOREIGN KEY (`RequestID`) REFERENCES `servicerequests` (`RequestID`),
  ADD CONSTRAINT `assignedtasks_ibfk_2` FOREIGN KEY (`StaffID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `bookingpackages`
--
ALTER TABLE `bookingpackages`
  ADD CONSTRAINT `bookingpackages_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `bookings` (`BookingID`),
  ADD CONSTRAINT `bookingpackages_ibfk_2` FOREIGN KEY (`PackageID`) REFERENCES `packages` (`PackageID`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`RoomID`) REFERENCES `rooms` (`RoomID`);

--
-- Constraints for table `servicerequests`
--
ALTER TABLE `servicerequests`
  ADD CONSTRAINT `servicerequests_ibfk_1` FOREIGN KEY (`BookingID`) REFERENCES `bookings` (`BookingID`),
  ADD CONSTRAINT `servicerequests_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `staffschedule`
--
ALTER TABLE `staffschedule`
  ADD CONSTRAINT `staffschedule_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
