<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Database connection
require_once 'config/database.php';

// Fetch existing users (excluding admin)
$users = [];
$stmt = $conn->prepare("SELECT * FROM users WHERE role != 'admin' ORDER BY username");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
$stmt->close();

?>

<?php require_once 'includes/header.php'; ?>

<div class="container-fluid bg-light min-vh-100 p-0">
    <div class="row">
        <?php include 'includes/menu.php'; ?>

        <main class=" col">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-0 fw-bold">👤 Users Management</h2>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus me-1"></i> Add New User
                    </button>
                </div>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); endif; ?>

            <!-- Users Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-semibold">
                    Users List
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>User Type</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo ucfirst(htmlspecialchars($user['user_type'] ?? 'primary')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($user['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-info btn-view" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewUserModal"
                                                data-user='<?php echo json_encode($user); ?>'>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                                data-user-id="<?php echo $user['id']; ?>" 
                                                data-username="<?php echo htmlspecialchars($user['username']); ?>" 
                                                data-email="<?php echo htmlspecialchars($user['email']); ?>" 
                                                data-phone="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                                data-address="<?php echo htmlspecialchars($user['address']); ?>" 
                                                data-status="<?php echo htmlspecialchars($user['status']); ?>"
                                                data-full-name="<?php echo htmlspecialchars($user['full_name']); ?>"
                                                data-user-type="<?php echo htmlspecialchars($user['user_type'] ?? 'primary'); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <button type="button" class="btn btn-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (empty($users)): ?>
                            <p class="text-muted text-center my-3">No users found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="./process/add_users.php" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                            <div class="invalid-feedback">Please enter full name.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="mobile" name="mobile" required>
                            <div class="invalid-feedback">Please enter mobile number.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" readonly required>
                            <small class="form-text text-muted">Username is auto-generated</small>
                            <div class="invalid-feedback">Please enter a username.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>



                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">Please enter a password.</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback">Please select a status.</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="user_type" class="form-label">User Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="user_type" name="user_type" required>
                                <option value="" selected disabled>Select user type</option>
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                            </select>
                            <div class="invalid-feedback">Please select a user type.</div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Add User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="./process/update_users.php" class="needs-validation" novalidate>
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                            <div class="invalid-feedback">Please enter full name.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                            <div class="invalid-feedback">Please enter phone number.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_username" name="username" readonly required>
                            <small class="form-text text-muted">Username is auto-generated</small>
                            <div class="invalid-feedback">Please enter a username.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="edit_address" class="form-label">Address</label>
                            <textarea class="form-control" id="edit_address" name="address" rows="3"></textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="edit_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="edit_password" name="password" placeholder="Leave blank to keep current password">
                            <div class="invalid-feedback">Please enter a password.</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="">Select status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            <div class="invalid-feedback">Please select a status.</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="edit_user_type" class="form-label">User Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_user_type" name="user_type" required>
                                <option value="">Select user type</option>
                                <option value="primary">Primary</option>
                                <option value="secondary">Secondary</option>
                            </select>
                            <div class="invalid-feedback">Please select a user type.</div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View User Modal -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewUserModalLabel"><i class="fas fa-user-circle me-2"></i>User Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="mb-4">
                            <h6 class="text-muted small mb-1">Full Name</h6>
                            <p id="view-full-name" class="mb-0 fw-bold"></p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted small mb-1">Username</h6>
                            <p id="view-username" class="mb-0"></p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted small mb-1">Email</h6>
                            <p id="view-email" class="mb-0"></p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted small mb-1">Phone</h6>
                            <p id="view-phone" class="mb-0"></p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="mb-4">
                            <h6 class="text-muted small mb-1">Address</h6>
                            <p id="view-address" class="mb-0"></p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted small mb-1">User Type</h6>
                            <p id="view-user-type" class="mb-0"></p>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted small mb-1">Status</h6>
                            <p id="view-status" class="mb-0"></p>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script>
// Form validation
(function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
})()

// Delete confirmation
function confirmDelete(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        window.location.href = './process/delete_users.php?id=' + userId;
    }
}
</script>

<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        window.location.href = './process/delete_users.php?id=' + userId;
    }
}

// Remove duplicate edit button event listener
// Function to generate username from full name and phone
function generateUsername(fullName, phone) {
    // Take first 4 characters of the full name (or all if less than 4)
    let namePart = fullName.replace(/\s+/g, '').toLowerCase().substring(0, 4);
    
    // Take last 4 digits of phone (or all if less than 4)
    let phonePart = phone.replace(/\D/g, ''); // Remove non-digits
    phonePart = phonePart.slice(-4); // Take last 4 digits
    
    // If phone is less than 4 digits, pad with zeros
    while (phonePart.length < 4) {
        phonePart = '0' + phonePart;
    }
    
    return namePart + phonePart;
}

// View user details modal
var viewUserModal = document.getElementById('viewUserModal');
if (viewUserModal) {
    viewUserModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var userData = JSON.parse(button.getAttribute('data-user'));
        
        // Format date if exists
        const formatDate = (dateString) => {
            if (!dateString || dateString === '0000-00-00 00:00:00') return 'Never';
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('en-US', options);
        };
        
        // Populate modal fields
        document.getElementById('view-full-name').textContent = userData.full_name || 'N/A';
        document.getElementById('view-username').textContent = userData.username || 'N/A';
        document.getElementById('view-email').textContent = userData.email || 'N/A';
        document.getElementById('view-phone').textContent = userData.phone || 'N/A';
        document.getElementById('view-address').textContent = userData.address || 'N/A';
        
        const userType = document.getElementById('view-user-type');
        userType.textContent = userData.user_type ? userData.user_type.charAt(0).toUpperCase() + userData.user_type.slice(1) : 'Primary';
        
        const status = document.getElementById('view-status');
        status.innerHTML = userData.status === 'active' 
            ? '<span class="badge bg-success">Active</span>' 
            : '<span class="badge bg-secondary">' + (userData.status ? userData.status.charAt(0).toUpperCase() + userData.status.slice(1) : 'Inactive') + '</span>';
            
        document.getElementById('view-last-login').textContent = formatDate(userData.last_login || '');
    });
}

document.addEventListener("DOMContentLoaded", function () {
    // Auto-generate username when full name or phone changes
    const fullNameInput = document.getElementById('full_name');
    const phoneInput = document.getElementById('mobile'); // Keep ID as 'mobile' to match form field
    const usernameInput = document.getElementById('username');
    
    function updateUsername() {
        const fullName = fullNameInput.value.trim();
        const phone = phoneInput.value.trim();
        
        if (fullName && phone) {
            usernameInput.value = generateUsername(fullName, phone);
        }
    }
    
    if (fullNameInput && phoneInput && usernameInput) {
        fullNameInput.addEventListener('input', updateUsername);
        phoneInput.addEventListener('input', updateUsername);
    }
    
    // Handle edit modal population
    var editModal = document.getElementById('editUserModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const username = button.getAttribute('data-username');
            const email = button.getAttribute('data-email');
            const phone = button.getAttribute('data-phone');
            const address = button.getAttribute('data-address');
            const status = button.getAttribute('data-status');
            const userType = button.getAttribute('data-user-type');
            // Get full name from the first column of the table row
            const fullName = button.getAttribute('data-full-name');
            
            // Update form fields
            document.getElementById('edit_id').value = userId || '';
            document.getElementById('edit_full_name').value = fullName || '';
            document.getElementById('edit_username').value = username || '';
            document.getElementById('edit_phone').value = phone || '';
            document.getElementById('edit_email').value = email || '';
            document.getElementById('edit_address').value = address || '';
            
            // Set status and user type
            if (status) {
                document.getElementById('edit_status').value = status;
            }
            if (userType) {
                document.getElementById('edit_user_type').value = userType;
            } else {
                document.getElementById('edit_user_type').value = 'primary'; // Default value
            }
        });
    }
});
</script>
