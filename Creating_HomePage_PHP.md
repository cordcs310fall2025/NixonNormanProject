# Creating the Home Page PHP File

## Overview
This guide will help you convert `homePage.html` to `homePage.php` with database functionality, following the same pattern used for `aboutPage.php`.

## Step 1: Understand the Current Structure

First, examine your existing `homePage.html` to identify:
- Static text that should become dynamic (headlines, descriptions)
- Featured work/projects that should come from database
- Client logos that should be manageable
- Any other content that might change

## Step 2: Plan Your Database Table

Create a table structure for home page content. Here's a recommended schema:

```sql
CREATE TABLE IF NOT EXISTS home_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hero_title VARCHAR(255) NOT NULL DEFAULT 'Nixon Norman Media',
    hero_subtitle TEXT,
    hero_description TEXT,
    cta_button_text VARCHAR(100) DEFAULT 'View Our Work',
    cta_button_link VARCHAR(255) DEFAULT 'projectsPage.php',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Optional: Featured Projects Table (if not already created)
```sql
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image_url VARCHAR(500),
    category VARCHAR(100),
    featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Optional: Client Logos Table
```sql
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(255) NOT NULL,
    logo_url VARCHAR(500),
    display_order INT DEFAULT 0,
    active BOOLEAN DEFAULT TRUE
);
```

## Step 3: Create the PHP File Structure

### Start with the PHP Header (Top of File)
```php
<?php
// homePage.php - Dynamic Home Page for Nixon Norman Media

// Start session
session_start();

// Include database configuration
require_once 'db_config.php';

// Set page-specific variables
$pageTitle = "Nixon Norman Media — Creative Visual Storytelling";
$currentPage = "home";

// Function to get home page content from database
function getHomeContent() {
    if (testDatabaseConnection()) {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->query("SELECT * FROM home_content ORDER BY id DESC LIMIT 1");
            $dbContent = $stmt->fetch();
            
            if ($dbContent) {
                return [
                    'hero_title' => $dbContent['hero_title'],
                    'hero_subtitle' => $dbContent['hero_subtitle'],
                    'hero_description' => $dbContent['hero_description'],
                    'cta_button_text' => $dbContent['cta_button_text'],
                    'cta_button_link' => $dbContent['cta_button_link']
                ];
            }
        } catch (Exception $e) {
            error_log("Database error in homePage.php: " . $e->getMessage());
        }
    }
    
    // Default content (fallback)
    return [
        'hero_title' => 'Nixon Norman Media',
        'hero_subtitle' => 'Creative Visual Storytelling',
        'hero_description' => 'Professional photography and videography services for automotive, commercial, and event needs.',
        'cta_button_text' => 'View Our Work',
        'cta_button_link' => 'projectsPage.php'
    ];
}

// Function to get featured projects
function getFeaturedProjects($limit = 6) {
    if (testDatabaseConnection()) {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE featured = 1 ORDER BY display_order, created_at DESC LIMIT :limit");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Database error fetching projects: " . $e->getMessage());
        }
    }
    return [];
}

// Function to get client logos
function getClientLogos() {
    if (testDatabaseConnection()) {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->query("SELECT * FROM clients WHERE active = 1 ORDER BY display_order");
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Database error fetching clients: " . $e->getMessage());
        }
    }
    return [];
}

// Get all content
$homeContent = getHomeContent();
$featuredProjects = getFeaturedProjects(6);
$clients = getClientLogos();

// Image path handling
$imageBasePath = (strpos($_SERVER['REQUEST_URI'], '/src/') !== false) ? '../images/' : '/images/';
?>
```

## Step 4: Copy Your HTML Structure

Copy everything from `<DOCTYPE html>` down from your `homePage.html`.

## Step 5: Replace Static Content with PHP Variables

### Example Replacements:

**Static HTML:**
```html
<title>Nixon Norman Media — About</title>
```

**Dynamic PHP:**
```php
<title><?php echo htmlspecialchars($pageTitle); ?></title>
```

---

**Static HTML:**
```html
<h1>Nixon Norman Media</h1>
<p>Creative Visual Storytelling</p>
```

**Dynamic PHP:**
```php
<h1><?php echo htmlspecialchars($homeContent['hero_title']); ?></h1>
<p><?php echo htmlspecialchars($homeContent['hero_subtitle']); ?></p>
```

---

**Static HTML Navigation:**
```html
<li><a href="homePage.html">Home</a></li>
<li><a href="aboutPage.html">About</a></li>
```

**Dynamic PHP Navigation:**
```php
<li><a href="homePage.php" <?php echo ($currentPage == 'home') ? 'class="active"' : ''; ?>>Home</a></li>
<li><a href="aboutPage.php" <?php echo ($currentPage == 'about') ? 'class="active"' : ''; ?>>About</a></li>
```

---

**Static Featured Projects:**
```html
<div class="project">
    <img src="project1.jpg" alt="Project 1">
    <h3>Project Title</h3>
</div>
```

**Dynamic Featured Projects:**
```php
<?php if (!empty($featuredProjects)): ?>
    <?php foreach ($featuredProjects as $project): ?>
        <div class="project">
            <img src="<?php echo htmlspecialchars($project['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($project['title']); ?>">
            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
            <p><?php echo htmlspecialchars($project['description']); ?></p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No featured projects available.</p>
<?php endif; ?>
```

## Step 6: Update Image Paths

Replace hardcoded image paths:

**Before:**
```html
<img src="../images/logo.png">
```

**After:**
```php
<img src="<?php echo $imageBasePath; ?>logo.png">
```

## Step 7: Add Security

Always use `htmlspecialchars()` when outputting variables:

```php
<?php echo htmlspecialchars($variable); ?>
```

This prevents XSS (Cross-Site Scripting) attacks.

## Step 8: Insert Default Database Content

In phpMyAdmin, run this SQL to add initial home page content:

```sql
INSERT INTO home_content (hero_title, hero_subtitle, hero_description, cta_button_text, cta_button_link) 
VALUES (
    'Nixon Norman Media',
    'Creative Visual Storytelling',
    'Professional photography and videography services for automotive, commercial, and event needs.',
    'View Our Work',
    'projectsPage.php'
);
```

## Step 9: Test Your Page

1. Save the file as `homePage.php` in your `src/` folder
2. Make sure XAMPP is running (Apache + MySQL)
3. Visit: `http://localhost:8080/NixonNormanProject/src/homePage.php`
4. Check that:
   - Page loads without errors
   - Content displays correctly
   - Navigation works
   - Database content appears (if database is set up)
   - Default content shows (if database is not set up)

## Step 10: Update All Navigation Links

Go through ALL your pages and update links:
- Change `homePage.html` → `homePage.php`
- Change `aboutPage.html` → `aboutPage.php`
- Change `contactPage.html` → `contactPage.php`
- etc.

## Key Principles to Follow

1. **Use the existing `db_config.php`** - Don't create a new database connection
2. **Keep the same HTML structure** - Just make content dynamic
3. **Always have fallback content** - In case database isn't connected
4. **Use `htmlspecialchars()`** - For security
5. **Test database connection first** - Use `testDatabaseConnection()` before queries
6. **Handle errors gracefully** - Use try-catch blocks
7. **Keep the same styling** - Link to the same `style.css`

## Common Mistakes to Avoid

❌ **Don't** forget the `<?php ?>` tags  
❌ **Don't** skip `htmlspecialchars()` on output  
❌ **Don't** hardcode content that should be dynamic  
❌ **Don't** forget to update navigation links to `.php`  
❌ **Don't** create duplicate database connection code  

✅ **Do** use `require_once 'db_config.php'`  
✅ **Do** provide fallback content  
✅ **Do** test with and without database connection  
✅ **Do** keep your HTML structure intact  
✅ **Do** follow the same pattern as `aboutPage.php`  

## Quick Reference: aboutPage.php Pattern

Your `aboutPage.php` is the perfect template. Copy its structure:
1. PHP header with session and database connection
2. Content-fetching functions
3. Fallback default content
4. HTML structure with PHP variables embedded
5. Security with `htmlspecialchars()`
6. Dynamic navigation with `.php` extensions

---

**Need Help?** Review `aboutPage.php` and `db_config.php` as working examples!