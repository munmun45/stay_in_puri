<?php
include 'includes/header.php';
?>

<br>

<style>
.rating-badge{
    position: unset;
}
    
    </style>

<!-- Room Listing Section -->
<div class="container">
        <!-- Header Search -->
        <div class="search-header">
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
        </div>

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="#">Home</a> › Hotels and more in Goa
        </div>

        <!-- Header Info -->
        <div class="header-info">
            <h1 class="results-title">2845 Properties in Goa</h1>
            <div class="explore-tips">Explore Travel Tips →</div>
            <button type="button" class="mobile-filter-btn d-md-none" id="openFilters" aria-label="Open Filters">☰ Filters</button>
        </div>

        <!-- Mobile Filters Overlay / Drawer -->
        <div class="filters-overlay" id="filtersOverlay" aria-hidden="true">
            <div class="filters-drawer" role="dialog" aria-modal="true" aria-labelledby="filtersTitle">
                <div class="filters-header">
                    <span id="filtersTitle">Filters</span>
                    <button type="button" class="filters-close" id="closeFilters" aria-label="Close">✕</button>
                </div>
                <div class="filters-content">
                    <!-- Duplicate of sidebar filters for mobile -->
                    <div class="filter-section">
                        <div class="filter-title">Suggested For You</div>
                        <div class="filter-option">
                            <label><input type="checkbox"> Last Minute Deals</label>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> 5 Star</label>
                            <span class="filter-count">(198)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> North Goa</label>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> Resorts</label>
                            <span class="filter-count">(353)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> Unmarried Couples Allowed</label>
                            <span class="filter-count">(2536)</span>
                        </div>
                        <div class="show-more">Show 7 more</div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-title">Price per night</div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹0 - ₹2000</label>
                            <span class="filter-count">(889)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹2000 - ₹4000</label>
                            <span class="filter-count">(777)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹4000 - ₹6000</label>
                            <span class="filter-count">(280)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹6000 - ₹9000</label>
                            <span class="filter-count">(211)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹9000 - ₹12000</label>
                            <span class="filter-count">(104)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹12000 - ₹15000</label>
                            <span class="filter-count">(98)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹15000 - ₹30000</label>
                            <span class="filter-count">(219)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹30000+</label>
                            <span class="filter-count">(114)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-layout">
            <!-- Sidebar -->
            <div>
                

                <!-- Filters -->
                <div class="sidebar">
                    <div class="filter-section">
                        <div class="filter-title">Suggested For You</div>
                        <div class="filter-option">
                            <label><input type="checkbox"> Last Minute Deals</label>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> 5 Star</label>
                            <span class="filter-count">(198)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> North Goa</label>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> Resorts</label>
                            <span class="filter-count">(353)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> Unmarried Couples Allowed</label>
                            <span class="filter-count">(2536)</span>
                        </div>
                        <div class="show-more">Show 7 more</div>
                    </div>

                    <div class="filter-section">
                        <div class="filter-title">Price per night</div>
                        
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹0 - ₹2000</label>
                            <span class="filter-count">(889)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹2000 - ₹4000</label>
                            <span class="filter-count">(777)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹4000 - ₹6000</label>
                            <span class="filter-count">(280)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹6000 - ₹9000</label>
                            <span class="filter-count">(211)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹9000 - ₹12000</label>
                            <span class="filter-count">(104)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹12000 - ₹15000</label>
                            <span class="filter-count">(98)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹15000 - ₹30000</label>
                            <span class="filter-count">(219)</span>
                        </div>
                        <div class="filter-option">
                            <label><input type="checkbox"> ₹30000+</label>
                            <span class="filter-count">(114)</span>
                        </div>
                    </div>

                    
                </div>
            </div>

            <!-- Results Section -->
            <div class="results-section">
                <!-- Sort Options -->
                <div class="sort-section">
                    <div class="sort-title">SORT BY</div>
                    <div class="sort-options">
                        <div class="sort-option active">Popular</div>
                        <div class="sort-option">User Rating (Highest First)</div>
                        <div class="sort-option">Price (Highest First)</div>
                        <div class="sort-option">Price (Lowest First)</div>
                    </div>
                </div>

                <div class="results-header">Showing Properties in Goa</div>

                <!-- Hotel Card 1 -->
                <div class="hotel-card">
                    <div class="card-content">
                        <div class="hotel-images">
                            <div class="main-image">
                                <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=400&h=300&fit=crop" alt="Ginger Goa">
                            </div>
                            <div class="thumbnail-grid">
                                <div class="thumbnail"><img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=60&h=72&fit=crop" alt="thumb1"></div>
                                <div class="thumbnail"><img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=60&h=72&fit=crop" alt="thumb2"></div>
                                <div class="thumbnail"><img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=60&h=72&fit=crop" alt="thumb3"></div>
                                <div class="thumbnail view-all">View All</div>
                            </div>
                        </div>
                        <div class="hotel-info">
                            <div class="hotel-name">Ginger Goa, Candolim</div>
                            <div class="hotel-stars">★★★★☆</div>
                            <div class="hotel-location">Candolim | 870 m drive to Candolim Beach</div>
                            <div class="hotel-tags">
                                <span class="tag">Couple Friendly</span>
                            </div>
                            <ul class="hotel-features">
                                <li>Free Cancellation till 24 hrs before check in</li>
                            </ul>
                            <div class="special-offer">Enjoy a Free Breakfast upgrade along with 20% off on F&B</div>
                        </div>
                        <div class="hotel-pricing">
                            <div class="rating-section">
                                <span class="rating-text">Excellent</span>
                                <div class="rating-badge excellent">4.4</div>
                            </div>
                            <div class="rating-count">(1252 Ratings)</div>
                            <div class="price-section">
                                <div class="current-price">₹2,999</div>
                                <div class="price-note">+ ₹360 taxes & fees<br>Per Night</div>
                            </div>
                            <button class="book-button">Book Now</button>
                        </div>
                    </div>
                </div>

                <!-- Hotel Card 2 -->
                <div class="hotel-card">
                    <div class="card-content">
                        <div class="hotel-images">
                            <div class="main-image">
                                <img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=400&h=300&fit=crop" alt="Bloom Boutique">
                            </div>
                            <div class="thumbnail-grid">
                                <div class="thumbnail"><img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=60&h=72&fit=crop" alt="thumb1"></div>
                                <div class="thumbnail"><img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=60&h=72&fit=crop" alt="thumb2"></div>
                                <div class="thumbnail"><img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=60&h=72&fit=crop" alt="thumb3"></div>
                                <div class="thumbnail view-all">View All</div>
                            </div>
                        </div>
                        <div class="hotel-info">
                            <div class="hotel-name">Bloom Boutique Baga</div>
                            <div class="hotel-stars">★★★☆☆</div>
                            <div class="hotel-location">Baga | 2.2 km drive to Baga Beach</div>
                            <div class="hotel-tags">
                                <span class="tag sponsored">SPONSORED</span>
                                <span class="tag">Couple Friendly</span>
                            </div>
                            <ul class="hotel-features">
                                <li>Free Cancellation till check-in</li>
                                <li>Complimentary Hi-Tea</li>
                            </ul>
                            <div style="font-size: 13px; color: #666; margin-top: 8px;">
                                💡 Ideal location near Baga Beach, clean modern rooms with rain showers, relaxing pool with views
                            </div>
                        </div>
                        <div class="hotel-pricing">
                            <div class="rating-section">
                                <span class="rating-text">Excellent</span>
                                <div class="rating-badge excellent">4.3</div>
                            </div>
                            <div class="rating-count">(119 Ratings)</div>
                            <div class="price-section">
                                <div class="original-price">₹3,500</div>
                                <div class="current-price">₹2,800</div>
                                <div class="price-note">+ ₹336 taxes & fees<br>Per Night</div>
                            </div>
                            <button class="book-button">Book Now</button>
                        </div>
                    </div>
                </div>

                <!-- Hotel Card 3 -->
                <div class="hotel-card">
                    <div class="card-content">
                        <div class="hotel-images">
                            <div class="main-image">
                                <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=400&h=300&fit=crop" alt="SinQ Beach Resort">
                            </div>
                            <div class="thumbnail-grid">
                                <div class="thumbnail"><img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=60&h=72&fit=crop" alt="thumb1"></div>
                                <div class="thumbnail"><img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=60&h=72&fit=crop" alt="thumb2"></div>
                                <div class="thumbnail"><img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?w=60&h=72&fit=crop" alt="thumb3"></div>
                                <div class="thumbnail view-all">View All</div>
                            </div>
                        </div>
                        <div class="hotel-info">
                            <div class="hotel-name">SinQ Beach Resort</div>
                            <div class="hotel-stars">★★★☆☆</div>
                            <div class="hotel-location">Calangute | 6 minutes walk to Calangute Beach</div>
                            <div class="hotel-tags">
                                <span class="tag">Couple Friendly</span>
                                <span class="tag" style="background: #ffe6e6; color: #d32f2f;">Limited Time Offer</span>
                            </div>
                            <ul class="hotel-features">
                                <li>Beach front location</li>
                                <li>Pool and spa facilities</li>
                            </ul>
                        </div>
                        <div class="hotel-pricing">
                            <div class="rating-section">
                                <span class="rating-text">Very Good</span>
                                <div class="rating-badge very-good">4.1</div>
                            </div>
                            <div class="rating-count">(5756 Ratings)</div>
                            <div class="price-section">
                                <div class="original-price">₹4,200</div>
                                <div class="current-price">₹3,600</div>
                                <div class="price-note">+ ₹432 taxes & fees<br>Per Night</div>
                            </div>
                            <button class="book-button">Book Now</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
    <br>
    <br>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const openBtn = document.getElementById('openFilters');
    const overlay = document.getElementById('filtersOverlay');
    const closeBtn = document.getElementById('closeFilters');
    if (!openBtn || !overlay || !closeBtn) return;

    function openDrawer() {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    openBtn.addEventListener('click', openDrawer);
    closeBtn.addEventListener('click', closeDrawer);
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            closeDrawer();
        }
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDrawer();
    });
});
</script>

<?php include 'includes/footer.php'; ?>
