<?php
session_start();
require '../config/db.con.php';

// Verify staff access
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
        }
        
        .sidebar {
            background: linear-gradient(135deg, #2c3e50, #2980b9);
            color: white;
        }
        
        .task-card {
            border-left: 4px solid #3498db;
            transition: transform 0.3s ease;
        }
        
        .task-card:hover {
            transform: translateX(5px);
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
            border-radius: 4px;
        }
        
        .status-Pending { background: #f1c40f; }
        .status-Assigned { background: #3498db; }
        .status-InProgress { background: #2ecc71; }
        .status-Completed { background: #95a5a6; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar col-md-3 col-lg-2 d-md-block">
            <div class="position-sticky">
                <div class="user-profile text-center p-4">
                    <div class="avatar mb-3">
                        <i class="fa-solid fa-user-tie fa-3x"></i>
                    </div>
                    <h5><?= htmlspecialchars($staff['FullName']) ?></h5>
                    <small class="text-muted">Staff Member</small>
                </div>
                <div class="menu p-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fa-solid fa-tachometer-alt me-3"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="tasks.php">
                                <i class="fa-solid fa-tasks me-3"></i> My Tas7ks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="schedule.php">
                                <i class="fa-solid fa-calendar-alt me-3"></i> Schedule
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../auth/logout.php">
                                <i class="fa-solid fa-sign-out-alt me-3"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="content col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between align-items-center py-4">
                <h3 class="my-0">Good <?= date('a') === 'am' ? 'Morning' : 'Afternoon' ?>, <?= htmlspecialchars($staff['FullName']) ?></h3>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fa-solid fa-clock"></i> <?= date('l, F jS') ?>
                    </div>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="card mb-4" data-aos="fade-up">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fa-solid fa-calendar-day me-2"></i>Today's Schedule
                    </h5>
                    <?php if ($schedule): ?>
                        <div class="schedule-item p-3 rounded">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <i class="fa-solid fa-clock me-2"></i>
                                    <?= date('g:i A', strtotime($schedule['StartTime'])) ?> 
                                    to 
                                    <?= date('g:i A', strtotime($schedule['EndTime'])) ?>
                                </div>
                                <span class="badge bg-primary">Active Shift</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No schedule for today</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Assigned Tasks -->
            <div class="card mb-4" data-aos="fade-up">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fa-solid fa-clipboard-list me-2"></i>Assigned Tasks
                    </h5>
                    <?php if (!empty($tasks)): ?>
                        <?php foreach ($tasks as $task): ?>
                            <div class="task-card p-3 mb-2 rounded">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6><?= htmlspecialchars($task['RequestType']) ?></h6>
                                        <small class="text-muted">
                                            Room <?= htmlspecialchars($task['RoomNumber']) ?> 
                                            | Assigned <?= date('M d, Y', strtotime($task['AssignmentDateTime'])) ?>
                                        </small>
                                    </div>
                                    <span class="status-badge status-<?= htmlspecialchars($task['TaskStatus']) ?>">
                                        <?= htmlspecialchars($task['TaskStatus']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No assigned tasks</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Service Requests -->
            <div class="card mb-4" data-aos="fade-up">
                <div class="card-body">
                    <h5 class="card-title mb-4">
                        <i class="fa-solid fa-bell me-2"></i>Service Requests
                    </h5>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $request): ?>
                            <div class="service-request p-3 mb-2 rounded">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6><?= htmlspecialchars($request['RequestType']) ?></h6>
                                        <small class="text-muted">
                                            Guest: <?= htmlspecialchars($request['FullName']) ?><br>
                                            Room <?= htmlspecialchars($request['RoomNumber']) ?>
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="status-badge status-<?= htmlspecialchars($request['Status']) ?> me-3">
                                            <?= htmlspecialchars($request['Status']) ?>
                                        </span>
                                        <a href="handle_request.php?rid=<?= $request['RequestID'] ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-check"></i> Take Action
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No pending requests</p>
                    <?php endif; ?>
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
                
                // Send AJAX request to update status
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