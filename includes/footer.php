</main>

<?php
    // Ensure contact info is available in footer as well
    if (!isset($contact_info)) {
        $contact_info = [
            'phone1' => '', 'phone2' => '', 'email1' => '', 'email2' => '', 'address' => '', 'google_map' => ''
        ];
        $conn = $conn ?? null;
        @require_once __DIR__ . '/../cbs/config/config.php';
        if (isset($conn)) {
            $rs_ci_f = $conn->query("SELECT phone1, phone2, email1, email2, address, google_map, facebook, twitter, instagram, youtube, whatsapp FROM contact_info WHERE id = 1");
            if ($rs_ci_f && $rs_ci_f->num_rows > 0) { $contact_info = $rs_ci_f->fetch_assoc(); }
        }
    }
    $footer_phone1 = $contact_info['phone1'] ?: '+91-8338011114';
    $footer_phone2 = $contact_info['phone2'] ?: '+91-9583506050';
    $footer_email  = $contact_info['email1'] ?: ($contact_info['email2'] ?: 'info@stayinpuri.in');
    $footer_addr   = $contact_info['address'] ?: 'Room No 100, Hotel Blue Sagar,Near Bengali Market, Swargadwar, Puri, Odisha 752001';
    // Build tel links (strip non-digits except +)
    $tel1 = preg_replace('/[^+\d]/', '', $footer_phone1);
    $tel2 = preg_replace('/[^+\d]/', '', $footer_phone2);
    $waNumber = $tel2 ?: $tel1;
    // Social links
    $facebook = trim($contact_info['facebook'] ?? '');
    $twitter = trim($contact_info['twitter'] ?? '');
    $instagram = trim($contact_info['instagram'] ?? '');
    $youtube = trim($contact_info['youtube'] ?? '');
    $whatsapp = trim($contact_info['whatsapp'] ?? '');
    $whatsapp_link = $whatsapp;
    if ($whatsapp_link !== '' && strpos($whatsapp_link, 'http') !== 0) {
        $whatsapp_link = 'https://wa.me/' . ltrim(preg_replace('/[^+\d]/', '', $whatsapp_link), '+');
    }
    if ($whatsapp_link === '') {
        $whatsapp_link = 'https://wa.me/' . htmlspecialchars(ltrim($waNumber, '+'), ENT_QUOTES);
    }
?>

<!-- Footer -->
<footer style="background-color:white;">

    

    <div class="footer mt-auto bg-dark text-white pt-5 pb-4">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget">
                        <a href="index.php" class="d-inline-block mb-4">
                            <img src="assets/img/stay-in-puri-white.png" alt="Stay in Puri" class="footer-logo" width="180">
                        </a>
                        <p class="mb-4">Your trusted partner for hotel bookings, travel packages, and restaurant reservations in Puri, Bhubaneswar, and across Odisha. Experience the best of Odisha with our curated services.</p>
                        <div class="social-media-bar">
                            <a href="<?= htmlspecialchars($facebook ?: '#', ENT_QUOTES) ?>" class="social-icon facebook" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-facebook-f"></i>
                                <span class="tooltip">Facebook</span>
                            </a>
                            <a href="<?= htmlspecialchars($twitter ?: '#', ENT_QUOTES) ?>" class="social-icon twitter" aria-label="Twitter" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-twitter"></i>
                                <span class="tooltip">Twitter</span>
                            </a>
                            <a href="<?= htmlspecialchars($instagram ?: '#', ENT_QUOTES) ?>" class="social-icon instagram" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-instagram"></i>
                                <span class="tooltip">Instagram</span>
                            </a>
                            <a href="<?= htmlspecialchars($youtube ?: '#', ENT_QUOTES) ?>" class="social-icon youtube" aria-label="YouTube" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-youtube"></i>
                                <span class="tooltip">YouTube</span>
                            </a>
                            <a href="#" class="social-icon linkedin" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-linkedin-in"></i>
                                <span class="tooltip">LinkedIn</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <h5 class="widget-title">Quick Links</h5>
                        <ul class="footer-links">
                            <li><a href="about.php">About Us</a></li>
                            <li><a href="services.php">Our Services</a></li>
                            <li><a href="destinations.php">Destinations</a></li>
                            <li><a href="packages.php">Tour Packages</a></li>
                            <li><a href="gallery.php">Gallery</a></li>
                            <li><a href="blog.php">Travel Blog</a></li>
                            <li><a href="testimonials.php">Testimonials</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="widget-title">Popular Destinations</h5>
                        <ul class="footer-links">
                            <li><a href="destination.php?city=puri">Puri Tourism</a></li>
                            <li><a href="destination.php?city=bhubaneswar">Bhubaneswar Tourism</a></li>
                            <li><a href="destination.php?city=konark">Konark Tourism</a></li>
                            <li><a href="destination.php?city=chilika">Chilika Lake</a></li>
                            <li><a href="destination.php?city=gopalpur">Gopalpur Beach</a></li>
                            <li><a href="destination.php?city=simlipal">Simlipal National Park</a></li>
                            <li><a href="destination.php?city=all">All Destinations</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget">
                        <h5 class="widget-title">Contact Us</h5>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($footer_addr, ENT_QUOTES) ?></span>
                            </li>
                            <li>
                                <i class="fas fa-phone-alt"></i>
                                <div>
                                    <a href="tel:<?= htmlspecialchars($tel1, ENT_QUOTES) ?>"><?= htmlspecialchars($footer_phone1, ENT_QUOTES) ?></a><br>
                                    <a href="tel:<?= htmlspecialchars($tel2, ENT_QUOTES) ?>"><?= htmlspecialchars($footer_phone2, ENT_QUOTES) ?></a>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?= htmlspecialchars($footer_email, ENT_QUOTES) ?>"><?= htmlspecialchars($footer_email, ENT_QUOTES) ?></a>
                            </li>
                        </ul>
                        <div class="newsletter-widget mt-4">
                            <h6>Subscribe to Our Newsletter</h6>
                            <form class="newsletter-form">
                                <div class="input-group">
                                    <input type="email" class="form-control" placeholder="Your email" required>
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4 bg-secondary">

            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="payment-methods mb-3 mb-md-0">
                        <span class="me-2">We accept:</span>
                        <img src="assets/images/payment/visa.png" alt="Visa" width="40">
                        <img src="assets/images/payment/mastercard.png" alt="Mastercard" width="40">
                        <img src="assets/images/payment/rupay.png" alt="RuPay" width="40">
                        <img src="assets/images/payment/upi.png" alt="UPI" width="40">
                        <img src="assets/images/payment/netbanking.png" alt="Net Banking" width="40">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Stay in Puri. All Rights Reserved.</p>
                </div>
            </div>
        </div>

        <!-- Back to Top Button -->
        <button id="backToTop" class="btn btn-primary back-to-top" aria-label="Back to top">
            <i class="fas fa-arrow-up"></i>
        </button>

        <!-- WhatsApp Float Button -->
        <a href="<?= htmlspecialchars($whatsapp_link, ENT_QUOTES) ?>" class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
</footer>


<!-- Preloader -->
<div class="preloader">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightgallery/2.7.0/lightgallery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.4/gsap.min.js"></script>

<!-- Main JS -->
<script src="assets/js/main.js" defer></script>

<!-- Structured Data for FAQ Page -->
<?php if (basename($_SERVER['PHP_SELF']) == 'faq.php'): ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "FAQPage",
            "mainEntity": [{
                "@type": "Question",
                "name": "How do I make a booking?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "You can make a booking directly through our website by selecting your preferred hotel, dates, and room type. Alternatively, you can contact our customer support for assistance."
                }
            }, {
                "@type": "Question",
                "name": "What is your cancellation policy?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Our cancellation policy varies depending on the hotel and rate plan. Please check the specific terms during the booking process or contact our support team for details."
                }
            }]
        }
    </script>
<?php endif; ?>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-XXXXXXXXXX');
</script>

<!-- Facebook Pixel Code -->
<script>
    ! function(f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function() {
            n.callMethod ?
                n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = '2.0';
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s)
    }(window, document, 'script',
        'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', 'YOUR_PIXEL_ID');
    fbq('track', 'PageView');
</script>
<noscript>
    <img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID&ev=PageView&noscript=1" />
</noscript>
<!-- End Facebook Pixel Code -->


</body>

</html>