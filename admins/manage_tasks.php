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

// Handle new task creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->autocommit(FALSE);

        // Validate required fields
        $required = ['BookingID', 'RequestType', 'StaffID'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All required fields must be filled");
            }
        }

        // Create new task
        $stmt = $conn->prepare("INSERT INTO ServiceRequests 
                               (BookingID, RequestType, Description, Status)
                               VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("iss", 
            $_POST['BookingID'], 
            $_POST['RequestType'], 
            $_POST['Description']
        );
        $stmt->execute();
        $requestId = $conn->insert_id;

        $stmt = $conn->prepare("INSERT INTO AssignedTasks 
                               (RequestID, StaffID, TaskStatus)
                               VALUES (?, ?, ?)");
        $taskStatus = $_POST['Status'] ?? 'Pending';
        $stmt->bind_param("iis", 
            $requestId, 
            $_POST['StaffID'], 
            $taskStatus
        );
        $stmt->execute();

        $conn->commit();
        $success = "Task created and assigned successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    try {
        $conn->autocommit(FALSE);
        
        $taskId = intval($_GET['id']);
        $requestId = $conn->query("SELECT RequestID FROM AssignedTasks WHERE TaskID = $taskId")->fetch_row()[0];
        
        $conn->query("DELETE FROM AssignedTasks WHERE TaskID = $taskId");
        $conn->query("DELETE FROM ServiceRequests WHERE RequestID = $requestId");
        
        $conn->commit();
        $success = "Task deleted successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Fetch required data
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

// Fetch tasks with related data
$tasks = $conn->query("
    SELECT at.TaskID, sr.RequestType, r.RoomNumber, u.FullName AS StaffName,
           at.TaskStatus, sr.Description, at.AssignmentDateTime,
           cust.FullName AS CustomerName
    FROM AssignedTasks at
    JOIN ServiceRequests sr ON at.RequestID = sr.RequestID
    JOIN Bookings b ON sr.BookingID = b.BookingID
    JOIN Rooms r ON b.RoomID = r.RoomID
    JOIN Users u ON at.StaffID = u.UserID
    JOIN Users cust ON b.UserID = cust.UserID
    ORDER BY at.AssignmentDateTime DESC
")->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin_manage.css">
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

        .table tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.1);
        }

        .status-badge {
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
            font-size: 0.875em;
        }
        
        .status-pending { background-color: #6c757d; color: white; }
        .status-inprogress { background-color: #0d6efd; color: white; }
        .status-completed { background-color: #198754; color: white; }
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
            <h3 class="mb-4 border-bottom pb-2">
                <i class="fas fa-tasks"></i> Create New Task
            </h3>

            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Booking *</label>
                        <select class="form-select" name="BookingID" required>
                            <option value="">Select Booking</option>
                            <?php foreach ($bookings as $booking): ?>
                                <option value="<?= $booking['BookingID'] ?>">
                                    Room <?= htmlspecialchars($booking['RoomNumber']) ?> - <?= htmlspecialchars($booking['FullName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Request Type *</label>
                        <input type="text" class="form-control" name="RequestType" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="Description" rows="3"></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Assign to Staff *</label>
                        <select class="form-select" name="StaffID" required>
                            <option value="">Select Staff Member</option>
                            <?php foreach ($staff as $member): ?>
                                <option value="<?= $member['UserID'] ?>">
                                    <?= htmlspecialchars($member['FullName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="Status" required>
                            <option value="Pending">Pending</option>
                            <option value="InProgress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-warning btn-lg">
                            <i class="fas fa-save"></i> Create Task
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="management-card">
            <h3 class="mb-4 border-bottom pb-2">
                <i class="fas fa-clipboard-list"></i> Active Tasks
            </h3>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Request Type</th>
                            <th>Room</th>
                            <th>Customer</th>
                            <th>Assigned To</th>
                            <th>Status</th>
                            <th>Assigned On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['RequestType']) ?></td>
                                <td><?= htmlspecialchars($task['RoomNumber']) ?></td>
                                <td><?= htmlspecialchars($task['CustomerName']) ?></td>
                                <td><?= htmlspecialchars($task['StaffName']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($task['TaskStatus']) ?>">
                                        <?= $task['TaskStatus'] ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($task['AssignmentDateTime'])) ?></td>
                                <td>
                                    <a href="edit_task.php?id=<?= $task['TaskID'] ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="manage_task.php?action=delete&id=<?= $task['TaskID'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this task?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to delete this task?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>