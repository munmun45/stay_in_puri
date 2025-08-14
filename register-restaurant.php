<?php include 'includes/header.php'; ?>

<div class="register-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="register-card">
                    <div class="register-header text-center">
                        <div class="register-icon restaurant-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <h2 class="mb-2">Restaurant Registration</h2>
                        <p class="text-muted">Register your restaurant with Stay in Puri</p>
                    </div>
                    
                    <form class="register-form" id="restaurantRegisterForm">
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
                        
                        <!-- Restaurant Information -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-utensils me-2"></i>Restaurant Information
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="restaurantName" class="form-label">Restaurant Name *</label>
                                    <input type="text" class="form-control" id="restaurantName" name="restaurantName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="restaurantType" class="form-label">Restaurant Type *</label>
                                    <select class="form-select" id="restaurantType" name="restaurantType" required>
                                        <option value="">Select Type</option>
                                        <option value="fine-dining">Fine Dining</option>
                                        <option value="casual-dining">Casual Dining</option>
                                        <option value="fast-food">Fast Food</option>
                                        <option value="cafe">Cafe</option>
                                        <option value="street-food">Street Food</option>
                                        <option value="dhaba">Dhaba</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="seatingCapacity" class="form-label">Seating Capacity *</label>
                                    <input type="number" class="form-control" id="seatingCapacity" name="seatingCapacity" min="10" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="priceRange" class="form-label">Price Range *</label>
                                    <select class="form-select" id="priceRange" name="priceRange" required>
                                        <option value="">Select Range</option>
                                        <option value="budget">Budget (₹100-300)</option>
                                        <option value="mid-range">Mid-range (₹300-800)</option>
                                        <option value="premium">Premium (₹800+)</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="establishedYear" class="form-label">Established Year</label>
                                    <input type="number" class="form-control" id="establishedYear" name="establishedYear" 
                                           min="1950" max="2024">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cuisine Types -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-pepper-hot me-2"></i>Cuisine Types
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="odiya" name="cuisine[]" value="odiya">
                                        <label class="form-check-label" for="odiya">Odiya Cuisine</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="north-indian" name="cuisine[]" value="north-indian">
                                        <label class="form-check-label" for="north-indian">North Indian</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="south-indian" name="cuisine[]" value="south-indian">
                                        <label class="form-check-label" for="south-indian">South Indian</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="chinese" name="cuisine[]" value="chinese">
                                        <label class="form-check-label" for="chinese">Chinese</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="continental" name="cuisine[]" value="continental">
                                        <label class="form-check-label" for="continental">Continental</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="seafood" name="cuisine[]" value="seafood">
                                        <label class="form-check-label" for="seafood">Seafood</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="vegetarian" name="cuisine[]" value="vegetarian">
                                        <label class="form-check-label" for="vegetarian">Pure Vegetarian</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="sweets" name="cuisine[]" value="sweets">
                                        <label class="form-check-label" for="sweets">Sweets & Desserts</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="beverages" name="cuisine[]" value="beverages">
                                        <label class="form-check-label" for="beverages">Beverages</label>
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
                                <label for="address" class="form-label">Complete Address *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                        </div>
                        
                        <!-- Facilities & Services -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-concierge-bell me-2"></i>Facilities & Services
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="ac-dining" name="facilities[]" value="ac-dining">
                                        <label class="form-check-label" for="ac-dining">AC Dining</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="parking" name="facilities[]" value="parking">
                                        <label class="form-check-label" for="parking">Parking Available</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="wifi" name="facilities[]" value="wifi">
                                        <label class="form-check-label" for="wifi">Free WiFi</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="home-delivery" name="facilities[]" value="home-delivery">
                                        <label class="form-check-label" for="home-delivery">Home Delivery</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="takeaway" name="facilities[]" value="takeaway">
                                        <label class="form-check-label" for="takeaway">Takeaway</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="live-music" name="facilities[]" value="live-music">
                                        <label class="form-check-label" for="live-music">Live Music</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="outdoor-seating" name="facilities[]" value="outdoor-seating">
                                        <label class="form-check-label" for="outdoor-seating">Outdoor Seating</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="bar" name="facilities[]" value="bar">
                                        <label class="form-check-label" for="bar">Bar Available</label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="family-friendly" name="facilities[]" value="family-friendly">
                                        <label class="form-check-label" for="family-friendly">Family Friendly</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Operating Hours -->
                        <div class="form-section">
                            <h5 class="section-title">
                                <i class="fas fa-clock me-2"></i>Operating Hours
                            </h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="openTime" class="form-label">Opening Time *</label>
                                    <input type="time" class="form-control" id="openTime" name="openTime" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="closeTime" class="form-label">Closing Time *</label>
                                    <input type="time" class="form-control" id="closeTime" name="closeTime" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Weekly Off Days</label>
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="monday-off" name="weeklyOff[]" value="monday">
                                            <label class="form-check-label" for="monday-off">Monday</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="tuesday-off" name="weeklyOff[]" value="tuesday">
                                            <label class="form-check-label" for="tuesday-off">Tuesday</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="wednesday-off" name="weeklyOff[]" value="wednesday">
                                            <label class="form-check-label" for="wednesday-off">Wednesday</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="thursday-off" name="weeklyOff[]" value="thursday">
                                            <label class="form-check-label" for="thursday-off">Thursday</label>
                                        </div>
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
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-check me-2"></i>Register Restaurant
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

.restaurant-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #ffc107, #ff8f00);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
}

.restaurant-icon i {
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
    color: #ffc107;
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
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.form-check-input:checked {
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning {
    background: linear-gradient(45deg, #ffc107, #ff8f00);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
    color: #000;
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
    color: #000;
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
    const registerForm = document.getElementById('restaurantRegisterForm');
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Collect form data
        const formData = new FormData(this);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (key === 'cuisine[]' || key === 'facilities[]' || key === 'weeklyOff[]') {
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
        console.log('Restaurant registration data:', data);
        alert('Restaurant registration submitted successfully! You will be contacted for verification.');
        
        // Redirect to login page
        setTimeout(() => {
            window.location.href = 'login.php';
        }, 2000);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
