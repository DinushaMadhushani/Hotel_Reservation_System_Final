<?php
session_start();
require '../config/db.con.php';

// Define base URL for consistent linking
$base_url = '/Hotel_Reservation_System_Final';

// Initialize variables
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter password.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
        // Prepare SQL statement with case-insensitive search
        $sql = "SELECT UserID, FullName, Email, PasswordHash, UserType 
                FROM Users 
                WHERE LOWER(Email) = LOWER(?)"; // Case-insensitive match
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                
                if ($result->num_rows == 1) {
                    $user = $result->fetch_assoc();
                    
                    // Verify password (plain text comparison)
                    if ($password === $user['PasswordHash']) {
                        // Start session
                        $_SESSION['UserID'] = $user['UserID'];
                        $_SESSION['FullName'] = $user['FullName'];
                        $_SESSION['UserType'] = $user['UserType'];
                        
                        // Redirect based on role
                        switch ($user['UserType']) {
                            case 'Admin':
                                header("Location: ../admins/dashboard.php");
                                exit();
                            case 'Staff':
                                header("Location: ../staff/dashboard.php");
                                exit();
                            case 'Customer':
                                header("Location: ../users/dashboard.php");
                                exit();
                            default:
                                $login_err = "Invalid user role.";
                        }
                    } else {
                        $login_err = "Invalid password.";
                    }
                } else {
                    $login_err = "Email not found. Please check your email address.";
                }
            } else {
                $login_err = "Database query failed. Please try again later.";
            }
            $stmt->close();
        } else {
            $login_err = "Database connection error. Please try again later.";
        }
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EaSyStaY</title>
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
                    <a href="<?php echo $base_url; ?>/index.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300">Home</a>
                    <a href="<?php echo $base_url; ?>/pages/rooms.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300">Rooms & Suites</a>
                    <a href="<?php echo $base_url; ?>/pages/gallery.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300">Gallery</a>
                    <a href="<?php echo $base_url; ?>/pages/about_us.php" class="nav-link relative text-secondary font-medium px-4 py-2 hover:text-accent transition-colors duration-300">About Us</a>
                    <a href="<?php echo $base_url; ?>/auth/register.php" class="cta-button bg-accent text-primary font-semibold px-6 py-2 rounded-xl shadow-lg hover:bg-dark hover:text-secondary transition-all duration-300 transform hover:-translate-y-1 ml-4">Register</a>
                </div>
            </div>
            
            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden lg:hidden bg-primary pb-4">
                <a href="<?php echo $base_url; ?>/index.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-home mr-3"></i>Home</a>
                <a href="<?php echo $base_url; ?>/pages/rooms.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-bed mr-3"></i>Rooms & Suites</a>
                <a href="<?php echo $base_url; ?>/pages/gallery.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-images mr-3"></i>Gallery</a>
                <a href="<?php echo $base_url; ?>/pages/about_us.php" class="block px-4 py-3 text-secondary hover:text-accent hover:bg-primary/80 transition-all"><i class="fas fa-info-circle mr-3"></i>About Us</a>
                <a href="<?php echo $base_url; ?>/auth/register.php" class="block bg-accent text-primary font-semibold px-4 py-3 rounded-lg mx-4 mt-3 text-center">
                    <i class="fas fa-user-plus mr-2"></i>Register
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content with Background -->
    <div class="min-h-screen pt-20 flex items-center justify-center bg-cover bg-center" style="background-image: url('../assets/images/other_hero/rooms-hero.jpg');">
        <div class="absolute inset-0 bg-gradient-to-b from-primary/70 to-primary/90"></div>
        
        <div class="container mx-auto px-4 py-16 relative z-10">
            <div class="max-w-md mx-auto" data-aos="fade-up" data-aos-delay="100">
                <!-- Logo and Title -->
                <div class="text-center mb-8">
                    <div class="text-5xl font-script text-accent mb-4 drop-shadow-lg animate-float">
                        <i class="fas fa-hotel text-accent mr-2"></i> EaSyStaY
                    </div>
                    <h1 class="text-3xl font-serif font-bold text-secondary mb-2">Welcome Back</h1>
                    <p class="text-secondary/80">Sign in to access your account</p>
                </div>
                
                <!-- Login Form Card -->
                <div class="bg-white rounded-xl shadow-xl overflow-hidden transform transition-all duration-500 hover:shadow-accent/30 border border-accent/10">
                    <!-- Form Header -->
                    <div class="bg-gradient-to-r from-primary to-dark p-4 text-center">
                        <h2 class="text-xl font-bold text-accent">Login</h2>
                    </div>
                    
                    <!-- Form Body -->
                    <div class="p-6">
                        <?php if (!empty($login_err)): ?>
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                                <p class="font-medium">Error</p>
                                <p><?= $login_err ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['register_success'])): ?>
                            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                                <p><?= $_SESSION['register_success']; ?></p>
                                <?php unset($_SESSION['register_success']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                            <!-- Email Field -->
                            <div class="mb-6">
                                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" id="email" 
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all <?= (!empty($email_err)) ? 'border-red-500' : '' ?>" 
                                           placeholder="your@email.com" value="<?= $email ?>" autocomplete="email">
                                </div>
                                <?php if (!empty($email_err)): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= $email_err ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Password Field -->
                            <div class="mb-6">
                                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input type="password" name="password" id="password" 
                                           class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent transition-all <?= (!empty($password_err)) ? 'border-red-500' : '' ?>" 
                                           placeholder="••••••••" autocomplete="current-password">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <button type="button" id="toggle-password" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php if (!empty($password_err)): ?>
                                    <p class="text-red-500 text-sm mt-1"><?= $password_err ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-accent text-primary font-bold py-3 px-4 rounded-lg hover:bg-accent-dark transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                            </button>
                        </form>
                        
                        <!-- Divider -->
                        <div class="relative flex items-center mt-8 mb-6">
                            <div class="flex-grow border-t border-gray-300"></div>
                            <span class="flex-shrink mx-4 text-gray-600">or</span>
                            <div class="flex-grow border-t border-gray-300"></div>
                        </div>
                        
                        <!-- Register Link -->
                        <div class="text-center">
                            <p class="text-gray-700">Don't have an account?</p>
                            <a href="./register.php" class="inline-block mt-2 text-accent font-semibold hover:underline relative overflow-hidden group">
                                <span>Create an Account</span>
                                <span class="absolute left-0 bottom-0 w-0 h-0.5 bg-accent transition-all duration-300 group-hover:w-full"></span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Back to Home -->
                <div class="text-center mt-8">
                    <a href="<?php echo $base_url; ?>/index.php" class="text-secondary hover:text-accent transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="bg-primary text-secondary border-t-2 border-accent py-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <a href="<?php echo $base_url; ?>/index.php" class="text-3xl font-script text-secondary drop-shadow-md inline-flex items-center">
                    <i class="fas fa-hotel text-accent mr-2"></i>
                    EaSyStaY
                </a>
                <p class="mt-2 text-sm text-secondary/70">© 2023 EaSyStaY. All rights reserved</p>
                <div class="flex justify-center space-x-4 mt-4">
                    <a href="#" class="w-8 h-8 border border-accent/50 rounded-full flex items-center justify-center transition-all hover:bg-accent hover:text-primary">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-8 h-8 border border-accent/50 rounded-full flex items-center justify-center transition-all hover:bg-accent hover:text-primary">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-8 h-8 border border-accent/50 rounded-full flex items-center justify-center transition-all hover:bg-accent hover:text-primary">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        
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
            
            // Password visibility toggle
            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Toggle icon
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>
</html>