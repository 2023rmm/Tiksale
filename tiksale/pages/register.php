<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Tiksale Auction</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="auth-left">
            <div class="auth-overlay">
                <div class="auth-content">
                    <h1>Join Tiksale Today!</h1>
                    <p>Create your free account and start bidding on thousands of amazing items</p>
                    <div class="auth-features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Free Registration</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Verified Sellers</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Secure Payments</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>24/7 Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="auth-right">
            <div class="auth-form-container">
                <div class="auth-header">
                    <div class="logo">
                        <i class="fas fa-gavel"></i>
                        <span>Tiksale</span>
                    </div>
                    <h2>Create Account</h2>
                    <p>Sign up to start your auction journey</p>
                </div>

                <form id="registerForm" class="auth-form" method="POST" action="../api/register.php">
                    <div class="alert" id="alertBox" style="display: none;"></div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" id="full_name" name="full_name" placeholder="John Doe" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <div class="input-group">
                                <i class="fas fa-at"></i>
                                <input type="text" id="username" name="username" placeholder="johndoe" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="your@email.com" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <div class="input-group">
                                <i class="fas fa-phone"></i>
                                <input type="tel" id="phone" name="phone" placeholder="+1234567890">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="country">Country</label>
                            <div class="input-group">
                                <i class="fas fa-globe"></i>
                                <select id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="Kenya">Kenya</option>
                                    <option value="USA">United States</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="Canada">Canada</option>
                                    <option value="Australia">Australia</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="user_type">Account Type</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="user_type" value="buyer" checked>
                                <span>
                                    <i class="fas fa-shopping-bag"></i>
                                    Buyer
                                </span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="user_type" value="seller">
                                <span>
                                    <i class="fas fa-store"></i>
                                    Seller
                                </span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="user_type" value="both">
                                <span>
                                    <i class="fas fa-exchange-alt"></i>
                                    Both
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Min. 8 characters" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="input-group">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" required>
                            <span>I agree to the <a href="#" class="link">Terms of Service</a> and <a href="#" class="link">Privacy Policy</a></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </button>

                    <div class="divider">
                        <span>OR</span>
                    </div>

                    <div class="social-login">
                        <button type="button" class="btn-social btn-google">
                            <i class="fab fa-google"></i>
                            Sign up with Google
                        </button>
                        <button type="button" class="btn-social btn-facebook">
                            <i class="fab fa-facebook"></i>
                            Sign up with Facebook
                        </button>
                    </div>

                    <div class="auth-footer">
                        <p>Already have an account? <a href="login.php" class="link-primary">Sign In</a></p>
                        <p><a href="../index.php" class="link"><i class="fas fa-arrow-left"></i> Back to Home</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.parentElement.querySelector('.toggle-password i');
            
            if (input.type === 'password') {
                input.type = 'text';
                button.classList.remove('fa-eye');
                button.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                button.classList.remove('fa-eye-slash');
                button.classList.add('fa-eye');
            }
        }

        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = e.target;
            const formData = new FormData(form);
            const alertBox = document.getElementById('alertBox');
            
            // Validate passwords match
            if (formData.get('password') !== formData.get('confirm_password')) {
                alertBox.className = 'alert alert-error';
                alertBox.textContent = 'Passwords do not match!';
                alertBox.style.display = 'block';
                return;
            }
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alertBox.className = 'alert alert-success';
                    alertBox.textContent = result.message;
                    alertBox.style.display = 'block';
                    
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
                } else {
                    alertBox.className = 'alert alert-error';
                    alertBox.textContent = result.message;
                    alertBox.style.display = 'block';
                }
            } catch (error) {
                alertBox.className = 'alert alert-error';
                alertBox.textContent = 'An error occurred. Please try again.';
                alertBox.style.display = 'block';
            }
        });
    </script>
</body>
</html>
