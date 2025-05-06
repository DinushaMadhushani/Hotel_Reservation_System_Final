<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$staffId = $_GET['user_id'];
$staff = $conn->query("
    SELECT * FROM Users 
    WHERE UserID = $staffId AND UserType = 'Staff'
")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("
        UPDATE Users SET
        FullName = ?,
        Email = ?,
        PhoneNumber = ?,
        Address = ?
        WHERE UserID = ?
    ");
    
    $stmt->bind_param("ssssi",
        $_POST['fullname'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['address'],
        $staffId
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
                <h5 class="card-title">Edit Staff Member</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label>Full Name</label>
                        <input type="text" name="fullname" class="form-control" 
                               value="<?= $staff['FullName'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="<?= $staff['Email'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" name="phone" class="form-control" 
                               value="<?= $staff['PhoneNumber'] ?>">
                    </div>
                    <div class="mb-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control"><?= $staff['Address'] ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>