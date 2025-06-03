<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize variables
$action = isset($_GET['action']) ? $_GET['action'] : 'add';
$editTaskId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = $success = '';
$taskData = [
    'RequestID' => '',
    'StaffID' => '',
    'TaskStatus' => 'Pending'
];

// Fetch service requests and staff
$requests = $conn->query("
    SELECT sr.RequestID, r.RoomNumber, sr.RequestType 
    FROM ServiceRequests sr
    JOIN Bookings b ON sr.BookingID = b.BookingID
    JOIN Rooms r ON b.RoomID = r.RoomID
    WHERE sr.Status = 'Pending'
")->fetch_all(MYSQLI_ASSOC);

$staff = $conn->query("
    SELECT UserID, FullName 
    FROM Users 
    WHERE UserType IN ('Staff', 'Admin')
    ORDER BY FullName
")->fetch_all(MYSQLI_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = intval($_POST['request_id']);
    $staffId = intval($_POST['staff_id']);
    $status = $_POST['status'];

    try {
        if (empty($requestId) || empty($staffId)) {
            throw new Exception("All fields marked with * are required");
        }

        if ($action === 'add') {
            // Assign new task
            $stmt = $conn->prepare("INSERT INTO AssignedTasks (RequestID, StaffID, TaskStatus) 
                                   VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $requestId, $staffId, $status);
            
            // Update service request status
            $updateReq = $conn->prepare("UPDATE ServiceRequests SET Status = 'Assigned' WHERE RequestID = ?");
            $updateReq->bind_param("i", $requestId);
            $updateReq->execute();
        } else {
            // Update existing task
            $stmt = $conn->prepare("UPDATE AssignedTasks SET 
                                  RequestID = ?,
                                  StaffID = ?,
                                  TaskStatus = ?
                                  WHERE TaskID = ?");
            $stmt->bind_param("iisi", $requestId, $staffId, $status, $editTaskId);
        }

        if ($stmt->execute()) {
            $success = "Task " . ($action === 'add' ? 'assigned' : 'updated') . " successfully!";
            $action = 'add';
            $editTaskId = 0;
            $taskData = ['RequestID' => '', 'StaffID' => '', 'TaskStatus' => 'Pending'];
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        $taskData = $_POST;
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $taskId = intval($_GET['id']);
    try {
        $conn->autocommit(FALSE); // Start transaction
        
        // First, get the RequestID for the task
        $getRequestStmt = $conn->prepare("SELECT RequestID FROM AssignedTasks WHERE TaskID = ?");
        $getRequestStmt->bind_param("i", $taskId);
        $getRequestStmt->execute();
        $result = $getRequestStmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Task not found or already deleted.");
        }
        
        $requestId = $result->fetch_row()[0];
        
        // Temporarily disable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=0");
        
        // Delete the task
        $deleteStmt = $conn->prepare("DELETE FROM AssignedTasks WHERE TaskID = ?");
        $deleteStmt->bind_param("i", $taskId);
        if (!$deleteStmt->execute()) {
            throw new Exception("Error deleting task: " . $deleteStmt->error);
        }
        
        // Update the service request status back to 'Pending' instead of deleting it
        $updateReqStmt = $conn->prepare("UPDATE ServiceRequests SET Status = 'Pending' WHERE RequestID = ?");
        $updateReqStmt->bind_param("i", $requestId);
        if (!$updateReqStmt->execute()) {
            throw new Exception("Error updating service request: " . $updateReqStmt->error);
        }
        
        // Re-enable foreign key checks
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        
        $conn->commit();
        $success = "Task deleted successfully and service request is now available for reassignment!";
    } catch (Exception $e) {
        $conn->rollback();
        // Re-enable foreign key checks in case of error
        $conn->query("SET FOREIGN_KEY_CHECKS=1");
        $error = $e->getMessage();
    } finally {
        $conn->autocommit(TRUE); // Reset autocommit mode
    }
}

// Fetch task for editing
if ($action === 'edit' && $editTaskId > 0) {
    $stmt = $conn->prepare("SELECT * FROM AssignedTasks WHERE TaskID = ?");
    $stmt->bind_param("i", $editTaskId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $taskData = $result->fetch_assoc();
    } else {
        $error = "Task not found!";
        $action = 'add';
        $editTaskId = 0;
    }
}

// Pagination and filtering
$results_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $results_per_page;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// Build query
$base_query = "FROM AssignedTasks at
              JOIN ServiceRequests sr ON at.RequestID = sr.RequestID
              JOIN Users u ON at.StaffID = u.UserID
              JOIN Bookings b ON sr.BookingID = b.BookingID
              JOIN Rooms r ON b.RoomID = r.RoomID
              WHERE 1=1";
$params = [];

if (!empty($search)) {
    $base_query .= " AND (u.FullName LIKE ? OR r.RoomNumber LIKE ? OR sr.RequestType LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($filter_status)) {
    $base_query .= " AND at.TaskStatus = ?";
    $params[] = $filter_status;
}

// Get total count
$count_stmt = $conn->prepare("SELECT COUNT(*) AS total $base_query");
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_rows = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $results_per_page);

// Fetch filtered data
$query = "SELECT at.*, sr.RequestType, r.RoomNumber, u.FullName AS StaffName $base_query 
          ORDER BY at.AssignmentDateTime DESC 
          LIMIT ? OFFSET ?";
$params[] = $results_per_page;
$params[] = $offset;

$stmt = $conn->prepare($query);
$types = (!empty($params) ? str_repeat('s', count($params)-2) : '') . 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tasks - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.rawgit.com/michalsnik/aos/2.3.1/dist/aos.css" rel="stylesheet">
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
<body>
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

    <!-- Main Content -->
    <div class="container">
        <!-- Alerts -->
        <?php if ($error): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success mt-4" role="alert">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <!-- Task Form -->
        <div class="management-card mt-4" data-aos="fade-up">
            <h3 class="mb-4">
                <i class="fas fa-tasks"></i> 
                <?= $action === 'add' ? 'Assign New Task' : 'Edit Task' ?>
            </h3>
            
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Service Request *</label>
                        <select class="form-select" name="request_id" required>
                            <option value="">Select Request</option>
                            <?php foreach ($requests as $request): ?>
                                <option value="<?= $request['RequestID'] ?>" 
                                    <?= $taskData['RequestID'] == $request['RequestID'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($request['RequestType']) ?> (Room <?= $request['RoomNumber'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Assign To *</label>
                        <select class="form-select" name="staff_id" required>
                            <option value="">Select Staff</option>
                            <?php foreach ($staff as $member): ?>
                                <option value="<?= $member['UserID'] ?>" 
                                    <?= $taskData['StaffID'] == $member['UserID'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($member['FullName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="status" required>
                            <option value="Pending" <?= $taskData['TaskStatus'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="InProgress" <?= $taskData['TaskStatus'] === 'InProgress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= $taskData['TaskStatus'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-accent btn-lg">
                            <i class="fas fa-save"></i> 
                            <?= $action === 'add' ? 'Assign Task' : 'Update Task' ?>
                        </button>
                        
                        <?php if ($action === 'edit'): ?>
                            <a href="manage_tasks.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tasks Table -->
        <div class="management-card mt-4" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="fas fa-clipboard-list"></i> All Tasks</h3>
                <div>
                    <a href="manage_tasks.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sync"></i> Reset Filters
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <form method="GET" class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search staff, room, or request type" 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET" class="input-group">
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="Pending" <?= $filter_status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="InProgress" <?= $filter_status === 'InProgress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Completed" <?= $filter_status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Showing <?= count($tasks) ?> of <?= $total_rows ?> results
                </span>
                <form method="GET" class="ms-3">
                    <select class="form-select form-select-sm" name="per_page" onchange="this.form.submit()">
                        <option value="10" <?= $results_per_page == 10 ? 'selected' : '' ?>>10 per page</option>
                        <option value="25" <?= $results_per_page == 25 ? 'selected' : '' ?>>25 per page</option>
                        <option value="50" <?= $results_per_page == 50 ? 'selected' : '' ?>>50 per page</option>
                    </select>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status) ?>">
                </form>
            </div>

            <!-- Tasks Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Request Type</th>
                            <th>Room</th>
                            <th>Assigned To</th>
                            <th>Assigned On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($tasks) > 0): ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?= htmlspecialchars($task['RequestType']) ?></td>
                                    <td><?= htmlspecialchars($task['RoomNumber']) ?></td>
                                    <td><?= htmlspecialchars($task['StaffName']) ?></td>
                                    <td><?= date('M d, Y', strtotime($task['AssignmentDateTime'])) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($task['TaskStatus']) ?>">
                                            <?= $task['TaskStatus'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="assign_task.php?action=edit&id=<?= $task['TaskID'] ?>" 
                                           class="btn btn-sm btn-accent">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="assign_task.php?action=delete&id=<?= $task['TaskID'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this task?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-clipboard-check fa-2x text-muted mb-3"></i>
                                    <h5>No tasks found</h5>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                                <a class="page-link" 
                                   href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($filter_status) ?>&per_page=<?= $results_per_page ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.rawgit.com/michalsnik/aos/2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>