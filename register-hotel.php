<?php include 'includes/header.php'; ?>

<div class="register-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="register-card">
                    <div class="register-header text-center">
                        <div class="register-icon">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <h2 class="mb-2">Hotel Registration</h2>
                        <p class="text-muted">Register your hotel with Stay in Puri</p>
                    </div>
                    
                    <form class="register-form" id="hotelRegisterForm">
                        <!-- Personal Information -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ownerName" class="form-label">Owner Name *</label>
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
                        
                        <!-- Hotel Information -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-hotel me-2"></i>Hotel Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="hotelName" class="form-label">Hotel Name *</label>
                                    <input type="text" class="form-control" id="hotelName" name="hotelName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="hotelType" class="form-label">Hotel Type *</label>
                                    <select class="form-select" id="hotelType" name="hotelType" required>
                                        <option value="">Select Type</option>
                                        <option value="hotel">Hotel</option>
                                        <option value="resort">Resort</option>
                                        <option value="guesthouse">Guest House</option>
                                        <option value="homestay">Homestay</option>
                                        <option value="lodge">Lodge</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="starRating" class="form-label">Star Rating</label>
                                    <select class="form-select" id="starRating" name="starRating">
                                        <option value="">Select Rating</option>
                                        <option value="1">1 Star</option>
                                        <option value="2">2 Star</option>
                                        <option value="3">3 Star</option>
                                        <option value="4">4 Star</option>
                                        <option value="5">5 Star</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="totalRooms" class="form-label">Total Rooms *</label>
                                    <input type="number" class="form-control" id="totalRooms" name="totalRooms" min="1" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="priceRange" class="form-label">Price Range *</label>
                                    <select class="form-select" id="priceRange" name="priceRange" required>
                                        <option value="">Select Range</option>
                                        <option value="budget">Budget (₹500-1500)</option>
                                        <option value="mid-range">Mid-range (₹1500-3000)</option>
                                        <option value="luxury">Luxury (₹3000+)</option>
                                    </select>
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
                                <label for="address" class="form-label">Complete Address *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                        
                        <!-- Amenities -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-concierge-bell me-2"></i>Amenities
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="wifi" name="amenities[]" value="wifi">
                                        <label class="form-check-label" for="wifi">Free WiFi</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="parking" name="amenities[]" value="parking">
                                        <label class="form-check-label" for="parking">Free Parking</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ac" name="amenities[]" value="ac">
                                        <label class="form-check-label" for="ac">Air Conditioning</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="restaurant" name="amenities[]" value="restaurant">
                                        <label class="form-check-label" for="restaurant">Restaurant</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="pool" name="amenities[]" value="pool">
                                        <label class="form-check-label" for="pool">Swimming Pool</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gym" name="amenities[]" value="gym">
                                        <label class="form-check-label" for="gym">Fitness Center</label>
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
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check me-2"></i>Register Hotel
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

.register-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #007bff, #0056b3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.register-icon i {
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
    color: #007bff;
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
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
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
    const registerForm = document.getElementById('hotelRegisterForm');
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Collect form data
        const formData = new FormData(this);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (key === 'amenities[]') {
                if (!data.amenities) data.amenities = [];
                data.amenities.push(value);
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
        console.log('Hotel registration data:', data);
        alert('Hotel registration submitted successfully! You will be contacted for verification.');
        
        // Redirect to login page
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
