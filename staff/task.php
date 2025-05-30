<?php
session_start();
require '../config/db.con.php';

// Session validation and security checks
if (!isset($_SESSION['UserID'], $_SESSION['FullName'], $_SESSION['UserType']) || 
    $_SESSION['UserType'] !== 'Staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize message system
$message = '';
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_message'] = '<div class="alert error">Security token mismatch!</div>';
        header("Location: task.php");
        exit();
    }

    if (isset($_POST['task_id'], $_POST['new_status'])) {
        $taskId = (int)$_POST['task_id'];
        $newStatus = $_POST['new_status'];
        
        // Validate inputs
        $allowedStatus = ['Pending', 'InProgress', 'Completed'];
        if (!in_array($newStatus, $allowedStatus)) {
            $_SESSION['flash_message'] = '<div class="alert error">Invalid status value provided!</div>';
            header("Location: task.php");
            exit();
        }

        // Update task status with ownership check
        $stmt = $conn->prepare("
            UPDATE AssignedTasks 
            SET TaskStatus = ? 
            WHERE TaskID = ? AND StaffID = ?
        ");
        $stmt->bind_param("sii", $newStatus, $taskId, $_SESSION['UserID']);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = '<div class="alert success">Task status updated successfully!</div>';
        } else {
            $_SESSION['flash_message'] = '<div class="alert error">Database error: ' . $conn->error . '</div>';
        }
        header("Location: task.php");
        exit();
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch tasks with error handling
try {
    $stmt = $conn->prepare("
        SELECT 
            t.TaskID, 
            sr.RequestType, 
            sr.Description AS RequestDescription,
            t.TaskStatus, 
            t.AssignmentDateTime,
            b.BookingID,
            r.RoomNumber
        FROM AssignedTasks t
        JOIN ServiceRequests sr ON t.RequestID = sr.RequestID
        JOIN Bookings b ON sr.BookingID = b.BookingID
        JOIN Rooms r ON b.RoomID = r.RoomID
        WHERE t.StaffID = ?
        ORDER BY t.AssignmentDateTime DESC
    ");
    $stmt->bind_param("i", $_SESSION['UserID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $_SESSION['flash_message'] = '<div class="alert error">Error loading tasks: ' . $e->getMessage() . '</div>';
    header("Location: task.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - Staff Dashboard</title>
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f0f2f5;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            margin-top: 60px;
        }

        .task-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            margin: 20px 0;
        }

        .task-table th, 
        .task-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .task-table thead {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
        }

        .status-select {
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            min-width: 120px;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            position: relative;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .task-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-pending { background-color: #ffeeba; color: #856404; }
        .status-inprogress { background-color: #b8daff; color: #004085; }
        .status-completed { background-color: #c3e6cb; color: #155724; }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
                margin-top: 40px;
            }
            
            .task-table {
                display: block;
                overflow-x: auto;
            }
            
            .btn {
                width: 100%;
                margin-top: 8px;
            }
            
            .status-select {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include('../includes/staff_header.php'); ?>

    <div class="container">
        
        
        <h2 class="text-center">My Assigned Tasks</h2>
        
        <?php if (!empty($tasks)): ?>
            <table class="task-table">
                <thead>
                    <tr>
                        <th>Task ID</th>
                        <th>Request Type</th>
                        <th>Description</th>
                        <th>Room</th>
                        <th>Assigned</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['TaskID']) ?></td>
                            <td><?= htmlspecialchars($task['RequestType']) ?></td>
                            <td><?= htmlspecialchars($task['RequestDescription']) ?></td>
                            <td><?= htmlspecialchars($task['RoomNumber']) ?></td>
                            <td><?= date('M j, Y H:i', strtotime($task['AssignmentDateTime'])) ?></td>
                            <td>
                                <span class="task-status status-<?= strtolower($task['TaskStatus']) ?>">
                                    <?= $task['TaskStatus'] ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="task_id" value="<?= $task['TaskID'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <select name="new_status" class="status-select">
                                        <option value="Pending" <?= $task['TaskStatus'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="InProgress" <?= $task['TaskStatus'] === 'InProgress' ? 'selected' : '' ?>>In Progress</option>
                                        <option value="Completed" <?= $task['TaskStatus'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert info">No tasks assigned to you at this time.</div>
        <?php endif; ?>
    </div>
</body>
</html>