<?php include 'includes/header.php'; ?>

<!-- Hero Slider with Swiper -->
<div class="hero-swiper">
    <div class="swiper-wrapper">
        <div class="swiper-slide">
            <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
                
            </div>
        </div>
        <div class="swiper-slide">
            <div class="hero-slide" style="background-image: url('https://www.industrialempathy.com/img/remote/ZiClJf-1920w.jpg');">
                
            </div>
        </div>
    </div>
</div>



<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="search-wrapper">
            <div class="search-tabs mb-3">
                <div class="nav nav-pills nav-fill" id="searchTab" role="tablist">
                    <button class="nav-link active me-2" id="hotels-tab" data-bs-toggle="pill" data-bs-target="#hotels" type="button" role="tab">
                        <i class="fas fa-hotel me-2"></i>Hotels
                    </button>
                    <button class="nav-link me-2" id="restaurants-tab" data-bs-toggle="pill" data-bs-target="#restaurants" type="button" role="tab">
                        <i class="fas fa-utensils me-2"></i>Restaurants
                    </button>
                    <button class="nav-link" id="tours-tab" data-bs-toggle="pill" data-bs-target="#tours" type="button" role="tab">
                        <i class="fas fa-map-marked-alt me-2"></i>Tours
                    </button>
                </div>
            </div>
            
            <div class="tab-content bg-white p-4 rounded-3" id="searchTabContent">
                <!-- Hotels Tab -->
                <div class="tab-pane fade show active" id="hotels" role="tabpanel" aria-labelledby="hotels-tab">
                    <form action="search.php" method="GET" class="search-form">
                        <input type="hidden" name="type" value="hotels">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="destination" class="form-label fw-500 text-muted mb-1">Destination</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                        <select class="form-select border-start-0 ps-1" id="destination" name="destination" required>
                                            <option value="">Where are you going?</option>
                                            <option value="puri">Puri, Odisha</option>
                                            <option value="bhubaneswar">Bhubaneswar, Odisha</option>
                                            <option value="konark">Konark, Odisha</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="daterange" class="form-label fw-500 text-muted mb-1">Check-in / Check-out</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-primary"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-1" id="daterange" name="daterange" value="" placeholder="Select dates" readonly>
                                        <input type="hidden" id="checkin" name="checkin">
                                        <input type="hidden" id="checkout" name="checkout">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="guests-display" class="form-label fw-500 text-muted mb-1">Guests & Rooms</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-users text-primary"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-1" id="guests-display" readonly value="2 Adults, 1 Room" onclick="toggleGuestDropdown()">
                                        <input type="hidden" id="adults" name="adults" value="2">
                                        <input type="hidden" id="children" name="children" value="0">
                                        <input type="hidden" id="rooms" name="rooms" value="1">
                                        
                                        <!-- Guest Selection Dropdown -->
                                        <div class="guest-selector-dropdown" id="guestDropdown">
                                            <div class="guest-option">
                                                <div class="guest-label">
                                                    <span>Adults</span>
                                                    <small>Ages 13 or above</small>
                                                </div>
                                                <div class="guest-counter">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-minus" onclick="updateGuestCount('adults', -1)">-</button>
                                                    <span id="adults-count" class="px-2">2</span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-plus" onclick="updateGuestCount('adults', 1)">+</button>
                                                </div>
                                            </div>
                                            
                                            <div class="guest-option">
                                                <div class="guest-label">
                                                    <span>Children</span>
                                                    <small>Ages 0-12</small>
                                                </div>
                                                <div class="guest-counter">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-minus" onclick="updateGuestCount('children', -1)" disabled>-</button>
                                                    <span id="children-count" class="px-2">0</span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-plus" onclick="updateGuestCount('children', 1)">+</button>
                                                </div>
                                            </div>
                                            
                                            <div class="guest-option">
                                                <div class="guest-label">
                                                    <span>Rooms</span>
                                                </div>
                                                <div class="guest-counter">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-minus" onclick="updateGuestCount('rooms', -1)" disabled>-</button>
                                                    <span id="rooms-count" class="px-2">1</span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-plus" onclick="updateGuestCount('rooms', 1)">+</button>
                                                </div>
                                            </div>
                                            
                                            <div class="guest-dropdown-footer">
                                                <button type="button" class="btn btn-sm btn-link text-muted" onclick="closeGuestDropdown()">Cancel</button>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="applyGuestSelection()">Apply</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Restaurants Tab -->
                <div class="tab-pane fade" id="restaurants" role="tabpanel" aria-labelledby="restaurants-tab">
                    <form action="search.php" method="GET" class="search-form">
                        <input type="hidden" name="type" value="restaurants">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="restaurant-location" class="form-label fw-500 text-muted mb-1">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                        <select class="form-select border-start-0 ps-1" id="restaurant-location" name="location" required>
                                            <option value="">Select Location</option>
                                            <option value="puri">Puri, Odisha</option>
                                            <option value="bhubaneswar">Bhubaneswar, Odisha</option>
                                            <option value="cuttack">Cuttack, Odisha</option>
                                            <option value="konark">Konark, Odisha</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="restaurant-datetime" class="form-label fw-500 text-muted mb-1">Date & Time</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-primary"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-1" id="restaurant-datetime" readonly>
                                        <input type="hidden" id="restaurant-date" name="date">
                                        <input type="hidden" id="restaurant-time" name="time">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="restaurant-guests-display" class="form-label fw-500 text-muted mb-1">Guests</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-users text-primary"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-1" id="restaurant-guests-display" readonly value="2 People" onclick="toggleRestaurantGuestDropdown()">
                                        <input type="hidden" id="restaurant-people" name="people" value="2">
                                        
                                        <!-- Restaurant Guest Selection Dropdown -->
                                        <div class="guest-selector-dropdown" id="restaurantGuestDropdown">
                                            <div class="guest-option">
                                                <div class="guest-label">
                                                    <span>People</span>
                                                    <small>Including children</small>
                                                </div>
                                                <div class="guest-counter">
                                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-minus" onclick="updateRestaurantGuestCount(-1)" disabled>-</button>
                                                    <span id="restaurant-people-count" class="px-2">2</span>
                                                    <button type="button" class="btn btn-sm btn-outline-primary btn-plus" onclick="updateRestaurantGuestCount(1)">+</button>
                                                </div>
                                            </div>
                                            
                                            <div class="guest-dropdown-footer">
                                                <button type="button" class="btn btn-sm btn-link text-muted" onclick="closeRestaurantGuestDropdown()">Cancel</button>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="applyRestaurantGuestSelection()">Apply</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="fas fa-search me-2"></i>Find Tables
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Tours Tab -->
                <div class="tab-pane fade" id="tours" role="tabpanel" aria-labelledby="tours-tab">
                    <form action="search.php" method="GET" class="search-form">
                        <input type="hidden" name="type" value="tours">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tour-destination" class="form-label fw-500 text-muted mb-1">Destination</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                        <select class="form-select border-start-0 ps-1" id="tour-destination" name="destination" required>
                                            <option value="">Where to?</option>
                                            <option value="puri">Puri, Odisha</option>
                                            <option value="bhubaneswar">Bhubaneswar, Odisha</option>
                                            <option value="konark">Konark, Odisha</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tour-date" class="form-label fw-500 text-muted mb-1">Tour Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-primary"></i></span>
                                        <input type="date" class="form-control border-start-0 ps-1" id="tour-date" name="tour_date" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tour-type" class="form-label fw-500 text-muted mb-1">Tour Type</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-tag text-primary"></i></span>
                                        <select class="form-select border-start-0 ps-1" id="tour-type" name="tour_type">
                                            <option value="">Any Type</option>
                                            <option value="cultural">Cultural</option>
                                            <option value="adventure">Adventure</option>
                                            <option value="heritage">Heritage</option>
                                            <option value="nature">Nature</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="fas fa-search me-2"></i>Find Tours
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>




<section class="offers-section">
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
    <ul class="nav nav-tabs" id="offerTabs" role="tablist" style="flex-wrap: nowrap;overflow-x: scroll;overflow-y: hidden;">
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
                <div class="swiper offers-swiper" id="offersSwiperHotels">
                    <div class="swiper-wrapper">
                        <!-- Card 1 -->
                        <div class="offer-card swiper-slide">
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
                        <div class="offer-card swiper-slide">
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
                        <div class="offer-card swiper-slide">
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
                        <div class="offer-card swiper-slide">
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
                        <div class="offer-card swiper-slide">
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
                        <div class="offer-card swiper-slide">
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
                    <div class="swiper-pagination d-md-none"></div>
                </div>
            </div>
        </div>

        <!-- Other tabs content -->
        <div class="tab-pane fade" id="all-offers" role="tabpanel" aria-labelledby="all-offers-tab">
            <div class="offers-slider">
                <div class="swiper offers-swiper" id="offersSwiperAll">
                    <div class="swiper-wrapper">
                        <!-- Sample cards for other tabs -->
                        <div class="offer-card swiper-slide">
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
                    <div class="swiper-pagination d-md-none"></div>
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




<!-- slider with contner  -->



<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="Too-Yumm.jpg" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="Too-Yumm.jpg" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="Too-Yumm.jpg" class="d-block w-100" alt="...">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>



<!-- Top Destinations Section -->
<section class="top-destinations py-5 bg-light">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <div class="section-title">
                <h2 class="fw-bold mb-1">Top Destinations</h2>
                <p class="text-muted mb-0">Explore our most popular destinations</p>
            </div>
            <div class="slider-nav d-none d-md-flex">
                <button class="btn btn-sm btn-outline-secondary me-2 destination-prev">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary destination-next">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
        
        <!-- Swiper -->
        <div class="position-relative">
            <div class="swiper destinations-swiper">
                <div class="swiper-wrapper">
                    <!-- Destination 1 -->
                    
                    <!-- Destination 2 -->
                    <div class="swiper-slide">
                        <div class="destination-card h-100">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="position-relative">
                                    <img src="images.jpeg" class="card-img-top" alt="Destination 2">
                                    <div class="price-tag">
                                        <span class="badge bg-primary">From ₹2,999</span>
                                    </div>
                                    <div class="rating-badge">
                                        <span class="badge bg-success">4.7 ★</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-1">Kerala Backwaters</h5>
                                    <p class="text-muted small mb-2">4 Days, 3 Nights</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="location">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                            <span>Kerala, India</span>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Destination 3 -->
                    <div class="swiper-slide">
                        <div class="destination-card h-100">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="position-relative">
                                    <img src="images.jpeg" class="card-img-top" alt="Destination 3">
                                    <div class="price-tag">
                                        <span class="badge bg-primary">From ₹3,499</span>
                                    </div>
                                    <div class="rating-badge">
                                        <span class="badge bg-success">4.8 ★</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-1">Manali Hills</h5>
                                    <p class="text-muted small mb-2">5 Days, 4 Nights</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="location">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                            <span>Himachal Pradesh, India</span>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Destination 4 -->
                    <div class="swiper-slide">
                        <div class="destination-card h-100">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="position-relative">
                                    <img src="images.jpeg" class="card-img-top" alt="Destination 4">
                                    <div class="price-tag">
                                        <span class="badge bg-primary">From ₹2,799</span>
                                    </div>
                                    <div class="rating-badge">
                                        <span class="badge bg-success">4.6 ★</span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title mb-1">Rajasthan Forts</h5>
                                    <p class="text-muted small mb-2">6 Days, 5 Nights</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="location">
                                            <i class="fas fa-map-marker-alt text-primary"></i>
                                            <span>Rajasthan, India</span>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-outline-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add more destinations here if needed -->
                </div>
                <!-- Mobile Pagination -->
                <div class="swiper-pagination d-md-none mt-3"></div>
            </div>
        </div>
        
       
    </div>
</section>

<div class="container my-5">
        <h2 style="font-size: 15px;">Stay in Puri – Hotel Booking</h2>
        <p style="font-size: 10px;"><strong>Hotels in Puri | Best Hotels in Puri | Beachside Hotels in Puri | Budget Hotels in Puri | 5-Star Hotels in Puri | Hotels Near Jagannath Temple | Puri Hotel Booking Online | Puri Hotels with Sea View</strong></p>

        <h4 class="mt-4" style="font-size: 15px;">Top Hotel Categories in Puri</h4>
        <ul style="display: flex; flex-wrap: wrap; font-size: 10px;">
            <li>Hotels Near Jagannath Temple</li>
            <li>Beach Resorts in Puri</li>
            <li>Budget Hotels in Puri</li>
            <li>3-Star Hotels in Puri</li>
            <li>5-Star Hotels in Puri</li>
            <li>Family Hotels in Puri</li>
            <li>Hotels for Pilgrims in Puri</li>
            <li>Hotels Near Puri Railway Station</li>
            <li>Sea View Hotels in Puri</li>
            <li>Hotels with Swimming Pool in Puri</li>
        </ul>

        <h4 class="mt-4" style="font-size: 15px;">Popular Areas to Stay in Puri</h4>
        <ul style="display: flex; flex-wrap: wrap; font-size: 10px;">
            <li>Swargadwar</li>
            <li>Chakra Tirtha Road</li>
            <li>Grand Road</li>
            <li>Baliapanda</li>
            <li>VIP Road</li>
            <li>Marine Drive</li>
            <li>New Marine Drive Road</li>
            <li>Near Jagannath Temple</li>
            <li>Station Road</li>
            <li>Near Gundicha Temple</li>
        </ul>

        <h4 class="mt-4" style="font-size: 15px;">Top Hotels in Puri</h4>
        <ul style="display: flex; flex-wrap: wrap;font-size: 10px;">
            <li>Mayfair Heritage Puri</li>
            <li>Toshali Sands Nature Escape</li>
            <li>Sterling Puri</li>
            <li>Hotel Holiday Resort</li>
            <li>Pride Ananya Resort</li>
            <li>Hotel Sonar Bangla</li>
            <li>Hotel Gandhara</li>
            <li>Mahodadhi Palace</li>
            <li>Hotel Dreamland</li>
            <li>Hotel Golden Tree</li>
            <li>Blue Lily Beach Resort</li>
            <li>Chanakya BNR Hotel</li>
            <li>Hotel Shreehari Grand</li>
            <li>Lucky India Royal Heritage</li>
            <li>Victoria Club Hotel</li>
            <li>Reba Beach Resort</li>
            <li>Hotel Jeevan Sandhya</li>
        </ul>

        <h4 class="mt-4" style="font-size: 15px;">Why Book Hotels in Puri With Us?</h4>
        <ul style="display: flex; flex-wrap: wrap; font-size: 10px;">
            <li>Wide Range of Hotels – Budget to Luxury</li>
            <li>Verified Guest Reviews</li>
            <li>Best Price Guarantee</li>
            <li>Easy Booking & Instant Confirmation</li>
            <li>Special Discounts on Festival Dates</li>
            <li>24x7 Customer Support</li>
            <li>No Hidden Charges</li>
        </ul>

        <h4 class="mt-4" style="font-size: 15px;">Experiences in Puri</h4>
        <ul style="display: flex; flex-wrap: wrap; font-size: 10px;">
            <li>Visit the Jagannath Temple</li>
            <li>Enjoy a peaceful morning at Puri Beach</li>
            <li>Attend Rath Yatra Festival</li>
            <li>Explore Raghurajpur Artist Village</li>
            <li>Take a short trip to Konark Sun Temple</li>
            <li>Enjoy local delicacies like Khaja and Mahaprasad</li>
        </ul>

        <h4 class="mt-4" style="font-size: 15px;">Related Destinations Near Puri</h4>
        <ul style="display: flex; flex-wrap: wrap; font-size: 10px;">
            <li>Hotels in Bhubaneswar</li>
            <li>Hotels in Konark</li>
            <li>Hotels in Chilika</li>
            <li>Hotels in Gopalpur</li>
            <li>Hotels in Satapada</li>
            <li>Hotels in Cuttack</li>
        </ul>

        <h4 class="mt-4" style="font-size: 15px;">Corporate & Group Bookings</h4>
        <p style="font-size: 10px;">Looking for <strong>bulk hotel bookings in Puri</strong> for weddings, pilgrim tours, school/college trips, or corporate stays? Get the best group discounts and end-to-end support. Reach out to our travel desk for customized packages.</p>
    </div>



<?php include 'includes/footer.php'; ?>