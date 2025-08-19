<!DOCTYPE html>
<html lang="en">

<head>

  <?= require("./config/meta.php") ?>
  <!-- Cropper.js CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" />
  <style>
    .cropper-container { max-width: 100%; }
    .img-preview { max-width: 200px; max-height: 120px; overflow: hidden; border: 1px solid #ddd; }
  </style>

</head>

<body>

  <?= require("./config/header.php") ?>
  <?= require("./config/menu.php") ?>



  <main id="main" class="main">

    <div class="pagetitle" style="display: flex;justify-content: space-between;align-items: center;">
      <div>
        <h1>Ads Management</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Ads</li>
          </ol>
        </nav>
      </div>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdModal">
        <i class="bi bi-plus-circle"></i> Add Ad
      </button>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <?php require("./config/config.php"); ?>
          <?php
            // Hotels for dropdown
            $hotels = [];
            $hres = $conn->query("SELECT id, name FROM hotels ORDER BY name ASC");
            if ($hres && $hres->num_rows > 0) {
              while ($h = $hres->fetch_assoc()) { $hotels[] = $h; }
            }

            // Ads list with hotel name
            $ads = [];
            $res = $conn->query("SELECT a.*, h.name AS hotel_name FROM ads a LEFT JOIN hotels h ON h.id = a.hotel_id ORDER BY a.id DESC");
            if ($res && $res->num_rows > 0) {
              while ($r = $res->fetch_assoc()) { $ads[] = $r; }
            }
          ?>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Ads Listings</h5>

              <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Image</th>
                      <th scope="col">Title</th>
                      <th scope="col">Hotel</th>
                      <th scope="col">Created</th>
                      <th scope="col">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($ads)): ?>
                      <tr>
                        <td colspan="6" class="text-center">No ads found</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($ads as $idx => $a): ?>
                        <tr>
                          <th scope="row"><?= $idx + 1 ?></th>
                          <td>
                            <?php if (!empty($a['image_path'])): ?>
                              <img src="<?= htmlspecialchars($a['image_path']) ?>" alt="" style="height:60px;object-fit:cover;border-radius:4px;">
                            <?php endif; ?>
                          </td>
                          <td><?= htmlspecialchars($a['title']) ?></td>
                          <td><?= htmlspecialchars($a['hotel_name'] ?? '') ?></td>
                          <td><?= isset($a['created_at']) ? date('d M Y', strtotime($a['created_at'])) : '' ?></td>
                          <td>
                            <button 
                              class="btn btn-sm btn-primary edit-btn"
                              data-id="<?= $a['id'] ?>"
                              data-hotel_id="<?= $a['hotel_id'] ?>"
                              data-title='<?= htmlspecialchars($a['title'], ENT_QUOTES) ?>'
                              data-image='<?= htmlspecialchars($a['image_path'], ENT_QUOTES) ?>'
                              data-bs-toggle="modal" data-bs-target="#editAdModal">
                              <i class="bi bi-pencil"></i>
                            </button>
                            <a href="./process/ads.php?delete=<?= $a['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this ad?');"><i class="bi bi-trash"></i></a>
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

    <!-- Add Ad Modal -->
    <div class="modal fade" id="addAdModal" tabindex="-1" aria-labelledby="addAdModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addAdModalLabel">Add New Ad</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./process/ads.php" method="POST" id="addAdForm">
              <input type="hidden" name="add_ad" value="1">
              <div class="row g-3">
                <div class="col-md-12">
                  <label for="add_hotel_id" class="form-label">Select Hotel</label>
                  <select class="form-select" id="add_hotel_id" name="hotel_id" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($hotels as $h): ?>
                      <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-12">
                  <label for="add_title" class="form-label">Title</label>
                  <input type="text" class="form-control" id="add_title" name="title" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Select Image</label>
                  <input type="file" class="form-control" id="add_image" accept="image/*">
                  <small class="text-muted">Recommended ratio 16:9</small>
                  <div class="mt-2">
                    <img id="addCropImage" style="max-width:100%; display:none;" alt="to crop" />
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Preview</label>
                  <div class="img-preview" id="addPreviewBox"></div>
                </div>
                <input type="hidden" name="cropped_image" id="add_cropped_image">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" form="addAdForm" class="btn btn-primary">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Ad Modal -->
    <div class="modal fade" id="editAdModal" tabindex="-1" aria-labelledby="editAdModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editAdModalLabel">Edit Ad</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="./process/ads.php" method="POST" id="editAdForm">
              <input type="hidden" name="update_ad" value="1">
              <input type="hidden" name="id" id="edit_id">
              <div class="row g-3">
                <div class="col-md-12">
                  <label for="edit_hotel_id" class="form-label">Select Hotel</label>
                  <select class="form-select" id="edit_hotel_id" name="hotel_id" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($hotels as $h): ?>
                      <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-12">
                  <label for="edit_title" class="form-label">Title</label>
                  <input type="text" class="form-control" id="edit_title" name="title" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Replace Image (optional)</label>
                  <input type="file" class="form-control" id="edit_image" accept="image/*">
                  <small class="text-muted">Recommended ratio 16:9</small>
                  <div class="mt-2">
                    <img id="editCropImage" style="max-width:100%; display:none;" alt="to crop" />
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Current / Preview</label>
                  <div class="img-preview" id="editPreviewBox"></div>
                </div>
                <input type="hidden" name="cropped_image" id="edit_cropped_image">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" id="editCropBtn" class="btn btn-outline-primary">Crop</button>
            <button type="submit" form="editAdForm" class="btn btn-primary">Update</button>
          </div>
        </div>
      </div>
    </div>

  </main><!-- End #main -->


  <!-- ======= Footer ======= -->

  <?= require("./config/footer.php") ?>

  <!-- Cropper.js JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
  <script>
    (function(){
      let addCropper = null;
      let editCropper = null;

      const addImageInput = document.getElementById('add_image');
      const addCropImg = document.getElementById('addCropImage');
      const addPreviewBox = document.getElementById('addPreviewBox');
      const addCroppedInput = document.getElementById('add_cropped_image');
      const addCropBtn = document.getElementById('addCropBtn');

      const editImageInput = document.getElementById('edit_image');
      const editCropImg = document.getElementById('editCropImage');
      const editPreviewBox = document.getElementById('editPreviewBox');
      const editCroppedInput = document.getElementById('edit_cropped_image');
      const editCropBtn = document.getElementById('editCropBtn');

      function initCropper(imgEl, current) {
        if (!imgEl.src) return null;
        return new Cropper(imgEl, {
          aspectRatio: 16 / 5,
          viewMode: 1,
          preview: current,
          autoCropArea: 1,
        });
      }

      function destroy(cropper){ if (cropper) { cropper.destroy(); } }

      // Add flow
      if (addImageInput) {
        addImageInput.addEventListener('change', function(e){
          const f = this.files && this.files[0];
          if (!f) return;
          const url = URL.createObjectURL(f);
          destroy(addCropper);
          addPreviewBox.innerHTML = '';
          addCroppedInput.value = '';
          addCropImg.onload = function(){
            destroy(addCropper);
            addCropper = initCropper(addCropImg, addPreviewBox);
          };
          addCropImg.src = url;
          addCropImg.style.display = 'block';
        });

        // Auto-generate cropped image on submit if missing
        const addForm = document.getElementById('addAdForm');
        if (addForm) {
          addForm.addEventListener('submit', function(e){
            if (!addCroppedInput.value) {
              if (addCropper) {
                const canvas = addCropper.getCroppedCanvas({ width: 1280, height: 720 });
                const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
                addCroppedInput.value = dataUrl;
                addPreviewBox.innerHTML = '<img src="' + dataUrl + '" style="max-width:100%;">';
              }
            }
            if (!addCroppedInput.value) {
              e.preventDefault();
              alert('Please choose an image and crop before saving.');
            }
          });
        }
      }

      // Edit flow
      if (editImageInput) {
        editImageInput.addEventListener('change', function(){
          const f = this.files && this.files[0];
          if (!f) return;
          const url = URL.createObjectURL(f);
          destroy(editCropper);
          editCroppedInput.value = '';
          editPreviewBox.innerHTML = '';
          editCropImg.onload = function(){
            destroy(editCropper);
            editCropper = initCropper(editCropImg, editPreviewBox);
          };
          editCropImg.src = url;
          editCropImg.style.display = 'block';
        });

        editCropBtn.addEventListener('click', function(){
          if (!editCropper) { alert('Please select a new image to crop.'); return; }
          const canvas = editCropper.getCroppedCanvas({ width: 1280, height: 720 });
          const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
          editCroppedInput.value = dataUrl;
          editPreviewBox.innerHTML = '<img src="' + dataUrl + '" style="max-width:100%;">';
        });

        // Auto-generate cropped image for edit if user chose a new file
        const editForm = document.getElementById('editAdForm');
        if (editForm) {
          editForm.addEventListener('submit', function(){
            if (!editCroppedInput.value && editCropper) {
              const canvas = editCropper.getCroppedCanvas({ width: 1280, height: 720 });
              const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
              editCroppedInput.value = dataUrl;
            }
          });
        }
      }

      // Populate edit modal
      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function(){
          const id = this.getAttribute('data-id');
          const hotelId = this.getAttribute('data-hotel_id');
          const title = this.getAttribute('data-title');
          const img = this.getAttribute('data-image');
          document.getElementById('edit_id').value = id;
          document.getElementById('edit_title').value = title || '';
          const sel = document.getElementById('edit_hotel_id');
          if (sel) sel.value = hotelId || '';
          editPreviewBox.innerHTML = img ? ('<img src="' + img + '" style="max-width:100%;">') : '';
          editCroppedInput.value = '';
          editCropImg.src = '';
          editCropImg.style.display = 'none';
          destroy(editCropper);
        });
      });
    })();
  </script>

</body>

</html>


</body>

</html>


</body>

</html>