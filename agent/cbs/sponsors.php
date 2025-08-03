<?php
session_start();

// Check if user is logged in and is primary user
if (!isset($_SESSION['user_id']) ) {
    header("Location: ../index.php");
    exit();
}

// Database connection
require_once 'config/database.php';

// Initialize message variables
$message = '';
$messageType = '';

// Get all sponsor images
$sponsors = [];
$stmt = $conn->prepare("SELECT * FROM sponsors ORDER BY uploaded_at DESC");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sponsors[] = $row;
    }
}
$stmt->close();
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/menu.php'; ?>
        
        <main class="col-md-12 ">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Sponsors</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fas fa-plus me-1"></i> Upload Sponsors
                    </button>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <?php if (empty($sponsors)): ?>
                    <div class="col-12">
                        <div class="alert alert-info">
                            No sponsor images found. Click the "Upload Sponsors" button to add new ones.
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($sponsors as $sponsor): ?>
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card h-100 sponsor-card">
                                <img src="<?php echo $sponsor['image_path']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($sponsor['original_name']); ?>" style="height: 200px; object-fit: contain; background-color: #f8f9fa;">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title text-truncate"><?php echo htmlspecialchars($sponsor['original_name']); ?></h6>
                                    <p class="card-text small text-muted mb-2">
                                        <?php echo round($sponsor['file_size'] / 1024, 2); ?> KB<br>
                                        <?php echo date('M d, Y H:i', strtotime($sponsor['uploaded_at'])); ?>
                                    </p>
                                    <div class="mt-auto">
                                        <button type="button" class="btn btn-sm btn-danger w-100 delete-sponsor" data-id="<?php echo $sponsor['id']; ?>">
                                            <i class="fas fa-trash me-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Sponsor Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sponsorImages" class="form-label">Select Images</label>
                        <input class="form-control" type="file" id="sponsorImages" name="sponsor_images[]" multiple accept="image/jpeg, image/png, image/webp" required>
                        <div class="form-text">Allowed formats: JPG, PNG, WebP. Maximum file size: 5MB per image.</div>
                    </div>
                    <div id="filePreview" class="row g-2 mb-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // File input change event
    const fileInput = document.getElementById('sponsorImages');
    const filePreview = document.getElementById('filePreview');
    const uploadForm = document.getElementById('uploadForm');
    const uploadModal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
    
    // Show file previews
    fileInput.addEventListener('change', function(e) {
        filePreview.innerHTML = '';
        
        if (this.files.length > 0) {
            Array.from(this.files).forEach(file => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-4';
                        col.innerHTML = `
                            <div class="card">
                                <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <p class="card-text small text-truncate mb-0">${file.name}</p>
                                    <p class="card-text small text-muted mb-0">${(file.size / 1024).toFixed(2)} KB</p>
                                </div>
                            </div>
                        `;
                        filePreview.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
    
    // Handle form submission with AJAX
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        // Disable submit button and show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';
        
        // Show loading message
        const loadingAlert = document.createElement('div');
        loadingAlert.className = 'alert alert-info';
        loadingAlert.innerHTML = 'Uploading files, please wait...';
        filePreview.before(loadingAlert);
        
        // Send AJAX request
        fetch('process/upload_sponsor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert(data.message, 'success');
                // Reload the page to show new uploads
                setTimeout(() => window.location.reload(), 1500);
            } else {
                // Show error message
                const errorMsg = data.errors && data.errors.length > 0 
                    ? data.errors.join('<br>')
                    : data.message;
                showAlert(errorMsg, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while uploading files.', 'danger');
        })
        .finally(() => {
            // Re-enable submit button and restore text
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            loadingAlert.remove();
            
            // Clear file input and preview
            fileInput.value = '';
            filePreview.innerHTML = '';
        });
    });
    
    // Handle delete button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-sponsor')) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete this sponsor image?')) {
                return false;
            }
            
            const deleteBtn = e.target.closest('.delete-sponsor');
            const sponsorId = deleteBtn.dataset.id;
            const card = deleteBtn.closest('.card');
            
            // Show loading state
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            
            // Send delete request
            const formData = new FormData();
            formData.append('id', sponsorId);
            
            fetch('process/delete_sponsor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the card from the DOM
                    card.closest('.col-md-4').remove();
                    showAlert('Sponsor image deleted successfully.', 'success');
                    
                    // If no more sponsors, show empty message
                    if (document.querySelectorAll('.sponsor-card').length === 0) {
                        const emptyMessage = `
                            <div class="col-12">
                                <div class="alert alert-info">
                                    No sponsor images found. Click the "Upload Sponsors" button to add new ones.
                                </div>
                            </div>`;
                        document.querySelector('.row').innerHTML = emptyMessage;
                    }
                } else {
                    throw new Error(data.message || 'Failed to delete sponsor image');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert(error.message || 'An error occurred while deleting the sponsor image.', 'danger');
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '<i class="fas fa-trash me-1"></i> Delete';
            });
        }
    });
    
    // Function to show alert messages
    function showAlert(message, type) {
        // Remove any existing alerts
        const existingAlerts = document.querySelectorAll('.alert-dismissible');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create and show new alert
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert after the page header
        const pageHeader = document.querySelector('.border-bottom');
        if (pageHeader) {
            pageHeader.parentNode.insertBefore(alertDiv, pageHeader.nextSibling);
        } else {
            document.querySelector('main').insertBefore(alertDiv, document.querySelector('main').firstChild);
        }
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertDiv);
            bsAlert.close();
        }, 5000);
    }
});
</script>

<?php include 'includes/footer.php'; ?>
