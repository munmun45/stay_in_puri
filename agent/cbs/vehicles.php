<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit;
}

// Database connection
require_once 'config/database.php';

// Fetch vehicles based on user type
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary') {
    // Primary users can see all vehicles with usernames
    $stmt = $conn->prepare("SELECT v.*, u.username, u.full_name, 
                          creator.username as creator_username, creator.full_name as creator_full_name
                          FROM vehicles v 
                          LEFT JOIN users u ON v.user_id = u.id 
                          LEFT JOIN users creator ON v.created_by = creator.id 
                          ORDER BY v.created_at DESC");
    $stmt->execute();
    $vehicles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    // Non-primary users can only see their own vehicles
    $user_id = $_SESSION['user_id'] ?? 0;
    $stmt = $conn->prepare("SELECT v.*, u.username, u.full_name, 
                          creator.username as creator_username, creator.full_name as creator_full_name
                          FROM vehicles v 
                          LEFT JOIN users u ON v.user_id = u.id 
                          LEFT JOIN users creator ON v.created_by = creator.id 
                          WHERE v.user_id = ? 
                          ORDER BY v.created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $vehicles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Function to format date for display
function formatDate($date) {
    if (empty($date) || $date == '0000-00-00') {
        return '';
    }
    return date('d M Y', strtotime($date));
}

?>

<?php require_once 'includes/header.php'; ?>

<div class="container-fluid bg-light min-vh-100 p-0">
    <div class="row">
        <?php include 'includes/menu.php'; ?>

        <main class=" col">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="mb-0 fw-bold">🚚 Insurance Records</h2>
                </div>
                <div class="d-flex gap-2">
                   
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                        <i class="fas fa-plus me-1"></i> Add New Vehicle
                    </button>
                </div>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); endif; ?>

            <!-- Vehicle Table -->
            <div class="card shadow-sm">
            <div class="card-header bg-primary text-white fw-semibold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-car me-2"></i> Vehicle Insurance Records</span>
                   
                </div>
                <div class="card-body p-0">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0" id="">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Customer Details</th>
                                    <th>Vehicle Details</th>
                                    <th>Policy Details</th>
                                    <th>Agency Details</th>

                                    <th>Amount</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($vehicles as $index => $vehicle): 
                                    $policyStart = formatDate($vehicle['policy_start_date']);
                                    $policyEnd = formatDate($vehicle['policy_end_date']);
                                    $regDate = formatDate($vehicle['reg_date']);
                                    $nominiDob = formatDate($vehicle['nomini_dob']);
                                ?>
                                <tr style="vertical-align: top;">
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($vehicle['customer_name']); ?></div>
                                        <div class="small text-muted">
                                            <i class="fas fa-id-card me-1"></i> <?php echo htmlspecialchars($vehicle['id_number']); ?><br>
                                            <i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($vehicle['customer_number1']); ?>
                                            <?php if (!empty($vehicle['customer_number2'])): ?>
                                                <br><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($vehicle['customer_number2']); ?>
                                            <?php endif; ?>
                                            <?php if (!empty($vehicle['customer_email'])): ?>
                                                <br><i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($vehicle['customer_email']); ?>
                                            <?php endif; ?>
                                            <?php if (!empty($vehicle['company_name'])): ?>
                                                <br><i class="fas fa-building me-1"></i> <?php echo htmlspecialchars($vehicle['company_name']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($vehicle['vehicle_number']); ?></div>
                                        <div class="small text-muted">
                                            <?php if (!empty($vehicle['make_model'])): ?>
                                                <i class="fas fa-car me-1"></i> <?php echo htmlspecialchars($vehicle['make_model']); ?><br>
                                            <?php endif; ?>
                                            <?php if (!empty($regDate)): ?>
                                                <i class="far fa-calendar-alt me-1"></i> Reg: <?php echo $regDate; ?><br>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if (!empty($vehicle['policy_no'])): ?>
                                            <div class="fw-bold"><?php echo htmlspecialchars($vehicle['policy_no']); ?></div>
                                        <?php endif; ?>
                                        <div class="small text-muted">
                                            <?php if (!empty($vehicle['policy_company'])): ?>
                                                <i class="fas fa-building me-1"></i> <?php echo htmlspecialchars($vehicle['policy_company']); ?><br>
                                            <?php endif; ?>
                                            <?php if ($policyStart && $policyEnd): ?>
                                                <i class="far fa-calendar me-1"></i> <?php echo $policyStart . ' to ' . $policyEnd; ?><br>
                                            <?php endif; ?>
                                            <?php if (!empty($vehicle['gst_number'])): ?>
                                                <i class="fas fa-receipt me-1"></i> GST: <?php echo htmlspecialchars($vehicle['gst_number']); ?><br>
                                            <?php endif; ?>
                                            <?php 
                                            $statusClass = '';
                                            $statusText = '';
                                            $currentDate = new DateTime();
                                            
                                            if (empty($vehicle['policy_end_date']) || $vehicle['policy_end_date'] == '0000-00-00') {
                                                $statusClass = 'text-warning';
                                                $statusText = 'Pending';
                                            } else {
                                                $endDate = new DateTime($vehicle['policy_end_date']);
                                                if ($endDate < $currentDate) {
                                                    $statusClass = 'text-danger';
                                                    $statusText = 'Expired';
                                                } else {
                                                    $statusClass = 'text-success';
                                                    $statusText = 'Active';
                                                }
                                            }
                                            ?>
                                            <div class="mt-2">
                                                <span class="badge <?php echo $statusClass; ?> px-3 py-2" style="font-size: 0.9rem; font-weight: 600; letter-spacing: 0.5px;">
                                                    <i class="fas fa-circle me-2"></i> Status: <?php echo $statusText; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                    <div class="fw-bold">Agency: <?php echo !empty($vehicle['full_name']) ? htmlspecialchars($vehicle['full_name']) : 'N/A'; ?></div>
                                    <div class="small text-muted">@<?php echo !empty($vehicle['username']) ? htmlspecialchars($vehicle['username']) : 'N/A'; ?></div>
                                        <div class="small text-muted">
                                            <?php if (!empty($vehicle['policy_company'])): ?>
                                                Created At: <?php echo htmlspecialchars($vehicle['created_at']); ?><br>
                                                <?php 
                                                $createdBy = !empty($vehicle['creator_full_name']) ? 
                                                    htmlspecialchars($vehicle['creator_full_name']) . 
                                                    (!empty($vehicle['creator_username']) ? ' (@' . htmlspecialchars($vehicle['creator_username']) . ')' : '') : 
                                                    'N/A';
                                                ?>
                                                Created By: <?php echo $createdBy; ?>
                                            <?php endif; ?>
                                            
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <?php if (!empty($vehicle['net_amount'])): ?>
                                            <div class="fw-bold">₹<?php echo number_format($vehicle['net_amount'], 2); ?></div>
                                            <?php if (!empty($vehicle['gross_amount'])): ?>
                                                <small class="text-muted">Gross: ₹<?php echo number_format($vehicle['gross_amount'], 2); ?></small><br>
                                            <?php endif; ?>
                                            <?php if (!empty($vehicle['commission'])): ?>
                                                <small class="text-muted">Comm: ₹<?php echo number_format($vehicle['commission'], 2); ?></small>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <div class=" btn-group-sm" style="
                                                display: flex;
                                                flex-direction: column;
                                                gap: 10px;
                                                align-items: center;
                                                justify-content: center;
                                                align-content: center;
                                            ">
                                            <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>
                                            <button type="button" class="btn btn-primary edit-vehicle" 
                                                data-id="<?php echo $vehicle['id']; ?>"
                                                data-bs-placement="top" 
                                                title="Edit Record"
                                                data-bs-target="#editVehicleModal" 
                                                data-record='<?php echo htmlspecialchars(json_encode($vehicle), ENT_QUOTES, 'UTF-8'); ?>'>
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php endif; ?>
                                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                            <button type="button" 
                                                class="btn btn-danger" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="Delete Record"
                                                onclick="confirmDelete(<?php echo $vehicle['id']; ?>, '<?php echo htmlspecialchars(addslashes($vehicle['customer_name'] . ' - ' . $vehicle['vehicle_number'])); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                            <button type="button" 
                                                class="btn btn-info text-white" 
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top" 
                                                title="View Details"
                                                onclick="viewDetails(<?php echo htmlspecialchars(json_encode($vehicle)); ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (empty($vehicles)): ?>
                            <p class="text-muted text-center my-3">No vehicle insurance records found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>


<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVehicleModalLabel">Add New Vehicle Insurance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="./process/add_vehicles.php" class="needs-validation" enctype="multipart/form-data" novalidate onsubmit="return validateForm(this);">
                    <div class="row">
                        <?php
                        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary') {
                            // For primary users, show dropdown of all users
                            $userStmt = $conn->prepare("SELECT id, full_name, username FROM users WHERE role = 'user' AND status = 'active' ORDER BY full_name");
                            $userStmt->execute();
                            $users = $userStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            $userStmt->close();
                            ?>
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">Select User <span class="text-danger">*</span></label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">-- Select User --</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo htmlspecialchars($user['id']); ?>">
                                            <?php echo htmlspecialchars($user['full_name'] . ' (' . $user['username'] . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a user.
                                </div>
                            </div>
                            <?php
                        } else {
                            // For non-primary users, show their own info as read-only
                            $userStmt = $conn->prepare("SELECT id, full_name, username FROM users WHERE id = ?");
                            $userStmt->bind_param("i", $_SESSION['user_id']);
                            $userStmt->execute();
                            $user = $userStmt->get_result()->fetch_assoc();
                            $userStmt->close();
                            ?>
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">User</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name'] . ' (' . $user['username'] . ')'); ?>" readonly>
                                <input type="hidden" id="user_id" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                            </div>
                            <?php
                        }
                        ?>
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>
                        <div class="col-md-6 mb-3" style="display: none;">
                            <label for="id_number" class="form-label">ID Number</label>
                            <input type="text" class="form-control" id="id_number" name="id_number">
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6 mb-3">
                            <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                            <div class="invalid-feedback">Please enter customer name.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="customer_number1" class="form-label">Customer Number 1 <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="customer_number1" name="customer_number1" 
                                pattern="[0-9]{10}" required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                title="Please enter exactly 10 digits">
                            <div class="invalid-feedback">Please enter a valid 10-digit phone number.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="customer_number2" class="form-label">Customer Number 2 (Optional)</label>
                            <input type="tel" class="form-control" id="customer_number2" name="customer_number2"
                                pattern="[0-9]{10}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                title="Please enter exactly 10 digits">
                            <div class="form-text">Enter 10-digit number only</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="customer_email" class="form-label">Customer Email ID (Optional)</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email"
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            <div class="form-text">example@domain.com</div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="current_address" class="form-label">Current Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="current_address" name="current_address" rows="2" required></textarea>
                            <div class="invalid-feedback">Please enter current address.</div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="permanent_address" class="form-label">Permanent Address (Same as current)</label>
                            <textarea class="form-control" id="permanent_address" name="permanent_address" rows="2"></textarea>
                            <div class="form-text">Leave blank if same as current address</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="postal_pin" class="form-label">Postal Pin <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="postal_pin" name="postal_pin" 
                                pattern="[0-9]{6}" required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                            <div class="invalid-feedback">Please enter a valid 6-digit PIN code.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nomini_name" class="form-label">Nomini Name</label>
                            <input type="text" class="form-control" id="nomini_name" name="nomini_name">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="nomini_dob" class="form-label">Nomini DOB</label>
                            <input type="date" class="form-control" id="nomini_dob" name="nomini_dob">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gst_number" class="form-label">GST Number</label>
                            <input type="text" class="form-control" id="gst_number" name="gst_number">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="vehicle_number" class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" required>
                            <div class="invalid-feedback">Please enter vehicle number</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="make_model" class="form-label">Make/Model <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="make_model" name="make_model" required>
                            <div class="invalid-feedback">Please enter make and model</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="reg_date" class="form-label">Registration Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="reg_date" name="reg_date" required>
                            <div class="invalid-feedback">Please select registration date</div>
                        </div>
                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>

                        <div class="col-md-6 mb-3">
                            <label for="policy_no" class="form-label">Policy No <?php echo ($_SESSION['user_type'] === 'primary') ? '<span class="text-danger">*</span>' : ''; ?></label>
                            <input type="text" class="form-control" id="policy_no" name="policy_no" 
                                <?php echo ($_SESSION['user_type'] === 'primary') ? 'required pattern="[A-Za-z0-9/-]+"' : ''; ?>>
                            <?php if ($_SESSION['user_type'] === 'primary'): ?>
                            <div class="invalid-feedback">Please enter a valid policy number.</div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <div class="col-md-6 mb-3">
                            <label for="policy_company" class="form-label">Policy Company <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="policy_company" name="policy_company" required>
                            <div class="invalid-feedback">Please enter the insurance company name.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="policy_start_date" class="form-label">Policy Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="policy_start_date" name="policy_start_date" required>
                            <div class="invalid-feedback">Please select a start date</div>
                        </div>
                        
                        

                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>

                        <div class="col-md-6 mb-3">
                            <label for="policy_end_date" class="form-label">Policy End Date</label>
                            <input type="date" class="form-control" id="policy_end_date" name="policy_end_date">
                            <div class="form-text">Optional end date</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="gross_amount" class="form-label">Gross Amount (₹) <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?><span class="text-danger">*</span><?php endif; ?></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="gross_amount" 
                                    name="gross_amount" <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary') echo 'required'; ?>>
                            </div>
                            <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>
                            <div class="invalid-feedback">Please enter a valid amount.</div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="net_amount" class="form-label">Net Amount (₹) <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?><span class="text-danger">*</span><?php endif; ?></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="net_amount" 
                                    name="net_amount" <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary') echo 'required'; ?>>
                            </div>
                            <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>
                            <div class="invalid-feedback">Please enter a valid amount.</div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="commission" class="form-label">Commission (₹) <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?><span class="text-danger">*</span><?php endif; ?></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="commission" 
                                    name="commission" <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary') echo 'required'; ?>>
                            </div>
                            <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>
                            <div class="invalid-feedback">Please enter a valid commission amount.</div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>


                    </div>

                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label for="document" class="form-label">Upload Document (JPG, JPEG, PNG, PDF - Max 5MB)</label>
                            <input type="file" class="form-control" id="document" name="document" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">Max file size: 5MB. Allowed formats: JPG, JPEG, PNG, PDF</div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Apply Vehicle Insurance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-labelledby="editVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" action="./process/update_vehicles.php" class="needs-validation" enctype="multipart/form-data" novalidate onsubmit="return validateForm(this);">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-header">
                    <h5 class="modal-title" id="editVehicleModalLabel">Edit Vehicle Insurance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <?php if (!isset($users)) {
                            // Fetch users with role 'user' for the dropdown if not already fetched
                            $userStmt = $conn->prepare("SELECT id, full_name, username FROM users WHERE role = 'user' AND status = 'active' ORDER BY full_name");
                            $userStmt->execute();
                            $users = $userStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            $userStmt->close();
                        } ?>
                        <div class="col-md-6 mb-3">
                            <label for="edit_user_id" class="form-label">Select User <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_user_id" name="user_id" required>
                                <option value="">-- Select User --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo htmlspecialchars($user['id']); ?>">
                                        <?php echo htmlspecialchars($user['full_name'] . ' (' . $user['username'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Please select a user.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3" style="display: none;">
                            <label for="edit_id_number" class="form-label">ID Number</label>
                            <input type="text" class="form-control" id="edit_id_number" name="id_number">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_customer_name" name="customer_name" required>
                            <div class="invalid-feedback">Please enter customer name.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_customer_number1" class="form-label">Customer Number 1 <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="edit_customer_number1" name="customer_number1" 
                                pattern="[0-9]{10}" required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                title="Please enter exactly 10 digits">
                            <div class="invalid-feedback">Please enter a valid 10-digit phone number.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_customer_number2" class="form-label">Customer Number 2 (Optional)</label>
                            <input type="tel" class="form-control" id="edit_customer_number2" name="customer_number2"
                                pattern="[0-9]{10}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)"
                                title="Please enter exactly 10 digits">
                            <div class="form-text">Enter 10-digit number only</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_customer_email" class="form-label">Customer Email ID (Optional)</label>
                            <input type="email" class="form-control" id="edit_customer_email" name="customer_email"
                                pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                            <div class="form-text">example@domain.com</div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="edit_current_address" class="form-label">Current Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="edit_current_address" name="current_address" rows="2" required></textarea>
                            <div class="invalid-feedback">Please enter current address.</div>
                        </div>

                        <div class="col-12 mb-3">
                            <label for="edit_permanent_address" class="form-label">Permanent Address (Same as current)</label>
                            <textarea class="form-control" id="edit_permanent_address" name="permanent_address" rows="2"></textarea>
                            <div class="form-text">Leave blank if same as current address</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_postal_pin" class="form-label">Postal Pin <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_postal_pin" name="postal_pin" 
                                pattern="[0-9]{6}" required
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                            <div class="invalid-feedback">Please enter a valid 6-digit PIN code.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_nomini_name" class="form-label">Nomini Name</label>
                            <input type="text" class="form-control" id="edit_nomini_name" name="nomini_name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_nomini_dob" class="form-label">Nomini DOB</label>
                            <input type="date" class="form-control" id="edit_nomini_dob" name="nomini_dob">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_company_name" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="edit_company_name" name="company_name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_gst_number" class="form-label">GST Number</label>
                            <input type="text" class="form-control" id="edit_gst_number" name="gst_number">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_vehicle_number" class="form-label">Vehicle Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_vehicle_number" name="vehicle_number" required>
                            <div class="invalid-feedback">Please enter vehicle number</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_make_model" class="form-label">Make/Model <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_make_model" name="make_model" required>
                            <div class="invalid-feedback">Please enter make and model</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_reg_date" class="form-label">Registration Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_reg_date" name="reg_date" required>
                            <div class="invalid-feedback">Please select registration date</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_policy_no" class="form-label">Policy No <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_policy_no" name="policy_no" required
                                pattern="[A-Za-z0-9/-]+">
                            <div class="invalid-feedback">Please enter a valid policy number.</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_policy_company" class="form-label">Policy Company <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_policy_company" name="policy_company" required>
                            <div class="invalid-feedback">Please enter the insurance company name.</div>
                        </div>

                        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'primary'): ?>
                        <div class="col-md-6 mb-3">
                            <label for="edit_policy_start_date" class="form-label">Policy Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_policy_start_date" name="policy_start_date" required>
                            <div class="invalid-feedback">Please select a start date</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="edit_policy_end_date" class="form-label">Policy End Date</label>
                            <input type="date" class="form-control" id="edit_policy_end_date" name="policy_end_date">
                            <div class="form-text">Optional end date</div>
                        </div>
                        <?php else: ?>
                        <input type="hidden" id="edit_policy_start_date" name="policy_start_date" value="">
                        <input type="hidden" id="edit_policy_end_date" name="policy_end_date" value="">
                        <?php endif; ?>

                        <div class="col-md-4 mb-3">
                            <label for="edit_gross_amount" class="form-label">Gross Amount (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_gross_amount" 
                                    name="gross_amount" required>
                            </div>
                            <div class="invalid-feedback">Please enter a valid amount.</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="edit_net_amount" class="form-label">Net Amount (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_net_amount" 
                                    name="net_amount" required>
                            </div>
                            <div class="invalid-feedback">Please enter a valid amount.</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="edit_commission" class="form-label">Commission (₹) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="edit_commission" 
                                    name="commission" required>
                            </div>
                            <div class="invalid-feedback">Please enter a valid commission amount.</div>
                        </div>
                    </div>
                </div>

                <!-- Document Upload -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label for="edit_document" class="form-label">Update Document (JPG, JPEG, PNG, PDF - Max 5MB)</label>
                        <input type="file" class="form-control" id="edit_document" name="document" accept=".jpg,.jpeg,.png,.pdf">
                        <div class="form-text">Leave empty to keep existing document. Supported formats: JPG, JPEG, PNG, PDF. Maximum file size: 5MB</div>
                        <div id="currentDocument" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detailsModalLabel">Vehicle Insurance Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Dynamic content will be loaded here -->
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
function confirmDelete(vehicleId) {
    if (confirm('Are you sure you want to delete this vehicle?')) {
        window.location.href = './process/delete_vehicles.php?id=' + vehicleId;
    }
}
</script>


<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>
<script>
// Initialize DataTable with responsive and export options
$(document).ready(function() {
    $('#vehiclesTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true,
        dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records...",
            lengthMenu: "Show _MENU_ records per page",
            zeroRecords: "No matching records found",
            info: "Showing _START_ to _END_ of _TOTAL_ records",
            infoEmpty: "No records available",
            infoFiltered: "(filtered from _MAX_ total records)",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        },
        columnDefs: [
            { orderable: false, targets: -1 }, // Disable sorting on actions column
            { responsivePriority: 1, targets: 1 }, // Customer Details
            { responsivePriority: 2, targets: 2 }, // Vehicle Details
            { responsivePriority: 3, targets: 3 }, // Policy Details
            { responsivePriority: 4, targets: 0 }, // #
            { responsivePriority: 5, targets: 4 }  // Amount
        ]
    });
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// View Details function
function viewDetails(vehicle) {
    // Format dates
    const formatDate = (dateStr) => {
        if (!dateStr) return 'N/A';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
    };

    // Format currency
    const formatCurrency = (amount) => {
        if (amount === null || amount === undefined) return 'N/A';
        return new Intl.NumberFormat('en-IN', { 
            style: 'currency', 
            currency: 'INR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2 
        }).format(amount);
    };

    // Create details HTML
    let detailsHtml = `
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">Customer Information</h5>
                <table class="table table-sm">
                    <tr>
                        <th style="width: 40%;">Customer Name:</th>
                        <td>${vehicle.customer_name || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Contact Numbers:</th>
                        <td>
                            ${vehicle.customer_number1 || ''}
                            ${vehicle.customer_number2 ? '<br>' + vehicle.customer_number2 : ''}
                        </td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td>${vehicle.customer_email || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Current Address:</th>
                        <td>${vehicle.current_address || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Permanent Address:</th>
                        <td>${vehicle.permanent_address || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Postal Pin:</th>
                        <td>${vehicle.postal_pin || 'N/A'}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">Vehicle & Policy Details</h5>
                <table class="table table-sm">
                    <tr>
                        <th style="width: 40%;">Vehicle Number:</th>
                        <td>${vehicle.vehicle_number || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Make/Model:</th>
                        <td>${vehicle.make_model || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Registration Date:</th>
                        <td>${formatDate(vehicle.reg_date)}</td>
                    </tr>
                    <tr>
                        <th>Policy Number:</th>
                        <td>${vehicle.policy_no || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Policy Company:</th>
                        <td>${vehicle.policy_company || 'N/A'}</td>
                    </tr>
                    <tr>
                        <th>Policy Period:</th>
                        <td>${formatDate(vehicle.policy_start_date)} to ${formatDate(vehicle.policy_end_date)}</td>
                    </tr>
                </table>
                
                <h5 class="border-bottom pb-2 mb-3 mt-4">Financial Details</h5>
                <table class="table table-sm">
                    <tr>
                        <th style="width: 40%;">Gross Amount:</th>
                        <td>${formatCurrency(vehicle.gross_amount)}</td>
                    </tr>
                    <tr>
                        <th>Net Amount:</th>
                        <td class="fw-bold">${formatCurrency(vehicle.net_amount)}</td>
                    </tr>
                    <tr>
                        <th>Commission:</th>
                        <td>${formatCurrency(vehicle.commission)}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>`;

    // Show in modal
    const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
    document.getElementById('detailsModalLabel').textContent = `Details - ${vehicle.vehicle_number || 'Vehicle'}`;
    document.getElementById('modalBody').innerHTML = detailsHtml;
    
    // Initialize any tooltips in the modal
    const modalTooltips = [].slice.call(document.querySelectorAll('#detailsModal [data-bs-toggle="tooltip"]'));
    modalTooltips.map(function(tooltipEl) {
        return new bootstrap.Tooltip(tooltipEl);
    });
    
    // Initialize document preview if available
    if (vehicle.document_path) {
        const fileExtension = vehicle.document_path.split('.').pop().toLowerCase();
        const previewContainer = document.createElement('div');
        previewContainer.className = 'mt-3';
        
        if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            // Image preview
            previewContainer.innerHTML = `
                <div class="border p-2 rounded">
                    <p class="small text-muted mb-2">Document Preview:</p>
                    <img src="${vehicle.document_path}" class="img-fluid" style="max-height: 300px;" alt="Document Preview">
                </div>`;
        } else if (fileExtension === 'pdf') {
            // PDF preview using PDF.js or show PDF icon with link
            previewContainer.innerHTML = `
                <div class="border p-2 rounded bg-light">
                    <p class="small text-muted mb-2">PDF Document</p>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-pdf text-danger me-2" style="font-size: 3rem;"></i>
                        <div>
                            <p class="mb-1">${vehicle.document_path.split('/').pop()}</p>
                            <a href="${vehicle.document_path}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i> Open in new tab
                            </a>
                        </div>
                    </div>
                </div>`;
        } else {
            // Generic file preview with download button
            previewContainer.innerHTML = `
                <div class="border p-2 rounded bg-light">
                    <p class="small text-muted mb-2">Document</p>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-file-alt text-secondary me-2" style="font-size: 3rem;"></i>
                        <div>
                            <p class="mb-1">${vehicle.document_path.split('/').pop()}</p>
                            <a href="${vehicle.document_path}" download class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>`;
        }
        
        document.querySelector('#detailsModal .modal-body').querySelector('.row:last-child').after(previewContainer);
    }
    
    modal.show();
}

// Initialize tooltips and edit modal
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle edit button clicks
    document.querySelectorAll('.edit-vehicle').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (!id) return;
            
            // Show loading state
            const editModal = new bootstrap.Modal(document.getElementById('editVehicleModal'));
            
            // Fetch vehicle details via AJAX
            fetch(`./process/get_vehicle.php?id=${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(vehicle => {
                    // Populate form fields
                    document.getElementById('edit_id').value = vehicle.id || '';
                    document.getElementById('edit_user_id').value = vehicle.user_id || '';
                    document.getElementById('edit_id_number').value = vehicle.id_number || '';
                    document.getElementById('edit_customer_name').value = vehicle.customer_name || '';
                    document.getElementById('edit_customer_number1').value = vehicle.customer_number1 || '';
                    document.getElementById('edit_customer_number2').value = vehicle.customer_number2 || '';
                    document.getElementById('edit_customer_email').value = vehicle.customer_email || '';
                    document.getElementById('edit_current_address').value = vehicle.current_address || '';
                    document.getElementById('edit_permanent_address').value = vehicle.permanent_address || '';
                    document.getElementById('edit_postal_pin').value = vehicle.postal_pin || '';
                    document.getElementById('edit_nomini_name').value = vehicle.nomini_name || '';
                    document.getElementById('edit_nomini_dob').value = vehicle.nomini_dob ? vehicle.nomini_dob.split(' ')[0] : '';
                    document.getElementById('edit_company_name').value = vehicle.company_name || '';
                    document.getElementById('edit_gst_number').value = vehicle.gst_number || '';
                    document.getElementById('edit_vehicle_number').value = vehicle.vehicle_number || '';
                    document.getElementById('edit_make_model').value = vehicle.make_model || '';
                    document.getElementById('edit_reg_date').value = vehicle.reg_date ? vehicle.reg_date.split(' ')[0] : '';
                    document.getElementById('edit_policy_no').value = vehicle.policy_no || '';
                    document.getElementById('edit_policy_company').value = vehicle.policy_company || '';
                    document.getElementById('edit_policy_start_date').value = vehicle.policy_start_date ? vehicle.policy_start_date.split(' ')[0] : '';
                    document.getElementById('edit_policy_end_date').value = vehicle.policy_end_date ? vehicle.policy_end_date.split(' ')[0] : '';
                    document.getElementById('edit_gross_amount').value = vehicle.gross_amount || '0.00';
                    document.getElementById('edit_net_amount').value = vehicle.net_amount || '0.00';
                    document.getElementById('edit_commission').value = vehicle.commission || '0.00';
                    
                    // Show the modal after populating fields
                    editModal.show();
                })
                .catch(error => {
                    console.error('Error fetching vehicle details:', error);
                    alert('Error loading vehicle details. Please try again.');
                });

            // Handle document preview if exists
            const currentDocumentDiv = document.getElementById('currentDocument');
            if (record.document_path) {
                const fileExt = record.document_path.split('.').pop().toLowerCase();
                let previewHtml = '<div class="alert alert-info p-2 mb-0">';
                previewHtml += '<i class="fas fa-paperclip me-2"></i> Current Document: ';
                
                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                    previewHtml += `<a href="${record.document_path}" target="_blank" class="text-primary">View Image</a>`;
                } else if (fileExt === 'pdf') {
                    previewHtml += `<a href="${record.document_path}" target="_blank" class="text-primary">View PDF</a>`;
                } else {
                    previewHtml += `<a href="${record.document_path}" target="_blank" class="text-primary">Download File</a>`;
                }
                
                previewHtml += ' <small class="text-muted">(Upload a new file to replace this document)</small>';
                previewHtml += '</div>';
                currentDocumentDiv.innerHTML = previewHtml;
            } else {
                currentDocumentDiv.innerHTML = '<div class="alert alert-warning p-2 mb-0">No document uploaded yet</div>';
            }

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('editVehicleModal'));
            modal.show();
        });
    });
});

// Form validation
function validateForm(form) {
    'use strict';
    
    // Check if form is valid
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return false;
    }
    
    // Additional custom validations
    const startDate = new Date(document.getElementById('policy_start_date').value);
    const endDate = new Date(document.getElementById('policy_end_date').value);
    
    if (endDate <= startDate) {
        alert('Policy end date must be after start date');
        return false;
    }
    
    // Auto-fill permanent address if empty
    const currentAddress = document.getElementById('current_address').value.trim();
    const permanentAddress = document.getElementById('permanent_address');
    
    if (permanentAddress.value.trim() === '' && currentAddress !== '') {
        permanentAddress.value = currentAddress;
    }
    
    return true;
}

// Auto-copy current address to permanent address if empty
document.getElementById('current_address').addEventListener('blur', function() {
    const currentAddress = this.value.trim();
    const permanentAddress = document.getElementById('permanent_address');
    
    if (currentAddress !== '' && permanentAddress.value.trim() === '') {
        permanentAddress.value = currentAddress;
    }
});


// Delete confirmation
function confirmDelete(id, name) {
    if (confirm('Are you sure you want to delete this vehicle insurance record for ' + name + '?')) {
        window.location.href = './process/delete_vehicles.php?id=' + id;
    }
}
</script>

