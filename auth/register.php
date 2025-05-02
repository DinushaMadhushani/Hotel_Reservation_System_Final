<?php
session_start();
require '../config/db.con.php';

// Initialize variables
$fullname = $email = $password = $confirm_password = $phone = $address = "";
$fullname_err = $email_err = $password_err = $confirm_password_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Full Name
    if (empty(trim($_POST["fullname"]))) {
        $fullname_err = "Please enter your full name.";
    } else {
        $fullname = trim($_POST["fullname"]);
    }

    // Validate Email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
        // Check if email exists
        $sql = "SELECT UserID FROM Users WHERE Email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $email_err = "Email already registered.";
                }
            }
            $stmt->close();
        }
    }

    // Validate Password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter password.";     
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate Confirm Password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if ($password != $confirm_password) {
            $confirm_password_err = "Passwords did not match.";
        }
    }

    // Check for errors before inserting
    if (empty($fullname_err) && empty($email_err) && empty($password_err) 
        && empty($confirm_password_err)) {
        
        $phone = trim($_POST["phone"]);
        $address = trim($_POST["address"]);
        
        // Set default user role to Customer
        $user_type = "Customer";
        
        // Insert user into database (PLAIN TEXT PASSWORD STORAGE)
        $sql = "INSERT INTO Users (FullName, Email, PasswordHash, PhoneNumber, Address, UserType) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssss", $fullname, $email, $password, $phone, $address, $user_type);
            
            if ($stmt->execute()) {
                // Registration success - redirect to login
                $_SESSION['register_success'] = "Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color:#d4af37; }
        .register-container { max-width: 500px; margin: 50px auto; }
        .form-control { border-radius: 25px; }
        .btn-register { border-radius: 25px; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="card shadow">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Register</h3>
                <?php 
                if (!empty($email_err)) {
                    echo '<div class="alert alert-danger">' . $email_err . '</div>';
                }
                ?>
                
                <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                    <div class="form-group">
                        <label>Full Name</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" name="fullname" class="form-control <?= (!empty($fullname_err)) ? 'is-invalid' : '' ?>" 
                                   value="<?= $fullname ?>">
                            <span class="invalid-feedback"><?= $fullname_err ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="email" name="email" class="form-control <?= (!empty($email_err)) ? 'is-invalid' : '' ?>" 
                                   value="<?= $email ?>">
                            <span class="invalid-feedback"><?= $email_err ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" name="password" class="form-control <?= (!empty($password_err)) ? 'is-invalid' : '' ?>">
                            <span class="invalid-feedback"><?= $password_err ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" name="confirm_password" class="form-control <?= (!empty($confirm_password_err)) ? 'is-invalid' : '' ?>">
                            <span class="invalid-feedback"><?= $confirm_password_err ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            </div>
                            <input type="text" name="phone" class="form-control" value="<?= $phone ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            </div>
                            <input type="text" name="address" class="form-control" value="<?= $address ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-dark btn-block btn-register">Register</button>
                    </div>
                    <div class="text-center">
                        Already have an account? <a href="login.php">Login here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>