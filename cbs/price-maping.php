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
    .tariff-row:hover {
      background-color: #f8f9fa;
    }
    .tariff-saved {
      animation: flash-success 1s;
    }
    @keyframes flash-success {
      0% { background-color: #d1e7dd; }
      100% { background-color: transparent; }
    }
    .tooltip-help {
      cursor: help;
    }
    .date-range-header {
      background-color: #f8f9fa;
      font-weight: bold;
    }
    .day-type-badge {
      font-size: 0.8rem;
      padding: 0.2rem 0.5rem;
      margin-left: 0.5rem;
    }
    .weekday-badge {
      background-color: #0d6efd;
    }
    .weekend-badge {
      background-color: #fd7e14;
    }
    .holiday-badge {
      background-color: #dc3545;
    }
    .all-badge {
      background-color: #20c997;
    }
  </style>
</head>

<body>
  <?= require("./config/header.php") ?>
  <?= require("./config/menu.php") ?>

  <?php
require_once "./config/config.php";

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login');
    exit();
}

// Create room_tariffs table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS `room_tariffs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `day_type` enum('weekday','weekend','holiday','all') NOT NULL DEFAULT 'all',
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `cp` decimal(10,2) DEFAULT NULL COMMENT 'Cost Price',
  `map` decimal(10,2) DEFAULT NULL COMMENT 'Minimum Advertised Price',
  `mvp` decimal(10,2) DEFAULT NULL COMMENT 'Minimum Viable Price',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  KEY `date_range` (`start_date`,`end_date`),
  KEY `day_type` (`day_type`),
  CONSTRAINT `room_tariffs_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$conn->query($create_table_sql);

// Get active hotels for filtering
$hotels_sql = "SELECT id, name FROM hotels WHERE is_active = 1 ORDER BY name";
$hotels_result = $conn->query($hotels_sql);
$hotels = array();
while ($hotel = $hotels_result->fetch_assoc()) {
    $hotels[$hotel['id']] = $hotel['name'];
}

// Filter variables
$filter_hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$selected_room_id = isset($_GET['manage_room']) ? (int)$_GET['manage_room'] : 0;

// Prepare base query to fetch rooms with their hotel name
$base_sql = "SELECT r.*, h.name as hotel_name 
            FROM rooms r 
            JOIN hotels h ON r.hotel_id = h.id 
            WHERE r.is_active = 1 AND h.is_active = 1";

// Apply filters
if ($filter_hotel_id > 0 && !empty($search)) {
  // Both hotel filter and search
  $search_term = '%' . $conn->real_escape_string($search) . '%';
  $sql = $base_sql . " AND r.hotel_id = ? AND (r.name LIKE ? OR r.type LIKE ? OR h.location LIKE ?) ORDER BY h.name ASC, r.name ASC";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('isss', $filter_hotel_id, $search_term, $search_term, $search_term);
  $stmt->execute();
  $result = $stmt->get_result();
} else if ($filter_hotel_id > 0) {
  // Only hotel filter
  $sql = $base_sql . " AND r.hotel_id = ? ORDER BY r.name ASC";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $filter_hotel_id);
  $stmt->execute();
  $result = $stmt->get_result();
} else if (!empty($search)) {
  // Only search
  $search_term = '%' . $conn->real_escape_string($search) . '%';
  $sql = $base_sql . " AND (r.name LIKE ? OR h.name LIKE ? OR r.type LIKE ? OR h.location LIKE ?) ORDER BY h.name ASC, r.name ASC";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ssss', $search_term, $search_term, $search_term, $search_term);
  $stmt->execute();
  $result = $stmt->get_result();
} else {
  // No filters
  $sql = $base_sql . " ORDER BY h.name ASC, r.name ASC";
  $result = $conn->query($sql);
}

// Fetch all rooms
$rooms = array();
$hotels_grouped = array();
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $rooms[$row['id']] = $row;
    
    // Group rooms by hotel for display
    if (!isset($hotels_grouped[$row['hotel_id']])) {
      $hotels_grouped[$row['hotel_id']] = array(
        'name' => $row['hotel_name'],
        'rooms' => array()
      );
    }
    $hotels_grouped[$row['hotel_id']]['rooms'][$row['id']] = $row;
  }
}

// Fetch current tariffs for all rooms
$current_date = date('Y-m-d');
$room_current_tariffs = array();

if (!empty($rooms)) {
  $room_ids = array_keys($rooms);
  $room_ids_str = implode(',', $room_ids);
  
  // Query to get current tariffs for all rooms
  // Order by room_id ASC, id DESC to get the latest tariff first for each room
  $current_tariffs_sql = "SELECT rt.* FROM room_tariffs rt 
                         WHERE rt.room_id IN ($room_ids_str) 
                         AND rt.start_date <= '$current_date' AND rt.end_date >= '$current_date' 
                         ORDER BY rt.room_id, rt.id DESC";
  
  $current_tariffs_result = $conn->query($current_tariffs_sql);
  
  if ($current_tariffs_result && $current_tariffs_result->num_rows > 0) {
    $processed_rooms = array();
    
    while ($tariff = $current_tariffs_result->fetch_assoc()) {
      $room_id = $tariff['room_id'];
      
      // If we haven't processed this room yet, use the first tariff we find
      // (which will be the one with the highest ID due to our ORDER BY)
      if (!in_array($room_id, $processed_rooms)) {
        $room_current_tariffs[$room_id] = $tariff;
        $processed_rooms[] = $room_id;
      }
    }
  }
}

// Get selected room for tariff management
$selected_room = null;
$room_tariffs = array();

if ($selected_room_id > 0 && isset($rooms[$selected_room_id])) {
  $selected_room = $rooms[$selected_room_id];
  
  // Fetch tariffs for the selected room
  $tariff_sql = "SELECT * FROM room_tariffs WHERE room_id = ? ORDER BY start_date ASC, day_type ASC";
  $tariff_stmt = $conn->prepare($tariff_sql);
  $tariff_stmt->bind_param('i', $selected_room_id);
  $tariff_stmt->execute();
  $tariff_result = $tariff_stmt->get_result();
  
  if ($tariff_result->num_rows > 0) {
    while ($tariff = $tariff_result->fetch_assoc()) {
      $room_tariffs[] = $tariff;
    }
  }
}

  ?>

  <main id="main" class="main">
    <div class="pagetitle" style="display: flex; justify-content: space-between; align-items: center;">
      <div>
        <h1>Room Tariff Management</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item active">Room Tariff Management</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= $_SESSION['success_message'] ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= $_SESSION['error_message'] ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
          <?php endif; ?>
          
          <!-- Room Selection Card -->
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Select Room for Tariff Management</h5>
                
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
              
              <!-- Legend for tariff types -->
              <div class="mb-3">
                <span class="badge bg-primary day-type-badge weekday-badge">Weekday</span>
                <span class="badge bg-warning day-type-badge weekend-badge">Weekend</span>
                <span class="badge bg-danger day-type-badge holiday-badge">Holiday</span>
                <span class="badge bg-success day-type-badge all-badge">All Days</span>
              </div>
              
              <!-- Rooms Selection Table -->
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Room</th>
                      <th>Room Type</th>
                      <th>Capacity</th>
                      <th>Current Price</th>
                      <th>CP</th>
                      <th>MAP</th>
                      <th>MVP</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($hotels_grouped) > 0): ?>
                      <?php foreach ($hotels_grouped as $hotel_id => $hotel_data): ?>
                        <!-- Hotel Group Header -->
                        <tr class="table-secondary">
                          <th colspan="9">
                            <i class="bi bi-building"></i> <?= htmlspecialchars($hotel_data['name']) ?>
                            <span class="badge bg-secondary ms-2"><?= count($hotel_data['rooms']) ?> rooms</span>
                          </th>
                        </tr>
                        
                        <?php if (count($hotel_data['rooms']) > 0): ?>
                          <?php foreach ($hotel_data['rooms'] as $room): ?>
                            <?php 
                              // Get current tariff for this room if available
                              $current_tariff = isset($room_current_tariffs[$room['id']]) ? $room_current_tariffs[$room['id']] : null;
                              $current_price = $current_tariff ? $current_tariff['price'] : 'N/A';
                              $current_cp = $current_tariff && !empty($current_tariff['cp']) ? $current_tariff['cp'] : 'N/A';
                              $current_map = $current_tariff && !empty($current_tariff['map']) ? $current_tariff['map'] : 'N/A';
                              $current_mvp = $current_tariff && !empty($current_tariff['mvp']) ? $current_tariff['mvp'] : 'N/A';
                              
                              // Day type for badge if available
                              $day_type = $current_tariff ? $current_tariff['day_type'] : '';
                              $day_type_class = '';
                              $day_type_label = '';
                              
                              switch($day_type) {
                                case 'weekday':
                                  $day_type_class = 'primary';
                                  $day_type_label = 'Weekday';
                                  break;
                                case 'weekend':
                                  $day_type_class = 'warning';
                                  $day_type_label = 'Weekend';
                                  break;
                                case 'holiday':
                                  $day_type_class = 'danger';
                                  $day_type_label = 'Holiday';
                                  break;
                                case 'all':
                                  $day_type_class = 'success';
                                  $day_type_label = 'All Days';
                                  break;
                              }
                            ?>
                            <tr class="<?= ($selected_room_id == $room['id']) ? 'table-primary' : '' ?>">
                              
                              <td><?= htmlspecialchars($room['name']) ?></td>
                              <td><?= isset($room['type']) ? htmlspecialchars($room['type']) : 'N/A' ?></td>
                              <td><?= isset($room['capacity']) ? $room['capacity'] . ' persons' : 'N/A' ?></td>
                              <td>
                                <?php if ($current_tariff): ?>
                                  ₹<?= number_format((float)$current_price, 2, '.', ',') ?>
                                  <?php if (!empty($day_type_label)): ?>
                                    <span class="badge bg-<?= $day_type_class ?> day-type-badge"><?= $day_type_label ?></span>
                                  <?php endif; ?>
                                <?php else: ?>
                                  <span class="text-muted">Not set</span>
                                <?php endif; ?>
                              </td>
                              <td><?= $current_cp !== 'N/A' ? '₹' . number_format((float)$current_cp, 2, '.', ',') : '<span class="text-muted">N/A</span>' ?></td>
                              <td><?= $current_map !== 'N/A' ? '₹' . number_format((float)$current_map, 2, '.', ',') : '<span class="text-muted">N/A</span>' ?></td>
                              <td><?= $current_mvp !== 'N/A' ? '₹' . number_format((float)$current_mvp, 2, '.', ',') : '<span class="text-muted">N/A</span>' ?></td>
                              <td>
                                <a href="price-maping.php?manage_room=<?= $room['id'] ?><?= $filter_hotel_id > 0 ? '&hotel_id='.$filter_hotel_id : '' ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
                                   class="btn btn-sm <?= ($selected_room_id == $room['id']) ? 'btn-success' : 'btn-primary' ?>">
                                  <?= ($selected_room_id == $room['id']) ? '<i class="bi bi-check-circle"></i> Selected' : '<i class="bi bi-calendar-range"></i> Manage Tariffs' ?>
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="9" class="text-center">No rooms found for this hotel</td>
                          </tr>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="9" class="text-center">No rooms found</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
          <!-- Tariff Management for Selected Room -->
          <?php if ($selected_room): ?>
          <div class="card mt-4">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">
                  Tariff Management for <?= htmlspecialchars($selected_room['name']) ?> 
                  <small class="text-muted">(<?= htmlspecialchars($selected_room['hotel_name']) ?>)</small>
                </h5>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTariffModal">
                  <i class="bi bi-plus-circle"></i> Add New Tariff Period
                </button>
              </div>
              
              <?php if (empty($room_tariffs)): ?>
                <div class="alert alert-info">
                  <i class="bi bi-info-circle"></i> No tariffs have been set for this room yet. 
                  Click the "Add New Tariff Period" button to create your first tariff period.
                </div>
              <?php else: ?>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Date Range</th>
                        <th>Day Type</th>
                        <th>Price</th>
                        <th>Discount Price</th>
                        <th>CP</th>
                        <th>MAP</th>
                        <th>MVP</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                      $current_date_range = '';
                      foreach ($room_tariffs as $index => $tariff): 
                        $date_range = date('d M Y', strtotime($tariff['start_date'])) . ' to ' . date('d M Y', strtotime($tariff['end_date']));
                        $is_new_date_range = $current_date_range !== $date_range;
                        
                        if ($is_new_date_range) {
                          $current_date_range = $date_range;
                        }
                        
                        $day_type_class = '';
                        $day_type_label = '';
                        
                        switch($tariff['day_type']) {
                          case 'weekday':
                            $day_type_class = 'primary';
                            $day_type_label = 'Weekday';
                            break;
                          case 'weekend':
                            $day_type_class = 'warning';
                            $day_type_label = 'Weekend';
                            break;
                          case 'holiday':
                            $day_type_class = 'danger';
                            $day_type_label = 'Holiday';
                            break;
                          case 'all':
                            $day_type_class = 'success';
                            $day_type_label = 'All Days';
                            break;
                          default:
                            $day_type_class = 'secondary';
                            $day_type_label = 'Unknown';
                        }
                      ?>
                        <tr class="tariff-row" id="tariff-row-<?= $tariff['id'] ?>">
                          <td>
                            <?= $date_range ?>
                          </td>
                          <td>
                            <span class="badge bg-<?= $day_type_class ?>"><?= $day_type_label ?></span>
                          </td>
                          <td>₹<?= number_format((float)$tariff['price'], 2, '.', ',') ?></td>
                          <td>₹<?= !empty($tariff['discount_price']) ? number_format((float)$tariff['discount_price'], 2, '.', ',') : 'N/A' ?></td>
                          <td>₹<?= !empty($tariff['cp']) ? number_format((float)$tariff['cp'], 2, '.', ',') : 'N/A' ?></td>
                          <td>₹<?= !empty($tariff['map']) ? number_format((float)$tariff['map'], 2, '.', ',') : 'N/A' ?></td>
                          <td>₹<?= !empty($tariff['mvp']) ? number_format((float)$tariff['mvp'], 2, '.', ',') : 'N/A' ?></td>
                          <td>
                            <div class="d-flex gap-1">
                              <button class="btn btn-sm btn-primary edit-tariff-btn" 
                                      data-id="<?= $tariff['id'] ?>"
                                      data-room-id="<?= $tariff['room_id'] ?>"
                                      data-start-date="<?= $tariff['start_date'] ?>"
                                      data-end-date="<?= $tariff['end_date'] ?>"
                                      data-day-type="<?= $tariff['day_type'] ?>"
                                      data-price="<?= $tariff['price'] ?>"
                                      data-discount-price="<?= $tariff['discount_price'] ?>"
                                      data-cp="<?= $tariff['cp'] ?>"
                                      data-map="<?= $tariff['map'] ?>"
                                      data-mvp="<?= $tariff['mvp'] ?>"
                                      data-bs-toggle="modal" data-bs-target="#editTariffModal">
                                <i class="bi bi-pencil"></i>
                              </button>
                              <a href="process/tariff-process.php?action=delete&id=<?= $tariff['id'] ?>&room_id=<?= $selected_room_id ?>" 
                                 class="btn btn-sm btn-danger delete-tariff-btn"
                                 onclick="return confirm('Are you sure you want to delete this tariff?')">
                                <i class="bi bi-trash"></i>
                              </a>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <!-- Add Tariff Modal -->
  <div class="modal fade" id="addTariffModal" tabindex="-1" aria-labelledby="addTariffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTariffModalLabel">Add New Tariff Period</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="process/tariff-process.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="room_id" value="<?= $selected_room_id ?>">
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required>
              </div>
              <div class="col-md-6">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="day_type" class="form-label">Day Type</label>
              <select class="form-select" id="day_type" name="day_type" required>
                <option value="all" selected>All Days (Default)</option>
                <option value="weekday">Weekday (Monday-Friday)</option>
                <option value="weekend">Weekend (Saturday-Sunday)</option>
                <option value="holiday">Holiday/Special Day</option>
              </select>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="price" class="form-label">Regular Price (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="price" name="price" required>
              </div>
              <div class="col-md-6">
                <label for="discount_price" class="form-label">Discount Price (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="discount_price" name="discount_price">
                <small class="text-muted">Leave empty if no discount</small>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-4">
                <label for="cp" class="form-label">Cost Price (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="cp" name="cp">
                <small class="text-muted">Your actual cost</small>
              </div>
              <div class="col-md-4">
                <label for="map" class="form-label">MAP (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="map" name="map">
                <small class="text-muted">Minimum Advertised Price</small>
              </div>
              <div class="col-md-4">
                <label for="mvp" class="form-label">MVP (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="mvp" name="mvp">
                <small class="text-muted">Minimum Viable Price</small>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Tariff</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Edit Tariff Modal -->
  <div class="modal fade" id="editTariffModal" tabindex="-1" aria-labelledby="editTariffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editTariffModalLabel">Edit Tariff</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="process/tariff-process.php" method="POST">
          <div class="modal-body">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_tariff_id">
            <input type="hidden" name="room_id" id="edit_room_id" value="<?= $selected_room_id ?>">
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="edit_start_date" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
              </div>
              <div class="col-md-6">
                <label for="edit_end_date" class="form-label">End Date</label>
                <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="edit_day_type" class="form-label">Day Type</label>
              <select class="form-select" id="edit_day_type" name="day_type" required>
                <option value="all">All Days (Default)</option>
                <option value="weekday">Weekday (Monday-Friday)</option>
                <option value="weekend">Weekend (Saturday-Sunday)</option>
                <option value="holiday">Holiday/Special Day</option>
              </select>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="edit_price" class="form-label">Regular Price (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="edit_price" name="price" required>
              </div>
              <div class="col-md-6">
                <label for="edit_discount_price" class="form-label">Discount Price (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="edit_discount_price" name="discount_price">
                <small class="text-muted">Leave empty if no discount</small>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-4">
                <label for="edit_cp" class="form-label">Cost Price (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="edit_cp" name="cp">
                <small class="text-muted">Your actual cost</small>
              </div>
              <div class="col-md-4">
                <label for="edit_map" class="form-label">MAP (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="edit_map" name="map">
                <small class="text-muted">Minimum Advertised Price</small>
              </div>
              <div class="col-md-4">
                <label for="edit_mvp" class="form-label">MVP (₹)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="edit_mvp" name="mvp">
                <small class="text-muted">Minimum Viable Price</small>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Tariff</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?= require("./config/footer.php") ?>
  
  <!-- Toast Container for notifications -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          <i class="bi bi-check-circle me-2"></i> <span id="successToastMessage">Tariff saved successfully!</span>
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
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('.tooltip-help'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
      });
      
      // Set min date for date inputs to today
      const today = new Date().toISOString().split('T')[0];
      if (document.getElementById('start_date')) {
        document.getElementById('start_date').min = today;
      }
      
      // Ensure end_date is not before start_date
      if (document.getElementById('start_date') && document.getElementById('end_date')) {
        document.getElementById('start_date').addEventListener('change', function() {
          document.getElementById('end_date').min = this.value;
          
          // If end_date is before start_date, update it
          if (document.getElementById('end_date').value && 
              document.getElementById('end_date').value < this.value) {
            document.getElementById('end_date').value = this.value;
          }
        });
      }
      
      // Handle edit tariff button clicks
      const editButtons = document.querySelectorAll('.edit-tariff-btn');
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const roomId = this.getAttribute('data-room-id');
          const startDate = this.getAttribute('data-start-date');
          const endDate = this.getAttribute('data-end-date');
          const dayType = this.getAttribute('data-day-type');
          const price = this.getAttribute('data-price');
          const discountPrice = this.getAttribute('data-discount-price');
          const cp = this.getAttribute('data-cp');
          const map = this.getAttribute('data-map');
          const mvp = this.getAttribute('data-mvp');
          
          // Populate the edit form
          document.getElementById('edit_tariff_id').value = id;
          document.getElementById('edit_room_id').value = roomId;
          document.getElementById('edit_start_date').value = startDate;
          document.getElementById('edit_end_date').value = endDate;
          document.getElementById('edit_day_type').value = dayType;
          document.getElementById('edit_price').value = price;
          document.getElementById('edit_discount_price').value = discountPrice || '';
          document.getElementById('edit_cp').value = cp || '';
          document.getElementById('edit_map').value = map || '';
          document.getElementById('edit_mvp').value = mvp || '';
        });
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