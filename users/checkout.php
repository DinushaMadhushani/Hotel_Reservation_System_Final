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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<?php include '../includes/user_header.php'; ?>

<body class="bg-light">
    <div class="container mx-auto px-4 py-12 max-w-7xl">
        <h2 class="text-3xl font-bold mb-8 text-primary flex items-center" data-aos="fade-right">
            <i class="fas fa-search mr-3 text-accent"></i>Check Room Availability
        </h2>

        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-custom mb-8" data-aos="fade-up" data-aos-delay="100">
            <div class="p-6">
                <form method="GET" action="checkout.php">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="space-y-2">
                            <label for="check_in" class="block text-sm font-medium text-primary">Check-in Date</label>
                            <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition duration-300"
                                id="check_in" name="check_in" value="<?= htmlspecialchars($checkIn) ?>" required>
                        </div>
                        <div class="space-y-2">
                            <label for="check_out" class="block text-sm font-medium text-primary">Check-out Date</label>
                            <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition duration-300"
                                id="check_out" name="check_out" value="<?= htmlspecialchars($checkOut) ?>" required>
                        </div>
                        <div class="space-y-2">
                            <label for="room_type" class="block text-sm font-medium text-primary">Room Type</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition duration-300"
                                id="room_type" name="room_type">
                                <option value="">All Types</option>
                                <?php foreach ($roomTypes as $type): ?>
                                    <option value="<?= htmlspecialchars($type['RoomType']) ?>"
                                        <?= $selectedType === $type['RoomType'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['RoomType']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-primary font-bold py-2 px-4 rounded-md transition duration-300 transform hover:scale-105 hover:shadow-accent flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>Search Rooms
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Error Messages -->
        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded-md animate-fade-in" data-aos="fade-up" data-aos-delay="150">
                <?php foreach ($error as $msg): ?>
                    <p class="mb-0"><?= $msg ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Available Rooms -->
        <?php if (!empty($availableRooms)): ?>
            <div class="space-y-6">
                <div class="mb-6" data-aos="fade-up" data-aos-delay="200">
                    <h4 class="text-2xl font-bold text-primary"><?= count($availableRooms) ?> Rooms Available</h4>
                    <p class="text-gray-600">
                        <?= date('M j, Y', strtotime($checkIn)) ?> - <?= date('M j, Y', strtotime($checkOut)) ?>
                        (<?= $nights = (new DateTime($checkIn))->diff(new DateTime($checkOut))->days ?> nights)
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($availableRooms as $index => $room):
                        $totalPrice = $room['BasePrice'] * $nights;
                    ?>
                        <div class="h-full" data-aos="fade-up" data-aos-delay="<?= 250 + ($index * 50) ?>">
                            <div class="bg-white rounded-lg shadow-custom h-full transform transition duration-500 hover:scale-105 hover:shadow-accent relative overflow-hidden group">
                                <!-- Decorative elements -->
                                <div class="absolute -right-12 -top-12 w-24 h-24 bg-accent rounded-full opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
                                <div class="absolute -left-12 -bottom-12 w-24 h-24 bg-accent rounded-full opacity-10 group-hover:opacity-30 transition-opacity duration-500"></div>
                                
                                <div class="p-6 relative z-10">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="text-xl font-bold text-primary mb-1 group-hover:text-accent transition-colors duration-300">
                                                <?= htmlspecialchars($room['RoomType']) ?>
                                            </h4>
                                            <p class="text-gray-600 mb-0">Room #<?= htmlspecialchars($room['RoomNumber']) ?></p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xl font-bold text-accent">
                                                $<?= number_format($totalPrice, 2) ?>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                $<?= number_format($room['BasePrice'], 2) ?>/night
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!empty($room['Description'])): ?>
                                        <p class="text-gray-600 mb-6"><?= htmlspecialchars($room['Description']) ?></p>
                                    <?php endif; ?>

                                    <div class="mt-auto">
                                        <a href="./booking.php?room_id=<?= $room['RoomID'] ?>&check_in=<?= $checkIn ?>&check_out=<?= $checkOut ?>"
                                            class="block w-full bg-gradient-to-r from-accent to-accent-light text-primary font-bold py-3 px-4 rounded-md transition duration-300 transform hover:from-accent-light hover:to-accent text-center">
                                            <i class="fas fa-bookmark mr-2"></i>Book Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../includes/sub_footer.php'; ?>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out',
            once: true
        });
        
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