<?php
// Dummy data for demonstration
$total_bookings = 150;
$total_users = 500;
$available_rooms = 20;
$recent_bookings = [
    ['id' => 1, 'user' => 'John Doe', 'room' => 'Deluxe Suite', 'date' => '2023-10-15'],
    ['id' => 2, 'user' => 'Jane Smith', 'room' => 'Standard Room', 'date' => '2023-10-16'],
    ['id' => 3, 'user' => 'Alice Johnson', 'room' => 'Executive Suite', 'date' => '2023-10-17'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Interface</title>
    <style>
        /* General Styles */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        /* Header */
        .header {
            background-color: #4CAF50; /* Green background */
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header .logo {
            font-size: 1.5em;
            font-weight: bold;
        }
        .header .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }
        .header .nav-links a:hover {
            text-decoration: underline;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #388E3C; /* Darker green for contrast */
            color: white;
            position: fixed;
            top: 60px;
            bottom: 0;
            padding: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px 0;
        }
        .sidebar a:hover {
            background-color: #4CAF50; /* Lighter green on hover */
            padding-left: 10px;
            border-left: 3px solid white;
        }

        /* Main Content */
        .main-content {
            margin-left: 270px;
            padding: 20px;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .stat-box {
            background-color: #e8f5e9; /* Light green background */
            border: 1px solid #c8e6c9;
            padding: 20px;
            text-align: center;
            flex: 1;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .stat-box h3 {
            margin: 0;
            font-size: 1.2em;
            color: #388E3C; /* Dark green for headings */
        }
        .stat-box p {
            margin: 5px 0 0;
            font-size: 1.5em;
            font-weight: bold;
            color: #2e7d32; /* Slightly darker green for emphasis */
        }

        /* Recent Bookings Table */
        .recent-bookings table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
        }
        .recent-bookings th, .recent-bookings td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .recent-bookings th {
            background-color: #4CAF50; /* Green header for the table */
            color: white;
        }
        .recent-bookings tr:nth-child(even) {
            background-color: #f1f8e9; /* Alternating light green rows */
        }

        /* Footer */
        .footer {
            background-color: #388E3C; /* Dark green footer */
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">Hotel Management</div>
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#">Bookings</a>
            <a href="#">Users</a>
            <a href="#">Settings</a>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#">Dashboard</a>
        <a href="#">Rooms</a>
        <a href="#">Staff</a>
        <a href="#">Packages</a>
        <a href="#">Reports</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Statistics Section -->
        <div class="stats">
            <div class="stat-box">
                <h3>Total Bookings</h3>
                <p><?php echo $total_bookings; ?></p>
            </div>
            <div class="stat-box">
                <h3>Total Users</h3>
                <p><?php echo $total_users; ?></p>
            </div>
            <div class="stat-box">
                <h3>Available Rooms</h3>
                <p><?php echo $available_rooms; ?></p>
            </div>
        </div>

        <!-- Recent Bookings Section -->
        <div class="section recent-bookings">
            <h2>Recent Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Room</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $booking): ?>
                        <tr>
                            <td><?php echo $booking['id']; ?></td>
                            <td><?php echo $booking['user']; ?></td>
                            <td><?php echo $booking['room']; ?></td>
                            <td><?php echo $booking['date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; 2023 Hotel Management System. All rights reserved.
    </div>
</body>
</html>