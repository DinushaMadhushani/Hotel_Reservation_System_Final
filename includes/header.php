<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL for consistent linking
$base_url = '/Hotel_Reservation_System_Final';

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EaSyStaY - Luxury Hotel Experience</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Dancing+Script:wght@700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
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
</head>
<body class="font-sans bg-light text-primary">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-gradient-to-r from-primary to-primary shadow-lg z-50 transition-all duration-300 border-b border-accent/20">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <a href="<?php echo $base_url; ?>/index.php" class="text-4xl font-script text-secondary drop-shadow-md flex items-center">
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
                    <a href="<?php echo $base_url; ?>/index.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300 <?php echo $current_page === 'index.php' ? 'text-accent' : ''; ?>">Home</a>
                    <a href="<?php echo $base_url; ?>/pages/rooms.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300 <?php echo $current_page === 'rooms.php' ? 'text-accent' : ''; ?>">Rooms & Suites</a>
                    <a href="<?php echo $base_url; ?>/pages/gallery.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300 <?php echo $current_page === 'gallery.php' ? 'text-accent' : ''; ?>">Gallery</a>
                    <a href="<?php echo $base_url; ?>/pages/about_us.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300 <?php echo $current_page === 'about_us.php' ? 'text-accent' : ''; ?>">About Us</a>
                    <a href="<?php echo $base_url; ?>/users/booking.php" class="cta-button bg-accent text-primary font-semibold px-6 py-2 rounded-xl shadow-lg hover:bg-dark hover:text-secondary transition-all duration-300 transform hover:-translate-y-1 ml-4">Book Now</a>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden lg:hidden bg-primary pb-4">
                <a href="<?php echo $base_url; ?>/index.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all <?php echo $current_page === 'index.php' ? 'text-accent' : ''; ?>"><i class="fas fa-home mr-3"></i>Home</a>
                <a href="<?php echo $base_url; ?>/pages/rooms.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all <?php echo $current_page === 'rooms.php' ? 'text-accent' : ''; ?>"><i class="fas fa-bed mr-3"></i>Rooms & Suites</a>
                <a href="<?php echo $base_url; ?>/pages/gallery.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all <?php echo $current_page === 'gallery.php' ? 'text-accent' : ''; ?>"><i class="fas fa-images mr-3"></i>Gallery</a>
                <a href="<?php echo $base_url; ?>/pages/about_us.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all <?php echo $current_page === 'about_us.php' ? 'text-accent' : ''; ?>"><i class="fas fa-info-circle mr-3"></i>About Us</a>
                <a href="<?php echo $base_url; ?>/users/booking.php" class="block bg-accent text-primary font-semibold px-4 py-3 rounded-lg mx-4 mt-3 text-center">
                    <i class="fas fa-calendar-check mr-2"></i>Book Now
                </a>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    mobileMenu.classList.toggle('animate-slide-up');
                });
            }
            
            // Initialize AOS
            AOS.init({
                duration: 800,
                easing: 'ease-in-out',
                once: false,
                mirror: true
            });
        });
    </script>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 z-50 bg-accent text-primary w-12 h-12 rounded-full flex items-center justify-center shadow-lg transform transition-all duration-300 opacity-0 translate-y-10 hover:bg-accent-dark hover:scale-110">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- AOS JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

  