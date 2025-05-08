<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete_user':
                $stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ?");
                $stmt->bind_param("i", $_POST['user_id']);
                $stmt->execute();
                break;
            case 'delete_room':
                $stmt = $conn->prepare("DELETE FROM Rooms WHERE RoomID = ?");
                $stmt->bind_param("i", $_POST['room_id']);
                $stmt->execute();
                break;
            case 'delete_staff':
                $stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ? AND UserType = 'Staff'");
                $stmt->bind_param("i", $_POST['user_id']);
                $stmt->execute();
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Admin Dashboard</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --side-bar: rgb(197, 164, 54);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin-top: 80px;
        }
        
        .top-nav {
            background: var(--side-bar);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: white !important;
        }
        
        .brand-logo {
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .user-dropdown .dropdown-toggle {
            color: white !important;
            background: transparent;
            border: none;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-3px);
        }
        
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        
        .status-available { background: #28a745; }
        .status-occupied { background: #dc3545; }
        .status-maintenance { background: #ffc107; }
        
        .dataTables_wrapper {
            padding: 1rem;
            background: white;
            border-radius: 10px;
        }
        
        .btn-primary {
            background-color: var(--accent);
            border: none;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #b8932c;
            transform: translateY(-2px);
        }

        /* Management Cards Styles */
        .manage-section {
            padding: 2rem 0;
        }
        
        .manage-section h2 {
            color: var(--primary);
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 600;
        }
        
        .manage-card {
            border: none;
            border-radius: 15px;
            padding: 2rem;
            margin: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            color: white;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .manage-card i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .manage-card .title {
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .gradient-manage-1 {
            background: linear-gradient(45deg, #1a1a1a, #d4af37);
        }
        
        .gradient-manage-2 {
            background: linear-gradient(45deg, #d4af37, #1a1a1a);
        }
        
        .manage-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .manage-card a {
            color: white;
            text-decoration: none;
        }

        /* Staff Management Specific Styles */
        .staff-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand brand-logo" href="#">
                <i class="fa-solid fa-hotel me-2"></i>Hotel Admin
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'dashboard' ? 'active' : '' ?>" 
                           href="?page=dashboard">
                            <i class="fa-solid fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'users' ? 'active' : '' ?>" 
                           href="?page=users">
                            <i class="fa-solid fa-users me-2"></i>Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'rooms' ? 'active' : '' ?>" 
                           href="?page=rooms">
                            <i class="fa-solid fa-door-closed me-2"></i>Rooms
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'bookings' ? 'active' : '' ?>" 
                           href="?page=bookings">
                            <i class="fa-solid fa-calendar-days me-2"></i>Bookings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($_GET['page'] ?? '') === 'staff' ? 'active' : '' ?>" 
                           href="?page=staff">
                            <i class="fa-solid fa-user-tie me-2"></i>Staff
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown user-dropdown">
                        <a class="dropdown-toggle d-flex align-items-center" href="#" 
                           role="button" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user-shield me-2"></i>
                            <span>Admin</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">
                                <i class="fa-solid fa-user-cog me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid p-4">
        <?php
        $page = $_GET['page'] ?? 'dashboard';
        switch($page):
            case 'dashboard': ?>
                <!-- Dashboard Content -->
                <div class="row g-4">
                    <!-- Stats Cards -->
                    <div class="col-12">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Total Rooms</h5>
                                        <h2 class="fw-bold">
                                            <?= $conn->query("SELECT COUNT(*) FROM Rooms")->fetch_row()[0] ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Monthly Bookings</h5>
                                        <h2 class="fw-bold">
                                            <?= $conn->query("SELECT COUNT(*) FROM Bookings WHERE MONTH(CreatedAt) = MONTH(NOW())")->fetch_row()[0] ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Active Users</h5>
                                        <h2 class="fw-bold">
                                            <?= $conn->query("SELECT COUNT(*) FROM Users WHERE UserType = 'Customer'")->fetch_row()[0] ?>
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Room Status -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Room Status</h5>
                                <div class="row g-4">
                                    <?php
                                    $status_counts = $conn->query("
                                        SELECT AvailabilityStatus, COUNT(*) as count 
                                        FROM Rooms 
                                        GROUP BY AvailabilityStatus
                                    ")->fetch_all(MYSQLI_ASSOC);
                                    foreach($status_counts as $status):
                                    ?>
                                    <div class="col-4 text-center">
                                        <div class="status-dot status-<?= strtolower($status['AvailabilityStatus']) ?> mx-auto mb-2"></div>
                                        <div class="fw-bold"><?= $status['count'] ?></div>
                                        <small><?= $status['AvailabilityStatus'] ?></small>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Recent Bookings</h5>
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>
                                            <?php
                                            $bookings = $conn->query("
                                                SELECT b.BookingID, u.FullName, r.RoomNumber, b.CheckInDate 
                                                FROM Bookings b
                                                JOIN Users u ON b.UserID = u.UserID
                                                JOIN Rooms r ON b.RoomID = r.RoomID
                                                ORDER BY b.CreatedAt DESC 
                                                LIMIT 5
                                            ")->fetch_all(MYSQLI_ASSOC);
                                            foreach($bookings as $booking):
                                            ?>
                                            <tr>
                                                <td class="align-middle">
                                                    <div class="bg-primary text-white rounded-circle p-2 text-center" 
                                                         style="width: 40px; height: 40px">
                                                        <?= substr($booking['FullName'], 0, 1) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-bold"><?= $booking['FullName'] ?></div>
                                                    <small class="text-muted"><?= $booking['RoomNumber'] ?></small>
                                                </td>
                                                <td class="text-end">
                                                    <?= date('M d', strtotime($booking['CheckInDate'])) ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Management Cards Section -->
                    <section class="manage-section mt-5 col-12" id="manage">
                        <h2 data-aos="fade-down">Management Console</h2>
                        <div class="row justify-content-center">
                            <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="100">
                                <a href="?page=users">
                                    <div class="manage-card gradient-manage-1">
                                        <i class="fas fa-users-cog"></i>
                                        <div class="title">Manage Users</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="200">
                                <a href="?page=rooms">
                                    <div class="manage-card gradient-manage-2">
                                        <i class="fas fa-door-open"></i>
                                        <div class="title">Manage Rooms</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="300">
                                <a href="?page=bookings">
                                    <div class="manage-card gradient-manage-1">
                                        <i class="fas fa-calendar-check"></i>
                                        <div class="title">Manage Bookings</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="400">
                                <a href="?page=staff">
                                    <div class="manage-card gradient-manage-2">
                                        <i class="fas fa-user-tie"></i>
                                        <div class="title">Manage Staff</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="500">
                                <a href="?page=services">
                                    <div class="manage-card gradient-manage-1">
                                        <i class="fas fa-concierge-bell"></i>
                                        <div class="title">Manage Services</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="600">
                                <a href="?page=packages">
                                    <div class="manage-card gradient-manage-2">
                                        <i class="fas fa-box-open"></i>
                                        <div class="title">Manage Packages</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="700">
                                <a href="?page=tasks">
                                    <div class="manage-card gradient-manage-1">
                                        <i class="fas fa-tasks"></i>
                                        <div class="title">Manage Tasks</div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 col-sm-6" data-aos="fade-up" data-aos-delay="800">
                                <a href="?page=reports">
                                    <div class="manage-card gradient-manage-2">
                                        <i class="fas fa-chart-bar"></i>
                                        <div class="title">View Reports</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </section>
                </div>
                <?php break;
            
            case 'users': ?>
                <!-- Users Management Content -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">User Management</h5>
                            <a href="add_user.php" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-2"></i>Add User
                            </a>
                        </div>
                        <table id="usersTable" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Phone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $users = $conn->query("SELECT * FROM Users")->fetch_all(MYSQLI_ASSOC);
                                foreach($users as $user):
                                ?>
                                <tr>
                                    <td><?= $user['UserID'] ?></td>
                                    <td><?= $user['FullName'] ?></td>
                                    <td><?= $user['Email'] ?></td>
                                    <td><?= $user['UserType'] ?></td>
                                    <td><?= $user['PhoneNumber'] ?: '-' ?></td>
                                    <td>
                                        <a href="edit_user.php?user_id=<?= $user['UserID'] ?>" 
                                           class="btn btn-sm btn-primary me-2">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="deleteUser(<?= $user['UserID'] ?>)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php break;
            
            case 'rooms': ?>
                <!-- Rooms Management Content -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Room Management</h5>
                            <a href="add_room.php" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-2"></i>Add Room
                            </a>
                        </div>
                        <table id="roomsTable" class="table">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Type</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rooms = $conn->query("SELECT * FROM Rooms")->fetch_all(MYSQLI_ASSOC);
                                foreach($rooms as $room):
                                ?>
                                <tr>
                                    <td><?= $room['RoomNumber'] ?></td>
                                    <td><?= $room['RoomType'] ?></td>
                                    <td>$<?= number_format($room['BasePrice'], 2) ?></td>
                                    <td>
                                        <span class="status-dot status-<?= strtolower($room['AvailabilityStatus']) ?>"></span>
                                        <?= $room['AvailabilityStatus'] ?>
                                    </td>
                                    <td>
                                        <a href="edit_room.php?room_id=<?= $room['RoomID'] ?>" 
                                           class="btn btn-sm btn-warning me-2">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="deleteRoom(<?= $room['RoomID'] ?>)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php break;
            
            case 'bookings': ?>
                <!-- Bookings Management Content -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Booking Management</h5>
                            <a href="add_booking.php" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-2"></i>Add Booking
                            </a>
                        </div>
                        <table id="bookingsTable" class="table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>User</th>
                                    <th>Room</th>
                                    <th>Dates</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <?php
                                $bookings = $conn->query("
                                    SELECT b.BookingID, u.FullName, r.RoomNumber, 
                                           b.CheckInDate, b.CheckOutDate, b.BookingStatus
                                    FROM Bookings b
                                    JOIN Users u ON b.UserID = u.UserID
                                    JOIN Rooms r ON b.RoomID = r.RoomID
                                ")->fetch_all(MYSQLI_ASSOC);
                                foreach($bookings as $booking):
                                ?>
                                <tr>
                                    <td><?= $booking['BookingID'] ?></td>
                                    <td><?= $booking['FullName'] ?></td>
                                    <td><?= $booking['RoomNumber'] ?></td>
                                    <td>
                                        <?= date('M d, Y', strtotime($booking['CheckInDate'])) ?> - 
                                        <?= date('M d, Y', strtotime($booking['CheckOutDate'])) ?>
                                    </td>
                                    <td><?= $booking['BookingStatus'] ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger ms-2">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php break;
            
            case 'staff': ?>
                <!-- Staff Management Content -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Staff Management</h5>
                            <a href="add_staff.php" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-2"></i>Add Staff
                            </a>
                        </div>
                        <table id="staffTable" class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Staff Member</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $staffMembers = $conn->query("
                                    SELECT * FROM Users 
                                    WHERE UserType = 'Staff'
                                ")->fetch_all(MYSQLI_ASSOC);
                                foreach($staffMembers as $staff): 
                                    $initials = substr($staff['FullName'], 0, 2);
                                ?>
                                <tr>
                                    <td><?= $staff['UserID'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="staff-avatar me-3">
                                                <?= strtoupper($initials) ?>
                                            </div>
                                            <div class="fw-bold"><?= $staff['FullName'] ?></div>
                                        </div>
                                    </td>
                                    <td><?= $staff['Email'] ?></td>
                                    <td><?= $staff['PhoneNumber'] ?: '-' ?></td>
                                    <td><?= $staff['LastLogin'] ? date('M d, Y h:i A', strtotime($staff['LastLogin'])) : 'Never' ?></td>
                                    <td>
                                        <a href="edit_staff.php?user_id=<?= $staff['UserID'] ?>" 
                                           class="btn btn-sm btn-primary me-2">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="deleteStaff(<?= $staff['UserID'] ?>)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php break;
            
        endswitch; ?>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            AOS.init({
                duration: 1000,
                once: true
            });
            
            // Initialize DataTables
            $('#usersTable, #roomsTable, #bookingsTable, #staffTable').DataTable({
                responsive: true,
                "order": []
            });

            // Delete functions
            window.deleteUser = function(userId) {
                if(confirm('Are you sure you want to delete this user?')) {
                    $.post('', { 
                        action: 'delete_user', 
                        user_id: userId 
                    }, function() {
                        location.reload();
                    });
                }
            }

            window.deleteRoom = function(roomId) {
                if(confirm('Are you sure you want to delete this room?')) {
                    $.post('', { 
                        action: 'delete_room', 
                        room_id: roomId 
                    }, function() {
                        location.reload();
                    });
                }
            }

            window.deleteStaff = function(userId) {
                if(confirm('Are you sure you want to delete this staff member?')) {
                    $.post('', { 
                        action: 'delete_staff', 
                        user_id: userId 
                    }, function() {
                        location.reload();
                    });
                }
            }
        });
    </script>
</body>
</html>