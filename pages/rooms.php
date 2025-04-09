
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <title>EaSyStaY - Rooms & Suites</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>

:root {
            --primary: #2DBD6E;
            --secondary: #0A1A28;
            --light: #E5F5F9;
            --accent: #F5C518;
        }
       /* Enhanced Navigation */
       .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: black;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .navbar-brand {
            font-family: 'Dancing Script', cursive;
            font-size: 2.5rem;
            color: white !important;
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

        .hero {
            background: url('https://via.placeholder.com/1920x1080/2c3e50/FFFFFF?text=Hotel+Rooms') no-repeat center center/cover;
            height: 60vh;
            position: relative;
            background-color:green; 
            margin-top:170px;
            display: flex;
            align-items: center;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .hero-content {
            background: rgba(7, 112, 218, 0.7);
            padding: 2rem 3rem;
            border-radius: 15px;
            margin-top:30px ;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
        }

        .room-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 15px;
            background: white;
        }
        .room-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .room-price {
            font-size: 1.75rem;
            color: #2c3e50;
            font-weight: 600;
        }
        .room-type-icon {
            font-size: 2.5rem;
            color: #3498db;
        }
        .card-btn {
            background: linear-gradient(45deg, #3498db, #2c3e50);
            border: none;
            transition: background 0.3s;
            padding: 0.75rem 2rem;
            font-weight: 500;
        }
        .card-btn:hover {
            background: linear-gradient(45deg, #2c3e50, #3498db);
        }

        .feature-box {
            border: 2px solid #3498db;
            border-radius: 15px;
            padding: 2rem;
            transition: transform 0.3s;
        }
        .feature-box:hover {
            transform: scale(1.05);
        }
        .feature-icon {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 1rem;
        }

        .testimonial-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
        }
        .testimonial-author {
            font-style: italic;
            color: #6c757d;
        }

        .cta {
            background: linear-gradient(45deg, #3498db, #2c3e50);
            color: white;
            padding: 3rem 0;
            border-radius: 15px;
        }

        @media (max-width: 768px) {
            .hero {
                height: 40vh;
            }
            .hero h1 {
                font-size: 2rem;
            }
            .room-card {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
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
                    <a class="nav-link" href="../index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./rooms.php">Rooms</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./gallery.php">Gallery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./about_us.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="btn login-btn" href="../auth/login.php">Login/Sign Up</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content" data-aos="fade-up">
                <h1>Find Your Perfect Stay</h1>
                <p class="lead">Experience comfort and luxury in our thoughtfully designed rooms</p>
                <a href="#rooms" class="btn btn-light btn-lg mt-3">Explore Rooms</a>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container mt-5">
        <!-- Standard Rooms Section -->
        <section id="rooms">
            <h2 class="text-center mb-5" data-aos="fade-up">Our Rooms</h2>
            
            <!-- Room Cards (Standard) -->
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <h3 class="text-primary mb-4">Standard Rooms</h3>
                </div>

                <!-- Single Room -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card room-card">
                        <img src="../assets/images/rooms/R (2).jpg" class="card-img-top" alt="Single Room">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-bed room-type-icon me-2"></i>
                                <h4 class="card-title mb-0">Single Room</h4>
                            </div>
                            <p class="card-text">Ideal for solo travelers with a comfortable single bed.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="room-price">$89<span class="text-muted">/night</span></div>
                                <a href="booking_form.php" class="btn card-btn">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Double Room -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card room-card">
                        <img src="../assets/images/rooms/R (1).jpg" class="card-img-top" alt="Double Room">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-bed room-type-icon me-2"></i>
                                <h4 class="card-title mb-0">Double Room</h4>
                            </div>
                            <p class="card-text">Perfect for couples with a cozy queen-size bed.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="room-price">$129<span class="text-muted">/night</span></div>
                                <a href="booking_form.php" class="btn card-btn">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Twin Room -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card room-card">
                        <img src="../assets/images/rooms/R (2).jpg" class="card-img-top" alt="Twin Room">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-bed room-type-icon me-2"></i>
                                <h4 class="card-title mb-0">Twin Room</h4>
                            </div>
                            <p class="card-text">Great for friends with two single beds.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="room-price">$119<span class="text-muted">/night</span></div>
                                <a href="booking_form.php" class="btn card-btn">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Premium Rooms Section -->
            <div class="row g-4">
                <div class="col-12">
                    <h3 class="text-primary mb-4">Premium Rooms</h3>
                </div>
                
                <!-- Deluxe Room -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card room-card">
                        <img src="https://via.placeholder.com/400x300/3498db/FFFFFF?text=Deluxe+Room" class="card-img-top" alt="Deluxe Room">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-star room-type-icon me-2 text-warning"></i>
                                <h4 class="card-title mb-0">Deluxe Room</h4>
                            </div>
                            <p class="card-text">Spacious room with modern amenities and city view.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="room-price">$199<span class="text-muted">/night</span></div>
                                <a href="#" class="btn card-btn">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family Room -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card room-card">
                        <img src="https://via.placeholder.com/400x300/3498db/FFFFFF?text=Family+Room" class="card-img-top" alt="Family Room">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-users room-type-icon me-2 text-success"></i>
                                <h4 class="card-title mb-0">Family Room</h4>
                            </div>
                            <p class="card-text">Perfect for families with two queen beds.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="room-price">$249<span class="text-muted">/night</span></div>
                                <a href="#" class="btn card-btn">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Suite Room -->
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="card room-card">
                        <img src="https://via.placeholder.com/400x300/3498db/FFFFFF?text=Suite+Room" class="card-img-top" alt="Suite Room">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-crown room-type-icon me-2 text-danger"></i>
                                <h4 class="card-title mb-0">Suite Room</h4>
                            </div>
                            <p class="card-text">Luxurious suite with separate living area.</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="room-price">$349<span class="text-muted">/night</span></div>
                                <a href="package.html" class="btn card-btn">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="my-5" data-aos="fade-up">
            <h2 class="text-center mb-5">Room Features</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box text-center p-4">
                        <i class="fas fa-wifi feature-icon"></i>
                        <h4>Free WiFi</h4>
                        <p>High-speed internet access in all rooms</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center p-4">
                        <i class="fas fa-coffee feature-icon"></i>
                        <h4>Breakfast Included</h4>
                        <p>Complimentary breakfast for all guests</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box text-center p-4">
                        <i class="fas fa-concierge-bell feature-icon"></i>
                        <h4>24/7 Service</h4>
                        <p>Round-the-clock room service available</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="my-5" data-aos="fade-up">
            <h2 class="text-center mb-5">Guest Reviews</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <p class="lead">"Amazing experience! The deluxe room had a breathtaking view and all the amenities we needed."</p>
                        <p class="testimonial-author">- Sarah Johnson</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <p class="lead">"Perfect family getaway! The suite was spacious and the kids loved the extra beds."</p>
                        <p class="testimonial-author">- Mark & Emily Davis</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta text-center my-5 p-5" data-aos="fade-up">
            <h2 class="mb-4">Ready to Book?</h2>
            <p class="lead">Find your perfect room and enjoy an unforgettable stay</p>
            <a href="./gallery.php" class="btn btn-light btn-lg mt-3">Check Gallery</a>
        </section>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
        });
    </script>
</body>
</html>