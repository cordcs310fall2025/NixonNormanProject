# Image Upload System - Setup Guide

This guide will help you set up the image management system for the Nixon Norman Media website.

## Overview

The system allows non-technical users to easily upload and delete project images through a simple drag-and-drop interface.

**Features:**
- ✅ Drag & drop image upload
- ✅ Multiple image uploads at once
- ✅ Image preview and management
- ✅ Easy delete with confirmation
- ✅ Secure file handling
- ✅ Automatic file validation (type, size)
- ✅ Database integration with MySQL

---

## Step 1: Create Database Table

1. Open **phpMyAdmin** in your browser:
   - URL: `http://localhost/phpmyadmin`

2. Select your database: **nixon_norman_media**

3. Click on the **SQL** tab at the top

4. Copy and paste the SQL from this file:
   `/Applications/XAMPP/xamppfiles/htdocs/NixonNormanProject/database/create_project_images_table.sql`

5. Click **Go** to execute the SQL

6. You should see a success message. The `project_images` table is now created!

---

## Step 2: Set Folder Permissions (Important!)

The uploads folder needs write permissions so PHP can save images.

Open **Terminal** and run:

```bash
chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/NixonNormanProject/uploads
```

This allows the web server to write files to the uploads directory.

---

## Step 3: Verify File Structure

Make sure these files exist:

```
NixonNormanProject/
├── src/
│   ├── upload_image.php              ← Handles uploads/deletes
│   ├── manage_project_images.php     ← Admin interface
│   └── adminEdit.html                ← Updated with link
├── uploads/
│   ├── projects/                     ← Images stored here
│   ├── .htaccess                     ← Security settings
│   └── index.php                     ← Prevents browsing
└── database/
    └── create_project_images_table.sql
```

---

## Step 4: Test the System

### Access the Image Manager:

1. **Login as admin** at: `http://localhost/NixonNormanProject/src/adminLogin.php`

2. Navigate to the image manager for a project:
   `http://localhost/NixonNormanProject/src/manage_project_images.php?project_id=1`

   (Replace `1` with any valid project ID from your database)

### Upload Images:

**Method 1: Drag & Drop**
- Drag image files from your computer
- Drop them onto the upload area
- Watch the progress bar
- Images appear automatically when done

**Method 2: Click to Browse**
- Click the "Choose Files" button
- Select one or more images
- Click Open
- Images upload automatically

### Delete Images:

- Click the **Delete** button under any image
- Confirm the deletion
- Image is removed from both database and server

---

## Step 5: Link from Admin Panel

The image manager link has been added to [adminEdit.html](src/adminEdit.html#L88).

To access it from your admin interface:
1. Edit a project
2. Look for "Featured Image URL" field
3. Click "manage project images gallery" link

---

## How It Works

### Database Structure

The `project_images` table stores:
- `project_id` - Links to the projects table
- `filename` - Unique filename on server
- `original_filename` - Original name when uploaded
- `file_path` - Web path to access the image
- `file_size` - Size in bytes
- `mime_type` - Image type (jpg, png, etc.)
- `display_order` - Order to show images
- `caption` - Optional description
- `uploaded_at` - Timestamp

### File Storage

Images are stored in: `/uploads/projects/`

Filenames are unique: `project_1_67548abc123.jpg`

This prevents conflicts and organizes files by project.

### Security Features

1. **Admin-only access** - Must be logged in as admin
2. **File type validation** - Only images allowed (jpg, png, gif, webp)
3. **File size limit** - 10MB maximum
4. **No PHP execution** - `.htaccess` prevents running PHP in uploads
5. **MIME type checking** - Validates actual file content, not just extension
6. **Directory browsing disabled** - Can't list files in uploads folder

---

## Integration with Projects Page

To display images from this system on your projects page:

### Option 1: Show All Project Images

```php
// In projectsPage.php, modify the image display:
$stmt = $pdo->prepare("
    SELECT pi.file_path
    FROM project_images pi
    WHERE pi.project_id = :project_id
    ORDER BY pi.display_order
    LIMIT 1
");
$stmt->execute([':project_id' => $project['id']]);
$featuredImage = $stmt->fetchColumn();
```

### Option 2: Create Image Gallery

```php
// Get all images for a project
$stmt = $pdo->prepare("
    SELECT * FROM project_images
    WHERE project_id = :project_id
    ORDER BY display_order
");
$stmt->execute([':project_id' => $projectId]);
$images = $stmt->fetchAll();

// Display them:
foreach ($images as $img) {
    echo '<img src="' . htmlspecialchars($img['file_path']) . '">';
}
```

---

## Troubleshooting

### "Permission denied" error when uploading
**Solution:** Run the chmod command from Step 2 again

### Images upload but don't display
**Solution:** Check that the file path starts with `/NixonNormanProject/uploads/projects/`

### "Database error"
**Solution:** Make sure you ran the SQL from Step 1 and the table exists

### Upload button doesn't work
**Solution:** Check browser console for JavaScript errors. Make sure you're logged in as admin.

### File too large error
**Solution:** Images must be under 10MB. You can adjust this in `upload_image.php` line 14

---

## File Size and Format Limits

**Supported formats:**
- JPEG/JPG
- PNG
- GIF
- WebP

**Maximum file size:** 10MB per image

**To change the limit**, edit `upload_image.php`:
```php
$maxFileSize = 10 * 1024 * 1024; // 10MB - change this number
```

---

## Advanced: Auto-Resize Images (Optional)

If you want to automatically resize large images, add this to `upload_image.php` after line 76:

```php
// Optional: Resize large images
if ($file['size'] > 2 * 1024 * 1024) { // If larger than 2MB
    $image = imagecreatefromjpeg($file['tmp_name']);
    // Resize logic here
}
```

---

## Support

If you encounter issues:
1. Check XAMPP is running
2. Check Apache error logs: `/Applications/XAMPP/xamppfiles/logs/error_log`
3. Verify database connection in phpMyAdmin
4. Check folder permissions

---

## Summary

You now have a complete image management system:
- ✅ Non-technical users can upload/delete images
- ✅ Secure file handling and validation
- ✅ Clean admin interface with drag & drop
- ✅ Database integration for organization
- ✅ Ready to display on projects page

**Next Steps:**
1. Run the SQL to create the table
2. Set folder permissions
3. Test uploading images
4. Integrate image display on your projects page
