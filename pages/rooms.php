<?php
// Include the header file
include_once '../includes/header.php';

// Include database connection
require_once '../config/db.con.php';

// Define room data
$rooms = [
    ['RoomID' => '101', 'RoomNumber' => '101', 'RoomType' => 'Standard', 'Description' => 'Cozy single room with basic amenities', 'BasePrice' => 100.00, 'AvailabilityStatus' => 'Available'],
    ['RoomID' => '202', 'RoomNumber' => '202', 'RoomType' => 'Deluxe', 'Description' => 'Spacious room with king-size bed', 'BasePrice' => 150.00, 'AvailabilityStatus' => 'Available'],
    ['RoomID' => '303', 'RoomNumber' => '303', 'RoomType' => 'Suite', 'Description' => 'Luxury suite with living area', 'BasePrice' => 250.00, 'AvailabilityStatus' => 'Available'],
    ['RoomID' => '404', 'RoomNumber' => '404', 'RoomType' => 'Family', 'Description' => 'Two-bedroom suite for families', 'BasePrice' => 200.00, 'AvailabilityStatus' => 'Available']
];

// Define package data
$packages = [
    ['PackageID' => '1', 'PackageName' => 'Breakfast Package', 'Description' => 'Daily breakfast for 2 guests', 'Price' => 25.00],
    ['PackageID' => '2', 'PackageName' => 'Spa Package', 'Description' => 'One-hour massage and sauna access', 'Price' => 80.00],
    ['PackageID' => '3', 'PackageName' => 'Romance Package', 'Description' => 'Champagne and flower arrangement', 'Price' => 50.00]
];
?>

<!-- Hero Section -->
<section id="hero" class="hero-bg h-screen flex items-center justify-center relative overflow-hidden" style="background-image: url('../assets/images/other_hero/rooms-hero.jpg');">
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/80"></div>
    <div class="container mx-auto px-4 z-10 text-center" data-aos="fade-up" data-aos-delay="100">
        <div class="mb-8">
            <h1 class="text-4xl md:text-6xl font-serif font-bold text-secondary mt-4 mb-6 leading-tight">Rooms & <span class="text-accent">Suites</span></h1>
            <p class="text-xl md:text-2xl text-secondary/90 max-w-3xl mx-auto">Experience unparalleled luxury in our meticulously designed accommodations</p>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="300">
            <a href="#packages" class="cta-button bg-accent text-primary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                View Packages <i class="fas fa-arrow-right ml-2"></i>
            </a>
            <a href="../users/booking.php" class="cta-button bg-transparent text-secondary border-2 border-secondary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 hover:bg-secondary/10 hover:border-accent hover:text-accent">
                Book Now <i class="fas fa-calendar-check ml-2"></i>
            </a>
        </div>
    </div>
    
    <!-- Animated scroll indicator -->
    <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 text-secondary animate-bounce">
        <a href="#filter" class="flex flex-col items-center">
            <span class="text-sm mb-2">Scroll Down</span>
            <i class="fas fa-chevron-down text-accent"></i>
        </a>
    </div>
</section>

<!-- Room Filter Section -->
<section class="py-20 bg-light">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-accent font-semibold tracking-wider uppercase">Accommodations</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Our Luxurious Rooms</h2>
            <p class="text-xl text-gray-600">Experience the perfect blend of comfort and elegance in our thoughtfully designed rooms.</p>
        </div>
        
        <div class="flex justify-center mb-10" data-aos="fade-up">
            <div class="inline-flex bg-gray-200 rounded-lg p-1">
                <button class="filter-btn px-4 py-2 rounded-lg bg-primary text-secondary active" data-filter="all">All Rooms</button>
                <button class="filter-btn px-4 py-2 rounded-lg bg-gray-200 text-primary" data-filter="Standard">Standard</button>
                <button class="filter-btn px-4 py-2 rounded-lg bg-gray-200 text-primary" data-filter="Deluxe">Deluxe</button>
                <button class="filter-btn px-4 py-2 rounded-lg bg-gray-200 text-primary" data-filter="Suite">Suite</button>
                <button class="filter-btn px-4 py-2 rounded-lg bg-gray-200 text-primary" data-filter="Family">Family</button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Room 1: Standard -->
            <div class="filter-item bg-white rounded-xl overflow-hidden shadow-lg border border-gray-200 transition-all duration-300 hover:shadow-xl hover:border-accent/30 group" data-type="Standard" data-aos="fade-up" data-aos-delay="100">
                <div class="relative overflow-hidden">
                    <img src="../assets/images/rooms/standard-room.jpg" alt="Standard Room" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $100<span class="text-sm">/night</span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-xl font-bold text-white">Standard Room</h3>
                        <div class="flex items-center text-white/90">
                            <i class="fas fa-user-friends mr-2"></i> 2 Guests
                            <span class="mx-2">•</span>
                            <i class="fas fa-bed mr-2"></i> Room 101
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">Free WiFi</span>
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">TV</span>
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">AC</span>
                    </div>
                    <p class="text-gray-600 mb-6">Cozy single room with basic amenities, perfect for solo travelers or couples.</p>
                    
                </div>
            </div>
            
            <!-- Room 2: Deluxe -->
            <div class="filter-item bg-white rounded-xl overflow-hidden shadow-lg border border-gray-200 transition-all duration-300 hover:shadow-xl hover:border-accent/30 group" data-type="Deluxe" data-aos="fade-up" data-aos-delay="150">
                <div class="relative overflow-hidden">
                    <img src="../assets/images/rooms/deluxe-room.jpg" alt="Deluxe Room" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $150<span class="text-sm">/night</span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-xl font-bold text-white">Deluxe Room</h3>
                        <div class="flex items-center text-white/90">
                            <i class="fas fa-user-friends mr-2"></i> 2 Guests
                            <span class="mx-2">•</span>
                            <i class="fas fa-bed mr-2"></i> Room 202
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">Free WiFi</span>
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">Minibar</span>
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">King Bed</span>
                    </div>
                    <p class="text-gray-600 mb-6">Spacious room with king-size bed, perfect for couples seeking extra comfort.</p>
                   
                </div>
            </div>
            
            <!-- Room 3: Suite -->
            <div class="filter-item bg-white rounded-xl overflow-hidden shadow-lg border border-gray-200 transition-all duration-300 hover:shadow-xl hover:border-accent/30 group" data-type="Suite" data-aos="fade-up" data-aos-delay="200">
                <div class="relative overflow-hidden">
                    <img src="../assets/images/rooms/executive_suite.jpg" alt="Suite" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $250<span class="text-sm">/night</span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-xl font-bold text-white">Luxury Suite</h3>
                        <div class="flex items-center text-white/90">
                            <i class="fas fa-user-friends mr-2"></i> 2-3 Guests
                            <span class="mx-2">•</span>
                            <i class="fas fa-bed mr-2"></i> Room 303
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">Free WiFi</span>
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">Living Area</span>
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">Premium View</span>
                    </div>
                    <p class="text-gray-600 mb-6">Luxury suite with living area, perfect for those seeking extra space and premium amenities.</p>
                    
                </div>
            </div>
            
            <!-- Room 4: Family -->
            <div class="filter-item bg-white rounded-xl overflow-hidden shadow-lg border border-gray-200 transition-all duration-300 hover:shadow-xl hover:border-accent/30 group" data-type="Family" data-aos="fade-up" data-aos-delay="250">
                <div class="relative overflow-hidden">
                    <img src="../assets/images/rooms/family-room.jpg" alt="Family Room" class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $200<span class="text-sm">/night</span>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-4">
                        <h3 class="text-xl font-bold text-white">Family Suite</h3>
                        <div class="flex items-center text-white/90">
                            <i class="fas fa-user-friends mr-2"></i> 4-5 Guests
                            <span class="mx-2">•</span>
                            <i class="fas fa-bed mr-2"></i> Room 404
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">Free WiFi</span>
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">Two Bedrooms</span>
                        <span class="px-3 py-1 bg-accent/10 text-accent rounded-full text-sm font-medium">Kitchenette</span>
                    </div>
                    <p class="text-gray-600 mb-6">Two-bedroom suite for families, perfect for those traveling with children or in small groups.</p>
                   
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Packages Section -->
<section id="packages" class="py-16 bg-primary text-secondary">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-accent font-semibold tracking-wider uppercase">Special Offers</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-secondary mt-4 mb-6">Exclusive Packages</h2>
            <p class="text-xl text-secondary/80">Enhance your stay with our carefully curated packages designed for maximum comfort and value.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Package 1: Breakfast Package -->
            <div class="bg-dark rounded-xl overflow-hidden shadow-lg border border-accent/20" data-aos="fade-up" data-aos-delay="100">
                <div class="relative">
                    <img src="../assets/images/packages/breakfast.jpg" alt="Breakfast Package" class="w-full h-64 object-cover">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $25<span class="text-sm">/package</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-secondary mb-2">Breakfast Package</h3>
                    <p class="text-secondary/80 mb-4">Daily breakfast for 2 guests</p>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Available Room Types:</h4>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors active">Standard</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Deluxe</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Suite</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Family</button>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Package Includes:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Full breakfast buffet</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Fresh juices and coffee</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Continental and local options</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Early breakfast available on request</li>
                        </ul>
                    </div>
                    
                </div>
            </div>
            
            <!-- Package 2: Spa Package -->
            <div class="bg-dark rounded-xl overflow-hidden shadow-lg border border-accent/20" data-aos="fade-up" data-aos-delay="200">
                <div class="relative">
                    <img src="../assets/images/packages/spa.jpg" alt="Spa Package" class="w-full h-64 object-cover">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $80<span class="text-sm">/package</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-secondary mb-2">Spa Package</h3>
                    <p class="text-secondary/80 mb-4">One-hour massage and sauna access</p>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Available Room Types:</h4>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Standard</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors active">Deluxe</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Suite</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Family</button>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Package Includes:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> One-hour full body massage</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Sauna and steam room access</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Aromatherapy options</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Complimentary herbal tea</li>
                        </ul>
                    </div>
                    
                </div>
            </div>
            
            <!-- Package 3: Romance Package -->
            <div class="bg-dark rounded-xl overflow-hidden shadow-lg border border-accent/20" data-aos="fade-up" data-aos-delay="300">
                <div class="relative">
                    <img src="../assets/images/packages/romance.jpg" alt="Romance Package" class="w-full h-64 object-cover">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $50<span class="text-sm">/package</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-secondary mb-2">Romance Package</h3>
                    <p class="text-secondary/80 mb-4">Champagne and flower arrangement</p>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Available Room Types:</h4>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Standard</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Deluxe</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors active">Suite</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Family</button>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Package Includes:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Bottle of premium champagne</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Fresh flower arrangement</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Chocolate-covered strawberries</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Late checkout (subject to availability)</li>
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-light">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-accent font-semibold tracking-wider uppercase">Room Features</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Standard Amenities</h2>
            <p class="text-xl text-gray-600">Every room at EaSyStaY comes equipped with these premium amenities for your comfort.</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <!-- Feature 1 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-accent/10 text-accent text-2xl rounded-full group-hover:bg-accent group-hover:text-primary transition-all duration-300 transform group-hover:scale-110">
                    <i class="fas fa-wifi"></i>
                </div>
                <h3 class="font-bold mb-2 group-hover:text-accent transition-colors">High-Speed WiFi</h3>
                <p class="text-gray-600 text-sm">Stay connected with complimentary high-speed internet access.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="150">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-accent/10 text-accent text-2xl rounded-full group-hover:bg-accent group-hover:text-primary transition-all duration-300 transform group-hover:scale-110">
                    <i class="fas fa-tv"></i>
                </div>
                <h3 class="font-bold mb-2 group-hover:text-accent transition-colors">Smart TV</h3>
                <p class="text-gray-600 text-sm">Enjoy your favorite shows on our 4K smart TVs with streaming services.</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-accent/10 text-accent text-2xl rounded-full group-hover:bg-accent group-hover:text-primary transition-all duration-300 transform group-hover:scale-110">
                    <i class="fas fa-snowflake"></i>
                </div>
                <h3 class="font-bold mb-2 group-hover:text-accent transition-colors">Climate Control</h3>
                <p class="text-gray-600 text-sm">Personalize your room temperature with individual climate controls.</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="250">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-accent/10 text-accent text-2xl rounded-full group-hover:bg-accent group-hover:text-primary transition-all duration-300 transform group-hover:scale-110">
                    <i class="fas fa-bath"></i>
                </div>
                <h3 class="font-bold mb-2 group-hover:text-accent transition-colors">Luxury Bathroom</h3>
                <p class="text-gray-600 text-sm">Indulge in premium bath products and plush towels in our marble bathrooms.</p>
            </div>
            
            <!-- Feature 5 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="300">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-accent/10 text-accent text-2xl rounded-full group-hover:bg-accent group-hover:text-primary transition-all duration-300 transform group-hover:scale-110">
                    <i class="fas fa-coffee"></i>
                </div>
                <h3 class="font-bold mb-2 group-hover:text-accent transition-colors">Coffee Station</h3>
                <p class="text-gray-600 text-sm">Start your day with our in-room premium coffee and tea making facilities.</p>
            </div>
            
            <!-- Feature 6 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="350">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-accent/10 text-accent text-2xl rounded-full group-hover:bg-accent group-hover:text-primary transition-all duration-300 transform group-hover:scale-110">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <h3 class="font-bold mb-2 group-hover:text-accent transition-colors">Room Service</h3>
                <p class="text-gray-600 text-sm">24/7 room service available for your convenience and comfort.</p>
            </div>
            
            <!-- Feature 7 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="400">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-accent/10 text-accent text-2xl rounded-full group-hover:bg-accent group-hover:text-primary transition-all duration-300 transform group-hover:scale-110">
                    <i class="fas fa-bed"></i>
                </div>
                <h3 class="font-bold mb-2 group-hover:text-accent transition-colors">Premium Bedding</h3>
                <p class="text-gray-600 text-sm">Sleep soundly on our luxury mattresses with high thread count linens.</p>
            </div>
            
            <!-- Feature 8 -->
            <div class="text-center group" data-aos="fade-up" data-aos-delay="450">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-accent/10 text-accent text-2xl rounded-full group-hover:bg-accent group-hover:text-primary transition-all duration-300 transform group-hover:scale-110">
                    <i class="fas fa-lock"></i>
                </div>
                <h3 class="font-bold mb-2 group-hover:text-accent transition-colors">Digital Safe</h3>
                <p class="text-gray-600 text-sm">Keep your valuables secure with our in-room electronic safes.</p>
            </div>
        </div>
    </div>
</section>

<!-- Room Comparison Modal -->
<div id="compareModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-primary">Room Comparison</h3>
            <button id="closeCompareModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="py-3 px-4 text-left bg-gray-100 border-b">Feature</th>
                        <th class="py-3 px-4 text-left bg-gray-100 border-b" id="room1Header">Room 1</th>
                        <th class="py-3 px-4 text-left bg-gray-100 border-b" id="room2Header">Room 2</th>
                    </tr>
                </thead>
                <tbody id="compareTableBody">
                    <!-- Table rows will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 text-center">
            <button id="resetCompare" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors mr-2">
                Reset
            </button>
            <a href="../users/booking.php" class="inline-block px-4 py-2 bg-primary text-secondary rounded-lg hover:bg-primary/80 transition-colors">
                Book Now
            </a>
        </div>
    </div>
</div>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-primary to-dark text-secondary relative overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <img src="../assets/images/patterns/pattern1.jpg" alt="" class="w-full h-full object-cover">
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl font-serif font-bold mb-6">Ready to Experience the Luxury?</h2>
            <p class="text-xl mb-8 text-secondary/90">Book your stay now and create unforgettable memories at EaSyStaY.</p>
            <a href="../users/booking.php" class="inline-block bg-accent text-primary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 hover:shadow-accent/30 hover:shadow-xl">
                Book Your Stay <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Room type selection
        const roomTypeBtns = document.querySelectorAll('.room-type-btn');
        
        roomTypeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons in the same group
                const parentDiv = this.closest('.flex');
                parentDiv.querySelectorAll('.room-type-btn').forEach(b => {
                    b.classList.remove('active', 'bg-accent', 'text-primary');
                    b.classList.add('bg-primary/50');
                });
                
                // Add active class to clicked button
                this.classList.add('active', 'bg-accent', 'text-primary');
                this.classList.remove('bg-primary/50');
            });
        });
        
        // Room filtering
        const filterBtns = document.querySelectorAll('.filter-btn');
        const roomCards = document.querySelectorAll('.filter-item');
        
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all filter buttons
                filterBtns.forEach(b => {
                    b.classList.remove('active', 'bg-primary', 'text-secondary');
                    b.classList.add('bg-gray-200', 'text-primary');
                });
                
                // Add active class to clicked button
                this.classList.add('active', 'bg-primary', 'text-secondary');
                this.classList.remove('bg-gray-200', 'text-primary');
                
                const filter = this.getAttribute('data-filter');
                
                // Show/hide room cards based on filter
                roomCards.forEach(card => {
                    if (filter === 'all' || card.getAttribute('data-type') === filter) {
                        card.classList.remove('hidden');
                        setTimeout(() => {
                            card.classList.add('opacity-100');
                            card.classList.remove('opacity-0');
                        }, 50);
                    } else {
                        card.classList.add('opacity-0');
                        card.classList.remove('opacity-100');
                        setTimeout(() => {
                            card.classList.add('hidden');
                        }, 300);
                    }
                });
            });
        });
        
        // Room comparison functionality
        const compareBtns = document.querySelectorAll('.compare-btn');
        const compareModal = document.getElementById('compareModal');
        const closeCompareModal = document.getElementById('closeCompareModal');
        const resetCompare = document.getElementById('resetCompare');
        const compareTableBody = document.getElementById('compareTableBody');
        const room1Header = document.getElementById('room1Header');
        const room2Header = document.getElementById('room2Header');
        
        let selectedRooms = [];
        
        // Static room data
        const roomData = {
            101: {
                name: "Standard Room",
                price: "$100",
                size: "25 m²",
                capacity: "2 Guests",
                bed: "1 Queen Bed",
                bathroom: "Shower",
                wifi: "Yes",
                tv: "32-inch LCD",
                ac: "Yes",
                fridge: "Mini Fridge",
                balcony: "No",
                roomService: "Limited Hours"
            },
            202: {
                name: "Deluxe Room",
                price: "$150",
                size: "35 m²",
                capacity: "2 Guests",
                bed: "1 King Bed",
                bathroom: "Shower & Bathtub",
                wifi: "Yes - High Speed",
                tv: "42-inch Smart TV",
                ac: "Yes",
                fridge: "Mini Bar",
                balcony: "Yes",
                roomService: "24 Hours"
            },
            303: {
                name: "Luxury Suite",
                price: "$250",
                size: "50 m²",
                capacity: "2-3 Guests",
                bed: "1 King Bed + Sofa",
                bathroom: "Luxury Bathroom",
                wifi: "Yes - Premium",
                tv: "55-inch Smart TV",
                ac: "Yes",
                fridge: "Full Mini Bar",
                balcony: "Yes - Large",
                roomService: "24 Hours Priority"
            },
            404: {
                name: "Family Suite",
                price: "$200",
                size: "60 m²",
                capacity: "4-5 Guests",
                bed: "2 Queen Beds",
                bathroom: "2 Bathrooms",
                wifi: "Yes - Premium",
                tv: "50-inch Smart TV",
                ac: "Yes",
                fridge: "Full Bar + Kitchenette",
                balcony: "Yes - Family Size",
                roomService: "24 Hours"
            }
        };
        
        compareBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const roomId = this.getAttribute('data-room-id');
                
                if (selectedRooms.length < 2) {
                    // Check if room is already selected
                    if (!selectedRooms.includes(roomId)) {
                        selectedRooms.push(roomId);
                        this.classList.add('bg-primary', 'text-secondary');
                        this.classList.remove('border-primary');
                        
                        // Update comparison table
                        updateComparisonTable();
                        
                        // Show modal if 2 rooms are selected
                        if (selectedRooms.length === 2) {
                            compareModal.classList.remove('hidden');
                        }
                    }
                }
            });
        });
        
        closeCompareModal.addEventListener('click', function() {
            compareModal.classList.add('hidden');
        });
        
        resetCompare.addEventListener('click', function() {
            selectedRooms = [];
            updateComparisonTable();
            compareModal.classList.add('hidden');
            
            // Reset compare buttons
            compareBtns.forEach(btn => {
                btn.classList.remove('bg-primary', 'text-secondary');
                btn.classList.add('border-primary', 'text-primary');
            });
        });
        
        function updateComparisonTable() {
            // Clear table
            compareTableBody.innerHTML = '';
            
            // Update column headers
            if (selectedRooms.length > 0) {
                room1Header.textContent = roomData[selectedRooms[0]].name;
            }
            
            if (selectedRooms.length > 1) {
                room2Header.textContent = roomData[selectedRooms[1]].name;
                room2Header.style.display = '';
            } else {
                room2Header.style.display = 'none';
            }
            
            // Add comparison rows
            const features = [
                { key: 'price', label: 'Price per Night' },
                { key: 'size', label: 'Room Size' },
                { key: 'capacity', label: 'Capacity' },
                { key: 'bed', label: 'Bed Type' },
                { key: 'bathroom', label: 'Bathroom' },
                { key: 'wifi', label: 'WiFi' },
                { key: 'tv', label: 'TV' },
                { key: 'ac', label: 'Air Conditioning' },
                { key: 'fridge', label: 'Refrigerator' },
                { key: 'balcony', label: 'Balcony' },
                { key: 'roomService', label: 'Room Service' }
            ];
            
            features.forEach(feature => {
                const row = document.createElement('tr');
                
                // Feature label
                const labelCell = document.createElement('td');
                labelCell.className = 'py-3 px-4 text-left border-b font-medium bg-gray-50';
                labelCell.textContent = feature.label;
                row.appendChild(labelCell);
                
                // Room 1 value
                const room1Cell = document.createElement('td');
                room1Cell.className = 'py-3 px-4 text-left border-b';
                if (selectedRooms.length > 0) {
                    room1Cell.textContent = roomData[selectedRooms[0]][feature.key];
                }
                row.appendChild(room1Cell);
                
                // Room 2 value
                const room2Cell = document.createElement('td');
                room2Cell.className = 'py-3 px-4 text-left border-b';
                if (selectedRooms.length > 1) {
                    room2Cell.textContent = roomData[selectedRooms[1]][feature.key];
                    room2Cell.style.display = '';
                    
                    // Highlight differences
                    if (roomData[selectedRooms[0]][feature.key] !== roomData[selectedRooms[1]][feature.key]) {
                        room1Cell.classList.add('bg-accent/10');
                        room2Cell.classList.add('bg-accent/10');
                    }
                } else {
                    room2Cell.style.display = 'none';
                }
                row.appendChild(room2Cell);
                
                compareTableBody.appendChild(row);
            });
        }
        
        // Close modal when clicking outside
        compareModal.addEventListener('click', function(e) {
            if (e.target === compareModal) {
                compareModal.classList.add('hidden');
            }
        });
        
        // Initialize AOS animations
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: false,
            mirror: false
        });
        
        // Back to top button functionality
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('opacity-100', 'translate-y-0');
                backToTopButton.classList.remove('opacity-0', 'translate-y-10');
            } else {
                backToTopButton.classList.remove('opacity-100', 'translate-y-0');
                backToTopButton.classList.add('opacity-0', 'translate-y-10');
            }
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>

<?php
// Include the footer file
include_once '../includes/footer.php';
?>