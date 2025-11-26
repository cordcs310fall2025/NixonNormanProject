# Setting Up Your Nixon Norman Media Database in XAMPP

## Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services
3. Make sure both show "Running" status

## Step 2: Access phpMyAdmin
1. Click the **Admin** button next to MySQL in XAMPP Control Panel
2. OR go to `http://localhost/phpmyadmin` in your browser

## Step 3: Create the Database
1. In phpMyAdmin, click **"New"** in the left sidebar
2. Enter database name: `nixon_norman_media`
3. Click **"Create"**

## Step 4: Create the About Content Table
1. Select your new database (`nixon_norman_media`)
2. Click the **SQL** tab at the top
3. Copy and paste this SQL code:

```sql
CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    headline VARCHAR(255) NOT NULL DEFAULT 'About Nixon Norman Media',
    intro TEXT,
    bio TEXT,
    mission TEXT,
    experience_years VARCHAR(10) DEFAULT '5+',
    projects_completed VARCHAR(10) DEFAULT '150+',
    happy_clients VARCHAR(10) DEFAULT '50+',
    services JSON,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

4. Click **"Go"** to execute

## Step 5: Insert Default Content
1. In the same SQL tab, copy and paste this code:

```sql
INSERT INTO about_content (headline, intro, bio, mission, services) VALUES (
    'About Nixon Norman Media',
    'Capturing moments that matter through the lens of creativity and passion.',
    'Nixon Norman Media specializes in professional photography and videography services. With years of experience in automotive, commercial, and event photography, we bring your vision to life with stunning visual storytelling.',
    'Our mission is to deliver exceptional visual content that exceeds expectations and creates lasting impressions.',
    '["Commercial Photography", "Event Photography", "Automotive Photography", "Video Production", "Social Media Content"]'
);
```

2. Click **"Go"** to execute

## Step 6: Test Your About Page
1. Open your web browser
2. Go to `http://localhost/your-project-folder/src/aboutPage.php`
3. The page should now load with content from the database!

## Next Steps
- Your aboutPage.php will automatically use database content when available
- If the database is not connected, it falls back to default content
- You can edit the content directly in phpMyAdmin by browsing the `about_content` table

## Additional Tables to Create (for future pages)
When you're ready, you can create these tables for other pages:

### Projects Table
```sql
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    category VARCHAR(100),
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Gear Table
```sql
CREATE TABLE IF NOT EXISTS gear (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    description TEXT,
    image_url VARCHAR(500),
    purchase_date DATE,
    price DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Contact Submissions Table
```sql
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'responded') DEFAULT 'new'
);
```

## Troubleshooting
- **Can't access phpMyAdmin?** Make sure Apache and MySQL are running in XAMPP
- **Database connection errors?** Check that the database name matches in `db_config.php`
- **Permission issues?** Make sure XAMPP is running as administrator
