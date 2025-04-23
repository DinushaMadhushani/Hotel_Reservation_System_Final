<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
      <!-- Fonts -->
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
            font-family: 'Montserrat', sans-serif;
            line-height: 1.6;
            color: var(--dark);
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
        .hero {
            background: url("../assets/images/hero/img4.jpg") no-repeat center center/cover;
            height: 80vh;
            position: relative;
            display: flex;
            align-items: center;
            color: var(--secondary);
            text-align: center;
            margin-top: 50px;
            padding: 0 2rem;
            font-family:sans serif;
        }

        .hero-content {
            background: rgba(0, 0, 0, 0.5);
            padding: 2rem 3rem;
            border-radius: 15px;
        }

        .hero h1 {
            
            font-size: 4rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .hero p {
            font-size: 1.5rem;
            max-width: 600px;
            margin: 0 auto 2rem;
        }

        .gallery-section {
            padding: 4rem 0;
        }

        .gallery-filter {
            margin-bottom: 3rem;
        }

        .gallery-filter button {
            background: transparent;
            border: 2px solid var(--accent);
            color: var(--dark);
            padding: 0.8rem 2rem;
            margin: 0 0.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .gallery-filter button.active {
            background: var(--accent);
            color: var(--dark);
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .gallery-item img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.1);
        }

        .gallery-item-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.9);
            padding: 1rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .gallery-item:hover .gallery-item-overlay {
            transform: translateY(0);
        }

        .footer {
            background: var(--dark);
            color: var(--light);
            padding: 4rem 0;
        }

        .footer h3 {
            color: var(--accent);
            margin-bottom: 1.5rem;
        }

        .footer a {
            color: var(--light);
            text-decoration: none;
        }

        .footer a:hover {
            color: var(--accent);
        }

        .social-icons a {
            color: var(--light);
            margin: 0 0.8rem;
            font-size: 1.2rem;
            transition: color 0.3s ease;
        }

        .social-icons a:hover {
            color: var(--accent);
        }

        /* Lightbox Styles */
        #lightboxModal .modal-content {
            border-radius: 15px;
            border: none;
            background: var(--light);
        }

        #lightboxModal .modal-body {
            padding: 0;
        }

        #lightboxModal img {
            width: 100%;
            height: auto;
            max-height: 70vh;
            object-fit: contain;
        }

        #lightboxModal .modal-footer {
            border-top: none;
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding: 1.5rem;
        }

        .btn-lightbox {
            background: var(--accent);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            transition: transform 0.2s ease;
        }

        .btn-lightbox:hover {
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.2rem;
            }
            
            .gallery-item img {
                height: 200px;
            }
        }
    </style>
</head>
<?php include '../includes/header.php'?>

    <!-- Hero Section -->
    <div class="hero" id="hero">
        <div class="hero-content">
            <h1 data-aos="fade-up">Experience Luxury Redefined</h1>
            <p data-aos="fade-up" data-aos-delay="200">Discover exquisite accommodations and world-class amenities in the heart of the city</p>
            <a href="#gallery" class="btn btn-outline-light btn-lg" data-aos="fade-up" data-aos-delay="400">Explore Gallery</a>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4" data-aos="fade-up">
                    <i class="fas fa-concierge-bell fa-3x mb-3 text-accent"></i>
                    <h4>24/7 Concierge</h4>
                    <p>Our dedicated staff is available around the clock to cater to your every need</p>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <i class="fas fa-spa fa-3x mb-3 text-accent"></i>
                    <h4>Luxury Spa</h4>
                    <p>Indulge in rejuvenating treatments at our award-winning spa</p>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <i class="fas fa-utensils fa-3x mb-3 text-accent"></i>
                    <h4>Fine Dining</h4>
                    <p>Experience culinary excellence at our Michelin-starred restaurant</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
<section id="gallery" class="gallery-section">
    <div class="container">
        <div class="gallery-filter text-center" data-aos="fade-up">
            <button class="btn active" data-filter="all">All</button>
            <button class="btn" data-filter="rooms">Rooms</button>
            <button class="btn" data-filter="restaurant">Restaurant</button>
            <button class="btn" data-filter="landscape">Landscape</button>
            <button class="btn" data-filter="spa">Spa</button>
            <button class="btn" data-filter="events">Events</button>
            <button class="btn" data-filter="amenities">Amenities</button>
        </div>
        
        <div class="row gallery">
            <!-- Rooms (6 items) -->
            <div class="col-md-3 gallery-item" data-category="rooms" data-aos="zoom-in" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1584132967334-10e028bd69f7" alt="Deluxe Suite">
                <div class="gallery-item-overlay">
                    <h5>Deluxe Suite</h5>
                    <p>King-size bed with city views</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="rooms" data-aos="zoom-in" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1560185003-3e54b3d491c5" alt="Presidential Suite">
                <div class="gallery-item-overlay">
                    <h5>Presidential Suite</h5>
                    <p>Two-bedroom luxury with private butler</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="rooms" data-aos="zoom-in" data-aos-delay="300">
                <img src="https://images.unsplash.com/photo-1596496198535-35870e3bc474" alt="Penthouse">
                <div class="gallery-item-overlay">
                    <h5>Rooftop Penthouse</h5>
                    <p>Private terrace with infinity pool</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="rooms" data-aos="zoom-in" data-aos-delay="400">
                <img src="https://images.unsplash.com/photo-1582719498240-25d5a3290dfd" alt="Junior Suite">
                <div class="gallery-item-overlay">
                    <h5>Junior Suite</h5>
                    <p>Modern luxury for business travelers</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="rooms" data-aos="zoom-in" data-aos-delay="500">
                <img src="https://images.unsplash.com/photo-1560184803-4d3066ed8a2b" alt="Family Room">
                <div class="gallery-item-overlay">
                    <h5>Family Room</h5>
                    <p>Spacious accommodation for 4 guests</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="rooms" data-aos="zoom-in" data-aos-delay="600">
                <img src="https://images.unsplash.com/photo-1584133961994-297d0b4d44ca" alt="Luxury Twin">
                <div class="gallery-item-overlay">
                    <h5>Luxury Twin</h5>
                    <p>Premium bedding with garden views</p>
                </div>
            </div>

            <!-- Restaurant (6 items) -->
            <div class="col-md-3 gallery-item" data-category="restaurant" data-aos="zoom-in" data-aos-delay="700">
                <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4" alt="Main Dining">
                <div class="gallery-item-overlay">
                    <h5>Main Restaurant</h5>
                    <p>International buffet breakfast</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="restaurant" data-aos="zoom-in" data-aos-delay="800">
                <img src="https://images.unsplash.com/photo-1588702547923-75e48d3d9e4d" alt="Rooftop Dining">
                <div class="gallery-item-overlay">
                    <h5>Rooftop Lounge</h5>
                    <p>Sunset cocktails and light bites</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="restaurant" data-aos="zoom-in" data-aos-delay="900">
                <img src="https://images.unsplash.com/photo-1551632811-561732d1e306" alt="Breakfast Spread">
                <div class="gallery-item-overlay">
                    <h5>Morning Delights</h5>
                    <p>Fresh pastries and artisan coffee</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="restaurant" data-aos="zoom-in" data-aos-delay="1000">
                <img src="https://images.unsplash.com/photo-1514517603703-24107b835c73" alt="Cocktail Bar">
                <div class="gallery-item-overlay">
                    <h5>Mixology Bar</h5>
                    <p>Craft cocktails by award-winning bartenders</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="restaurant" data-aos="zoom-in" data-aos-delay="1100">
                <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae39" alt="Private Dining">
                <div class="gallery-item-overlay">
                    <h5>Private Chef Experience</h5>
                    <p>Custom menus in intimate settings</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="restaurant" data-aos="zoom-in" data-aos-delay="1200">
                <img src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe" alt="Wine Cellar">
                <div class="gallery-item-overlay">
                    <h5>Wine Tasting Room</h5>
                    <p>Curated selection of 200+ labels</p>
                </div>
            </div>

            <!-- Landscape (6 items) -->
            <div class="col-md-3 gallery-item" data-category="landscape" data-aos="zoom-in" data-aos-delay="1300">
                <img src="https://images.unsplash.com/photo-1501785888041-af3ef285b470" alt="Infinity Pool">
                <div class="gallery-item-overlay">
                    <h5>Main Pool</h5>
                    <p>25m lap pool with swim-up bar</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="landscape" data-aos="zoom-in" data-aos-delay="1400">
                <img src="https://images.unsplash.com/photo-1542319396-0d15d0e3e6a4" alt="Garden Oasis">
                <div class="gallery-item-overlay">
                    <h5>Zen Garden</h5>
                    <p>Tranquil Japanese-inspired landscaping</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="landscape" data-aos="zoom-in" data-aos-delay="1500">
                <img src="https://images.unsplash.com/photo-1549880338-65ddcdfd017b" alt="Rooftop Terrace">
                <div class="gallery-item-overlay">
                    <h5>Sky Lounge</h5>
                    <p>360-degree panoramic city views</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="landscape" data-aos="zoom-in" data-aos-delay="1600">
                <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb" alt="Sunset View">
                <div class="gallery-item-overlay">
                    <h5>Golden Hour</h5>
                    <p>Perfect sunset photography spot</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="landscape" data-aos="zoom-in" data-aos-delay="1700">
                <img src="https://images.unsplash.com/photo-1533090475594-26fb350a7c6d" alt="Courtyard">
                <div class="gallery-item-overlay">
                    <h5>Central Courtyard</h5>
                    <p>Landscaped outdoor event space</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="landscape" data-aos="zoom-in" data-aos-delay="1800">
                <img src="https://images.unsplash.com/photo-1552674603-0e1fd9b2398e" alt="Water Features">
                <div class="gallery-item-overlay">
                    <h5>Reflective Pool</h5>
                    <p>Architectural water feature installation</p>
                </div>
            </div>

            <!-- Spa (6 items) -->
            <div class="col-md-3 gallery-item" data-category="spa" data-aos="zoom-in" data-aos-delay="1900">
                <img src="https://images.unsplash.com/photo-1560352819-5e1d8b9a49c0" alt="Treatment Room">
                <div class="gallery-item-overlay">
                    <h5>Massage Therapy</h5>
                    <p>Swedish and deep tissue options</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="spa" data-aos="zoom-in" data-aos-delay="2000">
                <img src="https://images.unsplash.com/photo-1544168199-0d15d0e3e6a4" alt="Wellness Area">
                <div class="gallery-item-overlay">
                    <h5>Ayurvedic Center</h5>
                    <p>Traditional Indian healing therapies</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="spa" data-aos="zoom-in" data-aos-delay="2100">
                <img src="https://images.unsplash.com/photo-1562139555-755b50e60a32" alt="Sauna">
                <div class="gallery-item-overlay">
                    <h5>Steam & Sauna</h5>
                    <p>Relaxation rooms with aromatherapy</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="spa" data-aos="zoom-in" data-aos-delay="2200">
                <img src="https://images.unsplash.com/photo-1588533234864-8a054b0db644" alt="Hydrotherapy">
                <div class="gallery-item-overlay">
                    <h5>Hydrotherapy Pool</h5>
                    <p>Heated water jets for muscle relief</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="spa" data-aos="zoom-in" data-aos-delay="2300">
                <img src="https://images.unsplash.com/photo-1573165670077-30f61e7a5eef" alt="Yoga Studio">
                <div class="gallery-item-overlay">
                    <h5>Yoga Pavilion</h5>
                    <p>Morning sessions with city skyline views</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="spa" data-aos="zoom-in" data-aos-delay="2400">
                <img src="https://images.unsplash.com/photo-1532619670758-8d30e5620633" alt="Beauty Salon">
                <div class="gallery-item-overlay">
                    <h5>Beauty Bar</h5>
                    <p>Hair and makeup services by experts</p>
                </div>
            </div>

            <!-- Events (6 items) -->
            <div class="col-md-3 gallery-item" data-category="events" data-aos="zoom-in" data-aos-delay="2500">
                <img src="https://images.unsplash.com/photo-1521577252920-b20a8662d1f7" alt="Grand Ballroom">
                <div class="gallery-item-overlay">
                    <h5>Ballroom</h5>
                    <p>Host up to 500 guests in luxury</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="events" data-aos="zoom-in" data-aos-delay="2600">
                <img src="https://images.unsplash.com/photo-1542831371-29b0f74f9713" alt="Conference Room">
                <div class="gallery-item-overlay">
                    <h5>Boardroom</h5>
                    <p>State-of-the-art AV equipment</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="events" data-aos="zoom-in" data-aos-delay="2700">
                <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644" alt="Seminar Setup">
                <div class="gallery-item-overlay">
                    <h5>Training Rooms</h5>
                    <p>Flexible spaces for workshops</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="events" data-aos="zoom-in" data-aos-delay="2800">
                <img src="https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3" alt="Celebration">
                <div class="gallery-item-overlay">
                    <h5>Wedding Chapel</h5>
                    <p>Intimate ceremonies with garden backdrop</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="events" data-aos="zoom-in" data-aos-delay="2900">
                <img src="https://images.unsplash.com/photo-1588533234864-8a054b0db644" alt="Exhibition Space">
                <div class="gallery-item-overlay">
                    <h5>Exhibition Hall</h5>
                    <p>10,000 sqft of flexible event space</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="events" data-aos="zoom-in" data-aos-delay="3000">
                <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd" alt="Outdoor Event">
                <div class="gallery-item-overlay">
                    <h5>Poolside Parties</h5>
                    <p>Catering for up to 200 guests</p>
                </div>
            </div>

            <!-- Amenities (6 items) -->
            <div class="col-md-3 gallery-item" data-category="amenities" data-aos="zoom-in" data-aos-delay="3100">
                <img src="https://images.unsplash.com/photo-1553095064-54d0f064b5d2" alt="Fitness Center">
                <div class="gallery-item-overlay">
                    <h5>24/7 Gym</h5>
                    <p>Precor equipment and personal trainers</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="amenities" data-aos="zoom-in" data-aos-delay="3200">
                <img src="https://images.unsplash.com/photo-1587821561119-6076e49a1a4d" alt="Executive Lounge">
                <div class="gallery-item-overlay">
                    <h5>Business Center</h5>
                    <p>Private workstations and printing services</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="amenities" data-aos="zoom-in" data-aos-delay="3300">
                <img src="https://images.unsplash.com/photo-1503264116251-35a269479413" alt="Indoor Pool">
                <div class="gallery-item-overlay">
                    <h5>Lap Pool</h5>
                    <p>Heated indoor swimming facilities</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="amenities" data-aos="zoom-in" data-aos-delay="3400">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c" alt="Kids Club">
                <div class="gallery-item-overlay">
                    <h5>Family Zone</h5>
                    <p>Supervised children's activities</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="amenities" data-aos="zoom-in" data-aos-delay="3500">
                <img src="https://images.unsplash.com/photo-1588533234864-8a054b0db644" alt="Wellness Center">
                <div class="gallery-item-overlay">
                    <h5>Wellness Hub</h5>
                    <p>Nutrition counseling and fitness classes</p>
                </div>
            </div>
            <div class="col-md-3 gallery-item" data-category="amenities" data-aos="zoom-in" data-aos-delay="3600">
                <img src="https://images.unsplash.com/photo-1564507593396-099324b08b8d" alt="Valet Parking">
                <div class="gallery-item-overlay">
                    <h5>Premium Parking</h5>
                    <p>Secure valet and self-parking options</p>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- Lightbox Modal -->
    <div class="modal fade" id="lightboxModal" tabindex="-1" aria-labelledby="lightboxModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" alt="Full-size image" class="img-fluid">
                </div>
                <div class="modal-footer border-0">
                    <a href="#" class="btn btn-lightbox me-3" download>
                        <i class="fas fa-download me-2"></i> Download
                    </a>
                    <button type="button" class="btn btn-lightbox" onclick="copyImageLink()">
                        <i class="fas fa-share-alt me-2"></i> Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5" data-aos="fade-up">What Our Guests Say</h2>
            <div class="row">
                <div class="col-md-4 mb-4" data-aos="fade-up">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">"Absolutely stunning property with impeccable service. The rooms are luxurious and the amenities are world-class!"</p>
                            <h6 class="card-subtitle mb-2 text-muted">- Sarah Johnson</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">"The dining experience was exceptional! Every meal felt like a gourmet adventure."</p>
                            <h6 class="card-subtitle mb-2 text-muted">- Michael Chen</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">"The spa treatments were divine. I left feeling completely refreshed and rejuvenated!"</p>
                            <h6 class="card-subtitle mb-2 text-muted">- Emily Davis</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include '../includes/footer.php'?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
        });

        // Gallery Filter
        document.querySelectorAll('.gallery-filter button').forEach(button => {
            button.addEventListener('click', () => {
                const filter = button.dataset.filter;
                
                document.querySelectorAll('.gallery-filter button').forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                
                document.querySelectorAll('.gallery-item').forEach(item => {
                    const category = item.dataset.category;
                    item.style.display = (filter === 'all' || category === filter) ? 'block' : 'none';
                });
            });
        });

        // Lightbox Functionality
        const lightboxModal = document.getElementById('lightboxModal');
        const modalImage = lightboxModal.querySelector('img');
        const downloadBtn = lightboxModal.querySelector('a[download]');
        
        document.querySelectorAll('.gallery-item').forEach(item => {
            item.addEventListener('click', () => {
                const img = item.querySelector('img');
                modalImage.src = img.src;
                modalImage.alt = img.alt;
                downloadBtn.href = img.src;
                downloadBtn.download = img.alt.replace(/\s+/g, '-').toLowerCase() + '.jpg';
                
                const modal = bootstrap.Modal.getOrCreateInstance(lightboxModal);
                modal.show();
            });
        });

        // Copy Link Function
        function copyImageLink() {
            const currentUrl = window.location.href.split('#')[0];
            const imageUrl = modalImage.src;
            
            navigator.clipboard.writeText(imageUrl)
                .then(() => {
                    alert('Image URL copied to clipboard!');
                })
                .catch(err => {
                    console.error('Failed to copy text: ', err);
                });
        }
    </script>
</body>
</html>