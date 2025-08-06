<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<header class="page-header bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold">Contact Us</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Contact</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</header>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <h2 class="section-title mb-4">Get In Touch</h2>
                <p class="mb-5">Have questions? Our team is here to help with any inquiries about your stay.</p>
                
                <div class="contact-info mb-4">
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="h6 mb-1">Our Location</h5>
                            <p>123 Beach Road, Puri, Odisha 752001</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <i class="fas fa-phone-alt fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="h6 mb-1">Phone</h5>
                            <p>+91 98765 43210</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <div class="me-3">
                            <i class="fas fa-envelope fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h5 class="h6 mb-1">Email</h5>
                            <p>info@stayinpuri.com</p>
                        </div>
                    </div>
                </div>
                
                <div class="social-links mt-5">
                    <h5 class="h6 mb-3">Follow Us</h5>
                    <a href="#" class="me-3 text-primary"><i class="fab fa-facebook-f fa-2x"></i></a>
                    <a href="#" class="me-3 text-primary"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#" class="text-primary"><i class="fab fa-tripadvisor fa-2x"></i></a>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="contact-form bg-white p-4 p-lg-5 shadow-sm rounded">
                    <h3 class="mb-4">Send Us a Message</h3>
                    <form id="contactForm" action="#" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <input type="text" class="form-control" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <input type="email" class="form-control" placeholder="Email Address" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <input type="text" class="form-control" placeholder="Subject" required>
                        </div>
                        
                        <div class="mb-4">
                            <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="map-container rounded shadow overflow-hidden">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3745.7406714389005!2d85.8334853153857!3d20.15694318650153!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a19b1f9e3a9a7c7%3A0x1e3a9b7d5e3e5d5b!2sPuri%2C%20Odisha!5e0!3m2!1sen!2sin!4v1620000000000!5m2!1sen!2sin" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>

<style>
.contact-form {
    background: #fff;
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,0.1);
}

.form-control {
    padding: 12px 15px;
    border: 1px solid #e1e5ee;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
}

.map-container {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.section-title {
    position: relative;
    display: inline-block;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.section-title:after {
    content: '';
    position: absolute;
    width: 50px;
    height: 3px;
    background: #0d6efd;
    bottom: -10px;
    left: 0;
    right: 0;
    margin: 0 auto;
}

.social-links a {
    transition: all 0.3s ease;
}

.social-links a:hover {
    opacity: 0.8;
    transform: translateY(-3px);
}
</style>

<?php include 'includes/footer.php'; ?>
