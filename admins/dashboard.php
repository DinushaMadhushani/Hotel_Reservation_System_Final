<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get counts for analytics
$counts = [];
$tables = ['Users', 'Rooms', 'Bookings', 'Packages', 'ServiceRequests'];

foreach ($tables as $table) {
    $sql = "SELECT COUNT(*) FROM $table";
    $result = $conn->query($sql);
    
    if (!$result) {
        die("Query failed: " . $conn->error);
    }
    
    $counts[$table] = $result->fetch_row()[0];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- AOS CSS -->
    <link href="https://cdn.rawgit.com/michalsnik/aos/2.3.1/dist/aos.css" rel="stylesheet">
    
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
            padding-top: 80px;
        }

        .top-nav {
            background: var(--dark);
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-brand {
            color: var(--secondary) !important;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .nav-brand:hover {
            color: var(--accent) !important;
        }

        .nav-link {
            color: var(--secondary) !important;
            margin: 0 1rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--accent) !important;
        }

        .dropdown-menu {
            background-color: var(--primary);
            border: 1px solid var(--accent);
        }

        .dropdown-item {
            color: var(--secondary) !important;
        }

        .dropdown-item:hover {
            background-color: var(--accent);
            color: var(--primary) !important;
        }

        .main-content {
            padding: 2rem;
        }

        .analytics-card {
            transition: transform 0.3s;
            border: none;
            border-radius: 10px;
            background: var(--secondary);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .analytics-card:hover {
            transform: translateY(-5px);
        }

        .management-card {
            background: var(--primary);
            color: var(--secondary);
            transition: all 0.3s ease;
            border: 2px solid var(--accent);
            border-radius: 10px;
            height: 100%;
            cursor: pointer;
        }

        .management-card:hover {
            background: var(--accent);
            color: var(--primary);
            transform: scale(1.03);
        }

        .management-card i {
            transition: transform 0.3s ease;
        }

        .management-card:hover i {
            transform: rotate(-10deg);
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand nav-brand" href="#"><i class="fas fa-hotel"></i> Hotel Admin</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon text-white"><i class="fas fa-bars"></i></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['FullName'] ?? 'Admin') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="./profile_management.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Analytics Section -->
        <h3 class="mb-4" data-aos="fade-right"><i class="fas fa-chart-line text-accent"></i> System Analytics</h3>
        <div class="row mb-5">
            <div class="col-md-3 mb-4" data-aos="zoom-in">
                <div class="card analytics-card">
                    <div class="card-body">
                        <h5 class="text-primary"><i class="fas fa-users"></i> Total Users</h5>
                        <h2 class="text-dark"><?= $counts['Users'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="zoom-in" data-aos-delay="100">
                <div class="card analytics-card">
                    <div class="card-body">
                        <h5 class="text-success"><i class="fas fa-bed"></i> Total Rooms</h5>
                        <h2 class="text-dark"><?= $counts['Rooms'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="zoom-in" data-aos-delay="200">
                <div class="card analytics-card">
                    <div class="card-body">
                        <h5 class="text-info"><i class="fas fa-calendar-check"></i> Bookings</h5>
                        <h2 class="text-dark"><?= $counts['Bookings'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4" data-aos="zoom-in" data-aos-delay="300">
                <div class="card analytics-card">
                    <div class="card-body">
                        <h5 class="text-warning"><i class="fas fa-box"></i> Packages</h5>
                        <h2 class="text-dark"><?= $counts['Packages'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Section -->
        <h3 class="mb-4" data-aos="fade-right"><i class="fas fa-cogs text-accent "></i> Management</h3>
        <div class="row g-4 justify-content-center">
            <div class="col-md-3" data-aos="flip-up">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-users-cog fa-3x mb-3"></i>
                        <h4>Manage Customers</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3" data-aos="flip-up" data-aos-delay="100">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-bed fa-3x mb-3"></i>
                        <h4>Manage Rooms</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3" data-aos="flip-up" data-aos-delay="200">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-user-tie fa-3x mb-3"></i>
                        <h4>Manage Staff</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3" data-aos="flip-up" data-aos-delay="300">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                        <h4>Manage Bookings</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3" data-aos="flip-up" data-aos-delay="400">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <h4>Manage Packages</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3" data-aos="flip-up" data-aos-delay="500">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-tasks fa-3x mb-3"></i>
                        <h4>Manage Tasks</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3" data-aos="flip-up" data-aos-delay="600">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-concierge-bell fa-3x mb-3"></i>
                        <h4>Manage Staff Schedule</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="flip-up" data-aos-delay="600">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                        <h4>Manage Admins</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="flip-up" data-aos-delay="600">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                        <h4>Assign Tasks</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="flip-up" data-aos-delay="600">
                <div class="card management-card">
                    <div class="card-body text-center py-4">
                        <i class="fas fa-user-shield fa-3x mb-3"></i>
                        <h4>User Management</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.rawgit.com/michalsnik/aos/2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Add click handlers for management cards
        document.querySelectorAll('.management-card').forEach(card => {
            card.addEventListener('click', function() {
                const title = this.querySelector('h4').textContent;
                switch(title) {
                    case 'Manage Customers':
                        window.location.href = 'manage_customes.php';
                        break;
                    case 'Manage Rooms':
                        window.location.href = 'manage_rooms.php';
                        break;
                    case 'Manage Staff':
                        window.location.href = 'manage_staff.php';
                        break;
                    case 'Manage Bookings':
                        window.location.href = 'manage_bookings.php';
                        break;
                    case 'Manage Packages':
                        window.location.href = 'manage_packages.php';
                        break;
                    case 'Manage Tasks':
                        window.location.href = 'manage_tasks.php';
                        break;
                    case 'Manage Staff Schedule':
                        window.location.href = 'manage_staff_schedule.php';
                        break;
                    case 'Manage Admins':
                        window.location.href = 'manage_admins.php';
                        break;
                    case 'Assign Tasks':
                        window.location.href = 'assign_task.php';
                        break;
                    case 'User Management':
                        window.location.href = 'manage_users.php';
                        break;
                      
                }
            });
        });
    </script>
</body>
</html>