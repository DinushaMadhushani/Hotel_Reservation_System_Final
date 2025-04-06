<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <style>
        /* Root Variables */
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }

        /* General Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--primary);
            line-height: 1.6;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: var(--primary);
            color: var(--secondary);
        }

        .logo {
            font-size: 18px;
            font-weight: bold;
            color: var(--accent);
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        nav ul li a {
            text-decoration: none;
            color: var(--secondary);
            padding: 10px 20px;
            background-color: var(--primary);
            border: 1px solid var(--accent);
            border-radius: 4px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        nav ul li a:hover {
            background-color: var(--accent);
            color: var(--primary);
        }

        /* Main Content */
        main {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: var(--secondary);
            border: 1px solid var(--accent);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .booking-form h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary);
        }

        .booking-form h3 {
            margin-top: 20px;
            margin-bottom: 10px;
            color: var(--accent);
        }

        .booking-form input,
        .booking-form textarea,
        .booking-form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--accent);
            border-radius: 4px;
            font-size: 16px;
            background-color: var(--light);
            color: var(--primary);
        }

        .booking-form textarea {
            resize: vertical;
            height: 80px;
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
            color: var(--primary);
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

        /* Footer */
        footer {
            text-align: center;
            padding: 10px 0;
            background-color: var(--primary);
            color: var(--secondary);
            border-top: 1px solid var(--accent);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">EaSyStaY</div> <!-- Updated Logo -->
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
    <main>
        <section class="booking-form">
            <h2>BOOKING FORM</h2>
            <form>
                <!-- User Details -->
                <h3>USER DETAILS</h3>
                <input type="text" placeholder="FULL NAME" required>
                <input type="email" placeholder="EMAIL ADDRESS" required>
                <input type="tel" placeholder="PHONE NUMBER" required>

                <!-- Booking Details -->
                <h3>BOOKING DETAILS</h3>
                <input type="date" placeholder="CHECK-IN" required>
                <input type="date" placeholder="CHECK-OUT" required>
                <input type="number" placeholder="NUMBER OF GUESTS" required>
                <textarea placeholder="REQUESTS"></textarea>
                <select required>
                    <option value="" disabled selected>Select Packages</option>
                    <option value="package1">Package 1</option>
                    <option value="package2">Package 2</option>
                    <option value="package3">Package 3</option>
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
    <footer>
        <p>FOOTER</p>
    </footer>
</body>
</html>
