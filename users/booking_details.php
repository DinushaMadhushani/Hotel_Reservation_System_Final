<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

$userId = $_SESSION['UserID'];
$error = '';
$booking = null;
$packages = [];

// Get booking ID from URL
$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validate booking ID
if (!$bookingId) {
    header("Location: dashboard.php");
    exit();
}

try {
    // Get booking details with room information
    $stmt = $conn->prepare("SELECT b.*, r.RoomNumber, r.RoomType, r.Description AS RoomDescription, r.BasePrice
                          FROM Bookings b
                          JOIN Rooms r ON b.RoomID = r.RoomID
                          WHERE b.BookingID = ? AND b.UserID = ?");
    $stmt->bind_param("ii", $bookingId, $userId);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    
    if (!$booking) {
        throw new Exception("Booking not found or you don't have permission to view it");
    }
    
    // Get packages for this booking
    $stmt = $conn->prepare("SELECT p.* 
                          FROM Packages p
                          JOIN BookingPackages bp ON p.PackageID = bp.PackageID
                          WHERE bp.BookingID = ?");
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $packages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Calculate total price
    $checkIn = new DateTime($booking['CheckInDate']);
    $checkOut = new DateTime($booking['CheckOutDate']);
    $nights = $checkIn->diff($checkOut)->days;
    $roomTotal = $booking['BasePrice'] * $nights;
    
    $packageTotal = 0;
    foreach ($packages as $package) {
        $packageTotal += $package['Price'];
    }
    
    $totalPrice = $roomTotal + $packageTotal;
    
} catch (Exception $e) {
    $error = $e->getMessage();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details - Hotel System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
     <link rel="stylesheet" href="../assets/css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a1a1a',
                        secondary: '#ffffff',
                        accent: '#d4af37',
                        light: '#f5f5f5',
                        dark: '#121212',
                        goldLight: '#f5e8c9',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        script: ['Dancing Script', 'cursive'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 1s ease-in-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'tilt': 'tilt 10s infinite linear',
                        'border-pulse': 'borderPulse 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        tilt: {
                            '0%, 100%': { transform: 'rotate(0deg)' },
                            '25%': { transform: 'rotate(1deg)' },
                            '75%': { transform: 'rotate(-1deg)' },
                        },
                        borderPulse: {
                            '0%': { 'border-color': 'rgba(212, 175, 55, 0.5)' },
                            '50%': { 'border-color': 'rgba(212, 175, 55, 1)' },
                            '100%': { 'border-color': 'rgba(212, 175, 55, 0.5)' },
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans bg-secondary text-dark min-h-screen">

<?php include '../includes/user_header.php'; ?>

<!-- Main Content -->
<div class="pt-24 px-4 md:px-8 lg:px-16 max-w-7xl mx-auto">
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" data-aos="fade-up">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
            <div class="mt-4">
                <a href="dashboard.php" class="inline-block px-4 py-2 bg-primary text-white rounded hover:bg-primary/80 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    <?php elseif ($booking): ?>
        <!-- Booking Details Card -->
        <div class="mb-8" data-aos="fade-up">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold text-primary">Booking Details</h2>
                <div class="flex space-x-2">
                    <a href="dashboard.php" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:bg-primary/80 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Dashboard
                    </a>
                    <a href="manage_bookings.php" class="inline-flex items-center px-4 py-2 bg-accent text-primary rounded hover:bg-accent/80 transition-colors">
                        <i class="fas fa-list mr-2"></i>All Bookings
                    </a>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <!-- Booking Header -->
                <div class="bg-primary p-6 text-white relative overflow-hidden">
                    <div class="absolute -right-12 -top-12 w-32 h-32 bg-accent rounded-full opacity-20"></div>
                    <div class="absolute -left-12 -bottom-12 w-24 h-24 bg-accent rounded-full opacity-10"></div>
                    
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center relative z-10">
                        <div>
                            <h3 class="text-2xl font-bold flex items-center">
                                <i class="fas fa-receipt mr-3 text-accent"></i>
                                Booking #<?= $booking['BookingID'] ?>
                            </h3>
                            <p class="text-gray-300 mt-1">Created on <?= date('F j, Y', strtotime($booking['CreatedAt'])) ?></p>
                        </div>
                        
                        <div class="mt-4 md:mt-0">
                            <span class="inline-block px-4 py-2 rounded-full text-sm font-bold
                                <?php 
                                switch(strtolower($booking['BookingStatus'])) {
                                    case 'confirmed':
                                        echo 'bg-green-500 text-white';
                                        break;
                                    case 'pending':
                                        echo 'bg-yellow-500 text-white';
                                        break;
                                    case 'cancelled':
                                        echo 'bg-red-500 text-white';
                                        break;
                                    case 'completed':
                                        echo 'bg-blue-500 text-white';
                                        break;
                                    default:
                                        echo 'bg-gray-500 text-white';
                                }
                                ?>"
                            >
                                <?= $booking['BookingStatus'] ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Content -->
                <div class="p-6">
                    <!-- Room Details Section -->
                    <div class="mb-8" data-aos="fade-up" data-aos-delay="100">
                        <h4 class="text-xl font-bold text-primary mb-4 border-b border-gray-200 pb-2 flex items-center">
                            <i class="fas fa-bed text-accent mr-2"></i> Room Details
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center mb-4">
                                    <div class="bg-accent/10 p-3 rounded-full mr-3">
                                        <i class="fas fa-door-open text-accent text-xl"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-bold text-primary">Room <?= htmlspecialchars($booking['RoomNumber']) ?></h5>
                                        <p class="text-gray-600"><?= htmlspecialchars($booking['RoomType']) ?></p>
                                    </div>
                                </div>
                                
                                <p class="text-gray-600 mb-4"><?= htmlspecialchars($booking['RoomDescription'] ?? 'No description available') ?></p>
                                
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Base Price per Night</span>
                                    <span class="font-bold text-accent">$<?= number_format($booking['BasePrice'], 2) ?></span>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-500 text-sm">Check-in Date</p>
                                        <p class="font-bold text-primary flex items-center mt-1">
                                            <i class="fas fa-calendar-check text-accent mr-2"></i>
                                            <?= date('M j, Y', strtotime($booking['CheckInDate'])) ?>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 text-sm">Check-out Date</p>
                                        <p class="font-bold text-primary flex items-center mt-1">
                                            <i class="fas fa-calendar-times text-accent mr-2"></i>
                                            <?= date('M j, Y', strtotime($booking['CheckOutDate'])) ?>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 text-sm">Number of Nights</p>
                                        <p class="font-bold text-primary flex items-center mt-1">
                                            <i class="fas fa-moon text-accent mr-2"></i>
                                            <?= $nights ?> night<?= $nights > 1 ? 's' : '' ?>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 text-sm">Number of Guests</p>
                                        <p class="font-bold text-primary flex items-center mt-1">
                                            <i class="fas fa-users text-accent mr-2"></i>
                                            <?= $booking['NumberOfGuests'] ?> guest<?= $booking['NumberOfGuests'] > 1 ? 's' : '' ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Packages Section -->
                    <?php if (!empty($packages)): ?>
                    <div class="mb-8" data-aos="fade-up" data-aos-delay="200">
                        <h4 class="text-xl font-bold text-primary mb-4 border-b border-gray-200 pb-2 flex items-center">
                            <i class="fas fa-gift text-accent mr-2"></i> Included Packages
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php foreach ($packages as $index => $package): ?>
                            <div class="bg-gray-50 p-4 rounded-lg" data-aos="fade-up" data-aos-delay="<?= 250 + ($index * 50) ?>">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h5 class="font-bold text-primary"><?= htmlspecialchars($package['PackageName']) ?></h5>
                                        <p class="text-gray-600 text-sm mt-1"><?= htmlspecialchars($package['Description']) ?></p>
                                    </div>
                                    <span class="font-bold text-accent">$<?= number_format($package['Price'], 2) ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Payment Summary Section -->
                    <div class="mb-8" data-aos="fade-up" data-aos-delay="300">
                        <h4 class="text-xl font-bold text-primary mb-4 border-b border-gray-200 pb-2 flex items-center">
                            <i class="fas fa-file-invoice-dollar text-accent mr-2"></i> Payment Summary
                        </h4>
                        
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                <div>
                                    <p class="text-gray-600">Room Charge (<?= $nights ?> nights):</p>
                                    <p class="text-gray-600">Package Charges:</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600">$<?= number_format($roomTotal, 2) ?></p>
                                    <p class="text-gray-600">$<?= number_format($packageTotal, 2) ?></p>
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-300 pt-4">
                                <div class="grid grid-cols-2 gap-2">
                                    <p class="font-bold text-primary">Total Amount:</p>
                                    <p class="text-right font-bold text-2xl text-accent">$<?= number_format($totalPrice, 2) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions Section -->
                    <div class="flex flex-col sm:flex-row justify-between gap-4" data-aos="fade-up" data-aos-delay="400">
                        <a href="manage_services.php" class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/80 transition-colors">
                            <i class="fas fa-concierge-bell mr-2"></i> Request Services
                        </a>
                        
                        <?php if ($booking['BookingStatus'] === 'Confirmed' || $booking['BookingStatus'] === 'Pending'): ?>
                        <form method="POST" action="manage_bookings.php" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                            <input type="hidden" name="booking_id" value="<?= $booking['BookingID'] ?>">
                            <button type="submit" name="cancel_booking" class="inline-flex items-center justify-center px-6 py-3 border-2 border-red-500 text-red-600 rounded-lg hover:bg-red-500 hover:text-white transition-colors w-full sm:w-auto">
                                <i class="fas fa-times-circle mr-2"></i> Cancel Booking
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Back to Top Button -->
<button id="back-to-top" class="fixed bottom-8 right-8 z-50 bg-accent text-primary w-12 h-12 rounded-full flex items-center justify-center shadow-lg transform transition-all duration-300 opacity-0 translate-y-10 hover:bg-accent/80 hover:scale-110">
    <i class="fas fa-arrow-up"></i>
</button>

<?php include '../includes/sub_footer.php'; ?>

<!-- Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: false,
        mirror: true
    });
    
    // Back to top button
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0', 'translate-y-10');
                backToTopButton.classList.add('opacity-100', 'translate-y-0');
            } else {
                backToTopButton.classList.remove('opacity-100', 'translate-y-0');
                backToTopButton.classList.add('opacity-0', 'translate-y-10');
            }
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
</body>

</html>