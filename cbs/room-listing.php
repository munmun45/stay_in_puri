<!DOCTYPE html>
<html lang="en">

<head>
  <?= require('./config/meta.php') ?>
  <style>
    .room-image {
      max-width: 80px;
      max-height: 80px;
      object-fit: contain;
      cursor: pointer;
    }
    .preview-image {
      max-width: 150px;
      max-height: 150px;
      margin-top: 10px;
      display: none;
    }
    .image-gallery {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }
    .image-gallery-item {
      position: relative;
      width: 150px;
      height: 150px;
    }
    .image-gallery-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .image-actions {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(0,0,0,0.7);
      display: flex;
      justify-content: space-around;
      padding: 5px 0;
    }
    .primary-badge {
      position: absolute;
      top: 5px;
      left: 5px;
      background: rgba(25, 135, 84, 0.8);
      color: white;
      padding: 2px 6px;
      border-radius: 3px;
      font-size: 10px;
    }
    .file-info {
      font-size: 0.875em;
      color: #666;
      margin-top: 5px;
    }
  </style>
</head>

<body>
  <?= require('./config/header.php') ?>
  <?= require('./config/menu.php') ?>

  <?php
  require('./config/config.php');

  // Check if user is logged in
  if (!isset($_SESSION['username'])) {
    header('Location: login');
    exit();
  }

  // Fetch all hotels for dropdown
  $hotels_sql = 'SELECT id, name FROM hotels ORDER BY name ASC';
  $hotels_result = $conn->query($hotels_sql);
  $hotels = [];
  while ($hotel = $hotels_result->fetch_assoc()) {
    $hotels[$hotel['id']] = $hotel['name'];
  }

  // Fetch all amenities for checkbox selection
  $amenities_sql = 'SELECT id, title FROM amenities ORDER BY title ASC';
  $amenities_result = $conn->query($amenities_sql);
  $amenities = [];
  while ($amenity = $amenities_result->fetch_assoc()) {
    $amenities[$amenity['id']] = $amenity['title'];
  }

  // Check if filtering by hotel
  $filter_hotel_id = isset($_GET['hotel_id']) ? (int)$_GET['hotel_id'] : 0;
  
  // Check for search query
  $search = isset($_GET['search']) ? trim($_GET['search']) : '';
  
  // Prepare base query
  $base_sql = 'SELECT r.*, h.name as hotel_name 
            FROM rooms r 
            JOIN hotels h ON r.hotel_id = h.id';
  
  // Apply filters
  if ($filter_hotel_id > 0 && !empty($search)) {
    // Both hotel filter and search
    $search_term = '%' . $search . '%';
    $sql = $base_sql . ' WHERE r.hotel_id = ? AND (r.name LIKE ? OR r.description LIKE ? OR h.name LIKE ?) ORDER BY r.id DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $filter_hotel_id, $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get the hotel name for display
    $hotel_name_sql = 'SELECT name FROM hotels WHERE id = ?';
    $hotel_stmt = $conn->prepare($hotel_name_sql);
    $hotel_stmt->bind_param('i', $filter_hotel_id);
    $hotel_stmt->execute();
    $hotel_result = $hotel_stmt->get_result();
    $filtered_hotel_name = ($hotel_result->num_rows > 0) ? $hotel_result->fetch_assoc()['name'] : 'Unknown Hotel';
  } 
  else if ($filter_hotel_id > 0) {
    // Only hotel filter
    $sql = $base_sql . ' WHERE r.hotel_id = ? ORDER BY r.id DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $filter_hotel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Get the hotel name for display
    $hotel_name_sql = 'SELECT name FROM hotels WHERE id = ?';
    $hotel_stmt = $conn->prepare($hotel_name_sql);
    $hotel_stmt->bind_param('i', $filter_hotel_id);
    $hotel_stmt->execute();
    $hotel_result = $hotel_stmt->get_result();
    $filtered_hotel_name = ($hotel_result->num_rows > 0) ? $hotel_result->fetch_assoc()['name'] : 'Unknown Hotel';
  } 
  else if (!empty($search)) {
    // Only search
    $search_term = '%' . $search . '%';
    $sql = $base_sql . ' WHERE r.name LIKE ? OR r.description LIKE ? OR h.name LIKE ? ORDER BY r.id DESC';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
  } 
  else {
    // No filters
    $sql = $base_sql . ' ORDER BY r.id DESC';
    $result = $conn->query($sql);
  }

  // Initialize variables for edit modal
  $editId = $editHotelId = $editName = $editDesc = $editCapacity = $editAmenities = '';
  $room_images = [];

  // Fetch room for editing if ID is provided in URL
  if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $editSql = 'SELECT * FROM rooms WHERE id = ?';
    $stmt = $conn->prepare($editSql);
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $editResult = $stmt->get_result();
    
    if ($editResult->num_rows > 0) {
      $editRow = $editResult->fetch_assoc();
      $editHotelId = $editRow['hotel_id'];
      $editName = $editRow['name'];
      $editDesc = $editRow['description'];
      $editCapacity = $editRow['max_capacity'];
      $editAmenities = explode(',', $editRow['amenities']);
      
      // Fetch room images
      $images_sql = 'SELECT * FROM room_images WHERE room_id = ? ORDER BY is_primary DESC, id ASC';
      $stmt = $conn->prepare($images_sql);
      $stmt->bind_param('i', $editId);
      $stmt->execute();
      $images_result = $stmt->get_result();
      
      while($image = $images_result->fetch_assoc()) {
        $room_images[] = $image;
      }
    }
  }
  ?>

  <main id="main" class="main">
    <div class="pagetitle" style="display: flex;justify-content: space-between;align-items: center;">
      <div>
        <h1>Room Listing</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Room Listing</li>
            <?php if (isset($filter_hotel_id) && $filter_hotel_id > 0): ?>
              <li class="breadcrumb-item active"><?= htmlspecialchars($filtered_hotel_name) ?></li>
            <?php endif; ?>
          </ol>
        </nav>
      </div>

      <div>
        <!-- Search form -->
        
        
        <!-- Filter and Add buttons -->
        <div class="d-flex">
          <?php if (isset($filter_hotel_id) && $filter_hotel_id > 0): ?>
            <a href="room-listing.php" class="btn btn-outline-secondary me-2">
              <i class="bi bi-funnel-fill"></i> Clear Filter
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
              <i class="bi bi-plus-circle"></i> Add Room to <?= htmlspecialchars($filtered_hotel_name) ?>
            </button>
          <?php else: ?>
            <div class="dropdown d-inline-block me-2">
              <button class="btn btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-funnel"></i> Filter by Hotel
              </button>
              <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                <?php foreach($hotels as $id => $name): ?>
                <li><a class="dropdown-item" href="room-listing.php?hotel_id=<?= $id ?>"><?= htmlspecialchars($name) ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
              <i class="bi bi-plus-circle"></i> Add New Room
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div><!-- End Page Title -->

    <!-- Search results info -->
    <?php if(!empty($search)): ?>
    <div class="alert alert-info mb-4">
      <i class="bi bi-info-circle"></i> 
      Search results for: <strong><?= htmlspecialchars($search) ?></strong> 
      (<?= $result->num_rows ?> results found)
      <?php if (isset($filter_hotel_id) && $filter_hotel_id > 0): ?>
        in hotel: <strong><?= htmlspecialchars($filtered_hotel_name) ?></strong>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Add Room Modal -->
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-labelledby="addRoomModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addRoomModalLabel">Add New Room</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="process/rooms.php" method="POST" id="addRoomForm" class="row g-3" enctype="multipart/form-data">
              <div class="col-md-6">
                <label for="hotel_id" class="form-label">Select Hotel <span class="text-danger">*</span></label>
                <select class="form-select" id="hotel_id" name="hotel_id" required>
                  <option value="">Choose...</option>
                  <?php foreach($hotels as $id => $name): ?>
                  <option value="<?= $id ?>" <?= (isset($filter_hotel_id) && $filter_hotel_id == $id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($name) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="col-md-6">
                <label for="name" class="form-label">Room Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              
              <div class="col-md-12">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
              </div>
              
              <div class="col-md-4">
                <label for="max_capacity" class="form-label">Max Capacity <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="max_capacity" name="max_capacity" min="1" required>
              </div>
              
              <div class="col-md-12">
                <label class="form-label">Room Amenities</label>
                <div class="row">
                  <?php foreach($amenities as $id => $title): ?>
                    <div class="col-md-4">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="<?= $id ?>" id="amenity-<?= $id ?>">
                        <label class="form-check-label" for="amenity-<?= $id ?>"><?= htmlspecialchars($title) ?></label>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
              
              <div class="col-md-12">
                <label for="images" class="form-label">Room Images <span class="text-danger">*</span></label>
                <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple required>
                <div class="file-info">
                  <small>Select multiple images. Supported formats: JPG, JPEG, PNG, GIF, WEBP. Maximum size: 5MB per image.</small>
                  <div id="file-preview" class="mt-2"></div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="addRoomForm" class="btn btn-primary" name="add_room">Add Room</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Room Modal -->
    <div class="modal fade" id="editRoomModal" tabindex="-1" aria-labelledby="editRoomModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editRoomModalLabel">Edit Room</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="process/rooms.php" method="POST" id="editRoomForm" class="row g-3" enctype="multipart/form-data">
              <input type="hidden" name="id" id="edit-id" value="<?= $editId ?>">
              
              <div class="col-md-6">
                <label for="edit-hotel_id" class="form-label">Hotel <span class="text-danger">*</span></label>
                <select class="form-select" id="edit-hotel_id" name="hotel_id" required>
                  <option value="">Select Hotel</option>
                  <?php foreach($hotels as $id => $name): ?>
                    <option value="<?= $id ?>" <?= ($editHotelId == $id) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($name) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              
              <div class="col-md-6">
                <label for="edit-name" class="form-label">Room Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit-name" name="name" value="<?= htmlspecialchars($editName) ?>" required>
              </div>
              
              <div class="col-md-12">
                <label for="edit-description" class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" id="edit-description" name="description" rows="4" required><?= htmlspecialchars($editDesc) ?></textarea>
              </div>
              
              <div class="col-md-4">
                <label for="edit-max_capacity" class="form-label">Max Capacity <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="edit-max_capacity" name="max_capacity" min="1" value="<?= $editCapacity ?>" required>
              </div>
              
              <div class="col-md-12">
                <label class="form-label">Room Amenities</label>
                <div class="row">
                  <?php foreach($amenities as $id => $title): ?>
                    <div class="col-md-4">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="<?= $id ?>" 
                          id="edit-amenity-<?= $id ?>" <?= (is_array($editAmenities) && in_array($id, $editAmenities)) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="edit-amenity-<?= $id ?>"><?= htmlspecialchars($title) ?></label>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
              
              <div class="col-md-12">
                <label for="edit-images" class="form-label">Add More Images</label>
                <input type="file" class="form-control" id="edit-images" name="images[]" accept="image/*" multiple>
                <div class="file-info">
                  <small>Add additional images. Supported formats: JPG, JPEG, PNG, GIF, WEBP. Maximum size: 5MB per image.</small>
                  <div id="edit-file-preview" class="mt-2"></div>
                </div>
              </div>
              
              <?php if(!empty($room_images)): ?>
                <div class="col-md-12">
                  <label class="form-label">Current Images</label>
                  <div class="image-gallery">
                    <?php foreach($room_images as $image): ?>
                      <div class="image-gallery-item">
                        <?php if($image['is_primary']): ?>
                          <span class="primary-badge">PRIMARY</span>
                        <?php endif; ?>
                        <img src="<?= $image['image_path'] ?>" alt="Room Image" class="img-thumbnail">
                        <div class="image-actions">
                          <?php if(!$image['is_primary']): ?>
                            <a href="process/rooms.php?set_primary=<?= $image['id'] ?>&room_id=<?= $editId ?>" 
                              class="btn btn-sm btn-success" title="Set as Primary">
                              <i class="bi bi-star-fill"></i>
                            </a>
                          <?php endif; ?>
                          <a href="process/rooms.php?delete_image=<?= $image['id'] ?>&room_id=<?= $editId ?>" 
                            class="btn btn-sm btn-danger" onclick="return confirm('Delete this image?')" title="Delete Image">
                            <i class="bi bi-trash"></i>
                          </a>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>
            </form>
          </div>
          <div class="modal-footer">
            <a href="room-listing" class="btn btn-secondary">Cancel</a>
            <button type="submit" form="editRoomForm" class="btn btn-primary" name="update_room">Update Room</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Room Images</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <img id="previewImage" src="" alt="Room Image" style="max-width: 100%; max-height: 80vh;">
          </div>
        </div>
      </div>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">

              <div class="d-flex justify-content-between align-items-center mb-4" style="    text-wrap-mode: nowrap;">
                <h5 class="card-title mb-0">Rooms List</h5>
                
                <!-- Search Form -->
                <form action="" method="GET" class="d-flex me-2 w-100" style="flex-direction: row-reverse;" >
                  <?php if (isset($filter_hotel_id) && $filter_hotel_id > 0): ?>
                    <input type="hidden" name="hotel_id" value="<?= $filter_hotel_id ?>">
                  <?php endif; ?>
                  <div class="input-group"  style="width: unset;" >
                    <input type="text" class="form-control" placeholder="Search rooms..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    <?php if(!empty($search)): ?>
                      <?php if (isset($filter_hotel_id) && $filter_hotel_id > 0): ?>
                        <a href="room-listing.php?hotel_id=<?= $filter_hotel_id ?>" class="btn btn-outline-secondary">Clear Search</a>
                      <?php else: ?>
                        <a href="room-listing.php" class="btn btn-outline-secondary">Clear Search</a>
                      <?php endif; ?>
                    <?php endif; ?>
                  </div>
                </form>
              </div>


              
              <!-- Table with all rooms -->
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Image</th>
                      <th scope="col">Hotel</th>
                      <th scope="col">Room Name</th>
                      <th scope="col">Description</th>
                      <th scope="col">Max Capacity</th>
                      <th scope="col">Amenities</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                      $counter = 1;
                      while ($row = $result->fetch_assoc()) {
                        // Get primary image
                        $img_sql = "SELECT image_path FROM room_images WHERE room_id = ? AND is_primary = 1 LIMIT 1";
                        $stmt = $conn->prepare($img_sql);
                        $stmt->bind_param("i", $row['id']);
                        $stmt->execute();
                        $img_result = $stmt->get_result();
                        $image_path = $img_result->num_rows > 0 ? $img_result->fetch_assoc()['image_path'] : '';
                        
                        // Get amenities titles
                        $amenity_titles = [];
                        if (!empty($row['amenities'])) {
                          $amenity_ids = explode(',', $row['amenities']);
                          foreach ($amenity_ids as $id) {
                            if (isset($amenities[$id])) {
                              $amenity_titles[] = $amenities[$id];
                            }
                          }
                        }
                        ?>
                      <tr>
                        <th scope="row"><?= $counter++ ?></th>
                        <td>
                          <?php if (!empty($image_path)): ?>
                            <img src="<?= $image_path ?>" alt="Room Image" class="room-image" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" onclick="showPreview('<?= $image_path ?>')">
                          <?php else: ?>
                            <span class="text-muted">No image</span>
                          <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['hotel_name']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= strlen($row['description']) > 50 ? htmlspecialchars(substr($row['description'], 0, 50)) . '...' : htmlspecialchars($row['description']) ?></td>
                        <td><?= $row['max_capacity'] ?> persons</td>
                        <td><?= !empty($amenity_titles) ? implode(', ', $amenity_titles) : '<span class="text-muted">None</span>' ?></td>
                        <td>
                          <a href="room-listing?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Edit</a>
                          <a href="price-maping.php?room_id=<?= $row['id'] ?>&search=<?= $row['name'] ?>" class="btn btn-sm btn-success"><i class="bi bi-tag"></i> Price</a>
                          <a href="process/rooms.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this room?')"><i class="bi bi-trash"></i> Delete</a>
                        </td>
                      </tr>
                    <?php
                      }
                    } else {
                      ?>
                      <tr>
                        <td colspan="8" class="text-center">No rooms found</td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <?= require('./config/footer.php') ?>

  <script>
    // Auto-open modals based on URL parameters
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('add')) {
        const addModal = new bootstrap.Modal(document.getElementById('addRoomModal'));
        addModal.show();
      } else if (urlParams.has('edit')) {
        const editModal = new bootstrap.Modal(document.getElementById('editRoomModal'));
        editModal.show();
      }
    });
    
    // Image preview function
    function showPreview(imagePath) {
      document.getElementById('previewImage').src = imagePath;
    }

    // File selection preview for add form
    document.getElementById('images').addEventListener('change', function(e) {
      const preview = document.getElementById('file-preview');
      preview.innerHTML = '';
      
      const files = e.target.files;
      if (files.length > 0) {
        const fileInfo = document.createElement('div');
        fileInfo.className = 'alert alert-info';
        fileInfo.innerHTML = `<strong>${files.length} file(s) selected:</strong><br>`;
        
        for (let i = 0; i < files.length; i++) {
          const file = files[i];
          const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
          fileInfo.innerHTML += `${i + 1}. ${file.name} (${sizeInMB} MB)<br>`;
          
          // Check file size
          if (file.size > 5000000) {
            fileInfo.innerHTML += `<span class="text-danger">⚠ File too large (max 5MB)</span><br>`;
          }
          
          // Check file type
          const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
          if (!validTypes.includes(file.type)) {
            fileInfo.innerHTML += `<span class="text-danger">⚠ Invalid file type</span><br>`;
          }
        }
        
        preview.appendChild(fileInfo);
      }
    });

    // File selection preview for edit form
    document.getElementById('edit-images').addEventListener('change', function(e) {
      const preview = document.getElementById('edit-file-preview');
      preview.innerHTML = '';
      
      const files = e.target.files;
      if (files.length > 0) {
        const fileInfo = document.createElement('div');
        fileInfo.className = 'alert alert-info';
        fileInfo.innerHTML = `<strong>${files.length} file(s) selected:</strong><br>`;
        
        for (let i = 0; i < files.length; i++) {
          const file = files[i];
          const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
          fileInfo.innerHTML += `${i + 1}. ${file.name} (${sizeInMB} MB)<br>`;
          
          // Check file size
          if (file.size > 5000000) {
            fileInfo.innerHTML += `<span class="text-danger">⚠ File too large (max 5MB)</span><br>`;
          }
          
          // Check file type
          const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
          if (!validTypes.includes(file.type)) {
            fileInfo.innerHTML += `<span class="text-danger">⚠ Invalid file type</span><br>`;
          }
        }
        
        preview.appendChild(fileInfo);
      }
    });

    // Form validation before submit
    document.getElementById('addRoomForm').addEventListener('submit', function(e) {
      const filesInput = document.getElementById('images');
      const files = filesInput.files;
      
      if (files.length === 0) {
        e.preventDefault();
        alert('Please select at least one image.');
        return false;
      }
      
      // Check file sizes and types
      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        if (file.size > 5000000) {
          e.preventDefault();
          alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
          return false;
        }
        
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
          e.preventDefault();
          alert(`File "${file.name}" is not a valid image type.`);
          return false;
        }
      }
    });
    // Automatically open edit modal when URL has edit parameter
    document.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('edit')) {
        const editModal = new bootstrap.Modal(document.getElementById('editRoomModal'));
        editModal.show();
        
        // Remove edit parameter from URL when modal is closed
        const editModalElement = document.getElementById('editRoomModal');
        editModalElement.addEventListener('hidden.bs.modal', function() {
          // Update URL without reloading the page
          window.history.replaceState({}, document.title, 'room-listing.php');
        });
      }
    });
  </script>
</body>

</html>