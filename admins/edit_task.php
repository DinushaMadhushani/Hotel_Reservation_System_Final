<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize variables
$error = $success = '';
$taskId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if task exists
if ($taskId <= 0) {
    header("Location: manage_tasks.php");
    exit();
}

// Fetch task data
try {
    $stmt = $conn->prepare("SELECT at.*, sr.RequestType, sr.Description, sr.BookingID, sr.Status as RequestStatus
                           FROM AssignedTasks at
                           JOIN ServiceRequests sr ON at.RequestID = sr.RequestID
                           WHERE at.TaskID = ?");
    $stmt->bind_param("i", $taskId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: manage_tasks.php?error=" . urlencode("Task not found"));
        exit();
    }
    
    $taskData = $result->fetch_assoc();
} catch (Exception $e) {
    header("Location: manage_tasks.php?error=" . urlencode($e->getMessage()));
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->autocommit(FALSE);
        
        // Validate required fields
        $required = ['BookingID', 'RequestType', 'StaffID', 'TaskStatus'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All required fields must be filled");
            }
        }
        
        // Update service request
        $stmt = $conn->prepare("UPDATE ServiceRequests 
                               SET BookingID = ?, RequestType = ?, Description = ?, Status = ?
                               WHERE RequestID = ?");
        $requestStatus = ($_POST['TaskStatus'] === 'Completed') ? 'Completed' : 'Assigned';
        $stmt->bind_param("isssi", 
            $_POST['BookingID'], 
            $_POST['RequestType'], 
            $_POST['Description'],
            $requestStatus,
            $taskData['RequestID']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating service request: " . $stmt->error);
        }
        
        // Update assigned task
        $stmt = $conn->prepare("UPDATE AssignedTasks 
                               SET StaffID = ?, TaskStatus = ?
                               WHERE TaskID = ?");
        $stmt->bind_param("isi", 
            $_POST['StaffID'], 
            $_POST['TaskStatus'],
            $taskId
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating task assignment: " . $stmt->error);
        }
        
        $conn->commit();
        $success = "Task updated successfully!";
        
        // Refresh task data
        $stmt = $conn->prepare("SELECT at.*, sr.RequestType, sr.Description, sr.BookingID, sr.Status as RequestStatus
                               FROM AssignedTasks at
                               JOIN ServiceRequests sr ON at.RequestID = sr.RequestID
                               WHERE at.TaskID = ?");
        $stmt->bind_param("i", $taskId);
        $stmt->execute();
        $taskData = $stmt->get_result()->fetch_assoc();
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Fetch required data for dropdowns
$bookings = $conn->query("
    SELECT b.BookingID, r.RoomNumber, u.FullName 
    FROM Bookings b
    JOIN Rooms r ON b.RoomID = r.RoomID
    JOIN Users u ON b.UserID = u.UserID
    ORDER BY b.CheckInDate DESC
")->fetch_all(MYSQLI_ASSOC);

$staff = $conn->query("
    SELECT UserID, FullName 
    FROM Users 
    WHERE UserType = 'Staff'
    ORDER BY FullName
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin_manage.css">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }

        .management-card {
            background: var(--secondary);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Top Navigation -->
    <nav class="top-nav navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand nav-brand" href="dashboard.php"><i class="fas fa-hotel"></i> Hotel Admin</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['FullName']) ?>
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

    <div class="container py-4">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="management-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="fas fa-edit"></i> Edit Task</h3>
                <a href="manage_tasks.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Tasks
                </a>
            </div>

            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Booking *</label>
                        <select class="form-select" name="BookingID" required>
                            <option value="">Select Booking</option>
                            <?php foreach ($bookings as $booking): ?>
                                <option value="<?= $booking['BookingID'] ?>" <?= ($taskData['BookingID'] == $booking['BookingID']) ? 'selected' : '' ?>>
                                    Room <?= htmlspecialchars($booking['RoomNumber']) ?> - <?= htmlspecialchars($booking['FullName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Request Type *</label>
                        <input type="text" class="form-control" name="RequestType" value="<?= htmlspecialchars($taskData['RequestType']) ?>" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="Description" rows="3"><?= htmlspecialchars($taskData['Description']) ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Assign to Staff *</label>
                        <select class="form-select" name="StaffID" required>
                            <option value="">Select Staff Member</option>
                            <?php foreach ($staff as $member): ?>
                                <option value="<?= $member['UserID'] ?>" <?= ($taskData['StaffID'] == $member['UserID']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($member['FullName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="TaskStatus" required>
                            <option value="Pending" <?= ($taskData['TaskStatus'] === 'Pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="InProgress" <?= ($taskData['TaskStatus'] === 'InProgress') ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= ($taskData['TaskStatus'] === 'Completed') ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save"></i> Update Task
                        </button>
                        <a href="manage_tasks.php" class="btn btn-secondary btn-lg ms-2">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.form-select, .form-control').forEach(el => {
            el.addEventListener('focus', () => {
                el.classList.add('shadow');
            });
            el.addEventListener('blur', () => {
                el.classList.remove('shadow');
            });
        });
    </script>
</body>
</html>