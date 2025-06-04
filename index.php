<?php
// Include the header file
include_once 'includes/header.php';
?>

<style>
    .hero-bg {
        background-size: cover;
        background-position: center;
        background-blend-mode: overlay;
        background-color: rgba(0, 0, 0, 0.5);
        transition: background-image 1s ease-in-out;
    }
    
    .room-card {
        perspective: 1000px;
    }
    
    .room-card-inner {
        transition: transform 0.6s;
        transform-style: preserve-3d;
    }
    
    .room-card:hover .room-card-inner {
        transform: rotateY(10deg);
    }
    
    .room-img {
        transition: transform 0.5s ease;
    }
    
    .room-card:hover .room-img {
        transform: scale(1.05);
    }
    
    .feature-card {
        overflow: hidden;
        position: relative;
    }
    
    .feature-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.1), transparent);
        transition: 0.5s;
    }
    
    .feature-card:hover::before {
        left: 100%;
    }
    
    .feature-icon {
        transition: all 0.5s ease;
    }
    
    .feature-card:hover .feature-icon {
        transform: rotateY(180deg);
        color:rgb(255, 255, 255);
    }
    
    .testimonial-card {
        transition: all 0.4s ease;
    }
    
    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2);
    }
    
    .parallax {
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
    
    .overlay-gradient {
        background: linear-gradient(135deg, rgba(26, 26, 26, 0.9) 0%, rgba(18, 18, 18, 0.95) 100%);
    }
    
    .amenity-icon {
        transition: all 0.3s ease;
    }
    
    .amenity-item:hover .amenity-icon {
        transform: scale(1.2);
        color: #d4af37;
    }
    
    @media (max-width: 768px) {
        .hero-bg {
            background-attachment: scroll;
        }
        .parallax {
            background-attachment: scroll;
        }
    }
</style>

<!-- Hero Section -->
<section id="hero" class="hero-bg h-screen flex items-center justify-center relative overflow-hidden" style="background-image: url('./assets/images/hero/P (1).jpg');">
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/80"></div>
    <div class="container mx-auto px-4 z-10 text-center" data-aos="fade-up" data-aos-delay="100">
        <div class="mb-8">
            <h1 class="text-4xl md:text-6xl font-serif font-bold text-secondary mt-4 mb-6 leading-tight">Experience Unparalleled <span class="text-accent">Luxury</span></h1>
            <p class="text-xl md:text-2xl text-secondary/90 max-w-3xl mx-auto">Where timeless elegance meets contemporary comfort in the heart of the city</p>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="300">
            <a href="<?php echo $base_url; ?>/pages/rooms.php" class="cta-button bg-accent text-primary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                Explore Our Suites <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Rooms Section -->
<section id="rooms" class="py-16 bg-light">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Exquisite Rooms & Suites</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Room 1 -->
            <div class="room-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl" data-aos="fade-up" data-aos-delay="100">
                <div class="overflow-hidden">
                    <img src="./assets/images/rooms/first.jpeg" alt="Deluxe Suite" class="w-full h-64 object-cover room-img">
                </div>
                <div class="p-6 room-card-inner">
                    <div class="flex justify-between items-start">
                        <h3 class="text-2xl font-bold mb-2">Deluxe Suite</h3>
                        <span class="text-accent font-bold">$250<small class="text-gray-500 font-normal">/night</small></span>
                    </div>
                    <p class="text-gray-600 mb-4">Spacious suite with king-size bed, private balcony, and panoramic city views.</p>
                    
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm">Free Wi-Fi</span>
                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm">Mini Bar</span>
                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm">24/7 Service</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <a href="<?php echo $base_url; ?>/pages/rooms.php" class="flex items-center text-accent font-semibold hover:text-dark transition-colors duration-300">
                            View Details <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <button class="bg-accent text-primary px-4 py-2 rounded-lg font-medium hover:bg-dark transition-colors">
                            Book Now
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Room 2 -->
            <div class="room-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl" data-aos="fade-up" data-aos-delay="200">
                <div class="overflow-hidden">
                    <img src="./assets/images/rooms/second.jpg" alt="Executive Suite" class="w-full h-64 object-cover room-img">
                </div>
                <div class="p-6 room-card-inner">
                    <div class="flex justify-between items-start">
                        <h3 class="text-2xl font-bold mb-2">Executive Suite</h3>
                        <span class="text-accent font-bold">$400<small class="text-gray-500 font-normal">/night</small></span>
                    </div>
                    <p class="text-gray-600 mb-4">Luxurious suite with separate living area, premium mattress, and spa bathroom.</p>
                    
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm">Personal Concierge</span>
                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm">Private Terrace</span>
                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm">Jacuzzi Tub</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <a href="<?php echo $base_url; ?>/pages/rooms.php" class="flex items-center text-accent font-semibold hover:text-dark transition-colors duration-300">
                            View Details <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <button class="bg-accent text-primary px-4 py-2 rounded-lg font-medium hover:bg-dark transition-colors">
                            Book Now
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Room 3 -->
            <div class="room-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl" data-aos="fade-up" data-aos-delay="300">
                <div class="overflow-hidden">
                    <img src="./assets/images/rooms/third.jpg" alt="Presidential Suite" class="w-full h-64 object-cover room-img">
                </div>
                <div class="p-6 room-card-inner">
                    <div class="flex justify-between items-start">
                        <h3 class="text-2xl font-bold mb-2">Presidential Suite</h3>
                        <span class="text-accent font-bold">$650<small class="text-gray-500 font-normal">/night</small></span>
                    </div>
                    <p class="text-gray-600 mb-4">Ultimate luxury with private dining, butler service, and panoramic city views.</p>
                    
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm">Private Chef</span>
                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm">Home Theater</span>
                        <span class="bg-accent/10 text-accent px-3 py-1 rounded-full text-sm">Panoramic View</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <a href="<?php echo $base_url; ?>/pages/rooms.php" class="flex items-center text-accent font-semibold hover:text-dark transition-colors duration-300">
                            View Details <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        <button class="bg-accent text-primary px-4 py-2 rounded-lg font-medium hover:bg-dark transition-colors">
                            Book Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12" data-aos="fade-up">
            <a href="<?php echo $base_url; ?>/pages/rooms.php" class="inline-block bg-primary text-secondary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 hover:bg-dark">
                View All Accommodations <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Amenities Section -->
<section id="features" class="py-16 bg-gradient-to-b from-light to-white">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-accent font-semibold tracking-wider uppercase">Luxury Amenities</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Indulge in Premium Experiences</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="feature-card bg-white p-8 rounded-xl shadow-lg text-center transition-all duration-300 hover:shadow-xl group" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full feature-icon group-hover:bg-accent group-hover:text-white">
                    <i class="fas fa-spa"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Luxury Spa</h3>
                <p class="text-gray-600">Rejuvenate your senses with our premium spa treatments and relaxation therapies.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-card bg-white p-8 rounded-xl shadow-lg text-center transition-all duration-300 hover:shadow-xl group" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full feature-icon group-hover:bg-accent group-hover:text-white">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Gourmet Dining</h3>
                <p class="text-gray-600">Experience culinary excellence at our award-winning restaurants and bars.</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-card bg-white p-8 rounded-xl shadow-lg text-center transition-all duration-300 hover:shadow-xl group" data-aos="fade-up" data-aos-delay="300">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full feature-icon group-hover:bg-accent group-hover:text-white">
                    <i class="fas fa-swimming-pool"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Infinity Pool</h3>
                <p class="text-gray-600">Enjoy breathtaking views from our rooftop infinity pool and sun deck.</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="feature-card bg-white p-8 rounded-xl shadow-lg text-center transition-all duration-300 hover:shadow-xl group" data-aos="fade-up" data-aos-delay="400">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full feature-icon group-hover:bg-accent group-hover:text-white">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Fitness Center</h3>
                <p class="text-gray-600">State-of-the-art fitness facilities with personal training sessions available.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section id="testimonials" class="py-16 bg-gradient-to-r from-primary to-dark text-secondary">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-secondary mt-4 mb-6">What Our Guests Say</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="testimonial-card bg-white/10 p-8 rounded-xl backdrop-blur-sm" data-aos="fade-up" data-aos-delay="100">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden mr-4 border-2 border-accent">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="John Doe" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Robert Johnson</h4>
                        <div class="flex text-accent">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p class="italic mb-4">"The attention to detail at EaSyStaY is extraordinary. From the luxurious bedding to the impeccable service, every aspect of our stay was perfect. The rooftop pool with city views was the highlight of our trip!"</p>
                <p class="text-sm text-secondary/80">- Business Traveler, USA</p>
            </div>
            
            <!-- Testimonial 2 -->
            <div class="testimonial-card bg-white/10 p-8 rounded-xl backdrop-blur-sm" data-aos="fade-up" data-aos-delay="200">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden mr-4 border-2 border-accent">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Sarah Johnson" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Sophia Williams</h4>
                        <div class="flex text-accent">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
                <p class="italic mb-4">"Our anniversary celebration was made truly special by the EaSyStaY team. They surprised us with champagne and rose petals in our suite. The dining experience was exceptional, with a chef who accommodated our dietary preferences perfectly."</p>
                <p class="text-sm text-secondary/80">- Honeymooners, UK</p>
            </div>
            
            <!-- Testimonial 3 -->
            <div class="testimonial-card bg-white/10 p-8 rounded-xl backdrop-blur-sm" data-aos="fade-up" data-aos-delay="300">
                <div class="flex items-center mb-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden mr-4 border-2 border-accent">
                        <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Michael Chen" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Michael Chen</h4>
                        <div class="flex text-accent">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p class="italic mb-4">"As a frequent traveler, I've stayed in many luxury hotels, but EaSyStaY stands out for its perfect blend of modern luxury and genuine hospitality. The executive lounge and spa facilities are world-class. I wouldn't stay anywhere else in the city."</p>
                <p class="text-sm text-secondary/80">- Frequent Guest, Singapore</p>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="py-16 bg-light">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-accent font-semibold tracking-wider uppercase">Visual Journey</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Discover EaSyStaY Through Our Gallery</h2>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="rounded-xl overflow-hidden tilt-animation" data-aos="zoom-in" data-aos-delay="100">
                <img src="./assets/images/gallery page/img1.jpg" alt="Hotel Lobby" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
            </div>
            <div class="rounded-xl overflow-hidden tilt-animation" data-aos="zoom-in" data-aos-delay="150">
                <img src="./assets/images/gallery page/img2.jpg" alt="Spa" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
            </div>
            <div class="rounded-xl overflow-hidden tilt-animation" data-aos="zoom-in" data-aos-delay="200">
                <img src="./assets/images/gallery page/img3.jpg" alt="Restaurant" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
            </div>
            <div class="rounded-xl overflow-hidden tilt-animation" data-aos="zoom-in" data-aos-delay="250">
                <img src="./assets/images/gallery page/img4.jpg" alt="Pool" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
            </div>
            <div class="rounded-xl overflow-hidden tilt-animation" data-aos="zoom-in" data-aos-delay="300">
                <img src="./assets/images/gallery page/img5.jpg" alt="Suite" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
            </div>
            <div class="rounded-xl overflow-hidden tilt-animation" data-aos="zoom-in" data-aos-delay="350">
                <img src="./assets/images/gallery page/img6.jpg" alt="Bar" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
            </div>
            <div class="rounded-xl overflow-hidden tilt-animation" data-aos="zoom-in" data-aos-delay="400">
                <img src="./assets/images/gallery page/img7.jpg" alt="Gym" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
            </div>
            <div class="rounded-xl overflow-hidden tilt-animation" data-aos="zoom-in" data-aos-delay="450">
                <img src="./assets/images/gallery page/img8.jpg" alt="Event Space" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
            </div>
        </div>
    </div>
</section>

<!-- Floating Book Now Button (Mobile) -->
<a href="<?php echo $base_url; ?>/auth/login.php" class="fixed bottom-6 right-6 lg:hidden bg-accent text-primary w-16 h-16 rounded-full flex items-center justify-center shadow-lg z-50 transform transition-all hover:scale-110 hover:shadow-xl">
    <i class="fas fa-calendar-check text-xl"></i>
</a>

<script>
    // Hero image slider
    const heroSection = document.querySelector('.hero-bg');
    const backgroundImages = [
        './assets/images/hero/img1.jpg',
        './assets/images/hero/img4.jpg',
        './assets/images/hero/img5.jpg',
        './assets/images/hero/img6.jpg',
        './assets/images/hero/image.jpg',
        './assets/images/hero/img7.jpg'
    ];
    
    let currentSlide = 0;
    
    function changeBackground() {
        currentSlide = (currentSlide + 1) % backgroundImages.length;
        heroSection.style.backgroundImage = `url('${backgroundImages[currentSlide]}')`;  
    }
    
    // Auto-slide functionality with 5-second interval
    setInterval(changeBackground, 5000);

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                const mobileMenu = document.getElementById('mobile-menu');
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
            }
        });
    });

    // Back to top button functionality
    const backToTopButton = document.getElementById('back-to-top');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backToTopButton.classList.remove('opacity-0', 'translate-y-10');
            backToTopButton.classList.add('opacity-100', 'translate-y-0');
        } else {
            backToTopButton.classList.remove('opacity-100', 'translate-y-0');
            backToTopButton.classList.add('opacity-0', 'translate-y-10');
        }
    });
    
    backToTopButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>

<?php
// Include the footer file
include_once 'includes/footer.php';
?>