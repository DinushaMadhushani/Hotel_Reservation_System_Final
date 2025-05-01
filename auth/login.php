<?php
session_start();
require '../config/db.con.php';

// Initialize variables
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        // Prepare SQL statement with case-insensitive search
        $sql = "SELECT UserID, FullName, Email, PasswordHash, UserType 
                FROM Users 
                WHERE LOWER(Email) = LOWER(?)"; // Case-insensitive match
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                    
                    // Verify password (plain text comparison)
                    if ($password === $user['PasswordHash']) {
                        // Start session
                        $_SESSION['UserID'] = $user['UserID'];
                        $_SESSION['FullName'] = $user['FullName'];
                        $_SESSION['UserType'] = $user['UserType'];
                        
                        // Redirect based on role
                        switch ($user['UserType']) {
                            case 'Admin':
                                header("Location: ../admins/dashboard.php");
                                exit();
                            case 'Staff':
                                header("Location: ../staff/dashboard.php");
                                exit();
                            case 'Customer':
                                header("Location: ../users/user_dashboard.php");
                                exit();
                            default:
                                $login_err = "Invalid user role.";
                        }
                    } else {
                        $login_err = "Invalid password.";
                    }
                } else {
                    $login_err = "Email not found. Please check your email address.";
                }
            } else {
                $login_err = "Database query failed. Please try again later.";
            }
            $stmt->close();
        } else {
            $login_err = "Database connection error. Please try again later.";
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: rgb(44, 109, 173); }
        .login-container { max-width: 400px; margin: 100px auto; }
        .form-control { border-radius: 25px; }
        .btn-login { border-radius: 25px; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Login</h3>
                <?php if (!empty($login_err)): ?>
                    <div class="alert alert-danger"><?= $login_err ?></div>
                <?php endif; ?>
                
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" name="email" class="form-control <?= (!empty($email_err)) ? 'is-invalid' : '' ?>" 
                                   value="<?= $email ?>" autocomplete="email">
                            <span class="invalid-feedback"><?= $email_err ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control <?= (!empty($password_err)) ? 'is-invalid' : '' ?>" 
                                   autocomplete="current-password">
                            <span class="invalid-feedback"><?= $password_err ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block btn-login">Login</button>
                    </div>
                    <div class="text-center">
                        <a href="./register.php">Don't Have Account? Register Here. </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>