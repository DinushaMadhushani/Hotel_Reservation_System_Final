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
$editScheduleId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = $success = '';
$scheduleData = [
    'UserID' => '',
    'ScheduleDate' => date('Y-m-d'),
    'StartTime' => '08:00',
    'EndTime' => '17:00'
];

// Fetch staff members
$staff = $conn->query("SELECT UserID, FullName FROM Users WHERE UserType IN ('Staff', 'Admin') ORDER BY FullName")->fetch_all(MYSQLI_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staffId = intval($_POST['staff_id']);
    $scheduleDate = $_POST['schedule_date'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];

    try {
        // Validate required fields
        if (empty($staffId) || empty($scheduleDate) || empty($startTime) || empty($endTime)) {
            throw new Exception("All fields marked with * are required");
        }

        // Validate time logic
        if (strtotime($endTime) <= strtotime($startTime)) {
            throw new Exception("End time must be after start time");
        }

        // Check for overlapping schedules
        $checkQuery = "SELECT ScheduleID FROM StaffSchedule 
                      WHERE UserID = ? 
                      AND ScheduleDate = ?
                      AND (
                          (StartTime BETWEEN ? AND ?)
                          OR (EndTime BETWEEN ? AND ?)
                          OR (? BETWEEN StartTime AND EndTime)
                      )";
        if ($action === 'edit') {
            $checkQuery .= " AND ScheduleID != ?";
        }

        $stmt = $conn->prepare($checkQuery);
        $params = [$staffId, $scheduleDate, $startTime, $endTime, $startTime, $endTime, $startTime];
        if ($action === 'edit') $params[] = $editScheduleId;
        
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Staff member already has a schedule overlapping with this time");
        }

        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO StaffSchedule 
                                   (UserID, ScheduleDate, StartTime, EndTime)
                                   VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $staffId, $scheduleDate, $startTime, $endTime);
        } else {
            $stmt = $conn->prepare("UPDATE StaffSchedule SET
                                  UserID = ?,
                                  ScheduleDate = ?,
                                  StartTime = ?,
                                  EndTime = ?
                                  WHERE ScheduleID = ?");
            $stmt->bind_param("isssi", $staffId, $scheduleDate, $startTime, $endTime, $editScheduleId);
        }

        if ($stmt->execute()) {
            $success = "Schedule " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
            $action = 'add';
            $editScheduleId = 0;
            $scheduleData = [
                'UserID' => '',
                'ScheduleDate' => date('Y-m-d'),
                'StartTime' => '08:00',
                'EndTime' => '17:00'
            ];
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        $scheduleData = $_POST;
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $scheduleId = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("DELETE FROM StaffSchedule WHERE ScheduleID = ?");
        $stmt->bind_param("i", $scheduleId);
        if ($stmt->execute()) {
            $success = "Schedule deleted successfully!";
        } else {
            throw new Exception("Error deleting schedule: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch schedule for editing
if ($action === 'edit' && $editScheduleId > 0) {
    $stmt = $conn->prepare("SELECT * FROM StaffSchedule WHERE ScheduleID = ?");
    $stmt->bind_param("i", $editScheduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $scheduleData = $result->fetch_assoc();
    } else {
        $error = "Schedule not found!";
        $action = 'add';
        $editScheduleId = 0;
    }
}

// Pagination and filtering
$results_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $results_per_page;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_date = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : '';

// Build query
$base_query = "FROM StaffSchedule ss
              JOIN Users u ON ss.UserID = u.UserID
              WHERE 1=1";
$params = [];

if (!empty($search)) {
    $base_query .= " AND u.FullName LIKE ?";
    $params[] = "%$search%";
}

if (!empty($filter_date)) {
    $base_query .= " AND ss.ScheduleDate = ?";
    $params[] = $filter_date;
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
$query = "SELECT ss.*, u.FullName $base_query 
          ORDER BY ss.ScheduleDate DESC, ss.StartTime ASC
          LIMIT ? OFFSET ?";
$params[] = $results_per_page;
$params[] = $offset;

$stmt = $conn->prepare($query);
$types = (!empty($params) ? str_repeat('s', count($params)-2) : '') . 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$schedules = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff Schedules - Admin Dashboard</title>
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

        .time-slot {
            background-color: rgba(212, 175, 55, 0.1);
            padding: 0.25em 0.5em;
            border-radius: 0.25rem;
            font-size: 0.9em;
        }
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

        <!-- Schedule Form -->
        <div class="management-card mt-4" data-aos="fade-up">
            <h3 class="mb-4">
                <i class="fas fa-calendar-alt"></i> 
                <?= $action === 'add' ? 'Add New Schedule' : 'Edit Schedule' ?>
            </h3>
            
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Staff Member *</label>
                        <select class="form-select" name="staff_id" required>
                            <option value="">Select Staff</option>
                            <?php foreach ($staff as $member): ?>
                                <option value="<?= $member['UserID'] ?>" 
                                    <?= $scheduleData['UserID'] == $member['UserID'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($member['FullName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Schedule Date *</label>
                        <input type="date" class="form-control" name="schedule_date" 
                               value="<?= htmlspecialchars($scheduleData['ScheduleDate']) ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Start Time *</label>
                        <input type="time" class="form-control" name="start_time" 
                               value="<?= htmlspecialchars($scheduleData['StartTime']) ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">End Time *</label>
                        <input type="time" class="form-control" name="end_time" 
                               value="<?= htmlspecialchars($scheduleData['EndTime']) ?>" required>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-accent btn-lg">
                            <i class="fas fa-save"></i> 
                            <?= $action === 'add' ? 'Add Schedule' : 'Update Schedule' ?>
                        </button>
                        
                        <?php if ($action === 'edit'): ?>
                            <a href="manage_staff_schedule.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Schedules Table -->
        <div class="management-card mt-4" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="fas fa-calendar-week"></i> All Schedules</h3>
                <div>
                    <a href="manage_staff_schedule.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sync"></i> Reset Filters
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <form method="GET" class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search staff name" 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <form method="GET" class="input-group">
                        <input type="date" class="form-control" name="date" 
                               value="<?= htmlspecialchars($filter_date) ?>"
                               onchange="this.form.submit()">
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Showing <?= count($schedules) ?> of <?= $total_rows ?> results
                </span>
                <form method="GET" class="ms-3">
                    <select class="form-select form-select-sm" name="per_page" onchange="this.form.submit()">
                        <option value="10" <?= $results_per_page == 10 ? 'selected' : '' ?>>10 per page</option>
                        <option value="25" <?= $results_per_page == 25 ? 'selected' : '' ?>>25 per page</option>
                        <option value="50" <?= $results_per_page == 50 ? 'selected' : '' ?>>50 per page</option>
                    </select>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="date" value="<?= htmlspecialchars($filter_date) ?>">
                </form>
            </div>

            <!-- Schedules Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Staff Member</th>
                            <th>Date</th>
                            <th>Time Slot</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($schedules) > 0): ?>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?= htmlspecialchars($schedule['FullName']) ?></td>
                                    <td><?= date('M j, Y', strtotime($schedule['ScheduleDate'])) ?></td>
                                    <td>
                                        <span class="time-slot">
                                            <?= date('g:i a', strtotime($schedule['StartTime'])) ?> - 
                                            <?= date('g:i a', strtotime($schedule['EndTime'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="manage_staff_schedule.php?action=edit&id=<?= $schedule['ScheduleID'] ?>" 
                                           class="btn btn-sm btn-accent">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_staff_schedule.php?action=delete&id=<?= $schedule['ScheduleID'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this schedule?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                                    <h5>No schedules found</h5>
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
                                   href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($filter_date) ?>&per_page=<?= $results_per_page ?>">
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

        // Time input validation
        document.querySelectorAll('input[type="time"]').forEach(input => {
            input.addEventListener('change', function() {
                const start = document.querySelector('input[name="start_time"]');
                const end = document.querySelector('input[name="end_time"]');
                
                if (start.value && end.value && end.value <= start.value) {
                    end.setCustomValidity('End time must be after start time');
                } else {
                    end.setCustomValidity('');
                }
            });
        });
    </script>
</body>
</html>