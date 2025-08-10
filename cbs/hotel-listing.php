<!DOCTYPE html>
<html lang="en">

<head>
  <?= require('./config/meta.php') ?>
  <style>
    .hotel-logo {
      max-width: 80px;
      max-height: 80px;
      object-fit: contain;
    }
    .preview-image {
      max-width: 150px;
      max-height: 150px;
      margin-top: 10px;
      display: none;
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

  // Check for search query
  $search = isset($_GET['search']) ? trim($_GET['search']) : '';
  
  // Fetch hotels with optional search filter
  if (!empty($search)) {
    $search_term = '%' . $search . '%';
    $sql = "SELECT * FROM hotels WHERE 
            name LIKE ? OR 
            location LIKE ? OR 
            email LIKE ? OR 
            mobile LIKE ? OR 
            gst_no LIKE ? 
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $search_term, $search_term, $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
  } else {
    $sql = 'SELECT * FROM hotels ORDER BY id DESC';
    $result = $conn->query($sql);
  }

  // Initialize variables for edit modal
  $editId = $editName = $editGstNo = $editMobile = $editEmail = $editGoogleLink = $editLocation = $editLogo = '';

  // Fetch hotel for editing if ID is provided in URL
  if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $editSql = 'SELECT * FROM hotels WHERE id = ?';
    $stmt = $conn->prepare($editSql);
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $editResult = $stmt->get_result();
    if ($editResult->num_rows > 0) {
      $editRow = $editResult->fetch_assoc();
      $editName = $editRow['name'];
      $editGstNo = $editRow['gst_no'];
      $editMobile = $editRow['mobile'];
      $editEmail = $editRow['email'];
      $editGoogleLink = $editRow['google_page_link'];
      $editLocation = $editRow['location'];
      $editLogo = $editRow['logo'];
    }
  }
  ?>

  <main id="main" class="main">
    <div class="pagetitle" style="display: flex;justify-content: space-between;align-items: center;">
      <div>
        <h1>Hotel Listing</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Hotel Listing</li>
          </ol>
        </nav>
      </div>

      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addHotelModal">
        <i class="bi bi-plus-circle"></i> Add New Hotel
      </button>
    </div><!-- End Page Title -->

    <!-- Add Hotel Modal -->
    <div class="modal fade" id="addHotelModal" tabindex="-1" aria-labelledby="addHotelModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addHotelModalLabel">Add New Hotel</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="process/hotels.php" method="POST" id="addHotelForm" class="row g-3" enctype="multipart/form-data">
              <div class="col-md-6">
                <label for="name" class="form-label">Hotel Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required>
              </div>
              
              <div class="col-md-6">
                <label for="gst_no" class="form-label">GST No. (Optional)</label>
                <input type="text" class="form-control" id="gst_no" name="gst_no">
              </div>
              
              <div class="col-md-6">
                <label for="mobile" class="form-label">Mobile No. <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="mobile" name="mobile" required>
              </div>
              
              <div class="col-md-6">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              
              <div class="col-md-12">
                <label for="google_page_link" class="form-label">Google Page Link (Optional)</label>
                <input type="url" class="form-control" id="google_page_link" name="google_page_link">
              </div>
              
              <div class="col-md-12">
                <label for="location" class="form-label">Location/Nearby <span class="text-danger">*</span></label>
                <textarea class="form-control" id="location" name="location" rows="3" required></textarea>
              </div>
              
              <div class="col-md-12">
                <label for="logo" class="form-label">Hotel Logo (Optional)</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*" onchange="previewImage(this, 'logoPreview')">
                <img id="logoPreview" src="#" alt="Logo Preview" class="preview-image">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="addHotelForm" class="btn btn-primary" name="add_hotel">Add Hotel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Hotel Modal -->
    <div class="modal fade" id="editHotelModal" tabindex="-1" aria-labelledby="editHotelModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editHotelModalLabel">Edit Hotel</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="process/hotels.php" method="POST" id="editHotelForm" class="row g-3" enctype="multipart/form-data">
              <input type="hidden" name="id" id="edit-id" value="<?= $editId ?>">
              
              <div class="col-md-6">
                <label for="edit-name" class="form-label">Hotel Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit-name" name="name" value="<?= $editName ?>" required>
              </div>
              
              <div class="col-md-6">
                <label for="edit-gst_no" class="form-label">GST No. (Optional)</label>
                <input type="text" class="form-control" id="edit-gst_no" name="gst_no" value="<?= $editGstNo ?>">
              </div>
              
              <div class="col-md-6">
                <label for="edit-mobile" class="form-label">Mobile No. <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit-mobile" name="mobile" value="<?= $editMobile ?>" required>
              </div>
              
              <div class="col-md-6">
                <label for="edit-email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="edit-email" name="email" value="<?= $editEmail ?>" required>
              </div>
              
              <div class="col-md-12">
                <label for="edit-google_page_link" class="form-label">Google Page Link (Optional)</label>
                <input type="url" class="form-control" id="edit-google_page_link" name="google_page_link" value="<?= $editGoogleLink ?>">
              </div>
              
              <div class="col-md-12">
                <label for="edit-location" class="form-label">Location/Nearby <span class="text-danger">*</span></label>
                <textarea class="form-control" id="edit-location" name="location" rows="3" required><?= $editLocation ?></textarea>
              </div>
              
              <div class="col-md-12">
                <label for="edit-logo" class="form-label">Hotel Logo (Optional)</label>
                <input type="file" class="form-control" id="edit-logo" name="logo" accept="image/*" onchange="previewImage(this, 'editLogoPreview')">
                <?php if (!empty($editLogo)): ?>
                  <div class="mt-2">
                    <p>Current logo:</p>
                    <img src="<?= $editLogo ?>" alt="Current Logo" style="max-width: 150px; max-height: 150px;">
                  </div>
                <?php endif; ?>
                <img id="editLogoPreview" src="#" alt="Logo Preview" class="preview-image">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <a href="hotel-listing" class="btn btn-secondary">Cancel</a>
            <button type="submit" form="editHotelForm" class="btn btn-primary" name="update_hotel">Update Hotel</button>
          </div>
        </div>
      </div>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Hotels List</h5>
                
                <!-- Search Form -->
                <form action="" method="GET" class="d-flex">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search hotels..." name="search" value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    <?php if(!empty($search)): ?>
                      <a href="hotel-listing.php" class="btn btn-outline-secondary">Clear</a>
                    <?php endif; ?>
                  </div>
                </form>
              </div>
              
              <!-- Search results info -->
              <?php if(!empty($search)): ?>
              <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                Search results for: <strong><?= htmlspecialchars($search) ?></strong> 
                (<?= $result->num_rows ?> results found)
              </div>
              <?php endif; ?>
              
              <!-- Table with all hotels -->
              <div class="table-responsive">
                <table class="table table-striped table-hover">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Logo</th>
                      <th scope="col">Hotel Name</th>
                      <th scope="col">GST No.</th>
                      <th scope="col">Mobile</th>
                      <th scope="col">Email</th>
                      <th scope="col">Location</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                      $counter = 1;
                      while ($row = $result->fetch_assoc()) {
                        ?>
                      <tr>
                        <th scope="row"><?= $counter++ ?></th>
                        <td>
                          <?php if (!empty($row['logo'])): ?>
                            <img src="<?= $row['logo'] ?>" alt="Hotel Logo" class="hotel-logo">
                          <?php else: ?>
                            <span class="text-muted">No logo</span>
                          <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= !empty($row['gst_no']) ? htmlspecialchars($row['gst_no']) : '<span class="text-muted">Not provided</span>' ?></td>
                        <td><?= htmlspecialchars($row['mobile']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td>
                          <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="<?= $row['id'] ?>" 
                            data-name="<?= htmlspecialchars($row['name']) ?>"
                            data-gst="<?= htmlspecialchars($row['gst_no'] ?? '') ?>"
                            data-mobile="<?= htmlspecialchars($row['mobile']) ?>"
                            data-email="<?= htmlspecialchars($row['email']) ?>"
                            data-google="<?= htmlspecialchars($row['google_page_link'] ?? '') ?>"
                            data-location="<?= htmlspecialchars($row['location']) ?>"
                            data-logo="<?= htmlspecialchars($row['logo'] ?? '') ?>"
                          ><i class="bi bi-pencil"></i> Edit</button>
                          <a href="process/hotels.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this hotel?')"><i class="bi bi-trash"></i> Delete</a>
                          <a href="room-listing.php?hotel_id=<?= $row['id'] ?>" class="btn btn-sm btn-success"><i class="bi bi-building"></i> Rooms</a>
                          <?php if (!empty($row['google_page_link'])): ?>
                            <a href="<?= $row['google_page_link'] ?>" target="_blank" class="btn btn-sm btn-info"><i class="bi bi-google"></i> View</a>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php
                      }
                    } else {
                      ?>
                      <tr>
                        <td colspan="8" class="text-center">No hotels found</td>
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
    document.addEventListener('DOMContentLoaded', function() {
      // Handle edit button clicks
      const editButtons = document.querySelectorAll('.edit-btn');
      const editModal = new bootstrap.Modal(document.getElementById('editHotelModal'));
      
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const name = this.getAttribute('data-name');
          const gst = this.getAttribute('data-gst');
          const mobile = this.getAttribute('data-mobile');
          const email = this.getAttribute('data-email');
          const google = this.getAttribute('data-google');
          const location = this.getAttribute('data-location');
          const logo = this.getAttribute('data-logo');
          
          // Set form values
          document.querySelector('#edit-id').value = id;
          document.querySelector('#edit-name').value = name;
          document.querySelector('#edit-gst_no').value = gst;
          document.querySelector('#edit-mobile').value = mobile;
          document.querySelector('#edit-email').value = email;
          document.querySelector('#edit-google_page_link').value = google;
          document.querySelector('#edit-location').value = location;
          
          // Show modal
          editModal.show();
        });
      });
      
      // Auto-open modals based on URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('add')) {
        const addModal = new bootstrap.Modal(document.getElementById('addHotelModal'));
        addModal.show();
      } else if (urlParams.has('edit')) {
        editModal.show();
      }
    });
    
    // Image preview function
    function previewImage(input, previewId) {
      const preview = document.getElementById(previewId);
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
      } else {
        preview.style.display = 'none';
      }
    }
  </script>
</body>
</html>