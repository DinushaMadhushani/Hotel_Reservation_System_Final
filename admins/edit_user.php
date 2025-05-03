<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$user = [];
if (isset($_GET['user_id'])) {
    $userId = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        $_SESSION['error'] = "User not found!";
        header("Location: users.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_user') {
    // ... [keep existing POST handling code] ...
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --side-bar: rgb(197, 164, 54);
        }

        body {
            background: var(--primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Arial', sans-serif;
        }

        .form-container {
            background: var(--secondary);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            max-width: 600px;
            margin: 2rem auto;
            position: relative;
        }

        .form-header {
            background: var(--side-bar);
            padding: 2rem;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }

        .form-title {
            color: var(--secondary);
            margin: 0;
            font-size: 1.8rem;
        }

        .close-btn {
            position: absolute;
            right: 20px;
            top: 20px;
            color: var(--secondary);
            font-size: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .close-btn:hover {
            color: var(--accent);
            transform: rotate(90deg);
        }

        .form-body {
            padding: 2rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-group label {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .input-group input,
        .input-group select,
        .input-group textarea {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.3);
        }

        .btn-primary {
            background: var(--accent);
            color: var(--primary);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1 class="form-title"><i class="fas fa-user-edit me-2"></i>Edit User</h1>
                <div class="close-btn" onclick="closeForm()">
                    <i class="fas fa-times"></i>
                </div>
            </div>
            <div class="form-body">
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="edit_user">
                    <input type="hidden" name="user_id" value="<?= $user['UserID'] ?? '' ?>">
                    
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="<?= htmlspecialchars($user['FullName'] ?? '') ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($user['Email'] ?? '') ?>" required>
                    </div>

                    <div class="input-group">
                        <label>User Type</label>
                        <select name="user_type" class="form-select" required>
                            <option value="Customer" <?= ($user['UserType'] ?? '') === 'Customer' ? 'selected' : '' ?>>Customer</option>
                            <option value="Staff" <?= ($user['UserType'] ?? '') === 'Staff' ? 'selected' : '' ?>>Staff</option>
                            <option value="Admin" <?= ($user['UserType'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="<?= htmlspecialchars($user['PhoneNumber'] ?? '') ?>" pattern="[0-9]{10}">
                    </div>

                    <div class="input-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="3"><?= 
                            htmlspecialchars($user['Address'] ?? '') ?></textarea>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function closeForm() {
            // Return to previous page
            window.history.back();
        }

        // Close form with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeForm();
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>