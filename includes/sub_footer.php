<!-- Footer with enhanced design, animations and responsiveness -->
<footer class="bg-gradient-to-b from-primary to-primary-dark text-secondary border-t border-accent/30 py-10 mt-8 relative overflow-hidden">
  <!-- Decorative elements -->
  <div class="absolute top-0 left-0 w-24 h-24 bg-accent/5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
  <div class="absolute bottom-0 right-0 w-32 h-32 bg-accent/5 rounded-full translate-x-1/2 translate-y-1/2"></div>
  
  <div class="container mx-auto px-4 relative z-10">
    <!-- Footer Grid with improved layout -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="100">
      <!-- Brand Column with animations -->
      <div class="text-center md:text-left transform transition-all duration-500 hover:translate-y-[-5px]">
        <h3 class="text-xl font-serif font-bold mb-3 pb-2 relative inline-block after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-full after:h-0.5 after:bg-accent after:transform after:scale-x-0 after:transition-transform after:duration-300 hover:after:scale-x-100">
          <span class="bg-clip-text text-transparent bg-gradient-accent">EaSyStaY</span>
        </h3>
        <p class="text-gray-300 text-sm mb-4 max-w-xs mx-auto md:mx-0">
          Crafting exceptional hospitality experiences with golden standard service since 2023.
        </p>
      </div>

      <!-- Quick Links with hover effects -->
      <div class="text-center transform transition-all duration-500 hover:translate-y-[-5px]">
        <h3 class="text-lg font-serif font-semibold mb-3 pb-2 relative inline-block after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-full after:h-0.5 after:bg-accent after:transform after:scale-x-0 after:transition-transform after:duration-300 hover:after:scale-x-100">
          <span class="bg-clip-text text-transparent bg-gradient-accent">Quick Links</span>
        </h3>
        <ul class="space-y-2">
          <li class="transform transition-all duration-300 hover:translate-x-1">
            <a href="../index.php" class="text-gray-300 hover:text-accent transition-all duration-300 flex items-center justify-center md:justify-center gap-2 group">
              <i class="fas fa-home text-xs opacity-70 group-hover:opacity-100"></i>
              <span>Home</span>
            </a>
          </li>
          <li class="transform transition-all duration-300 hover:translate-x-1">
            <a href="../pages/gallery.php" class="text-gray-300 hover:text-accent transition-all duration-300 flex items-center justify-center md:justify-center gap-2 group">
              <i class="fas fa-images text-xs opacity-70 group-hover:opacity-100"></i>
              <span>Gallery</span>
            </a>
          </li>
          <li class="transform transition-all duration-300 hover:translate-x-1">
            <a href="../pages/about_us.php" class="text-gray-300 hover:text-accent transition-all duration-300 flex items-center justify-center md:justify-center gap-2 group">
              <i class="fas fa-info-circle text-xs opacity-70 group-hover:opacity-100"></i>
              <span>About Us</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Social Media with enhanced animations -->
      <div class="text-center md:text-right transform transition-all duration-500 hover:translate-y-[-5px]">
        <h3 class="text-lg font-serif font-semibold mb-3 pb-2 relative inline-block after:content-[''] after:absolute after:left-0 after:bottom-0 after:w-full after:h-0.5 after:bg-accent after:transform after:scale-x-0 after:transition-transform after:duration-300 hover:after:scale-x-100">
          <span class="bg-clip-text text-transparent bg-gradient-accent">Follow Us</span>
        </h3>
        <div class="flex justify-center md:justify-end space-x-4">
          <a href="#" class="text-gray-300 hover:text-accent transition-all duration-300 transform hover:scale-125 hover:rotate-6 bg-primary-light/50 hover:bg-primary-light p-2 rounded-full">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-accent transition-all duration-300 transform hover:scale-125 hover:rotate-6 bg-primary-light/50 hover:bg-primary-light p-2 rounded-full">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-accent transition-all duration-300 transform hover:scale-125 hover:rotate-6 bg-primary-light/50 hover:bg-primary-light p-2 rounded-full">
            <i class="fab fa-instagram"></i>
          </a>
        </div>
      </div>
    </div>

    <!-- Copyright with gradient border -->
    <div class="border-t border-gray-700/50 mt-8 pt-4 text-center text-gray-400 text-sm">
      <div class="inline-block px-4 py-1 rounded-full bg-primary-light/20 backdrop-blur-sm animate-border-pulse">
        &copy; <?php echo date('Y'); ?> <span class="text-accent">EaSyStaY</span>. All rights reserved.
      </div>
    </div>
  </div>
</footer>

<!-- AOS (Animate On Scroll) -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        easing: 'ease-in-out-quad',
        offset: 50
    });
    
    // Fade out alert messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-message');
        
        if (alerts.length > 0) {
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 1s ease-out';
                    alert.style.opacity = '0';
                    
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 1000);
                }, 5000);
            });
        }
        
        // Add scroll reveal animation for footer elements
        const footerElements = document.querySelectorAll('footer h3, footer p, footer ul, footer .flex');
        footerElements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 100 + (index * 150));
        });
    });
</script>
</body>
</html>