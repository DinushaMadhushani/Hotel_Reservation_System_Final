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
$imgname = $customer['Email'];
?>

<?php
// Profile Image Handling
$userType = $_SESSION['UserType'];  // From session (Admin/Customer/Staff)
$email = $customer['Email'];        // From database

// Define valid role folders
$roleFolders = [
    'Admin' => '../assets/images/Admin/dp/',
    'Customer' => '../assets/images/Customer/dp/',
    'Staff' => '../assets/images/Staff/dp/'
];

$imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$imageFound = false;

// Set base path based on user type
$basePath = $roleFolders[$userType] ?? '../assets/images/default/';
$basePath = rtrim($basePath, '/') . '/';

// 1. First check for email-based image
foreach ($imageExtensions as $ext) {
    $safeEmail = basename($email); // Prevent directory traversal
    $safeEmail = str_replace(['.', '@'], ['.', '_at_'], $safeEmail);

    $testPath = $basePath . $safeEmail . '.' . $ext;

    // die($testPath);


    if (file_exists($testPath)) {
        $imagePath = $testPath;
        $imageFound = true;
        break;
    }
}

// 2. If not found, check for role-specific default.jpg
if (!$imageFound) {
    $roleDefault = $basePath . 'default.jpg';
    if (file_exists($roleDefault)) {
        $imagePath = $roleDefault;
        $imageFound = true;
    }
}

// 3. Final fallback to global default
if (!$imageFound) {
    $imagePath = '../assets/images/default/default_profile.jpg';
}

// Add cache buster for non-global images
if ($imageFound) {
    $imagePath .= '?v=' . time();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/user_dashboard.css">

</head>

<body>

<?php include '../includes/user_header.php'; ?>
    <!-- Navigation Bar -->
    <!-- <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand accent-text" href="#">
                <i class="fas fa-hotel me-2"></i>Hotel Dashboard
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($customer['FullName']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="./profile_management.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav> -->

    <!-- Main Content -->
    <div class="main-content">
        <!-- Welcome Section -->
        <div class="row mb-4 justify-content-center flex" data-aos="fade-up">
            <div class="col-12 text-center">
                <h3 class="text-dark accent">Welcome Back, <?= htmlspecialchars($customer['FullName']) ?>!</h3>
                <p class="text-dark">Your travel experience matters to us</p>
            </div>
        </div>

        <!-- Statistics Section -->
        <!-- Profile Card Section -->
        <div class="row mb-4" data-aos="fade-up">
            <div class="col-md-4">
                <div class="profile-card">
                    <div class="p-4 text-center">
                        <div class="avatar mb-3">
                            <img src="<?= $imagePath ?>"
                                alt="<?= htmlspecialchars($customer['FullName']) ?> Profile"
                                class="rounded-circle shadow-sm"
                                style="width: 200px; height: 200px; object-fit: cover;">
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
                        <div class="stat-card d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas fa-calendar-check fa-2x text-warning mb-3"></i>
                                <h5 class="fw-medium text-white">Upcoming Stays</h5>
                                <h2 class="fw-bold text-white"><?= $stats['upcoming_stays'] ?? 0 ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3" data-aos="fade-up" data-aos-delay="150">
                        <div class="stat-card d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas fa-history fa-2x text-warning mb-3"></i>
                                <h5 class="fw-medium text-white">Completed Stays</h5>
                                <h2 class="fw-bold text-white"><?= ($stats['total_bookings'] - $stats['upcoming_stays']) ?? 0 ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-card h-100 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas fa-concierge-bell fa-2x text-warning mb-3"></i>
                                <h5 class="fw-medium text-white">Pending Requests</h5>
                                <h2 class="fw-bold text-white">2</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Section -->
        <div class="row" data-aos="fade-up">
            <div class="col-md-12 mb-4 text-center">
                <h4 class="mb-4 accent-text">Quick Actions</h4>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="management-card">
                            <i class="fas fa-bed fa-3x accent-text mb-3"></i>
                            <h5>New Booking</h5>
                            <p class="text-white">Book a new stay with us</p>
                            <a href="checkout.php" class="btn btn-primary">Book Now</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="management-card">
                            <i class="fas fa-clipboard-list fa-3x accent-text mb-3"></i>
                            <h5>Current Reservations</h5>
                            <p class="text-white">Manage your bookings</p>
                            <a href="manage_bookings.php" class="btn btn-primary">View</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="management-card">
                            <i class="fas fa-concierge-bell fa-3x accent-text mb-3"></i>
                            <h5>Service Requests</h5>
                            <p class="text-white">Manage your services</p>
                            <a href="manage_services.php" class="btn btn-primary">Manage</a>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="management-card">
                            <i class="fas fa-user-cog fa-3x accent-text mb-3"></i>
                            <h5>Profile Settings</h5>
                            <p class="text-white">Update your information</p>
                            <a href="profile_management.php" class="btn btn-primary">Update</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Bookings Section -->
        <div class="row mt-4" data-aos="fade-up">
            <div class="col-12">
                <div class="card" style="background: var(--primary); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="card-body">
                        <h5 class="card-title text-white text-center my-3">Recent Bookings</h5>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover text-center">
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Check-in</th>
                                        <th>Check-out</th>
                                        <th>Status</th>
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

                                    while ($booking = $bookings->fetch_assoc()):
                                    ?>
                                    <tr>
                                        <td>Room <?= htmlspecialchars($booking['RoomNumber']) ?></td>
                                        <td><?= date('M d, Y', strtotime($booking['CheckInDate'])) ?></td>
                                        <td><?= date('M d, Y', strtotime($booking['CheckOutDate'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $booking['BookingStatus'] === 'Confirmed' ? 'success' : 
                                                ($booking['BookingStatus'] === 'Pending' ? 'warning' : 'danger') 
                                            ?>">
                                                <?= $booking['BookingStatus'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                        <tr>
                                            <td>Room <?= htmlspecialchars($booking['RoomNumber']) ?></td>
                                            <td><?= date('M d, Y', strtotime($booking['CheckInDate'])) ?></td>
                                            <td><?= date('M d, Y', strtotime($booking['CheckOutDate'])) ?></td>
                                            <td>
                                                <span class="badge bg-<?=
                                                                        $booking['BookingStatus'] === 'Confirmed' ? 'success' : ($booking['BookingStatus'] === 'Pending' ? 'warning' : 'danger')
                                                                        ?>">
                                                    <?= $booking['BookingStatus'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="booking_details.php?id=<?= $booking['BookingID'] ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
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
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>

</html>