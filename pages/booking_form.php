<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EaSyStaY - Booking Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
      <!-- Fonts -->
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.7;
            padding-top: 80px;
            color: var(--primary);
            background-color: var(--light);
        }
       
        /* Booking Form */
        .booking-form {
            max-width: 900px;
            margin: 120px auto 20px; /* Adjusted for fixed navbar */
            padding: 20px;
            background-color: var(--light);
            border: 1px solid var(--primary);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .booking-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary);
        }
        .booking-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: var(--primary);
        }
        .booking-form input,
        .booking-form textarea,
        .booking-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--primary);
            border-radius: 4px;
            font-size: 16px;
            background-color: var(--light);
            color: var(--primary);
        }
        .booking-form select {
            padding-left: 30px; /* Space for the icon */
        }
        .buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .buttons button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .buttons button:first-child {
            background-color: var(--accent);
            color: var(--secondary);
        }
        .buttons button:first-child:hover {
            background-color: var(--dark);
            color: var(--secondary);
        }
        .buttons button:last-child {
            background-color: var(--secondary);
            color: var(--primary);
            border: 1px solid var(--accent);
        }
        .buttons button:last-child:hover {
            background-color: var(--accent);
            color: var(--primary);
        }
       
    </style>
</head>
<body>
<?php include '../includes/header.php'?>

    <!-- Main Content -->
    <main>
        <section class="booking-form">
            <h2>BOOKING FORM</h2>
            <form action="#" method="POST">
                <!-- User Details -->
                <h3>USER DETAILS</h3>
                <label for="full-name">FULL NAME</label>
                <input type="text" id="full-name" name="full-name" placeholder="Enter your full name" required>

                <label for="email">EMAIL ADDRESS</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>

                <label for="phone">PHONE NUMBER</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>

                <!-- Booking Details -->
                <h3>BOOKING DETAILS</h3>
                <label for="check-in">CHECK-IN</label>
                <input type="date" id="check-in" name="check-in" required>

                <label for="check-out">CHECK-OUT</label>
                <input type="date" id="check-out" name="check-out" required>

                <label for="guests">NUMBER OF GUESTS</label>
                <input type="number" id="guests" name="guests" placeholder="Number of guests" min="1" required>

                <label for="requests">REQUESTS</label>
                <textarea id="requests" name="requests" placeholder="Any special requests?"></textarea>

                <label for="packages">SELECT PACKAGES</label>
                <select id="packages" name="packages" required>
                    <option value="" disabled selected>Select Package</option>
                    <option value="package1">Package 1</option>
                    <option value="package2">Package 2</option>
                    <option value="package3">Package 3</option>
                    <option value="package4">Package 4</option>
                    <option value="package5">Package 5</option>
                </select>

                <!-- Buttons -->
                <div class="buttons">
                    <button type="submit">CONFIRM</button>
                    <button type="reset">CANCEL</button>
                </div>
            </form>
        </section>
    </main>

    <!-- Footer -->
    <?php include '../includes/footer.php'?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>