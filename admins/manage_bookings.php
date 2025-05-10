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
    'BookingStatus' => 'Pending',
    'Packages' => []
];

// Fetch data for dropdowns
$customers = $conn->query("SELECT UserID, FullName FROM Users WHERE UserType = 'Customer' ORDER BY FullName")->fetch_all(MYSQLI_ASSOC);
$rooms = $conn->query("SELECT RoomID, RoomNumber, RoomType, BasePrice FROM Rooms ORDER BY RoomNumber")->fetch_all(MYSQLI_ASSOC);
$packages = $conn->query("SELECT PackageID, PackageName, Price FROM Packages ORDER BY PackageName")->fetch_all(MYSQLI_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id']);
    $roomId = intval($_POST['room_id']);
    $checkIn = $_POST['check_in'];
    $checkOut = $_POST['check_out'];
    $guests = intval($_POST['guests']);
    $status = $_POST['status'];
    $selectedPackages = isset($_POST['packages']) ? $_POST['packages'] : [];

    try {
        // Validate required fields
        if (empty($userId) || empty($roomId) || empty($checkIn) || empty($checkOut)) {
            throw new Exception("All fields marked with * are required");
        }

        // Verify selected user is a customer
        $stmt = $conn->prepare("SELECT UserID FROM Users WHERE UserID = ? AND UserType = 'Customer'");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception("Selected user is not a customer");
        }

        // Validate dates
        if (strtotime($checkOut) <= strtotime($checkIn)) {
            throw new Exception("Check-out date must be after check-in date");
        }

        // Validate guests
        if ($guests < 1 || $guests > 10) {
            throw new Exception("Number of guests must be between 1 and 10");
        }

        // Validate packages
        $validPackageIds = array_column($packages, 'PackageID');
        foreach ($selectedPackages as $packageId) {
            if (!in_array($packageId, $validPackageIds)) {
                throw new Exception("Invalid package selected");
            }
        }

        $conn->autocommit(FALSE);

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
            $stmt->execute();
            $bookingId = $conn->insert_id;
        } else {
            $bookingId = $editBookingId;
            // Update existing booking
            $stmt = $conn->prepare("UPDATE Bookings SET 
                                  UserID = ?,
                                  RoomID = ?,
                                  CheckInDate = ?,
                                  CheckOutDate = ?,
                                  NumberOfGuests = ?,
                                  BookingStatus = ?
                                  WHERE BookingID = ?");
            $stmt->bind_param("iissisi", $userId, $roomId, $checkIn, $checkOut, $guests, $status, $bookingId);
            $stmt->execute();
        }

        // Handle packages
        if ($action === 'edit') {
            $conn->query("DELETE FROM BookingPackages WHERE BookingID = $bookingId");
        }

        if (!empty($selectedPackages)) {
            $stmt = $conn->prepare("INSERT INTO BookingPackages (BookingID, PackageID) VALUES (?, ?)");
            foreach ($selectedPackages as $packageId) {
                $stmt->bind_param("ii", $bookingId, $packageId);
                $stmt->execute();
            }
        }

        $conn->commit();
        $success = "Booking " . ($action === 'add' ? 'created' : 'updated') . " successfully!";
        $action = 'add';
        $editBookingId = 0;
        $bookingData = [
            'UserID' => '',
            'RoomID' => '',
            'CheckInDate' => '',
            'CheckOutDate' => '',
            'NumberOfGuests' => 1,
            'BookingStatus' => 'Pending',
            'Packages' => []
        ];
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
        $bookingData = $_POST;
        $bookingData['Packages'] = $selectedPackages;
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $bookingId = intval($_GET['id']);
    try {
        $conn->autocommit(FALSE);
        $conn->query("DELETE FROM BookingPackages WHERE BookingID = $bookingId");
        $conn->query("DELETE FROM Bookings WHERE BookingID = $bookingId");
        $conn->commit();
        $success = "Booking deleted successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Fetch booking for editing
if ($action === 'edit' && $editBookingId > 0) {
    $stmt = $conn->prepare("SELECT b.*, GROUP_CONCAT(bp.PackageID) AS Packages 
                           FROM Bookings b
                           LEFT JOIN BookingPackages bp ON b.BookingID = bp.BookingID
                           WHERE b.BookingID = ?
                           GROUP BY b.BookingID");
    $stmt->bind_param("i", $editBookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $bookingData = $result->fetch_assoc();
        $bookingData['Packages'] = $bookingData['Packages'] ? explode(',', $bookingData['Packages']) : [];
        
        // Verify customer
        $userCheck = $conn->prepare("SELECT UserID FROM Users WHERE UserID = ? AND UserType = 'Customer'");
        $userCheck->bind_param("i", $bookingData['UserID']);
        $userCheck->execute();
        if ($userCheck->get_result()->num_rows === 0) {
            $error = "Booked user is not a customer!";
            $action = 'add';
            $editBookingId = 0;
            $bookingData = [
                'UserID' => '',
                'RoomID' => '',
                'CheckInDate' => '',
                'CheckOutDate' => '',
                'NumberOfGuests' => 1,
                'BookingStatus' => 'Pending',
                'Packages' => []
            ];
        }
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
              LEFT JOIN BookingPackages bp ON b.BookingID = bp.BookingID
              LEFT JOIN Packages p ON bp.PackageID = p.PackageID
              WHERE u.UserType = 'Customer'";
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
$count_stmt = $conn->prepare("SELECT COUNT(DISTINCT b.BookingID) AS total $base_query");
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_rows = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $results_per_page);

// Fetch filtered data
$query = "SELECT b.*, u.FullName, r.RoomNumber, r.RoomType, r.BasePrice, 
                 GROUP_CONCAT(p.PackageName) AS PackageNames $base_query 
          GROUP BY b.BookingID
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
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php"><i class="fas fa-hotel"></i> Hotel Admin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
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

    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-danger mt-4"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success mt-4"><?= $success ?></div>
        <?php endif; ?>

        <div class="management-card mt-4">
            <h3 class="mb-4">
                <i class="fas fa-calendar-check"></i> 
                <?= $action === 'add' ? 'Create New Booking' : 'Edit Booking' ?>
            </h3>
            
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Customer *</label>
                        <select class="form-select" name="user_id" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer['UserID'] ?>" 
                                    <?= $bookingData['UserID'] == $customer['UserID'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($customer['FullName']) ?>
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
                                    data-type="<?= htmlspecialchars($room['RoomType']) ?>"
                                    data-price="<?= $room['BasePrice'] ?>"
                                    <?= $bookingData['RoomID'] == $room['RoomID'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($room['RoomNumber']) ?> - 
                                    <?= htmlspecialchars($room['RoomType']) ?> ($<?= $room['BasePrice'] ?>/night)
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

                    <div class="col-12">
                        <label class="form-label">Packages</label>
                        <select class="form-select" name="packages[]" multiple>
                            <?php foreach ($packages as $package): ?>
                                <option value="<?= $package['PackageID'] ?>"
                                    <?= in_array($package['PackageID'], $bookingData['Packages']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($package['PackageName']) ?> - $<?= $package['Price'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Hold CTRL/CMD to select multiple packages</small>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-warning btn-lg">
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

        <div class="management-card mt-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="fas fa-calendar-alt"></i> All Bookings</h3>
                <div>
                    <a href="manage_bookings.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sync"></i> Reset Filters
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-8">
                    <form method="GET" class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by customer name or room number" 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-warning">
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

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Room</th>
                            <th>Dates</th>
                            <th>Guests</th>
                            <th>Packages</th>
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
                                    <td>
                                        <?= htmlspecialchars($booking['RoomNumber']) ?><br>
                                        <small class="text-muted"><?= htmlspecialchars($booking['RoomType']) ?></small>
                                    </td>
                                    <td>
                                        <?= date('M j', strtotime($booking['CheckInDate'])) ?> - 
                                        <?= date('M j, Y', strtotime($booking['CheckOutDate'])) ?>
                                    </td>
                                    <td><?= $booking['NumberOfGuests'] ?></td>
                                    <td><?= $booking['PackageNames'] ? htmlspecialchars($booking['PackageNames']) : 'None' ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($booking['BookingStatus']) ?>">
                                            <?= $booking['BookingStatus'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="manage_bookings.php?action=edit&id=<?= $booking['BookingID'] ?>" 
                                           class="btn btn-sm btn-warning">
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
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-2x text-muted mb-3"></i>
                                    <h5>No bookings found</h5>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Date validation
        const checkIn = document.querySelector('input[name="check_in"]');
        const checkOut = document.querySelector('input[name="check_out"]');
        
        checkIn.addEventListener('change', () => {
            checkOut.min = checkIn.value;
        });

        // Room info display
        document.querySelector('select[name="room_id"]').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            console.log('Selected Room Type:', selectedOption.dataset.type);
            console.log('Base Price:', selectedOption.dataset.price);
        });
    </script>
</body>
</html>