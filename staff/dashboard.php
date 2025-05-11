<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION['user_type'] != 'Staff') {
    header("Location: ../unauthorized.php");
    exit();
}

// Handle task status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $taskId = $conn->real_escape_string($_POST['task_id']);
    $newStatus = $conn->real_escape_string($_POST['new_status']);
    
    $updateStmt = $conn->prepare("UPDATE AssignedTasks SET TaskStatus = ? WHERE TaskID = ? AND StaffID = ?");
    $updateStmt->bind_param("sii", $newStatus, $taskId, $_SESSION['user_id']);
    $updateStmt->execute();
    $updateStmt->close();
}

// Get staff schedule
$scheduleQuery = $conn->prepare("
    SELECT ScheduleDate, StartTime, EndTime 
    FROM StaffSchedule 
    WHERE UserID = ? AND ScheduleDate >= CURDATE()
    ORDER BY ScheduleDate ASC
");
$scheduleQuery->bind_param("i", $_SESSION['user_id']);
$scheduleQuery->execute();
$scheduleResult = $scheduleQuery->get_result();

// Get assigned tasks
$taskQuery = $conn->prepare("
    SELECT t.TaskID, sr.RequestType, sr.Description, t.TaskStatus, b.RoomID
    FROM AssignedTasks t
    JOIN ServiceRequests sr ON t.RequestID = sr.RequestID
    JOIN Bookings b ON sr.BookingID = b.BookingID
    WHERE t.StaffID = ?
    ORDER BY t.AssignmentDateTime DESC
");
$taskQuery->bind_param("i", $_SESSION['user_id']);
$taskQuery->execute();
$taskResult = $taskQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card { margin-bottom: 20px; }
        table { margin-top: 15px; }
        .status-pending { color: #dc3545; }
        .status-inprogress { color: #ffc107; }
        .status-completed { color: #28a745; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Staff Dashboard - Welcome <?php echo htmlspecialchars($_SESSION['full_name']); ?></h2>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>

        <!-- Schedule Section -->
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Your Schedule</h4>
            </div>
            <div class="card-body">
                <?php if ($scheduleResult->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($schedule = $scheduleResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($schedule['ScheduleDate']); ?></td>
                                        <td><?php echo date("g:i A", strtotime($schedule['StartTime'])); ?></td>
                                        <td><?php echo date("g:i A", strtotime($schedule['EndTime'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No upcoming schedule found.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tasks Section -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">Assigned Tasks</h4>
            </div>
            <div class="card-body">
                <?php if ($taskResult->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Room</th>
                                    <th>Request Type</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($task = $taskResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($task['RoomID']); ?></td>
                                        <td><?php echo htmlspecialchars($task['RequestType']); ?></td>
                                        <td><?php echo htmlspecialchars($task['Description']); ?></td>
                                        <td class="status-<?php echo strtolower($task['TaskStatus']); ?>">
                                            <?php echo htmlspecialchars($task['TaskStatus']); ?>
                                        </td>
                                        <td>
                                            <form method="POST" class="d-flex gap-2">
                                                <input type="hidden" name="task_id" value="<?php echo $task['TaskID']; ?>">
                                                <select name="new_status" class="form-select form-select-sm">
                                                    <option value="Pending" <?php echo ($task['TaskStatus'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="InProgress" <?php echo ($task['TaskStatus'] == 'InProgress') ? 'selected' : ''; ?>>In Progress</option>
                                                    <option value="Completed" <?php echo ($task['TaskStatus'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                </select>
                                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-arrow-clockwise"></i> Update
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No tasks assigned currently.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
