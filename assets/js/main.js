/**
 * Main JavaScript File
 * Handles all the interactive elements of the website
 */

// Menu Slider
class MenuSlider {
    constructor() {
        this.menuSlider = document.querySelector('.menu-slider');
        this.menuContent = document.querySelector('.menu-slider-content');
        this.menuToggle = document.querySelector('.menu-slider-toggle');
        this.closeButton = document.querySelector('.menu-slider-close');
        this.menuItems = document.querySelectorAll('.menu-item-has-children > a');
        
        this.init();
    }
    
    init() {
        // Toggle menu when clicking the menu button
        if (this.menuToggle) {
            this.menuToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleMenu();
            });
        }
        
        // Close menu when clicking the close button
        if (this.closeButton) {
            this.closeButton.addEventListener('click', () => this.closeMenu());
        }
        
        // Close menu when clicking outside
        if (this.menuSlider) {
            this.menuSlider.addEventListener('click', (e) => {
                if (e.target === this.menuSlider) {
                    this.closeMenu();
                }
            });
        }
        
        // Toggle submenus
        this.menuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                if (window.innerWidth < 992) { // Only on mobile
                    e.preventDefault();
                    const parent = item.parentElement;
                    const submenu = parent.querySelector('.sub-menu');
                    
                    if (submenu) {
                        parent.classList.toggle('active');
                        if (submenu.style.display === 'block') {
                            submenu.style.display = 'none';
                        } else {
                            submenu.style.display = 'block';
                        }
                    }
                }
            });
        });
    }
    
    toggleMenu() {
        document.body.classList.toggle('menu-open');
        this.menuSlider.classList.toggle('active');
        
        if (this.menuSlider.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
    
    openMenu() {
        document.body.classList.add('menu-open');
        this.menuSlider.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    closeMenu() {
        document.body.classList.remove('menu-open');
        this.menuSlider.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Simple Image Slider
class SimpleSlider {
    constructor() {
        this.slides = document.querySelectorAll('.slide');
        this.dots = document.querySelectorAll('.dot');
        this.prevBtn = document.querySelector('.slider-btn.prev');
        this.nextBtn = document.querySelector('.slider-btn.next');
        this.currentSlide = 0;
        this.slideInterval = null;
        this.autoSlideDelay = 5000; // 5 seconds
        
        if (this.slides.length > 0) {
            this.init();
        }
    }
    
    init() {
        // Show first slide
        this.showSlide(this.currentSlide);
        
        // Start auto slide
        this.startAutoSlide();
        
        // Event Listeners
        if (this.prevBtn) this.prevBtn.addEventListener('click', () => this.prevSlide());
        if (this.nextBtn) this.nextBtn.addEventListener('click', () => this.nextSlide());
        
        // Dot navigation
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.goToSlide(index));
        });
        
        // Pause on hover
        const slider = document.querySelector('.simple-slider');
        if (slider) {
            slider.addEventListener('mouseenter', () => this.pauseAutoSlide());
            slider.addEventListener('mouseleave', () => this.startAutoSlide());
            
            // Touch events for mobile
            let touchStartX = 0;
            let touchEndX = 0;
            
            slider.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                this.pauseAutoSlide();
            }, { passive: true });
            
            slider.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                this.handleSwipe(touchStartX, touchEndX);
                this.startAutoSlide();
            }, { passive: true });
        }
    }
    
    showSlide(index) {
        // Hide all slides
        this.slides.forEach(slide => slide.classList.remove('active'));
        this.dots.forEach(dot => dot.classList.remove('active'));
        
        // Show current slide
        if (this.slides[index]) this.slides[index].classList.add('active');
        if (this.dots[index]) this.dots[index].classList.add('active');
        
        this.currentSlide = index;
    }
    
    nextSlide() {
        const nextIndex = (this.currentSlide + 1) % this.slides.length;
        this.showSlide(nextIndex);
    }
    
    prevSlide() {
        const prevIndex = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
        this.showSlide(prevIndex);
    }
    
    goToSlide(index) {
        this.showSlide(index);
    }
    
    startAutoSlide() {
        this.pauseAutoSlide();
        this.slideInterval = setInterval(() => this.nextSlide(), this.autoSlideDelay);
    }
    
    pauseAutoSlide() {
        if (this.slideInterval) {
            clearInterval(this.slideInterval);
            this.slideInterval = null;
        }
    }
    
    handleSwipe(startX, endX) {
        const swipeThreshold = 50; // Minimum distance for a swipe
        const difference = startX - endX;
        
        if (Math.abs(difference) > swipeThreshold) {
            if (difference > 0) {
                this.nextSlide(); // Swipe left
            } else {
                this.prevSlide(); // Swipe right
            }
        }
    }
}

// Initialize the slider when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize menu slider if it exists on the page
    if (document.querySelector('.menu-slider')) {
        new MenuSlider();
    }
    
    // Initialize image slider if it exists on the page
    if (document.querySelector('.simple-slider')) {
        new SimpleSlider();
    }
    
    // Rest of your existing DOMContentLoaded code
    // Preloader
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        window.addEventListener('load', function() {
            setTimeout(function() {
                preloader.classList.add('fade-out');
                setTimeout(function() {
                    preloader.style.display = 'none';
                }, 500);
            }, 500);
        });
    }

    // Mobile Menu Toggle
    const mobileMenuToggler = document.querySelector('.navbar-toggler');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileMenuToggler && mobileMenu) {
        mobileMenuToggler.addEventListener('click', function() {
            document.body.classList.toggle('mobile-menu-open');
        });
        
        // Close mobile menu when clicking on a nav link
        const mobileNavLinks = mobileMenu.querySelectorAll('.nav-link');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                const bsCollapse = new bootstrap.Collapse(mobileMenu);
                bsCollapse.hide();
                document.body.classList.remove('mobile-menu-open');
            });
        });
    }

    // Sticky Header
    const header = document.querySelector('.main-header');
    if (header) {
        let lastScroll = 0;
        const headerHeight = header.offsetHeight;
        
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > headerHeight) {
                header.classList.add('header-sticky');
                document.body.style.paddingTop = headerHeight + 'px';
                
                if (currentScroll > lastScroll && currentScroll > headerHeight * 2) {
                    // Scrolling down
                    header.style.transform = 'translateY(-100%)';
                } else {
                    // Scrolling up
                    header.style.transform = 'translateY(0)';
                }
            } else {
                header.classList.remove('header-sticky');
                document.body.style.paddingTop = 0;
            }
            
            lastScroll = currentScroll;
        });
    }

    // Back to Top Button
    const backToTopButton = document.getElementById('backToTop');
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });
        
        backToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Initialize Hero Slider
    const heroSlider = document.querySelector('.hero-slider .swiper-container');
    if (heroSlider) {
        new Swiper(heroSlider, {
            loop: true,
            effect: 'fade',
            speed: 1000,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }

    // Initialize Testimonial Slider
    const testimonialSlider = document.querySelector('.testimonial-slider');
    if (testimonialSlider) {
        new Swiper(testimonialSlider, {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 30,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                992: {
                    slidesPerView: 3,
                }
            }
        });
    }

    // Smooth Scrolling for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                
                const headerHeight = document.querySelector('.main-header').offsetHeight;
                const targetPosition = targetElement.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Close mobile menu if open
                if (document.body.classList.contains('mobile-menu-open')) {
                    const bsCollapse = new bootstrap.Collapse(document.getElementById('mobileMenu'));
                    bsCollapse.hide();
                    document.body.classList.remove('mobile-menu-open');
                }
            }
        });
    });

    // Form Validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Lazy Loading Images
    if ('loading' in HTMLImageElement.prototype) {
        const images = document.querySelectorAll('img[loading="lazy"]');
        images.forEach(img => {
            img.src = img.dataset.src;
        });
    } else {
        // Fallback for browsers that don't support lazy loading
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
        document.body.appendChild(script);
    }

    // Initialize LightGallery
    const galleryElements = document.querySelectorAll('.gallery');
    if (galleryElements.length > 0 && typeof lightGallery === 'function') {
        galleryElements.forEach(element => {
            lightGallery(element, {
                selector: '.gallery-item',
                download: false,
                thumbnail: true,
                animateThumb: true,
                showThumbByDefault: false,
                speed: 500
            });
        });
    }

    // Add active class to current nav link
    const currentPage = location.pathname.split('/').pop() || 'index.php';
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    navLinks.forEach(link => {
        const linkHref = link.getAttribute('href');
        if (linkHref === currentPage || (currentPage === '' && linkHref === 'index.php')) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
            
            // If it's a dropdown parent, also highlight the parent
            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown) {
                const dropdownToggle = parentDropdown.querySelector('.dropdown-toggle');
                if (dropdownToggle) {
                    dropdownToggle.classList.add('active');
                }
            }
        }
    });

    // Initialize AOS (Animate On Scroll)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    }

    // Handle search form submission
    const searchForms = document.querySelectorAll('.search-form');
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const checkIn = this.querySelector('input[name="check_in"]');
            const checkOut = this.querySelector('input[name="check_out"]');
            
            if (checkIn && checkOut) {
                const checkInDate = new Date(checkIn.value);
                const checkOutDate = new Date(checkOut.value);
                
                if (checkInDate >= checkOutDate) {
                    e.preventDefault();
                    alert('Check-out date must be after check-in date');
                    checkOut.focus();
                }
            }
        });
    });

    // Set minimum date for check-in and check-out fields
    const today = new Date().toISOString().split('T')[0];
    const checkInInputs = document.querySelectorAll('input[name="check_in"]');
    const checkOutInputs = document.querySelectorAll('input[name="check_out"]');
    
    checkInInputs.forEach(input => {
        input.setAttribute('min', today);
        
        input.addEventListener('change', function() {
            checkOutInputs.forEach(outInput => {
                outInput.setAttribute('min', this.value);
                if (outInput.value && new Date(outInput.value) <= new Date(this.value)) {
                    outInput.value = '';
                }
            });
        });
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Add animation to elements when they come into view
    const animateOnScroll = function() {
        const elements = document.querySelectorAll('.animate-on-scroll');
        
        elements.forEach(element => {
            const elementPosition = element.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;
            
            if (elementPosition < screenPosition) {
                element.classList.add('animated');
            }
        });
    };
    
    window.addEventListener('scroll', animateOnScroll);
    // Run once on page load
    animateOnScroll();

    // Handle newsletter subscription
    const newsletterForms = document.querySelectorAll('.newsletter-form');
    newsletterForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            if (email && validateEmail(email)) {
                // Here you would typically send this to your server
                console.log('Subscribing email:', email);
                alert('Thank you for subscribing to our newsletter!');
                emailInput.value = '';
            } else {
                alert('Please enter a valid email address');
                emailInput.focus();
            }
        });
    });

    // Email validation helper
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    // Handle click outside mobile menu to close it
    document.addEventListener('click', function(e) {
        const mobileMenu = document.getElementById('mobileMenu');
        const menuButton = document.querySelector('.navbar-toggler');
        
        if (mobileMenu && menuButton && !mobileMenu.contains(e.target) && !menuButton.contains(e.target)) {
            const bsCollapse = new bootstrap.Collapse(mobileMenu);
            bsCollapse.hide();
            document.body.classList.remove('mobile-menu-open');
        }
    });

    // Add loading state to buttons on form submission
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('form');
            if (form && form.checkValidity()) {
                this.setAttribute('disabled', 'disabled');
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
                form.submit();
            }
        });
    });

    // Handle tab content loading
    const tabLinks = document.querySelectorAll('[data-bs-toggle="tab"], [data-bs-toggle="pill"]');
    tabLinks.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            const targetPane = document.querySelector(e.target.getAttribute('data-bs-target'));
            if (targetPane) {
                // Trigger any lazy loading or other actions when tab is shown
                const lazyImages = targetPane.querySelectorAll('img[loading="lazy"]');
                lazyImages.forEach(img => {
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                });
            }
        });
    });

    // Add active class to current section in navigation
    const sections = document.querySelectorAll('section[id]');
    
    function onScroll() {
        const scrollPosition = window.scrollY + 200;
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute('id');
            
            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                document.querySelector(`.navbar-nav .nav-link[href*="${sectionId}"]`).classList.add('active');
            } else {
                const navLink = document.querySelector(`.navbar-nav .nav-link[href*="${sectionId}"]`);
                if (navLink && !navLink.classList.contains('dropdown-toggle')) {
                    navLink.classList.remove('active');
                }
            }
        });
    }
    
    // Only run this if we have sections with IDs
    if (sections.length > 0) {
        window.addEventListener('scroll', onScroll);
    }
});
