<?php
session_start();
require '../config/db.con.php';

if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_room') {
    $roomNumber = filter_input(INPUT_POST, 'room_number', FILTER_SANITIZE_STRING);
    $roomType = filter_input(INPUT_POST, 'room_type', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $basePrice = filter_input(INPUT_POST, 'base_price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $availabilityStatus = filter_input(INPUT_POST, 'availability_status', FILTER_SANITIZE_STRING);

    if (!empty($roomNumber) && !empty($roomType) && !empty($basePrice)) {
        $stmt = $conn->prepare("INSERT INTO Rooms (RoomNumber, RoomType, Description, BasePrice, AvailabilityStatus) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $roomNumber, $roomType, $description, $basePrice, $availabilityStatus);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Room added successfully!";
        } else {
            $_SESSION['error'] = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Please fill all required fields!";
    }
    header("Location: add_room.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
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
            display: block;
        }

        .input-group input,
        .input-group select,
        .input-group textarea {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px;
            width: 100%;
            transition: all 0.3s ease;
            background: var(--secondary);
            color: var(--primary);
        }

        .input-group input:focus,
        .input-group select:focus,
        .input-group textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 8px rgba(212, 175, 55, 0.3);
            outline: none;
        }

        .input-group-text {
            background: var(--accent);
            color: var(--primary);
            border: none;
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
                <h1 class="form-title"><i class="fas fa-door-open me-2"></i>Add New Room</h1>
            </div>
            <div class="form-body">
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="add_room">
                    
                    <div class="input-group">
                        <label>Room Number</label>
                        <input type="text" name="room_number" class="form-control" required>
                    </div>

                    <div class="input-group">
                        <label>Room Type</label>
                        <select name="room_type" class="form-select" required>
                            <option value="Standard">Standard</option>
                            <option value="Deluxe">Deluxe</option>
                            <option value="Suite">Suite</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="input-group">
                        <label>Base Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="base_price" class="form-control" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Availability Status</label>
                        <select name="availability_status" class="form-select" required>
                            <option value="Available">Available</option>
                            <option value="Occupied">Occupied</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Add Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>