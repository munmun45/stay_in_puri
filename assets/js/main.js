/**
 * Main JavaScript File
 * Handles all the interactive elements of the website
 */


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
            }, {
                passive: true
            });

            slider.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                this.handleSwipe(touchStartX, touchEndX);
                this.startAutoSlide();
            }, {
                passive: true
            });
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





// Slider functionality
class OffersSlider {
    constructor() {
        this.container = document.getElementById('offersContainer');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.cardWidth = 420; // 400px + 20px gap
        this.currentPosition = 0;
        this.maxPosition = 0;
        this.autoSlideInterval = null;
        this.touchStartX = 0;
        this.touchEndX = 0;

        this.init();
    }

    init() {
        this.calculateMaxPosition();
        this.updateButtons();
        this.bindEvents();
        this.enableTouch();
        this.startAutoSlide();

        // Recalculate on window resize
        window.addEventListener('resize', () => {
            this.calculateMaxPosition();
            this.currentPosition = 0;
            this.updateSlider();
        });
    }

    calculateMaxPosition() {
        const containerWidth = this.container.parentElement.offsetWidth;
        const totalCards = this.container.children.length;
        const totalWidth = totalCards * this.cardWidth;
        this.maxPosition = Math.max(0, totalWidth - containerWidth);
    }

    bindEvents() {
        this.prevBtn.addEventListener('click', () => {
            this.slideLeft();
            this.resetAutoSlide();
        });

        this.nextBtn.addEventListener('click', () => {
            this.slideRight();
            this.resetAutoSlide();
        });
    }

    slideLeft() {
        this.currentPosition = Math.max(0, this.currentPosition - this.cardWidth);
        this.updateSlider();
    }

    slideRight() {
        this.currentPosition = Math.min(this.maxPosition, this.currentPosition + this.cardWidth);
        this.updateSlider();
    }

    updateSlider() {
        this.container.style.transform = `translateX(-${this.currentPosition}px)`;
        this.updateButtons();
    }

    updateButtons() {
        this.prevBtn.classList.toggle('disabled', this.currentPosition === 0);
        this.nextBtn.classList.toggle('disabled', this.currentPosition >= this.maxPosition);
    }

    // === Auto Slide ===
    startAutoSlide() {
        this.autoSlideInterval = setInterval(() => {
            if (this.currentPosition < this.maxPosition) {
                this.slideRight();
            } else {
                this.currentPosition = 0;
                this.updateSlider();
            }
        }, 3000); // every 3 seconds
    }

    resetAutoSlide() {
        clearInterval(this.autoSlideInterval);
        this.startAutoSlide();
    }

    // === Touch Swipe ===
    enableTouch() {
        this.container.addEventListener('touchstart', (e) => {
            this.touchStartX = e.touches[0].clientX;
        });

        this.container.addEventListener('touchmove', (e) => {
            this.touchEndX = e.touches[0].clientX;
        });

        this.container.addEventListener('touchend', () => {
            const distance = this.touchStartX - this.touchEndX;
            if (Math.abs(distance) > 50) {
                if (distance > 0) {
                    this.slideRight();
                } else {
                    this.slideLeft();
                }
                this.resetAutoSlide();
            }
        });
    }
}

// Initialize slider when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    let slider = new OffersSlider();

    // Reinitialize slider when tab changes
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function() {
            setTimeout(() => {
                slider = new OffersSlider(); // recreate to rebind all
            }, 100);
        });
    });
});











// Initialize slider when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new OffersSlider();

    // Reinitialize slider when tab changes
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function() {
            setTimeout(() => {
                new OffersSlider();
            }, 100);
        });
    });
});



















// Guest Selector Functions
function toggleGuestDropdown() {
    const dropdown = document.getElementById('guestDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function closeGuestDropdown() {
    document.getElementById('guestDropdown').style.display = 'none';
}

function updateGuestCount(type, change) {
    const countElement = document.getElementById(`${type}-count`);
    const inputElement = document.getElementById(type);
    let count = parseInt(countElement.textContent) + change;

    // Set min and max values
    if (type === 'adults') {
        count = Math.max(1, Math.min(10, count));
    } else if (type === 'children') {
        count = Math.max(0, Math.min(4, count));
    } else if (type === 'rooms') {
        count = Math.max(1, Math.min(5, count));
    }

    // Update the display and hidden input
    countElement.textContent = count;
    inputElement.value = count;

    // Update minus button disabled state
    const minusBtn = countElement.previousElementSibling;
    const plusBtn = countElement.nextElementSibling;

    if (type === 'adults' || type === 'rooms') {
        minusBtn.disabled = count <= 1;
        plusBtn.disabled = count >= (type === 'adults' ? 10 : 5);
    } else {
        minusBtn.disabled = count <= 0;
        plusBtn.disabled = count >= 4;
    }
}

function applyGuestSelection() {
    const adults = parseInt(document.getElementById('adults').value);
    const children = parseInt(document.getElementById('children').value);
    const rooms = parseInt(document.getElementById('rooms').value);

    let displayText = `${adults} ${adults === 1 ? 'Adult' : 'Adults'}`;
    if (children > 0) {
        displayText += `, ${children} ${children === 1 ? 'Child' : 'Children'}`;
    }
    displayText += `, ${rooms} ${rooms === 1 ? 'Room' : 'Rooms'}`;

    document.getElementById('guests-display').value = displayText;
    closeGuestDropdown();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('guestDropdown');
    const input = document.getElementById('guests-display');
    if (!dropdown.contains(event.target) && event.target !== input) {
        dropdown.style.display = 'none';
    }
});

// Restaurant Guest Selector Functions
function toggleRestaurantGuestDropdown() {
    const dropdown = document.getElementById('restaurantGuestDropdown');
    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
}

function closeRestaurantGuestDropdown() {
    document.getElementById('restaurantGuestDropdown').style.display = 'none';
}

function updateRestaurantGuestCount(change) {
    const countElement = document.getElementById('restaurant-people-count');
    const inputElement = document.getElementById('restaurant-people');
    let count = parseInt(countElement.textContent) + change;

    // Set min and max values (1-10 people)
    count = Math.max(1, Math.min(10, count));

    // Update the display and hidden input
    countElement.textContent = count;
    inputElement.value = count;

    // Update buttons state
    const minusBtn = countElement.previousElementSibling;
    const plusBtn = countElement.nextElementSibling;

    minusBtn.disabled = count <= 1;
    plusBtn.disabled = count >= 10;
}

function applyRestaurantGuestSelection() {
    const people = parseInt(document.getElementById('restaurant-people').value);
    const displayText = people + (people === 1 ? ' Person' : ' People');
    document.getElementById('restaurant-guests-display').value = displayText;
    closeRestaurantGuestDropdown();
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('guestDropdown');
    const input = document.getElementById('guests-display');
    const restaurantDropdown = document.getElementById('restaurantGuestDropdown');
    const restaurantInput = document.getElementById('restaurant-guests-display');

    if (dropdown && !dropdown.contains(event.target) && event.target !== input) {
        dropdown.style.display = 'none';
    }

    if (restaurantDropdown && !restaurantDropdown.contains(event.target) && event.target !== restaurantInput) {
        restaurantDropdown.style.display = 'none';
    }
});

// Initialize Swiper
$(document).ready(function() {
    // Initialize Hero Swiper
    new Swiper('.hero-swiper', {
        loop: true,
        autoplay: {
            delay: 5000
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        effect: 'fade',
        speed: 1000,
        grabCursor: true
    });

    // Initialize Restaurant Date Time Picker
    $('#restaurant-datetime').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        singleDatePicker: true,
        timePicker24Hour: false,
        locale: {
            format: 'MMMM D, YYYY h:mm A',
            applyLabel: 'Select',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        },
        minDate: new Date(),
        startDate: new Date(new Date().setHours(19, 0, 0, 0)), // Default to 7:00 PM
        minHour: 8, // 8 AM
        maxHour: 22 // 10 PM
    }, function(start, end, label) {
        $('#restaurant-date').val(start.format('YYYY-MM-DD'));
        $('#restaurant-time').val(start.format('HH:mm'));
    });

    // Set initial values
    const now = new Date();
    $('#restaurant-date').val(now.toISOString().split('T')[0]);
    $('#restaurant-time').val('19:00');

    // Initialize Date Range Picker
    $('input[name="daterange"]').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        minDate: new Date(),
        locale: {
            cancelLabel: 'Clear',
            format: 'DD/MM/YYYY',
            separator: ' - ',
            applyLabel: 'Apply',
            cancelLabel: 'Cancel',
            fromLabel: 'From',
            toLabel: 'To',
            customRangeLabel: 'Custom',
            daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            firstDay: 1
        }
    }, function(start, end, label) {
        // Format the dates as needed
        var startDate = start.format('DD/MM/YYYY');
        var endDate = end.format('DD/MM/YYYY');

        // Set the input field value
        $('input[name="daterange"]').val(startDate + ' - ' + endDate);

        // Set the hidden input values
        $('#checkin').val(start.format('YYYY-MM-DD'));
        $('#checkout').val(end.format('YYYY-MM-DD'));
    });

    // Clear the date range picker
    $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        $('#checkin').val('');
        $('#checkout').val('');
    });
});










document.addEventListener('DOMContentLoaded', function() {
    // Initialize Destination Swiper
    const destinationSwiper = new Swiper('.destinations-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        speed: 800,
        grabCursor: true,
        navigation: {
            nextEl: '.destination-next',
            prevEl: '.destination-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            576: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 3,
            },
            992: {
                slidesPerView: 4,
            }
        },
        on: {
            init: function() {
                // Add animation class to active slides
                this.slides[this.activeIndex].classList.add('swiper-slide-visible');
            },
            slideChange: function() {
                // Update animation classes on slide change
                this.slides.forEach(slide => {
                    slide.classList.remove('swiper-slide-visible');
                });
                this.slides[this.activeIndex].classList.add('swiper-slide-visible');
            }
        }
    });
    new Swiper('.destinations-swiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        autoplay: {
            delay: 3000, // 3 seconds delay between slides
            disableOnInteraction: false,
        },
        speed: 800, // Animation speed in ms
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            576: {
                slidesPerView: 2,
            },
            768: {
                slidesPerView: 2,
            },
            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 4,
            },
        }
    });
});