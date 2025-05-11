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
$editRoomId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = $success = '';
$roomData = [
    'RoomNumber' => '',
    'RoomType' => 'Standard',
    'Description' => '',
    'BasePrice' => '',
    'AvailabilityStatus' => 'Available'
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomNumber = trim($_POST['room_number']);
    $roomType = trim($_POST['room_type']);
    $description = trim($_POST['description']);
    $basePrice = trim($_POST['base_price']);
    $availability = trim($_POST['availability']);

    try {
        // Validate required fields
        if (empty($roomNumber) || empty($roomType) || empty($basePrice)) {
            throw new Exception("All fields marked with * are required");
        }

        // Validate numeric price
        if (!is_numeric($basePrice) || $basePrice < 0) {
            throw new Exception("Base price must be a valid positive number");
        }

        if ($action === 'add') {
            // Check for duplicate room number
            $stmt = $conn->prepare("SELECT RoomID FROM Rooms WHERE RoomNumber = ?");
            $stmt->bind_param("s", $roomNumber);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("Room number already exists");
            }

            // Insert new room
            $stmt = $conn->prepare("INSERT INTO Rooms (RoomNumber, RoomType, Description, BasePrice, AvailabilityStatus) 
                                   VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssds", $roomNumber, $roomType, $description, $basePrice, $availability);
        } else {
            // Update existing room
            $stmt = $conn->prepare("UPDATE Rooms SET 
                                  RoomNumber = ?,
                                  RoomType = ?,
                                  Description = ?,
                                  BasePrice = ?,
                                  AvailabilityStatus = ?
                                  WHERE RoomID = ?");
            $stmt->bind_param("sssdsi", $roomNumber, $roomType, $description, $basePrice, $availability, $editRoomId);
        }

        if ($stmt->execute()) {
            $success = "Room " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
            $action = 'add';
            $editRoomId = 0;
            // Reset form data
            $roomData = [
                'RoomNumber' => '',
                'RoomType' => 'Standard',
                'Description' => '',
                'BasePrice' => '',
                'AvailabilityStatus' => 'Available'
            ];
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        // Preserve form data on error
        $roomData = $_POST;
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $roomId = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("DELETE FROM Rooms WHERE RoomID = ?");
        $stmt->bind_param("i", $roomId);
        if ($stmt->execute()) {
            $success = "Room deleted successfully!";
        } else {
            throw new Exception("Error deleting room: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch room for editing
if ($action === 'edit' && $editRoomId > 0) {
    $stmt = $conn->prepare("SELECT * FROM Rooms WHERE RoomID = ?");
    $stmt->bind_param("i", $editRoomId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $roomData = $result->fetch_assoc();
    } else {
        $error = "Room not found!";
        $action = 'add';
        $editRoomId = 0;
    }
}

// Pagination and filtering
$results_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $results_per_page;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';
$filter_status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// Build query
$base_query = "FROM Rooms WHERE 1=1";
$params = [];

if (!empty($search)) {
    $base_query .= " AND (RoomNumber LIKE ? OR Description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($filter_type)) {
    $base_query .= " AND RoomType = ?";
    $params[] = $filter_type;
}

if (!empty($filter_status)) {
    $base_query .= " AND AvailabilityStatus = ?";
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
$query = "SELECT * $base_query ORDER BY RoomNumber ASC LIMIT ? OFFSET ?";
$params[] = $results_per_page;
$params[] = $offset;

$stmt = $conn->prepare($query);
$types = (!empty($params) ? str_repeat('s', count($params)-2) : '') . 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$rooms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.rawgit.com/michalsnik/aos/2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_manage.css">

    <style>
        .status-available { color: #28a745; }
        .status-occupied { color: #dc3545; }
        .status-maintenance { color: #ffc107; }
    </style>
</head>
<body>
    <!-- Top Navigation (Same as manage_users.php) -->
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

        <!-- Room Form -->
        <div class="management-card mt-4" data-aos="fade-up">
            <h3 class="mb-4">
                <i class="fas fa-door-open"></i> 
                <?= $action === 'add' ? 'Add New Room' : 'Edit Room' ?>
            </h3>
            
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Room Number *</label>
                        <input type="text" class="form-control" name="room_number" 
                               value="<?= htmlspecialchars($roomData['RoomNumber']) ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Room Type *</label>
                        <select class="form-select" name="room_type" required>
                            <option value="Standard" <?= $roomData['RoomType'] === 'Standard' ? 'selected' : '' ?>>Standard</option>
                            <option value="Deluxe" <?= $roomData['RoomType'] === 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                            <option value="Suite" <?= $roomData['RoomType'] === 'Suite' ? 'selected' : '' ?>>Suite</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($roomData['Description']) ?></textarea>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Base Price ($) *</label>
                        <input type="number" class="form-control" name="base_price" 
                               value="<?= htmlspecialchars($roomData['BasePrice']) ?>" step="0.01" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Availability Status *</label>
                        <select class="form-select" name="availability" required>
                            <option value="Available" <?= $roomData['AvailabilityStatus'] === 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Occupied" <?= $roomData['AvailabilityStatus'] === 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                            <option value="Maintenance" <?= $roomData['AvailabilityStatus'] === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-accent btn-lg">
                            <i class="fas fa-save"></i> 
                            <?= $action === 'add' ? 'Add Room' : 'Update Room' ?>
                        </button>
                        
                        <?php if ($action === 'edit'): ?>
                            <a href="manage_rooms.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Rooms Table -->
        <div class="management-card mt-4" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="fas fa-door-closed"></i> All Rooms</h3>
                <div>
                    <a href="manage_rooms.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sync"></i> Reset Filters
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="row g-3 mb-4">
                <div class="col-md-5">
                    <form method="GET" class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search room number or description" value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET" class="input-group">
                        <select class="form-select" name="type" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="Standard" <?= $filter_type === 'Standard' ? 'selected' : '' ?>>Standard</option>
                            <option value="Deluxe" <?= $filter_type === 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                            <option value="Suite" <?= $filter_type === 'Suite' ? 'selected' : '' ?>>Suite</option>
                        </select>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status) ?>">
                    </form>
                </div>
                <div class="col-md-3">
                    <form method="GET" class="input-group">
                        <select class="form-select" name="status" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="Available" <?= $filter_status === 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Occupied" <?= $filter_status === 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                            <option value="Maintenance" <?= $filter_status === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($filter_type) ?>">
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Showing <?= count($rooms) ?> of <?= $total_rows ?> results
                </span>
                <form method="GET" class="ms-3">
                    <select class="form-select form-select-sm" name="per_page" onchange="this.form.submit()">
                        <option value="10" <?= $results_per_page == 10 ? 'selected' : '' ?>>10 per page</option>
                        <option value="25" <?= $results_per_page == 25 ? 'selected' : '' ?>>25 per page</option>
                        <option value="50" <?= $results_per_page == 50 ? 'selected' : '' ?>>50 per page</option>
                    </select>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($filter_type) ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($filter_status) ?>">
                </form>
            </div>

            <!-- Rooms Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Room #</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($rooms) > 0): ?>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td><?= htmlspecialchars($room['RoomNumber']) ?></td>
                                    <td><?= htmlspecialchars($room['RoomType']) ?></td>
                                    <td><?= htmlspecialchars($room['Description']) ?></td>
                                    <td>$<?= number_format($room['BasePrice'], 2) ?></td>
                                    <td>
                                        <span class="status-<?= strtolower($room['AvailabilityStatus']) ?>">
                                            <i class="fas fa-circle me-1"></i>
                                            <?= $room['AvailabilityStatus'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="manage_rooms.php?action=edit&id=<?= $room['RoomID'] ?>" 
                                           class="btn btn-sm btn-accent">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_rooms.php?action=delete&id=<?= $room['RoomID'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this room?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-door-open fa-2x text-muted mb-3"></i>
                                    <h5>No rooms found matching your criteria</h5>
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
                                   href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($filter_type) ?>&status=<?= urlencode($filter_status) ?>&per_page=<?= $results_per_page ?>">
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