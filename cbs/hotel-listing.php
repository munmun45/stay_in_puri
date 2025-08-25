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
  $editMobile2 = $editMobile3 = $editMobile4 = $editEmail2 = '';
  $editDoc1Type = $editDoc2Type = $editDoc3Type = $editDoc4Type = '';
  $editAddress = '';

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
      $editName = $editRow["name"];
      $editGstNo = $editRow['gst_no'];
      $editMobile = $editRow['mobile'];
      $editEmail = $editRow['email'];
      $editGoogleLink = $editRow['google_page_link'];
      $editLocation = $editRow['location'];
      $editAddress = $editRow['address'] ?? '';
      $editLogo = $editRow['logo'];
      $editMobile2 = $editRow['mobile2'] ?? '';
      $editMobile3 = $editRow['mobile3'] ?? '';
      $editMobile4 = $editRow['mobile4'] ?? '';
      $editEmail2 = $editRow['email2'] ?? '';
      $editDoc1Type = $editRow['doc1_type'] ?? '';
      $editDoc2Type = $editRow['doc2_type'] ?? '';
      $editDoc3Type = $editRow['doc3_type'] ?? '';
      $editDoc4Type = $editRow['doc4_type'] ?? '';
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
                <label for="mobile2" class="form-label">Mobile No. 2 (Optional)</label>
                <input type="text" class="form-control" id="mobile2" name="mobile2">
              </div>
              <div class="col-md-6">
                <label for="mobile3" class="form-label">Mobile No. 3 (Optional)</label>
                <input type="text" class="form-control" id="mobile3" name="mobile3">
              </div>
              <div class="col-md-6">
                <label for="mobile4" class="form-label">Mobile No. 4 (Optional)</label>
                <input type="text" class="form-control" id="mobile4" name="mobile4">
              </div>
              
              <div class="col-md-6">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="col-md-6">
                <label for="email2" class="form-label">Alternate Email (Optional)</label>
                <input type="email" class="form-control" id="email2" name="email2">
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
                <label for="address" class="form-label">Address (Optional)</label>
                <textarea class="form-control" id="address" name="address" rows="2"></textarea>
              </div>
              
              <div class="col-md-12">
                <label for="logo" class="form-label">Hotel Logo (Optional)</label>
                <input type="file" class="form-control" id="logo" name="logo" accept="image/*" onchange="previewImage(this, 'logoPreview')">
                <img id="logoPreview" src="#" alt="Logo Preview" class="preview-image">
              </div>
              <div class="col-md-12">
                <label class="form-label">Documents (Any 2)</label>
                <div class="row g-2">
                  <div class="col-md-4">
                    <select class="form-select" name="doc1_type">
                      <option value="">Select Document Type</option>
                      <option value="aadhaar">Aadhaar</option>
                      <option value="pan">PAN</option>
                      <option value="gst">GST</option>
                      <option value="licence">Licence</option>
                    </select>
                  </div>
                  <div class="col-md-8">
                    <input type="file" class="form-control" name="doc1_file" accept="image/*,application/pdf">
                  </div>
                  <div class="col-md-4">
                    <select class="form-select" name="doc2_type">
                      <option value="">Select Document Type</option>
                      <option value="aadhaar">Aadhaar</option>
                      <option value="pan">PAN</option>
                      <option value="gst">GST</option>
                      <option value="licence">Licence</option>
                    </select>
                  </div>
                  <div class="col-md-8">
                    <input type="file" class="form-control" name="doc2_file" accept="image/*,application/pdf">
                  </div>
                  <div class="col-md-4">
                    <select class="form-select" name="doc3_type">
                      <option value="">Select Document Type</option>
                      <option value="aadhaar">Aadhaar</option>
                      <option value="pan">PAN</option>
                      <option value="gst">GST</option>
                      <option value="licence">Licence</option>
                    </select>
                  </div>
                  <div class="col-md-8">
                    <input type="file" class="form-control" name="doc3_file" accept="image/*,application/pdf">
                  </div>
                  <div class="col-md-4">
                    <select class="form-select" name="doc4_type">
                      <option value="">Select Document Type</option>
                      <option value="aadhaar">Aadhaar</option>
                      <option value="pan">PAN</option>
                      <option value="gst">GST</option>
                      <option value="licence">Licence</option>
                    </select>
                  </div>
                  <div class="col-md-8">
                    <input type="file" class="form-control" name="doc4_file" accept="image/*,application/pdf">
                  </div>
                </div>
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
                <label for="edit-mobile2" class="form-label">Mobile No. 2 (Optional)</label>
                <input type="text" class="form-control" id="edit-mobile2" name="mobile2" value="<?= htmlspecialchars($editMobile2) ?>">
              </div>
              <div class="col-md-6">
                <label for="edit-mobile3" class="form-label">Mobile No. 3 (Optional)</label>
                <input type="text" class="form-control" id="edit-mobile3" name="mobile3" value="<?= htmlspecialchars($editMobile3) ?>">
              </div>
              <div class="col-md-6">
                <label for="edit-mobile4" class="form-label">Mobile No. 4 (Optional)</label>
                <input type="text" class="form-control" id="edit-mobile4" name="mobile4" value="<?= htmlspecialchars($editMobile4) ?>">
              </div>
              
              <div class="col-md-6">
                <label for="edit-email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="edit-email" name="email" value="<?= $editEmail ?>" required>
              </div>
              <div class="col-md-6">
                <label for="edit-email2" class="form-label">Alternate Email (Optional)</label>
                <input type="email" class="form-control" id="edit-email2" name="email2" value="<?= htmlspecialchars($editEmail2) ?>">
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
                <label for="edit-address" class="form-label">Address (Optional)</label>
                <textarea class="form-control" id="edit-address" name="address" rows="2"><?= htmlspecialchars($editAddress) ?></textarea>
              </div>
              
              <div class="col-md-12">
                <label for="edit-logo" class="form-label">Hotel Logo (Optional)</label>
                <input type="file" class="form-control" id="edit-logo" name="logo" accept="image/*" onchange="previewImage(this, 'editLogoPreview')">
                <div id="currentLogoWrap" class="mt-2" style="display:none;">
                  <p>Current logo:</p>
                  <img id="currentLogoImg" src="" alt="Current Logo" style="max-width: 150px; max-height: 150px;">
                </div>
                <img id="editLogoPreview" src="#" alt="Logo Preview" class="preview-image">
              </div>
              
              <div class="col-md-12">
                <label class="form-label">Documents (Any 2)</label>
                <div class="row g-2">
                  <div class="col-md-4">
                    <select class="form-select" name="doc1_type">
                      <?php $d1 = $editDoc1Type; ?>
                      <option value="" <?= $d1===''?'selected':''; ?>>Select Document Type</option>
                      <option value="aadhaar" <?= $d1==='aadhaar'?'selected':''; ?>>Aadhaar</option>
                      <option value="pan" <?= $d1==='pan'?'selected':''; ?>>PAN</option>
                      <option value="gst" <?= $d1==='gst'?'selected':''; ?>>GST</option>
                      <option value="licence" <?= $d1==='licence'?'selected':''; ?>>Licence</option>
                    </select>
                  </div>
                  <div class="col-md-8">
                    <input type="file" class="form-control" name="doc1_file" accept="image/*,application/pdf">
                    <small id="doc1Current" class="text-muted" style="display:none;">Current: <a id="doc1Link" href="#" target="_blank">View</a></small>
                  </div>
                  <div class="col-md-4">
                    <select class="form-select" name="doc2_type">
                      <?php $d2 = $editDoc2Type; ?>
                      <option value="" <?= $d2===''?'selected':''; ?>>Select Document Type</option>
                      <option value="aadhaar" <?= $d2==='aadhaar'?'selected':''; ?>>Aadhaar</option>
                      <option value="pan" <?= $d2==='pan'?'selected':''; ?>>PAN</option>
                      <option value="gst" <?= $d2==='gst'?'selected':''; ?>>GST</option>
                      <option value="licence" <?= $d2==='licence'?'selected':''; ?>>Licence</option>
                    </select>
                  </div>
                  <div class="col-md-8">
                    <input type="file" class="form-control" name="doc2_file" accept="image/*,application/pdf">
                    <small id="doc2Current" class="text-muted" style="display:none;">Current: <a id="doc2Link" href="#" target="_blank">View</a></small>
                  </div>
                  <div class="col-md-4">
                    <select class="form-select" name="doc3_type">
                      <?php $d3 = $editDoc3Type; ?>
                      <option value="" <?= $d3===''?'selected':''; ?>>Select Document Type</option>
                      <option value="aadhaar" <?= $d3==='aadhaar'?'selected':''; ?>>Aadhaar</option>
                      <option value="pan" <?= $d3==='pan'?'selected':''; ?>>PAN</option>
                      <option value="gst" <?= $d3==='gst'?'selected':''; ?>>GST</option>
                      <option value="licence" <?= $d3==='licence'?'selected':''; ?>>Licence</option>
                    </select>
                  </div>
                  <div class="col-md-8">
                    <input type="file" class="form-control" name="doc3_file" accept="image/*,application/pdf">
                    <small id="doc3Current" class="text-muted" style="display:none;">Current: <a id="doc3Link" href="#" target="_blank">View</a></small>
                  </div>
                  <div class="col-md-4">
                    <select class="form-select" name="doc4_type">
                      <?php $d4 = $editDoc4Type; ?>
                      <option value="" <?= $d4===''?'selected':''; ?>>Select Document Type</option>
                      <option value="aadhaar" <?= $d4==='aadhaar'?'selected':''; ?>>Aadhaar</option>
                      <option value="pan" <?= $d4==='pan'?'selected':''; ?>>PAN</option>
                      <option value="gst" <?= $d4==='gst'?'selected':''; ?>>GST</option>
                      <option value="licence" <?= $d4==='licence'?'selected':''; ?>>Licence</option>
                    </select>
                  </div>
                  <div class="col-md-8">
                    <input type="file" class="form-control" name="doc4_file" accept="image/*,application/pdf">
                    <small id="doc4Current" class="text-muted" style="display:none;">Current: <a id="doc4Link" href="#" target="_blank">View</a></small>
                  </div>
                </div>
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
                      <th scope="col">Status</th>
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
                          <?php $isActive = isset($row['is_active']) ? $row['is_active'] : 1; ?>
                          <span class="badge bg-<?= $isActive ? 'success' : 'danger' ?>">
                            <?= $isActive ? 'Active' : 'Inactive' ?>
                          </span>
                        </td>
                        <td>
                          <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="<?= $row['id'] ?>" 
                            data-name="<?= htmlspecialchars($row['name']) ?>"
                            data-gst="<?= htmlspecialchars($row['gst_no'] ?? '') ?>"
                            data-mobile="<?= htmlspecialchars($row['mobile']) ?>"
                            data-email="<?= htmlspecialchars($row['email']) ?>"
                            data-email2="<?= htmlspecialchars($row['email2'] ?? '') ?>"
                            data-mobile2="<?= htmlspecialchars($row['mobile2'] ?? '') ?>"
                            data-mobile3="<?= htmlspecialchars($row['mobile3'] ?? '') ?>"
                            data-mobile4="<?= htmlspecialchars($row['mobile4'] ?? '') ?>"
                            data-google="<?= htmlspecialchars($row['google_page_link'] ?? '') ?>"
                            data-location="<?= htmlspecialchars($row['location']) ?>"
                            data-address="<?= htmlspecialchars($row['address'] ?? '') ?>"
                            data-logo="<?= htmlspecialchars($row['logo'] ?? '') ?>"
                            data-doc1_type="<?= htmlspecialchars($row['doc1_type'] ?? '') ?>"
                            data-doc2_type="<?= htmlspecialchars($row['doc2_type'] ?? '') ?>"
                            data-doc3_type="<?= htmlspecialchars($row['doc3_type'] ?? '') ?>"
                            data-doc4_type="<?= htmlspecialchars($row['doc4_type'] ?? '') ?>"
                            data-doc1_file="<?= htmlspecialchars($row['doc1_file'] ?? '') ?>"
                            data-doc2_file="<?= htmlspecialchars($row['doc2_file'] ?? '') ?>"
                            data-doc3_file="<?= htmlspecialchars($row['doc3_file'] ?? '') ?>"
                            data-doc4_file="<?= htmlspecialchars($row['doc4_file'] ?? '') ?>"
                          ><i class="bi bi-pencil"></i> Edit</button>
                          <a href="process/hotels.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this hotel?')"><i class="bi bi-trash"></i> Delete</a>
                          <a href="room-listing.php?hotel_id=<?= $row['id'] ?>" class="btn btn-sm btn-success"><i class="bi bi-building"></i> Rooms</a>
                          <?php $isActive = isset($row['is_active']) ? $row['is_active'] : 1; ?>
                          <a href="process/hotels.php?toggle_status=<?= $row['id'] ?>&current=<?= $isActive ?>" 
                             class="btn btn-sm btn-<?= $isActive ? 'warning' : 'info' ?>">
                            <i class="bi bi-toggle-<?= $isActive ? 'on' : 'off' ?>"></i> 
                            <?= $isActive ? 'Deactivate' : 'Activate' ?>
                          </a>
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
          const editForm = document.getElementById('editHotelForm');
          const id = this.getAttribute('data-id');
          const name = this.getAttribute('data-name');
          const gst = this.getAttribute('data-gst');
          const mobile = this.getAttribute('data-mobile');
          const email = this.getAttribute('data-email');
          const email2 = this.getAttribute('data-email2');
          const google = this.getAttribute('data-google');
          const location = this.getAttribute('data-location');
          const address = this.getAttribute('data-address');
          const logo = this.getAttribute('data-logo');
          const mobile2 = this.getAttribute('data-mobile2');
          const mobile3 = this.getAttribute('data-mobile3');
          const mobile4 = this.getAttribute('data-mobile4');
          const d1type = this.getAttribute('data-doc1_type');
          const d2type = this.getAttribute('data-doc2_type');
          const d3type = this.getAttribute('data-doc3_type');
          const d4type = this.getAttribute('data-doc4_type');
          const doc1File = this.getAttribute('data-doc1_file') || '';
          const doc2File = this.getAttribute('data-doc2_file') || '';
          const doc3File = this.getAttribute('data-doc3_file') || '';
          const doc4File = this.getAttribute('data-doc4_file') || '';

          // Set form values
          document.querySelector('#edit-id').value = id;
          document.querySelector('#edit-name').value = name;
          document.querySelector('#edit-gst_no').value = gst;
          document.querySelector('#edit-mobile').value = mobile;
          document.querySelector('#edit-email').value = email;
          if (document.querySelector('#edit-email2')) document.querySelector('#edit-email2').value = email2 || '';
          if (document.querySelector('#edit-mobile2')) document.querySelector('#edit-mobile2').value = mobile2 || '';
          if (document.querySelector('#edit-mobile3')) document.querySelector('#edit-mobile3').value = mobile3 || '';
          if (document.querySelector('#edit-mobile4')) document.querySelector('#edit-mobile4').value = mobile4 || '';
          if (editForm && editForm.querySelector('select[name="doc1_type"]')) editForm.querySelector('select[name="doc1_type"]').value = d1type || '';
          if (editForm && editForm.querySelector('select[name="doc2_type"]')) editForm.querySelector('select[name="doc2_type"]').value = d2type || '';
          if (editForm && editForm.querySelector('select[name="doc3_type"]')) editForm.querySelector('select[name="doc3_type"]').value = d3type || '';
          if (editForm && editForm.querySelector('select[name="doc4_type"]')) editForm.querySelector('select[name="doc4_type"]').value = d4type || '';
          document.querySelector('#edit-google_page_link').value = google;
          document.querySelector('#edit-location').value = location;
          const editAddressEl = document.querySelector('#edit-address');
          if (editAddressEl) editAddressEl.value = address || '';

          // Populate current logo preview (existing file)
          const currentLogoWrap = document.getElementById('currentLogoWrap');
          const currentLogoImg = document.getElementById('currentLogoImg');
          if (logo) {
            currentLogoImg.src = logo;
            currentLogoWrap.style.display = 'block';
          } else {
            currentLogoImg.src = '';
            currentLogoWrap.style.display = 'none';
          }

          // Populate current document links
          const setDocLink = (wrapId, linkId, url) => {
            const wrap = document.getElementById(wrapId);
            const link = document.getElementById(linkId);
            if (wrap && link) {
              if (url) {
                link.href = url;
                wrap.style.display = 'inline';
              } else {
                link.removeAttribute('href');
                wrap.style.display = 'none';
              }
            }
          };
          setDocLink('doc1Current', 'doc1Link', doc1File);
          setDocLink('doc2Current', 'doc2Link', doc2File);
          setDocLink('doc3Current', 'doc3Link', doc3File);
          setDocLink('doc4Current', 'doc4Link', doc4File);

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