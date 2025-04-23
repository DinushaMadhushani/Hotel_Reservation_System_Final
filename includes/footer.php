<style>
.footer {
    background: linear-gradient(135deg, var(--dark) 0%, #1a1a1a 100%);
    color: var(--light);
    padding: 4rem 0;
    position: relative;
    overflow: hidden;
}

.footer::before {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background: linear-gradient(transparent 25%, rgba(255,255,255,0.05)), 
                linear-gradient(to right, transparent 25%, rgba(255,255,255,0.05));
    z-index: 0;
    opacity: 0.2;
}

.footer .container {
    position: relative;
    z-index: 1;
}

.footer h3 {
    color: var(--accent);
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.footer h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 40px;
    height: 2px;
    background: var(--accent);
    transition: width 0.3s ease;
}

.footer h3:hover::after {
    width: 60px;
}

.footer a {
    color: var(--light);
    text-decoration: none;
    transition: color 0.3s ease, transform 0.3s ease;
    display: inline-block;
    padding: 0.2rem 0;
    position: relative;
}

.footer a:hover {
    color: var(--accent);
    transform: translateX(5px);
}

.footer a::after {
    content: '';
    position: absolute;
    left: 0;
    right: 0;
    bottom: -2px;
    height: 1px;
    background: var(--accent);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.footer a:hover::after {
    transform: scaleX(1);
}

.social-icons a {
    color: var(--light);
    margin: 0 0.8rem;
    font-size: 1.2rem;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
    padding: 0.5rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.social-icons a:hover {
    color: var(--accent);
    transform: scale(1.2) rotate(15deg);
    box-shadow: 0 0 15px rgba(255,255,255,0.2);
}

.footer-widget {
    padding: 1.5rem;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.footer-widget:hover {
    background: rgba(255,255,255,0.05);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.newsletter-input {
    background: rgba(255,255,255,0.1);
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 25px;
    color: var(--light);
    transition: all 0.3s ease;
    width: 100%;
    margin-bottom: 1rem;
}

.newsletter-input:focus {
    outline: none;
    background: rgba(255,255,255,0.2);
    box-shadow: 0 0 0 2px var(--accent);
}

.newsletter-btn {
    background: var(--accent);
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
}

.newsletter-btn:hover {
    background: #ff6b6b;
    transform: scale(1.05);
}

.footer-bottom {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

@media (max-width: 768px) {
    .footer h3::after {
        display: none;
    }
    
    .footer-widget {
        margin-bottom: 1.5rem;
    }
    
    .social-icons a {
        margin: 0.5rem;
        font-size: 1.1rem;
    }
    
    .newsletter-input {
        margin-bottom: 0.5rem;
    }
}
</style>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- About Widget -->
            <div class="col-md-3 col-sm-6 footer-widget">
                <h3>About EaSyStaY</h3>
                <img src="logo.png" alt="Logo" class="mb-3" style="max-width: 120px;">
                <p class="mb-3">Crafting luxury experiences since 2023. Where modern amenities meet timeless elegance.</p>
                <div class="social-icons mt-3">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            
            <!-- Contact Widget -->
            <div class="col-md-3 col-sm-6 footer-widget">
                <h3>Contact Us</h3>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        29 Park Street, New York City, NY 10003
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:+12125550187">(212) 555-0187</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:info@easystay.com">info@easystay.com</a>
                    </li>
                    <li class="mt-3">
                        <i class="fas fa-clock me-2"></i>
                        Mon-Fri: 9AM - 8PM<br>
                        Sat-Sun: 10AM - 6PM
                    </li>
                </ul>
            </div>
            
            <!-- Quick Links -->
            <div class="col-md-3 col-sm-6 footer-widget">
                <h3>Quick Links</h3>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#">Terms of Service</a></li>
                    <li class="mb-2"><a href="#">Career Opportunities</a></li>
                    <li class="mb-2"><a href="#">FAQs</a></li>
                    <li class="mb-2"><a href="#">Sitemap</a></li>
                </ul>
            </div>
            
            <!-- Newsletter -->
            <div class="col-md-3 col-sm-6 footer-widget">
                <h3>Newsletter</h3>
                <p>Subscribe for exclusive offers and updates</p>
                <form class="newsletter-form">
                    <input type="email" class="newsletter-input" placeholder="Your email address">
                    <button type="submit" class="newsletter-btn">Subscribe</button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">© 2023 EaSyStaY. All rights reserved | Designed by ordainex</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <img src="payment-methods.png" alt="Payment Methods" style="max-width: 180px;">
                </div>
            </div>
        </div>
    </div>
    
</footer>