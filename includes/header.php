<?php
// includes/header.php
// Shared navigation for all pages
?>
   <div class="header">
        <div class="inner_header">
            <div class="logo_container">
                    <img src="../images/NNM-white.png" alt="Nixon Norman Media Logo" width="80" height="80">
            </div>
            <nav class="navigation">
                <ul>
                    <li><a href="homePage.php" <?php echo ($currentPage == 'home') ? 'class="active"' : ''; ?>>Home</a></li>
                    <li><a href="aboutPage.php" <?php echo ($currentPage == 'about') ? 'class="active"' : ''; ?>>About</a></li>
                    <li><a href="contactPage.html">Contact</a></li> <!--IN HTML-->
                    <!--<li><a href="gearPage.php">Gear</a></li>-->
                    <li><a href="projectsPage.php">Projects</a></li>
                    <li><a href="adminHome.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </div>