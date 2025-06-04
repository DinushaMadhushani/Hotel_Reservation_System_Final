<?php
// Include the header file
include_once '../includes/header.php';
?>

<!-- Hero Section -->
<section id="hero" class="hero-bg h-screen flex items-center justify-center relative overflow-hidden" style="background-image: url('../assets/images/other_hero/gallery-hero.jpg');">
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/80"></div>
    <div class="container mx-auto px-4 z-10 text-center" data-aos="fade-up" data-aos-delay="100">
        <div class="mb-8">
            <h1 class="text-4xl md:text-6xl font-serif font-bold text-secondary mt-4 mb-6 leading-tight">Our <span class="text-accent">Gallery</span></h1>
            <p class="text-xl md:text-2xl text-secondary/90 max-w-3xl mx-auto">Explore the visual journey through our luxurious spaces and experiences</p>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-center gap-4" data-aos="fade-up" data-aos-delay="300">
            <a href="#gallery" class="cta-button bg-accent text-primary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                View Gallery <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-light">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-accent font-semibold tracking-wider uppercase">Experience Excellence</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Signature Services</h2>
            <p class="text-xl text-gray-600">Discover the exceptional amenities and services that define the EaSyStaY experience.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">24/7 Concierge</h3>
                <p class="text-gray-600">Our dedicated concierge team is available around the clock to fulfill your every request, from dinner reservations to exclusive experiences.</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full">
                    <i class="fas fa-spa"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Luxury Spa</h3>
                <p class="text-gray-600">Indulge in rejuvenating treatments at our world-class spa, featuring premium products and expert therapists for ultimate relaxation.</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="bg-white p-8 rounded-xl shadow-lg text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="w-20 h-20 mx-auto mb-6 flex items-center justify-center bg-accent/10 text-accent text-3xl rounded-full">
                    <i class="fas fa-utensils"></i>
                </div>
                <h3 class="text-xl font-bold mb-3">Fine Dining</h3>
                <p class="text-gray-600">Experience culinary excellence at our award-winning restaurants, where master chefs create unforgettable gastronomic journeys.</p>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section id="gallery" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
            <span class="text-accent font-semibold tracking-wider uppercase">Visual Journey</span>
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-primary mt-4 mb-6">Explore Our Spaces</h2>
            <p class="text-xl text-gray-600">Take a visual tour through our luxurious accommodations, amenities, and experiences.</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Gallery Item 1 -->
            <div class="gallery-item rounded-xl overflow-hidden shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="100" data-src="../assets/images/gallery page/img1.jpg" data-title="Luxury Suite Bedroom">
                <div class="relative overflow-hidden group">
                    <img src="../assets/images/gallery page/img1.jpg" alt="Luxury Suite Bedroom" class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <div>
                            <h3 class="text-xl font-bold text-white">Luxury Suite Bedroom</h3>
                            <p class="text-white/80">Experience ultimate comfort in our premium suites</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Item 2 -->
            <div class="gallery-item rounded-xl overflow-hidden shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="150" data-src="../assets/images/gallery page/img2.jpg" data-title="Infinity Pool">
                <div class="relative overflow-hidden group">
                    <img src="../assets/images/gallery page/img2.jpg" alt="Infinity Pool" class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <div>
                            <h3 class="text-xl font-bold text-white">Infinity Pool</h3>
                            <p class="text-white/80">Swim with breathtaking panoramic views</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Item 3 -->
            <div class="gallery-item rounded-xl overflow-hidden shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="200" data-src="../assets/images/gallery page/img3.jpg" data-title="Gourmet Restaurant">
                <div class="relative overflow-hidden group">
                    <img src="../assets/images/gallery page/img3.jpg" alt="Gourmet Restaurant" class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <div>
                            <h3 class="text-xl font-bold text-white">Gourmet Restaurant</h3>
                            <p class="text-white/80">Savor exquisite culinary creations</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Item 4 -->
            <div class="gallery-item rounded-xl overflow-hidden shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="250" data-src="../assets/images/gallery page/img4.jpg" data-title="Luxury Spa">
                <div class="relative overflow-hidden group">
                    <img src="../assets/images/gallery page/img4.jpg" alt="Luxury Spa" class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <div>
                            <h3 class="text-xl font-bold text-white">Luxury Spa</h3>
                            <p class="text-white/80">Indulge in rejuvenating treatments</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Item 5 -->
            <div class="gallery-item rounded-xl overflow-hidden shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="300" data-src="../assets/images/gallery page/img5.jpg" data-title="Executive Lounge">
                <div class="relative overflow-hidden group">
                    <img src="../assets/images/gallery page/img5.jpg" alt="Executive Lounge" class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <div>
                            <h3 class="text-xl font-bold text-white">Executive Lounge</h3>
                            <p class="text-white/80">Exclusive space for business and relaxation</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Item 6 -->
            <div class="gallery-item rounded-xl overflow-hidden shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="350" data-src="../assets/images/gallery page/img6.jpg" data-title="Penthouse Suite">
                <div class="relative overflow-hidden group">
                    <img src="../assets/images/gallery page/img6.jpg" alt="Penthouse Suite" class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <div>
                            <h3 class="text-xl font-bold text-white">Penthouse Suite</h3>
                            <p class="text-white/80">The pinnacle of luxury accommodation</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Item 7 -->
            <div class="gallery-item rounded-xl overflow-hidden shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="400" data-src="../assets/images/gallery page/img7.jpg" data-title="Fitness Center">
                <div class="relative overflow-hidden group">
                    <img src="../assets/images/gallery page/img7.jpg" alt="Fitness Center" class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <div>
                            <h3 class="text-xl font-bold text-white">Fitness Center</h3>
                            <p class="text-white/80">State-of-the-art equipment and personal trainers</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Item 8 -->
            <div class="gallery-item rounded-xl overflow-hidden shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="450" data-src="../assets/images/gallery page/img8.jpg" data-title="Rooftop Bar">
                <div class="relative overflow-hidden group">
                    <img src="../assets/images/gallery page/img8.jpg" alt="Rooftop Bar" class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <div>
                            <h3 class="text-xl font-bold text-white">Rooftop Bar</h3>
                            <p class="text-white/80">Cocktails with stunning city views</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gallery Item 9 -->
            <div class="gallery-item rounded-xl overflow-hidden shadow-lg cursor-pointer" data-aos="fade-up" data-aos-delay="500" data-src="../assets/images/gallery page/img9.jpg" data-title="Grand Ballroom">
                <div class="relative overflow-hidden group">
                    <img src="../assets/images/gallery page/img9.jpg" alt="Grand Ballroom" class="w-full h-80 object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                        <div>
                            <h3 class="text-xl font-bold text-white">Grand Ballroom</h3>
                            <p class="text-white/80">Elegant space for memorable events</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Lightbox -->
<div id="lightbox" class="fixed inset-0 bg-black/90 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-5xl w-full">
        <button id="close-lightbox" class="absolute -top-12 right-0 text-white text-3xl hover:text-accent transition-colors">
            <i class="fas fa-times"></i>
        </button>
        <div class="flex items-center justify-between">
            <button id="prev-image" class="text-white text-4xl hover:text-accent transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="mx-4 flex-1">
                <img id="lightbox-image" src="" alt="Gallery Image" class="max-h-[80vh] mx-auto rounded-lg shadow-2xl">
                <div class="mt-4 text-center">
                    <h3 id="lightbox-title" class="text-xl font-bold text-white"></h3>
                    <div class="flex justify-center mt-4 space-x-4">
                        <a id="download-image" href="" download class="text-white hover:text-accent transition-colors">
                            <i class="fas fa-download mr-2"></i> Download
                        </a>
                        <button id="copy-link" class="text-white hover:text-accent transition-colors">
                            <i class="fas fa-link mr-2"></i> Copy Link
                        </button>
                    </div>
                </div>
            </div>
            <button id="next-image" class="text-white text-4xl hover:text-accent transition-colors">
                <i class="fas fa-chevron-right"></i>
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
            <a href="<?php echo $base_url; ?>/auth/login.php" class="inline-block bg-accent text-primary font-semibold px-8 py-4 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                Book Your Stay <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<script>
    // Lightbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        const lightbox = document.getElementById('lightbox');
        const lightboxImage = document.getElementById('lightbox-image');
        const lightboxTitle = document.getElementById('lightbox-title');
        const downloadImage = document.getElementById('download-image');
        const copyLink = document.getElementById('copy-link');
        const closeLightbox = document.getElementById('close-lightbox');
        const prevImage = document.getElementById('prev-image');
        const nextImage = document.getElementById('next-image');
        const galleryItems = document.querySelectorAll('.gallery-item');
        
        let currentIndex = 0;
        
        // Open lightbox
        galleryItems.forEach((item, index) => {
            item.addEventListener('click', function() {
                const imgSrc = this.getAttribute('data-src');
                const imgTitle = this.getAttribute('data-title');
                
                lightboxImage.src = imgSrc;
                lightboxTitle.textContent = imgTitle;
                downloadImage.href = imgSrc;
                currentIndex = index;
                
                lightbox.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            });
        });
        
        // Close lightbox
        closeLightbox.addEventListener('click', function() {
            lightbox.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
        
        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) {
                lightbox.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        });
        
        // Navigate to previous image
        prevImage.addEventListener('click', function() {
            currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
            const item = galleryItems[currentIndex];
            lightboxImage.src = item.getAttribute('data-src');
            lightboxTitle.textContent = item.getAttribute('data-title');
            downloadImage.href = item.getAttribute('data-src');
        });
        
        // Navigate to next image
        nextImage.addEventListener('click', function() {
            currentIndex = (currentIndex + 1) % galleryItems.length;
            const item = galleryItems[currentIndex];
            lightboxImage.src = item.getAttribute('data-src');
            lightboxTitle.textContent = item.getAttribute('data-title');
            downloadImage.href = item.getAttribute('data-src');
        });
        
        // Arrow key navigation
        document.addEventListener('keydown', function(e) {
            if (lightbox.classList.contains('hidden')) return;
            
            if (e.key === 'ArrowLeft') {
                prevImage.click();
            } else if (e.key === 'ArrowRight') {
                nextImage.click();
            }
        });
        
        // Copy image link
        copyLink.addEventListener('click', function() {
            const tempInput = document.createElement('input');
            tempInput.value = lightboxImage.src;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            // Show copied notification
            const originalText = copyLink.innerHTML;
            copyLink.innerHTML = '<i class="fas fa-check mr-2"></i> Copied!';
            setTimeout(() => {
                copyLink.innerHTML = originalText;
            }, 2000);
        });
    });
</script>

<?php
// Include the footer file
include_once '../includes/footer.php';
?>