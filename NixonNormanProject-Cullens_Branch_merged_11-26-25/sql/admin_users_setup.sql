-- ============================================================
-- Admin Users Table Setup
-- ============================================================
-- This table stores administrator login credentials
-- Passwords are stored as secure hashes, never plain text
-- ============================================================

-- Switch to the Nixon Norman Media database
USE nixon_norman_media;

-- Create the admin_users table
-- This table will hold all admin user accounts for the website
CREATE TABLE IF NOT EXISTS admin_users (
    -- Unique ID for each admin user (auto-increments)
    id INT PRIMARY KEY AUTO_INCREMENT,
    
    -- Username for login (must be unique, max 50 characters)
    username VARCHAR(50) UNIQUE NOT NULL,
    
    -- Encrypted password (using PHP password_hash function)
    -- Stored as a hash for security - never store plain text passwords!
    password_hash VARCHAR(255) NOT NULL,
    
    -- When this admin account was created
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- When this admin last logged in (useful for tracking activity)
    last_login TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Create Default Admin Account
-- ============================================================
-- Username: admin
-- Password: admin123
-- 
-- IMPORTANT: Change this password immediately after first login!
-- The hash below is created using PHP's password_hash() function
-- ============================================================

INSERT INTO admin_users (username, password_hash) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Note: The password hash above corresponds to 'admin123'
-- To create your own password hash, use this PHP code:
-- echo password_hash('your_password_here', PASSWORD_DEFAULT);
