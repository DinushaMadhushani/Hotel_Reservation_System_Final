<?php
session_start();
require '../config/db.con.php';

// Session validation and security checks
if (!isset($_SESSION['UserID'], $_SESSION['FullName'], $_SESSION['UserType']) || 
    $_SESSION['UserType'] !== 'Staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Initialize message system
$message = '';
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_message'] = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">Security token mismatch!</div>';
        header("Location: task.php");
        exit();
    }

    if (isset($_POST['task_id'], $_POST['new_status'])) {
        $taskId = (int)$_POST['task_id'];
        $newStatus = $_POST['new_status'];
        
        // Validate inputs
        $allowedStatus = ['Pending', 'InProgress', 'Completed'];
        if (!in_array($newStatus, $allowedStatus)) {
            $_SESSION['flash_message'] = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">Invalid status value provided!</div>';
            header("Location: task.php");
            exit();
        }

        // Update task status with ownership check
        $stmt = $conn->prepare("
            UPDATE AssignedTasks 
            SET TaskStatus = ? 
            WHERE TaskID = ? AND StaffID = ?
        ");
        $stmt->bind_param("sii", $newStatus, $taskId, $_SESSION['UserID']);
        
        if ($stmt->execute()) {
            $_SESSION['flash_message'] = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">Task status updated successfully!</div>';
        } else {
            $_SESSION['flash_message'] = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">Database error: ' . $conn->error . '</div>';
        }
        header("Location: task.php");
        exit();
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch tasks with error handling
try {
    $stmt = $conn->prepare("
        SELECT 
            t.TaskID, 
            sr.RequestType, 
            sr.Description AS RequestDescription,
            t.TaskStatus, 
            t.AssignmentDateTime,
            b.BookingID,
            r.RoomNumber
        FROM AssignedTasks t
        JOIN ServiceRequests sr ON t.RequestID = sr.RequestID
        JOIN Bookings b ON sr.BookingID = b.BookingID
        JOIN Rooms r ON b.RoomID = r.RoomID
        WHERE t.StaffID = ?
        ORDER BY t.AssignmentDateTime DESC
    ");
    $stmt->bind_param("i", $_SESSION['UserID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $_SESSION['flash_message'] = '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">Error loading tasks: ' . $e->getMessage() . '</div>';
    header("Location: task.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks - Staff Dashboard</title>
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
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
    <?php include('../includes/staff_header.php'); ?>

    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold text-center mb-8" data-aos="fade-down">My Assigned Tasks</h2>
        
        <?php if (!empty($message)): ?>
            <div class="mb-6" data-aos="fade-in">
                <?= $message ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($tasks)): ?>
            <div class="overflow-x-auto bg-white rounded-lg shadow-md" data-aos="fade-up" data-aos-delay="100">
                <table class="w-full table-auto">
                    <thead class="bg-gradient-to-r from-primary to-accent text-white">
                        <tr>
                            <th class="px-6 py-4 text-left">Task ID</th>
                            <th class="px-6 py-4 text-left">Request Type</th>
                            <th class="px-6 py-4 text-left">Description</th>
                            <th class="px-6 py-4 text-left">Room</th>
                            <th class="px-6 py-4 text-left">Assigned</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($tasks as $index => $task): ?>
                            <tr class="hover:bg-gray-50 transition-colors duration-200" data-aos="fade-up" data-aos-delay="<?= 150 + ($index * 50) ?>">
                                <td class="px-6 py-4"><?= htmlspecialchars($task['TaskID']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($task['RequestType']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($task['RequestDescription']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($task['RoomNumber']) ?></td>
                                <td class="px-6 py-4"><?= date('M j, Y H:i', strtotime($task['AssignmentDateTime'])) ?></td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $statusClass = '';
                                    switch(strtolower($task['TaskStatus'])) {
                                        case 'pending':
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'inprogress':
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'completed':
                                            $statusClass = 'bg-green-100 text-green-800';
                                            break;
                                    }
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusClass ?>">
                                        <?= $task['TaskStatus'] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" class="flex flex-col sm:flex-row gap-2">
                                        <input type="hidden" name="task_id" value="<?= $task['TaskID'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <select name="new_status" class="rounded-md border-gray-300 shadow-sm focus:border-accent focus:ring focus:ring-accent focus:ring-opacity-50">
                                            <option value="Pending" <?= $task['TaskStatus'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="InProgress" <?= $task['TaskStatus'] === 'InProgress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="Completed" <?= $task['TaskStatus'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md transition-all duration-300 hover:-translate-y-0.5 shadow-md hover:shadow-lg">
                                            Update
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded shadow-md" data-aos="fade-up">
                <p class="font-medium">No tasks assigned to you at this time.</p>
            </div>
        <?php endif; ?>
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