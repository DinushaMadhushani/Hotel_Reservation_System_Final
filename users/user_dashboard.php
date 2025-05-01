<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserID'])) {
    header("Location:../auth/login.php");
    exit();
}

$userId = $_SESSION['UserID'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Fetch upcoming bookings
$bookingsStmt = $conn->prepare("
    SELECT b.BookingID, r.RoomNumber, r.RoomType, b.CheckInDate, b.CheckOutDate, b.BookingStatus 
    FROM Bookings b
    JOIN Rooms r ON b.RoomID = r.RoomID
    WHERE b.UserID = ? AND b.CheckInDate > CURDATE()
    ORDER BY b.CheckInDate ASC
");
$bookingsStmt->bind_param("i", $userId);
$bookingsStmt->execute();
$upcomingBookings = $bookingsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch recent activity
$activityStmt = $conn->prepare("
    SELECT 
        'Booking' AS type, 
        b.CheckInDate AS date, 
        CONCAT('Booked ', r.RoomNumber, ' (', r.RoomType, ')') AS description
    FROM Bookings b
    JOIN Rooms r ON b.RoomID = r.RoomID
    WHERE b.UserID = ?
    
    UNION
    
    SELECT 
        'Service Request' AS type, 
        sr.CreatedAt AS date, 
        CONCAT('Requested ', sr.RequestType, ' for ', r.RoomNumber) AS description
    FROM ServiceRequests sr
    JOIN Bookings b ON sr.BookingID = b.BookingID
    JOIN Rooms r ON b.RoomID = r.RoomID
    WHERE sr.UserID = ?
    
    ORDER BY date DESC
    LIMIT 5
");
$activityStmt->bind_param("ii", $userId, $userId);
$activityStmt->execute();
$recentActivity = $activityStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            margin-top: 80px;
        }
        
        .top-nav {
            background: var(--accent);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .main-content {
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .booking-item, .activity-item {
            transition: all 0.3s ease;
        }
        
        .booking-item:hover {
            transform: translateX(10px);
        }
        
        .activity-item:hover {
            transform: translateX(10px);
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav navbar navbar-expand-lg">
        <div class="nav-container container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fa-solid fa- me-2"></i>User Dashboard
            </a>
            
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fa-solid fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bookings.php">
                            <i class="fa-solid fa-calendar-check me-2"></i>My Bookings
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBookingModal">
                        <i class="fa-solid fa-plus me-2"></i>New Booking
                    </button>
                    
                    <div class="dropdown">
                        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user me-2"></i><?= htmlspecialchars($user['FullName']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="user_profile.php">
                                <i class="fa-solid fa-user-gear me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/login.php">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Welcome Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <h3 class="fw-bold">Welcome Back, <?= htmlspecialchars($user['FullName']) ?>!</h3>
                    <p class="text-muted">Your current bookings and activity</p>
                </div>
            </div>

            <!-- Flex Column Layout -->
            <div class="d-flex flex-column gap-4">
                <!-- Upcoming Bookings Card -->
                <div class="card" data-aos="fade-up">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fa-solid fa-calendar-alt me-2"></i>Upcoming Bookings
                        </h5>
                        <?php if (!empty($upcomingBookings)): ?>
                            <?php foreach ($upcomingBookings as $booking): ?>
                                <div class="booking-item p-3 mb-2 rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6><?= htmlspecialchars($booking['RoomNumber']) ?></h6>
                                            <small class="text-muted">
                                                <?= date('M d, Y', strtotime($booking['CheckInDate'])) ?> 
                                                - 
                                                <?= date('M d, Y', strtotime($booking['CheckOutDate'])) ?>
                                            </small>
                                        </div>
                                        <span class="badge bg-success"><?= $booking['BookingStatus'] ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fa-solid fa-calendar-xmark fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No upcoming bookings found</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div class="card" data-aos="fade-up">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="fa-solid fa-clock me-2"></i>Recent Activity
                        </h5>
                        <?php if (!empty($recentActivity)): ?>
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="activity-item p-3 mb-2 rounded">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <?php if ($activity['type'] === 'Booking'): ?>
                                                <i class="fa-solid fa-calendar-check fa-lg text-success"></i>
                                            <?php else: ?>
                                                <i class="fa-solid fa-bell fa-lg text-warning"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <p class="mb-0"><?= htmlspecialchars($activity['description']) ?></p>
                                            <small class="text-muted">
                                                <?= date('M d, Y', strtotime($activity['date'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No recent activity found</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Booking Modal -->
    <div class="modal fade" id="newBookingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-calendar-plus me-2"></i>New Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Room Type</label>
                            <select class="form-select">
                                <option>Deluxe Room</option>
                                <option>Suite</option>
                                <option>Executive Room</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success">Book Now</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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