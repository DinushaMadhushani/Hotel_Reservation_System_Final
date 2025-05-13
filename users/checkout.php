<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

$error = $availableRooms = [];
$checkIn = $checkOut = $selectedType = '';
$roomTypes = [];

// Get all room types for filter dropdown
try {
    $stmt = $conn->prepare("SELECT DISTINCT RoomType FROM Rooms ORDER BY RoomType");
    $stmt->execute();
    $roomTypes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error[] = "Error fetching room types: " . $e->getMessage();
}

// Handle date search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check_in']) && isset($_GET['check_out'])) {
    $checkIn = $_GET['check_in'];
    $checkOut = $_GET['check_out'];
    $selectedType = $_GET['room_type'] ?? '';

    // Validate dates
    $today = new DateTime();


    // var_dump($checkIn, $checkOut, $today);


    try {
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);

        if ($checkInDate < $today) {
            throw new Exception("Check-in date cannot be in the past");
        }

        if ($checkOutDate <= $checkInDate) {
            throw new Exception("Check-out date must be after check-in date");
        }

        // Build comprehensive availability query
        $query = "SELECT r.* 
                FROM Rooms r
                WHERE r.AvailabilityStatus = 'Available' OR r.AvailabilityStatus ='Occupied'
                AND r.RoomID NOT IN (
                    SELECT b.RoomID 
                    FROM Bookings b
                    WHERE b.BookingStatus IN ('Pending', 'Confirmed')
                    AND (
                        (b.CheckInDate <= ? AND b.CheckOutDate >= ?) OR  -- Existing booking overlaps start
                        (b.CheckInDate <= ? AND b.CheckOutDate >= ?) OR  -- Existing booking overlaps end
                        (b.CheckInDate >= ? AND b.CheckOutDate <= ?)     -- Existing booking within selected dates
                    )
                )";

        // Add room type filter if selected
        $params = [$checkOut, $checkIn, $checkIn, $checkOut, $checkIn, $checkOut];
        $types = str_repeat('s', 6);

        if (!empty($selectedType)) {
            $query .= " AND r.RoomType = ?";
            $params[] = $selectedType;
            $types .= "s";
        }

        // Prepare and execute query
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $availableRooms = $result->fetch_all(MYSQLI_ASSOC);

        if (empty($availableRooms)) {
            $error[] = "No rooms available for selected dates" . (!empty($selectedType) ? " and room type" : "");
        }
    } catch (Exception $e) {
        $error[] = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Availability - Hotel System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.0.0/css/all.min.css">
    <style>
        .room-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
        }

        .room-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .price-display {
            font-size: 1.25rem;
            font-weight: 600;
            color: #27ae60;
        }

        .night-count {
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4"><i class="fas fa-search me-2"></i>Check Room Availability</h2>

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="checkout.php">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="check_in" class="form-label">Check-in Date</label>
                            <input type="date" class="form-control" id="check_in" name="check_in"
                                value="<?= htmlspecialchars($checkIn) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="check_out" class="form-label">Check-out Date</label>
                            <input type="date" class="form-control" id="check_out" name="check_out"
                                value="<?= htmlspecialchars($checkOut) ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="room_type" class="form-label">Room Type</label>
                            <select class="form-select" id="room_type" name="room_type">
                                <option value="">All Types</option>
                                <?php foreach ($roomTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type['RoomType']) ?>"
                                        <?= $selectedType === $type['RoomType'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['RoomType']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Search Rooms
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php foreach ($error as $msg): ?>
                    <p class="mb-0"><?= $msg ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Available Rooms -->
        <?php if (!empty($availableRooms)): ?>
            <div class="row">
                <div class="col-12 mb-4">
                    <h4><?= count($availableRooms) ?> Rooms Available</h4>
                    <p class="text-muted">
                        <?= date('M j, Y', strtotime($checkIn)) ?> - <?= date('M j, Y', strtotime($checkOut)) ?>
                        (<?= $nights = (new DateTime($checkIn))->diff(new DateTime($checkOut))->days ?> nights)
                    </p>
                </div>

                <?php foreach ($availableRooms as $room):
                    $totalPrice = $room['BasePrice'] * $nights;
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="room-card h-100">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4 class="mb-1"><?= htmlspecialchars($room['RoomType']) ?></h4>
                                    <p class="text-muted mb-0">Room #<?= htmlspecialchars($room['RoomNumber']) ?></p>
                                </div>
                                <div class="price-display">
                                    $<?= number_format($totalPrice, 2) ?>
                                    <div class="night-count">$<?= number_format($room['BasePrice'], 2) ?>/night</div>
                                </div>
                            </div>

                            <?php if (!empty($room['Description'])): ?>
                                <p class="text-muted mb-3"><?= htmlspecialchars($room['Description']) ?></p>
                            <?php endif; ?>

                            <div class="d-grid">
                                <a href="./booking.php?room_id=<?= $room['RoomID'] ?>&check_in=<?= $checkIn ?>&check_out=<?= $checkOut ?>"
                                    class="btn btn-success">
                                    <i class="fas fa-bookmark me-2"></i>Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Date validation
        const today = new Date().toISOString().split('T')[0];
        const checkInEl = document.getElementById('check_in');
        const checkOutEl = document.getElementById('check_out');

        // Set initial min dates
        checkInEl.min = today;
        checkOutEl.min = today;

        // Update check-out min when check-in changes
        checkInEl.addEventListener('change', function() {
            checkOutEl.min = this.value;
            if (new Date(checkOutEl.value) < new Date(this.value)) {
                checkOutEl.value = this.value;
            }
        });

        // Update check-in max when check-out changes
        checkOutEl.addEventListener('change', function() {
            checkInEl.max = this.value;
        });
    </script>
</body>

</html>
<?php $conn->close(); ?>