<?php
session_start();
require '../config/db.con.php'; // This now provides $conn

// Check if user is logged in
if (!isset($_SESSION['UserID'])) {
    header("Location:../auth/login.php");
    exit();
}

$userId = $_SESSION['UserID'];
$error = '';
$success = '';

// Fetch user data
$loggedInUser = [];
$query = "SELECT * FROM Users WHERE UserID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    $loggedInUser = mysqli_fetch_assoc($result);
} else {
    // Gracefully handle missing user
    session_destroy();
    header("Location: ../auth/login.php?error=user_not_found");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['FullName']);
    $email = trim($_POST['Email']);
    $phone = trim($_POST['PhoneNumber']);
    $address = trim($_POST['Address']);

    // Enhanced validation
    if (empty($fullName) || empty($email)) {
        $error = "Full Name and Email are required!";
    } else {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
        } else {
            // Check email uniqueness
            $checkQuery = "SELECT UserID FROM Users WHERE Email = ? AND UserID != ?";
            $checkStmt = mysqli_prepare($conn, $checkQuery);
            mysqli_stmt_bind_param($checkStmt, "si", $email, $userId);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_store_result($checkStmt);

            if (mysqli_stmt_num_rows($checkStmt) > 0) {
                $error = "Email is already registered to another account!";
            } else {
                // Update user information
                $updateQuery = "UPDATE Users SET 
                                FullName = ?, 
                                Email = ?, 
                                PhoneNumber = ?, 
                                Address = ?
                                WHERE UserID = ?";
                $updateStmt = mysqli_prepare($conn, $updateQuery);
                mysqli_stmt_bind_param($updateStmt, "ssssi", 
                    $fullName, 
                    $email, 
                    $phone,
                    $address, 
                    $userId
                );

                if (mysqli_stmt_execute($updateStmt)) {
                    $success = "Profile updated successfully!";
                    // Refresh user data
                    $refreshStmt = mysqli_prepare($conn, "SELECT * FROM Users WHERE UserID = ?");
                    mysqli_stmt_bind_param($refreshStmt, "i", $userId);
                    mysqli_stmt_execute($refreshStmt);
                    $refreshResult = mysqli_stmt_get_result($refreshStmt);
                    $loggedInUser = mysqli_fetch_assoc($refreshResult);
                } else {
                    // Log error and show user-friendly message
                    error_log("Profile update failed: " . mysqli_error($conn));
                    $error = "Error updating profile. Please try again later.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
            color: var(--primary);
        }

        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: var(--secondary);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-header h2 {
            font-weight: bold;
            color: var(--accent);
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
        }

        .btn-update {
            background-color: var(--accent);
            border: none;
            color: var(--primary);
        }

        .btn-update:hover {
            background-color: var(--dark);
            color: var(--secondary);
        }
    </style>
</head>
<body>
    <div class="profile-container" data-aos="fade-up" data-aos-duration="1000">
        <div class="profile-header">
            <h2><i class="fas fa-user-circle me-2"></i>User Profile</h2>
            <p>Manage your personal information here.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="FullName" class="form-label">Full Name</label>
                <input type="text" class="form-label" id="FullName" name="FullName"
                    value="<?php echo htmlspecialchars($loggedInUser['FullName']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="Email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="Email" name="Email"
                    value="<?php echo htmlspecialchars($loggedInUser['Email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="PhoneNumber" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="PhoneNumber" name="PhoneNumber"
                    value="<?php echo htmlspecialchars($loggedInUser['PhoneNumber']); ?>">
            </div>
            <div class="mb-3">
                <label for="Address" class="form-label">Address</label>
                <textarea class="form-control" id="Address" name="Address" rows="3"
                    ><?php echo htmlspecialchars($loggedInUser['Address']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-update w-100"><i class="fas fa-save me-2"></i>Update Profile</button>
        </form>
    </div>

    <!-- Existing scripts remain the same -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>
</html>