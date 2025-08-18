<?php
// Public Hotel Listing submission page (no CBS auth)
require_once __DIR__ . '/cbs/config/config.php';
include __DIR__ . '/includes/header.php';

$success = null;
$error = null;

function uploadHotelLogo($file) {
    $targetDir = __DIR__ . '/cbs/uploads/hotel_logos/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($file['name']);
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg','jpeg','png','gif'])) {
        return [false, 'Only JPG, JPEG, PNG, GIF allowed.'];
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return [false, 'File too large. Max 5MB.'];
    }
    $check = @getimagesize($file['tmp_name']);
    if ($check === false) {
        return [false, 'Uploaded file is not a valid image.'];
    }

    $newName = uniqid('hotel_', true) . '.' . $ext;
    $targetPath = $targetDir . $newName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return [false, 'Failed to upload image.'];
    }
    // Return relative path used elsewhere in CBS
    return [true, 'uploads/hotel_logos/' . $newName];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $gst_no = trim($_POST['gst_no'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $google_page_link = trim($_POST['google_page_link'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $logo_path = null;

    if ($name === '' || $mobile === '' || $email === '' || $location === '') {
        $error = 'Please fill all required fields.';
    } else {
        if (!empty($_FILES['logo']['name'])) {
            [$ok, $msg] = uploadHotelLogo($_FILES['logo']);
            if ($ok) {
                $logo_path = $msg; // relative path like uploads/hotel_logos/xxx
            } else {
                $error = 'Logo error: ' . $msg;
            }
        }

        if ($error === null) {
            $sql = "INSERT INTO hotels (name, gst_no, mobile, email, google_page_link, location, logo, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
            $stmt = $conn->prepare($sql);
            $gst_param = $gst_no !== '' ? $gst_no : null;
            $google_param = $google_page_link !== '' ? $google_page_link : null;
            $logo_param = $logo_path !== null ? $logo_path : null;
            $stmt->bind_param('sssssss', $name, $gst_param, $mobile, $email, $google_param, $location, $logo_param);
            if ($stmt->execute()) {
                $success = 'Thanks! Your hotel has been submitted and is pending review.';
                // Clear fields
                $name = $gst_no = $mobile = $email = $google_page_link = $location = '';
                $logo_path = null;
            } else {
                $error = 'Database error: ' . htmlspecialchars($stmt->error);
            }
        }
    }
}
?>

<section class="py-4 py-md-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card border-0 rounded-4">
          <div class="card-body p-3 p-sm-4 p-md-5">
            <h1 class="h4 mb-4">Submit your Hotel</h1>
            <?php if ($success): ?>
              <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
              <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="" method="post" enctype="multipart/form-data" class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Hotel Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">GST No. (Optional)</label>
                <input type="text" class="form-control" name="gst_no" value="<?php echo htmlspecialchars($gst_no ?? ''); ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label">Mobile No. <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="mobile" value="<?php echo htmlspecialchars($mobile ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
              </div>
              <div class="col-md-12">
                <label class="form-label">Google Page Link (Optional)</label>
                <input type="url" class="form-control" name="google_page_link" value="<?php echo htmlspecialchars($google_page_link ?? ''); ?>">
              </div>
              <div class="col-md-12">
                <label class="form-label">Location/Nearby <span class="text-danger">*</span></label>
                <textarea class="form-control" rows="3" name="location" required><?php echo htmlspecialchars($location ?? ''); ?></textarea>
              </div>
              <div class="col-md-12">
                <label class="form-label">Hotel Logo (Optional)</label>
                <input type="file" class="form-control" name="logo" accept="image/*">
              </div>
              <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="/" class="btn btn-outline-secondary">Cancel</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
