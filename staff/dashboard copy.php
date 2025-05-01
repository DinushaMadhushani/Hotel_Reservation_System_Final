<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Staff') {
    header("Location: ../login.php");
    exit();
}

$staffId = $_SESSION['UserID'];

// Fetch staff details
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $staffId);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

// Fetch today's schedule
$scheduleStmt = $conn->prepare("
    SELECT * FROM StaffSchedule 
    WHERE UserID = ? AND ScheduleDate = CURDATE()
");
$scheduleStmt->bind_param("i", $staffId);
$scheduleStmt->execute();
$schedule = $scheduleStmt->get_result()->fetch_assoc();

// Fetch assigned tasks
$tasksStmt = $conn->prepare("
    SELECT 
        t.TaskID,
        sr.RequestType,
        r.RoomNumber,
        t.TaskStatus,
        t.AssignmentDateTime
    FROM AssignedTasks t
    JOIN ServiceRequests sr ON t.RequestID = sr.RequestID
    JOIN Bookings b ON sr.BookingID = b.BookingID
    JOIN Rooms r ON b.RoomID = r.RoomID
    WHERE t.StaffID = ?
    ORDER BY t.AssignmentDateTime DESC
");
$tasksStmt->bind_param("i", $staffId);
$tasksStmt->execute();
$tasks = $tasksStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch pending service requests
$requestsStmt = $conn->prepare("
    SELECT 
        sr.RequestID,
        u.FullName,
        r.RoomNumber,
        sr.RequestType,
        sr.Description,
        sr.Status
    FROM ServiceRequests sr
    JOIN Bookings b ON sr.BookingID = b.BookingID
    JOIN Rooms r ON b.RoomID = r.RoomID
    JOIN Users u ON sr.UserID = u.UserID
    WHERE sr.Status IN ('Pending', 'Assigned')
    ORDER BY sr.CreatedAt DESC
");
$requestsStmt->execute();
$requests = $requestsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #2980b9;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            margin-top: 80px;
        }
        
        .top-nav {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
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
        
        .user-dropdown .dropdown-toggle {
            color: white !important;
            background: transparent;
            border: none;
        }
        
        .task-card {
            border-left: 4px solid var(--secondary);
            transition: transform 0.3s ease;
        }
        
        .task-card:hover {
            transform: translateX(5px);
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
        }
        
        .status-Pending { background: #f1c40f; }
        .status-Assigned { background: var(--secondary); }
        .status-InProgress { background: #2ecc71; }
        .status-Completed { background: #95a5a6; }
        
        .service-request {
            background: rgba(52, 152, 219, 0.05);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .service-request:hover {
            background: rgba(52, 152, 219, 0.1);
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <nav class="top-nav navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fa-solid fa-hotel me-2"></i>Staff Dashboard
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fa-solid fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tasks.php">
                            <i class="fa-solid fa-tasks me-2"></i>Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="schedule.php">
                            <i class="fa-solid fa-calendar-alt me-2"></i>Schedule
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown user-dropdown">
                        <a class="dropdown-toggle d-flex align-items-center" href="#" role="button" 
                           data-bs-toggle="dropdown">
                            <div class="me-2 text-end">
                                <div class="fw-bold"><?= htmlspecialchars($staff['FullName']) ?></div>
                                <small>Staff Member</small>
                            </div>
                            <i class="fa-solid fa-user-tie fa-lg"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="fa-solid fa-user-gear me-2"></i>Profile
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
    <div class="container-fluid">
        <div class="row mt-4">
            <!-- Welcome Header -->
            <div class="col-12 mb-4">
                <h3 class="fw-bold">Good <?= date('a') === 'am' ? 'Morning' : 'Afternoon' ?>, <?= htmlspecialchars($staff['FullName']) ?></h3>
                <p class="text-muted"><?= date('l, F jS Y') ?></p>
            </div>

            <!-- Today's Schedule Card -->
            <div class="col-lg-4 mb-4">
                <div class="card h-100" data-aos="fade-up">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-calendar-day me-2"></i>Today's Schedule
                        </h5>
                        <?php if ($schedule): ?>
                            <div class="schedule-item p-3 rounded mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fa-solid fa-clock me-2"></i>
                                        <?= date('g:i A', strtotime($schedule['StartTime'])) ?> 
                                        - 
                                        <?= date('g:i A', strtotime($schedule['EndTime'])) ?>
                                    </div>
                                    <span class="badge bg-primary">Active</span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fa-solid fa-calendar-xmark fa-2x text-muted"></i>
                                <p class="text-muted mt-2">No schedule today</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Assigned Tasks Card -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100" data-aos="fade-up">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-clipboard-list me-2"></i>Assigned Tasks
                        </h5>
                        <?php if (!empty($tasks)): ?>
                            <div class="mt-3">
                                <?php foreach ($tasks as $task): ?>
                                    <div class="task-card p-3 mb-2 rounded">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6><?= htmlspecialchars($task['RequestType']) ?></h6>
                                                <small class="text-muted">
                                                    Room <?= htmlspecialchars($task['RoomNumber']) ?> 
                                                    | <?= date('M d, Y', strtotime($task['AssignmentDateTime'])) ?>
                                                </small>
                                            </div>
                                            <span class="status-badge status-<?= htmlspecialchars($task['TaskStatus']) ?>">
                                                <?= htmlspecialchars($task['TaskStatus']) ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fa-solid fa-check-circle fa-2x text-muted"></i>
                                <p class="text-muted mt-2">No assigned tasks</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Service Requests Card -->
            <div class="col-12 mb-4">
                <div class="card" data-aos="fade-up">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fa-solid fa-bell me-2"></i>Pending Requests
                        </h5>
                        <?php if (!empty($requests)): ?>
                            <div class="row mt-3">
                                <?php foreach ($requests as $request): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="service-request p-3 rounded">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6><?= htmlspecialchars($request['RequestType']) ?></h6>
                                                    <small class="text-muted">
                                                        Room <?= htmlspecialchars($request['RoomNumber']) ?><br>
                                                        <?= htmlspecialchars($request['FullName']) ?>
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="status-badge status-<?= htmlspecialchars($request['Status']) ?> mb-2">
                                                        <?= htmlspecialchars($request['Status']) ?>
                                                    </span>
                                                    <br>
                                                    <a href="handle_request.php?rid=<?= $request['RequestID'] ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fa-solid fa-arrow-right"></i> Handle
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-2x text-muted"></i>
                                <p class="text-muted mt-2">No pending requests</p>
                            </div>
                        <?php endif; ?>
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
            
            // Handle task status updates
            $('.status-update').on('change', function() {
                const taskId = $(this).data('task');
                const newStatus = $(this).val();
                
                $.post('update_task.php', {
                    task_id: taskId,
                    status: newStatus
                }, function(response) {
                    location.reload();
                });
            });
        });
    </script>
</body>
</html>