<?php
// Enable error reporting (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../config/db.con.php';

// Validate session and permissions
if (!isset($_SESSION['UserID'], $_SESSION['FullName'], $_SESSION['UserType']) || 
    $_SESSION['UserType'] !== 'Staff') {
    header("Location: ../auth/login.php");
    exit();
} 

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// Get staff schedule
$scheduleQuery = $conn->prepare("
    SELECT ScheduleID, ScheduleDate, StartTime, EndTime 
    FROM StaffSchedule 
    WHERE UserID = ? 
    ORDER BY ScheduleDate DESC
");
$scheduleQuery->bind_param("i", $_SESSION['UserID']);
$scheduleQuery->execute();
$scheduleResult = $scheduleQuery->get_result();

// Get current date and time for status comparison
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Schedule</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-light min-h-screen font-sans">

<?php include('../includes/staff_header.php'); ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-3xl font-bold mb-6 text-primary" data-aos="fade-down">My Schedule</h1>
            
            <div class="bg-white rounded-xl shadow-md overflow-hidden" data-aos="fade-up">
                <div class="bg-gradient-to-r from-primary to-accent p-4 text-white">
                    <i class="fas fa-calendar-alt mr-2"></i>Schedule Overview
                </div>
                <div class="p-4">
                    <?php if ($scheduleResult->num_rows > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-gray-700">Date</th>
                                        <th class="px-4 py-2 text-left text-gray-700">Start Time</th>
                                        <th class="px-4 py-2 text-left text-gray-700">End Time</th>
                                        <th class="px-4 py-2 text-left text-gray-700">Duration</th>
                                        <th class="px-4 py-2 text-left text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php while ($schedule = $scheduleResult->fetch_assoc()): 
                                        // Calculate shift status
                                        $scheduleDate = $schedule['ScheduleDate'];
                                        $startTime = $schedule['StartTime'];
                                        $endTime = $schedule['EndTime'];
                                        
                                        $status = '';
                                        $statusClass = '';
                                        
                                        if ($scheduleDate > $currentDate) {
                                            $status = 'Upcoming';
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                        } elseif ($scheduleDate < $currentDate) {
                                            $status = 'Completed';
                                            $statusClass = 'bg-green-100 text-green-800';
                                        } else { // Same day
                                            if ($currentTime < $startTime) {
                                                $status = 'Today';
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                            } elseif ($currentTime >= $startTime && $currentTime <= $endTime) {
                                                $status = 'Current';
                                                $statusClass = 'bg-purple-100 text-purple-800';
                                            } else {
                                                $status = 'Completed';
                                                $statusClass = 'bg-green-100 text-green-800';
                                            }
                                        }
                                        
                                        // Calculate duration
                                        $start = new DateTime($startTime);
                                        $end = new DateTime($endTime);
                                        $duration = $start->diff($end)->format('%h h %i m');
                                    ?>
                                    <tr class="hover:bg-gray-50 transition-colors duration-200" data-aos="fade-up" data-aos-delay="<?= $loop_count * 50 ?>">
                                        <td class="px-4 py-3"><?= date('M j, Y', strtotime($scheduleDate)) ?></td>
                                        <td class="px-4 py-3"><?= date('h:i A', strtotime($startTime)) ?></td>
                                        <td class="px-4 py-3"><?= date('h:i A', strtotime($endTime)) ?></td>
                                        <td class="px-4 py-3"><?= $duration ?></td>
                                        <td class="px-4 py-3">
                                            <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusClass ?>">
                                                <?= $status ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="bg-blue-50 text-blue-700 p-4 rounded-md" data-aos="fade-up">
                            <p class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                No schedule found. Please contact your supervisor for scheduling information.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-6 text-center" data-aos="fade-up" data-aos-delay="200">
                <a href="dashboard.php" class="inline-flex items-center text-accent hover:text-accent/80 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            easing: 'ease-in-out-quad',
            offset: 50
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>