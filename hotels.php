<?php
// DB connection
require_once __DIR__ . '/cbs/config/config.php';
include 'includes/header.php';

// Fetch active rooms with their hotel and primary image (no city filter)
$rooms = [];
$today = date('Y-m-d');

// Build amenities title -> icon map
$amenitiesMap = [];
$amenitiesSql = "SELECT title, icon FROM amenities";
if ($amenitiesResult = $conn->query($amenitiesSql)) {
    while ($a = $amenitiesResult->fetch_assoc()) {
        $key = strtolower(trim($a['title']));
        if ($key !== '') {
            $amenitiesMap[$key] = $a['icon'];
        }
    }
    $amenitiesResult->free();
}

$sql = "
    SELECT r.id AS room_id,
           r.name AS room_name,
           r.description,
           r.max_capacity,
           r.amenities,
           h.id AS hotel_id,
           h.name AS hotel_name,
           h.location AS hotel_location,
           tp.price AS current_price,
           tp.discount_price AS current_discount_price,
           (
             SELECT ri.image_path
             FROM room_images ri
             WHERE ri.room_id = r.id
               AND ri.is_primary = 1
             ORDER BY ri.id ASC
             LIMIT 1
           ) AS primary_image,
           (
             SELECT ri2.image_path
             FROM room_images ri2
             WHERE ri2.room_id = r.id
             ORDER BY ri2.id ASC
             LIMIT 1
           ) AS any_image
    FROM rooms r
    INNER JOIN hotels h ON h.id = r.hotel_id
    LEFT JOIN (
        SELECT rt1.*
        FROM room_tariffs rt1
        INNER JOIN (
            SELECT room_id, MAX(id) AS max_id
            FROM room_tariffs
            WHERE start_date <= '$today' AND end_date >= '$today'
            GROUP BY room_id
        ) latest ON latest.room_id = rt1.room_id AND latest.max_id = rt1.id
    ) tp ON tp.room_id = r.id
    ORDER BY r.id DESC
";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        // Fallback to any image if no explicit primary
        $row['display_image'] = !empty($row['primary_image']) ? $row['primary_image'] : $row['any_image'];
        $rooms[] = $row;
    }
    $result->free();
} else {
    // Optional: uncomment to debug SQL errors during development
    // echo '<div class="alert alert-warning">SQL Error: ' . htmlspecialchars($conn->error) . '</div>';
}
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
            <h1 class="results-title"><?php echo count($rooms); ?> Rooms found</h1>
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

                <div class="results-header">Showing Rooms</div>

                <?php if (empty($rooms)): ?>
                    <div class="alert alert-light" role="alert" style="border: 1px solid #eee;">
                        No rooms found.
                    </div>
                <?php else: ?>
                    <?php foreach ($rooms as $room): ?>
                        <div class="hotel-card">
                            <div class="card-content">
                                <div class="hotel-images">
                                    <div class="main-image">
                                        <?php
                                            $img = !empty($room['display_image']) ? 'cbs/' . $room['display_image'] : 'assets/img/stay-in-puri.png';
                                            $alt = htmlspecialchars($room['room_name']);
                                        ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo $alt; ?>">
                                    </div>
                                </div>
                                <div class="hotel-info">
                                    <div class="hotel-name"><?php echo htmlspecialchars($room['room_name']); ?></div>
                                    <div class="hotel-location"><?php echo htmlspecialchars($room['hotel_name']); ?><?php echo !empty($room['hotel_location']) ? ' | ' . htmlspecialchars($room['hotel_location']) : ''; ?></div>
                                    <?php if (!empty($room['amenities'])): ?>
                                        <div class="hotel-tags">
                                            <?php foreach (explode(',', $room['amenities']) as $tag): ?>
                                                <?php
                                                    $tag = trim($tag);
                                                    if ($tag==='') continue;
                                                    $lookup = strtolower($tag);
                                                    $iconClass = isset($amenitiesMap[$lookup]) ? trim($amenitiesMap[$lookup]) : '';
                                                ?>
                                                <span class="tag">
                                                    <?php if ($iconClass !== ''): ?>
                                                        <i class="<?php echo htmlspecialchars($iconClass); ?>" aria-hidden="true"></i>
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($tag); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <ul class="hotel-features">
                                        <li>Max capacity: <?php echo (int)$room['max_capacity']; ?></li>
                                    </ul>
                                    <?php if (!empty($room['description'])): ?>
                                        <div class="hotel-description" style="color:#666; font-size: 0.95rem; margin-top:6px;">
                                            <?php
                                                $desc = trim(strip_tags((string)$room['description']));
                                                $maxLen = 160;
                                                if (mb_strlen($desc) > $maxLen) {
                                                    $desc = mb_substr($desc, 0, $maxLen - 1) . '…';
                                                }
                                                echo htmlspecialchars($desc);
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="hotel-pricing">
                                    <div class="price-section">
                                        <?php
                                            $hasDiscount = isset($room['current_discount_price']) && $room['current_discount_price'] !== null && $room['current_discount_price'] !== '';
                                            $hasPrice = isset($room['current_price']) && $room['current_price'] !== null && $room['current_price'] !== '';
                                            if ($hasDiscount || $hasPrice):
                                                $display = $hasDiscount ? (float)$room['current_discount_price'] : (float)$room['current_price'];
                                                $original = ($hasDiscount && $hasPrice) ? (float)$room['current_price'] : null;
                                        ?>
                                                <?php if ($original): ?>
                                                    <div class="original-price" style="text-decoration: line-through; color:#888;">₹<?php echo number_format($original, 0); ?></div>
                                                <?php endif; ?>
                                                <div class="current-price">₹<?php echo number_format($display, 0); ?></div>
                                                <div class="price-note">Per Night</div>
                                        <?php else: ?>
                                                <div class="current-price">Request Price</div>
                                                <div class="price-note">Per Night</div>
                                        <?php endif; ?>
                                    </div>
                                    <a class="book-button" href="contact.php">Enquire</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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
