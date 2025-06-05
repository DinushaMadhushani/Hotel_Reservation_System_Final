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

// Handle task status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $taskId = filter_input(INPUT_POST, 'task_id', FILTER_SANITIZE_NUMBER_INT);
    $newStatus = filter_input(INPUT_POST, 'new_status', FILTER_SANITIZE_STRING);
    
    $updateStmt = $conn->prepare("UPDATE AssignedTasks SET TaskStatus = ? WHERE TaskID = ? AND StaffID = ?");
    $updateStmt->bind_param("sii", $newStatus, $taskId, $_SESSION['UserID']);
    $updateStmt->execute();
    $updateStmt->close();
}

// Get task statistics
$taskStats = $conn->query("
    SELECT 
        SUM(CASE WHEN TaskStatus = 'Completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN TaskStatus = 'Pending' THEN 1 ELSE 0 END) as pending
    FROM AssignedTasks 
    WHERE StaffID = {$_SESSION['UserID']}
");
$stats = $taskStats->fetch_assoc();
$completedTasks = $stats['completed'] ?? 0;
$pendingTasks = $stats['pending'] ?? 0;

// Get assigned rooms count
$assignedRooms = $conn->query("
    SELECT COUNT(DISTINCT b.RoomID) as rooms 
    FROM ServiceRequests sr
    JOIN Bookings b ON sr.BookingID = b.BookingID
    WHERE sr.UserID = {$_SESSION['UserID']}
")->fetch_assoc()['rooms'] ?? 0;

// Get staff schedule
$scheduleQuery = $conn->prepare("
    SELECT ScheduleDate, StartTime, EndTime 
    FROM StaffSchedule 
    WHERE UserID = ? AND ScheduleDate >= CURDATE()
    ORDER BY ScheduleDate ASC
");
$scheduleQuery->bind_param("i", $_SESSION['UserID']);
$scheduleQuery->execute();
$scheduleResult = $scheduleQuery->get_result();

// Get assigned tasks
$taskQuery = $conn->prepare("
    SELECT t.TaskID, sr.RequestType, sr.Description, t.TaskStatus, b.RoomID, r.RoomNumber
    FROM AssignedTasks t
    LEFT JOIN ServiceRequests sr ON t.RequestID = sr.RequestID
    LEFT JOIN Bookings b ON sr.BookingID = b.BookingID
    LEFT JOIN Rooms r ON b.RoomID = r.RoomID
    WHERE t.StaffID = ?
    ORDER BY t.AssignmentDateTime DESC
");
$taskQuery->bind_param("i", $_SESSION['UserID']);
$taskQuery->execute();
$taskResult = $taskQuery->get_result();
?>

<?php
// Get user data from database
$userQuery = $conn->prepare("SELECT * FROM Users WHERE UserID = ?");
$userQuery->bind_param("i", $_SESSION['UserID']);
$userQuery->execute();
$customer = $userQuery->get_result()->fetch_assoc();

// Profile Image Handling
$userType = $_SESSION['UserType'];  // From session (Admin/Customer/Staff)
$email = $customer['Email'] ?? '';  // From database with null check

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
    // Skip if email is empty
    if (empty($email)) {
        continue;
    }
    
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
    <title>Staff Dashboard</title>
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
                        'primary-dark': '#0f0f0f',
                        secondary: '#ffffff',
                        accent: '#d4af37',
                        'accent-light': '#e6c458',
                        'accent-dark': '#b39020',
                        light: '#f5f5f5',
                        dark: '#121212',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                    },
                }
            }
        }
    </script>
    <style>
        /* Custom progress bar animation */
        @keyframes progress {
            0% { width: 0; }
            100% { width: var(--progress-width); }
        }
        .progress-bar {
            animation: progress 1.5s ease-out forwards;
        }
        
        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        /* Table row hover animation */
        .table-row-hover {
            transition: all 0.2s ease;
        }
        .table-row-hover:hover {
            background-color: rgba(212, 175, 55, 0.1);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans">

<?php include('../includes/staff_header.php'); ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-primary to-primary-dark rounded-xl shadow-lg mb-8 overflow-hidden" data-aos="fade-down">
            <div class="flex flex-col md:flex-row items-center justify-between p-6">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl md:text-3xl font-bold text-white">Welcome back, <?= htmlspecialchars($_SESSION['FullName']) ?>!</h1>
                    <p class="text-gray-300 mt-2">Here's an overview of your tasks and schedule</p>
                </div>
                <div class="flex space-x-2">
                    <a href="./task.php" class="bg-accent hover:bg-accent-dark text-white font-medium py-2 px-4 rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-tasks mr-2"></i> View Tasks
                    </a>
                    <a href="./shedule.php" class="bg-white hover:bg-gray-100 text-primary font-medium py-2 px-4 rounded-lg transition-colors duration-300 flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i> Schedule
                    </a>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Analytics Cards -->
            <div class="col-span-1" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-white rounded-xl shadow-md h-full overflow-hidden card-hover border border-gray-100">
                    <div class="bg-gradient-to-r from-primary to-primary-dark p-4 text-white flex justify-between items-center">
                        <span class="font-medium"><i class="fas fa-tasks mr-2"></i>Total Tasks</span>
                        <span class="bg-white bg-opacity-20 rounded-full h-8 w-8 flex items-center justify-center">
                            <i class="fas fa-chart-pie text-white"></i>
                        </span>
                    </div>
                    <div class="p-6 flex flex-col">
                        <h2 class="text-4xl font-bold mb-2"><?= $completedTasks + $pendingTasks ?></h2>
                        <div class="text-sm text-gray-500">Assigned to you</div>
                        
                        <?php if(($completedTasks + $pendingTasks) > 0): ?>
                        <div class="mt-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs font-medium text-gray-500">Progress</span>
                                <span class="text-xs font-medium text-gray-500">
                                    <?= round(($completedTasks / ($completedTasks + $pendingTasks)) * 100) ?>%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-accent h-2.5 rounded-full progress-bar" style="--progress-width: <?= ($completedTasks / ($completedTasks + $pendingTasks)) * 100 ?>%"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-span-1" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-white rounded-xl shadow-md h-full overflow-hidden card-hover border border-gray-100">
                    <div class="bg-gradient-to-r from-green-600 to-green-700 p-4 text-white flex justify-between items-center">
                        <span class="font-medium"><i class="fas fa-check-circle mr-2"></i>Completed</span>
                        <span class="bg-white bg-opacity-20 rounded-full h-8 w-8 flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-white"></i>
                        </span>
                    </div>
                    <div class="p-6 flex flex-col">
                        <h2 class="text-4xl font-bold mb-2"><?= $completedTasks ?></h2>
                        <div class="text-sm text-gray-500">Tasks completed</div>
                        
                        <?php if(($completedTasks + $pendingTasks) > 0): ?>
                        <div class="mt-4 flex items-center">
                            <span class="text-sm font-medium text-green-600 mr-2">
                                <i class="fas fa-arrow-up"></i>
                            </span>
                            <span class="text-sm font-medium text-green-600">
                                <?= round(($completedTasks / ($completedTasks + $pendingTasks)) * 100) ?>% completion rate
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-span-1" data-aos="fade-up" data-aos-delay="300">
                <div class="bg-white rounded-xl shadow-md h-full overflow-hidden card-hover border border-gray-100">
                    <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 p-4 text-white flex justify-between items-center">
                        <span class="font-medium"><i class="fas fa-exclamation-circle mr-2"></i>Pending</span>
                        <span class="bg-white bg-opacity-20 rounded-full h-8 w-8 flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-white"></i>
                        </span>
                    </div>
                    <div class="p-6 flex flex-col">
                        <h2 class="text-4xl font-bold mb-2"><?= $pendingTasks ?></h2>
                        <div class="text-sm text-gray-500">Tasks awaiting completion</div>
                        
                        <?php if($pendingTasks > 0): ?>
                        <div class="mt-4 flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Requires attention
                            </span>
                        </div>
                        <?php else: ?>
                        <div class="mt-4 flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i> All caught up!
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-span-1" data-aos="fade-up" data-aos-delay="400">
                <div class="bg-white rounded-xl shadow-md h-full overflow-hidden card-hover border border-gray-100">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4 text-white flex justify-between items-center">
                        <span class="font-medium"><i class="fas fa-door-open mr-2"></i>Assigned Rooms</span>
                        <span class="bg-white bg-opacity-20 rounded-full h-8 w-8 flex items-center justify-center">
                            <i class="fas fa-bed text-white"></i>
                        </span>
                    </div>
                    <div class="p-6 flex flex-col">
                        <h2 class="text-4xl font-bold mb-2"><?= $assignedRooms ?></h2>
                        <div class="text-sm text-gray-500">Rooms under your care</div>
                        
                        <div class="mt-4 flex items-center">
                            <a href="./task.php" class="text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                <i class="fas fa-arrow-right mr-1"></i> View room assignments
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="col-span-1 lg:col-span-1" data-aos="fade-up" data-aos-delay="500">
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover border border-gray-100">
                    <div class="bg-gradient-to-r from-primary to-primary-dark p-4 text-white">
                        <div class="flex justify-between items-center">
                            <span class="font-medium"><i class="fas fa-user mr-2"></i>Staff Profile</span>
                            <a href="./profile_manage.php" class="text-xs bg-white bg-opacity-20 hover:bg-opacity-30 px-2 py-1 rounded transition-all duration-200">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                        </div>
                    </div>
                    <div class="p-6 text-center">
                        <div class="relative w-35 h-35 mx-auto mb-4 rounded-full border-4 border-accent overflow-hidden shadow-lg group">
                            <div class="absolute -inset-1 rounded-full  opacity-75 blur-sm group-hover:opacity-100 transition duration-300"></div>
                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($customer['FullName']) ?> Profile"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        </div>
                        <h4 class="text-xl font-bold mb-1"><?= htmlspecialchars($_SESSION['FullName']) ?></h4>
                        <p class="text-gray-500 mb-4 flex items-center justify-center">
                            <i class="fas fa-id-badge mr-2 text-accent"></i>Staff Member
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <div class="text-2xl font-bold text-accent"><?= $completedTasks ?></div>
                                <div class="text-xs text-gray-500 mt-1">Completed Tasks</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                <div class="text-2xl font-bold text-accent"><?= $scheduleResult->num_rows ?></div>
                                <div class="text-xs text-gray-500 mt-1">Upcoming Shifts</div>
                            </div>
                        </div>
                        
                        <a href="./profile_manage.php" class="mt-6 inline-flex items-center text-sm text-accent hover:text-accent-dark transition-colors duration-200">
                            <i class="fas fa-user-cog mr-2"></i> Manage Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Schedule Card -->
            <div class="col-span-1 lg:col-span-3" data-aos="fade-up" data-aos-delay="600">
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover border border-gray-100">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 p-4 text-white flex justify-between items-center">
                        <span class="font-medium"><i class="fas fa-calendar-alt mr-2"></i>Upcoming Schedule</span>
                        <a href="./shedule.php" class="text-xs bg-white bg-opacity-20 hover:bg-opacity-30 px-2 py-1 rounded transition-all duration-200">
                            <i class="fas fa-external-link-alt mr-1"></i> Full Schedule
                        </a>
                    </div>
                    <div class="p-4">
                        <?php if ($scheduleResult->num_rows > 0): ?>
                            <div class="overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-600 text-sm leading-normal">
                                            <th class="py-3 px-4 text-left font-semibold">Date</th>
                                            <th class="py-3 px-4 text-left font-semibold">Start Time</th>
                                            <th class="py-3 px-4 text-left font-semibold">End Time</th>
                                            <th class="py-3 px-4 text-left font-semibold">Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $today = date('Y-m-d');
                                        $scheduleResult->data_seek(0); // Reset pointer
                                        while ($schedule = $scheduleResult->fetch_assoc()): 
                                            $isToday = $schedule['ScheduleDate'] === $today;
                                        ?>
                                        <tr class="table-row-hover border-b border-gray-200 text-sm">
                                            <td class="py-3 px-4">
                                                <div class="flex items-center">
                                                    <div class="mr-2">
                                                        <?php if($isToday): ?>
                                                            <span class="flex h-2.5 w-2.5 relative">
                                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span>
                                                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-accent"></span>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="h-2.5 w-2.5 rounded-full bg-gray-300 inline-block"></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <span class="<?= $isToday ? 'font-semibold text-accent' : '' ?>">
                                                        <?= date('M j, Y', strtotime($schedule['ScheduleDate'])) ?>
                                                        <?= $isToday ? ' <span class="text-xs font-medium bg-accent/10 text-accent px-1.5 py-0.5 rounded">Today</span>' : '' ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="bg-blue-50 text-blue-700 py-1 px-2 rounded text-xs font-medium">
                                                    <i class="far fa-clock mr-1"></i>
                                                    <?= date('h:i A', strtotime($schedule['StartTime'])) ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="bg-purple-50 text-purple-700 py-1 px-2 rounded text-xs font-medium">
                                                    <i class="far fa-clock mr-1"></i>
                                                    <?= date('h:i A', strtotime($schedule['EndTime'])) ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <?php
                                                $start = new DateTime($schedule['StartTime']);
                                                $end = new DateTime($schedule['EndTime']);
                                                $interval = $start->diff($end);
                                                $hours = $interval->h;
                                                $minutes = $interval->i;
                                                
                                                // Calculate total minutes for width percentage
                                                $totalMinutes = ($hours * 60) + $minutes;
                                                $widthPercentage = min(100, max(10, ($totalMinutes / 480) * 100)); // 8 hours = 480 minutes as max
                                                ?>
                                                <div class="flex items-center">
                                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mr-2">
                                                        <div class="bg-indigo-500 h-1.5 rounded-full" style="width: <?= $widthPercentage ?>%"></div>
                                                    </div>
                                                    <span class="text-gray-600 whitespace-nowrap">
                                                        <?= $hours ?>h <?= $minutes ?>m
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="bg-blue-50 border border-blue-100 text-blue-700 p-4 rounded-md flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                <span>No upcoming schedule found. Check back later for updates.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Task Management -->
            <div class="col-span-1 lg:col-span-4" data-aos="fade-up" data-aos-delay="700">
                <div class="bg-white rounded-xl shadow-md overflow-hidden card-hover border border-gray-100">
                    <div class="bg-gradient-to-r from-primary to-primary-dark p-4 text-white flex justify-between items-center">
                        <span class="font-medium"><i class="fas fa-clipboard-list mr-2"></i>Task Management</span>
                        <a href="./task.php" class="text-xs bg-white bg-opacity-20 hover:bg-opacity-30 px-2 py-1 rounded transition-all duration-200">
                            <i class="fas fa-external-link-alt mr-1"></i> All Tasks
                        </a>
                    </div>
                    <div class="p-4">
                        <?php if ($taskResult->num_rows > 0): ?>
                            <!-- Task Summary -->
                            <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 flex items-center">
                                    <div class="bg-yellow-100 p-3 rounded-full mr-3">
                                        <i class="fas fa-hourglass-half text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Pending</div>
                                        <div class="text-xl font-bold"><?= $pendingTasks ?></div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 flex items-center">
                                    <div class="bg-green-100 p-3 rounded-full mr-3">
                                        <i class="fas fa-check-circle text-green-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Completed</div>
                                        <div class="text-xl font-bold"><?= $completedTasks ?></div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 flex items-center">
                                    <div class="bg-blue-100 p-3 rounded-full mr-3">
                                        <i class="fas fa-percentage text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500">Completion Rate</div>
                                        <div class="text-xl font-bold">
                                            <?= ($completedTasks + $pendingTasks) > 0 ? round(($completedTasks / ($completedTasks + $pendingTasks)) * 100) : 0 ?>%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead>
                                        <tr class="bg-gray-50 text-gray-600 text-sm leading-normal">
                                            <th class="py-3 px-4 text-left font-semibold">Task ID</th>
                                            <th class="py-3 px-4 text-left font-semibold">Request Type</th>
                                            <th class="py-3 px-4 text-left font-semibold">Description</th>
                                            <th class="py-3 px-4 text-left font-semibold">Room</th>
                                            <th class="py-3 px-4 text-left font-semibold">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if ($taskResult && $taskResult->num_rows > 0) {
                                            $taskResult->data_seek(0); // Reset pointer
                                            $count = 0;
                                            while (($task = $taskResult->fetch_assoc()) && $count < 5): // Limit to 5 tasks
                                                $count++;
                                                // Ensure task data exists and set defaults if not
                                                $taskId = isset($task['TaskID']) ? $task['TaskID'] : 'N/A';
                                                $requestType = isset($task['RequestType']) ? $task['RequestType'] : '';
                                                $description = isset($task['Description']) ? $task['Description'] : '';
                                                $roomNumber = isset($task['RoomNumber']) && !empty($task['RoomNumber']) ? $task['RoomNumber'] : (isset($task['RoomID']) && !empty($task['RoomID']) ? $task['RoomID'] : 'N/A');
                                                $taskStatus = isset($task['TaskStatus']) ? $task['TaskStatus'] : '';
                                        ?>
                                        <tr class="table-row-hover border-b border-gray-200 text-sm">
                                            <td class="py-3 px-4">
                                                <span class="font-medium">#<?= $taskId ?></span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <?php 
                                                $requestTypeIcon = '';
                                                $requestTypeClass = '';
                                                if (!empty($requestType)) {
                                                    switch(strtolower($requestType)) {
                                                        case 'cleaning':
                                                            $requestTypeIcon = 'fa-broom';
                                                            $requestTypeClass = 'bg-green-50 text-green-700';
                                                            break;
                                                        case 'maintenance':
                                                            $requestTypeIcon = 'fa-tools';
                                                            $requestTypeClass = 'bg-blue-50 text-blue-700';
                                                            break;
                                                        case 'room service':
                                                            $requestTypeIcon = 'fa-utensils';
                                                            $requestTypeClass = 'bg-purple-50 text-purple-700';
                                                            break;
                                                        default:
                                                            $requestTypeIcon = 'fa-concierge-bell';
                                                            $requestTypeClass = 'bg-gray-50 text-gray-700';
                                                    }
                                                } else {
                                                    $requestTypeIcon = 'fa-question-circle';
                                                    $requestTypeClass = 'bg-gray-50 text-gray-700';
                                                }
                                                ?>
                                                <span class="<?= $requestTypeClass ?> py-1 px-2 rounded text-xs font-medium">
                                                    <i class="fas <?= $requestTypeIcon ?> mr-1"></i>
                                                    <?= htmlspecialchars($requestType) ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="truncate max-w-xs"><?= htmlspecialchars($description) ?></div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="bg-accent/10 text-accent py-1 px-2 rounded text-xs font-medium">
                                                    <i class="fas fa-door-closed mr-1"></i>
                                                    <?= $roomNumber !== 'N/A' ? 'Room ' . $roomNumber : 'No Room Assigned' ?>
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <?php 
                                                $statusClass = '';
                                                $statusIcon = '';
                                                if (!empty($taskStatus)) {
                                                    switch(strtolower($taskStatus)) {
                                                        case 'pending':
                                                            $statusClass = 'bg-yellow-100 text-yellow-800 border border-yellow-200';
                                                            $statusIcon = 'fa-hourglass-half';
                                                            break;
                                                        case 'inprogress':
                                                            $statusClass = 'bg-blue-100 text-blue-800 border border-blue-200';
                                                            $statusIcon = 'fa-spinner fa-spin';
                                                            break;
                                                        case 'completed':
                                                            $statusClass = 'bg-green-100 text-green-800 border border-green-200';
                                                            $statusIcon = 'fa-check-circle';
                                                            break;
                                                        default:
                                                            $statusClass = 'bg-gray-100 text-gray-800 border border-gray-200';
                                                            $statusIcon = 'fa-question-circle';
                                                    }
                                                } else {
                                                    $statusClass = 'bg-gray-100 text-gray-800 border border-gray-200';
                                                    $statusIcon = 'fa-question-circle';
                                                }
                                                ?>
                                                <span class="px-3 py-1 rounded-full text-xs font-medium inline-flex items-center <?= $statusClass ?>">
                                                    <i class="fas <?= $statusIcon ?> mr-1"></i>
                                                    <?= $taskStatus ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php 
                                            endwhile;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                
                                <?php if ($taskResult->num_rows > 5): ?>
                                <div class="mt-4 text-center">
                                    <a href="./task.php" class="text-accent hover:text-accent-dark transition-colors duration-200 inline-flex items-center">
                                        <span>View all <?= $taskResult->num_rows ?> tasks</span>
                                        <i class="fas fa-arrow-right ml-2"></i>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="bg-blue-50 border border-blue-100 text-blue-700 p-6 rounded-md flex flex-col items-center justify-center text-center">
                                <div class="bg-blue-100 p-3 rounded-full mb-3">
                                    <i class="fas fa-clipboard-list text-blue-500 text-xl"></i>
                                </div>
                                <h3 class="text-lg font-medium mb-2">No Tasks Assigned</h3>
                                <p class="text-blue-600 mb-4">You don't have any tasks assigned to you at the moment.</p>
                                <a href="./task.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors duration-300 inline-flex items-center">
                                    <i class="fas fa-search mr-2"></i> Check Task Board
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once '../includes/sub_footer.php';?>

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