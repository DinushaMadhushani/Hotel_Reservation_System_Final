<?php
// Simulated data for Upcoming Bookings and Recent Activity
$upcomingBookings = [
    ["date" => "2023-11-15", "room" => "Deluxe Room", "status" => "Confirmed"],
    ["date" => "2023-11-20", "room" => "Suite", "status" => "Pending"]
];

$recentActivity = [
    ["date" => "2023-11-01", "activity" => "Booked a Deluxe Room"],
    ["date" => "2023-11-05", "activity" => "Updated Profile Information"]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #e9f7ef; /* Light green background */
            color: #333;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #28a745; /* Green background */
            color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 18px;
            font-weight: bold;
            color: #fff;
        }

        nav ul {
            list-style: none;
            display: flex;
        }

        nav li {
            margin-left: 20px;
        }

        nav a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #ffc107; /* Yellow hover effect */
            color: #333;
        }

        /* Container */
        .container {
            display: flex;
            padding: 20px;
        }

        /* Sidebar */
        .sidebar {
            width: 20%;
            background-color: #d4edda; /* Light green sidebar */
            padding: 20px;
            border-radius: 5px;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .sidebar a {
            text-decoration: none;
            color: #28a745; /* Green text */
            font-weight: bold;
            display: block;
            padding: 10px 20px;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #ffc107; /* Yellow hover effect */
            color: #333;
        }

        /* Main Content */
        .content {
            width: 80%;
            padding: 20px;
        }

        section {
            margin-bottom: 20px;
            background-color: #d4edda; /* Light green background */
            padding: 20px;
            border-radius: 5px;
        }

        .upcoming-bookings h2,
        .recent-activity h2 {
            margin-bottom: 10px;
            font-size: 20px;
            font-weight: bold;
            color: #28a745; /* Green heading */
        }

        .text-area {
            height: auto;
            min-height: 150px;
            border: 1px solid #c3e6cb;
            padding: 20px;
            text-align: left;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .buttons button {
            flex: 1;
            margin-right: 10px;
            padding: 10px 20px;
            background-color: #28a745; /* Green buttons */
            border: none;
            border-radius: 5px;
            font-weight: bold;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .buttons button:last-child {
            margin-right: 0;
        }

        .buttons button:hover {
            background-color: #ffc107; /* Yellow hover effect */
            color: #333;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #28a745; /* Green footer */
            color: #fff;
            font-weight: bold;
        }

        /* Additional Colors */
        .upcoming-bookings strong {
            color: #ffc107; /* Yellow for dates */
        }

        .upcoming-bookings span {
            color: #28a745; /* Green for status */
        }

        .recent-activity strong {
            color: #ffc107; /* Yellow for dates */
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">LOGO</div>
        <nav>
            <ul>
                <li><a href="#">HOME</a></li>
                <li><a href="#">ABOUT US</a></li>
                <li><a href="#">BOOKINGS</a></li>
                <li><a href="#">CONTACT US</a></li>
                <li><a href="#">Login / Signup</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <ul>
                <li><a href="#">BOOK ROOM</a></li>
                <li><a href="#">VIEW ROOM</a></li>
                <li><a href="#">VIEW PACKAGES</a></li>
                <li><a href="#">REQUEST SERVICES</a></li>
                <li><a href="#">PROFILE</a></li>
                <li><a href="#">LOGOUT</a></li>
            </ul>
        </aside>

        <!-- Main Section -->
        <main class="content">
            <!-- Upcoming Bookings Section -->
            <section class="upcoming-bookings">
                <h2>UPCOMING BOOKINGS</h2>
                <div class="text-area">
                    <?php if (!empty($upcomingBookings)): ?>
                        <ul>
                            <?php foreach ($upcomingBookings as $booking): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($booking['date']); ?>:</strong> 
                                    <?php echo htmlspecialchars($booking['room']); ?> 
                                    (<span style="color: #28a745;"><?php echo htmlspecialchars($booking['status']); ?></span>)
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No upcoming bookings.</p>
                    <?php endif; ?>
                </div>
                <div class="buttons">
                    <button>BOOK A NEW ROOM</button>
                    <button>VIEW BOOKING HISTORY</button>
                    <button>UPDATE PROFILE</button>
                </div>
            </section>

            <!-- Recent Activity Section -->
            <section class="recent-activity">
                <h2>RECENT ACTIVITY</h2>
                <div class="text-area">
                    <?php if (!empty($recentActivity)): ?>
                        <ul>
                            <?php foreach ($recentActivity as $activity): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($activity['date']); ?>:</strong> 
                                    <?php echo htmlspecialchars($activity['activity']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No recent activity.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Footer -->
    <footer>
        <p>FOOTER</p>
    </footer>
</body>
</html> 