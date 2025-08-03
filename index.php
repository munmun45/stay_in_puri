<?php include 'includes/header.php'; ?>


<!-- Bootstrap CSS -->

    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .offers-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            margin: 20px;
            padding: 24px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .view-all-link {
            color: #1a73e8;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .view-all-link:hover {
            color: #1557b0;
        }

        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 24px;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 12px 20px;
            border-bottom: 3px solid transparent;
            background: none;
        }

        .nav-tabs .nav-link.active {
            color: #1a73e8;
            border-bottom-color: #1a73e8;
            background: none;
        }

        .nav-tabs .nav-link:hover {
            color: #1a73e8;
            border-color: transparent;
        }

        .offer-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid #e9ecef;
        }

        .offer-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-image {
            height: 180px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .card-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(255, 255, 255, 0.95);
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .card-subtitle {
            font-size: 14px;
            color: #666;
            margin-bottom: 16px;
        }

        .card-btn {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .card-btn:hover {
            background: #1557b0;
            transform: translateY(-1px);
        }

        .card-btn.btn-outline {
            background: transparent;
            color: #1a73e8;
            border: 2px solid #1a73e8;
        }

        .card-btn.btn-outline:hover {
            background: #1a73e8;
            color: white;
        }

        .bank-logo {
            position: absolute;
            bottom: 12px;
            left: 12px;
            background: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .vacation-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .swiper {
            overflow: visible;
        }

        .swiper-slide {
            height: auto;
        }

        .swiper-button-next,
        .swiper-button-prev {
            background: white;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            color: #1a73e8;
            margin-top: -22px;
        }

        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 18px;
            font-weight: bold;
        }

        .swiper-button-disabled {
            opacity: 0.5;
        }

        .swiper-pagination-bullet {
            background: #dee2e6;
            opacity: 1;
        }

        .swiper-pagination-bullet-active {
            background: #1a73e8;
        }

        /* Specific card backgrounds matching the image */
        .card-1 {
            background-image: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
        }

        .card-2 {
            background-image: linear-gradient(135deg, #00cec9 0%, #00b894 100%);
        }

        .card-3 {
            background-image: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
        }

        .card-4 {
            background-image: linear-gradient(135deg, #fd79a8 0%, #e84393 100%);
        }

        .card-5 {
            background-image: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
        }

        .vacation-card {
            background-image: linear-gradient(135deg, #2d3436 0%, #636e72 100%);
        }

        @media (max-width: 768px) {
            .offers-section {
                margin: 10px;
                padding: 16px;
            }

            .section-title {
                font-size: 24px;
            }
        }
    </style>


<!-- Simple Image Slider -->

<div class="simple-slider">
    <div class="slider-container">
        <div class="slide active">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Puri Beach">
            
        </div>
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Jagannath Temple">
            
        </div>
        <div class="slide">
            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80" alt="Odisha Cuisine">
            
        </div>
    </div>
   
</div>


<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="search-tabs">
            <ul class="nav nav-tabs" id="searchTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="hotels-tab" data-bs-toggle="tab" data-bs-target="#hotels" type="button" role="tab" aria-controls="hotels" aria-selected="true">Hotels</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="restaurants-tab" data-bs-toggle="tab" data-bs-target="#restaurants" type="button" role="tab" aria-controls="restaurants" aria-selected="false">Restaurants</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tours-tab" data-bs-toggle="tab" data-bs-target="#tours" type="button" role="tab" aria-controls="tours" aria-selected="false">Tours</button>
                </li>
            </ul>
            <div class="tab-content" id="searchTabContent">
                <div class="tab-pane fade show active" id="hotels" role="tabpanel" aria-labelledby="hotels-tab">
                    <form action="search.php" method="GET" class="search-form">
                        <input type="hidden" name="type" value="hotels">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="destination" class="form-label">Destination</label>
                                <select class="form-select" id="destination" name="destination" required>
                                    <option value="">Select Destination</option>
                                    <option value="puri">Puri</option>
                                    <option value="bhubaneswar">Bhubaneswar</option>
                                    <option value="konark">Konark</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="check-in" class="form-label">Check-in</label>
                                <input type="date" class="form-control" id="check-in" name="check_in" required>
                            </div>
                            <div class="col-md-3">
                                <label for="check-out" class="form-label">Check-out</label>
                                <input type="date" class="form-control" id="check-out" name="check_out" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Similar forms for restaurants and tours -->
            </div>
        </div>
    </div>
</section>





    <section class="  offers-section">
        <div class="section-header">
            <h2 class="section-title">Offers</h2>
            <div class="view-all-container">
                <a href="offers.php" class="view-all-link">
                    VIEW ALL <i class="fas fa-arrow-right"></i>
                </a>
                <div class="slider-nav-buttons">
                    <button class="slider-nav-btn" id="prevBtn">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button class="slider-nav-btn" id="nextBtn">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Offer Tabs -->
        <ul class="nav nav-tabs" id="offerTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="hotels-offers-tab" data-bs-toggle="tab" data-bs-target="#hotels-offers" type="button" role="tab" aria-controls="hotels-offers" aria-selected="true">Hotels</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-offers-tab" data-bs-toggle="tab" data-bs-target="#all-offers" type="button" role="tab" aria-controls="all-offers" aria-selected="false">All Offers</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="flights-offers-tab" data-bs-toggle="tab" data-bs-target="#flights-offers" type="button" role="tab" aria-controls="flights-offers" aria-selected="false">Flights</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="holidays-offers-tab" data-bs-toggle="tab" data-bs-target="#holidays-offers" type="button" role="tab" aria-controls="holidays-offers" aria-selected="false">Holidays</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="trains-offers-tab" data-bs-toggle="tab" data-bs-target="#trains-offers" type="button" role="tab" aria-controls="trains-offers" aria-selected="false">Trains</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cabs-offers-tab" data-bs-toggle="tab" data-bs-target="#cabs-offers" type="button" role="tab" aria-controls="cabs-offers" aria-selected="false">Cabs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="bank-offers-tab" data-bs-toggle="tab" data-bs-target="#bank-offers" type="button" role="tab" aria-controls="bank-offers" aria-selected="false">Bank Offers</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="offerTabsContent">
            <!-- Hotels Tab -->
            <div class="tab-pane fade show active" id="hotels-offers" role="tabpanel" aria-labelledby="hotels-offers-tab">
                <div class="offers-slider">
                    <div class="offers-container" id="offersContainer">
                        <!-- Card 1 -->
                        <div class="offer-card">
                            <div class="card-image card-flight-img">
                                <div class="card-badge">T&C'S APPLY</div>
                            </div>
                            <div class="card-content">
                                <div>
                                    <h5 class="card-title">Ah-mazing Deal for You: Enjoy Up to ₹10,000 OFF*</h5>
                                    <p class="card-subtitle">on Flights and Hotels.</p>
                                </div>
                                <a href="#" class="card-btn">BOOK NOW</a>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="offer-card">
                            <div class="card-image card-hotel-img">
                                <div class="card-badge">T&C'S APPLY</div>
                                <div class="bank-logo">
                                    <img src="https://logos-world.net/wp-content/uploads/2020/09/HDFC-Bank-Logo.png" alt="HDFC" style="height: 16px;">
                                </div>
                            </div>
                            <div class="card-content">
                                <div>
                                    <h5 class="card-title">Up to 15% OFF*</h5>
                                    <p class="card-subtitle">on domestic & international hotels</p>
                                </div>
                                <a href="#" class="card-btn">BOOK NOW</a>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="offer-card">
                            <div class="card-image card-vacation-img">
                                <div class="vacation-badge">5★ VACATION INVITATION</div>
                                <div class="card-badge">T&C'S APPLY</div>
                            </div>
                            <div class="card-content">
                                <div>
                                    <h5 class="card-title">Just-in-time deals for luxe long weekend:</h5>
                                    <p class="card-subtitle">Up to 30% OFF* on premium hotels & villas across India.</p>
                                </div>
                                <a href="#" class="card-btn">BOOK NOW</a>
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div class="offer-card">
                            <div class="card-image card-travel-img">
                                <div class="card-badge">T&C'S APPLY</div>
                                <div class="bank-logo">
                                    <img src="https://logos-world.net/wp-content/uploads/2021/02/HSBC-Logo.png" alt="HSBC" style="height: 16px;">
                                </div>
                            </div>
                            <div class="card-content">
                                <div>
                                    <h5 class="card-title">Grab Up to 35% OFF* on</h5>
                                    <p class="card-subtitle">domestic + international flights, hotels & holiday packages for a memorable trip!</p>
                                </div>
                                <a href="#" class="card-btn btn-outline">VIEW DETAILS</a>
                            </div>
                        </div>

                        <!-- Card 5 -->
                        <div class="offer-card">
                            <div class="card-image card-emi-img">
                                <div class="card-badge">T&C'S APPLY</div>
                                <div class="bank-logo">
                                    <img src="https://logos-world.net/wp-content/uploads/2021/02/HSBC-Logo.png" alt="HSBC" style="height: 16px;">
                                </div>
                            </div>
                            <div class="card-content">
                                <div>
                                    <h5 class="card-title">Get 12-month EMI on hotels</h5>
                                    <p class="card-subtitle">on flights and hotel bookings with your next trip</p>
                                </div>
                                <a href="#" class="card-btn btn-outline">VIEW DETAILS</a>
                            </div>
                        </div>

                        <!-- Card 6 -->
                        <div class="offer-card">
                            <div class="card-image card-book-img">
                                <div class="card-badge">T&C'S APPLY</div>
                            </div>
                            <div class="card-content">
                                <div>
                                    <h5 class="card-title">Book by 30 Nov</h5>
                                    <p class="card-subtitle">For your favourite Goa destinations</p>
                                </div>
                                <a href="#" class="card-btn btn-outline">VIEW DETAILS</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other tabs content -->
            <div class="tab-pane fade" id="all-offers" role="tabpanel" aria-labelledby="all-offers-tab">
                <div class="offers-slider">
                    <div class="offers-container">
                        <!-- Sample cards for other tabs -->
                        <div class="offer-card">
                            <div class="card-image card-flight-img">
                                <div class="card-badge">T&C'S APPLY</div>
                            </div>
                            <div class="card-content">
                                <div>
                                    <h5 class="card-title">All Offers - Special Deal</h5>
                                    <p class="card-subtitle">Amazing discounts on all services</p>
                                </div>
                                <a href="#" class="card-btn">BOOK NOW</a>
                            </div>
                        </div>
                        <!-- Add more cards as needed -->
                    </div>
                </div>
            </div>

            <!-- Repeat similar structure for other tabs -->
            <div class="tab-pane fade" id="flights-offers" role="tabpanel" aria-labelledby="flights-offers-tab">
                <div class="text-center py-5">
                    <h4>Flight Offers Coming Soon</h4>
                    <p class="text-muted">Stay tuned for exclusive flight deals!</p>
                </div>
            </div>

            <div class="tab-pane fade" id="holidays-offers" role="tabpanel" aria-labelledby="holidays-offers-tab">
                <div class="text-center py-5">
                    <h4>Holiday Packages Coming Soon</h4>
                    <p class="text-muted">Amazing holiday deals are on the way!</p>
                </div>
            </div>

            <div class="tab-pane fade" id="trains-offers" role="tabpanel" aria-labelledby="trains-offers-tab">
                <div class="text-center py-5">
                    <h4>Train Offers Coming Soon</h4>
                    <p class="text-muted">Great train booking deals coming your way!</p>
                </div>
            </div>

            <div class="tab-pane fade" id="cabs-offers" role="tabpanel" aria-labelledby="cabs-offers-tab">
                <div class="text-center py-5">
                    <h4>Cab Offers Coming Soon</h4>
                    <p class="text-muted">Great cab deals coming your way!</p>
                </div>
            </div>

            <div class="tab-pane fade" id="bank-offers" role="tabpanel" aria-labelledby="bank-offers-tab">
                <div class="text-center py-5">
                    <h4>Bank Offers Coming Soon</h4>
                    <p class="text-muted">Exclusive bank partnership deals!</p>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Slider functionality
        class OffersSlider {
            constructor() {
                this.container = document.getElementById('offersContainer');
                this.prevBtn = document.getElementById('prevBtn');
                this.nextBtn = document.getElementById('nextBtn');
                this.cardWidth = 420; // 400px + 20px gap
                this.currentPosition = 0;
                this.maxPosition = 0;

                this.init();
            }

            init() {
                this.calculateMaxPosition();
                this.updateButtons();
                this.bindEvents();

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
                this.prevBtn.addEventListener('click', () => this.slideLeft());
                this.nextBtn.addEventListener('click', () => this.slideRight());
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
        }

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
    </script>










<?php include 'includes/footer.php'; ?>
