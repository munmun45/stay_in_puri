<?php
require_once "./config/config.php";

// Initialize variables
$success_msg = $error_msg = "";
$offer_id = $hotel_id = $room_id = $discount_percentage = $min_amount = $valid_from = $valid_to = $description = $is_active = "";

// Check for session messages
session_start();
if (isset($_SESSION['offer_message'])) {
    if ($_SESSION['offer_status'] === 'success') {
        $success_msg = $_SESSION['offer_message'];
    } else {
        $error_msg = $_SESSION['offer_message'];
    }
    // Clear the session messages
    unset($_SESSION['offer_message']);
    unset($_SESSION['offer_status']);
}

// Edit offer
if (isset($_GET["edit"]) && !empty($_GET["edit"])) {
    $edit_id = $_GET["edit"];
    $sql = "SELECT * FROM offers WHERE id = " . $conn->real_escape_string($edit_id);
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $offer_id = $row["id"];
        $hotel_id = $row["hotel_id"];
        $room_id = $row["room_id"];
        $discount_percentage = $row["discount_percentage"];
        $min_amount = $row["min_amount"];
        $valid_from = $row["valid_from"];
        $valid_to = $row["valid_to"];
        $description = $row["description"];
        $is_active = $row["is_active"];
    }
}

// Fetch all hotels
$hotels = [];
$sql = "SELECT id, name FROM hotels WHERE is_active = 1 ORDER BY name ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
}

// Fetch all rooms with hotel names
$rooms = [];
$sql = "SELECT r.id, r.name, h.name as hotel_name 
        FROM rooms r 
        JOIN hotels h ON r.hotel_id = h.id 
        WHERE r.is_active = 1 AND h.is_active = 1 
        ORDER BY h.name ASC, r.name ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

// Fetch all offers with hotel and room details
$offers = [];
$sql = "SELECT o.*, 
        h.name as hotel_name, 
        r.name as room_name,
        CASE 
            WHEN o.room_id IS NOT NULL THEN (SELECT h2.name FROM hotels h2 JOIN rooms r2 ON r2.hotel_id = h2.id WHERE r2.id = o.room_id)
            ELSE h.name 
        END as display_hotel_name
        FROM offers o 
        LEFT JOIN hotels h ON o.hotel_id = h.id 
        LEFT JOIN rooms r ON o.room_id = r.id 
        ORDER BY o.valid_to DESC, o.created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $offers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <?= require("./config/meta.php") ?>
  <style>
    .form-check-input {
      cursor: pointer;
    }
    .offer-expired {
      background-color: #fff3cd;
    }
    .offer-inactive {
      background-color: #f8d7da;
    }
  </style>
</head>

<body>

  <?= require("./config/header.php") ?>
  <?= require("./config/menu.php") ?>


  <main id="main" class="main">

    <div class="pagetitle" style="display: flex;justify-content: space-between;align-items: center;">
      <div>
        <h1>Offer Management</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Offers</li>
          </ol>
        </nav>
      </div>
      
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfferModal">
        <i class="bi bi-plus-circle"></i> Add New Offer
      </button>
    </div><!-- End Page Title -->

    <!-- Add Offer Modal -->
    <div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addOfferModalLabel">Add New Offer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./process/offer-process.php" method="POST" id="addOfferForm" class="row g-3">
              <input type="hidden" name="offer_id" value="">
              
              <div class="col-md-6">
                <label for="hotel_id" class="form-label">Hotel</label>
                <select class="form-select" id="hotel_id" name="hotel_id">
                  <option value="">Select Hotel (for hotel-wide offer)</option>
                  <?php foreach ($hotels as $hotel): ?>
                    <option value="<?= $hotel['id'] ?>"><?= htmlspecialchars($hotel['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="col-md-6">
                <label for="room_id" class="form-label">Room</label>
                <select class="form-select" id="room_id" name="room_id">
                  <option value="">Select Room (for room-specific offer)</option>
                  <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['hotel_name'] . ' - ' . $room['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="col-md-6">
                <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" min="0" max="100" step="0.01" required>
              </div>
              
              <div class="col-md-6">
                <label for="min_amount" class="form-label">Minimum Amount (₹)</label>
                <input type="number" class="form-control" id="min_amount" name="min_amount" min="0" step="0.01">
                <small class="text-muted">Leave empty if no minimum amount</small>
              </div>
              
              <div class="col-md-6">
                <label for="valid_from" class="form-label">Valid From</label>
                <input type="date" class="form-control" id="valid_from" name="valid_from" required>
              </div>
              
              <div class="col-md-6">
                <label for="valid_to" class="form-label">Valid To</label>
                <input type="date" class="form-control" id="valid_to" name="valid_to" required>
              </div>
              
              <div class="col-md-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
              </div>
              
              <div class="col-md-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                  <label class="form-check-label" for="is_active">Active</label>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="addOfferForm" class="btn btn-primary">Add Offer</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Offer Modal -->
    <div class="modal fade" id="editOfferModal" tabindex="-1" aria-labelledby="editOfferModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editOfferModalLabel">Edit Offer</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./process/offer-process.php" method="POST" id="editOfferForm" class="row g-3">
              <input type="hidden" name="offer_id" id="edit_offer_id">
              
              <div class="col-md-6">
                <label for="edit_hotel_id" class="form-label">Hotel</label>
                <select class="form-select" id="edit_hotel_id" name="hotel_id">
                  <option value="">Select Hotel (for hotel-wide offer)</option>
                  <?php foreach ($hotels as $hotel): ?>
                    <option value="<?= $hotel['id'] ?>"><?= htmlspecialchars($hotel['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="col-md-6">
                <label for="edit_room_id" class="form-label">Room</label>
                <select class="form-select" id="edit_room_id" name="room_id">
                  <option value="">Select Room (for room-specific offer)</option>
                  <?php foreach ($rooms as $room): ?>
                    <option value="<?= $room['id'] ?>"><?= htmlspecialchars($room['hotel_name'] . ' - ' . $room['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="col-md-6">
                <label for="edit_discount_percentage" class="form-label">Discount Percentage (%)</label>
                <input type="number" class="form-control" id="edit_discount_percentage" name="discount_percentage" min="0" max="100" step="0.01" required>
              </div>
              
              <div class="col-md-6">
                <label for="edit_min_amount" class="form-label">Minimum Amount (₹)</label>
                <input type="number" class="form-control" id="edit_min_amount" name="min_amount" min="0" step="0.01">
                <small class="text-muted">Leave empty if no minimum amount</small>
              </div>
              
              <div class="col-md-6">
                <label for="edit_valid_from" class="form-label">Valid From</label>
                <input type="date" class="form-control" id="edit_valid_from" name="valid_from" required>
              </div>
              
              <div class="col-md-6">
                <label for="edit_valid_to" class="form-label">Valid To</label>
                <input type="date" class="form-control" id="edit_valid_to" name="valid_to" required>
              </div>
              
              <div class="col-md-12">
                <label for="edit_description" class="form-label">Description</label>
                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
              </div>
              
              <div class="col-md-12">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                  <label class="form-check-label" for="edit_is_active">Active</label>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="editOfferForm" class="btn btn-primary">Update Offer</button>
          </div>
        </div>
      </div>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <!-- Display Messages -->
          <?php if (!empty($success_msg)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= $success_msg ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <?php if (!empty($error_msg)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= $error_msg ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <!-- Offers List Card -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Offer Listings</h5>

              <!-- Table with stripped rows -->
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Hotel/Room</th>
                      <th scope="col">Discount</th>
                      <th scope="col">Min Amount</th>
                      <th scope="col">Valid Period</th>
                      <th scope="col">Status</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($offers)): ?>
                      <tr>
                        <td colspan="7" class="text-center">No offers found</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($offers as $index => $offer): 
                        $today = date('Y-m-d');
                        $is_expired = ($today > $offer['valid_to']);
                        $row_class = '';
                        if (!$offer['is_active']) {
                          $row_class = 'offer-inactive';
                        } elseif ($is_expired) {
                          $row_class = 'offer-expired';
                        }
                      ?>
                        <tr class="<?= $row_class ?>">
                          <th scope="row"><?= $index + 1 ?></th>
                          <td>
                            <?php if (!empty($offer['hotel_id']) && empty($offer['room_id'])): ?>
                              <strong>Hotel:</strong> <?= htmlspecialchars($offer['display_hotel_name']) ?>
                              <br><small class="text-muted">All rooms</small>
                            <?php elseif (!empty($offer['room_id'])): ?>
                              <strong>Hotel:</strong> <?= htmlspecialchars($offer['display_hotel_name']) ?>
                              <br><strong>Room:</strong> <?= htmlspecialchars($offer['room_name']) ?>
                            <?php endif; ?>
                          </td>
                          <td><?= $offer['discount_percentage'] ?>%</td>
                          <td><?= !empty($offer['min_amount']) ? '₹' . $offer['min_amount'] : 'N/A' ?></td>
                          <td>
                            <?= date('d M Y', strtotime($offer['valid_from'])) ?> to<br>
                            <?= date('d M Y', strtotime($offer['valid_to'])) ?>
                            <?php if ($is_expired): ?>
                              <span class="badge bg-warning">Expired</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php if ($offer['is_active']): ?>
                              <span class="badge bg-success">Active</span>
                            <?php else: ?>
                              <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <button type="button" class="btn btn-sm btn-primary edit-btn" 
                              data-id="<?= $offer['id'] ?>"
                              data-hotel-id="<?= $offer['hotel_id'] ?>"
                              data-room-id="<?= $offer['room_id'] ?>"
                              data-discount="<?= $offer['discount_percentage'] ?>"
                              data-min-amount="<?= $offer['min_amount'] ?>"
                              data-valid-from="<?= $offer['valid_from'] ?>"
                              data-valid-to="<?= $offer['valid_to'] ?>"
                              data-description="<?= htmlspecialchars($offer['description']) ?>"
                              data-active="<?= $offer['is_active'] ?>">
                              <i class="bi bi-pencil"></i>
                            </button>
                            <a href="./process/offer-process.php?delete=<?= $offer['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this offer?')"><i class="bi bi-trash"></i></a>
                            <?php $isActive = isset($offer['is_active']) ? $offer['is_active'] : 1; ?>
                            <a href="./process/offer-process.php?toggle_status=<?= $offer['id'] ?>&current=<?= $isActive ?>" 
                               class="btn btn-sm btn-<?= $isActive ? 'warning' : 'info' ?>">
                              <i class="bi bi-toggle-<?= $isActive ? 'on' : 'off' ?>"></i> 
                              <?= $isActive ? 'Deactivate' : 'Activate' ?>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Get the hotel and room select elements for add form
      const hotelSelect = document.getElementById('hotel_id');
      const roomSelect = document.getElementById('room_id');
      
      // Get the hotel and room select elements for edit form
      const editHotelSelect = document.getElementById('edit_hotel_id');
      const editRoomSelect = document.getElementById('edit_room_id');
      
      // Add event listeners to ensure only one of hotel or room is selected in add form
      hotelSelect.addEventListener('change', function() {
        if (this.value !== '') {
          roomSelect.value = '';
        }
      });
      
      roomSelect.addEventListener('change', function() {
        if (this.value !== '') {
          hotelSelect.value = '';
        }
      });
      
      // Add event listeners to ensure only one of hotel or room is selected in edit form
      editHotelSelect.addEventListener('change', function() {
        if (this.value !== '') {
          editRoomSelect.value = '';
        }
      });
      
      editRoomSelect.addEventListener('change', function() {
        if (this.value !== '') {
          editHotelSelect.value = '';
        }
      });
      
      // Handle edit button clicks
      const editButtons = document.querySelectorAll('.edit-btn');
      const editModal = new bootstrap.Modal(document.getElementById('editOfferModal'));
      
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const offerId = this.getAttribute('data-id');
          const hotelId = this.getAttribute('data-hotel-id');
          const roomId = this.getAttribute('data-room-id');
          const discountPercentage = this.getAttribute('data-discount');
          const minAmount = this.getAttribute('data-min-amount');
          const validFrom = this.getAttribute('data-valid-from');
          const validTo = this.getAttribute('data-valid-to');
          const description = this.getAttribute('data-description');
          const isActive = this.getAttribute('data-active');
          
          // Set form values
          document.getElementById('edit_offer_id').value = offerId;
          document.getElementById('edit_hotel_id').value = hotelId || '';
          document.getElementById('edit_room_id').value = roomId || '';
          document.getElementById('edit_discount_percentage').value = discountPercentage;
          document.getElementById('edit_min_amount').value = minAmount || '';
          document.getElementById('edit_valid_from').value = validFrom;
          document.getElementById('edit_valid_to').value = validTo;
          document.getElementById('edit_description').value = description || '';
          document.getElementById('edit_is_active').checked = isActive === '1';
          
          // Show modal
          editModal.show();
        });
      });
      
      // Set today's date as default for valid_from in add form
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('valid_from').value = today;
      
      // Set date 30 days from now as default for valid_to in add form
      const thirtyDaysFromNow = new Date();
      thirtyDaysFromNow.setDate(thirtyDaysFromNow.getDate() + 30);
      document.getElementById('valid_to').value = thirtyDaysFromNow.toISOString().split('T')[0];
    });
  </script>
</body>
</html>