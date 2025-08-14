<?php include 'includes/header.php'; ?>

<div class="register-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="register-card">
                    <div class="register-header text-center">
                        <div class="register-icon travel-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h2 class="mb-2">Travel Agency Registration</h2>
                        <p class="text-muted">Register your travel agency with Stay in Puri</p>
                    </div>
                    
                    <form class="register-form" id="travelRegisterForm">
                        <!-- Personal Information -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ownerName" class="form-label">Owner/Manager Name *</label>
                                    <input type="text" class="form-control" id="ownerName" name="ownerName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mobile" class="form-label">Mobile Number *</label>
                                    <input type="tel" class="form-control" id="mobile" name="mobile" 
                                           pattern="[0-9]{10}" maxlength="10" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Agency Information -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-building me-2"></i>Agency Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="agencyName" class="form-label">Agency Name *</label>
                                    <input type="text" class="form-control" id="agencyName" name="agencyName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="licenseNumber" class="form-label">License Number</label>
                                    <input type="text" class="form-control" id="licenseNumber" name="licenseNumber">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="establishedYear" class="form-label">Established Year</label>
                                    <input type="number" class="form-control" id="establishedYear" name="establishedYear" 
                                           min="1950" max="2024">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="teamSize" class="form-label">Team Size</label>
                                    <select class="form-select" id="teamSize" name="teamSize">
                                        <option value="">Select Size</option>
                                        <option value="1-5">1-5 People</option>
                                        <option value="6-15">6-15 People</option>
                                        <option value="16-50">16-50 People</option>
                                        <option value="50+">50+ People</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Services Offered -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-concierge-bell me-2"></i>Services Offered
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pilgrimage" name="services[]" value="pilgrimage">
                                        <label class="form-check-label" for="pilgrimage">Pilgrimage Tours</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="heritage" name="services[]" value="heritage">
                                        <label class="form-check-label" for="heritage">Heritage Tours</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="beach" name="services[]" value="beach">
                                        <label class="form-check-label" for="beach">Beach Tours</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="wildlife" name="services[]" value="wildlife">
                                        <label class="form-check-label" for="wildlife">Wildlife Tours</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="adventure" name="services[]" value="adventure">
                                        <label class="form-check-label" for="adventure">Adventure Activities</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="transport" name="services[]" value="transport">
                                        <label class="form-check-label" for="transport">Transportation</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="accommodation" name="services[]" value="accommodation">
                                        <label class="form-check-label" for="accommodation">Hotel Booking</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="guide" name="services[]" value="guide">
                                        <label class="form-check-label" for="guide">Tour Guide Services</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location Information -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-map-marker-alt me-2"></i>Location Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City *</label>
                                    <select class="form-select" id="city" name="city" required>
                                        <option value="">Select City</option>
                                        <option value="puri">Puri</option>
                                        <option value="bhubaneswar">Bhubaneswar</option>
                                        <option value="cuttack">Cuttack</option>
                                        <option value="konark">Konark</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="area" class="form-label">Area/Locality *</label>
                                    <input type="text" class="form-control" id="area" name="area" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Office Address *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                        
                        <!-- Coverage Areas -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-globe me-2"></i>Coverage Areas
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="puri-area" name="coverage[]" value="puri">
                                        <label class="form-check-label" for="puri-area">Puri</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bhubaneswar-area" name="coverage[]" value="bhubaneswar">
                                        <label class="form-check-label" for="bhubaneswar-area">Bhubaneswar</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="konark-area" name="coverage[]" value="konark">
                                        <label class="form-check-label" for="konark-area">Konark</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="cuttack-area" name="coverage[]" value="cuttack">
                                        <label class="form-check-label" for="cuttack-area">Cuttack</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="chilika-area" name="coverage[]" value="chilika">
                                        <label class="form-check-label" for="chilika-area">Chilika Lake</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="all-odisha" name="coverage[]" value="all-odisha">
                                        <label class="form-check-label" for="all-odisha">All Odisha</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Terms and Submit -->
                        <div class="form-section">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-decoration-none">Terms & Conditions</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                                </label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>Register Travel Agency
                                </button>
                                <a href="login.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Login
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.register-container {
    min-height: 100vh;
    background: white;
    padding: 1rem 0;
}

.register-card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    margin: 0.5rem 0;
    border: 1px solid #e9ecef;
}

.register-header {
    margin-bottom: 1.5rem;
}

.travel-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #28a745, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.travel-icon i {
    font-size: 2rem;
    color: white;
}

.form-section {
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-title {
    color: #333;
    margin-bottom: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.section-title i {
    color: #28a745;
}

.form-label {
    font-weight: 500;
    color: #555;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
}

.btn-outline-secondary {
    border-radius: 8px;
    padding: 0.75rem 2rem;
    font-weight: 500;
}

@media (max-width: 768px) {
    .register-card {
        margin: 0.25rem;
        padding: 1rem;
    }
    
    .register-container {
        padding: 0.5rem 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mobile number validation
    const mobileInput = document.getElementById('mobile');
    mobileInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }
    });
    
    // Form submission
    const registerForm = document.getElementById('travelRegisterForm');
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Collect form data
        const formData = new FormData(this);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (key === 'services[]' || key === 'coverage[]') {
                const fieldName = key.replace('[]', '');
                if (!data[fieldName]) data[fieldName] = [];
                data[fieldName].push(value);
            } else {
                data[key] = value;
            }
        }
        
        // Validation
        if (data.mobile && data.mobile.length !== 10) {
            alert('Please enter a valid 10-digit mobile number');
            return;
        }
        
        // Here you would typically send the data to your server
        console.log('Travel agency registration data:', data);
        alert('Travel agency registration submitted successfully! You will be contacted for verification.');
        
        // Redirect to login page
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
