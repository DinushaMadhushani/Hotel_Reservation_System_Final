<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - EaSyStaY</title>
     <!-- Fonts -->
     <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #ffffff;
            --accent: #d4af37;
            --light: #f5f5f5;
            --dark: #121212;
        }
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.7;
            padding-top: 80px;
            color: var(--primary);
            background-color: var(--light);
        }
       
        .login-btn {
            background: var(--accent);
            border-radius: 12px;
            padding: 10px 25px;
            margin-left: 15px;
            transition: transform 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        .vm-section {
            background: var(--light);
            padding: 60px 0;
        }
        .vm-icon {
            font-size: 3rem;
            color: var(--accent);
            margin-bottom: 20px;
        }
        .contact-section {
            background: var(--secondary);
            padding: 60px 0;
        }
        .contact-info {
            font-size: 1.2rem;
            color: var(--primary);
        }
        .contact-form {
            background-color: var(--secondary);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .form-control {
            border-radius: 20px;
            padding: 15px;
            border: 1px solid var(--accent);
        }
        .btn-submit {
            background: var(--accent);
            border-radius: 20px;
            padding: 10px 40px;
        }
        .map-container {
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
        }
        .team-member {
            transition: transform 0.3s ease;
        }
        .team-member:hover {
            transform: translateY(-5px);
        }
       
        @media (max-width: 992px) {
            .navbar-collapse {
                background: var(--primary);
                margin-top: 10px;
            }
            .navbar-toggler {
                color: var(--secondary) !important;
            }
        }
    </style>
</head>
<?php include '../includes/header.php'?>
<!-- Vision & Mission Section -->
<section class="vm-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6" data-aos="fade-right">
                <div class="text-center mb-4">
                    <i class="fas fa-eye vm-icon"></i>
                    <h3 class="h4">Our Vision</h3>
                    <p>To revolutionize the hospitality industry through innovative technology and exceptional service standards.</p>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="text-center mb-4">
                    <i class="fas fa-bullseye vm-icon"></i>
                    <h3 class="h4">Our Mission</h3>
                    <p>Deliver seamless booking experiences while maintaining the highest standards of customer satisfaction and operational excellence.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-4" data-aos="fade-up">
                <div class="feature-box p-4 text-center">
                    <i class="fas fa-concierge-bell fa-3x mb-3 text-accent"></i>
                    <h4>24/7 Support</h4>
                    <p>Our virtual concierge is always available to assist with your needs</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box p-4 text-center">
                    <i class="fas fa-hand-holding-usd fa-3x mb-3 text-accent"></i>
                    <h4>Best Price Guarantee</h4>
                    <p>We offer the most competitive rates for all our properties</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-box p-4 text-center">
                    <i class="fas fa-globe fa-3x mb-3 text-accent"></i>
                    <h4>Global Network</h4>
                    <p>Access to 500,000+ properties worldwide</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <h2 class="text-center mb-5 text-secondary" data-aos="fade-up">Contact Us</h2>
        <div class="row">
            <div class="col-md-6" data-aos="fade-right">
                <div class="contact-info p-4">
                    <h5 class="text-secondary">Headquarters</h5>
                    <p class="text-secondary"><i class="fas fa-map-marker-alt"></i> 123 Hospitality Blvd, Suite 456<br>New York, NY 10001</p>
                    <p class="text-secondary"><i class="fas fa-phone"></i> +1 (800) 123-4567</p>
                    <p class="text-secondary"><i class="fas fa-envelope"></i> info@easystay.com</p>
                    <div class="social-icons mt-4">
                        <a href="#" class="text-secondary mr-3"><i class="fab fa-facebook-f fa-lg"></i></a>
                        <a href="#" class="text-secondary mr-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-secondary mr-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-secondary mr-3"><i class="fab fa-linkedin-in fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="contact-form">
                    <h5 class="mb-4 text-secondary">Send Us a Message</h5>
                    <form>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Subject">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-submit">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Team Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up">Our Team</h2>
        <div class="row">
            <div class="col-md-3" data-aos="zoom-in">
                <div class="team-member card border-0">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" class="card-img-top" alt="Team Member">
                    <div class="card-body text-center">
                        <h5 class="card-title">Sarah Johnson</h5>
                        <p class="card-text text-muted">Customer Experience Lead</p>
                        <div class="social-icons">
                            <a href="#" class="text-secondary"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-secondary"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="200">
                <div class="team-member card border-0">
                    <img src="https://randomuser.me/api/portraits/men/44.jpg" class="card-img-top" alt="Team Member">
                    <div class="card-body text-center">
                        <h5 class="card-title">Michael Chen</h5>
                        <p class="card-text text-muted">Tech Innovation Manager</p>
                        <div class="social-icons">
                            <a href="#" class="text-secondary"><i class="fab fa-github"></i></a>
                            <a href="#" class="text-secondary"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="400">
                <div class="team-member card border-0">
                    <img src="https://randomuser.me/api/portraits/women/24.jpg" class="card-img-top" alt="Team Member">
                    <div class="card-body text-center">
                        <h5 class="card-title">Emma Davis</h5>
                        <p class="card-text text-muted">Operations Coordinator</p>
                        <div class="social-icons">
                            <a href="#" class="text-secondary"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="text-secondary"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" data-aos="zoom-in" data-aos-delay="600">
                <div class="team-member card border-0">
                    <img src="https://randomuser.me/api/portraits/men/24.jpg" class="card-img-top" alt="Team Member">
                    <div class="card-body text-center">
                        <h5 class="card-title">David Kim</h5>
                        <p class="card-text text-muted">Quality Assurance Lead</p>
                        <div class="social-icons">
                            <a href="#" class="text-secondary"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-secondary"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5" data-aos="fade-up">What Our Guests Say</h2>
        <div class="row">
            <div class="col-md-4" data-aos="fade-up">
                <div class="card mb-4">
                    <div class="card-body">
                        <p class="card-text">"The seamless booking experience made my trip planning effortless!"</p>
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/women/64.jpg" class="rounded-circle mr-3" width="50" alt="Guest">
                            <div>
                                <h6 class="mb-0">Laura M.</h6>
                                <small class="text-muted">Recent Guest</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card mb-4">
                    <div class="card-body">
                        <p class="card-text">"The AI-powered recommendations helped me find the perfect boutique hotel."</p>
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/men/64.jpg" class="rounded-circle mr-3" width="50" alt="Guest">
                            <div>
                                <h6 class="mb-0">James R.</h6>
                                <small class="text-muted">Business Traveler</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                <div class="card mb-4">
                    <div class="card-body">
                        <p class="card-text">"Customer support resolved my last-minute change request in minutes!"</p>
                        <div class="d-flex align-items-center">
                            <img src="https://randomuser.me/api/portraits/women/84.jpg" class="rounded-circle mr-3" width="50" alt="Guest">
                            <div>
                                <h6 class="mb-0">Sophie T.</h6>
                                <small class="text-muted">Vacation Planner</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Footer -->
<?php include '../includes/footer.php'?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out-back',
    });
</script>
</body>
</html>