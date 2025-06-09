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

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

$userId = $_SESSION['UserID'];
$error = $success = '';
$userData = [];

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

if ($result->num_rows === 0) {
    die("User not found");
}

// Profile Image Configuration
$roleFolders = [
    'Admin' => '../assets/images/Admin/dp/',
    'Customer' => '../assets/images/Customer/dp/',
    'Staff' => '../assets/images/Staff/dp/'
];

$imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$imageFound = false;
$basePath = $roleFolders[$_SESSION['UserType']] ?? '../assets/images/default/';
$basePath = rtrim($basePath, '/') . '/';
$safeEmail = basename($userData['Email']);

// Sanitize email for filename
$cleanEmail = str_replace(['.', '@'], ['.', '_at_'], $userData['Email']);
$cleanEmail = preg_replace('/[^a-zA-Z0-9_\.]/', '', $cleanEmail);


// Find existing profile image
foreach ($imageExtensions as $ext) {
    $testPath = $basePath . $safeEmail . '.' . $ext;
    if (file_exists($testPath)) {
        $imagePath = $testPath;
        $imageFound = true;
        break;
    }
}

if (!$imageFound) {
    $roleDefault = $basePath . 'default.jpg';
    $imagePath = file_exists($roleDefault) ? $roleDefault : '../assets/images/default/default.jpg';
}

// Add cache buster
$imagePath .= $imageFound ? '?v=' . time() : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... [Keep existing form handling code] ...


    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['profile_image']['tmp_name'];
        $fileExt = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExt, $imageExtensions)) {
            throw new Exception("Invalid image format. Allowed: " . implode(', ', $imageExtensions));
        }

        // Create directory if not exists
        if (!is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }

        // Remove old profile images with different extensions
        foreach ($imageExtensions as $ext) {
            $oldFile = $basePath . $cleanEmail . '.' . $ext;
            if (file_exists($oldFile)) @unlink($oldFile);
        }

        // Save new image with standardized naming
        $newFileName = $cleanEmail . '.' . $fileExt;
        $newFilePath = $basePath . $newFileName;

        if (!move_uploaded_file($fileTmp, $newFilePath)) {
            throw new Exception("Failed to upload profile image");
        }

        // Update image path for immediate display
        $imagePath = $newFilePath . '?v=' . time();
    }

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
            $stmt->bind_param("si", $email, $userId);
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

                if (!password_verify($currentPassword, $userData['PasswordHash'])) {
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
            $params[] = $userId;

            $stmt = $conn->prepare($query);
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                // Update session data
                $_SESSION['FullName'] = $fullName;
                $_SESSION['Email'] = $email;

                $success = "Profile updated successfully!";
                // Refresh user data
                $stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $userData = $result->fetch_assoc();
            } else {
                throw new Exception("Error updating profile: " . $stmt->error);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a1a1a',
                        secondary: '#ffffff',
                        accent: '#d4af37',
                        light: '#f5f5f5',
                        dark: '#121212',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body class="bg-light">
    <?php include('../includes/staff_header.php'); ?>
    <!-- Main Content -->
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Alerts -->
            <?php if ($error): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2 text-red-500"></i>
                        <span><?= $error ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2 text-green-500"></i>
                        <span><?= $success ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Profile Form -->
            <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 mb-8 transition-all duration-300 hover:shadow-xl" data-aos="fade-up">
                <form method="POST" enctype="multipart/form-data">
                    <div class="relative w-40 h-40 mx-auto mb-8 rounded-full overflow-hidden border-4 border-accent group cursor-pointer hover:shadow-lg transition-all duration-300">
                        <img src="<?= htmlspecialchars($imagePath) ?>" class="w-full h-full object-cover" id="profile-preview" alt="<?= htmlspecialchars($userData['FullName']) ?>">
                        <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white py-2 text-center text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <i class="fas fa-camera mr-1"></i> Change Photo
                        </div>
                        <input type="file" name="profile_image" id="profileUpload"
                            accept="image/*" class="hidden" onchange="previewImage(this)">
                    </div>

                    <h3 class="text-2xl font-semibold text-center mb-6 text-gray-800 flex items-center justify-center">
                        <i class="fas fa-user-cog text-accent mr-2"></i> Profile Settings
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Full Name *</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200" name="fullname"
                                value="<?= htmlspecialchars($userData['FullName']) ?>" required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Email *</label>
                            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200" name="email"
                                value="<?= htmlspecialchars($userData['Email']) ?>" required>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200" name="phone"
                                value="<?= htmlspecialchars($userData['PhoneNumber']) ?>">
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200" name="address"
                                value="<?= htmlspecialchars($userData['Address']) ?>">
                        </div>
                    </div>

                    <div class="mt-10 mb-6">
                        <h5 class="text-xl font-medium text-gray-800 border-b border-gray-200 pb-2 mb-6 flex items-center">
                            <i class="fas fa-lock text-accent mr-2"></i> Change Password
                            <span class="ml-2 text-sm font-normal text-gray-500">(leave blank to keep current password)</span>
                        </h5>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="relative space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Current Password</label>
                                <div class="relative">
                                    <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200" name="current_password">
                                    <i class="fas fa-eye absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-accent cursor-pointer transition-colors duration-200" onclick="togglePassword(this)"></i>
                                </div>
                            </div>

                            <div class="relative space-y-2">
                                <label class="block text-sm font-medium text-gray-700">New Password</label>
                                <div class="relative">
                                    <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200" name="new_password">
                                    <i class="fas fa-eye absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-accent cursor-pointer transition-colors duration-200" onclick="togglePassword(this)"></i>
                                </div>
                            </div>

                            <div class="relative space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                <div class="relative">
                                    <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-200" name="confirm_password">
                                    <i class="fas fa-eye absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-accent cursor-pointer transition-colors duration-200" onclick="togglePassword(this)"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="w-full bg-accent hover:bg-accent/90 text-primary font-medium py-3 px-4 rounded-lg transition-all duration-300 flex items-center justify-center text-lg shadow-md hover:shadow-lg">
                            <i class="fas fa-save mr-2"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.rawgit.com/michalsnik/aos/2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Profile image handling
        document.querySelector('.w-40.h-40').addEventListener('click', () => {
            document.getElementById('profileUpload').click();
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('profile-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function togglePassword(icon) {
            const input = icon.parentElement.querySelector('input');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye-slash');
        }

        // Password validation with visual feedback
        const newPassword = document.querySelector('input[name="new_password"]');
        const confirmPassword = document.querySelector('input[name="confirm_password"]');

        function validatePasswords() {
            if (newPassword.value && confirmPassword.value) {
                if (newPassword.value !== confirmPassword.value) {
                    confirmPassword.classList.add('border-red-500');
                    confirmPassword.classList.remove('border-green-500');
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.classList.remove('border-red-500');
                    confirmPassword.classList.add('border-green-500');
                    confirmPassword.setCustomValidity('');
                }
            } else {
                confirmPassword.classList.remove('border-red-500', 'border-green-500');
                confirmPassword.setCustomValidity('');
            }
        }

        newPassword.addEventListener('input', validatePasswords);
        confirmPassword.addEventListener('input', validatePasswords);
    </script>
</body>

</html>