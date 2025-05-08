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
$editBookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = $success = '';
$bookingData = [
    'UserID' => '',
    'RoomID' => '',
    'CheckInDate' => '',
    'CheckOutDate' => '',
    'NumberOfGuests' => 1,
    'BookingStatus' => 'Pending'
];

// Fetch users and rooms for dropdowns
$users = $conn->query("SELECT UserID, FullName FROM Users ORDER BY FullName")->fetch_all(MYSQLI_ASSOC);
$rooms = $conn->query("SELECT RoomID, RoomNumber FROM Rooms ORDER BY RoomNumber")->fetch_all(MYSQLI_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id']);
    $roomId = intval($_POST['room_id']);
    $checkIn = $_POST['check_in'];
    $checkOut = $_POST['check_out'];
    $guests = intval($_POST['guests']);
    $status = $_POST['status'];

    try {
        // Validate required fields
        if (empty($userId) || empty($roomId) || empty($checkIn) || empty($checkOut)) {
            throw new Exception("All fields marked with * are required");
        }

        // Validate dates
        if (strtotime($checkOut) <= strtotime($checkIn)) {
            throw new Exception("Check-out date must be after check-in date");
        }

        // Validate guests
        if ($guests < 1 || $guests > 10) {
            throw new Exception("Number of guests must be between 1 and 10");
        }

        if ($action === 'add') {
            // Check room availability
            $stmt = $conn->prepare("SELECT BookingID FROM Bookings 
                                   WHERE RoomID = ? 
                                   AND (
                                       (CheckInDate BETWEEN ? AND ?)
                                       OR (CheckOutDate BETWEEN ? AND ?)
                                       OR (? BETWEEN CheckInDate AND CheckOutDate)
                                   )");
            $stmt->bind_param("isssss", $roomId, $checkIn, $checkOut, $checkIn, $checkOut, $checkIn);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("Room is already booked for selected dates");
            }

            // Create new booking
            $stmt = $conn->prepare("INSERT INTO Bookings 
                                   (UserID, RoomID, CheckInDate, CheckOutDate, NumberOfGuests, BookingStatus)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissis", $userId, $roomId, $checkIn, $checkOut, $guests, $status);
        } else {
            // Update existing booking
            $stmt = $conn->prepare("UPDATE Bookings SET 
                                  UserID = ?,
                                  RoomID = ?,
                                  CheckInDate = ?,
                                  CheckOutDate = ?,
                                  NumberOfGuests = ?,
                                  BookingStatus = ?
                                  WHERE BookingID = ?");
            $stmt->bind_param("iissisi", $userId, $roomId, $checkIn, $checkOut, $guests, $status, $editBookingId);
        }

        if ($stmt->execute()) {
            $success = "Booking " . ($action === 'add' ? 'created' : 'updated') . " successfully!";
            $action = 'add';
            $editBookingId = 0;
            // Reset form data
            $bookingData = [
                'UserID' => '',
                'RoomID' => '',
                'CheckInDate' => '',
                'CheckOutDate' => '',
                'NumberOfGuests' => 1,
                'BookingStatus' => 'Pending'
            ];
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        // Preserve form data on error
        $bookingData = $_POST;
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $bookingId = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("DELETE FROM Bookings WHERE BookingID = ?");
        $stmt->bind_param("i", $bookingId);
        if ($stmt->execute()) {
            $success = "Booking deleted successfully!";
        } else {
            throw new Exception("Error deleting booking: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch booking for editing
if ($action === 'edit' && $editBookingId > 0) {
    $stmt = $conn->prepare("SELECT * FROM Bookings WHERE BookingID = ?");
    $stmt->bind_param("i", $editBookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $bookingData = $result->fetch_assoc();
    } else {
        $error = "Booking not found!";
        $action = 'add';
        $editBookingId = 0;
    }
}

// Pagination and filtering
$results_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $results_per_page;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// Build query
$base_query = "FROM Bookings b
              JOIN Users u ON b.UserID = u.UserID
              JOIN Rooms r ON b.RoomID = r.RoomID
              WHERE 1=1";
$params = [];

if (!empty($search)) {
    $base_query .= " AND (u.FullName LIKE ? OR r.RoomNumber LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($filter_status)) {
    $base_query .= " AND b.BookingStatus = ?";
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
$query = "SELECT b.*, u.FullName, r.RoomNumber $base_query 
          ORDER BY b.CreatedAt DESC 
          LIMIT ? OFFSET ?";
$params[] = $results_per_page;
$params[] = $offset;

$stmt = $conn->prepare($query);
$types = (!empty($params) ? str_repeat('s', count($params)-2) : '') . 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$bookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Admin Dashboard</title>
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
        }
        
        .status-pending { background-color: #6c757d; color: white; }
        .status-confirmed { background-color: #198754; color: white; }
        .status-cancelled { background-color: #dc3545; color: white; }
        .status-completed { background-color: #0d6efd; color: white; }
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

        <!-- Booking Form -->
        <div class="management-card mt-4" data-aos="fade-up">
            <h3 class="mb-4">
                <i class="fas fa-calendar-check"></i> 
                <?= $action === 'add' ? 'Create New Booking' : 'Edit Booking' ?>
            </h3>
            
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Guest *</label>
                        <select class="form-select" name="user_id" required>
                            <option value="">Select Guest</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['UserID'] ?>" 
                                    <?= $bookingData['UserID'] == $user['UserID'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['FullName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Room *</label>
                        <select class="form-select" name="room_id" required>
                            <option value="">Select Room</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?= $room['RoomID'] ?>" 
                                    <?= $bookingData['RoomID'] == $room['RoomID'] ? 'selected' : '' ?>>
                                    Room <?= htmlspecialchars($room['RoomNumber']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Check-in Date *</label>
                        <input type="date" class="form-control" name="check_in" 
                               value="<?= htmlspecialchars($bookingData['CheckInDate']) ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Check-out Date *</label>
                        <input type="date" class="form-control" name="check_out" 
                               value="<?= htmlspecialchars($bookingData['CheckOutDate']) ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Number of Guests *</label>
                        <input type="number" class="form-control" name="guests" 
                               value="<?= htmlspecialchars($bookingData['NumberOfGuests']) ?>" min="1" max="10" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Status *</label>
                        <select class="form-select" name="status" required>
                            <option value="Pending" <?= $bookingData['BookingStatus'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Confirmed" <?= $bookingData['BookingStatus'] === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="Cancelled" <?= $bookingData['BookingStatus'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="Completed" <?= $bookingData['BookingStatus'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-accent btn-lg">
                            <i class="fas fa-save"></i> 
                            <?= $action === 'add' ? 'Create Booking' : 'Update Booking' ?>
                        </button>
                        
                        <?php if ($action === 'edit'): ?>
                            <a href="manage_bookings.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="management-card mt-4" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="fas fa-calendar-alt"></i> All Bookings</h3>
                <div>
                    <a href="manage_bookings.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sync"></i> Reset Filters
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <form method="GET" class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by guest name or room number" 
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
                            <option value="Confirmed" <?= $filter_status === 'Confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="Cancelled" <?= $filter_status === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="Completed" <?= $filter_status === 'Completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Showing <?= count($bookings) ?> of <?= $total_rows ?> results
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

            <!-- Bookings Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Guest</th>
                            <th>Room</th>
                            <th>Dates</th>
                            <th>Guests</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($bookings) > 0): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td>#<?= $booking['BookingID'] ?></td>
                                    <td><?= htmlspecialchars($booking['FullName']) ?></td>
                                    <td>Room <?= htmlspecialchars($booking['RoomNumber']) ?></td>
                                    <td>
                                        <?= date('M j', strtotime($booking['CheckInDate'])) ?> - 
                                        <?= date('M j, Y', strtotime($booking['CheckOutDate'])) ?>
                                    </td>
                                    <td><?= $booking['NumberOfGuests'] ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($booking['BookingStatus']) ?>">
                                            <?= $booking['BookingStatus'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="manage_bookings.php?action=edit&id=<?= $booking['BookingID'] ?>" 
                                           class="btn btn-sm btn-accent">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_bookings.php?action=delete&id=<?= $booking['BookingID'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this booking?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                                    <h5>No bookings found</h5>
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

        // Date validation
        const checkIn = document.querySelector('input[name="check_in"]');
        const checkOut = document.querySelector('input[name="check_out"]');
        
        checkIn.addEventListener('change', () => {
            checkOut.min = checkIn.value;
        });
    </script>
</body>
</html>