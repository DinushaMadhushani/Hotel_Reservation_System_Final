<?php
// Include the header file
include_once '../includes/header.php';
?>

<!-- Hero Section -->
<section id="hero" class="hero-bg h-screen flex items-center justify-center relative overflow-hidden" style="background-image: url('../assets/images/other_hero/about-hero.jpg');">
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/80"></div>
    <div class="container mx-auto px-4 z-10 text-center" data-aos="fade-up" data-aos-delay="100">
        <div class="mb-8">
            <h1 class="text-4xl md:text-6xl font-serif font-bold text-secondary mt-4 mb-6 leading-tight">Our <span class="text-accent">Story</span></h1>
            <p class="text-xl md:text-2xl text-secondary/90 max-w-3xl mx-auto">Discover the legacy and vision behind EaSyStaY's commitment to excellence</p>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="300">
            <a href="#vision" class="cta-button bg-accent text-primary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                Explore Our Journey <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Vision & Mission Section -->
<section id="vision" class="py-16 bg-light">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1" data-aos="fade-right">
                <span class="text-accent font-semibold tracking-wider uppercase">Our Vision</span>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Setting New Standards in Luxury Hospitality</h2>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    At EaSyStaY, our vision is to redefine luxury hospitality by creating extraordinary experiences that combine timeless elegance with modern innovation. We strive to be the benchmark against which all luxury hotels are measured, known for our impeccable service, stunning environments, and commitment to exceeding guest expectations.
                </p>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    We believe that true luxury lies in the details—the warmth of a genuine smile, the comfort of a perfectly appointed room, the surprise of anticipating a need before it's expressed. Our vision guides every decision we make, from the selection of our locations to the training of our staff.
                </p>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                        <i class="fas fa-star"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Excellence in Every Detail</h4>
                        <p class="text-gray-600">Committed to perfection in every aspect of your stay</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Personalized Experience</h4>
                        <p class="text-gray-600">Tailoring every moment to your unique preferences</p>
                    </div>
                </div>
            </div>
            <div class="order-1 lg:order-2" data-aos="fade-left">
                <div class="relative">
                    <img src="../assets/images/about/vision.jpg" alt="Luxury Hotel Vision" class="w-full h-auto rounded-xl shadow-xl">
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-accent rounded-xl flex items-center justify-center text-primary text-5xl transform rotate-6 shadow-lg">
                        <i class="fas fa-gem"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div data-aos="fade-right">
                <div class="relative">
                    <img src="../assets/images/about/mission.jpg" alt="Our Mission" class="w-full h-auto rounded-xl shadow-xl">
                    <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary rounded-xl flex items-center justify-center text-secondary text-5xl transform -rotate-6 shadow-lg">
                        <i class="fas fa-compass"></i>
                    </div>
                </div>
            </div>
            <div data-aos="fade-left">
                <span class="text-accent font-semibold tracking-wider uppercase">Our Mission</span>
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Creating Unforgettable Moments</h2>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    Our mission is to create a sanctuary where guests can escape the ordinary and experience the extraordinary. We are dedicated to providing personalized service that anticipates needs, respects privacy, and creates memorable moments that last a lifetime.
                </p>
                <p class="text-gray-700 mb-6 leading-relaxed">
                    We are committed to sustainable luxury that honors our environment and communities. By integrating local culture, supporting local artisans, and implementing eco-friendly practices, we create authentic experiences while preserving the beauty of our destinations for future generations.
                </p>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Sustainable Luxury</h4>
                        <p class="text-gray-600">Eco-friendly practices without compromising on luxury</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-accent/10 flex items-center justify-center text-accent">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg">Community Engagement</h4>
                        <p class="text-gray-600">Supporting local culture and artisans in all our locations</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-16 bg-gradient-to-r from-primary to-dark text-secondary">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-accent font-semibold tracking-wider uppercase">Our Core Values</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-secondary mt-4 mb-6">The Principles That Guide Us</h2>
            <p class="text-xl text-secondary/90">These fundamental beliefs shape our culture and define how we interact with our guests, our team members, and our world.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Value 1 -->
            <div class="bg-white/10 p-8 rounded-xl backdrop-blur-sm text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent text-primary text-3xl rounded-full">
                    <i class="fas fa-crown"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Excellence</h3>
                <p>We pursue excellence in everything we do, constantly raising the bar and never settling for anything less than extraordinary.</p>
            </div>
            
            <!-- Value 2 -->
            <div class="bg-white/10 p-8 rounded-xl backdrop-blur-sm text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent text-primary text-3xl rounded-full">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Integrity</h3>
                <p>We act with honesty, transparency, and ethical responsibility in all our interactions and business practices.</p>
            </div>
            
            <!-- Value 3 -->
            <div class="bg-white/10 p-8 rounded-xl backdrop-blur-sm text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent text-primary text-3xl rounded-full">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Innovation</h3>
                <p>We embrace creativity and forward-thinking to continuously evolve and enhance the guest experience.</p>
            </div>
            
            <!-- Value 4 -->
            <div class="bg-white/10 p-8 rounded-xl backdrop-blur-sm text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent text-primary text-3xl rounded-full">
                    <i class="fas fa-globe"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Sustainability</h3>
                <p>We are committed to responsible practices that respect our environment and contribute positively to our communities.</p>
            </div>
        </div>
    </div>
</section>

 

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-primary to-dark text-secondary relative overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <img src="../assets/images/patterns/pattern1.jpg" alt="" class="w-full h-full object-cover">
    </div>
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl font-serif font-bold mb-6">Experience the EaSyStaY Difference</h2>
            <p class="text-xl mb-8 text-secondary/90">Join us for an unforgettable stay where luxury meets personalized service.</p>
            <a href="<?php echo $base_url; ?>/auth/login.php" class="inline-block bg-accent text-primary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                Book Your Stay Now <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<script>
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
</script>

<?php
// Include the footer file
include_once '../includes/footer.php';
?>