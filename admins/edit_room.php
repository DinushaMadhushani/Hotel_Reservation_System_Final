<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

$room = [];
if (isset($_GET['room_id'])) {
    $roomId = filter_input(INPUT_GET, 'room_id', FILTER_SANITIZE_NUMBER_INT);
    $stmt = $conn->prepare("SELECT * FROM Rooms WHERE RoomID = ?");
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $result = $stmt->get_result();
    $room = $result->fetch_assoc();
    $stmt->close();
    
    if (!$room) {
        $_SESSION['error'] = "Room not found!";
        header("Location: rooms.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_room') {
    $roomId = filter_input(INPUT_POST, 'room_id', FILTER_SANITIZE_NUMBER_INT);
    $roomNumber = filter_input(INPUT_POST, 'room_number', FILTER_SANITIZE_STRING);
    $roomType = filter_input(INPUT_POST, 'room_type', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $basePrice = filter_input(INPUT_POST, 'base_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $availabilityStatus = filter_input(INPUT_POST, 'availability_status', FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("UPDATE Rooms SET 
        RoomNumber = ?, 
        RoomType = ?, 
        Description = ?, 
        BasePrice = ?, 
        AvailabilityStatus = ? 
        WHERE RoomID = ?");
    $stmt->bind_param("sssdsi", $roomNumber, $roomType, $description, $basePrice, $availabilityStatus, $roomId);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Room updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating room: " . $stmt->error;
    }
    $stmt->close();
    header("Location: edit_room.php?room_id=".$roomId);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Same styles as edit_user.php */
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --side-bar: rgb(197, 164, 54);
        }

        .price-input {
            position: relative;
        }

        .input-group-text {
            background: var(--accent);
            color: var(--primary);
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <h1 class="form-title"><i class="fas fa-edit me-2"></i>Edit Room</h1>
            </div>
            <div class="form-body">
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="edit_room">
                    <input type="hidden" name="room_id" value="<?= $room['RoomID'] ?? '' ?>">
                    
                    <div class="input-group">
                        <label>Room Number</label>
                        <input type="text" name="room_number" class="form-control" 
                               value="<?= htmlspecialchars($room['RoomNumber'] ?? '') ?>" required>
                    </div>

                    <div class="input-group">
                        <label>Room Type</label>
                        <select name="room_type" class="form-select" required>
                            <option value="Standard" <?= ($room['RoomType'] ?? '') === 'Standard' ? 'selected' : '' ?>>Standard</option>
                            <option value="Deluxe" <?= ($room['RoomType'] ?? '') === 'Deluxe' ? 'selected' : '' ?>>Deluxe</option>
                            <option value="Suite" <?= ($room['RoomType'] ?? '') === 'Suite' ? 'selected' : '' ?>>Suite</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= 
                            htmlspecialchars($room['Description'] ?? '') ?></textarea>
                    </div>

                    <div class="input-group price-input">
                        <label>Base Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="base_price" class="form-control" 
                                   value="<?= htmlspecialchars($room['BasePrice'] ?? '') ?>" step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Availability Status</label>
                        <select name="availability_status" class="form-select" required>
                            <option value="Available" <?= ($room['AvailabilityStatus'] ?? '') === 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="Occupied" <?= ($room['AvailabilityStatus'] ?? '') === 'Occupied' ? 'selected' : '' ?>>Occupied</option>
                            <option value="Maintenance" <?= ($room['AvailabilityStatus'] ?? '') === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                        </select>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>