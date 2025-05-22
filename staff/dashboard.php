<?php
// Enable error reporting (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../config/db.con.php';

// Validate session and permissions
if (!isset($_SESSION['UserID'], $_SESSION['FullName'], $_SESSION['UserType']) || 
    $_SESSION['UserType'] !== 'Staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// Handle task status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $taskId = filter_input(INPUT_POST, 'task_id', FILTER_SANITIZE_NUMBER_INT);
    $newStatus = filter_input(INPUT_POST, 'new_status', FILTER_SANITIZE_STRING);
    
    $updateStmt = $conn->prepare("UPDATE AssignedTasks SET TaskStatus = ? WHERE TaskID = ? AND StaffID = ?");
    $updateStmt->bind_param("sii", $newStatus, $taskId, $_SESSION['UserID']);
    $updateStmt->execute();
    $updateStmt->close();
}

// Get task statistics
$taskStats = $conn->query("
    SELECT 
        SUM(CASE WHEN TaskStatus = 'Completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN TaskStatus = 'Pending' THEN 1 ELSE 0 END) as pending
    FROM AssignedTasks 
    WHERE StaffID = {$_SESSION['UserID']}
");
$stats = $taskStats->fetch_assoc();
$completedTasks = $stats['completed'] ?? 0;
$pendingTasks = $stats['pending'] ?? 0;

// Get assigned rooms count (CORRECTED)
$assignedRooms = $conn->query("
    SELECT COUNT(DISTINCT b.RoomID) as rooms 
    FROM ServiceRequests sr
    JOIN Bookings b ON sr.BookingID = b.BookingID
    WHERE sr.UserID = {$_SESSION['UserID']}
")->fetch_assoc()['rooms'] ?? 0;

// Get staff schedule
$scheduleQuery = $conn->prepare("
    SELECT ScheduleDate, StartTime, EndTime 
    FROM StaffSchedule 
    WHERE UserID = ? AND ScheduleDate >= CURDATE()
    ORDER BY ScheduleDate ASC
");
$scheduleQuery->bind_param("i", $_SESSION['UserID']);
$scheduleQuery->execute();
$scheduleResult = $scheduleQuery->get_result();

// Get assigned tasks (CORRECTED)
$taskQuery = $conn->prepare("
    SELECT t.TaskID, sr.RequestType, sr.Description, t.TaskStatus, b.RoomID
    FROM AssignedTasks t
    JOIN ServiceRequests sr ON t.RequestID = sr.RequestID
    JOIN Bookings b ON sr.BookingID = b.BookingID
    WHERE t.StaffID = ?
    ORDER BY t.AssignmentDateTime DESC
");
$taskQuery->bind_param("i", $_SESSION['UserID']);
$taskQuery->execute();
$taskResult = $taskQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
        }

        body {
            background-color: var(--secondary);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            padding-top: 70px;
        }

        .navbar {
            background: var(--primary);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            height: 70px;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: var(--light);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: var(--light);
            font-weight: 600;
            border-radius: 16px 16px 0 0 !important;
            padding: 1.5rem;
        }

        .status-badge {
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .status-badge:hover {
            transform: translateY(-2px);
        }

        .status-pending { background: #fee2e2; color: #b91c1c; }
        .status-inprogress { background: #ffedd5; color: #c2410c; }
        .status-completed { background: #dcfce7; color: #15803d; }

        .profile-card {
            position: relative;
            overflow: hidden;
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, var(--primary), var(--accent));
            opacity: 0.1;
            transform: rotate(45deg);
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 3px solid var(--light);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.05);
        }

        .table-hover tbody tr {
            transition: all 0.3s ease;
        }

        .table-hover tbody tr:hover {
            transform: translateX(8px);
            background: var(--secondary);
        }

        .nav-link {
            transition: all 0.3s ease;
            border-radius: 12px;
            padding: 12px 20px;
        }

        .nav-link:hover {
            background: rgba(99, 102, 241, 0.1);
            transform: translateX(5px);
        }

        .dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border: none;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand text-white fw-bold" href="#" data-aos="fade-right">
                <i class="fas fa-hotel me-2"></i>Staff Portal
            </a>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center" 
                            type="button" id="profileDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <span><?= htmlspecialchars($_SESSION['FullName']) ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#profile">
                            <i class="fas fa-user me-2"></i>Profile
                        </a></li>
                        <li><a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-5">
        <div class="row g-4">
            <!-- Main Section -->
            <div class="col-12 p-4">
                <!-- Analytics Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="50">
                        <div class="card h-100 border-start border-primary border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary p-3 rounded-3 me-3">
                                        <i class="fas fa-tasks fa-2x text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1 text-muted">Total Tasks</h5>
                                        <h2 class="mb-0"><?= $completedTasks + $pendingTasks ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="100">
                        <div class="card h-100 border-start border-success border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success p-3 rounded-3 me-3">
                                        <i class="fas fa-check-circle fa-2x text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1 text-muted">Completed</h5>
                                        <h2 class="mb-0"><?= $completedTasks ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="150">
                        <div class="card h-100 border-start border-warning border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning p-3 rounded-3 me-3">
                                        <i class="fas fa-exclamation-circle fa-2x text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1 text-muted">Pending</h5>
                                        <h2 class="mb-0"><?= $pendingTasks ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3" data-aos="fade-up" data-aos-delay="200">
                        <div class="card h-100 border-start border-info border-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info p-3 rounded-3 me-3">
                                        <i class="fas fa-door-open fa-2x text-white"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1 text-muted">Assigned Rooms</h5>
                                        <h2 class="mb-0"><?= $assignedRooms ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile & Schedule Section -->
                <div class="row g-4 mb-4">
                    <!-- Profile Card -->
                    <div class="col-12 col-lg-4" data-aos="zoom-in">
                        <div class="card profile-card">
                            <div class="card-body text-center position-relative">
                                <img src="https://via.placeholder.com/120" alt="Profile" 
                                     class="profile-img rounded-circle mb-3">
                                <h4 class="card-title mb-1"><?= htmlspecialchars($_SESSION['FullName']) ?></h4>
                                <p class="text-muted mb-3">Staff Member</p>
                                <div class="d-flex justify-content-around">
                                    <div class="px-3">
                                        <h5 class="mb-0"><?= $completedTasks ?></h5>
                                        <small class="text-muted">Completed Tasks</small>
                                    </div>
                                    <div class="px-3">
                                        <h5 class="mb-0"><?= $scheduleResult->num_rows ?></h5>
                                        <small class="text-muted">Upcoming Shifts</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Card -->
                    <div class="col-12 col-lg-8" data-aos="fade-left">
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-calendar-alt me-2"></i>Upcoming Schedule
                            </div>
                            <div class="card-body">
                                <?php if ($scheduleResult->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Start Time</th>
                                                    <th>End Time</th>
                                                    <th>Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($schedule = $scheduleResult->fetch_assoc()): ?>
                                                <tr data-aos="fade-right">
                                                    <td><?= date('M j, Y', strtotime($schedule['ScheduleDate'])) ?></td>
                                                    <td><?= date('h:i A', strtotime($schedule['StartTime'])) ?></td>
                                                    <td><?= date('h:i A', strtotime($schedule['EndTime'])) ?></td>
                                                    <td>
                                                        <?php
                                                        $start = new DateTime($schedule['StartTime']);
                                                        $end = new DateTime($schedule['EndTime']);
                                                        echo $start->diff($end)->format('%h h %i m');
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0">No upcoming schedule found</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Management -->
                <div class="card mb-4" data-aos="zoom-in">
                    <div class="card-header">
                        <i class="fas fa-clipboard-list me-2"></i>Task Management
                    </div>
                    <div class="card-body">
                        <?php if ($taskResult->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle task-table">
                                    <thead>
                                        <tr>
                                            <th>Task ID</th>
                                            <th>Request Type</th>
                                            <th>Description</th>
                                            <th>Room</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($task = $taskResult->fetch_assoc()): ?>
                                        <tr data-aos="fade-up">
                                            <td>#<?= $task['TaskID'] ?></td>
                                            <td><?= htmlspecialchars($task['RequestType']) ?></td>
                                            <td><?= htmlspecialchars($task['Description']) ?></td>
                                            <td>Room <?= $task['RoomID'] ?></td>
                                            <td>
                                                <span class="status-badge status-<?= strtolower($task['TaskStatus']) ?>">
                                                    <?= $task['TaskStatus'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="task_id" value="<?= $task['TaskID'] ?>">
                                                    <select name="new_status" 
                                                            class="form-select form-select-sm border-0 shadow-sm" 
                                                            onchange="this.form.submit()">
                                                        <option value="Pending" <?= $task['TaskStatus'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="InProgress" <?= $task['TaskStatus'] === 'InProgress' ? 'selected' : '' ?>>In Progress</option>
                                                        <option value="Completed" <?= $task['TaskStatus'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">No tasks assigned</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            easing: 'ease-in-out-quad',
            offset: 100
        });
    </script>   
</body>
</html>
<?php
$conn->close();
?>            