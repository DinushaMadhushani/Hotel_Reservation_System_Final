<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EaSyStaY - Room Packages</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
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
       
        .hero {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                        url('https://images.unsplash.com/photo-1582719365379-005b891343d8') no-repeat center center/cover;
            height: 70vh;
            position: relative;
            display: flex;
            align-items: center;
            color: var(--accent);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .hero-content {
            font-family:sans serif;
            background: rgba(0, 0, 0, 0.7);
            padding: 2rem 3rem;
            border-radius: 15px;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
        }
        .accent{
            color:var(--accent);
        }
        .dark{
           color:var(--dark);
        }
        .package-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            background: var(--light);
            overflow: hidden;
            position: relative;
        }
        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .package-card:hover .icon-circle {
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        .package-price {
            font-size: 1.75rem;
            color: var(--accent);
            font-weight: 600;
        }
        .package-includes li {
            display: flex;
            align-items: center;
            margin: 0.5rem 0;
            transition: color 0.3s ease;
        }
        .package-card:hover .package-includes li {
            color: var(--accent);
        }
        .a {
            background: var(--accent);
        }
        .feature-box {
            border: 2px solid var(--accent);
            border-radius: 15px;
            padding: 2rem;
            transition: transform 0.3s;
            background: var(--secondary);
        }
        .feature-box:hover {
            transform: scale(1.05);
        }
        .feature-icon {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 1rem;
        }
        .cta {
            background: linear-gradient(45deg, var(--primary), var(--accent));
            color: var(--secondary);
            padding: 3rem 0;
            border-radius: 15px;
        }
        .divider {
            width: 80px;
            height: 3px;
            background: var(--accent);
            margin: 0 auto;
        }
        .btn-outline-accent {
            border: 2px solid var(--accent);
            color: var(--accent);
            transition: all 0.3s ease;
        }
        .btn-outline-accent:hover {
            background: var(--accent);
            color: var(--secondary);
            transform: scale(1.05);
        }
        .icon-circle {
            width: 60px;
            height: 60px;
            background: var(--light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        .package-header {
            transition: color 0.3s ease;
        }
        .package-card:hover .package-header {
            color: var(--accent);
        }
        .room-card {
            transition: transform 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .room-price {
            color: var(--accent);
            font-size: 1.25rem;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .hero {
                height: 50vh;
            }
            .hero h1 {
                font-size: 2rem;
            }
            .package-card {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'?>
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content text-center" data-aos="zoom-in" data-aos-duration="1500">
            <h1>Exclusive Rooms & Packages</h1>
            <p class="lead">Find the perfect accommodation for your needs</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#packages" class="btn btn-light btn-lg mt-3">Explore Packages</a>
                <a href="#rooms" class="btn btn-light btn-lg mt-3">Explore Rooms</a>
            </div>
        </div>
    </div>
</section>
<!-- Packages Section -->
<section id="packages" class="container mt-5">
    <div class="text-center mb-5" data-aos="fade-up">
        <h2 class="display-5 fw-bold accent">Our Room Packages</h2>
        <div class="divider my-4"></div>
        <p class="lead text-muted">Tailored accommodation experiences for every traveler</p>
    </div>
    <div class="row g-4">
        <!-- Standard Room -->
        <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="package-card p-4 h-100">
                <div class="package-header d-flex align-items-center mb-4">
                    <div class="icon-circle me-3">
                        <i class="fas fa-bed fa-2x text-accent"></i>
                    </div>
                    <h3 class="h5 mb-0">Standard Room Stay</h3>
                </div>
                <p class="text-muted mb-4">Comfortable accommodation for short stays</p>
                <div class="mb-4">
                    <label class="form-label fw-bold">Room Type:</label>
                    <select class="form-select" aria-label="Room type">
                        <option value="single">Single Room ($89/night)</option>
                        <option value="double">Double Room ($129/night)</option>
                        <option value="twin">Twin Room ($119/night)</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="package-price">$149</span>
                    <a href="#" class="btn btn-outline-accent">Book Package + Room</a>
                </div>
            </div>
        </div>
        <!-- Deluxe Room -->
        <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="400">
            <div class="package-card p-4 h-100">
                <div class="package-header d-flex align-items-center mb-4">
                    <div class="icon-circle me-3">
                        <i class="fas fa-hotel fa-2x text-accent"></i>
                    </div>
                    <h3 class="h5 mb-0">Deluxe Room Package</h3>
                </div>
                <p class="text-muted mb-4">Premium comfort with extra amenities</p>
                <div class="mb-4">
                    <label class="form-label fw-bold">Room Type:</label>
                    <select class="form-select" aria-label="Room type">
                        <option value="deluxe-queen">Deluxe Queen ($199/night)</option>
                        <option value="deluxe-king">Deluxe King ($229/night)</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="package-price">$229</span>
                    <a href="#" class="btn btn-outline-accent">Book Package + Room</a>
                </div>
            </div>
        </div>
        <!-- Family Suite -->
        <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="500">
            <div class="package-card p-4 h-100">
                <div class="package-header d-flex align-items-center mb-4">
                    <div class="icon-circle me-3">
                        <i class="fas fa-users fa-2x text-accent"></i>
                    </div>
                    <h3 class="h5 mb-0">Family Suite Escape</h3>
                </div>
                <p class="text-muted mb-4">Spacious accommodation for families</p>
                <div class="mb-4">
                    <label class="form-label fw-bold">Room Type:</label>
                    <select class="form-select" aria-label="Room type">
                        <option value="family-2queen">2 Queen Beds ($249/night)</option>
                        <option value="family-suite">Family Suite ($299/night)</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="package-price">$349</span>
                    <a href="#" class="btn btn-outline-accent">Book Package + Room</a>
                </div>
            </div>
        </div>
        <!-- Long Stay -->
        <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="600">
            <div class="package-card p-4 h-100">
                <div class="package-header d-flex align-items-center mb-4">
                    <div class="icon-circle me-3">
                        <i class="fas fa-calendar-alt fa-2x text-accent"></i>
                    </div>
                    <h3 class="h5 mb-0">Long Stay Special</h3>
                </div>
                <p class="text-muted mb-4">Discounted rates for extended stays</p>
                <div class="mb-4">
                    <label class="form-label fw-bold">Room Type:</label>
                    <select class="form-select" aria-label="Room type">
                        <option value="studio">Studio Apartment ($149/night)</option>
                        <option value="executive">Executive Suite ($179/night)</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="package-price">$499</span>
                    <a href="#" class="btn btn-outline-accent">Book Package + Room</a>
                </div>
            </div>
        </div>
        <!-- Luxury Suite -->
        <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="700">
            <div class="package-card p-4 h-100">
                <div class="package-header d-flex align-items-center mb-4">
                    <div class="icon-circle me-3">
                        <i class="fas fa-crown fa-2x text-accent"></i>
                    </div>
                    <h3 class="h5 mb-0">Luxury Suite Experience</h3>
                </div>
                <p class="text-muted mb-4">Ultimate luxury accommodation</p>
                <div class="mb-4">
                    <label class="form-label fw-bold">Room Type:</label>
                    <select class="form-select" aria-label="Room type">
                        <option value="presidential">Presidential Suite ($599/night)</option>
                        <option value="penthouse">Penthouse ($799/night)</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="package-price">$699</span>
                    <a href="#" class="btn btn-outline-accent">Book Package + Room</a>
                </div>
            </div>
        </div>
        <!-- Romantic Getaway -->
        <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="800">
            <div class="package-card p-4 h-100">
                <div class="package-header d-flex align-items-center mb-4">
                    <div class="icon-circle me-3">
                        <i class="fas fa-heart fa-2x text-accent"></i>
                    </div>
                    <h3 class="h5 mb-0">Romantic Getaway</h3>
                </div>
                <p class="text-muted mb-4">Perfect for couples retreat</p>
                <div class="mb-4">
                    <label class="form-label fw-bold">Room Type:</label>
                    <select class="form-select" aria-label="Room type">
                        <option value="honeymoon">Honeymoon Suite ($299/night)</option>
                        <option value="jacuzzi">Jacuzzi Room ($349/night)</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="package-price">$299</span>
                    <a href="#" class="btn btn-outline-accent">Book Package + Room</a>
                </div>
            </div>
        </div>
        <!-- New Package: Business Traveler -->
        <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="900">
            <div class="package-card p-4 h-100">
                <div class="package-header d-flex align-items-center mb-4">
                    <div class="icon-circle me-3">
                        <i class="fas fa-briefcase fa-2x text-accent"></i>
                    </div>
                    <h3 class="h5 mb-0">Business Traveler</h3>
                </div>
                <p class="text-muted mb-4">Productive stay with work amenities</p>
                <div class="mb-4">
                    <label class="form-label fw-bold">Room Type:</label>
                    <select class="form-select" aria-label="Room type">
                        <option value="executive">Executive Room ($179/night)</option>
                        <option value="business-suite">Business Suite ($229/night)</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="package-price">$329</span>
                    <a href="#" class="btn btn-outline-accent">Book Package + Room</a>
                </div>
            </div>
        </div>
        <!-- New Package: Weekend Escape -->
        <div class="col-12 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="1000">
            <div class="package-card p-4 h-100">
                <div class="package-header d-flex align-items-center mb-4">
                    <div class="icon-circle me-3">
                        <i class="fas fa-suitcase-rolling fa-2x text-accent"></i>
                    </div>
                    <h3 class="h5 mb-0">Weekend Escape</h3>
                </div>
                <p class="text-muted mb-4">Perfect for quick getaways</p>
                <div class="mb-4">
                    <label class="form-label fw-bold">Room Type:</label>
                    <select class="form-select" aria-label="Room type">
                        <option value="deluxe-weekend">Deluxe Room ($199/night)</option>
                        <option value="suite-weekend">Mini Suite ($249/night)</option>
                    </select>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="package-price">$279</span>
                    <a href="#" class="btn btn-outline-accent">Book Package + Room</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Rooms Section -->
<section id="rooms" class="container mt-5">
    <h2 class="display-5 fw-bold text-center accent">Our Rooms</h2>
    
    <!-- Standard Rooms -->
    <div class="row g-4 mb-5">
        <div class="col-12">
            <h3 class="fw-bold mb-4 dark">Standard Rooms</h3>
        </div>
        <div class="col-md-4">
            <div class="card room-card">
                <img src="../assets/images/rooms/R (2).jpg" class="card-img-top" alt="Single Room">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-bed me-2 text-accent"></i>
                        <h4 class="card-title mb-0">Single Room</h4>
                    </div>
                    <p class="card-text">Ideal for solo travelers with a comfortable single bed.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="room-price">$89<span class="text-muted">/night</span></div>
                        <a href="booking_form.php" class="btn btn-outline-accent">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card room-card">
                <img src="../assets/images/rooms/R (1).jpg" class="card-img-top" alt="Double Room">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-bed me-2 text-accent"></i>
                        <h4 class="card-title mb-0">Double Room</h4>
                    </div>
                    <p class="card-text">Perfect for couples with a cozy queen-size bed.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="room-price">$129<span class="text-muted">/night</span></div>
                        <a href="booking_form.php" class="btn btn-outline-accent">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card room-card">
                <img src="../assets/images/rooms/twinroom.jpg" class="card-img-top" alt="Twin Room">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-bed me-2 text-accent"></i>
                        <h4 class="card-title mb-0">Twin Room</h4>
                    </div>
                    <p class="card-text">Great for friends with two single beds.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="room-price">$119<span class="text-muted">/night</span></div>
                        <a href="booking_form.php" class="btn btn-outline-accent">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Premium Rooms -->
    <div class="row g-4">
        <div class="col-12">
            <h3 class="fw-bold dark mb-4">Premium Rooms</h3>
        </div>
        <div class="col-md-4">
            <div class="card room-card">
                <img src="../assets/images/rooms/deluxeroom.jpg" class="card-img-top" alt="Deluxe Room">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-star me-2 text-accent"></i>
                        <h4 class="card-title mb-0">Deluxe Room</h4>
                    </div>
                    <p class="card-text">Spacious room with modern amenities and city view.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="room-price">$199<span class="text-muted">/night</span></div>
                        <a href="booking_form.php" class="btn btn-outline-accent">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card room-card">
                <img src="../assets/images/rooms/familyroom.jpg" class="card-img-top" alt="Family Room">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-users me-2 text-accent"></i>
                        <h4 class="card-title mb-0">Family Room</h4>
                    </div>
                    <p class="card-text">Perfect for families with two queen beds.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="room-price">$199<span class="text-muted">/night</span></div>
                        <a href="booking_form.php" class="btn btn-outline-accent">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
    
    <div class="col-md-4">
            <div class="card room-card">
                <img src="../assets/images/rooms/penthousesuits.jpeg" class="card-img-top" alt="Penthouse Suites">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-users me-2 text-accent"></i>
                        <h4 class="card-title mb-0">Penthouse Suites</h4>
                    </div>
                    <p class="card-text"> Located on the highest floor of a hotel, these suites offer unparalleled views and luxury. </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="room-price">$199<span class="text-muted">/night</span></div>
                        <a href="booking_Form.php" class="btn btn-outline-accent">Book Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta text-center my-5 p-5" data-aos="fade-up" data-aos-delay="1100">
    <h2 class="mb-4">Ready to Book Your Stay?</h2>
    <p class="lead">Choose your perfect room package and reserve today</p>
    <a href="../users/booking.php" class="btn btn-light btn-lg mt-3">Make a Reservation</a>
</section>
<?php include '../includes/footer.php'?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        once: true
    });

    // Package selection handling
    document.querySelectorAll('.package-card select').forEach(select => {
        select.addEventListener('change', function() {
            // Add price calculation or validation logic here
        });
    });
</script>
</body>
</html>