<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
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

// Profile Image Configuration
$roleFolders = [
    'Admin' => '../assets/images/Admin/dp/',
    'Customer' => '../assets/images/Customer/dp/',
    'Staff' => '../assets/images/Staff/dp/'
];

$imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$imageFound = false;
$userType = $_SESSION['UserType'];
$basePath = $roleFolders[$userType] ?? '../assets/images/default/';
$basePath = rtrim($basePath, '/') . '/';

// Sanitize email for filename
$cleanEmail = str_replace(['@', '.'], ['_at_', '_dot_'], $userData['Email']);
$cleanEmail = preg_replace('/[^a-zA-Z0-9_\.]/', '', $cleanEmail);

// Find existing profile image
foreach ($imageExtensions as $ext) {
    $testPath = $basePath . $cleanEmail . '.' . $ext;
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

    // ... [Rest of existing form handling code] ...
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Keep existing head section -->
</head>
<body>
    <!-- Keep existing navigation -->

    <!-- Main Content -->
    <div class="container">
        <div class="profile-section">
            <!-- Keep existing alerts -->

            <!-- Profile Form -->
            <div class="management-card mt-4" data-aos="fade-up">
                <div class="profile-image-container">
                    <img src="<?= htmlspecialchars($imagePath) ?>" class="profile-image" alt="Profile Picture">
                    <div class="upload-overlay">
                        <small>Click to change</small>
                    </div>
                    <input type="file" name="profile_image" id="profileUpload" 
                           accept="image/*" hidden onchange="previewImage(this)">
                </div>

                <!-- Keep rest of the form -->
            </div>
        </div>
    </div>

    <!-- Keep existing scripts -->
</body>
</html>