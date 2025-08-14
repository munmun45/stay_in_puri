<?php include 'includes/header.php'; ?>

<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card">
                    <div class="login-header text-center">
                        <img src="assets/img/stay-in-puri.png" alt="Stay in Puri" width="150" class="mb-4">
                        <h2 class="mb-2">Welcome Back</h2>
                        <p class="text-muted">Sign in to your account</p>
                    </div>
                    
                    <form class="login-form" id="loginForm">
                        <div class="form-group mb-3">
                            <label for="mobile" class="form-label">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" class="form-control" id="mobile" name="mobile" 
                                       placeholder="Enter your mobile number" required 
                                       pattern="[0-9]{10}" maxlength="10">
                            </div>
                            <div class="form-text">Enter 10-digit mobile number</div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Enter your password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    Remember me
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                        
                        <div class="text-center mb-3">
                            <a href="#" class="text-decoration-none">Forgot Password?</a>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="mb-3">Don't have an account?</p>
                            <button type="button" class="btn btn-outline-primary w-100" id="registerBtn">
                                <i class="fas fa-user-plus me-2"></i>Register Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Type Modal -->
<div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="registrationModalLabel">Choose Registration Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="mb-4 text-muted">Select the type of account you want to create</p>
                
                <div class="row g-3">
                    <div class="col-12">
                        <button class="btn btn-outline-primary w-100 py-3 registration-type-btn" 
                                data-type="hotel" onclick="redirectToRegistration('hotel')">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-hotel fa-2x me-3 text-primary"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Hotel Owner</h6>
                                    <small class="text-muted">Register your hotel or accommodation</small>
                                </div>
                            </div>
                        </button>
                    </div>
                    
                    <div class="col-12">
                        <button class="btn btn-outline-success w-100 py-3 registration-type-btn" 
                                data-type="travel" onclick="redirectToRegistration('travel')">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-map-marked-alt fa-2x me-3 text-success"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Travel Agency</h6>
                                    <small class="text-muted">Register your travel agency</small>
                                </div>
                            </div>
                        </button>
                    </div>
                    
                    <div class="col-12">
                        <button class="btn btn-outline-warning w-100 py-3 registration-type-btn" 
                                data-type="restaurant" onclick="redirectToRegistration('restaurant')">
                            <div class="d-flex align-items-center justify-content-center">
                                <i class="fas fa-utensils fa-2x me-3 text-warning"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">Restaurant Owner</h6>
                                    <small class="text-muted">Register your restaurant</small>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.login-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    padding: 2rem 0;
}

.login-card {
    background: white;
    border-radius: 15px;
    padding: 2.5rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: none;
}

.login-header img {
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
}

.login-header h2 {
    color: #333;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    color: #555;
    margin-bottom: 0.5rem;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
    color: #6c757d;
}

.form-control {
    border-left: none;
    padding-left: 0.5rem;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    padding: 0.75rem;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
}

.btn-outline-primary {
    border-radius: 8px;
    padding: 0.75rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.registration-type-btn {
    border-radius: 12px;
    transition: all 0.3s ease;
    border-width: 2px;
}

.registration-type-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.modal-header {
    padding: 1.5rem 1.5rem 0;
}

.modal-body {
    padding: 1rem 1.5rem 1.5rem;
}

@media (max-width: 576px) {
    .login-card {
        margin: 1rem;
        padding: 2rem 1.5rem;
    }
    
    .login-container {
        padding: 1rem 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
    
    // Mobile number validation
    const mobileInput = document.getElementById('mobile');
    mobileInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 10) {
            this.value = this.value.slice(0, 10);
        }
    });
    
    // Show registration modal
    const registerBtn = document.getElementById('registerBtn');
    registerBtn.addEventListener('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
        modal.show();
    });
    
    // Login form submission
    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const mobile = document.getElementById('mobile').value;
        const password = document.getElementById('password').value;
        
        if (mobile.length !== 10) {
            alert('Please enter a valid 10-digit mobile number');
            return;
        }
        
        // Here you would typically send the data to your server
        console.log('Login attempt:', { mobile, password });
        alert('Login functionality will be implemented with backend integration');
    });
});

function redirectToRegistration(type) {
    const modal = bootstrap.Modal.getInstance(document.getElementById('registrationModal'));
    modal.hide();
    
    // Redirect to specific registration page
    setTimeout(() => {
        window.location.href = `register-${type}.php`;
    }, 300);
}
</script>

<?php include 'includes/footer.php'; ?>
