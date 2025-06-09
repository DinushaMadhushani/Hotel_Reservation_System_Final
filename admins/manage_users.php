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
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $userType = $_POST['usertype'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    try {
        $conn->autocommit(FALSE); // Start transaction
        
        // Validate required fields
        if (empty($fullName) || empty($email) || empty($userType)) {
            throw new Exception("All fields marked with * are required");
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please enter a valid email address");
        }
        
        // Check for password if adding a new user
        if ($action === 'add' && empty($password)) {
            throw new Exception("Password is required for new users");
        }
        
        // Check for duplicate email
        $checkEmailStmt = $conn->prepare("SELECT UserID FROM Users WHERE Email = ? AND UserID != ?");
        $checkEmailStmt->bind_param("si", $email, $editUserId);
        $checkEmailStmt->execute();
        $emailResult = $checkEmailStmt->get_result();
        
        if ($emailResult->num_rows > 0) {
            throw new Exception("Email address is already in use by another user");
        }
        
        // Hash password for security (in a real application)
        // For this example, we'll keep it as is, but in production you should use password_hash()
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO Users (FullName, Email, PasswordHash, UserType, PhoneNumber, Address) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $fullName, $email, $password, $userType, $phone, $address);
        } else {
            if (!empty($password)) {
                $stmt = $conn->prepare("UPDATE Users SET 
                                      FullName = ?, 
                                      Email = ?, 
                                      PasswordHash = ?, 
                                      UserType = ?, 
                                      PhoneNumber = ?, 
                                      Address = ? 
                                      WHERE UserID = ?");
                $stmt->bind_param("ssssssi", $fullName, $email, $password, $userType, $phone, $address, $editUserId);
            } else {
                $stmt = $conn->prepare("UPDATE Users SET 
                                      FullName = ?, 
                                      Email = ?, 
                                      UserType = ?, 
                                      PhoneNumber = ?, 
                                      Address = ? 
                                      WHERE UserID = ?");
                $stmt->bind_param("sssssi", $fullName, $email, $userType, $phone, $address, $editUserId);
            }
        }

        if (!$stmt->execute()) {
            throw new Exception("Database error: " . $stmt->error);
        }
        
        $conn->commit();
        $success = "User " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
        $action = 'add';
        $editUserId = 0;
        $userData = [
            'FullName' => '',
            'Email' => '',
            'UserType' => 'Customer',
            'PhoneNumber' => '',
            'Address' => ''
        ];
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
        // Preserve form data on error
        $userData = [
            'FullName' => $fullName,
            'Email' => $email,
            'UserType' => $userType,
            'PhoneNumber' => $phone,
            'Address' => $address
        ];
    } finally {
        $conn->autocommit(TRUE); // Reset autocommit mode
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $userId = intval($_GET['id']);
    try {
        $conn->autocommit(FALSE); // Start transaction
        
        // First check if user has any related records in other tables
        $tables = [
            'Bookings' => 'bookings',
            'ServiceRequests' => 'service requests',
            'StaffSchedule' => 'staff schedules',
            'AssignedTasks' => 'assigned tasks'
        ];
        
        $relatedRecords = [];
        
        // Check Bookings
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM Bookings WHERE UserID = ?");
        $checkStmt->bind_param("i", $userId);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();
        if ($result['count'] > 0) {
            $relatedRecords[] = $result['count'] . ' booking' . ($result['count'] > 1 ? 's' : '');
        }
        
        // Check ServiceRequests
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM ServiceRequests WHERE UserID = ?");
        $checkStmt->bind_param("i", $userId);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();
        if ($result['count'] > 0) {
            $relatedRecords[] = $result['count'] . ' service request' . ($result['count'] > 1 ? 's' : '');
        }
        
        // Check StaffSchedule
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM StaffSchedule WHERE UserID = ?");
        $checkStmt->bind_param("i", $userId);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();
        if ($result['count'] > 0) {
            $relatedRecords[] = $result['count'] . ' staff schedule' . ($result['count'] > 1 ? 's' : '');
        }
        
        // Check AssignedTasks
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM AssignedTasks WHERE StaffID = ?");
        $checkStmt->bind_param("i", $userId);
        $checkStmt->execute();
        $result = $checkStmt->get_result()->fetch_assoc();
        if ($result['count'] > 0) {
            $relatedRecords[] = $result['count'] . ' assigned task' . ($result['count'] > 1 ? 's' : '');
        }
        
        // If there are related records, prevent deletion
        if (!empty($relatedRecords)) {
            throw new Exception("Cannot delete this user because they have related records: " . implode(", ", $relatedRecords) . ". Please remove these records first or reassign them to another user.");
        }
        
        // If no related records, proceed with deletion
        $stmt = $conn->prepare("DELETE FROM Users WHERE UserID = ?");
        $stmt->bind_param("i", $userId);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting user: " . $stmt->error);
        }
        
        $conn->commit();
        $success = "User deleted successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    } finally {
        $conn->autocommit(TRUE); // Reset autocommit mode
    }
}

// Fetch user for editing
if ($action === 'edit' && $editUserId > 0) {
    $stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
    $stmt->bind_param("i", $editUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
    } else {
        $error = "User not found!";
        $action = 'add';
        $editUserId = 0;
    }
}

// Pagination and filtering
$results_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $results_per_page;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filter_type = isset($_GET['type']) ? $conn->real_escape_string($_GET['type']) : '';

// Build query
$base_query = "FROM Users WHERE 1=1";
if (!empty($search)) {
    $base_query .= " AND (FullName LIKE '%$search%' OR Email LIKE '%$search%')";
}
if (!empty($filter_type)) {
    $base_query .= " AND UserType = '$filter_type'";
}

// Get total count
$count_result = $conn->query("SELECT COUNT(*) AS total $base_query");
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $results_per_page);

// Fetch users
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
    <title>Manage Users - Admin Dashboard</title>
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
                <?= $action === 'add' ? 'Add New User' : 'Edit User' ?>
            </h3>
            
            <form method="POST">
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
                        <input type="password" class="form-control" name="password">
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">User Type *</label>
                        <select class="form-select" name="usertype" required>
                            <option value="Customer" <?= $userData['UserType'] === 'Customer' ? 'selected' : '' ?>>Customer</option>
                            <option value="Staff" <?= $userData['UserType'] === 'Staff' ? 'selected' : '' ?>>Staff</option>
                            <option value="Admin" <?= $userData['UserType'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
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
                            <?= $action === 'add' ? 'Add User' : 'Update User' ?>
                        </button>
                        
                        <?php if ($action === 'edit'): ?>
                            <a href="manage_users.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="management-card mt-4" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="fas fa-users"></i> All Users</h3>
                <div>
                    <a href="manage_users.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sync"></i> Reset
                    </a>
                </div>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-md-6">
                    <form method="GET" class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-accent" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <div class="col-md-6">
                    <form method="GET" class="input-group">
                        <select class="form-select" name="type" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="Customer" <?= $filter_type === 'Customer' ? 'selected' : '' ?>>Customer</option>
                            <option value="Staff" <?= $filter_type === 'Staff' ? 'selected' : '' ?>>Staff</option>
                            <option value="Admin" <?= $filter_type === 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Showing <?= count($users) ?> of <?= $total_rows ?> results
                </span>
                <form method="GET" class="ms-3">
                    <select class="form-select form-select-sm" name="per_page" onchange="this.form.submit()">
                        <option value="10" <?= $results_per_page == 10 ? 'selected' : '' ?>>10 per page</option>
                        <option value="25" <?= $results_per_page == 25 ? 'selected' : '' ?>>25 per page</option>
                        <option value="50" <?= $results_per_page == 50 ? 'selected' : '' ?>>50 per page</option>
                    </select>
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($filter_type) ?>">
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Phone</th>
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
                                    <td>
                                        <span class="badge bg-<?= 
                                            $user['UserType'] === 'Admin' ? 'danger' : 
                                            ($user['UserType'] === 'Staff' ? 'warning' : 'primary') 
                                        ?>">
                                            <?= $user['UserType'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($user['PhoneNumber']) ?></td>
                                    <td><?= date('M d, Y', strtotime($user['CreatedAt'])) ?></td>
                                    <td>
                                        <a href="manage_users.php?action=edit&id=<?= $user['UserID'] ?>" 
                                           class="btn btn-sm btn-accent">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_users.php?action=delete&id=<?= $user['UserID'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-user-slash fa-2x text-muted mb-3"></i>
                                    <h5>No users found matching your criteria</h5>
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
                                   href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($filter_type) ?>&per_page=<?= $results_per_page ?>">
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