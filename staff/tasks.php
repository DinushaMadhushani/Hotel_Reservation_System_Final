<?php
session_start();
require '../config/db.con.php';

// Verify staff access
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Staff') {
    header("Location: ../login.php");
    exit();
}

$staffId = $_SESSION['UserID'];

// Handle status update via AJAX
if (isset($_POST['task_id']) && isset($_POST['status'])) {
    $taskId = intval($_POST['task_id']);
    $newStatus = $_POST['status'];
    
    $stmt = $conn->prepare("
        UPDATE AssignedTasks 
        SET TaskStatus = ? 
        WHERE TaskID = ? AND StaffID = ?
    ");
    $stmt->bind_param("sii", $newStatus, $taskId, $staffId);
    $stmt->execute();
    exit();
}

// Fetch all assigned tasks with details
$stmt = $conn->prepare("
    SELECT 
        t.TaskID,
        t.TaskStatus,
        t.AssignmentDateTime,
        sr.RequestType,
        sr.Description AS request_desc,
        r.RoomNumber,
        u.FullName AS guest_name,
        b.CheckInDate,
        b.CheckOutDate
    FROM AssignedTasks t
    JOIN ServiceRequests sr ON t.RequestID = sr.RequestID
    JOIN Bookings b ON sr.BookingID = b.BookingID
    JOIN Rooms r ON b.RoomID = r.RoomID
    JOIN Users u ON b.UserID = u.UserID
    WHERE t.StaffID = ?
    ORDER BY t.AssignmentDateTime DESC
");
$stmt->bind_param("i", $staffId);
$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .task-card {
            transition: transform 0.3s ease;
            border-left: 4px solid #3498db;
        }
        .task-card:hover {
            transform: translateX(5px);
        }
        .status-selector {
            min-width: 150px;
        }
        .status-Pending { color: #f1c40f; }
        .status-InProgress { color: #2ecc71; }
        .status-Completed { color: #95a5a6; }
        .status-Assigned { color: #3498db; }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fa-solid fa-tachometer-alt me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="tasks.php">
                                <i class="fa-solid fa-tasks me-2"></i>My Tasks
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="schedule.php">
                                <i class="fa-solid fa-calendar-alt me-2"></i>Schedule
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">My Tasks</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                                <i class="fa-solid fa-sync"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Task List -->
                <div class="row">
                    <?php foreach ($tasks as $task): ?>
                        <div class="col-md-6 mb-4" data-aos="fade-up">
                            <div class="card task-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title"><?= htmlspecialchars($task['RequestType']) ?></h5>
                                        <span class="badge status-<?= htmlspecialchars($task['TaskStatus']) ?>">
                                            <?= htmlspecialchars($task['TaskStatus']) ?>
                                        </span>
                                    </div>
                                    <p class="card-text text-muted">
                                        <i class="fa-solid fa-bed me-2"></i>
                                        Room <?= htmlspecialchars($task['RoomNumber']) ?> 
                                        (<?= date('M d', strtotime($task['CheckInDate'])) ?> 
                                        - <?= date('M d', strtotime($task['CheckOutDate'])) ?>)
                                    </p>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="fa-solid fa-user me-2"></i>
                                            <?= htmlspecialchars($task['guest_name']) ?>
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <select class="form-select status-selector" 
                                                data-task="<?= $task['TaskID'] ?>"
                                                data-current="<?= $task['TaskStatus'] ?>">
                                            <option value="Pending" <?= $task['TaskStatus'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="InProgress" <?= $task['TaskStatus'] === 'InProgress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="Completed" <?= $task['TaskStatus'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                        <button class="btn btn-primary ms-3" 
                                                onclick="showDetails('<?= htmlspecialchars($task['request_desc'], ENT_QUOTES) ?>')">
                                            <i class="fa-solid fa-info-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($tasks)): ?>
                        <div class="col-12 text-center">
                            <p class="text-muted">No tasks assigned</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Task Details Modal -->
    <div class="modal fade" id="taskDetailsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="taskDetailsBody">
                    <!-- Details will be populated here -->
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

            // Status change handler
            $('.status-selector').on('change', function() {
                const taskId = $(this).data('task');
                const newStatus = $(this).val();
                const prevStatus = $(this).data('current');
                
                if(confirm(`Change status from ${prevStatus} to ${newStatus}?`)) {
                    $.post('', {
                        task_id: taskId,
                        status: newStatus
                    }, function() {
                        location.reload();
                    });
                } else {
                    $(this).val(prevStatus); // Revert selection
                }
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Show task details in modal
        function showDetails(description) {
            $('#taskDetailsBody').text(description);
            $('#taskDetailsModal').modal('show');
        }
    </script>
</body>
</html>