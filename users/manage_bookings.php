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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #ecf0f1;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
        }

        .booking-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }

        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            display: inline-block;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .price-display {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--success);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Current Reservations</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
            <div class="alert alert-info">You have no active reservations</div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($bookings as $booking): ?>
                    <div class="col-lg-6">
                        <div class="booking-card">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="mb-2">Room <?= htmlspecialchars($booking['RoomNumber']) ?></h4>
                                    <span class="status-badge status-<?= strtolower($booking['BookingStatus']) ?>">
                                        <?= htmlspecialchars($booking['BookingStatus']) ?>
                                    </span>
                                </div>
                                <div class="price-display">
                                    $<?= number_format($booking['TotalPrice'], 2) ?>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-check me-2"></i>
                                        <div>
                                            <div class="text-muted small">Check-in</div>
                                            <div class="fw-bold"><?= date('M j, Y', strtotime($booking['CheckInDate'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-times me-2"></i>
                                        <div>
                                            <div class="text-muted small">Check-out</div>
                                            <div class="fw-bold"><?= date('M j, Y', strtotime($booking['CheckOutDate'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users me-2"></i>
                                        <div>
                                            <div class="text-muted small">Guests</div>
                                            <div class="fw-bold"><?= $booking['NumberOfGuests'] ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-bed me-2"></i>
                                        <div>
                                            <div class="text-muted small">Room Type</div>
                                            <div class="fw-bold"><?= htmlspecialchars($booking['RoomType']) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($booking['Packages'])): ?>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-gift me-2"></i>
                                        <div class="fw-bold">Included Packages</div>
                                    </div>
                                    <div class="text-muted"><?= htmlspecialchars($booking['Packages']) ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if ($booking['BookingStatus'] === 'Confirmed' || $booking['BookingStatus'] === 'Pending'): ?>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <input type="hidden" name="booking_id" value="<?= $booking['BookingID'] ?>">
                                    <button type="submit" name="cancel_booking" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-times-circle me-2"></i>Cancel Reservation
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>