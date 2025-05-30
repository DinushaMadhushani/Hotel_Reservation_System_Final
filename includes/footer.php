 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

<style>
.footer {
    background: var(--primary);
    color: var(--secondary);
    padding: 3rem 0;
    border-top: 2px solid var(--accent);
    font-family: 'Inter', sans-serif;
}

.footer h5 {
    color: var(--secondary);
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.footer h5::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 2px;
    background: var(--accent);
}

.footer a {
    color: var(--light);
    text-decoration: none;
    transition: color 0.3s ease;
    font-size: 0.95rem;
}

.footer a:hover {
    color: var(--accent);
}

.social-links {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.social-links a {
    width: 38px;
    height: 38px;
    border: 1px solid var(--accent);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.social-links a:hover {
    background: var(--accent);
    color: var(--primary);
}

.contact-info li {
    margin-bottom: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.footer-bottom {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

@media (max-width: 768px) {
    .footer-col {
        margin-bottom: 2rem;
    }
}
</style>

<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6 footer-col">
                <h5>EaSyStaY</h5>
                <p class="text-light" style="opacity: 0.9;">
                    Crafting exceptional hospitality experiences with golden standard service since 2023.
                </p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
            
                    
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-4 col-md-6 footer-col">
                <h5>Contact</h5>
                <ul class="list-unstyled contact-info">
                    <li>
                        <i class="fas fa-map-marker-alt text-accent"></i>
                        29 Park Street, New York City
                    </li>
                    <li>
                        <i class="fas fa-phone text-accent"></i>
                        <a href="tel:+12125550187">(212) 555-0187</a>
                    </li>
                    <li>
                        <i class="fas fa-envelope text-accent"></i>
                        <a href="mailto:info@easystay.com">info@easystay.com</a>
                    </li>
                </ul>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-4 col-md-12 footer-col">
                <h5>Explore</h5>
                <div class="row">
                    <div class="col-6">
                        <a href="#" class="d-block mb-2">Home</a>
                        <a href="#" class="d-block mb-2">Rooms</a>
                        <a href="#" class="d-block mb-2">Gallery</a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="d-block mb-2">About Us</a>
                        <a href="#" class="d-block mb-2">Crew</a>
                        <a href="#" class="d-block mb-2">Contact</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <small class="text-light" style="opacity: 0.8;">© 2023 EaSyStaY. All rights reserved</small>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex gap-3 justify-content-center justify-content-md-end">
                        <img src="../assets/images/SVG/Visa.svg" alt="Visa" style="height: 24px; filter: grayscale(1) brightness(2);">
                        <img src="../assets/images/SVG/Mastercard.svg" alt="Mastercard" style="height: 24px; filter: grayscale(1) brightness(2);">
                        <img src="../assets/images/SVG/Paypal.svg"     alt="PayPal" style="height: 24px; filter: grayscale(1) brightness(2);">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>   