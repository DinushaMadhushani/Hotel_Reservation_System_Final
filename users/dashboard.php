<?php
session_start();
require '../config/db.con.php';

// Authentication check
if (!isset($_SESSION['UserType']) || $_SESSION['UserType'] !== 'Customer') {
    header("Location: ../auth/login.php");
    exit();
}

// Get customer data
$userId = $_SESSION['UserID'];
$customerQuery = "SELECT * FROM Users WHERE UserID = ?";
$stmt = $conn->prepare($customerQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$imgname = $customer['Email'];
?>

<?php
// Profile Image Handling
$userType = $_SESSION['UserType'];  // From session (Admin/Customer/Staff)
$email = $customer['Email'];        // From database

// Define valid role folders
$roleFolders = [
    'Admin' => '../assets/images/Admin/dp/',
    'Customer' => '../assets/images/Customer/dp/',
    'Staff' => '../assets/images/Staff/dp/'
];

$imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$imageFound = false;

// Set base path based on user type
$basePath = $roleFolders[$userType] ?? '../assets/images/default/';
$basePath = rtrim($basePath, '/') . '/';

// 1. First check for email-based image
foreach ($imageExtensions as $ext) {
    $safeEmail = basename($email); // Prevent directory traversal
    $safeEmail = str_replace(['.', '@'], ['.', '_at_'], $safeEmail);

    $testPath = $basePath . $safeEmail . '.' . $ext;

    // die($testPath);


    if (file_exists($testPath)) {
        $imagePath = $testPath;
        $imageFound = true;
        break;
    }
}

// 2. If not found, check for role-specific default.jpg
if (!$imageFound) {
    $roleDefault = $basePath . 'default.jpg';
    if (file_exists($roleDefault)) {
        $imagePath = $roleDefault;
        $imageFound = true;
    }
}

// 3. Final fallback to global default
if (!$imageFound) {
    $imagePath = '../assets/images/default/default_profile.jpg';
}

// Add cache buster for non-global images
if ($imageFound) {
    $imagePath .= '?v=' . time();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
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
    <!-- Welcome Section -->
    <div class="mb-8" data-aos="fade-up">
        <div class="text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-primary mb-2">Welcome Back, <span class="text-accent"><?= htmlspecialchars($customer['FullName']) ?></span>!</h2>
            <p class="text-gray-600 text-lg">Your travel experience matters to us</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 mb-10">
    <!-- Profile Card -->
    <div class="md:col-span-4 group">
        <div class="bg-gray-900 rounded-2xl shadow-lg overflow-hidden transform transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 border border-gray-700" data-aos="fade-up">
            <!-- Profile Header -->
            <div class="p-6 text-center border-b border-gray-700 relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity">
                    <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-gold to-transparent transform rotate-45 translate-y-10"></div>
                </div>

                <!-- Profile Image -->
                <div class="mb-6 relative inline-block group/image">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-accent/30 p-1 group-hover:border-accent/70 transition-all duration-500">
                        <img src="<?= $imagePath ?>" 
                            alt="<?= htmlspecialchars($customer['FullName']) ?> Profile"
                            class="w-full h-full object-cover rounded-full transition-transform duration-700 group-hover/image:scale-110">
                    </div>
                    <!-- Image glow effect -->
                    <div class="absolute -inset-1 rounded-full bg-accent opacity-0 group-hover/image:opacity-20 blur-md transition-opacity duration-500"></div>
                </div>

                <!-- User Info -->
                <h3 class="text-2xl font-bold text-white mb-1 group-hover:text-accent transition-colors"><?= htmlspecialchars($customer['FullName']) ?></h3>
                <p class="text-accent font-semibold flex items-center justify-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-accent animate-pulse"></span>
                    Premium Member
                </p>
            </div>

            <!-- Profile Details -->
            <div class="p-6 bg-gray-800/50 backdrop-blur-sm">
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div class="group/email relative ">
                        <p class="text-xs text-white mb-1">Email</p>
                        <div class="flex items-center gap-2 group-hover/email:text-accent transition-colors">
                            <i class="fas fa-envelope text-white-500 group-hover/email:text-accent transition-colors"></i>
                            <p class="text-sm text-white font-medium truncate" title="<?= htmlspecialchars($customer['Email']) ?>"><?= htmlspecialchars($customer['Email']) ?></p>
                        </div>
                    </div>
                    
                    <div class="group/phone">
                        <p class="text-xs text-white mb-1">Phone</p>
                        <div class="flex items-center gap-2 group-hover/phone:text-accent transition-colors">
                            <i class="fas fa-phone text-white-500 group-hover/phone:text-accent transition-colors"></i>
                            <p class="text-sm text-white font-medium"><?= htmlspecialchars($customer['PhoneNumber']) ?? 'N/A' ?></p>
                        </div>
                    </div>
                </div>

                <!-- Membership Info -->
                <div class="pt-4 border-t border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Member Since</p>
                            <p class="text-white font-medium"><?= date('M Y', strtotime($customer['CreatedAt'])) ?></p>
                        </div>
                        <div class="text-accent">
                            <i class="fas fa-crown fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="md:col-span-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $bookingQuery = "SELECT COUNT(*) AS total_bookings, 
                            SUM(CASE WHEN BookingStatus = 'Confirmed' THEN 1 ELSE 0 END) AS upcoming_stays
                            FROM Bookings WHERE UserID = ?";
            $stmt = $conn->prepare($bookingQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stats = $stmt->get_result()->fetch_assoc();
            ?>
            
            <!-- Upcoming Stays Card -->
            <div class="group/card bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-lg p-6 text-center transform transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 overflow-hidden relative" data-aos="fade-up" data-aos-delay="100">
                <!-- Background decoration -->
                <div class="absolute inset-0 bg-gradient-to-r from-accent/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>
                
                <!-- Icon Container -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="text-accent text-4xl mb-4 transform transition-transform duration-500 group-hover/card:scale-110">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    
                    <h3 class="text-white text-lg font-medium mb-3 group-hover/card:text-accent transition-colors">Upcoming Stays</h3>
                    
                    <p class="text-5xl font-bold text-accent mb-2 group-hover/card:animate-pulse"><?= $stats['upcoming_stays'] ?? 0 ?></p>
                    
                    <p class="text-xs text-gray-400 group-hover/card:text-gray-300 transition-colors">Confirmed reservations</p>
                    
                    <!-- Decorative corner elements -->
                    <div class="absolute top-0 right-0 w-12 h-12 opacity-10 group-hover/card:opacity-30 transition-opacity">
                        <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-accent"></div>
                    </div>
                </div>
            </div>
            
            <!-- Completed Stays Card -->
            <div class="group/card bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-lg p-6 text-center transform transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 overflow-hidden relative" data-aos="fade-up" data-aos-delay="150">
                <!-- Background decoration -->
                <div class="absolute inset-0 bg-gradient-to-l from-accent/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative z-10 flex flex-col items-center">
                    <div class="text-accent text-4xl mb-4 transform transition-transform duration-500 group-hover/card:scale-110">
                        <i class="fas fa-history"></i>
                    </div>
                    
                    <h3 class="text-white text-lg font-medium mb-3 group-hover/card:text-accent transition-colors">Completed Stays</h3>
                    
                    <p class="text-5xl font-bold text-accent mb-2 group-hover/card:animate-pulse"><?= ($stats['total_bookings'] - $stats['upcoming_stays']) ?? 0 ?></p>
                    
                    <p class="text-xs text-gray-400 group-hover/card:text-gray-300 transition-colors">Past reservations</p>
                    
                    <div class="absolute top-0 right-0 w-12 h-12 opacity-10 group-hover/card:opacity-30 transition-opacity">
                        <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-accent"></div>
                    </div>
                </div>
            </div>
            
            <!-- Pending Requests Card -->
            <div class="group/card bg-gradient-to-br from-primary to-primary-dark rounded-xl shadow-lg p-6 text-center transform transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 overflow-hidden relative" data-aos="fade-up" data-aos-delay="200">
                <!-- Background decoration -->
                <div class="absolute inset-0 bg-gradient-to-r from-accent/10 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500"></div>
                
                <div class="relative z-10 flex flex-col items-center">
                    <div class="text-accent text-4xl mb-4 transform transition-transform duration-500 group-hover/card:scale-110">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    
                    <h3 class="text-white text-lg font-medium mb-3 group-hover/card:text-accent transition-colors">Pending Requests</h3>
                    
                    <p class="text-5xl font-bold text-accent mb-2 group-hover/card:animate-pulse">2</p>
                    
                    <p class="text-xs text-gray-400 group-hover/card:text-gray-300 transition-colors">Awaiting confirmation</p>
                    
                    <div class="absolute top-0 right-0 w-12 h-12 opacity-10 group-hover/card:opacity-30 transition-opacity">
                        <div class="absolute top-0 right-0 w-6 h-6 border-t-2 border-r-2 border-accent"></div>
                    </div>
                </div>
            </div>
            </div>
            <!-- Make Reservation Button Section -->
            <div class="flex justify-center items-center my-12" data-aos="fade-up" data-aos-delay="250">
                <a href="checkout.php" class="group relative inline-flex items-center justify-center px-10 py-5 text-xl font-bold text-white transition-all duration-500 bg-gradient-to-r from-primary to-primary-dark rounded-xl shadow-xl hover:shadow-2xl overflow-hidden transform hover:-translate-y-2">
                    <span class="absolute inset-0 w-full h-full bg-gradient-to-br from-accent to-accent/70 opacity-0 group-hover:opacity-100 transition-opacity duration-500 transform group-hover:scale-105"></span>
                    <span class="absolute -inset-px bg-gradient-to-r from-accent/30 to-transparent opacity-0 group-hover:opacity-100 blur-md transition-all duration-500"></span>
                    <span class="absolute top-0 left-0 w-full h-full bg-black opacity-0 group-hover:opacity-5"></span>
                    <span class="relative flex items-center gap-3 z-10 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-calendar-plus text-2xl text-accent group-hover:text-white transition-colors duration-300"></i>
                        <span class="text-accent group-hover:text-white transition-colors duration-300 tracking-wide">Make Reservation</span>
                    </span>
                </a>
            </div>
    </div>

</div>



<!-- Quick Actions Section -->
    <div class="mb-10" data-aos="fade-up">
        <h2 class="text-2xl font-bold text-primary text-center mb-6">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- New Booking Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden group transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-accent/10 flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-all duration-300">
                        <i class="fas fa-bed text-2xl text-accent group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-2">New Booking</h3>
                    <p class="text-gray-600 text-sm mb-4">Book a new stay with us</p>
                    <a href="checkout.php" class="inline-block px-6 py-2 bg-accent text-primary font-medium rounded-lg shadow-md hover:bg-accent/80 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                        Book Now
                    </a>
                </div>
            </div>
            
            <!-- Current Reservations Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden group transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="150">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-accent/10 flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-all duration-300">
                        <i class="fas fa-clipboard-list text-2xl text-accent group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-2">Current Reservations</h3>
                    <p class="text-gray-600 text-sm mb-4">Manage your bookings</p>
                    <a href="manage_bookings.php" class="inline-block px-6 py-2 bg-accent text-primary font-medium rounded-lg shadow-md hover:bg-accent/80 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                        View
                    </a>
                </div>
            </div>
            
            <!-- Service Requests Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden group transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-accent/10 flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-all duration-300">
                        <i class="fas fa-concierge-bell text-2xl text-accent group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-2">Service Requests</h3>
                    <p class="text-gray-600 text-sm mb-4">Manage your services</p>
                    <a href="manage_services.php" class="inline-block px-6 py-2 bg-accent text-primary font-medium rounded-lg shadow-md hover:bg-accent/80 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                        Manage
                    </a>
                </div>
            </div>
            
            <!-- Profile Settings Card -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden group transform transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" data-aos="fade-up" data-aos-delay="250">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-accent/10 flex items-center justify-center mb-4 group-hover:bg-accent/20 transition-all duration-300">
                        <i class="fas fa-user-cog text-2xl text-accent group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-primary mb-2">Profile Settings</h3>
                    <p class="text-gray-600 text-sm mb-4">Update your information</p>
                    <a href="profile_management.php" class="inline-block px-6 py-2 bg-accent text-primary font-medium rounded-lg shadow-md hover:bg-accent/80 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1">
                        Update
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Section -->
    <div class="mb-10" data-aos="fade-up">
        <h2 class="text-2xl font-bold text-primary text-center mb-6">Recent Bookings</h2>
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-6 py-4">Room</th>
                            <th class="px-6 py-4">Check-in</th>
                            <th class="px-6 py-4">Check-out</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $bookingQuery = "SELECT b.*, r.RoomNumber FROM Bookings b
                                        JOIN Rooms r ON b.RoomID = r.RoomID
                                        WHERE UserID = ? ORDER BY CreatedAt DESC LIMIT 3";
                        $stmt = $conn->prepare($bookingQuery);
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $bookings = $stmt->get_result();

                        while ($booking = $bookings->fetch_assoc()):
                            $statusColor = $booking['BookingStatus'] === 'Confirmed' ? 'bg-green-100 text-green-800' : 
                                          ($booking['BookingStatus'] === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                        ?>
                        <tr class="border-b hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium">Room <?= htmlspecialchars($booking['RoomNumber']) ?></td>
                            <td class="px-6 py-4"><?= date('M d, Y', strtotime($booking['CheckInDate'])) ?></td>
                            <td class="px-6 py-4"><?= date('M d, Y', strtotime($booking['CheckOutDate'])) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusColor ?>">
                                    <?= $booking['BookingStatus'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="booking_details.php?id=<?= $booking['BookingID'] ?>" class="text-accent hover:text-accent/80 transition-colors duration-200">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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