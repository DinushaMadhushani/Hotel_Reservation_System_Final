<?php
session_start();
require '../config/db.con.php';

// Validate session and permissions
if (!isset($_SESSION['UserID'], $_SESSION['FullName'], $_SESSION['UserType']) || 
    $_SESSION['UserType'] !== 'Staff') {
    header("Location: ../auth/login.php");
    exit();
}

// Get staff schedule
$stmt = $conn->prepare("
    SELECT 
        ScheduleID,
        ScheduleDate,
        StartTime,
        EndTime
    FROM StaffSchedule 
    WHERE UserID = ?
    ORDER BY ScheduleDate DESC, StartTime ASC
");
$stmt->bind_param("i", $_SESSION['UserID']); // Fixed session variable case
$stmt->execute();
$result = $stmt->get_result();
$schedules = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule - Staff Dashboard</title>
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f0f2f5;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            margin-top: 60px;
            padding: 20px;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }

        .schedule-table th, 
        .schedule-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .schedule-table thead {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
        }

        .today {
            background-color: #e3f2fd;
        }

        .past-date {
            opacity: 0.6;
            background-color: #f5f5f5;
        }

        .schedule-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .current-shift {
            background-color: #c8e6c9;
            color: #2e7d32;
        }

        .upcoming-shift {
            background-color: #fff3e0;
            color: #ef6c00;
        }

        .completed-shift {
            color: #666;
        }

        .no-schedule {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .schedule-table {
                font-size: 0.9em;
            }
            
            .schedule-table th, 
            .schedule-table td {
                padding: 8px 10px;
            }
        }
    </style>
</head>
<body>
    <?php include('../includes/staff_header.php'); ?>

    <div class="container">
        <h2 class="my-4 text-center">My Work Schedule</h2>
        
        <?php if (count($schedules) > 0): ?>
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Duration</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): 
                        // Create DateTime objects
                        $start = new DateTime($schedule['ScheduleDate'] . ' ' . $schedule['StartTime']);
                        $end = new DateTime($schedule['ScheduleDate'] . ' ' . $schedule['EndTime']);
                        
                        // Handle overnight shifts
                        if ($end < $start) {
                            $end->modify('+1 day');
                        }
                        
                        $duration = $start->diff($end);
                        $now = new DateTime();
                        $today = new DateTime('today');
                        $scheduleDate = new DateTime($schedule['ScheduleDate']);
                        
                        // Determine status
                        $rowClass = '';
                        if ($scheduleDate->format('Y-m-d') == $today->format('Y-m-d')) {
                            $rowClass = 'today';
                            if ($now >= $start && $now <= $end) {
                                $status = '<span class="schedule-status current-shift">Current Shift</span>';
                            } elseif ($now < $start) {
                                $status = '<span class="schedule-status upcoming-shift">Upcoming</span>';
                            } else {
                                $status = '<span class="completed-shift">Completed</span>';
                            }
                        } elseif ($scheduleDate < $today) {
                            $rowClass = 'past-date';
                            $status = '<span class="completed-shift">Completed</span>';
                        } else {
                            $status = '<span class="schedule-status upcoming-shift">Upcoming</span>';
                        }
                    ?>
                    <tr class="<?= $rowClass ?>">
                        <td><?= $scheduleDate->format('D, M j, Y') ?></td>
                        <td><?= $start->format('h:i A') ?></td>
                        <td><?= $end->format('h:i A') ?></td>
                        <td><?= $duration->format('%h h %i m') ?></td>
                        <td><?= $status ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-schedule">
                <p>No schedule entries found</p>
                <p>Please contact your manager for scheduling information</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>