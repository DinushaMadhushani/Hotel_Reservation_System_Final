<?php
session_start();
require '../config/db.con.php';

// Initialize variables
$error = $success = '';
$editRequest = null;
$activeBookings = [];

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['UserID'];

try {
    // Handle edit parameter
    if (isset($_GET['edit'])) {
        $requestId = intval($_GET['edit']);
        
        // Validate and get request details
        $stmt = $conn->prepare("SELECT * FROM ServiceRequests 
                               WHERE RequestID = ? AND UserID = ? AND Status = 'Pending'");
        $stmt->bind_param("ii", $requestId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $editRequest = $result->fetch_assoc();
        } else {
            throw new Exception("Invalid request or request cannot be edited");
        }
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['edit_request'])) {
            $requestId = intval($_POST['request_id']);
            $newType = trim($_POST['request_type']);
            $newDesc = trim($_POST['description']);

            // Validate request ownership
            $stmt = $conn->prepare("UPDATE ServiceRequests 
                                   SET RequestType = ?, Description = ?
                                   WHERE RequestID = ? AND UserID = ? AND Status = 'Pending'");
            $stmt->bind_param("ssii", $newType, $newDesc, $requestId, $userId);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                $success = "Service request updated successfully!";
                header("Refresh: 2; url=manage_service.php");
            } else {
                throw new Exception("No changes made or request cannot be updated");
            }
        }
    }

    // Get active bookings for new requests
    $stmt = $conn->prepare("SELECT b.BookingID, r.RoomNumber 
                          FROM Bookings b
                          JOIN Rooms r ON b.RoomID = r.RoomID
                          WHERE b.UserID = ? AND b.CheckOutDate >= CURDATE()");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $activeBookings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $error = $e->getMessage();
}

// Close connection at the end
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $editRequest ? 'Edit' : 'New' ?> Service Request - Hotel System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
        }

        .service-form {
            background: var(--secondary);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">

<?php include '../includes/user_header.php'; ?>
    <div class="container py-4">
        <h2 class="mb-4">
            <i class="fas <?= $editRequest ? 'fa-edit' : 'fa-plus-circle' ?>"></i>
            <?= $editRequest ? 'Edit Service Request' : 'New Service Request' ?>
        </h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="service-form p-4 mb-4">
            <form method="POST">
                <?php if ($editRequest): ?>
                    <input type="hidden" name="request_id" value="<?= $editRequest['RequestID'] ?>">
                <?php endif; ?>

                <div class="row g-3">
                    <?php if (!$editRequest): ?>
                        <div class="col-md-4">
                            <label class="form-label">Select Booking</label>
                            <select name="booking_id" class="form-select" required>
                                <option value="">Choose Booking...</option>
                                <?php foreach ($activeBookings as $booking): ?>
                                    <option value="<?= $booking['BookingID'] ?>">
                                        Room <?= htmlspecialchars($booking['RoomNumber']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-<?= $editRequest ? '6' : '4' ?>">
                        <label class="form-label">Request Type</label>
                        <select name="request_type" class="form-select" required>
                            <?php $types = ['Room Service', 'Housekeeping', 'Maintenance', 'Transportation', 'Other']; ?>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= $type ?>" 
                                    <?= ($editRequest && $editRequest['RequestType'] === $type) ? 'selected' : '' ?>>
                                    <?= $type ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-<?= $editRequest ? '6' : '4' ?>">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" 
                               value="<?= htmlspecialchars($editRequest['Description'] ?? '') ?>" 
                               class="form-control" required>
                    </div>

                    <div class="col-12 mt-3">
                        <div class="d-grid gap-2">
                            <button type="submit" name="<?= $editRequest ? 'edit_request' : 'new_request' ?>" 
                                    class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $editRequest ? 'Update' : 'Submit' ?>
                            </button>
                            <a href="manage_service.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>