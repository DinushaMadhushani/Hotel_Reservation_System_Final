<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php"); // Adjust path as needed
    exit();
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // User management
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'delete_user':
                $stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ?");
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
            --light: #f5f5f5;
            --dark: #121212;
        }
        
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .bg-gradient-purple {
    background: accent  ;
}

/* Sidebar Styling */
.sidebar {
    width: 280px;
    transition: all 0.3s ease-in-out;
}

.sidebar .nav-link {
    padding: 1rem 1.5rem;
    border-radius: 0;
    transition: all 0.3s ease;
}

.sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateX(10px);
}

.sidebar .nav-link.active {
    background-color: rgba(255, 255, 255, 0.15);
    border-right: 4px solid white;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        position: fixed;
        z-index: 1000;
    }
    
    .offcanvas-md {
        max-width: 280px;
    }
    
    .nav-text {
        display: none;
    }
    
    .sidebar-header {
        display: none;
    }
}

/* Add animation to links */
.nav-link {
    animation: fadeInLeft 0.5s ease-out;
}

@keyframes fadeInLeft {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
        
        .content-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 10px 10px 0 0;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            transition: transform 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .dataTables_wrapper {
            padding: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .status-available { background: #198754; }
        .status-occupied { background: #dc3545; }
        .status-maintenance { background: #ffc107; }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                height: auto;
                width: 100%;
            }
            .content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
<nav class="sidebar navbar-dark bg-gradient-purple">
    <!-- Mobile Toggle Button -->
    <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Brand Logo -->
    <div class="sidebar-header text-center py-4">
        <i class="fa-solid fa-hotel fa-3x mb-2 text-white"></i>
        <h4 class="fw-bold text-white">Hotel Admin</h4>
    </div>

    <!-- Navigation Links -->
    <div class="offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu">
        <div class="offcanvas-body d-flex flex-column p-0">
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="?page=dashboard" class="nav-link link-light" aria-current="page">
                        <i class="fa-solid fa-tachometer-alt me-3"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="?page=users" class="nav-link link-light">
                        <i class="fa-solid fa-users me-3"></i>
                        <span class="nav-text">Users</span>
                    </a>
                </li>
                <li>
                    <a href="?page=rooms" class="nav-link link-light">
                        <i class="fa-solid fa-door-open me-3"></i>
                        <span class="nav-text">Rooms</span>
                    </a>
                </li>
                <li>
                    <a href="?page=bookings" class="nav-link link-light">
                        <i class="fa-solid fa-calendar-check me-3"></i>
                        <span class="nav-text">Bookings</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>


        <!-- Main Content -->
        <div class="content col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-user me-2"></i> Admin
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Profile</a></li>
                                <li><a class="dropdown-item" href="../auth/logout.php">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <?php
            $page = $_GET['page'] ?? 'dashboard';
            switch($page):
                case 'dashboard':
            ?>
                <div class="content-header p-4 rounded">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">Dashboard</h3>
                        <div class="d-flex">
                            <div class="me-4">
                                <p class="mb-1">Total Rooms</p>
                                <h4 class="fw-bold"><?php echo $conn->query("SELECT COUNT(*) FROM Rooms")->fetch_row()[0]; ?></h4>
                            </div>
                            <div class="me-4">
                                <p class="mb-1">Bookings This Month</p>
                                <h4 class="fw-bold"><?php echo $conn->query("SELECT COUNT(*) FROM Bookings WHERE MONTH(CreatedAt) = MONTH(NOW())")->fetch_row()[0]; ?></h4>
                            </div>
                            <div>
                                <p class="mb-1">Active Users</p>
                                <h4 class="fw-bold"><?php echo $conn->query("SELECT COUNT(*) FROM Users WHERE UserType = 'Customer'")->fetch_row()[0]; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Room Status</h5>
                                <div class="d-flex justify-content-around">
                                    <?php
                                    $status_counts = $conn->query("
                                        SELECT AvailabilityStatus, COUNT(*) as count 
                                        FROM Rooms 
                                        GROUP BY AvailabilityStatus
                                    ")->fetch_all(MYSQLI_ASSOC);
                                    foreach($status_counts as $status):
                                    ?>
                                    <div class="text-center">
                                        <span class="status-dot status-<?php echo strtolower($status['AvailabilityStatus']); ?>"></span>
                                        <span><?php echo $status['AvailabilityStatus']; ?></span>
                                        <h4><?php echo $status['count']; ?></h4>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                                <td><div class="user-avatar"><?php echo substr($booking['FullName'], 0, 1); ?></div></td>
                                                <td>
                                                    <p class="mb-0"><?php echo $booking['FullName']; ?></p>
                                                    <small class="text-muted"><?php echo $booking['RoomNumber']; ?></small>
                                                </td>
                                                <td><?php echo date('M d', strtotime($booking['CheckInDate'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php break; case 'users': ?>
                <div class="content-header p-4 rounded">
                    <h3 class="mb-0">User Management</h3>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <table id="usersTable" class="table table-striped">
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
                                    <td><?php echo $user['UserID']; ?></td>
                                    <td><?php echo $user['FullName']; ?></td>
                                    <td><?php echo $user['Email']; ?></td>
                                    <td><?php echo $user['UserType']; ?></td>
                                    <td><?php echo $user['PhoneNumber'] ?: '-'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['UserID']; ?>)">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php break; case 'rooms': ?>
                <div class="content-header p-4 rounded">
                    <h3 class="mb-0">Room Management</h3>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <table id="roomsTable" class="table table-striped">
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
                                    <td><?php echo $room['RoomNumber']; ?></td>
                                    <td><?php echo $room['RoomType']; ?></td>
                                    <td>$<?php echo number_format($room['BasePrice'], 2); ?></td>
                                    <td>
                                        <span class="status-dot status-<?php echo strtolower($room['AvailabilityStatus']); ?>"></span>
                                        <?php echo $room['AvailabilityStatus']; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php break; case 'bookings': ?>
                <div class="content-header p-4 rounded">
                    <h3 class="mb-0">Booking Management</h3>
                </div>
                <div class="card mt-4">
                    <div class="card-body">
                        <table id="bookingsTable" class="table table-striped">
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
                                    <td><?php echo $booking['BookingID']; ?></td>
                                    <td><?php echo $booking['FullName']; ?></td>
                                    <td><?php echo $booking['RoomNumber']; ?></td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($booking['CheckInDate'])); ?> 
                                        - 
                                        <?php echo date('M d, Y', strtotime($booking['CheckOutDate'])); ?>
                                    </td>
                                    <td><?php echo $booking['BookingStatus']; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php endswitch; ?>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            AOS.init();
            
            // Initialize DataTables
            $('#usersTable, #roomsTable, #bookingsTable').DataTable({
                responsive: true,
                "order": []
            });

            // Delete user confirmation
            window.deleteUser = function(userId) {
                if(confirm('Are you sure you want to delete this user?')) {
                    $.post('', { action: 'delete_user', user_id: userId }, function() {
                        location.reload();
                    });
                }
            }
        });
    </script>
</body>
</html>