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
$editUserId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = $success = '';
$userData = [
    'FullName' => '',
    'Email' => '',
    'UserType' => 'Customer',
    'PhoneNumber' => '',
    'Address' => ''
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $userType = 'Customer'; // Fixed to Customer only
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    try {
        if (empty($fullName) || empty($email)) {
            throw new Exception("Full Name and Email are required");
        }

        if ($action === 'add') {
            if (empty($password)) {
                throw new Exception("Password is required when adding a new user");
            }
            $stmt = $conn->prepare("INSERT INTO Users (FullName, Email, PasswordHash, UserType, PhoneNumber, Address) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $fullName, $email, $password, $userType, $phone, $address);
        } else {
            if (!empty($password)) {
                $stmt = $conn->prepare("UPDATE Users SET 
                                      FullName = ?, 
                                      Email = ?, 
                                      PasswordHash = ?, 
                                      PhoneNumber = ?, 
                                      Address = ? 
                                      WHERE UserID = ?");
                $stmt->bind_param("sssssi", $fullName, $email, $password, $phone, $address, $editUserId);
            } else {
                $stmt = $conn->prepare("UPDATE Users SET 
                                      FullName = ?, 
                                      Email = ?, 
                                      PhoneNumber = ?, 
                                      Address = ? 
                                      WHERE UserID = ?");
                $stmt->bind_param("ssssi", $fullName, $email, $phone, $address, $editUserId);
            }
        }

        if ($stmt->execute()) {
            $success = "User " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
            $action = 'add';
            $editUserId = 0;
            // Reset form data after successful submission
            $userData = [
                'FullName' => '',
                'Email' => '',
                'PhoneNumber' => '',
                'Address' => ''
            ];
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $userId = intval($_GET['id']);
    
    try {
        // Check for existing bookings
        $bookingCheck = $conn->prepare("SELECT COUNT(*) AS booking_count FROM Bookings WHERE UserID = ?");
        $bookingCheck->bind_param("i", $userId);
        $bookingCheck->execute();
        $bookingResult = $bookingCheck->get_result()->fetch_assoc();
        
        if ($bookingResult['booking_count'] > 0) {
            throw new Exception("Cannot delete user because they have existing bookings");
        }
        
        // Check for service requests
        $serviceCheck = $conn->prepare("SELECT COUNT(*) AS service_count FROM ServiceRequests WHERE UserID = ?");
        $serviceCheck->bind_param("i", $userId);
        $serviceCheck->execute();
        $serviceResult = $serviceCheck->get_result()->fetch_assoc();
        
        if ($serviceResult['service_count'] > 0) {
            throw new Exception("Cannot delete user because they have existing service requests");
        }
        
        // Delete the user
        $deleteStmt = $conn->prepare("DELETE FROM Users WHERE UserID = ? AND UserType = 'Customer'");
        $deleteStmt->bind_param("i", $userId);
        
        if ($deleteStmt->execute()) {
            if ($deleteStmt->affected_rows > 0) {
                $success = "Customer deleted successfully!";
            } else {
                throw new Exception("Customer not found or could not be deleted");
            }
        } else {
            throw new Exception("Error deleting customer: " . $deleteStmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch user for editing (only customers)
if ($action === 'edit' && $editUserId > 0) {
    $stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ? AND UserType = 'Customer'");
    $stmt->bind_param("i", $editUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    } else {
        $error = "Customer not found!";
        $action = 'add';
        $editUserId = 0;
    }
}

// Pagination and filtering - ONLY CUSTOMERS
$results_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $results_per_page;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build query - ONLY CUSTOMERS
$base_query = "FROM Users WHERE UserType = 'Customer'";
if (!empty($search)) {
    $base_query .= " AND (FullName LIKE '%$search%' OR Email LIKE '%$search%')";
}

// Get total count
$count_result = $conn->query("SELECT COUNT(*) AS total $base_query");
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $results_per_page);

// Fetch users - ONLY CUSTOMERS
$query = "SELECT * $base_query ORDER BY CreatedAt DESC LIMIT $results_per_page OFFSET $offset";
$result = $conn->query($query);
$users = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.rawgit.com/michalsnik/aos/2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_manage.css">
</head>
<body>
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

    <div class="container">
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

        <div class="management-card mt-4" data-aos="fade-up">
            <h3 class="mb-4">
                <i class="fas fa-user-edit"></i> 
                <?= $action === 'add' ? 'Add New Customer' : 'Edit Customer' ?>
            </h3>
            
            <form method="POST">
                <input type="hidden" name="usertype" value="Customer">
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name *</label>
                        <input type="text" class="form-control" name="fullname" 
                               value="<?= htmlspecialchars($userData['FullName']) ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" class="form-control" name="email" 
                               value="<?= htmlspecialchars($userData['Email']) ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Password <?= $action === 'add' ? '*' : '(optional)' ?></label>
                        <input type="password" class="form-control" name="password" <?= $action === 'add' ? 'required' : '' ?>>
                        <?php if ($action === 'edit'): ?>
                            <small class="form-text text-muted">Leave blank to keep current password</small>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">User Type</label>
                        <input type="text" class="form-control" value="Customer" disabled>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" 
                               value="<?= htmlspecialchars($userData['PhoneNumber']) ?>">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" class="form-control" name="address" 
                               value="<?= htmlspecialchars($userData['Address']) ?>">
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-accent btn-lg">
                            <i class="fas fa-save"></i> 
                            <?= $action === 'add' ? 'Add Customer' : 'Update Customer' ?>
                        </button>
                        
                        <?php if ($action === 'edit'): ?>
                            <a href="manage_customes.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="management-card mt-4" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="fas fa-users"></i> Manage Customers</h3>
                <div>
                    <a href="manage_customes.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <form method="GET" class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-accent" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Showing <?= count($users) ?> of <?= $total_rows ?> customers
                </span>
                <form method="GET" class="ms-3">
                    <select class="form-select form-select-sm" name="per_page" onchange="this.form.submit()">
                        <option value="10" <?= $results_per_page == 10 ? 'selected' : '' ?>>10 per page</option>
                        <option value="25" <?= $results_per_page == 25 ? 'selected' : '' ?>>25 per page</option>
                        <option value="50" <?= $results_per_page == 50 ? 'selected' : '' ?>>50 per page</option>
                    </select>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($users) > 0): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['FullName']) ?></td>
                                    <td><?= htmlspecialchars($user['Email']) ?></td>
                                    <td><?= htmlspecialchars($user['PhoneNumber']) ?></td>
                                    <td><?= htmlspecialchars($user['Address']) ?></td>
                                    <td><?= date('M d, Y', strtotime($user['CreatedAt'])) ?></td>
                                    <td>
                                        <a href="manage_customes.php?action=edit&id=<?= $user['UserID'] ?>" 
                                           class="btn btn-sm btn-accent">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_customes.php?action=delete&id=<?= $user['UserID'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this customer? All their data will be permanently removed.')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                                    <h5>No customers found</h5>
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
                                   href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&per_page=<?= $results_per_page ?>">
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
        
        // Clear form after successful add
        <?php if ($success && $action === 'add'): ?>
            document.querySelector('form').reset();
        <?php endif; ?>
    </script>
</body>
</html>