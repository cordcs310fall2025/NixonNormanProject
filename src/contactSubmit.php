<?php
//POST data
$email = $_POST['email'];
$selectedJobType = $_POST['selectedJobType'];
$jobDate = $_POST['jobDate'];
$jobComments = $_POST['jobComments'];


// DB connection
$conn = new mysqli('localhost', 'root', '', 'nixon_norman_media');


if ($conn->connect_error) {
   die('Connection failed: ' . $conn->connect_error);
}


// SQL statement
$stmt = $conn->prepare("
   INSERT INTO contactFormSubmissions (email, jobDate, selectedJobType, jobComments)
   VALUES (?, ?, ?, ?)
");


$stmt->bind_param("ssss", $email, $jobDate, $selectedJobType, $jobComments);


// submit
if ($stmt->execute()) {
   echo "Submission successful! Expect a response in roughly _ business days";
} else {
   echo "Error: " . $stmt->error;
}


$stmt->close();
$conn->close();
?>



