<!-- Footer Section -->
<footer class="bg-primary text-secondary border-t-2 border-accent py-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Company Info -->
            <div class="mb-8 md:mb-0">
                <h5 class="text-xl font-serif font-bold mb-6 pb-2 relative after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-10 after:h-0.5 after:bg-accent">
                    EaSyStaY
                </h5>
                <p class="text-secondary/90 mb-6">
                    Crafting exceptional hospitality experiences with golden standard service since 2023.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 border border-accent rounded-full flex items-center justify-center transition-all hover:bg-accent hover:text-primary">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 border border-accent rounded-full flex items-center justify-center transition-all hover:bg-accent hover:text-primary">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 border border-accent rounded-full flex items-center justify-center transition-all hover:bg-accent hover:text-primary">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="w-10 h-10 border border-accent rounded-full flex items-center justify-center transition-all hover:bg-accent hover:text-primary">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="mb-8 md:mb-0">
                <h5 class="text-xl font-serif font-bold mb-6 pb-2 relative after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-10 after:h-0.5 after:bg-accent">
                    Contact
                </h5>
                <ul class="space-y-4">
                    <li class="flex items-center space-x-3">
                        <i class="fas fa-map-marker-alt text-accent"></i>
                        <span>29 Park Street, New York City</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <i class="fas fa-phone text-accent"></i>
                        <a href="tel:+12125550187" class="text-secondary/90 hover:text-accent transition-colors">(212) 555-0187</a>
                    </li>
                    <li class="flex items-center space-x-3">
                        <i class="fas fa-envelope text-accent"></i>
                        <a href="mailto:info@easystay.com" class="text-secondary/90 hover:text-accent transition-colors">info@easystay.com</a>
                    </li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div>
                <h5 class="text-xl font-serif font-bold mb-6 pb-2 relative after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-10 after:h-0.5 after:bg-accent">
                    Explore
                </h5>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <a href="<?php echo $base_url; ?>/index.php" class="block mb-3 text-secondary/90 hover:text-accent transition-colors">Home</a>
                        <a href="<?php echo $base_url; ?>/pages/rooms.php" class="block mb-3 text-secondary/90 hover:text-accent transition-colors">Rooms</a>
                        <a href="<?php echo $base_url; ?>/pages/gallery.php" class="block mb-3 text-secondary/90 hover:text-accent transition-colors">Gallery</a>
                    </div>
                    <div>
                        <a href="<?php echo $base_url; ?>/pages/about_us.php" class="block mb-3 text-secondary/90 hover:text-accent transition-colors">About Us</a>
                        <a href="<?php echo $base_url; ?>/auth/login.php" class="block mb-3 text-secondary/90 hover:text-accent transition-colors">Login</a>
                        <a href="<?php echo $base_url; ?>/auth/register.php" class="block mb-3 text-secondary/90 hover:text-accent transition-colors">Register</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="mt-12 pt-8 border-t border-secondary/10">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-secondary/80 text-sm">© 2023 EaSyStaY. All rights reserved</p>
                </div>
                <div class="flex space-x-4">
                    <img src="../assets/images/SVG/Visa.svg" alt="Visa" class="h-6 grayscale brightness-200">
                    <img src="../assets/images/SVG/Mastercard.svg" alt="Mastercard" class="h-6 grayscale brightness-200">
                    <img src="../assets/images/SVG/Paypal.svg" alt="PayPal" class="h-6 grayscale brightness-200">
                </div>
            </div>
        </div>
    </div>
</footer>