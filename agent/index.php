<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Somas Fleet Management - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./cbs/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-image-container">
            <div class="login-image">
                <img src="./cbs/img/login_bg.png" alt="Truck Background" height="">
            </div>
        </div>
        <div class="login-form-container">
                <div class="login-form-wrapper">
                    <div class="logo-container text-center mb-4">
                        <img src="./cbs/img/somas-logo.png" alt="Somas Fleet Logo" class="login-logo" style="height: 40px">
                    </div>
                    <h1 class="login-title">Welcome Back</h1>
                    <p class="login-subtitle">Sign in to continue</p>
                    
                    <?php if(isset($_SESSION['success']) && !empty($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 1000;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-2"></i>
                            <div>
                                <?php echo $_SESSION['success']; ?>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success']); endif; ?>

                    <?php if(isset($_SESSION['error']) && !empty($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" role="alert" style="z-index: 1000;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div>
                                <?php echo $_SESSION['error']; ?>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); endif; ?>
                    
                    <form action="./auth/login.php" method="post" class="login-form" id="loginForm">
                        <div class="form-group mb-3 text-center">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary active" id="usernameBtn">
                                    <i class="fas fa-user me-1"></i> Username
                                </button>
                                <button type="button" class="btn btn-outline-primary" id="phoneBtn">
                                    <i class="fas fa-phone me-1"></i> Phone
                                </button>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4" id="usernameGroup">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username">
                            </div>
                        </div>
                        
                        <div class="form-group mb-4 d-none" id="phoneGroup">
                            <label for="phone" class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your 10-digit phone number" pattern="[0-9]{10}">
                            </div>
                            <small class="text-muted">Enter your 10-digit Phone number</small>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                <span class="input-group-text password-toggle" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">
                                Remember me
                            </label>
                            <a href="auth/forgot-password.php" class="float-end forgot-link">Forgot Password?</a>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i> Sign In
                            </button>
                        </div>

                        <br>
                        <br>

                        <span class="text-muted"> ❤️ Designed By <a href="https://somasindia.com/" target="_blank">SOMAS TECHNOLOGY INDIA PVT LTD</a> </span>
                    </form>
                </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle between username and phone login
        document.addEventListener('DOMContentLoaded', function() {
            const usernameBtn = document.getElementById('usernameBtn');
            const phoneBtn = document.getElementById('phoneBtn');
            const usernameGroup = document.getElementById('usernameGroup');
            const phoneGroup = document.getElementById('phoneGroup');
            const usernameInput = document.getElementById('username');
            const phoneInput = document.getElementById('phone');
            const loginForm = document.getElementById('loginForm');
            
            // Toggle to username login
            usernameBtn.addEventListener('click', function() {
                usernameBtn.classList.add('active');
                phoneBtn.classList.remove('active');
                usernameGroup.classList.remove('d-none');
                phoneGroup.classList.add('d-none');
                phoneInput.removeAttribute('required');
                usernameInput.setAttribute('required', '');
            });
            
            // Toggle to phone login
            phoneBtn.addEventListener('click', function() {
                phoneBtn.classList.add('active');
                usernameBtn.classList.remove('active');
                phoneGroup.classList.remove('d-none');
                usernameGroup.classList.add('d-none');
                usernameInput.removeAttribute('required');
                phoneInput.setAttribute('required', '');
            });
            
            // Form validation
            loginForm.addEventListener('submit', function(e) {
                // If phone login is active, validate phone number
                if (phoneBtn.classList.contains('active')) {
                    const phone = phoneInput.value.trim();
                    if (!/^\d{10}$/.test(phone)) {
                        e.preventDefault();
                        alert('Please enter a valid 10-digit phone number');
                        return false;
                    }
                }
                return true;
            });
        });
        
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
