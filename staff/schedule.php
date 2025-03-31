<?php
session_start();
require '../config/db.con.php';

// Verify staff access
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'Staff') {
    header("Location: ../login.php");
    exit();
}

$staffId = $_SESSION['UserID'];

// Get current week dates
$currentDate = date('Y-m-d');
$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));

// Fetch weekly schedule
$stmt = $conn->prepare("
    SELECT * FROM StaffSchedule 
    WHERE UserID = ? 
    AND ScheduleDate BETWEEN ? AND ?
    ORDER BY ScheduleDate ASC
");
$stmt->bind_param("iss", $staffId, $monday, $sunday);
$stmt->execute();
$schedule = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Schedule</title>
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        .schedule-table {
            border-collapse: separate;
            border-spacing: 0 10px;
        }
        .schedule-table th {
            background: #f8f9fa;
            border: none;
        }
        .schedule-table td {
            background: white;
            border: none;
            border-radius: 8px;
        }
        .today {
            background: #d1e7dd !important;
            font-weight: bold;
        }
        .shift-time {
            font-size: 0.9rem;
            color: #666;
        }
        .no-schedule {
            background: #ffeeba;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Main Content -->
            <main class="col-md-12 ms-sm-auto px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Weekly Schedule</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="prevWeek()">
                                <i class="fa-solid fa-chevron-left"></i> Previous Week
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="nextWeek()">
                                Next Week <i class="fa-solid fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Schedule Table -->
                <div class="table-responsive">
                    <table class="table schedule-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Shift Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedule as $shift): ?>
                                <tr class="<?= date('Y-m-d') === $shift['ScheduleDate'] ? 'today' : '' ?>">
                                    <td><?= date('M d', strtotime($shift['ScheduleDate'])) ?></td>
                                    <td><?= date('l', strtotime($shift['ScheduleDate'])) ?></td>
                                    <td>
                                        <div class="shift-time">
                                            <?= date('g:i A', strtotime($shift['StartTime'])) ?>
                                            to
                                            <?= date('g:i A', strtotime($shift['EndTime'])) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (strtotime($shift['ScheduleDate']) < time()): ?>
                                            <span class="badge bg-secondary">Completed</span>
                                        <?php elseif (date('Y-m-d') === $shift['ScheduleDate']): ?>
                                            <span class="badge bg-success">Today</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Upcoming</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($schedule)): ?>
                                <tr>
                                    <td colspan="4" class="no-schedule">
                                        <i class="fa-solid fa-calendar-xmark fa-2x mb-2"></i>
                                        <div>No schedule found for this week</div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        $(document).ready(function() {
            AOS.init();
        });

        function prevWeek() {
            // Implement previous week navigation
            alert('Previous week navigation not implemented yet');
        }

        function nextWeek() {
            // Implement next week navigation
            alert('Next week navigation not implemented yet');
        }
    </script>
</body>
</html>