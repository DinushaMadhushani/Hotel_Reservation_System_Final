<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Get customer ID
$customerId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch customer details
$customer = [];
$reservations = [];

try {
    // Get customer info
    $stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $customer = $stmt->get_result()->fetch_assoc();

    if (!$customer) {
        throw new Exception("Customer not found");
    }

    // Get reservations
    $stmt = $conn->prepare("
        SELECT b.*, r.RoomNumber, p.PackageName 
        FROM Bookings b
        LEFT JOIN Rooms r ON b.RoomID = r.RoomID
        LEFT JOIN BookingPackages bp ON b.BookingID = bp.BookingID
        LEFT JOIN Packages p ON bp.PackageID = p.PackageID
        WHERE b.UserID = ?
        ORDER BY b.CheckInDate DESC
    ");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $reservations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $error = $e->getMessage();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Details - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }

        .detail-card {
            background: var(--secondary);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .reservation-badge {
            background-color: rgba(212, 175, 55, 0.1);
            color: var(--accent);
            padding: 0.25em 0.5em;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <!-- Top Navigation (Same as manage_customers.php) -->
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
                            <li><a class="dropdown-item" href="profile_management.php"><i class="fas fa-user-circle"></i> Profile</a></li>
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
        <div class="detail-card mt-4">
            <a href="./manage_customes.php" class="btn btn-secondary mb-4">
                <i class="fas fa-arrow-left"></i> Back to Customers
            </a>

            <h3 class="mb-4">
                <i class="fas fa-user"></i> Customer Details
            </h3>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php else: ?>
                <!-- Customer Info -->
                <div class="row mb-5">
                    <div class="col-md-6">
                        <h5>Basic Information</h5>
                        <dl class="row">
                            <dt class="col-sm-4">Full Name:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($customer['FullName']) ?></dd>

                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($customer['Email']) ?></dd>

                            <dt class="col-sm-4">Phone:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($customer['PhoneNumber']) ?></dd>

                            <dt class="col-sm-4">Address:</dt>
                            <dd class="col-sm-8"><?= htmlspecialchars($customer['Address']) ?></dd>

                            <dt class="col-sm-4">Joined:</dt>
                            <dd class="col-sm-8"><?= date('M d, Y', strtotime($customer['CreatedAt'])) ?></dd>
                        </dl>
                    </div>
                </div>

                <!-- Reservations -->
                <h5 class="mb-3">Reservation History</h5>
                <?php if (count($reservations) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Room</th>
                                    <th>Dates</th>
                                    <th>Guests</th>
                                    <th>Package</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $res): ?>
                                    <tr>
                                        <td>Room <?= htmlspecialchars($res['RoomNumber']) ?></td>
                                        <td>
                                            <span class="reservation-badge">
                                                <?= date('M j', strtotime($res['CheckInDate'])) ?> - 
                                                <?= date('M j, Y', strtotime($res['CheckOutDate'])) ?>
                                            </span>
                                        </td>
                                        <td><?= $res['NumberOfGuests'] ?></td>
                                        <td><?= $res['PackageName'] ?? 'None' ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $res['BookingStatus'] === 'Confirmed' ? 'success' : 
                                                ($res['BookingStatus'] === 'Pending' ? 'warning' : 
                                                ($res['BookingStatus'] === 'Cancelled' ? 'danger' : 'secondary')) 
                                            ?>">
                                                <?= $res['BookingStatus'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No reservations found for this customer
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>