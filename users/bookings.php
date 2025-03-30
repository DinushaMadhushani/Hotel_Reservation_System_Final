<?php
session_start();
require '../config/db.con.php';

// Check if user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['UserID'];

// Handle booking cancellation
if (isset($_GET['cancel']) && isset($_GET['bid'])) {
    $bookingId = intval($_GET['bid']);
    
    // Check if booking belongs to user and is cancellable
    $checkStmt = $conn->prepare("
        SELECT BookingStatus 
        FROM Bookings 
        WHERE BookingID = ? AND UserID = ?
    ");
    $checkStmt->bind_param("ii", $bookingId, $userId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows === 1) {
        $status = $result->fetch_assoc()['BookingStatus'];
        if ($status === 'Pending' || $status === 'Confirmed') {
            $updateStmt = $conn->prepare("
                UPDATE Bookings 
                SET BookingStatus = 'Cancelled' 
                WHERE BookingID = ?
            ");
            $updateStmt->bind_param("i", $bookingId);
            $updateStmt->execute();
            header("Location: bookings.php?success=Booking+cancelled");
        }
    }
    $checkStmt->close();
}

// Fetch all user bookings with room details
$stmt = $conn->prepare("
    SELECT 
        b.BookingID,
        r.RoomNumber,
        r.RoomType,
        b.CheckInDate,
        b.CheckOutDate,
        b.NumberOfGuests,
        b.BookingStatus,
        b.CreatedAt
    FROM Bookings b
    JOIN Rooms r ON b.RoomID = r.RoomID
    WHERE b.UserID = ?
    ORDER BY b.CheckInDate DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .booking-card {
            transition: transform 0.3s ease;
        }
        .booking-card:hover {
            transform: translateY(-5px);
        }
        .status {
            font-weight: bold;
            text-transform: uppercase;
            padding: 4px 8px;
            border-radius: 4px;
        }
        .status-pending { background: #ffc107; color: #333; }
        .status-confirmed { background: #28a745; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
        .status-completed { background: #6c757d; color: white; }
    </style>
</head>
<body>
    <?php 
    // Include navigation bar with error handling
    $navbarPath = __DIR__ . '/navbar.php';
    if(file_exists($navbarPath)) {
        include $navbarPath;
    } else {
        echo '<div class="alert alert-danger">Navigation bar component missing</div>';
    }
    ?>

    <div class="container mt-5">
        <h3 class="mb-4">My Bookings</h3>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Dates</th>
                                        <th>Guests</th>
                                        <th>Status</th>
                                        <th>Booked On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr class="booking-card" data-aos="fade-up">
                                            <td>
                                                <strong><?= htmlspecialchars($booking['RoomNumber']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($booking['RoomType']) ?></small>
                                            </td>
                                            <td>
                                                <?= date('M d, Y', strtotime($booking['CheckInDate'])) ?><br>
                                                <i class="fa-solid fa-arrow-right"></i><br>
                                                <?= date('M d, Y', strtotime($booking['CheckOutDate'])) ?>
                                            </td>
                                            <td><?= htmlspecialchars($booking['NumberOfGuests']) ?></td>
                                            <td>
                                                <span class="status status-<?= strtolower($booking['BookingStatus']) ?>">
                                                    <?= htmlspecialchars($booking['BookingStatus']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($booking['CreatedAt'])) ?></td>
                                            <td>
                                                <?php if ($booking['BookingStatus'] === 'Pending' || $booking['BookingStatus'] === 'Confirmed'): ?>
                                                    <a href="?cancel=1&bid=<?= $booking['BookingID'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('Are you sure?')">
                                                        <i class="fa-solid fa-ban"></i> Cancel
                                                    </a>
                                                <?php endif; ?>
                                                <a href="booking_details.php?bid=<?= $booking['BookingID'] ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fa-solid fa-eye"></i> Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($bookings)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No bookings found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        $(document).ready(function() {
            AOS.init();
        });
    </script>
</body>
</html>