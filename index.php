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

<style>
.hero-swiper {
    height: 70vh;
    min-height: 400px;
    width: 100%;
}
.hero-slide {
    height: 100%;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    position: relative;
}
.hero-slide:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0);
}
.hero-slide .container {
    position: relative;
    z-index: 1;
    color: white;
    max-width: 600px;
}
.hero-slide h2 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}
@media (max-width: 768px) {
    .hero-swiper {
        height: 50vh;
    }
    .hero-slide h2 {
        font-size: 1.8rem;
    }
}
</style>

<!-- Include jQuery and daterangepicker -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
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

// Initialize Swiper
$(document).ready(function() {
    // Initialize Hero Swiper
    new Swiper('.hero-swiper', {
        loop: true,
        autoplay: { delay: 5000 },
        pagination: { el: '.swiper-pagination', clickable: true },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        effect: 'fade',
        speed: 1000,
        grabCursor: true
    });

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
</script>


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
            
            <div class="tab-content bg-white p-4 rounded-3 shadow-sm" id="searchTabContent">
                <!-- Hotels Tab -->
                <div class="tab-pane fade show active" id="hotels" role="tabpanel" aria-labelledby="hotels-tab">
                    <form action="search.php" method="GET" class="search-form">
                        <input type="hidden" name="type" value="hotels">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
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
                            
                            <div class="col-md-4">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="restaurant-location" class="form-label fw-500 text-muted mb-1">Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                        <input type="text" class="form-control border-start-0 ps-1" id="restaurant-location" placeholder="Search by location or cuisine" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="restaurant-date" class="form-label fw-500 text-muted mb-1">Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="far fa-calendar-alt text-primary"></i></span>
                                        <input type="date" class="form-control border-start-0 ps-1" id="restaurant-date">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                                    <i class="fas fa-search me-2"></i>Find
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

<style>
/* Guest Selector Styles */
.guest-selector-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 5px;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.guest-option {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.guest-option:last-child {
    border-bottom: none;
}

.guest-label {
    flex: 1;
}

.guest-label span {
    display: block;
    font-weight: 500;
    color: #333;
}

.guest-label small {
    font-size: 12px;
    color: #6c757d;
}

.guest-counter {
    display: flex;
    align-items: center;
}

.guest-counter button {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    font-size: 14px;
    line-height: 1;
}

.guest-counter span {
    min-width: 30px;
    text-align: center;
    font-weight: 500;
}

.guest-dropdown-footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid #f0f0f0;
}

.guest-dropdown-footer .btn {
    margin-left: 10px;
    padding: 5px 15px;
}

/* Search Section Styles */
.search-section {
    position: relative;
    z-index: 10;
    margin-top: -157px;
    border-radius: 10px;
}

.search-tabs .nav-pills {
    background: #f8f9fa;
    padding: 8px;
    border-radius: 50px;
    display: inline-flex;
}

.search-tabs .nav-link {
    color: #495057;
    border-radius: 50px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    margin: 0 2px;
}

.search-tabs .nav-link.active {
    background: #0d6efd;
    color: white;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
}

.search-tabs .nav-link i {
    margin-right: 8px;
}

.tab-content {
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
}

.form-label {
    font-size: 0.85rem;
    font-weight: 500;
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.input-group-text {
    border-right: none;
    background: transparent;
}

.form-control, .form-select {
    border-left: none;
    padding-left: 0.5rem;
}

.form-control:focus, .form-select:focus {
    box-shadow: none;
    border-color: #dee2e6;
}

.btn-primary {
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.3s ease;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .search-tabs .nav-pills {
        width: 100%;
        overflow-x: auto;
        flex-wrap: nowrap;
        justify-content: flex-start;
        padding: 6px;
    }
    
    .search-tabs .nav-link {
        white-space: nowrap;
        padding: 8px 15px;
        font-size: 0.9rem;
    }
    
    .search-section {
        margin-top: -15px;
    }
}

@media (max-width: 767.98px) {
    .form-group {
        margin-bottom: 1rem;
    }
    
    .search-section {
        margin: 0 -15px;
        border-radius: 0;
    }
    
    .tab-content {
        padding: 15px !important;
    }
    
    .search-tabs .nav-pills {
        border-radius: 8px;
    }
}
</style>





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

<style>
/* Destination Slider Styles */
.destination-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    height: 100%;
}

.destination-card:hover {
    transform: translateY(-5px);
}

.destination-content {
    border-radius: 0 0 0.25rem 0.25rem;
    transition: all 0.3s ease;
}

.destination-card .card {
    overflow: hidden;
}

.destination-card img {
    transition: transform 0.5s ease;
}

.destination-card:hover img {
    transform: scale(1.05);
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .section-header {
        margin-bottom: 0.5rem !important;
    }
    
    .destination-card {
        margin-bottom: 1rem;
    }
}
</style>

<!-- Initialize Destination Swiper -->
<script>
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
</script>

<!-- Add this to your CSS file -->
<style>
.top-destinations {
    padding: 60px 0;
    position: relative;
}

.destinations-swiper {
    padding: 20px 0 40px;
}

.destination-card {
    transition: transform 0.3s ease;
    margin-bottom: 20px;
    height: 100%;
}

.destination-card:hover {
    transform: translateY(-5px);
}

.card {
    border-radius: 10px;
    overflow: hidden;
    height: 100%;
    margin: 0 5px;
}

.card-img-top {
    height: 200px;
    width: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.destination-card:hover .card-img-top {
    transform: scale(1.05);
}

.price-tag {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 2;
}

.rating-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 2;
}

.card-body {
    padding: 1.25rem;
}

.card-title {
    font-weight: 600;
    color: #333;
}

.location {
    font-size: 0.9rem;
    color: #666;
}

.location i {
    margin-right: 5px;
}

.btn-outline-primary {
    border-width: 1px;
    font-size: 0.8rem;
    padding: 0.25rem 0.75rem;
}

.section-header h2 {
    position: relative;
    display: inline-block;
    margin-bottom: 15px;
}

.section-header h2:after {
    content: '';
    position: absolute;
    width: 50px;
    height: 3px;
    background: var(--bs-primary);
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
}

/* Swiper Navigation */
.swiper-button-next,
.swiper-button-prev {
    color: var(--bs-primary);
    background: rgba(255, 255, 255, 0.8);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.swiper-button-next:after,
.swiper-button-prev:after {
    font-size: 1.2rem;
    font-weight: bold;
}

.swiper-button-next:hover,
.swiper-button-prev:hover {
    background: var(--bs-primary);
    color: white;
}

/* Pagination */
.swiper-pagination-bullet {
    width: 10px;
    height: 10px;
    background: #ccc;
    opacity: 1;
}

.swiper-pagination-bullet-active {
    background: var(--bs-primary);
    transform: scale(1.2);
}
</style>

<?php include 'includes/footer.php'; ?>