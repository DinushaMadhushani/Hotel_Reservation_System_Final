<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("
        INSERT INTO Users (FullName, Email, PasswordHash, UserType, PhoneNumber, Address)
        VALUES (?, ?, ?, 'Staff', ?, ?)
    ");
    
    $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $stmt->bind_param("sssss",
        $_POST['fullname'],
        $_POST['email'],
        $passwordHash,
        $_POST['phone'],
        $_POST['address']
    );
    
    $stmt->execute();
    header("Location: index.php?page=staff");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Same header as other pages -->
</head>
<body>
    <!-- Same navigation as other pages -->

    <div class="container-fluid p-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Add New Staff Member</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Staff</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>