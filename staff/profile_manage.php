<?php
// Enable error reporting (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../config/db.con.php';

// Validate session and permissions
if (!isset($_SESSION['UserID'], $_SESSION['FullName'], $_SESSION['UserType']) || 
    $_SESSION['UserType'] !== 'Staff') {
    header("Location: ../auth/login.php");
    exit();
} 

// Initialize error/success messages
$error = '';
$success = '';
$userData = []; 


// Check database connection
if (!isset($conn) || $conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// Configuration
$uploadDir = '../uploads/profiles/';
$defaultImage = 'default-profile.jpg';
$maxFileSize = 2 * 1024 * 1024; // 2MB
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

// Create upload directory if not exists
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Get current user data
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $_SESSION['UserID']);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();

// Handle profile updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        // Sanitize inputs
        $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $currentImage = $userData['ProfileImage'] ?? $defaultImage;

        // Handle file upload
        if (!empty($_FILES['profile_image']['name'])) {
            $file = $_FILES['profile_image'];
            $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = uniqid('profile_') . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;

            // Validate file
            if (!in_array($fileExt, $allowedTypes)) {
                $error = "Invalid file type. Allowed types: " . implode(', ', $allowedTypes);
            } elseif ($file['size'] > $maxFileSize) {
                $error = "File size exceeds 2MB limit";
            } elseif (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                $error = "Error uploading file";
            } else {
                // Delete old image if not default
                if ($currentImage !== $defaultImage && file_exists($currentImage)) {
                    unlink($currentImage);
                }
                $currentImage = $targetPath;
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE Users SET 
                FullName = ?, 
                Email = ?, 
                PhoneNumber = ?, 
                Address = ?,
                ProfileImage = ?
                WHERE UserID = ?");
            $stmt->bind_param("sssssi", $fullName, $email, $phone, $address, $currentImage, $_SESSION['UserID']);
            
            if ($stmt->execute()) {
                $_SESSION['FullName'] = $fullName;
                $_SESSION['ProfileImage'] = $currentImage;
                $success = "Profile updated successfully!";
            } else {
                $error = "Error updating profile: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    // Handle password change
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if (password_verify($currentPassword, $userData['PasswordHash'])) {
            if ($newPassword === $confirmPassword) {
                if (strlen($newPassword) < 8) {
                    $error = "Password must be at least 8 characters";
                } else {
                    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE Users SET PasswordHash = ? WHERE UserID = ?");
                    $stmt->bind_param("si", $newHash, $_SESSION['UserID']);
                    
                    if ($stmt->execute()) {
                        $success = "Password changed successfully!";
                    } else {
                        $error = "Error changing password: " . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                $error = "New passwords do not match!";
            }
        } else {
            $error = "Current password is incorrect!";
        }
    }
}

// Refresh user data after updates
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $_SESSION['UserID']);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
        }

        body {
            background-color: var(--light);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .profile-card {
            background: var(--secondary);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .profile-img-container {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 20px;
            border: 3px solid var(--accent);
            position: relative;
        }

        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .upload-btn {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .upload-btn input {
            display: none;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: var(--accent);
            border-color: var(--accent);
        }
    </style>
</head>
<body>
   

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="mb-4 text-center">Manage Profile</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <!-- Profile Image Section -->
                <div class="text-center mb-4">
                    <div class="profile-img-container">
                        <img src="<?= htmlspecialchars($userData['ProfileImage'] ?? $defaultImage) ?>" 
                             class="profile-img" 
                             alt="Profile Image">
                        <label class="upload-btn">
                            <i class="fas fa-camera"></i>
                            <input type="file" name="profile_image" 
                                   accept="image/*" 
                                   form="profileForm">
                        </label>
                    </div>
                </div>

                <!-- Personal Information Card -->
                <div class="card profile-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="profileForm">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" 
                                    value="<?= htmlspecialchars($userData['FullName'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" 
                                    value="<?= htmlspecialchars($userData['Email'] ?? '') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone" 
                                    value="<?= htmlspecialchars($userData['PhoneNumber'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="3"><?= 
                                    htmlspecialchars($userData['Address'] ?? '') ?></textarea>
                            </div>

                            <button type="submit" name="update_profile" 
                                class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Password Change Card -->
                <div class="card profile-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" 
                                    name="current_password" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" 
                                    name="new_password" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" 
                                    name="confirm_password" required>
                            </div>

                            <button type="submit" name="change_password" 
                                class="btn btn-primary w-100">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="dashboard.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image preview functionality
        document.querySelector('input[name="profile_image"]').addEventListener('change', function(e) {
            const reader = new FileReader();
            reader.onload = function() {
                document.querySelector('.profile-img').src = reader.result;
            }
            if (this.files[0]) {
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>