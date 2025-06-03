<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EaSyStaY - Room Packages</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
        .gold-gradient {
            background: linear-gradient(135deg, #d4af37 0%, #f1c40f 100%);
        }
        .gold-text-gradient {
            background: linear-gradient(135deg, #d4af37 0%, #f1c40f 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .gold-border {
            border: 2px solid #d4af37;
        }
        .gold-border:hover {
            border-color: #f1c40f;
        }
        .text-shadow {
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        .gold-shadow {
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        .gold-shadow:hover {
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }
    </style>
</head>
<body class="font-poppins bg-gray-100">
    <!-- Header Placeholder -->
    <div class="bg-black text-white py-4 px-6 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-2xl font-bold gold-text-gradient">EaSyStaY</div>
            <div class="hidden md:flex space-x-6">
                <a href="#" class="text-gray-300 hover:text-yellow-400 transition">Home</a>
                <a href="#packages" class="text-gray-300 hover:text-yellow-400 transition">Packages</a>
                <a href="#rooms" class="text-gray-300 hover:text-yellow-400 transition">Rooms</a>
                <a href="#" class="text-gray-300 hover:text-yellow-400 transition">Contact</a>
            </div>
            <button class="md:hidden text-yellow-400">
                <i class="fas fa-bars text-2xl"></i>
            </button>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="relative h-[70vh] bg-black overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-black to-transparent opacity-90"></div>
        <img src="https://images.unsplash.com/photo-1582719365379-005b891343d8" alt="Luxury Hotel" class="w-full h-full object-cover">
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="container mx-auto px-6 text-center animate-fade-in">
                <h1 class="text-4xl md:text-6xl font-bold text-yellow-400 mb-4 text-shadow">Exclusive Rooms & Packages</h1>
                <p class="text-xl text-gray-300 mb-8 max-w-2xl mx-auto">Find the perfect accommodation for your needs with our luxurious options</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#packages" class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-black font-semibold rounded-full transition transform hover:scale-105 shadow-lg">
                        Explore Packages
                    </a>
                    <a href="#rooms" class="px-8 py-3 border-2 border-yellow-500 text-yellow-400 hover:bg-yellow-500 hover:text-black font-semibold rounded-full transition transform hover:scale-105">
                        Explore Rooms
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section id="packages" class="py-16 bg-gray-100">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16 animate-fade-in">
                <h2 class="text-4xl font-bold gold-text-gradient mb-4">Our Room Packages</h2>
                <div class="w-20 h-1 bg-yellow-500 mx-auto mb-6"></div>
                <p class="text-gray-600 max-w-2xl mx-auto">Tailored accommodation experiences for every traveler with exclusive benefits</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Standard Room -->
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center mr-4">
                                <i class="fas fa-bed text-2xl text-yellow-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Standard Room Stay</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Comfortable accommodation for short stays</p>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Room Type:</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="single">Single Room ($89/night)</option>
                                <option value="double">Double Room ($129/night)</option>
                                <option value="twin">Twin Room ($119/night)</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-yellow-600">$149</span>
                            <button class="px-6 py-2 border-2 border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white font-medium rounded-full transition">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Deluxe Room -->
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center mr-4">
                                <i class="fas fa-hotel text-2xl text-yellow-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Deluxe Room Package</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Premium comfort with extra amenities</p>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Room Type:</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="deluxe-queen">Deluxe Queen ($199/night)</option>
                                <option value="deluxe-king">Deluxe King ($229/night)</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-yellow-600">$229</span>
                            <button class="px-6 py-2 border-2 border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white font-medium rounded-full transition">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Family Suite -->
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center mr-4">
                                <i class="fas fa-users text-2xl text-yellow-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Family Suite Escape</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Spacious accommodation for families</p>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Room Type:</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="family-2queen">2 Queen Beds ($249/night)</option>
                                <option value="family-suite">Family Suite ($299/night)</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-yellow-600">$349</span>
                            <button class="px-6 py-2 border-2 border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white font-medium rounded-full transition">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Long Stay -->
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center mr-4">
                                <i class="fas fa-calendar-alt text-2xl text-yellow-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Long Stay Special</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Discounted rates for extended stays</p>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Room Type:</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="studio">Studio Apartment ($149/night)</option>
                                <option value="executive">Executive Suite ($179/night)</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-yellow-600">$499</span>
                            <button class="px-6 py-2 border-2 border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white font-medium rounded-full transition">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Luxury Suite -->
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center mr-4">
                                <i class="fas fa-crown text-2xl text-yellow-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Luxury Suite Experience</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Ultimate luxury accommodation</p>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Room Type:</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="presidential">Presidential Suite ($599/night)</option>
                                <option value="penthouse">Penthouse ($799/night)</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-yellow-600">$699</span>
                            <button class="px-6 py-2 border-2 border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white font-medium rounded-full transition">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Romantic Getaway -->
                <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-shadow">
                    <div class="p-6">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 rounded-full bg-yellow-50 flex items-center justify-center mr-4">
                                <i class="fas fa-heart text-2xl text-yellow-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800">Romantic Getaway</h3>
                        </div>
                        <p class="text-gray-600 mb-6">Perfect for couples retreat</p>
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Room Type:</label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                <option value="honeymoon">Honeymoon Suite ($299/night)</option>
                                <option value="jacuzzi">Jacuzzi Room ($349/night)</option>
                            </select>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-yellow-600">$299</span>
                            <button class="px-6 py-2 border-2 border-yellow-500 text-yellow-600 hover:bg-yellow-500 hover:text-white font-medium rounded-full transition">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold gold-text-gradient mb-4">Our Rooms</h2>
                <div class="w-20 h-1 bg-yellow-500 mx-auto mb-6"></div>
                <p class="text-gray-600 max-w-2xl mx-auto">Experience comfort and luxury in our carefully designed rooms</p>
            </div>

            <!-- Standard Rooms -->
            <div class="mb-16">
                <h3 class="text-2xl font-bold text-gray-800 mb-8">Standard Rooms</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Single Room -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-border">
                        <img src="../assets/images/rooms/R (2).jpg" alt="Single Room" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-bed text-yellow-600 mr-2"></i>
                                <h4 class="text-xl font-semibold text-gray-800">Single Room</h4>
                            </div>
                            <p class="text-gray-600 mb-6">Ideal for solo travelers with a comfortable single bed.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-yellow-600">$89<span class="text-gray-500 text-sm">/night</span></span>
                                <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-full transition transform hover:scale-105">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Double Room -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-border">
                        <img src="../assets/images/rooms/R (1).jpg" alt="Double Room" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-bed text-yellow-600 mr-2"></i>
                                <h4 class="text-xl font-semibold text-gray-800">Double Room</h4>
                            </div>
                            <p class="text-gray-600 mb-6">Perfect for couples with a cozy queen-size bed.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-yellow-600">$129<span class="text-gray-500 text-sm">/night</span></span>
                                <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-full transition transform hover:scale-105">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Twin Room -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-border">
                        <img src="../assets/images/rooms/twinroom.jpg" alt="Twin Room" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-bed text-yellow-600 mr-2"></i>
                                <h4 class="text-xl font-semibold text-gray-800">Twin Room</h4>
                            </div>
                            <p class="text-gray-600 mb-6">Great for friends with two single beds.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-yellow-600">$119<span class="text-gray-500 text-sm">/night</span></span>
                                <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-full transition transform hover:scale-105">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Premium Rooms -->
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-8">Premium Rooms</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Deluxe Room -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-border">
                        <img src="../assets/images/rooms/deluxeroom.jpg" alt="Deluxe Room" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-star text-yellow-600 mr-2"></i>
                                <h4 class="text-xl font-semibold text-gray-800">Deluxe Room</h4>
                            </div>
                            <p class="text-gray-600 mb-6">Spacious room with modern amenities and city view.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-yellow-600">$199<span class="text-gray-500 text-sm">/night</span></span>
                                <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-full transition transform hover:scale-105">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Family Room -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-border">
                        <img src="../assets/images/rooms/familyroom.jpg" alt="Family Room" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-users text-yellow-600 mr-2"></i>
                                <h4 class="text-xl font-semibold text-gray-800">Family Room</h4>
                            </div>
                            <p class="text-gray-600 mb-6">Perfect for families with two queen beds.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-yellow-600">$199<span class="text-gray-500 text-sm">/night</span></span>
                                <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-full transition transform hover:scale-105">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Penthouse Suite -->
                    <div class="bg-white rounded-xl overflow-hidden shadow-md hover-scale gold-border">
                        <img src="../assets/images/rooms/penthousesuits.jpeg" alt="Penthouse Suite" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <i class="fas fa-home text-yellow-600 mr-2"></i>
                                <h4 class="text-xl font-semibold text-gray-800">Penthouse Suite</h4>
                            </div>
                            <p class="text-gray-600 mb-6">Located on the highest floor with unparalleled views.</p>
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-yellow-600">$399<span class="text-gray-500 text-sm">/night</span></span>
                                <button class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-full transition transform hover:scale-105">
                                    Book Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 gold-gradient">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-black mb-6">Ready to Book Your Stay?</h2>
            <p class="text-black text-lg mb-8 max-w-2xl mx-auto">Choose your perfect room package and reserve today for an unforgettable experience</p>
            <button class="px-8 py-3 bg-black hover:bg-gray-900 text-yellow-400 font-bold rounded-full transition transform hover:scale-105 shadow-lg">
                Make a Reservation
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold gold-text-gradient mb-4">EaSyStaY</h3>
                    <p class="text-gray-400">Luxury accommodation with exceptional service and comfort.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-yellow-400 mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-yellow-400 transition">Home</a></li>
                        <li><a href="#packages" class="text-gray-400 hover:text-yellow-400 transition">Packages</a></li>
                        <li><a href="#rooms" class="text-gray-400 hover:text-yellow-400 transition">Rooms</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-yellow-400 transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-yellow-400 mb-4">Contact Us</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li class="flex items-center"><i class="fas fa-map-marker-alt text-yellow-400 mr-2"></i> 123 Luxury Street, City</li>
                        <li class="flex items-center"><i class="fas fa-phone text-yellow-400 mr-2"></i> +1 234 567 890</li>
                        <li class="flex items-center"><i class="fas fa-envelope text-yellow-400 mr-2"></i> info@easystay.com</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-yellow-400 mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-yellow-400 hover:bg-yellow-400 hover:text-black transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-yellow-400 hover:bg-yellow-400 hover:text-black transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-yellow-400 hover:bg-yellow-400 hover:text-black transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500">
                <p>&copy; 2023 EaSyStaY. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Simple animation on scroll
        document.addEventListener('DOMContentLoaded', () => {
            const animateElements = document.querySelectorAll('.animate-fade-in');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            animateElements.forEach(el => {
                el.style.opacity = 0;
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>3