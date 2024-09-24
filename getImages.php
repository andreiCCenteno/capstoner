<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstone_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$difficulty = $_GET['difficulty'];
$numChoices = 5; // Default for easy
$numCorrect = 3; // Default for easy

switch ($difficulty) {
    case 'medium':
        $numChoices = 7;
        $numCorrect = 4;
        break;
    case 'hard':
        $numChoices = 10;
        $numCorrect = 1;
        break;
}

// Fetch images from the database
$sql = "SELECT image_url FROM images WHERE difficulty = '$difficulty' ORDER BY RAND() LIMIT $numChoices";
$result = $conn->query($sql);

$choices = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $choices[] = $row['image_url'];
    }
}

// Assume target image URL is pre-defined or fetched similarly
$targetImage = 'images/target_image.jpg'; // Update as needed

$response = [
    'target' => $targetImage,
    'choices' => $choices
];

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
