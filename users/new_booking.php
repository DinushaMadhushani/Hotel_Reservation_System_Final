<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

$error = '';
$availableRooms = [];
$roomSearch = '';

// Handle availability check
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_in'])) {
    $checkIn = $_GET['check_in'];
    $checkOut = $_GET['check_out'];
    $roomSearch = trim($_GET['room_number'] ?? '');

    try {
        // Validate dates
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        $today = new DateTime();

        if ($checkInDate < $today) {
            throw new Exception("Check-in date cannot be in the past");
        }
        
        if ($checkInDate >= $checkOutDate) {
            throw new Exception("Check-out date must be after check-in date");
        }

        // Build query based on search
        $query = "SELECT r.RoomID, r.RoomNumber, r.RoomType, 
                         r.Description, r.BasePrice
                  FROM Rooms r
                  WHERE r.AvailabilityStatus = 'Available'
                  AND r.RoomID NOT IN (
                      SELECT b.RoomID FROM Bookings b
                      WHERE b.CheckInDate < ? 
                      AND b.CheckOutDate > ?
                  )";

        $params = [$checkOut, $checkIn];
        $types = "ss";

        // Add room number filter if specified
        if (!empty($roomSearch)) {
            $query .= " AND r.RoomNumber = ?";
            $params[] = $roomSearch;
            $types .= "s";
        }

        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $availableRooms = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Availability - Hotel System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
     <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }

        .availability-card {
            background: var(--secondary);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .room-card {
            transition: transform 0.3s ease;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .price-badge {
            background: var(--accent);
            color: var(--primary);
            font-size: 1.1rem;
        }
    </style>
</head>
<body class="bg-light">
    <?php include '../includes/user_header.php'; ?>
    <div class="container py-4 mt-5">
        <div class="availability-card">
            <h3 class="mb-4"><i class="fas fa-search"></i> Check Room Availability</h3>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Check-in Date</label>
                        <input type="date" class="form-control" name="check_in" 
                               value="<?= htmlspecialchars($_GET['check_in'] ?? date('Y-m-d')) ?>" 
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Check-out Date</label>
                        <input type="date" class="form-control" name="check_out" 
                               value="<?= htmlspecialchars($_GET['check_out'] ?? date('Y-m-d', strtotime('+1 day'))) ?>" 
                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Room Number (optional)</label>
                        <input type="text" class="form-control" name="room_number"
                               value="<?= htmlspecialchars($roomSearch) ?>"
                               placeholder="Enter room number">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Check
                        </button>
                    </div>
                </div>
            </form>

            <?php if (!empty($availableRooms)): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <h4 class="mb-3">Available Rooms</h4>
                        <div class="row g-4">
                            <?php foreach ($availableRooms as $room): ?>
                                <div class="col-md-4">
                                    <div class="card room-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h5 class="card-title"><?= htmlspecialchars($room['RoomNumber']) ?></h5>
                                                <span class="price-badge badge">
                                                    $<?= number_format($room['BasePrice'], 2) ?>/night
                                                </span>
                                            </div>
                                            <p class="card-text">
                                                <i class="fas fa-bed"></i> <?= htmlspecialchars($room['RoomType']) ?><br>
                                                <?= htmlspecialchars($room['Description']) ?>
                                            </p>
                                            <div class="mt-2">
                                                <a href="booking.php?room=<?= $room['RoomID'] ?>&check_in=<?= urlencode($_GET['check_in']) ?>&check_out=<?= urlencode($_GET['check_out']) ?>"
                                                   class="btn btn-success w-100">
                                                    <i class="fas fa-book"></i> Book Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php elseif (isset($_GET['check_in'])): ?>
                <div class="alert alert-info mt-4">No rooms available matching your criteria</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Date validation
        const checkInInput = document.querySelector('input[name="check_in"]');
        const checkOutInput = document.querySelector('input[name="check_out"]');

        checkInInput.addEventListener('change', () => {
            const checkInDate = new Date(checkInInput.value);
            const minCheckOut = new Date(checkInDate);
            minCheckOut.setDate(checkInDate.getDate() + 1);
            
            checkOutInput.min = minCheckOut.toISOString().split('T')[0];
            if (new Date(checkOutInput.value) < minCheckOut) {
                checkOutInput.value = minCheckOut.toISOString().split('T')[0];
            }
        });
    </script>
</body>
</html>