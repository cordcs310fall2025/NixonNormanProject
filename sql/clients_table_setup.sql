-- ============================================================
-- Clients Table Setup for Nixon Norman Media
-- ============================================================
-- This table stores client information including contact details,
-- services provided, payment information, and client status
-- ============================================================

-- Create clients table
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    company_name VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    status ENUM('potential', 'current', 'past') DEFAULT 'potential',
    services_provided TEXT,
    total_paid DECIMAL(10, 2) DEFAULT 0.00,
    notes TEXT,
    first_contact_date DATE,
    last_project_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO clients (client_name, company_name, email, phone, status, services_provided, total_paid, notes, first_contact_date, last_project_date) VALUES
('John Smith', 'Smith Automotive', 'john@smithauto.com', '555-0100', 'current', 'Automotive Photography, Video Production', 2500.00, 'Great client, referred by previous customer', '2024-06-15', '2025-11-20'),
('Sarah Johnson', 'Johnson Real Estate', 'sarah@johnsonrealty.com', '555-0101', 'past', 'Real Estate Photography', 1200.00, 'Completed 3 property shoots', '2024-03-10', '2024-09-15'),
('Mike Williams', 'Tech Startup Inc', 'mike@techstartup.com', '555-0102', 'potential', NULL, 0.00, 'Inquired about commercial video production', '2025-11-28', NULL),
('Emily Davis', 'Davis Events', 'emily@davisevents.com', '555-0103', 'current', 'Event Photography, Social Media Content', 3800.00, 'Regular monthly contract', '2024-08-01', '2025-11-30');
