<?php
session_start();
require '../config/db.con.php';
// Simulated logged-in user data (replace this with your actual database logic)
$loggedInUser = [
    'UserID' => 1,
    'FullName' => 'John Doe',
    'Email' => 'johndoe@example.com',
    'PhoneNumber' => '123-456-7890',
    'Address' => '123 Main St, City, Country',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to update user profile
    $loggedInUser['FullName'] = htmlspecialchars($_POST['FullName']);
    $loggedInUser['Email'] = htmlspecialchars($_POST['Email']);
    $loggedInUser['PhoneNumber'] = htmlspecialchars($_POST['PhoneNumber']);
    $loggedInUser['Address'] = htmlspecialchars($_POST['Address']);

    // In a real-world scenario, you would update the database here
    echo "<script>alert('Profile updated successfully!');</script>";
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
        <form method="POST" action="">
            <div class="mb-3">
                <label for="FullName" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="FullName" name="FullName"
                    value="<?php echo $loggedInUser['FullName']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="Email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="Email" name="Email"
                    value="<?php echo $loggedInUser['Email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="PhoneNumber" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="PhoneNumber" name="PhoneNumber"
                    value="<?php echo $loggedInUser['PhoneNumber']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="Address" class="form-label">Address</label>
                <textarea class="form-control" id="Address" name="Address" rows="3"
                    required><?php echo $loggedInUser['Address']; ?></textarea>
            </div>
            <button type="submit" class="btn btn-update w-100"><i class="fas fa-save me-2"></i>Update Profile</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init();
    </script>
</body>

</html>