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
$roomId = isset($_GET['room']) ? intval($_GET['room']) : 0;
$checkIn = $_GET['check_in'] ?? '';
$checkOut = $_GET['check_out'] ?? '';

// Validate initial parameters
if (!$roomId || !$checkIn || !$checkOut) {
    header("Location: new_booking.php");
    exit();
}

try {
    // Get room details and verify availability
    $stmt = $conn->prepare("SELECT r.* FROM Rooms r
                          WHERE r.RoomID = ?
                          AND r.AvailabilityStatus = 'Available'
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome@6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #ecf0f1;
            --accent: #3498db;
            --success: #27ae60;
        }

        .booking-summary {
            background: var(--secondary);
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .package-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .package-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .price-highlight {
            font-size: 1.5rem;
            color: var(--success);
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="booking-summary">
                    <h2 class="mb-4"><i class="fas fa-receipt"></i> Complete Your Booking</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php else: ?>
                        <!-- Booking Details -->
                        <div class="mb-4">
                            <h4>Room Details</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Room Number:</strong> <?= $roomDetails['RoomNumber'] ?></p>
                                    <p class="mb-1"><strong>Type:</strong> <?= $roomDetails['RoomType'] ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Check-in:</strong> <?= date('M j, Y', strtotime($checkIn)) ?></p>
                                    <p class="mb-1"><strong>Check-out:</strong> <?= date('M j, Y', strtotime($checkOut)) ?></p>
                                    <p class="mb-1"><strong>Nights:</strong> <?= $nights ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Form -->
                        <form method="POST">
                            <div class="mb-4">
                                <h4>Guest Information</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Number of Guests</label>
                                        <select class="form-select" name="guests" required>
                                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                                <option value="<?= $i ?>" <?= isset($_POST['guests']) && $_POST['guests'] == $i ? 'selected' : '' ?>>
                                                    <?= $i ?> Guest<?= $i > 1 ? 's' : '' ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($packages)): ?>
                            <div class="mb-4">
                                <h4>Additional Packages</h4>
                                <div class="row g-3">
                                    <?php foreach ($packages as $package): ?>
                                        <div class="col-md-6">
                                            <div class="package-card">
                                                <div class="form-check">
                                                    <input class="form-check-input package-checkbox" 
                                                           type="checkbox" 
                                                           name="packages[]" 
                                                           value="<?= $package['PackageID'] ?>" 
                                                           id="package<?= $package['PackageID'] ?>"
                                                           data-price="<?= $package['Price'] ?>">
                                                    <label class="form-check-label w-100" for="package<?= $package['PackageID'] ?>">
                                                        <div class="d-flex justify-content-between">
                                                            <div>
                                                                <strong><?= $package['PackageName'] ?></strong>
                                                                <p class="mb-0 text-muted"><?= $package['Description'] ?></p>
                                                            </div>
                                                            <span class="text-success">+$<?= number_format($package['Price'], 2) ?></span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="mb-4">
                                <h4>Payment Summary</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2">Base Price (<?= $nights ?> nights):</p>
                                        <p class="mb-2">Additional Packages:</p>
                                        <hr>
                                        <p class="mb-2"><strong>Total Price:</strong></p>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <p class="mb-2">$<?= number_format($basePrice, 2) ?></p>
                                        <p class="mb-2" id="package-total">$0.00</p>
                                        <hr>
                                        <p class="mb-2 price-highlight" id="total-price">$<?= number_format($totalPrice, 2) ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-credit-card"></i> Confirm Booking
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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