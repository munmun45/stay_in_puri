<!DOCTYPE html>
<html lang="en">

<head>

  <?= require("./config/meta.php") ?>

</head>

<body>

  <?= require("./config/header.php") ?>
  <?= require("./config/menu.php") ?>







  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Contact Info</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Contact Info</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <?php
        require_once __DIR__ . '/config/config.php';
        // Ensure table exists and fetch single row (id=1)
        $conn->query("CREATE TABLE IF NOT EXISTS contact_info (
          id INT PRIMARY KEY,
          phone1 VARCHAR(50) DEFAULT NULL,
          phone2 VARCHAR(50) DEFAULT NULL,
          email1 VARCHAR(100) DEFAULT NULL,
          email2 VARCHAR(100) DEFAULT NULL,
          address TEXT DEFAULT NULL,
          google_map TEXT DEFAULT NULL,
          updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $data = [
          'phone1' => '', 'phone2' => '', 'email1' => '', 'email2' => '', 'address' => '', 'google_map' => ''
        ];
        $rs = $conn->query("SELECT phone1, phone2, email1, email2, address, google_map FROM contact_info WHERE id = 1");
        if ($rs && $rs->num_rows > 0) {
          $data = $rs->fetch_assoc();
        }
      ?>

      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">Update Contact Information</h5>
            </div>
            <div class="card-body">
              <form action="process/contact-info.php" method="post">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Phone No 1</label>
                    <input type="text" name="phone1" class="form-control" value="<?= htmlspecialchars($data['phone1'] ?? '', ENT_QUOTES) ?>" placeholder="e.g. +91 9876543210">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Phone No 2</label>
                    <input type="text" name="phone2" class="form-control" value="<?= htmlspecialchars($data['phone2'] ?? '', ENT_QUOTES) ?>" placeholder="Optional">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email 1</label>
                    <input type="email" name="email1" class="form-control" value="<?= htmlspecialchars($data['email1'] ?? '', ENT_QUOTES) ?>" placeholder="e.g. info@example.com">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email 2</label>
                    <input type="email" name="email2" class="form-control" value="<?= htmlspecialchars($data['email2'] ?? '', ENT_QUOTES) ?>" placeholder="Optional">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="3" class="form-control" placeholder="Full address..."><?= htmlspecialchars($data['address'] ?? '') ?></textarea>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Google Map (textarea)</label>
                    <textarea name="google_map" rows="4" class="form-control" placeholder="Paste Google Map iframe or embed code here"><?= htmlspecialchars($data['google_map'] ?? '') ?></textarea>
                    <div class="form-text">You can paste the Google Maps iframe embed code, or a map URL.</div>
                  </div>
                </div>
                <div class="mt-3">
                  <button type="submit" name="save_contact" class="btn btn-primary">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->







  <?= require("./config/footer.php") ?>




</body>

</html>