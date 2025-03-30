<?php
session_start();
require '../config/db.con.php';

// Check if user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
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

// Fetch recent activity (FIXED QUERY)
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
            --primary: #28a745;
            --secondary: #ffc107;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        
        .sidebar {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link {
            color: white;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
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
        
        .user-card {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .booking-item {
            background: rgba(40, 167, 69, 0.1);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .booking-item:hover {
            transform: translateX(10px);
        }
        
        .activity-item {
            background: rgba(255, 193, 7, 0.1);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .activity-item:hover {
            transform: translateX(10px);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar col-md-3 col-lg-2 d-md-block">
            <div class="position-sticky">
                <div class="user-card text-center p-4">
                    <div class="user-avatar mx-auto mb-3">
                        <i class="fa-solid fa-user fa-3x"></i>
                    </div>
                    <h5><?= htmlspecialchars($user['FullName']) ?></h5>
                    <small><?= htmlspecialchars($user['Email']) ?></small>
                </div>
                <div class="menu p-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fa-solid fa-tachometer-alt me-3"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="bookings.php">
                                <i class="fa-solid fa-calendar-check me-3"></i> My Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fa-solid fa-user-edit me-3"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fa-solid fa-sign-out-alt me-3"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="content col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center py-3">
                <h3 class="my-0">Welcome Back, <?= htmlspecialchars($user['FullName']) ?>!</h3>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fa-solid fa-coins"></i> Loyalty Points: 150
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBookingModal">
                        <i class="fa-solid fa-plus"></i> New Booking
                    </button>
                </div>
            </div>

            <!-- Upcoming Bookings -->
            <div class="card mt-4" data-aos="fade-up">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fa-solid fa-calendar-alt me-2"></i>Upcoming Bookings</h5>
                    <?php if (!empty($upcomingBookings)): ?>
                        <?php foreach ($upcomingBookings as $booking): ?>
                            <div class="booking-item p-3 mb-2 rounded">
                                <div class="d-flex justify-content-between">
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
                        <p class="text-muted text-center py-3">No upcoming bookings</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mt-4" data-aos="fade-up">
                <div class="card-body">
                    <h5 class="card-title mb-4"><i class="fa-solid fa-clock me-2"></i>Recent Activity</h5>
                    <?php if (!empty($recentActivity)): ?>
                        <?php foreach ($recentActivity as $activity): ?>
                            <div class="activity-item p-3 mb-2 rounded">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <?php if ($activity['type'] === 'Booking'): ?>
                                            <i class="fa-solid fa-calendar-check text-success"></i>
                                        <?php else: ?>
                                            <i class="fa-solid fa-bell text-warning"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="mb-0"><?= htmlspecialchars($activity['description']) ?></p>
                                        <small class="text-muted"><?= date('M d, Y', strtotime($activity['date'])) ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No recent activity</p>
                    <?php endif; ?>
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

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        $(document).ready(function() {
            AOS.init();
            
            // Sidebar toggle for mobile
            $('.navbar-toggler').click(function() {
                $('.sidebar').toggleClass('active');
            });
        });
    </script>
</body>
</html>