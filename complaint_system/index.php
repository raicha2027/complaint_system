<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Complaint Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="landing-page">
    <nav class="navbar">
        <div class="nav-container">
            <h1 class="logo"><i class="fas fa-graduation-cap"></i> ComplaintSystem</h1>
            <div class="nav-links">
                <a href="#home">Home</a>
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <button class="btn-login" onclick="openModal('loginModal')">Login</button>
            </div>
        </div>
    </nav>

    <section class="hero" id="home">
        <div class="hero-content">
            <h1 class="hero-title">Your Voice Matters</h1>
            <p class="hero-subtitle">Submit, track, and resolve campus complaints efficiently</p>
            <div class="hero-buttons">
                <button class="btn btn-primary" onclick="openModal('registerModal')">Get Started</button>
                <button class="btn btn-secondary" onclick="openModal('loginModal')">Login</button>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <h2 class="section-title">Why Choose Us?</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <h3>Fast Response</h3>
                <p>Get quick responses to your complaints from our dedicated admin team</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Secure & Private</h3>
                <p>Your complaints are handled with complete confidentiality</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                <h3>Track Progress</h3>
                <p>Monitor the status of your complaints in real-time</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <h3>24/7 Access</h3>
                <p>Submit and check complaints anytime, anywhere</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
       <h2 class="section-title">About ComplaintSystem</h2>
       <p class="about-text">
            ComplaintSystem is a modern platform designed to streamline the complaint management process for students. 
            Whether it's hostel issues, cafeteria concerns, academic challenges, or service delays, we're here to 
            ensure your voice is heard and your issues are resolved efficiently.
        </p>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <h2 class="section-title">Need Help?</h2>
        <p class="contact-text">Contact us at <a href="mailto:support@complainthub.edu">support@complainthub.edu</a></p>
        <p class="admin-link">Are you an administrator? <a href="#" onclick="openModal('adminRegisterModal')">Register as Admin</a></p>
    </section>

    <!-- Login  -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('loginModal')">&times;</span>
            <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?>
            
            <form action="php/login.php" method="POST">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group checkbox-group">
                    <label><input type="checkbox" name="remember"> Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <p class="form-footer">Don't have an account? <a href="#" onclick="switchModal('loginModal', 'registerModal')">Register here</a></p>
        </div>
    </div>

    <!-- Student Register Modal -->
    <!-- Student Register Modal -->
<div id="registerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('registerModal')">&times;</span>
        <h2><i class="fas fa-user-plus"></i> Student Registration</h2>
        <form action="php/register_student.php" method="POST">
            <div class="form-group">
                <span class="icon"><i class="fas fa-user"></i></span>
                <input type="text" name="name" required>
                <label>Full Name</label>
            </div>
            
            <div class="form-group">
                <span class="icon"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" required>
                <label>Email</label>
            </div>
            
            <div class="form-group">
                <span class="icon"><i class="fas fa-building"></i></span>
                <select name="department" required>
                    <option value="">Select Department</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Business Administration">Business Administration</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Management Information Systems">Management Information Systems</option>
                    <option value="Law">Law</option>
                </select>
              
            </div>
            
            <div class="form-group">
                <span class="icon"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" required minlength="8">
                <label>Password</label>
            </div>
            
            <div class="form-group">
                <span class="icon"><i class="fas fa-lock"></i></span>
                <input type="password" name="confirm_password" required>
                <label>Confirm Password</label>
            </div>
            
            <div class="checkbox-group">
                <label>
                    <input type="checkbox" required> I agree to the terms & conditions
                </label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        <p class="form-footer">Already have an account? <a href="#" onclick="switchModal('registerModal', 'loginModal')">Login here</a></p>
    </div>
</div>

    <!-- Admin Register Modal -->
<div id="adminRegisterModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('adminRegisterModal')">&times;</span>
        <h2><i class="fas fa-user-shield"></i> Admin Registration</h2>
        <form action="php/register_admin.php" method="POST">
            <div class="form-group">
                <span class="icon"><i class="fas fa-user"></i></span>
                <input type="text" name="name" required>
                <label>Full Name</label>
            </div>
            
            <div class="form-group">
                <span class="icon"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" required>
                <label>Email</label>
            </div>
            
            <div class="form-group">
                <span class="icon"><i class="fas fa-key"></i></span>
                <input type="text" name="access_code" required>
                <label>Admin Access Code</label>
                <small>Contact system administrator for access code</small>
            </div>
            
            <div class="form-group">
                <span class="icon"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" required minlength="8">
                <label>Password</label>
            </div>
            
            <div class="form-group">
                <span class="icon"><i class="fas fa-lock"></i></span>
                <input type="password" name="confirm_password" required>
                <label>Confirm Password</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Register as Admin</button>
        </form>
        <p class="form-footer">Back to <a href="#" onclick="switchModal('adminRegisterModal', 'loginModal')">Login</a></p>
    </div>
</div>

    <footer class="footer">
        <p>&copy; 2025 ComplaintSystem. All rights reserved.</p>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
