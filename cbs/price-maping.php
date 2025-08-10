<!DOCTYPE html>
<html lang="en">

<head>
  <?= require("./config/meta.php") ?>
  <style>
    .price-input {
      max-width: 120px;
    }
    .save-btn {
      width: 80px;
    }
    .price-row:hover {
      background-color: #f8f9fa;
    }
    .price-saved {
      animation: flash-success 1s;
    }
    @keyframes flash-success {
      0% { background-color: #d1e7dd; }
      100% { background-color: transparent; }
    }
    .tooltip-help {
      cursor: help;
    }
  </style>
</head>

<body>
  <?= require("./config/header.php") ?>
  <?= require("./config/menu.php") ?>

  <?php
  require('./config/config.php');

  // Check if user is logged in
  if (!isset($_SESSION['username'])) {
    header('Location: login');
    exit();
  }

  // Fetch all hotels for dropdown
  $hotels_sql = "SELECT id, name FROM hotels ORDER BY name ASC";
  $hotels_result = $conn->query($hotels_sql);
  $hotels = array();
  if ($hotels_result->num_rows > 0) {
    while ($hotel = $hotels_result->fetch_assoc()) {
      $hotels[$hotel['id']] = $hotel['name'];
    }
  }

  // Get selected hotel ID from filter
  $filter_hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
  
  // Get specific room ID if provided
  $filter_room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
  
  // If we have a room_id, get its hotel_id to set the filter
  if ($filter_room_id > 0 && $filter_hotel_id == 0) {
    $room_sql = "SELECT hotel_id FROM rooms WHERE id = ?";
    $room_stmt = $conn->prepare($room_sql);
    $room_stmt->bind_param("i", $filter_room_id);
    $room_stmt->execute();
    $room_result = $room_stmt->get_result();
    
    if ($room_result->num_rows > 0) {
      $filter_hotel_id = $room_result->fetch_assoc()['hotel_id'];
    }
  }

  // Get search query
  $search = isset($_GET['search']) ? trim($_GET['search']) : '';

  // Initialize rooms array
  $rooms = array();

  // Prepare base query to fetch rooms with their prices and hotel name
  $base_sql = "SELECT r.*, h.name as hotel_name, 
              COALESCE(rp.id, 0) as price_id, 
              COALESCE(rp.main_price, 0.00) as main_price, 
              COALESCE(rp.discount_price, 0.00) as discount_price, 
              COALESCE(rp.cp, 0.00) as cp, 
              COALESCE(rp.map, 0.00) as map, 
              COALESCE(rp.mvp, 0.00) as mvp 
              FROM rooms r 
              JOIN hotels h ON r.hotel_id = h.id 
              LEFT JOIN room_prices rp ON r.id = rp.room_id";

  // Apply filters
  if ($filter_hotel_id > 0 && !empty($search)) {
    // Both hotel filter and search
    $search_term = '%' . $search . '%';
    $sql = $base_sql . " WHERE r.hotel_id = ? AND (r.name LIKE ? OR h.name LIKE ?) ORDER BY h.name ASC, r.name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iss', $filter_hotel_id, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
  } else if ($filter_hotel_id > 0) {
    // Only hotel filter
    $sql = $base_sql . " WHERE r.hotel_id = ? ORDER BY r.name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $filter_hotel_id);
    $stmt->execute();
    $result = $stmt->get_result();
  } else if (!empty($search)) {
    // Only search
    $search_term = '%' . $search . '%';
    $sql = $base_sql . " WHERE r.name LIKE ? OR h.name LIKE ? ORDER BY h.name ASC, r.name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
  } else {
    // No filters
    $sql = $base_sql . " ORDER BY h.name ASC, r.name ASC";
    $result = $conn->query($sql);
  }

  // Fetch rooms with their prices
  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $rooms[] = $row;
    }
  }
  ?>

  <main id="main" class="main">
    <div class="pagetitle" style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h1>Price Mapping</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Price Mapping</li>
            <?php if ($filter_hotel_id > 0 && isset($hotels[$filter_hotel_id])): ?>
              <li class="breadcrumb-item active"><?= htmlspecialchars($hotels[$filter_hotel_id]) ?></li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Room Price Management</h5>
                
                <!-- Filter and Search -->
                <div class="d-flex gap-2">
                  <!-- Search Form -->
                  <form action="" method="GET" class="d-flex">
                    <?php if ($filter_hotel_id > 0): ?>
                      <input type="hidden" name="hotel_id" value="<?= $filter_hotel_id ?>">
                    <?php endif; ?>
                    <div class="input-group">
                      <input type="text" class="form-control" placeholder="Search rooms..." name="search" value="<?= htmlspecialchars($search) ?>">
                      <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                      <?php if(!empty($search)): ?>
                        <?php if ($filter_hotel_id > 0): ?>
                          <a href="price-maping.php?hotel_id=<?= $filter_hotel_id ?>" class="btn btn-outline-secondary">Clear</a>
                        <?php else: ?>
                          <a href="price-maping.php" class="btn btn-outline-secondary">Clear</a>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>
                  </form>
                  
                  <!-- Hotel Filter Dropdown -->
                  <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="hotelFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi bi-funnel"></i> 
                      <?= ($filter_hotel_id > 0 && isset($hotels[$filter_hotel_id])) ? 'Hotel: ' . htmlspecialchars(substr($hotels[$filter_hotel_id], 0, 15)) . (strlen($hotels[$filter_hotel_id]) > 15 ? '...' : '') : 'Filter by Hotel' ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="hotelFilterDropdown">
                      <li><a class="dropdown-item <?= ($filter_hotel_id == 0) ? 'active' : '' ?>" href="price-maping.php<?= !empty($search) ? '?search='.urlencode($search) : '' ?>">All Hotels</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <?php foreach($hotels as $id => $name): ?>
                      <li>
                        <a class="dropdown-item <?= ($filter_hotel_id == $id) ? 'active' : '' ?>" 
                           href="price-maping.php?hotel_id=<?= $id ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>">
                          <?= htmlspecialchars($name) ?>
                        </a>
                      </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
              </div>
              
              <!-- Search results info -->
              <?php if(!empty($search) || $filter_hotel_id > 0): ?>
              <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                <?php if(!empty($search) && $filter_hotel_id > 0): ?>
                  Showing search results for "<strong><?= htmlspecialchars($search) ?></strong>" 
                  in hotel "<strong><?= htmlspecialchars($hotels[$filter_hotel_id]) ?></strong>"
                <?php elseif(!empty($search)): ?>
                  Showing search results for "<strong><?= htmlspecialchars($search) ?></strong>"
                <?php else: ?>
                  Showing rooms for hotel "<strong><?= htmlspecialchars($hotels[$filter_hotel_id]) ?></strong>"
                <?php endif; ?>
                <span class="badge bg-secondary ms-2"><?= count($rooms) ?> rooms</span>
              </div>
              <?php endif; ?>
              
              <!-- Legend for price types -->
             
              
              <!-- Rooms and Pricing Table -->
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Hotel</th>
                      <th>Room</th>
                      <th>Main Price <i class="bi bi-info-circle-fill tooltip-help" title="Regular price"></i></th>
                      <th>Discount Price <i class="bi bi-info-circle-fill tooltip-help" title="Special offer price"></i></th>
                      <th>CP <i class="bi bi-info-circle-fill tooltip-help" title="Cost Price - Your actual cost"></i></th>
                      <th>MAP <i class="bi bi-info-circle-fill tooltip-help" title="Minimum Advertised Price"></i></th>
                      <th>MVP <i class="bi bi-info-circle-fill tooltip-help" title="Minimum Viable Price"></i></th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($rooms) > 0): ?>
                      <?php foreach ($rooms as $room): ?>
                        <tr class="price-row" id="room-row-<?= $room['id'] ?>">
                          <td><?= htmlspecialchars($room['hotel_name']) ?></td>
                          <td><?= htmlspecialchars($room['name']) ?></td>
                          <td>
                            <div class="input-group input-group-sm">
                              <span class="input-group-text">₹</span>
                              <input type="number" min="0" step="0.01" class="form-control price-input" 
                                    id="main-price-<?= $room['id'] ?>" 
                                    value="<?= number_format((float)$room['main_price'], 2, '.', '') ?>">
                            </div>
                          </td>
                          <td>
                            <div class="input-group input-group-sm">
                              <span class="input-group-text">₹</span>
                              <input type="number" min="0" step="0.01" class="form-control price-input" 
                                    id="discount-price-<?= $room['id'] ?>" 
                                    value="<?= number_format((float)$room['discount_price'], 2, '.', '') ?>">
                            </div>
                          </td>
                          <td>
                            <div class="input-group input-group-sm">
                              <span class="input-group-text">₹</span>
                              <input type="number" min="0" step="0.01" class="form-control price-input" 
                                    id="cp-<?= $room['id'] ?>" 
                                    value="<?= number_format((float)$room['cp'], 2, '.', '') ?>">
                            </div>
                          </td>
                          <td>
                            <div class="input-group input-group-sm">
                              <span class="input-group-text">₹</span>
                              <input type="number" min="0" step="0.01" class="form-control price-input" 
                                    id="map-<?= $room['id'] ?>" 
                                    value="<?= number_format((float)$room['map'], 2, '.', '') ?>">
                            </div>
                          </td>
                          <td>
                            <div class="input-group input-group-sm">
                              <span class="input-group-text">₹</span>
                              <input type="number" min="0" step="0.01" class="form-control price-input" 
                                    id="mvp-<?= $room['id'] ?>" 
                                    value="<?= number_format((float)$room['mvp'], 2, '.', '') ?>">
                            </div>
                          </td>
                          <td>
                            <button class="btn btn-primary btn-sm save-btn" 
                                   onclick="saveRoomPrices(<?= $room['id'] ?>, <?= $room['price_id'] ?>)">
                              <i class="bi bi-save"></i> Save
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="8" class="text-center py-4">
                          <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle"></i> No rooms found.
                            <?php if ($filter_hotel_id > 0 || !empty($search)): ?>
                              Try clearing your filters or adding rooms first.
                            <?php else: ?>
                              Please add rooms in the <a href="room-listing.php" class="alert-link">Room Listing</a> page first.
                            <?php endif; ?>
                          </div>
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <?= require("./config/footer.php") ?>
  
  <!-- Toast Container for notifications -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-check-circle me-2"></i> <span id="successToastMessage">Price saved successfully!</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
    
    <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-exclamation-triangle me-2"></i> <span id="errorToastMessage">Error saving price.</span>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>

  <script>
    // Initialize tooltips and toasts
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize tooltips
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
      });
      
      // Initialize toasts
      var toastElList = [].slice.call(document.querySelectorAll('.toast'))
      var toastList = toastElList.map(function(toastEl) {
        return new bootstrap.Toast(toastEl, { delay: 3000 })
      });
    });
    
    // Save room prices function
    function saveRoomPrices(roomId, priceId) {
      const mainPrice = document.getElementById(`main-price-${roomId}`).value;
      const discountPrice = document.getElementById(`discount-price-${roomId}`).value;
      const cp = document.getElementById(`cp-${roomId}`).value;
      const map = document.getElementById(`map-${roomId}`).value;
      const mvp = document.getElementById(`mvp-${roomId}`).value;
      
      // Create form data
      const formData = new FormData();
      formData.append('room_id', roomId);
      formData.append('price_id', priceId);
      formData.append('main_price', mainPrice);
      formData.append('discount_price', discountPrice);
      formData.append('cp', cp);
      formData.append('map', map);
      formData.append('mvp', mvp);
      formData.append('save_room_prices', 1);
      
      // Send AJAX request
      fetch('process/prices.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin' // Include cookies in the request
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Flash the row with success color
          const row = document.getElementById(`room-row-${roomId}`);
          row.classList.add('price-saved');
          setTimeout(() => {
            row.classList.remove('price-saved');
          }, 1500);
          
          // Update the price_id if it was an insert
          if (priceId === 0 && data.price_id) {
            // Update the onclick attribute with the new price_id
            document.querySelector(`#room-row-${roomId} button.save-btn`).setAttribute(
              'onclick', `saveRoomPrices(${roomId}, ${data.price_id})`
            );
          }
          
          // Show success toast message
          const roomName = document.querySelector(`#room-row-${roomId} td:nth-child(2)`).textContent;
          document.getElementById('successToastMessage').textContent = `Price for "${roomName}" saved successfully!`;
          bootstrap.Toast.getOrCreateInstance(document.getElementById('successToast')).show();
        } else {
          // Show error toast message
          document.getElementById('errorToastMessage').textContent = `Error: ${data.message}`;
          bootstrap.Toast.getOrCreateInstance(document.getElementById('errorToast')).show();
        }
      })
      .catch(error => {
        console.error('Error saving room prices:', error);
        // Show network error toast message
        document.getElementById('errorToastMessage').textContent = 'Network error occurred. Please try again.';
        bootstrap.Toast.getOrCreateInstance(document.getElementById('errorToast')).show();
      });
    }
  </script>
</body>

</html>