<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EaSyStaY - Your Ultimate Stay Experience</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2DBD6E;
            --secondary: #0A1A28;
            --light: #E5F5F9;
            --accent: #F5C518;
        }
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.7;
            padding-top: 80px;
            color: var(--secondary);
        }
        /* Enhanced Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-family: 'Dancing Script', cursive;
            font-size: 2.5rem;
            color: var(--secondary) !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-nav a {
            color: white !important;
            font-weight: 600;
            position: relative;
            transition: all 0.3s ease;
            padding: 1rem 1.5rem;
        }
        .navbar-nav a:hover {
            color: var(--light) !important;
        }
        .navbar-nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--light);
            bottom: 0;
            left: 50%;
            transition: all 0.3s ease;
        }
        .navbar-nav a:hover::after {
            width: 80%;
            left: 10%;
        }
        .login-btn {
            background: var(--secondary);
            border-radius: 25px;
            padding: 10px 25px;
            margin-left: 15px;
            transition: transform 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        /* Hero Section */
        .hero-section {
            position: relative;
            height: 100vh;
            background-size: cover;
            background-position: center;
            overflow: hidden;
            transition: background-image 0.5s ease;
            background-blend-mode: overlay;
        }
        .hero-content {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .hero-content h1 {
            font-family:sans,serif;
            font-size: 3rem;
            margin-bottom: 25px;
            font-weight: 600;
        }
        .hero-content p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            
        }
        .search-bar {
            position: absolute;
            top: 300px;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(255,255,255,0.9);
            padding: 20px 30px;
            border-radius: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: flex;
            gap: 15px;
        }
        .search-bar input[type="date"],
        .search-bar select {
            padding: 12px;
            border: 1px solid var(--primary);
            border-radius: 15px;
            background-color: white;
            color: var(--secondary);
            font-size: 1rem;
        }
        .search-bar button {
            padding: 12px 25px;
            background-color: var(--primary);
            color: white;
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .search-bar button:hover {
            background-color: var(--accent);
        }
        .image-slider {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
        }
        .image-slider img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .image-slider img.active {
            transform: scale(1.2);
            box-shadow: 0 0 10px rgba(255,255,255,0.5);
        }
        /* Rooms Section */
        .room-card {
            transition: transform 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .room-card:hover {
            transform: translateY(-10px);
        }
        .room-card img {
            border-radius: 15px 15px 0 0;
        }
        .amenities {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }
        .amenities li {
            display: inline-block;
            margin-right: 15px;
            font-size: 0.9rem;
        }
        /* New Sections */
        .why-choose-us {
            background-color: var(--light);
            padding: 50px 0;
        }
        .feature-box {
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        .feature-box:hover {
            background-color: var(--primary);
            color: white;
        }
        .feature-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 15px;
        }
        .testimonials {
            background: linear-gradient(45deg, var(--primary), var(--accent));
            color: white;
            padding: 50px 0;
        }
        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
        }
        .social-icons a {
            color: white;
            margin: 0 10px;
            font-size: 1.5rem;
        }
    </style>
</head>
<body data-spy="scroll" data-target="#navbarNav">
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">EaSyStaY</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#hero">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#rooms">Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#features">Why Choose Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#testimonials">Testimonials</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="btn login-btn" href="login.php">Login/Sign Up</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section" style="background-image: url('../assets/images/hero/P (1).jpg');">
    <div class="hero-content">
        <h1>Unforgettable Stays, Unmatched Experiences</h1>
        <p>Where luxury meets comfort in every corner</p>
        <button class="btn btn-light">Explore Now</button>
    </div>
    <div class="search-bar">
        <input type="date" placeholder="Arrival Date" required>
        <input type="date" placeholder="Departure Date" required>
        <select required>
            <option value="1">1 Room</option>
            <option value="2">2 Rooms</option>
        </select>
        <select required>
            <option value="1">1 Adult</option>
            <option value="2">2 Adults</option>
        </select>
        <select required>
            <option value="0">0 Children</option>
            <option value="1">1 Child</option>
        </select>
        <button class="btn">Search</button>
    </div>
    <div class="image-slider">
        <img src="../assets/images/hero/P (1).jpg" alt="Slide 1" class="active">
        <img src="../assets/images/hero/P (2).jpg" alt="Slide 2">
        <img src="../assets/images/hero/P (3).jpg" alt="Slide 3">
        <img src="../assets/images/hero/P (4).jpg" alt="Slide 4">
        <img src="../assets/images/hero/P (5).jpg" alt="Slide 5">
        <img src="../assets/images/hero/P (6).jpg" alt="Slide 6">
    </div>
</section>

<!-- Rooms Section -->
<section id="rooms" class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Available Rooms</h2>
        <div class="row g-4">
            <?php
            $rooms = [
                ["image" => "../assets/images/rooms/first.jpg", "title" => "Deluxe Suite", "description" => "Spacious room with a king-size bed, private balcony, and city views.", "price" => 250, "amenities" => ["Free Wi-Fi", "Mini Bar", "24/7 Room Service"]],
                ["image" => "../assets/images/rooms/second.jpg", "title" => "Executive Suite", "description" => "Luxurious suite with a separate living area, premium mattress, and spa-like bathroom.", "price" => 400, "amenities" => ["Personal Concierge", "Private Terrace", "Jacuzzi Tub"]],
                ["image" => "../assets/images/rooms/third.jpg", "title" => "Standard Room", "description" => "Comfortable room with a queen-size bed, modern amenities, and elegant decor.", "price" => 150, "amenities" => ["Flat-screen TV", "Air Conditioning", "Daily Housekeeping"]]
            ];
            foreach ($rooms as $room):
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card room-card shadow-sm rounded">
                    <img src="<?php echo $room['image']; ?>" class="card-img-top rounded-top" alt="<?php echo $room['title']; ?>">
                    <div class="card-body">
                        <h3 class="card-title"><?php echo $room['title']; ?></h3>
                        <p class="card-text"><?php echo $room['description']; ?></p>
                        <ul class="amenities">
                            <?php foreach ($room['amenities'] as $amenity): ?>
                                <li><?php echo $amenity; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="card-text fw-bold">$<?php echo $room['price']; ?>/night</p>
                        <button class="btn btn-primary">Book Now</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section id="features" class="why-choose-us">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose EaSyStaY?</h2>
        <div class="row g-4">
            <div class="col-md-4 feature-box">
                <div class="feature-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <h4>Luxurious Rooms</h4>
                <p>Experience premium accommodations with modern amenities</p>
            </div>
            <div class="col-md-4 feature-box">
                <div class="feature-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <h4>Delicious Dining</h4>
                <p>Enjoy gourmet meals from our award-winning chefs</p>
            </div>
            <div class="col-md-4 feature-box">
                <div class="feature-icon">
                    <i class="fas fa-smile"></i>
                </div>
                <h4>24/7 Service</h4>
                <p>Uninterrupted concierge and guest services</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="testimonials">
    <div class="container">
        <h2 class="text-center text-white mb-5">What Our Guests Say</h2>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="testimonial-text">
                    "The best hotel experience I've ever had! The staff was amazing and the rooms were beyond expectations."
                    <br><small>- John Doe, USA</small>
                </div>
                <div class="testimonial-text mt-4">
                    "The perfect blend of luxury and affordability. I'll definitely be back!"
                    <br><small>- Sarah Johnson, UK</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer Section -->
<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5 class="mb-3">Contact Us</h5>
                <p>123 Luxury Lane, Dream City, 12345</p>
                <p>Phone: (123) 456-7890</p>
                <p>Email: info@easystay.com</p>
            </div>
            <div class="col-md-4">
                <h5 class="mb-3">Follow Us</h5>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <h5 class="mb-3">Newsletter</h5>
                <form>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter your email">
                        <button class="btn btn-primary" type="button">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center mt-4">
            <p>&copy; <?php echo date('Y'); ?> EaSyStaY. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/your-font-awesome-key.js" crossorigin="anonymous"></script>
<script>
    // Image Slider Logic
    const slides = document.querySelectorAll('.image-slider img');
    const heroSection = document.querySelector('.hero-section');
    let currentSlide = 0;
    const backgroundImages = [
        '../assets/images/hero/P (1).jpg',
        '../assets/images/hero/P (2).jpg',
        '../assets/images/hero/P (3).jpg',
        '../assets/images/hero/P (4).jpg',
        '../assets/images/hero/P (5).jpg',
        '../assets/images/hero/P (6).jpg'
    ];
    
    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === index);
        });
        currentSlide = index;
        heroSection.style.backgroundImage = `url('${backgroundImages[index]}')`;
    }

    // Auto-slide functionality with 5-second interval
    setInterval(() => {
        const nextSlide = (currentSlide + 1) % slides.length;
        showSlide(nextSlide);
    }, 5000);

    // Manual navigation
    slides.forEach((slide, index) => {
        slide.addEventListener('click', () => {
            showSlide(index);
        });
    });
</script>
</body>
</html>
