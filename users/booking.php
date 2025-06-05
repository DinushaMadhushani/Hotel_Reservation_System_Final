<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['UserID'];
$error = $success = '';
$roomDetails = [];
$packages = [];
$totalPrice = 0;

// Get booking parameters from URL
$roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;
$checkIn = $_GET['check_in'] ?? '';
$checkOut = $_GET['check_out'] ?? '';

// Validate initial parameters
if (!$roomId || !$checkIn || !$checkOut) {
    header("Location: checkout.php");
    exit();
}

try {
    // Get room details and verify availability
    $stmt = $conn->prepare("SELECT r.* FROM Rooms r
                          WHERE r.RoomID = ?
                          AND r.AvailabilityStatus = 'Available' OR r.AvailabilityStatus = 'occupied'
                          AND r.RoomID NOT IN (
                              SELECT b.RoomID FROM Bookings b
                              WHERE b.CheckInDate < ? 
                              AND b.CheckOutDate > ?
                          )");
    $stmt->bind_param("iss", $roomId, $checkOut, $checkIn);
    $stmt->execute();
    $roomDetails = $stmt->get_result()->fetch_assoc();

    if (!$roomDetails) {
        throw new Exception("Selected room is no longer available");
    }

    // Get all packages
    $stmt = $conn->prepare("SELECT * FROM Packages");
    $stmt->execute();
    $packages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Calculate base price
    $checkInDate = new DateTime($checkIn);
    $checkOutDate = new DateTime($checkOut);
    $nights = $checkInDate->diff($checkOutDate)->days;
    $basePrice = $roomDetails['BasePrice'] * $nights;
    $totalPrice = $basePrice;
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guests = intval($_POST['guests']);
    $selectedPackages = $_POST['packages'] ?? [];

    try {
        // Validate inputs
        if ($guests < 1 || $guests > 4) {
            throw new Exception("Invalid number of guests");
        }

        // Verify room availability again
        $stmt = $conn->prepare("SELECT RoomID FROM Rooms 
                              WHERE RoomID = ? 
                              AND AvailabilityStatus = 'Available'");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();

        if (!$stmt->get_result()->num_rows) {
            throw new Exception("Room is no longer available");
        }

        // Calculate total price with packages
        $packageTotal = 0;
        if (!empty($selectedPackages)) {
            $packageIds = array_map('intval', $selectedPackages);
            $placeholders = implode(',', array_fill(0, count($packageIds), '?'));

            $stmt = $conn->prepare("SELECT SUM(Price) AS total FROM Packages 
                                   WHERE PackageID IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($packageIds)), ...$packageIds);
            $stmt->execute();
            $packageTotal = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
        }

        $totalPrice = $basePrice + $packageTotal;

        // Create booking
        $conn->begin_transaction();

        // Insert booking
        $stmt = $conn->prepare("INSERT INTO Bookings 
                               (UserID, RoomID, CheckInDate, CheckOutDate, NumberOfGuests, BookingStatus)
                               VALUES (?, ?, ?, ?, ?, 'Confirmed')");
        $stmt->bind_param("iissi", $userId, $roomId, $checkIn, $checkOut, $guests);
        $stmt->execute();
        $bookingId = $conn->insert_id;

        // Insert packages
        if (!empty($selectedPackages)) {
            foreach ($selectedPackages as $packageId) {
                $stmt = $conn->prepare("INSERT INTO BookingPackages (BookingID, PackageID)
                                       VALUES (?, ?)");
                $stmt->bind_param("ii", $bookingId, $packageId);
                $stmt->execute();
            }
        }

        // Update room status
        $stmt = $conn->prepare("UPDATE Rooms SET AvailabilityStatus = 'Occupied' WHERE RoomID = ?");
        $stmt->bind_param("i", $roomId);
        $stmt->execute();

        $conn->commit();
        $success = "Booking confirmed! Booking ID: $bookingId, Total Price: $" . number_format($totalPrice, 2);
    } catch (Exception $e) {
        $conn->rollback();
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
    <title>Complete Booking - Hotel System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<?php include '../includes/user_header.php'; ?>

<body class="bg-light">
    <div class="container mx-auto px-4 py-12 max-w-5xl">
        <div class="flex justify-center">
            <div class="w-full">
                <div class="bg-white rounded-lg shadow-custom overflow-hidden" data-aos="fade-up">
                    <!-- Decorative header -->
                    <div class="bg-gradient-to-r from-primary to-primary-light p-6 text-white relative overflow-hidden">
                        <div class="absolute -right-12 -top-12 w-32 h-32 bg-accent rounded-full opacity-20"></div>
                        <div class="absolute -left-12 -bottom-12 w-24 h-24 bg-accent rounded-full opacity-10"></div>
                        <h2 class="text-2xl font-bold relative z-10 flex items-center">
                            <i class="fas fa-receipt mr-3"></i> Complete Your Booking
                        </h2>
                    </div>

                    <div class="p-6">
                        <?php if ($error): ?>
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded animate-fade-in" data-aos="fade-up">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded animate-fade-in" data-aos="fade-up">
                                <?= $success ?>
                                <div class="mt-4">
                                    <a href="./manage_bookings.php" class="inline-block bg-accent hover:bg-accent-dark text-primary font-bold py-2 px-4 rounded transition duration-300 transform hover:scale-105">
                                        <i class="fas fa-list-ul mr-2"></i>View My Bookings
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Booking Details -->
                            <div class="mb-8" data-aos="fade-up" data-aos-delay="100">
                                <h4 class="text-xl font-bold text-primary mb-4 border-b border-gray-200 pb-2">Room Details</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="mb-2 flex items-center">
                                            <span class="font-semibold w-32">Room Number:</span> 
                                            <span class="text-primary-light"><?= $roomDetails['RoomNumber'] ?></span>
                                        </p>
                                        <p class="mb-2 flex items-center">
                                            <span class="font-semibold w-32">Type:</span> 
                                            <span class="text-primary-light"><?= $roomDetails['RoomType'] ?></span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="mb-2 flex items-center">
                                            <span class="font-semibold w-32">Check-in:</span> 
                                            <span class="text-primary-light"><?= date('M j, Y', strtotime($checkIn)) ?></span>
                                        </p>
                                        <p class="mb-2 flex items-center">
                                            <span class="font-semibold w-32">Check-out:</span> 
                                            <span class="text-primary-light"><?= date('M j, Y', strtotime($checkOut)) ?></span>
                                        </p>
                                        <p class="mb-2 flex items-center">
                                            <span class="font-semibold w-32">Nights:</span> 
                                            <span class="text-primary-light"><?= $nights ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Form -->
                            <form method="POST">
                                <div class="mb-8" data-aos="fade-up" data-aos-delay="150">
                                    <h4 class="text-xl font-bold text-primary mb-4 border-b border-gray-200 pb-2">Guest Information</h4>
                                    <div>
                                        <label class="block text-sm font-medium text-primary mb-2">Number of Guests</label>
                                        <select class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition duration-300"
                                            name="guests" required>
                                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                                <option value="<?= $i ?>" <?= isset($_POST['guests']) && $_POST['guests'] == $i ? 'selected' : '' ?>>
                                                    <?= $i ?> Guest<?= $i > 1 ? 's' : '' ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>

                                <?php if (!empty($packages)): ?>
                                    <div class="mb-8" data-aos="fade-up" data-aos-delay="200">
                                        <h4 class="text-xl font-bold text-primary mb-4 border-b border-gray-200 pb-2">Additional Packages</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <?php foreach ($packages as $index => $package): ?>
                                                <div data-aos="fade-up" data-aos-delay="<?= 250 + ($index * 50) ?>">
                                                    <div class="border border-gray-200 rounded-lg p-4 transition duration-300 transform hover:scale-105 hover:shadow-accent hover:border-accent group">
                                                        <label class="flex items-start space-x-3 cursor-pointer w-full">
                                                            <input class="form-checkbox h-5 w-5 text-accent rounded border-gray-300 focus:ring-accent mt-1 package-checkbox"
                                                                type="checkbox"
                                                                name="packages[]"
                                                                value="<?= $package['PackageID'] ?>"
                                                                id="package<?= $package['PackageID'] ?>"
                                                                data-price="<?= $package['Price'] ?>">
                                                            <div class="flex-1">
                                                                <div class="flex justify-between">
                                                                    <span class="font-bold text-primary group-hover:text-accent transition duration-300">
                                                                        <?= $package['PackageName'] ?>
                                                                    </span>
                                                                    <span class="text-accent font-bold">
                                                                        +$<?= number_format($package['Price'], 2) ?>
                                                                    </span>
                                                                </div>
                                                                <p class="text-gray-600 text-sm mt-1"><?= $package['Description'] ?></p>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-8 bg-gray-50 p-6 rounded-lg" data-aos="fade-up" data-aos-delay="300">
                                    <h4 class="text-xl font-bold text-primary mb-4 border-b border-gray-200 pb-2">Payment Summary</h4>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <p class="mb-2">Base Price (<?= $nights ?> nights):</p>
                                            <p class="mb-2">Additional Packages:</p>
                                            <div class="border-t border-gray-200 my-2 pt-2"></div>
                                            <p class="mb-2 font-bold">Total Price:</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="mb-2">$<?= number_format($basePrice, 2) ?></p>
                                            <p class="mb-2" id="package-total">$0.00</p>
                                            <div class="border-t border-gray-200 my-2 pt-2"></div>
                                            <p class="mb-2 text-2xl font-bold text-accent" id="total-price">$<?= number_format($totalPrice, 2) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div data-aos="fade-up" data-aos-delay="350">
                                    <button type="submit" class="w-full bg-gradient-to-r from-accent to-accent-light text-primary font-bold py-3 px-4 rounded-md transition duration-300 transform hover:scale-105 hover:from-accent-light hover:to-accent flex items-center justify-center">
                                        <i class="fas fa-credit-card mr-2"></i> Confirm Booking
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
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
        
        // Real-time price calculation
        document.querySelectorAll('.package-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updatePrices);
        });

        function updatePrices() {
            let packageTotal = 0;
            document.querySelectorAll('.package-checkbox:checked').forEach(checkbox => {
                packageTotal += parseFloat(checkbox.dataset.price);
            });

            const basePrice = <?= $basePrice ?>;
            const totalPrice = basePrice + packageTotal;

            document.getElementById('package-total').textContent = `$${packageTotal.toFixed(2)}`;
            document.getElementById('total-price').textContent = `$${totalPrice.toFixed(2)}`;
        }
    </script>
</body>

</html>