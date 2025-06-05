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
    <title>Profile Management - Customer Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="../assets/css/style.css">
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
                        goldLight: '#f5e8c9',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        script: ['Dancing Script', 'cursive'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 1s ease-in-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'tilt': 'tilt 10s infinite linear',
                        'border-pulse': 'borderPulse 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        tilt: {
                            '0%, 100%': { transform: 'rotate(0deg)' },
                            '25%': { transform: 'rotate(1deg)' },
                            '75%': { transform: 'rotate(-1deg)' },
                        },
                        borderPulse: {
                            '0%': { 'border-color': 'rgba(212, 175, 55, 0.5)' },
                            '50%': { 'border-color': 'rgba(212, 175, 55, 1)' },
                            '100%': { 'border-color': 'rgba(212, 175, 55, 0.5)' },
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans bg-light text-dark min-h-screen">

<?php include '../includes/user_header.php'; ?>

<!-- Main Content -->
<div class="pt-24 px-4 md:px-8 lg:px-16 max-w-7xl mx-auto">
    <div class="max-w-3xl mx-auto">
        <!-- Alerts -->
        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-md transform transition-all duration-300 hover:shadow-lg" data-aos="fade-up">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2 text-xl"></i>
                    <p><?= $error ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-md transform transition-all duration-300 hover:shadow-lg" data-aos="fade-up">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2 text-xl"></i>
                    <p><?= $success ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Profile Card -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200 transition-all duration-300 hover:shadow-2xl" data-aos="fade-up">
            <!-- Card Header -->
            <div class="bg-primary p-6 text-white relative overflow-hidden">
                <div class="absolute -right-12 -top-12 w-32 h-32 bg-accent rounded-full opacity-20"></div>
                <div class="absolute -left-12 -bottom-12 w-24 h-24 bg-accent rounded-full opacity-10"></div>
                
                <h2 class="text-3xl font-bold text-center relative z-10 font-serif flex items-center justify-center">
                    <i class="fas fa-user-cog mr-3 text-accent"></i>
                    <span class="animate-pulse-slow">Profile Settings</span>
                </h2>
            </div>
            
            <!-- Profile Form -->
            <form method="POST" enctype="multipart/form-data" class="p-6">
                <!-- Profile Image -->
                <div class="flex justify-center mb-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="relative group">
                        <div class="w-32 h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-4 border-accent animate-border-pulse shadow-lg transition-all duration-300 transform group-hover:scale-105">
                            <img src="<?= htmlspecialchars($imagePath) ?>" class="w-full h-full object-cover" alt="Profile Picture">
                        </div>
                        <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 cursor-pointer">
                            <div class="text-white text-center">
                                <i class="fas fa-camera text-2xl mb-1"></i>
                                <p class="text-sm">Change Photo</p>
                            </div>
                        </div>
                        <input type="file" name="profile_image" id="profileUpload" accept="image/*" class="hidden" onchange="previewImage(this)">
                    </div>
                </div>

                <!-- Personal Information Section -->
                <div class="mb-8" data-aos="fade-up" data-aos-delay="200">
                    <h3 class="text-xl font-bold text-primary mb-4 border-b border-gray-200 pb-2 flex items-center">
                        <i class="fas fa-user text-accent mr-2"></i> Personal Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" name="fullname" value="<?= htmlspecialchars($userData['FullName']) ?>" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-300 outline-none">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" value="<?= htmlspecialchars($userData['Email']) ?>" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-300 outline-none">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                            <input type="tel" name="phone" value="<?= htmlspecialchars($userData['PhoneNumber']) ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-300 outline-none">
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" name="address" value="<?= htmlspecialchars($userData['Address']) ?>"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-300 outline-none">
                        </div>
                    </div>
                </div>
                
                <!-- Password Section -->
                <div class="mb-8" data-aos="fade-up" data-aos-delay="300">
                    <h3 class="text-xl font-bold text-primary mb-4 border-b border-gray-200 pb-2 flex items-center">
                        <i class="fas fa-lock text-accent mr-2"></i> Change Password
                        <span class="ml-2 text-sm font-normal text-gray-500">(leave blank to keep current password)</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2 relative">
                            <label class="block text-sm font-medium text-gray-700">Current Password</label>
                            <div class="relative">
                                <input type="password" name="current_password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-300 outline-none pr-10">
                                <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-accent focus:outline-none transition-colors duration-300" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="space-y-2 relative">
                            <label class="block text-sm font-medium text-gray-700">New Password</label>
                            <div class="relative">
                                <input type="password" name="new_password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-300 outline-none pr-10">
                                <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-accent focus:outline-none transition-colors duration-300" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="space-y-2 relative">
                            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                            <div class="relative">
                                <input type="password" name="confirm_password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-accent transition-all duration-300 outline-none pr-10">
                                <button type="button" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-accent focus:outline-none transition-colors duration-300" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="mt-8" data-aos="fade-up" data-aos-delay="400">
                    <button type="submit" class="w-full bg-accent text-primary py-3 px-6 rounded-lg font-bold text-lg shadow-md hover:bg-accent/90 hover:shadow-lg transform hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-accent focus:ring-opacity-50 flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Back to Dashboard Button -->
        <div class="mt-6 text-center" data-aos="fade-up" data-aos-delay="500">
            <a href="dashboard.php" class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/80 transition-all duration-300 transform hover:-translate-y-1 shadow-md hover:shadow-lg">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Back to Top Button -->
<button id="back-to-top" class="fixed bottom-8 right-8 z-50 bg-accent text-primary w-12 h-12 rounded-full flex items-center justify-center shadow-lg transform transition-all duration-300 opacity-0 translate-y-10 hover:bg-accent/80 hover:scale-110">
    <i class="fas fa-arrow-up"></i>
</button>


<?php include '../includes/sub_footer.php'; ?>

<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: false,
        mirror: true
    });
    
    // Profile image handling - Fix for profile browse option
    document.addEventListener('DOMContentLoaded', function() {
        const profileContainer = document.querySelector('.relative.group');
        const profileUpload = document.getElementById('profileUpload');
        
        if (profileContainer && profileUpload) {
            profileContainer.addEventListener('click', function() {
                profileUpload.click();
            });
        }
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const profileImg = document.querySelector('.group img');
                if (profileImg) {
                    profileImg.src = e.target.result;
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePassword(button) {
        const input = button.parentElement.querySelector('input');
        const icon = button.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
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
    
    // Back to top button
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0', 'translate-y-10');
                backToTopButton.classList.add('opacity-100', 'translate-y-0');
            } else {
                backToTopButton.classList.remove('opacity-100', 'translate-y-0');
                backToTopButton.classList.add('opacity-0', 'translate-y-10');
            }
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
</body>

</html>