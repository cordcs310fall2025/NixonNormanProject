<?php $currentPage = 'contact'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Nixon Norman Media — Contact</title>
</head>
<body>

<!-- Navigation -->
<?php include '../includes/header.php'; ?>

<div class="contactHero">
    <section class="quoteOrderWelcome">
        <h2>Order a Quote Today!</h2>
    </section>
    <section class="contactInfoEntry">
        <form action="contactSubmit.php" method="post">
        <div class="emailInputSection">
            <label for="email">Email:</label>
            <input type="text" id="email" name="email" placeholder="Enter your email">
        </div>

        <div class="jobSelectionSection">
            <label for="jobType">Choose a Job Type:</label>
            <select id="jobType" name="selectedJobType">
                <option value="commercialPhotoShoot">Commercial PhotoShoot</option>
                <option value="commercialVideoShoot">Commercial VideoShoot</option>
                <option value="eventPhotoShoot">Event PhotoShoot</option>
                <option value="eventVideoShoot">Event VideoShoot</option>
            </select>
        </div>

        <div class="jobDateSelectionSection">
            <label for="jobDate">Select a Date for Your Shoot:</label>
            <input type="date" id="jobDate" name="jobDate">
        </div>

        <div class="jobCommentsSection">
            <label for="jobComments">Job Details:</label>
            <textarea id="jobComments" name="jobComments" rows="5" cols="40" placeholder="Enter your job details here"></textarea>
        </div>

        <div class="submitButton-div">
            <button type="submit">Submit</button>
        </div>
        </form>
    </section>

    <!--FOOTER-->
    <?php include '../includes/footer.php'; ?>  

</div>
</body>
</html>
