<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EaSyStaY - Luxury Hotel Experience</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a1a1a',
                        secondary: '#ffffff',
                        accent: '#d4af37',
                        light: '#f5f5f5',
                        dark: '#121212',
                        goldLight: '#f5e8c9',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                        script: ['Dancing Script', 'cursive'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 1s ease-in-out',
                        'slide-up': 'slideUp 0.8s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'tilt': 'tilt 10s infinite linear',
                        'border-pulse': 'borderPulse 2s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        tilt: {
                            '0%, 100%': { transform: 'rotate(0deg)' },
                            '25%': { transform: 'rotate(1deg)' },
                            '75%': { transform: 'rotate(-1deg)' },
                        },
                        borderPulse: {
                            '0%': { 'border-color': 'rgba(212, 175, 55, 0.5)' },
                            '50%': { 'border-color': 'rgba(212, 175, 55, 1)' },
                            '100%': { 'border-color': 'rgba(212, 175, 55, 0.5)' },
                        }
                    }
                }
            }
        }
    </script>
    <style type="text/css">
        body {
            scroll-behavior: smooth;
        }
        
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
            color: #d4af37;
        }
        
        .testimonial-card {
            transition: all 0.4s ease;
        }
        
        .testimonial-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.2);
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: #d4af37;
            bottom: 0;
            left: 50%;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover::after {
            width: 80%;
            left: 10%;
        }
        
        .cta-button {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: 0.5s;
            z-index: -1;
        }
        
        .cta-button:hover::before {
            left: 100%;
        }
        
        .floating {
            animation: float 6s ease-in-out infinite;
        }
        
        .tilt-animation {
            animation: tilt 10s infinite linear;
        }
        
        .border-pulse {
            animation: borderPulse 2s infinite;
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
        
        .gold-border {
            border: 2px solid #d4af37;
        }
        
        .gold-border:hover {
            border-color: #f5e8c9;
        }
        
        .amenity-icon {
            transition: all 0.3s ease;
        }
        
        .amenity-item:hover .amenity-icon {
            transform: scale(1.2);
            color: #d4af37;
        }
        
        .counter-item {
            transition: all 0.3s ease;
        }
        
        .counter-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .booking-form {
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }
        
        .booking-form input:focus, .booking-form select:focus {
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.3);
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
</head>
<body class="font-sans bg-light text-primary">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-gradient-to-r from-primary to-primary shadow-lg z-50 transition-all duration-300 border-b border-accent/20">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <a href="#" class="text-4xl font-script text-secondary drop-shadow-md flex items-center">
                    <i class="fas fa-hotel text-accent mr-2"></i>
                    EaSyStaY
                </a>
                
                <!-- Mobile menu button -->
                <div class="lg:hidden">
                    <button id="mobile-menu-button" class="text-secondary focus:outline-none">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Desktop menu -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#hero" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300">Home</a>
                    <a href="./pages/rooms.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300">Rooms & Suites</a>
                    <a href="#features" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300">Amenities</a>
                   
                    <a href="#testimonials" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300">Testimonials</a>
                    <a href="./pages/about_us.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300">About Us</a>
                    <a href="./auth/login.php" class="cta-button bg-accent text-primary font-semibold px-6 py-2 rounded-xl shadow-lg hover:bg-dark hover:text-secondary transition-all duration-300 transform hover:-translate-y-1 ml-4">Book Now</a>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden lg:hidden bg-primary pb-4">
                <a href="#hero" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-home mr-3"></i>Home</a>
                <a href="./pages/rooms.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-bed mr-3"></i>Rooms & Suites</a>
                <a href="#features" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-spa mr-3"></i>Amenities</a>
                <a href="#dining" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-utensils mr-3"></i>Dining</a>
                <a href="#testimonials" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-star mr-3"></i>Testimonials</a>
                <a href="./pages/about_us.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-info-circle mr-3"></i>About Us</a>
                <a href="./auth/login.php" class="block bg-accent text-primary font-semibold px-4 py-3 rounded-lg mx-4 mt-3 text-center">
                    <i class="fas fa-calendar-check mr-2"></i>Book Now
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="hero" class="hero-bg h-screen flex items-center justify-center relative overflow-hidden" style="background-image: url('./assets/images/hero/P (1).jpg');">
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/80"></div>
        <div class="container mx-auto px-4 z-10 text-center animate-fade-in">
            <div class="mb-8 animate-slide-up">
                
                <h1 class="text-4xl md:text-6xl font-serif font-bold text-secondary mt-4 mb-6 leading-tight">Experience Unparalleled <span class="text-accent">Luxury</span></h1>
                <p class="text-xl md:text-2xl text-secondary/90 max-w-3xl mx-auto">Where timeless elegance meets contemporary comfort in the heart of the city</p>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4 animate-slide-up">
                <a href="./pages/rooms.php" class="cta-button bg-accent text-primary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                    Explore Our Suites <i class="fas fa-arrow-right ml-2"></i>
                </a>
               
            </div>
        </div>

       
        
    </section>
    
    <!-- Rooms Section -->
    <section id="rooms" class="py-16 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Exquisite Rooms & Suites</h2>
                
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Room 1 -->
                <div class="room-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
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
                            <a href="./pages/rooms.php" class="flex items-center text-accent font-semibold hover:text-dark transition-colors duration-300">
                                View Details <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                            <button class="bg-accent text-primary px-4 py-2 rounded-lg font-medium hover:bg-dark transition-colors">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Room 2 -->
                <div class="room-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
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
                            <a href="./pages/rooms.php" class="flex items-center text-accent font-semibold hover:text-dark transition-colors duration-300">
                                View Details <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                            <button class="bg-accent text-primary px-4 py-2 rounded-lg font-medium hover:bg-dark transition-colors">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Room 3 -->
                <div class="room-card bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
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
                            <a href="./pages/rooms.php" class="flex items-center text-accent font-semibold hover:text-dark transition-colors duration-300">
                                View Details <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                            <button class="bg-accent text-primary px-4 py-2 rounded-lg font-medium hover:bg-dark transition-colors">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <a href="./pages/rooms.php" class="inline-block bg-primary text-secondary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 hover:bg-dark">
                    View All Accommodations <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Amenities Section -->
    <section id="features" class="py-16 bg-gradient-to-b from-light to-white">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-accent font-semibold tracking-wider uppercase">Luxury Amenities</span>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Indulge in Premium Experiences</h2>
               
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg text-center transition-all duration-300 hover:shadow-xl group">
                    <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full feature-icon group-hover:bg-accent group-hover:text-white">
                        <i class="fas fa-spa"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Luxury Spa</h3>
                    <p class="text-gray-600">Rejuvenate your senses with our premium spa treatments and relaxation therapies.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg text-center transition-all duration-300 hover:shadow-xl group">
                    <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full feature-icon group-hover:bg-accent group-hover:text-white">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Gourmet Dining</h3>
                    <p class="text-gray-600">Experience culinary excellence at our award-winning restaurants and bars.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg text-center transition-all duration-300 hover:shadow-xl group">
                    <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full feature-icon group-hover:bg-accent group-hover:text-white">
                        <i class="fas fa-swimming-pool"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Infinity Pool</h3>
                    <p class="text-gray-600">Enjoy breathtaking views from our rooftop infinity pool and sun deck.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-lg text-center transition-all duration-300 hover:shadow-xl group">
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
            <div class="text-center max-w-3xl mx-auto mb-16">
              
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-secondary mt-4 mb-6">What Our Guests Say</h2>
                
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="testimonial-card bg-white/10 p-8 rounded-xl backdrop-blur-sm">
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
                <div class="testimonial-card bg-white/10 p-8 rounded-xl backdrop-blur-sm">
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
                <div class="testimonial-card bg-white/10 p-8 rounded-xl backdrop-blur-sm">
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
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-accent font-semibold tracking-wider uppercase">Visual Journey</span>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Discover EaSyStaY Through Our Gallery</h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="rounded-xl overflow-hidden tilt-animation">
                    <img src="./assets/images/gallery page/img1.jpg" alt="Hotel Lobby" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
                </div>
                <div class="rounded-xl overflow-hidden tilt-animation">
                    <img src="./assets/images/gallery page/img2.jpg" alt="Spa" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
                </div>
                <div class="rounded-xl overflow-hidden tilt-animation">
                    <img src="./assets/images/gallery page/img3.jpg" alt="Restaurant" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
                </div>
                <div class="rounded-xl overflow-hidden tilt-animation">
                    <img src="./assets/images/gallery page/img4.jpg" alt="Pool" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
                </div>
                <div class="rounded-xl overflow-hidden tilt-animation">
                    <img src="./assets/images/gallery page/img5.jpg" alt="Suite" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
                </div>
                <div class="rounded-xl overflow-hidden tilt-animation">
                    <img src="./assets/images/gallery page/img6.jpg" alt="Bar" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
                </div>
                <div class="rounded-xl overflow-hidden tilt-animation">
                    <img src="./assets/images/gallery page/img7.jpg" alt="Gym" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
                </div>
                <div class="rounded-xl overflow-hidden tilt-animation">
                    <img src="./assets/images/gallery page/img8.jpg" alt="Event Space" class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110">
                </div>
            </div>
        </div>
    </section>
    

    <!-- Footer -->
    <footer class="bg-dark text-secondary pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-3xl font-script mb-4 flex items-center">
                        <i class="fas fa-hotel text-accent mr-2"></i>
                        EaSyStaY
                    </h3>
                    <p class="mb-6 text-secondary/80">Your sanctuary in the city, where luxury meets comfort and every stay becomes a cherished memory.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-secondary hover:bg-accent hover:text-primary transition-all">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-secondary hover:bg-accent hover:text-primary transition-all">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-secondary hover:bg-accent hover:text-primary transition-all">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-secondary hover:bg-accent hover:text-primary transition-all">
                            <i class="fab fa-pinterest"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-4 pb-2 border-b border-accent/30">Explore</h4>
                    <ul class="space-y-3">
                        <li><a href="#hero" class="hover:text-accent transition-colors duration-300 flex items-center"><i class="fas fa-chevron-right text-xs text-accent mr-2"></i> Home</a></li>
                        <li><a href="./pages/rooms.php" class="hover:text-accent transition-colors duration-300 flex items-center"><i class="fas fa-chevron-right text-xs text-accent mr-2"></i> Rooms & Suites</a></li>
                        <li><a href="#features" class="hover:text-accent transition-colors duration-300 flex items-center"><i class="fas fa-chevron-right text-xs text-accent mr-2"></i> Amenities</a></li>
                        <li><a href="#dining" class="hover:text-accent transition-colors duration-300 flex items-center"><i class="fas fa-chevron-right text-xs text-accent mr-2"></i> Dining Experiences</a></li>
                        <li><a href="#testimonials" class="hover:text-accent transition-colors duration-300 flex items-center"><i class="fas fa-chevron-right text-xs text-accent mr-2"></i> Guest Reviews</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-bold mb-4 pb-2 border-b border-accent/30">Contact Us</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-accent"></i>
                            <span>123 Luxury Avenue, Golden District, GC 10001</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-accent"></i>
                            <span>+1 (888) 123-4567</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-accent"></i>
                            <span>reservations@easystay.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-concierge-bell mr-3 text-accent"></i>
                            <span>24/7 Concierge Service</span>
                        </li>
                    </ul>
                </div>
                
                
            </div>
            
            <div class="border-t border-secondary/20 mt-12 pt-8 text-center text-secondary/60">
                <p>&copy; 2023 EaSyStaY Luxury Hotels. All rights reserved. | Designed with <i class="fas fa-heart text-accent"></i> for exceptional stays</p>
            </div>
        </div>
    </footer>

    <!-- Floating Book Now Button (Mobile) -->
    <a href="./auth/login.php" class="fixed bottom-6 right-6 lg:hidden bg-accent text-primary w-16 h-16 rounded-full flex items-center justify-center shadow-lg z-50 transform transition-all hover:scale-110 hover:shadow-xl">
        <i class="fas fa-calendar-check text-xl"></i>
    </a>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
            menu.classList.toggle('animate-slide-up');
        });

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

        // Scroll animation for elements
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.animate-on-scroll');
            
            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (elementPosition < windowHeight - 100) {
                    element.classList.add('animate-fade-in', 'animate-slide-up');
                }
            });
        };

        // Initial check
        window.addEventListener('load', animateOnScroll);
        
        // Check on scroll
        window.addEventListener('scroll', animateOnScroll);
        
        // Initialize
        animateOnScroll();
    </script>
</body>any
</html>