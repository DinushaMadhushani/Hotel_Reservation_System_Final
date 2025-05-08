<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$adminId = $_SESSION['UserID'];
$error = $success = '';
$adminData = [];

// Fetch current admin data
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$adminData = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    try {
        // Validate required fields
        if (empty($fullName) || empty($email)) {
            throw new Exception("Name and email are required");
        }

        // Check email uniqueness
        $stmt = $conn->prepare("SELECT UserID FROM Users WHERE Email = ? AND UserID != ?");
        $stmt->bind_param("si", $email, $adminId);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Email already exists");
        }

        // Handle password change
        $passwordUpdate = '';
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                throw new Exception("Current password is required to change password");
            }
            
            if (!password_verify($currentPassword, $adminData['PasswordHash'])) {
                throw new Exception("Current password is incorrect");
            }
            
            if ($newPassword !== $confirmPassword) {
                throw new Exception("New passwords do not match");
            }
            
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $passwordUpdate = ", PasswordHash = ?";
        }

        // Build update query
        $query = "UPDATE Users SET 
                 FullName = ?,
                 Email = ?,
                 PhoneNumber = ?,
                 Address = ?
                 $passwordUpdate
                 WHERE UserID = ?";
        
        $params = [$fullName, $email, $phone, $address];
        if (!empty($passwordUpdate)) {
            $params[] = $hashedPassword;
        }
        $params[] = $adminId;
        
        $stmt = $conn->prepare($query);
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            // Update session data
            $_SESSION['FullName'] = $fullName;
            $_SESSION['Email'] = $email;
            
            $success = "Profile updated successfully!";
            // Refresh admin data
            $stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
            $stmt->bind_param("i", $adminId);
            $stmt->execute();
            $result = $stmt->get_result();
            $adminData = $result->fetch_assoc();
        } else {
            throw new Exception("Error updating profile: " . $stmt->error);
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management - Admin Dashboard</title>
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

        body {
            background-color: var(--light);
            padding-top: 80px;
        }

        .top-nav {
            background: var(--dark);
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .management-card {
            background: var(--secondary);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .profile-section {
            max-width: 800px;
            margin: 0 auto;
        }

        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .btn-accent {
    background-color: var(--accent);
    color: var(--primary);
    border: none;
}

.btn-accent:hover {
    background-color: #b89329;
    color: var(--primary);
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
                            <li><a class="dropdown-item active" href="./profile_management.php"><i class="fas fa-user-cog"></i> Profile Settings</a></li>
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
        <div class="profile-section">
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

            <!-- Profile Form -->
            <div class="management-card mt-4" data-aos="fade-up">
                <h3 class="mb-4">
                    <i class="fas fa-user-cog"></i> Profile Settings
                </h3>
                
                <form method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="fullname" 
                                   value="<?= htmlspecialchars($adminData['FullName']) ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?= htmlspecialchars($adminData['Email']) ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="<?= htmlspecialchars($adminData['PhoneNumber']) ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" 
                                   value="<?= htmlspecialchars($adminData['Address']) ?>">
                        </div>
                        
                        <div class="col-12 mt-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-lock"></i> Change Password
                                <small class="text-muted">(leave blank to keep current password)</small>
                            </h5>
                        </div>
                        
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password">
                            <i class="fas fa-eye password-toggle" onclick="togglePassword(this)"></i>
                        </div>
                        
                        <div class="col-md-4 position-relative">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password">
                            <i class="fas fa-eye password-toggle" onclick="togglePassword(this)"></i>
                        </div>
                        
                        <div class="col-md-4 position-relative">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password">
                            <i class="fas fa-eye password-toggle" onclick="togglePassword(this)"></i>
                        </div>
                        
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-accent btn-lg">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </div>
                    </div>
                </form>
            </div>
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

        function togglePassword(icon) {
            const input = icon.previousElementSibling;
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
            icon.classList.toggle('fa-eye-slash');
        }

        // Password validation
        const newPassword = document.querySelector('input[name="new_password"]');
        const confirmPassword = document.querySelector('input[name="confirm_password"]');
        
        function validatePasswords() {
            if (newPassword.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Passwords do not match');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        
        newPassword.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);
    </script>
</body>
</html>