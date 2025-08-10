

<!DOCTYPE html>
<html lang="en">

<head>
  <?= require ('./config/meta.php') ?>
  <!-- Include Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .icon-preview {
      font-size: 24px;
      margin: 10px 0;
    }
    .icon-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
      gap: 10px;
      max-height: 300px;
      overflow-y: auto;
      border: 1px solid #ddd;
      padding: 10px;
      margin-top: 10px;
    }
    .icon-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      cursor: pointer;
      padding: 10px;
      border-radius: 5px;
    }
    .icon-item:hover {
      background-color: #f0f0f0;
    }
    .icon-item i {
      font-size: 24px;
      margin-bottom: 5px;
    }
    .icon-item span {
      font-size: 10px;
      text-align: center;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      width: 100%;
    }
  </style>
</head>

<body>
  <?= require ('./config/header.php') ?>
  <?= require ('./config/menu.php') ?>



  <?php
  require ('./config/config.php');

  // Check if user is logged in
  if (!isset($_SESSION['username'])) {
    header('Location: login');
    exit();
  }

  // Fetch all amenities
  $sql = 'SELECT * FROM amenities ORDER BY id DESC';
  $result = $conn->query($sql);

  // Initialize variables for edit modal
  $editId = $editTitle = $editIcon = '';

  // Fetch amenity for editing if ID is provided in URL
  if (isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $editSql = 'SELECT * FROM amenities WHERE id = ?';
    $stmt = $conn->prepare($editSql);
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $editResult = $stmt->get_result();
    if ($editResult->num_rows > 0) {
      $editRow = $editResult->fetch_assoc();
      $editTitle = $editRow['title'];
      $editIcon = $editRow['icon'];
    }
  }
  ?>

  <main id="main" class="main">
    <div class="pagetitle" style="display: flex;justify-content: space-between;align-items: center;">

      <div >
        
        <h1>Amenities Management</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Amenities</li>
          </ol>
        </nav>

      </div>


      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAmenityModal">
        <i class="bi bi-plus-circle"></i> Add New Amenity
      </button>


    </div><!-- End Page Title -->




    <!-- Edit Amenity Modal -->
    <div class="modal fade" id="editAmenityModal" tabindex="-1" aria-labelledby="editAmenityModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editAmenityModalLabel">Edit Amenity</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form action="process/amenities.php" method="POST" id="editAmenityForm" class="row g-3">
                        <input type="hidden" name="id" value="<?= $editId ?>">
                        
                        <div class="col-md-12">
                          <label for="edit-title" class="form-label">Title</label>
                          <input type="text" class="form-control" id="edit-title" name="title" value="<?= $editTitle ?>" required>
                        </div>
                        
                        <div class="col-md-12">
                          <label for="edit-icon" class="form-label">Icon</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="edit-icon" name="icon" value="<?= $editIcon ?>" required>
                            <button class="btn btn-outline-secondary" type="button" id="editIconPickerBtn">Pick Icon</button>
                          </div>
                          <div class="icon-preview mt-2" id="editIconPreview">
                            <?php if (!empty($editIcon)): ?>
                              <i class="<?= $editIcon ?>"></i>
                            <?php endif; ?>
                          </div>
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <a href="amenities" class="btn btn-secondary">Cancel</a>
                      <button type="submit" form="editAmenityForm" class="btn btn-primary" name="update_amenity">Update Amenity</button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Add Amenity Modal -->
              <div class="modal fade" id="addAmenityModal" tabindex="-1" aria-labelledby="addAmenityModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="addAmenityModalLabel">Add New Amenity</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form action="process/amenities.php" method="POST" id="addAmenityForm" class="row g-3">
                        <div class="col-md-12">
                          <label for="title" class="form-label">Title</label>
                          <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="col-md-12">
                          <label for="icon" class="form-label">Icon</label>
                          <div class="input-group">
                            <input type="text" class="form-control" id="icon" name="icon" required>
                            <button class="btn btn-outline-secondary" type="button" id="iconPickerBtn">Pick Icon</button>
                          </div>
                          <div class="icon-preview mt-2" id="iconPreview"></div>
                        </div>
                      </form>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" form="addAmenityForm" class="btn btn-primary" name="add_amenity">Add Amenity</button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Icon Picker Modal -->
              <div class="modal fade" id="iconPickerModal" tabindex="-1" aria-labelledby="iconPickerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="iconPickerModalLabel">Select an Icon</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <input type="text" class="form-control mb-3" id="iconSearch" placeholder="Search icons...">
                      <div class="icon-grid" id="iconGrid"></div>
                    </div>
                  </div>
                </div>
              </div>











    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Amenities List</h5>
              
              <!-- Table with all amenities -->
              <table class="table table-striped table-hover">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Icon</th>
                    <th scope="col">Preview</th>
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
                      <td><?= htmlspecialchars($row['title']) ?></td>
                      <td><code><?= htmlspecialchars($row['icon']) ?></code></td>
                      <td><i class="<?= $row['icon'] ?>"></i></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="<?= $row['id'] ?>" data-title="<?= htmlspecialchars($row['title']) ?>" data-icon="<?= htmlspecialchars($row['icon']) ?>"><i class="bi bi-pencil"></i> Edit</button>
                        <a href="process/amenities.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this amenity?')"><i class="bi bi-trash"></i> Delete</a>
                      </td>
                    </tr>
                  <?php
                    }
                  } else {
                    ?>
                    <tr>
                      <td colspan="5" class="text-center">No amenities found</td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><!-- End #main -->

  <?= require ('./config/footer.php') ?>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Get elements for add form
      const iconInput = document.getElementById('icon');
      const iconPreview = document.getElementById('iconPreview');
      const iconPickerBtn = document.getElementById('iconPickerBtn');
      
      // Get elements for edit form if they exist
      const editIconInput = document.getElementById('edit-icon');
      const editIconPreview = document.getElementById('editIconPreview');
      const editIconPickerBtn = document.getElementById('editIconPickerBtn');
      
      const iconPickerModal = new bootstrap.Modal(document.getElementById('iconPickerModal'));
      const iconGrid = document.getElementById('iconGrid');
      const iconSearch = document.getElementById('iconSearch');
      
      // Track which input is currently being edited
      let currentIconInput = null;
      let currentIconPreview = null;
      
      // Common Font Awesome icons
      const icons = [
        'fas fa-wifi', 'fas fa-swimming-pool', 'fas fa-parking', 'fas fa-concierge-bell',
        'fas fa-coffee', 'fas fa-utensils', 'fas fa-glass-martini', 'fas fa-dumbbell',
        'fas fa-spa', 'fas fa-hot-tub', 'fas fa-snowflake', 'fas fa-tv',
        'fas fa-shower', 'fas fa-bath', 'fas fa-baby-carriage', 'fas fa-wheelchair',
        'fas fa-smoking-ban', 'fas fa-paw', 'fas fa-shuttle-van', 'fas fa-taxi',
        'fas fa-car', 'fas fa-bicycle', 'fas fa-hiking', 'fas fa-mountain',
        'fas fa-umbrella-beach', 'fas fa-sun', 'fas fa-moon', 'fas fa-star',
        'fas fa-bed', 'fas fa-door-open', 'fas fa-key', 'fas fa-lock',
        'fas fa-shield-alt', 'fas fa-first-aid', 'fas fa-medkit', 'fas fa-heartbeat',
        'fas fa-phone', 'fas fa-wifi', 'fas fa-ethernet', 'fas fa-network-wired',
        'fas fa-desktop', 'fas fa-laptop', 'fas fa-mobile-alt', 'fas fa-plug',
        'fas fa-lightbulb', 'fas fa-fan', 'fas fa-air-freshener', 'fas fa-wind',
        'bi bi-wifi', 'bi bi-cup-hot', 'bi bi-cup', 'bi bi-telephone',
        'bi bi-tv', 'bi bi-camera', 'bi bi-alarm', 'bi bi-bell',
        'bi bi-bicycle', 'bi bi-car-front', 'bi bi-bus-front', 'bi bi-train-front',
        'bi bi-airplane', 'bi bi-person', 'bi bi-people', 'bi bi-person-wheelchair',
        'bi bi-door-open', 'bi bi-door-closed', 'bi bi-window', 'bi bi-house',
        'bi bi-building', 'bi bi-shop', 'bi bi-bank', 'bi bi-cash',
        'bi bi-credit-card', 'bi bi-safe', 'bi bi-lock', 'bi bi-unlock'
      ];
      
      // Update icon preview function
      function updateIconPreview(input, preview) {
        const iconClass = input.value.trim();
        preview.innerHTML = iconClass ? `<i class="${iconClass}"></i>` : '';
      }
      
      // Setup event listeners for add form
      if (iconInput && iconPreview) {
        iconInput.addEventListener('input', function() {
          updateIconPreview(iconInput, iconPreview);
        });
        
        iconPickerBtn.addEventListener('click', function() {
          currentIconInput = iconInput;
          currentIconPreview = iconPreview;
          populateIconGrid(icons);
          iconPickerModal.show();
        });
        
        // Initialize icon preview
        updateIconPreview(iconInput, iconPreview);
      }
      
      // Setup event listeners for edit form if it exists
      if (editIconInput && editIconPreview) {
        editIconInput.addEventListener('input', function() {
          updateIconPreview(editIconInput, editIconPreview);
        });
        
        editIconPickerBtn.addEventListener('click', function() {
          currentIconInput = editIconInput;
          currentIconPreview = editIconPreview;
          populateIconGrid(icons);
          iconPickerModal.show();
        });
        
        // Initialize edit icon preview
        updateIconPreview(editIconInput, editIconPreview);
      }
      
      // Search icons
      if (iconSearch) {
        iconSearch.addEventListener('input', function() {
          const searchTerm = this.value.toLowerCase();
          const filteredIcons = icons.filter(icon => icon.toLowerCase().includes(searchTerm));
          populateIconGrid(filteredIcons);
        });
      }
      
      // Populate icon grid
      function populateIconGrid(iconsArray) {
        iconGrid.innerHTML = '';
        iconsArray.forEach(icon => {
          const iconItem = document.createElement('div');
          iconItem.className = 'icon-item';
          iconItem.innerHTML = `<i class="${icon}"></i><span>${icon}</span>`;
          iconItem.addEventListener('click', function() {
            if (currentIconInput && currentIconPreview) {
              currentIconInput.value = icon;
              updateIconPreview(currentIconInput, currentIconPreview);
              iconPickerModal.hide();
            }
          });
          iconGrid.appendChild(iconItem);
        });
      }
      
      // Handle edit button clicks
      const editButtons = document.querySelectorAll('.edit-btn');
      const editModal = new bootstrap.Modal(document.getElementById('editAmenityModal'));
      
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const title = this.getAttribute('data-title');
          const icon = this.getAttribute('data-icon');
          
          // Set form values
          document.querySelector('#editAmenityForm input[name="id"]').value = id;
          document.querySelector('#edit-title').value = title;
          document.querySelector('#edit-icon').value = icon;
          
          // Update icon preview
          if (editIconPreview) {
            editIconPreview.innerHTML = icon ? `<i class="${icon}"></i>` : '';
          }
          
          // Show modal
          editModal.show();
        });
      });
      
      // Auto-open modals based on URL parameters
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('add')) {
        const addModal = new bootstrap.Modal(document.getElementById('addAmenityModal'));
        addModal.show();
      } else if (urlParams.has('edit')) {
        editModal.show();
      }
    });
  </script>
</body>
</html>