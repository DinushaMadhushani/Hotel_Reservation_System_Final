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
$editPackageId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = $success = '';
$packageData = [
    'PackageName' => '',
    'Description' => '',
    'Price' => ''
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $packageName = trim($_POST['package_name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);

    try {
        // Validate required fields
        if (empty($packageName) || empty($price)) {
            throw new Exception("All fields marked with * are required");
        }

        // Validate price format
        if (!is_numeric($price) || $price <= 0) {
            throw new Exception("Price must be a valid positive number");
        }

        if ($action === 'add') {
            // Check for existing package name
            $stmt = $conn->prepare("SELECT PackageID FROM Packages WHERE PackageName = ?");
            $stmt->bind_param("s", $packageName);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                throw new Exception("Package name already exists");
            }

            // Insert new package
            $stmt = $conn->prepare("INSERT INTO Packages (PackageName, Description, Price) 
                                   VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $packageName, $description, $price);
        } else {
            // Update existing package
            $stmt = $conn->prepare("UPDATE Packages SET 
                                  PackageName = ?,
                                  Description = ?,
                                  Price = ?
                                  WHERE PackageID = ?");
            $stmt->bind_param("ssdi", $packageName, $description, $price, $editPackageId);
        }

        if ($stmt->execute()) {
            $success = "Package " . ($action === 'add' ? 'added' : 'updated') . " successfully!";
            $action = 'add';
            $editPackageId = 0;
            // Reset form data
            $packageData = [
                'PackageName' => '',
                'Description' => '',
                'Price' => ''
            ];
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        // Preserve form data on error
        $packageData = $_POST;
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $packageId = intval($_GET['id']);
    try {
        $stmt = $conn->prepare("DELETE FROM Packages WHERE PackageID = ?");
        $stmt->bind_param("i", $packageId);
        if ($stmt->execute()) {
            $success = "Package deleted successfully!";
        } else {
            throw new Exception("Error deleting package: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Fetch package for editing
if ($action === 'edit' && $editPackageId > 0) {
    $stmt = $conn->prepare("SELECT * FROM Packages WHERE PackageID = ?");
    $stmt->bind_param("i", $editPackageId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $packageData = $result->fetch_assoc();
    } else {
        $error = "Package not found!";
        $action = 'add';
        $editPackageId = 0;
    }
}

// Pagination and filtering
$results_per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $results_per_page;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build query
$base_query = "FROM Packages WHERE 1=1";
$params = [];

if (!empty($search)) {
    $base_query .= " AND (PackageName LIKE ? OR Description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
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
$query = "SELECT * $base_query ORDER BY CreatedAt DESC LIMIT ? OFFSET ?";
$params[] = $results_per_page;
$params[] = $offset;

$stmt = $conn->prepare($query);
$types = (!empty($params) ? str_repeat('s', count($params)-2) : '') . 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$packages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages - Admin Dashboard</title>
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

        .price-badge {
            background-color: rgba(212, 175, 55, 0.1);
            color: var(--accent);
            padding: 0.35em 0.65em;
            border-radius: 0.25rem;
            font-weight: 500;
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

        <!-- Package Form -->
        <div class="management-card mt-4" data-aos="fade-up">
            <h3 class="mb-4">
                <i class="fas fa-box-open"></i> 
                <?= $action === 'add' ? 'Add New Package' : 'Edit Package' ?>
            </h3>
            
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Package Name *</label>
                        <input type="text" class="form-control" name="package_name" 
                               value="<?= htmlspecialchars($packageData['PackageName']) ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Price *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" name="price" 
                                   value="<?= htmlspecialchars($packageData['Price']) ?>" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($packageData['Description']) ?></textarea>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-accent btn-lg">
                            <i class="fas fa-save"></i> 
                            <?= $action === 'add' ? 'Add Package' : 'Update Package' ?>
                        </button>
                        
                        <?php if ($action === 'edit'): ?>
                            <a href="manage_packages.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Packages Table -->
        <div class="management-card mt-4" data-aos="fade-up">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0"><i class="fas fa-boxes"></i> All Packages</h3>
                <div>
                    <a href="manage_packages.php" class="btn btn-sm btn-secondary">
                        <i class="fas fa-sync"></i> Reset Filters
                    </a>
                </div>
            </div>

            <!-- Search -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <form method="GET" class="input-group">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search package name or description" 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-accent">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">
                    Showing <?= count($packages) ?> of <?= $total_rows ?> results
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

            <!-- Packages Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Package Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($packages) > 0): ?>
                            <?php foreach ($packages as $package): ?>
                                <tr>
                                    <td><?= htmlspecialchars($package['PackageName']) ?></td>
                                    <td><?= htmlspecialchars($package['Description']) ?></td>
                                    <td>
                                        <span class="price-badge">
                                            $<?= number_format($package['Price'], 2) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($package['CreatedAt'])) ?></td>
                                    <td>
                                        <a href="manage_packages.php?action=edit&id=<?= $package['PackageID'] ?>" 
                                           class="btn btn-sm btn-accent">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="manage_packages.php?action=delete&id=<?= $package['PackageID'] ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete this package?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                                    <h5>No packages found</h5>
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
    </script>
</body>
</html>