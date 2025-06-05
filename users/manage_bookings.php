<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['UserID'];
$error = $success = '';
$bookings = [];

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $bookingId = intval($_POST['booking_id']);
    
    try {
        $conn->begin_transaction();
        
        // Get booking details
        $stmt = $conn->prepare("SELECT b.*, r.RoomID 
                              FROM Bookings b
                              JOIN Rooms r ON b.RoomID = r.RoomID
                              WHERE b.BookingID = ? AND b.UserID = ?");
        $stmt->bind_param("ii", $bookingId, $userId);
        $stmt->execute();
        $booking = $stmt->get_result()->fetch_assoc();
        
        if (!$booking) {
            throw new Exception("Booking not found");
        }
        
        // Check if cancellation is allowed
        $checkInDate = new DateTime($booking['CheckInDate']);
        $today = new DateTime();
        
        if ($today >= $checkInDate) {
            throw new Exception("Cannot cancel booking after check-in date");
        }
        
        // Update booking status
        $stmt = $conn->prepare("UPDATE Bookings 
                              SET BookingStatus = 'Cancelled' 
                              WHERE BookingID = ?");
        $stmt->bind_param("i", $bookingId);
        $stmt->execute();
        
        // Update room availability
        $stmt = $conn->prepare("UPDATE Rooms 
                              SET AvailabilityStatus = 'Available' 
                              WHERE RoomID = ?");
        $stmt->bind_param("i", $booking['RoomID']);
        $stmt->execute();
        
        $conn->commit();
        $success = "Booking #$bookingId has been cancelled successfully";
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Get current bookings
try {
    $stmt = $conn->prepare("SELECT 
        b.BookingID,
        b.CheckInDate,
        b.CheckOutDate,
        b.NumberOfGuests,
        b.BookingStatus,
        r.RoomNumber,
        r.RoomType,
        r.BasePrice,
        GROUP_CONCAT(pk.PackageName SEPARATOR ', ') AS Packages,
        SUM(IFNULL(pk.Price, 0)) AS PackageTotal
        FROM Bookings b
        JOIN Rooms r ON b.RoomID = r.RoomID
        LEFT JOIN BookingPackages bp ON b.BookingID = bp.BookingID
        LEFT JOIN Packages pk ON bp.PackageID = pk.PackageID
        WHERE b.UserID = ? 
        AND b.BookingStatus IN ('Pending', 'Confirmed')
        AND b.CheckOutDate >= CURDATE()
        GROUP BY b.BookingID
        ORDER BY b.CheckInDate DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Calculate total prices
    foreach ($bookings as &$booking) {
        $checkIn = new DateTime($booking['CheckInDate']);
        $checkOut = new DateTime($booking['CheckOutDate']);
        $nights = $checkIn->diff($checkOut)->days;
        $roomTotal = $booking['BasePrice'] * $nights;
        $booking['TotalPrice'] = $roomTotal + $booking['PackageTotal'];
    }
    
} catch (Exception $e) {
    $error = "Error fetching bookings: " . $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Hotel System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<?php include '../includes/user_header.php'; ?>

<body class="bg-light">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <h2 class="text-2xl font-bold mb-6 flex items-center text-primary" data-aos="fade-right">
            <i class="fas fa-calendar-alt mr-3 text-accent"></i> Current Reservations
        </h2>
        
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded animate-fade-in" data-aos="fade-up">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded animate-fade-in" data-aos="fade-up">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded" data-aos="fade-up">
                You have no active reservations
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($bookings as $index => $booking): ?>
                    <div data-aos="fade-up" data-aos-delay="<?= 100 + ($index * 50) ?>">
                        <div class="bg-white rounded-lg shadow-custom overflow-hidden transition duration-300 transform hover:scale-105 hover:shadow-accent group border border-gray-200 hover:border-accent">
                            <div class="p-6 relative overflow-hidden">
                                <!-- Decorative elements -->
                                <div class="absolute -right-12 -top-12 w-32 h-32 bg-accent rounded-full opacity-5 group-hover:opacity-10 transition-opacity duration-300"></div>
                                <div class="absolute -left-12 -bottom-12 w-24 h-24 bg-primary rounded-full opacity-5 group-hover:opacity-10 transition-opacity duration-300"></div>
                                
                                <div class="flex justify-between items-start mb-4 relative z-10">
                                    <div>
                                        <h4 class="text-xl font-bold mb-2 text-primary group-hover:text-accent transition duration-300">
                                            Room <?= htmlspecialchars($booking['RoomNumber']) ?>
                                        </h4>
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                                            <?php 
                                            switch(strtolower($booking['BookingStatus'])) {
                                                case 'confirmed':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'pending':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                default:
                                                    echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>"
                                        >
                                            <?= htmlspecialchars($booking['BookingStatus']) ?>
                                        </span>
                                    </div>
                                    <div class="text-xl font-bold text-accent animate-float">
                                        $<?= number_format($booking['TotalPrice'], 2) ?>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div class="flex items-center">
                                        <div class="bg-primary-light bg-opacity-10 p-2 rounded-full mr-3">
                                            <i class="fas fa-calendar-check text-accent"></i>
                                        </div>
                                        <div>
                                            <div class="text-gray-500 text-sm">Check-in</div>
                                            <div class="font-bold"><?= date('M j, Y', strtotime($booking['CheckInDate'])) ?></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="bg-primary-light bg-opacity-10 p-2 rounded-full mr-3">
                                            <i class="fas fa-calendar-times text-accent"></i>
                                        </div>
                                        <div>
                                            <div class="text-gray-500 text-sm">Check-out</div>
                                            <div class="font-bold"><?= date('M j, Y', strtotime($booking['CheckOutDate'])) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div class="flex items-center">
                                        <div class="bg-primary-light bg-opacity-10 p-2 rounded-full mr-3">
                                            <i class="fas fa-users text-accent"></i>
                                        </div>
                                        <div>
                                            <div class="text-gray-500 text-sm">Guests</div>
                                            <div class="font-bold"><?= $booking['NumberOfGuests'] ?></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="bg-primary-light bg-opacity-10 p-2 rounded-full mr-3">
                                            <i class="fas fa-bed text-accent"></i>
                                        </div>
                                        <div>
                                            <div class="text-gray-500 text-sm">Room Type</div>
                                            <div class="font-bold"><?= htmlspecialchars($booking['RoomType']) ?></div>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($booking['Packages'])): ?>
                                    <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <div class="flex items-center mb-2">
                                            <div class="bg-primary-light bg-opacity-10 p-2 rounded-full mr-3">
                                                <i class="fas fa-gift text-accent"></i>
                                            </div>
                                            <div class="font-bold text-primary">Included Packages</div>
                                        </div>
                                        <div class="text-gray-600 pl-11"><?= htmlspecialchars($booking['Packages']) ?></div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($booking['BookingStatus'] === 'Confirmed' || $booking['BookingStatus'] === 'Pending'): ?>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        <input type="hidden" name="booking_id" value="<?= $booking['BookingID'] ?>">
                                        <button type="submit" name="cancel_booking" 
                                                class="w-full mt-2 border-2 border-red-500 text-red-600 hover:bg-red-500 hover:text-white font-bold py-2 px-4 rounded-md transition duration-300 transform hover:scale-105 flex items-center justify-center">
                                            <i class="fas fa-times-circle mr-2"></i>Cancel Reservation
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/sub_footer.php'; ?>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true
        });
    </script>
</body>
</html>