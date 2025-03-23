<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Hotel Website</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary:rgb(49, 168, 236);
            --secondary:rgb(8, 55, 58);
            --light:rgb(137, 214, 224);
        }
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.7;
            padding-top: 80px;
        }
        /* Enhanced Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: linear-gradient(90deg, var(--primary), #1a252f);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-family: 'Dancing Script', cursive;
            font-size: 2rem;
            color: var(--secondary) !important;
        }
        .navbar-nav a {
            color: white !important;
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
        }
        .navbar-nav a:hover {
            color: var(--secondary) !important;
        }
        .navbar-nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--secondary);
            bottom: -5px;
            left: 50%;
            transition: all 0.3s ease;
        }
        .navbar-nav a:hover::after {
            width: 50%;
            left: 25%;
        }
        .login-btn {
            background: var(--secondary);
            border-radius: 20px;
            padding: 8px 20px;
            margin-left: 15px;
            transition: transform 0.3s ease;
        }
        .login-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body data-spy="scroll" data-target="#navbarNav">
<nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">EaSyStaY</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="bookings.php">Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn login-btn" href="login.php">Login / Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    </header>

    <section id="bookings" class="bg-light py-5">
        <div class="container">
            <div class="card shadow-lg">
                <div class="card-body p-4">
                    <h2 class="card-title fs-3 fw-bold text-center mb-4">Booking</h2>
                    <form method="GET" action="booking.php" class="row justify-content-center g-3">
                        <div class="col-md-6">
                            <label for="check-in" class="form-label">Check-in</label>
                            <input type="date" id="check-in" name="checkin" required class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="check-out" class="form-label">Check-out</label>
                            <input type="date" id="check-out" name="checkout" required class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="rooms" class="form-label">Rooms</label>
                            <select id="rooms" name="rooms" required class="form-select">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="adults" class="form-label">Adults</label>
                            <select id="adults" name="adults" required class="form-select">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="children" class="form-label">Children</label>
                            <select id="children" name="children" required class="form-select">
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary">Check Availability</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light py-5">
        <div class="container">
            <h2 class="fs-3 fw-bold text-center mb-4">Available Rooms</h2>
            <?php
            // Simulated room data
            $rooms = [
                ["image" => "room1.jpg", "title" => "Deluxe Room", "description" => "Spacious room with a king-size bed and balcony.", "price" => 200],
                ["image" => "room2.jpg", "title" => "Suite Room", "description" => "Luxurious suite with a separate living area and panoramic views.", "price" => 300],
                ["image" => "room3.jpg", "title" => "Standard Room", "description" => "Comfortable room with a queen-size bed.", "price" => 100],
            ];
            ?>
            <div class="row g-4">
                <?php foreach ($rooms as $room): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm rounded">
                        <img src="<?php echo $room['image']; ?>" alt="<?php echo $room['title']; ?>" class="card-img-top">
                        <div class="card-body">
                            <h3 class="card-title fs-5 fw-bold"><?php echo $room['title']; ?></h3>
                            <p class="card-text"><?php echo $room['description']; ?></p>
                            <p class="card-text fw-bold">$<?php echo $room['price']; ?>/night</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="bg-dark py-4">
        <div class="container text-center text-light">
            <p>&copy; <?php echo date('Y'); ?> EaSyStaY. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
