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
            color: var(--secondary) !important;
            font-weight: 600;
            position: relative;
            transition: all 0.3s ease;
            padding: 1rem 1.5rem;
        }
        .navbar-nav a:hover {
            color: var(--accent) !important;
        }
        .navbar-nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--accent);
            bottom: 0;
            left: 50%;
            transition: all 0.3s ease;
        }
        .navbar-nav a:hover::after {
            width: 80%;
            left: 10%;
        }
        .login-btn {
            background: var(--accent);
            border-radius: 12px;
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
            color: var(--secondary);
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
            border: 1px solid var(--accent);
            border-radius: 15px;
            background-color: var(--secondary);
            color: var(--primary);
            font-size: 1rem;
        }
        .search-bar button {
            padding: 12px 25px;
            background-color: var(--accent);
            color: var(--secondary);
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .search-bar button:hover {
            background-color: var(--dark);
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

        .exbtn a{
            text-decoration: none;
            color: var(--secondary);
        }

        .exbtn{
            padding: 12px 25px;
            background-color: var(--accent);
            color: var(--secondary);
            border-radius: 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .exbtn:hover{
            background-color: var(--dark);
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
        .roomex{
            padding: 12px 25px;
            background-color: var(--accent);
            color: var(--secondary);
            border-radius: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .roomex a{
            text-decoration: none;
            color: var(--secondary);
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
            background-color: var(--accent);
            color: var(--secondary);
        }
        .feature-icon {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 15px;
        }
        .testimonials {
            background: linear-gradient(45deg, var(--primary), var(--accent));
            color: var(--secondary);
            padding: 50px 0;
        }
        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
        }
        .social-icons a {
            color: var(--secondary);
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
                    <a class="nav-link" href="./pages/rooms.php">Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#features">Why Choose Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#testimonials">Testimonials</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./pages/about_us.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="btn login-btn" href="./auth/login.php">Login/Sign Up</a>
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
        <button class="btn btn-light exbtn"><a href="./pages/rooms.php">Explore Now</a></button>
    </div>

    <div class="image-slider">
        <img src="./assets/images/hero/img1.jpg" alt="Slide 1" class="active">
        <img src="./assets/images/hero/img4.jpg" alt="Slide 2">
        <img src="./assets/images/hero/img5.jpg" alt="Slide 3">
        <img src="./assets/images/hero/img6.jpg" alt="Slide 4">
        <img src="./assets/images/hero/image.jpg" alt="Slide 5">
        <img src="./assets/images/hero/img7.jpg" alt="Slide 6">
    </div>
</section>
<!-- Rooms Section -->
<section id="rooms" class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Available Rooms</h2>
        <div class="row g-4">
            <?php
            $rooms = [
                ["image" => "./assets/images/rooms/first.jpeg", "title" => "Deluxe Suite", "description" => "Spacious room with a king-size bed, highly comfatable service, private balcony, and city views.", "price" => 250, "amenities" => ["Free Wi-Fi and internetcode", "Mini Bar", "24/7 Room Service"]],
                ["image" => "./assets/images/rooms/second.jpg", "title" => "Executive Suite", "description" => "Luxurious suite with a separate living area, premium mattress, and spa-like bathroom.", "price" => 400, "amenities" => ["Personal Concierge", "Private Terrace", "Jacuzzi Tub"]],
                ["image" => "./assets/images/rooms/third.jpg", "title" => "Standard Room", "description" => "Comfortable room with a queen-size bed, modern amenities, and elegant decor.", "price" => 150, "amenities" => ["Flat-screen TV", "Air Conditioning", "Daily Housekeeping"]]
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
                        <button class="btn btn-primary roomex"> <a href="./pages/rooms.php">Explore More ➡️ </a></button>
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

<?php include './includes/footer.php';?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/your-font-awesome-key.js" crossorigin="anonymous"></script>
<script>
    // Image Slider Logic
    const slides = document.querySelectorAll('.image-slider img');
    const heroSection = document.querySelector('.hero-section');
    let currentSlide = 0;
    const backgroundImages = [
        './assets/images/hero/img1.jpg',
        './assets/images/hero/img4.jpg',
        './assets/images/hero/img5.jpg',
        './assets/images/hero/img6.jpg',
        './assets/images/hero/image.jpg',
        './assets/images/hero/img7.jpg'
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