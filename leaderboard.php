<?php
session_start();

// Assuming the logged-in user's username is stored in the session
$current_user = $_SESSION['username'];

// Connect to the database
$servername = "localhost"; // Adjust as necessary
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "capstone_db"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to get the top 5 players and the current logged-in user
$sql = "
(
    SELECT username, score FROM tblUsers
    ORDER BY score DESC LIMIT 5
)
UNION
(
    SELECT username, score FROM tblUsers
    WHERE username = '$current_user'
)
ORDER BY score DESC";

// Execute the query
$result = $conn->query($sql);

// Prepare an array to hold the results and prevent duplicates
$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Avoid adding duplicate entries
        if (!isset($users[$row['username']])) {
            $users[$row['username']] = $row['score'];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;700&display=swap');

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            font-family: 'Roboto Mono', monospace;
            color: #00ffcc;
            text-align: center;
            background: linear-gradient(135deg, #ff0066, #ff9933, #ffff00, #33cc33, #0066ff, #9933ff);
            background-size: 400% 400%;
            animation: gradientAnimation 5s ease infinite;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1;
        }

        h1 {
            color: #00ffcc;
            text-shadow: 0 0 10px rgba(0, 255, 204, 0.7);
            margin-bottom: 20px;
            font-size: 3.5em;
            font-family: 'Orbitron', sans-serif;
        }

        .leaderboard {
            background: rgba(0, 0, 0, 0.8) url('path/to/your/background-image.jpg') no-repeat center center;
            background-size: cover;
            padding: 50px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 255, 204, 0.7);
            max-width: 600px;
            margin: auto;
            z-index: 2;
        }

        .leaderboard ul {
            list-style: none;
            padding: 0;
        }

        .leaderboard li {
            margin: 10px 0;
            color: #fff;
            font-size: 1.2em;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 50px;
            height: 50px;
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 50%;
            cursor: pointer;
            text-align: center;
            line-height: 50px;
            font-size: 1.5em;
            color: #00ffcc;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease;
            z-index: 2;
        }

        .back-button:hover {
            background-color: #00d1b2;
            transform: scale(1.1);
        }

        .back-button:focus {
            outline: 2px solid #00ffcc;
            outline-offset: 4px;
        }
    </style>
</head>
<body>
    <a href="mainmenu.php" class="back-button" title="Back">&larr;</a>
    <div class="leaderboard">
        <h1>Leaderboard</h1>
        <ul>
            <?php
            if (!empty($users)) {
                foreach ($users as $username => $score) {
                    // Highlight the current logged-in user
                    if ($username == $current_user) {
                        echo "<li><strong>$username (You)</strong> - $score points</li>";
                    } else {
                        echo "<li>$username - $score points</li>";
                    }
                }
            } else {
                echo "<li>No scores available yet.</li>";
            }

            // Close the database connection
            $conn->close();
            ?>
        </ul>
    </div>
</body>
</html>
