<?php
// Include the header file
include_once '../includes/header.php';

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
            <a href="<?php echo $base_url; ?>/auth/login.php" class="cta-button bg-transparent text-secondary border-2 border-secondary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 hover:bg-secondary/10 hover:border-accent hover:text-accent">
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


<!-- Packages Section -->
<section id="packages" class="py-16 bg-primary text-secondary">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-accent font-semibold tracking-wider uppercase">Luxury Accommodations</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-secondary mt-4 mb-6">Our Premium Packages</h2>
            <p class="text-xl text-secondary/80">Choose from our selection of carefully curated packages designed to provide an unforgettable experience.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Package 1 -->
            <div class="bg-dark rounded-xl overflow-hidden shadow-lg border border-accent/20" data-aos="fade-up" data-aos-delay="100">
                <div class="relative">
                    <img src="../assets/images/rooms/deluxe-room.jpg" alt="Deluxe Room" class="w-full h-64 object-cover">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $299<span class="text-sm">/night</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-secondary mb-2">Deluxe Package</h3>
                    <p class="text-secondary/80 mb-4">Perfect for couples seeking a luxurious getaway with premium amenities and services.</p>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Select Room Type:</h4>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors active">Deluxe King</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Deluxe Twin</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Deluxe Suite</button>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Package Includes:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Daily breakfast for two</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Access to fitness center</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Welcome drink on arrival</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Free Wi-Fi</li>
                        </ul>
                    </div>
                    
                    <div class="flex justify-between">
                        <a href="#" class="text-accent font-semibold hover:underline">View Details</a>
                        <a href="<?php echo $base_url; ?>/auth/login.php" class="bg-accent text-primary px-4 py-2 rounded-lg hover:bg-accent/80 transition-colors">Book Now</a>
                    </div>
                </div>
            </div>
            
            <!-- Package 2 -->
            <div class="bg-dark rounded-xl overflow-hidden shadow-lg border border-accent/20" data-aos="fade-up" data-aos-delay="200">
                <div class="relative">
                    <img src="../assets/images/rooms/executive-suite.jpg" alt="Executive Suite" class="w-full h-64 object-cover">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $499<span class="text-sm">/night</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-secondary mb-2">Executive Package</h3>
                    <p class="text-secondary/80 mb-4">Elevated luxury with exclusive perks for the discerning business traveler or leisure seeker.</p>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Select Room Type:</h4>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors active">Executive Suite</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Business Suite</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Corner Suite</button>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Package Includes:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Daily breakfast and dinner</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Access to Executive Lounge</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Complimentary minibar</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Airport transfer (one-way)</li>
                        </ul>
                    </div>
                    
                    <div class="flex justify-between">
                        <a href="#" class="text-accent font-semibold hover:underline">View Details</a>
                        <a href="<?php echo $base_url; ?>/auth/login.php" class="bg-accent text-primary px-4 py-2 rounded-lg hover:bg-accent/80 transition-colors">Book Now</a>
                    </div>
                </div>
            </div>
            
            <!-- Package 3 -->
            <div class="bg-dark rounded-xl overflow-hidden shadow-lg border border-accent/20" data-aos="fade-up" data-aos-delay="300">
                <div class="relative">
                    <img src="../assets/images/rooms/presidential-suite.jpg" alt="Presidential Suite" class="w-full h-64 object-cover">
                    <div class="absolute top-4 right-4 bg-accent text-primary px-4 py-2 rounded-full font-bold">
                        $999<span class="text-sm">/night</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-secondary mb-2">Presidential Package</h3>
                    <p class="text-secondary/80 mb-4">The ultimate luxury experience with personalized service and exclusive amenities.</p>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Select Room Type:</h4>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors active">Presidential Suite</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Royal Suite</button>
                            <button class="room-type-btn px-4 py-2 bg-primary/50 rounded-lg text-secondary hover:bg-accent hover:text-primary transition-colors">Penthouse</button>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2 text-accent">Package Includes:</h4>
                        <ul class="space-y-2">
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> All-inclusive dining</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Personal butler service</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Luxury spa treatments</li>
                            <li class="flex items-center"><i class="fas fa-check text-accent mr-2"></i> Private airport transfers</li>
                        </ul>
                    </div>
                    
                    <div class="flex justify-between">
                        <a href="#" class="text-accent font-semibold hover:underline">View Details</a>
                        <a href="<?php echo $base_url; ?>/auth/login.php" class="bg-accent text-primary px-4 py-2 rounded-lg hover:bg-accent/80 transition-colors">Book Now</a>
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
<div id="comparison-modal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl p-6 max-w-4xl w-full max-h-[90vh] overflow-y-auto transform transition-all duration-500 scale-95 opacity-0" id="modal-content">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-primary">Room Comparison</h3>
            <button id="close-modal" class="text-gray-500 hover:text-primary text-2xl transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div id="comparison-content" class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border p-3 bg-gray-100 text-left">Feature</th>
                        <th class="border p-3 bg-gray-100 text-left room-col-1">Room 1</th>
                        <th class="border p-3 bg-gray-100 text-left room-col-2 hidden">Room 2</th>
                    </tr>
                </thead>
                <tbody id="comparison-table-body">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
        
        <div class="mt-6 text-center">
            <button id="reset-comparison" class="bg-primary text-secondary px-6 py-2 rounded-lg hover:bg-primary/80 transition-colors transform hover:scale-105">
                Reset Comparison
            </button>
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
            <a href="<?php echo $base_url; ?>/auth/login.php" class="inline-block bg-accent text-primary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105 hover:shadow-accent/30 hover:shadow-xl">
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
        const comparisonModal = document.getElementById('comparison-modal');
        const modalContent = document.getElementById('modal-content');
        const closeModalBtn = document.getElementById('close-modal');
        const resetComparisonBtn = document.getElementById('reset-comparison');
        const comparisonTableBody = document.getElementById('comparison-table-body');
        const roomCol1 = document.querySelector('.room-col-1');
        const roomCol2 = document.querySelector('.room-col-2');
        
        let selectedRooms = [];
        
        // Static room data
        const roomData = <?php echo json_encode($rooms); ?>;
        
        compareBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const roomId = parseInt(this.getAttribute('data-room-id'));
                const room = roomData.find(r => r.RoomID == roomId);
                
                if (selectedRooms.length < 2) {
                    // Check if room is already selected
                    if (!selectedRooms.some(r => r.RoomID == roomId)) {
                        selectedRooms.push(room);
                        this.classList.add('bg-primary', 'text-secondary');
                        this.classList.remove('border-primary');
                        
                        // Update comparison table
                        updateComparisonTable();
                        
                        // Show modal if 2 rooms are selected
                        if (selectedRooms.length === 2) {
                            comparisonModal.classList.remove('hidden');
                            setTimeout(() => {
                                modalContent.classList.remove('scale-95', 'opacity-0');
                                modalContent.classList.add('scale-100', 'opacity-100');
                            }, 50);
                        }
                    }
                }
            });
        });
        
        closeModalBtn.addEventListener('click', function() {
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                comparisonModal.classList.add('hidden');
            }, 300);
        });
        
        resetComparisonBtn.addEventListener('click', function() {
            selectedRooms = [];
            updateComparisonTable();
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                comparisonModal.classList.add('hidden');
            }, 300);
            
            // Reset compare buttons
            compareBtns.forEach(btn => {
                btn.classList.remove('bg-primary', 'text-secondary');
                btn.classList.add('border-primary', 'text-primary');
            });
        });
        
        function updateComparisonTable() {
            // Clear table
            comparisonTableBody.innerHTML = '';
            
            // Update column headers
            if (selectedRooms.length > 0) {
                roomCol1.textContent = selectedRooms[0].RoomType + ' (Room ' + selectedRooms[0].RoomNumber + ')';
            }
            
            if (selectedRooms.length > 1) {
                roomCol2.textContent = selectedRooms[1].RoomType + ' (Room ' + selectedRooms[1].RoomNumber + ')';
                roomCol2.classList.remove('hidden');
            } else {
                roomCol2.classList.add('hidden');
            }
            
            // Add comparison rows
            addComparisonRow('Room Type', selectedRooms.map(r => r.RoomType));
            addComparisonRow('Price per Night', selectedRooms.map(r => '$' + parseFloat(r.BasePrice).toFixed(2)));
            addComparisonRow('Availability', selectedRooms.map(r => r.AvailabilityStatus));
            addComparisonRow('Description', selectedRooms.map(r => r.Description));
        }
        
        function addComparisonRow(feature, values) {
            const row = document.createElement('tr');
            
            const featureCell = document.createElement('td');
            featureCell.className = 'border p-3 font-semibold';
            featureCell.textContent = feature;
            row.appendChild(featureCell);
            
            for (let i = 0; i < 2; i++) {
                const valueCell = document.createElement('td');
                valueCell.className = 'border p-3';
                
                if (values[i] !== undefined) {
                    valueCell.textContent = values[i];
                    
                    // Add special styling for availability status
                    if (feature === 'Availability') {
                        if (values[i] === 'Available') {
                            valueCell.classList.add('text-green-600');
                        } else if (values[i] === 'Occupied') {
                            valueCell.classList.add('text-red-600');
                        } else {
                            valueCell.classList.add('text-yellow-600');
                        }
                    }
                }
                
                row.appendChild(valueCell);
                
                // Hide second column if only one room selected
                if (i === 1 && selectedRooms.length < 2) {
                    valueCell.classList.add('hidden');
                }
            }
            
            comparisonTableBody.appendChild(row);
        }
        
        // Close modal when clicking outside
        comparisonModal.addEventListener('click', function(e) {
            if (e.target === comparisonModal) {
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    comparisonModal.classList.add('hidden');
                }, 300);
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