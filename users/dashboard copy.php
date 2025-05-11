<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

// Get customer data
$userId = $_SESSION['UserID'];
$customerQuery = "SELECT * FROM Users WHERE UserID = ?";
$stmt = $conn->prepare($customerQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        

        
:root {
    --primary: #1a1a1a;
    --secondary: #ffffff;
    --accent: #d4af37;
    --light: #f5f5f5;
    --dark: #121212;
}

        body {
            background-color: var(--light);
            color: var(--text-light);
        }

        .navbar {
            background: var(--light) !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }

        .nav-link {
            color: var(--text-light) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover {
            color: var(--accent) !important;
            transform: translateY(-2px);
        }

        .dropdown-menu {
            background-color: var(--light);
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .dropdown-item {
            color: var(--text-light);
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background-color: var(--primary);
            color: var(--light);
        }

        .main-content {
            margin-top: 80px;
            padding: 20px;
        }

        .stat-card {
            background: var(--light);
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .management-card {
            background: var(--light);
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(0,0,0,0.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .management-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .accent-text {
            color: var(--accent);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            color: var(--light);
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #357abd;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
        }

        .profile-card {
            background: var(--light);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .profile-card:hover {
            transform: translateY(-5px);
        }

        .table-hover tbody tr {
            transition: all 0.2s;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(74, 144, 226, 0.05);
            transform: translateX(10px);
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#" style="color: var(--primary);">
                <i class="fas fa-hotel me-2"></i>Sunrise Resorts
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2 fs-5"></i>
                            <span class="fw-medium"><?= htmlspecialchars($customer['FullName']) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Profile Card Section -->
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-md-4">
                <div class="profile-card">
                    <div class="p-4 text-center">
                        <div class="avatar mb-3">
                            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px; margin: 0 auto;">
                                <span class="text-white fs-3"><?= strtoupper(substr($customer['FullName'], 0, 1)) ?></span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold"><?= htmlspecialchars($customer['FullName']) ?></h4>
                        <p class="text-muted mb-3">Premium Member</p>
                    </div>
                    <div class="profile-details p-4 border-top">
                        <div class="row">
                            <div class="col-6 mb-3">
                                <p class="small text-muted mb-1">Email</p>
                                <p class="mb-0"><?= htmlspecialchars($customer['Email']) ?></p>
                            </div>
                            <div class="col-6 mb-3">
                                <p class="small text-muted mb-1">Phone</p>
                                <p class="mb-0"><?= htmlspecialchars($customer['PhoneNumber']) ?? 'N/A' ?></p>
                            </div>
                            <div class="col-12">
                                <p class="small text-muted mb-1">Member Since</p>
                                <p class="mb-0"><?= date('M Y', strtotime($customer['CreatedAt'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="col-md-8">
                <div class="row h-100">
                    <?php
                    $bookingQuery = "SELECT COUNT(*) AS total_bookings, 
                                    SUM(CASE WHEN BookingStatus = 'Confirmed' THEN 1 ELSE 0 END) AS upcoming_stays
                                    FROM Bookings WHERE UserID = ?";
                    $stmt = $conn->prepare($bookingQuery);
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $stats = $stmt->get_result()->fetch_assoc();
                    ?>
                    <div class="col-md-6 mb-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-card">
                            <i class="fas fa-calendar-check fa-2x text-primary mb-3"></i>
                            <h5 class="fw-medium text-muted">Upcoming Stays</h5>
                            <h2 class="fw-bold text-dark"><?= $stats['upcoming_stays'] ?? 0 ?></h2>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" data-aos="fade-up" data-aos-delay="150">
                        <div class="stat-card">
                            <i class="fas fa-history fa-2x text-primary mb-3"></i>
                            <h5 class="fw-medium text-muted">Completed Stays</h5>
                            <h2 class="fw-bold text-dark"><?= ($stats['total_bookings'] - $stats['upcoming_stays']) ?? 0 ?></h2>
                        </div>
                    </div>
                    <div class="col-md-12" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-card h-100 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas fa-concierge-bell fa-2x text-primary mb-3"></i>
                                <h5 class="fw-medium text-muted">Pending Requests</h5>
                                <h2 class="fw-bold text-dark">2</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-12">
                <h4 class="fw-bold mb-4 text-dark">Quick Actions</h4>
                <div class="row g-4">
                    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="100">
                        <div class="management-card text-center">
                            <div class="icon-wrapper mb-4">
                                <i class="fas fa-bed fa-3x text-primary mb-3"></i>
                            </div>
                            <h5 class="fw-medium mb-2">New Booking</h5>
                            <p class="text-muted small mb-4">Discover our luxurious accommodations</p>
                            <a href="new_booking.php" class="btn btn-primary px-4">Book Now</a>
                        </div>
                    </div>
                    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="150">
                        <div class="management-card text-center">
                            <div class="icon-wrapper mb-4">
                                <i class="fas fa-clipboard-list fa-3x text-primary mb-3"></i>
                            </div>
                            <h5 class="fw-medium mb-2">Reservations</h5>
                            <p class="text-muted small mb-4">Manage your current bookings</p>
                            <a href="reservations.php" class="btn btn-primary px-4">View All</a>
                        </div>
                    </div>
                    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                        <div class="management-card text-center">
                            <div class="icon-wrapper mb-4">
                                <i class="fas fa-concierge-bell fa-3x text-primary mb-3"></i>
                            </div>
                            <h5 class="fw-medium mb-2">Services</h5>
                            <p class="text-muted small mb-4">Request additional services</p>
                            <a href="services.php" class="btn btn-primary px-4">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-3" data-aos="zoom-in" data-aos-delay="250">
                        <div class="management-card text-center">
                            <div class="icon-wrapper mb-4">
                                <i class="fas fa-user-cog fa-3x text-primary mb-3"></i>
                            </div>
                            <h5 class="fw-medium mb-2">Profile</h5>
                            <p class="text-muted small mb-4">Update your information</p>
                            <a href="profile.php" class="btn btn-primary px-4">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings Section -->
        <div class="row" data-aos="fade-up">
            <div class="col-12">
                <div class="stat-card">
                    <h5 class="fw-bold mb-4 text-dark">Recent Bookings</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="text-primary">
                                <tr>
                                    <th>Room</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $bookingQuery = "SELECT b.*, r.RoomNumber FROM Bookings b
                                                JOIN Rooms r ON b.RoomID = r.RoomID
                                                WHERE UserID = ? ORDER BY CreatedAt DESC LIMIT 3";
                                $stmt = $conn->prepare($bookingQuery);
                                $stmt->bind_param("i", $userId);
                                $stmt->execute();
                                $bookings = $stmt->get_result();
                                
                                while($booking = $bookings->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="fw-medium">Room <?= htmlspecialchars($booking['RoomNumber']) ?></td>
                                    <td><?= date('M d, Y', strtotime($booking['CheckInDate'])) ?></td>
                                    <td><?= date('M d, Y', strtotime($booking['CheckOutDate'])) ?></td>
                                    <td>
                                        <span class="badge rounded-pill bg-<?= 
                                            $booking['BookingStatus'] === 'Confirmed' ? 'success' : 
                                            ($booking['BookingStatus'] === 'Pending' ? 'warning' : 'danger') 
                                        ?>">
                                            <?= $booking['BookingStatus'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="booking_details.php?id=<?= $booking['BookingID'] ?>" 
                                           class="btn btn-sm btn-primary rounded-circle">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            easing: 'ease-out-quad'
        });
    </script>
</body>
</html>