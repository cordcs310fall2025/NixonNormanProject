<?php
/**
 * ============================================================
 * Admin Login Page
 * ============================================================
 * This is where admin users enter their username and password
 * to access the admin dashboard and management tools.
 * 
 * Features:
 * - Secure login form
 * - Password verification
 * - Error messages for failed login attempts
 * - Redirects to admin dashboard on success
 * ============================================================
 */

// Start the session (needed to track login status)
session_start();

// Include our authentication functions
require_once 'adminAuth.php';

// Check if user is already logged in
// If they are, send them straight to the admin dashboard
if (isAdminLoggedIn()) {
    header('Location: adminHome.php');
    exit();
}

// Variable to hold error messages
$errorMessage = '';

// Check if the login form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the username and password from the form
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Try to log in with the provided credentials
    if (loginAdmin($username, $password)) {
        // Login successful! Redirect to admin dashboard
        header('Location: adminHome.php');
        exit();
    } else {
        // Login failed - show error message
        $errorMessage = 'Invalid username or password. Please try again.';
    }
}

// Set base path for images
$imagePath = '/NixonNormanProject/images/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Admin Login — Nixon Norman Media</title>
    <style>
        /* Styles specific to the login page */
        
        /* Center the login box on the page */
        .login-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #325b78 0%, #1e4159 100%);
            padding: 20px;
        }
        
        /* The white box containing the login form */
        .login-box {
            background: white;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        
        /* Logo at the top of the login box */
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .login-logo img {
            width: 100px;
            height: auto;
        }
        
        /* Main heading on login page */
        .login-title {
            text-align: center;
            color: #325b78;
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        /* Subtitle text */
        .login-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }
        
        /* Each form field container */
        .form-group {
            margin-bottom: 20px;
        }
        
        /* Labels for form fields */
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        /* Text input fields */
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
            transition: border-color 0.3s ease;
        }
        
        /* Input field when user clicks on it */
        .form-group input:focus {
            outline: none;
            border-color: #325b78;
        }
        
        /* The login button */
        .login-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #325b78, #1e4159);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            font-family: 'Montserrat', sans-serif;
        }
        
        /* Button when user hovers over it */
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(50, 91, 120, 0.3);
        }
        
        /* Button when user clicks it */
        .login-button:active {
            transform: translateY(0);
        }
        
        /* Error message styling (shown when login fails) */
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            border-left: 4px solid #c33;
        }
        
        /* Link back to main website */
        .back-to-site {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-to-site a {
            color: #325b78;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        
        .back-to-site a:hover {
            color: #1e4159;
            text-decoration: underline;
        }
        
        /* Responsive design for mobile devices */
        @media (max-width: 480px) {
            .login-box {
                padding: 30px 20px;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Main login container (centers everything) -->
    <div class="login-container">
        <!-- The white box with the login form -->
        <div class="login-box">
            <!-- Logo -->
            <div class="login-logo">
                <img src="<?php echo $imagePath; ?>NNM-white.png" alt="Nixon Norman Media Logo">
            </div>
            
            <!-- Page title -->
            <h1 class="login-title">Admin Login</h1>
            <p class="login-subtitle">Enter your credentials to access the admin dashboard</p>
            
            <!-- Show error message if login failed -->
            <?php if ($errorMessage): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            
            <!-- Login form -->
            <form method="POST" action="adminLogin.php">
                <!-- Username field -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        autofocus
                        placeholder="Enter your username"
                    >
                </div>
                
                <!-- Password field -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="Enter your password"
                    >
                </div>
                
                <!-- Submit button -->
                <button type="submit" class="login-button">
                    Log In
                </button>
            </form>
            
            <!-- Link back to the main website -->
            <div class="back-to-site">
                <a href="homePage.php">← Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>
