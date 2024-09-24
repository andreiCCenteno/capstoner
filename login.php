<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="./login.css">
    <style>
        /* Modal styles */
        .modal {
            display: none; 
            position: fixed;
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            text-align: center;
            color: black; /* Set text color to black */
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h2>LOGIN FORM</h2>
        <form method="POST">
            <label>Username:</label>
            <input type="text" id="username" name="username" required>

            <label>Password:</label>
            <input type="password" id="pass" name="pass" required>

            <a href="#">Forgot Password?</a>

            <button type="submit" name="login">LOGIN</button>

            <p>Don't have an account? <a href="registration.php">Create Account</a></p>
        </form>
    </div>
    
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalMessage"> <!-- Modal message container --></p>
        </div>
    </div>

    <div class="footer">@All Rights Reserved</div>

    <script>
        // Modal handling
        function showModal(message) {
            console.log("showModal called with message:", message); // Debugging log
            document.getElementById('modalMessage').innerText = message; // Set modal message text
            let modal = document.getElementById('myModal');
            modal.style.display = "block";
        }

        // Close modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('myModal').style.display = "none";
        }

        window.onclick = function(event) {
            let modal = document.getElementById('myModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

<?php
session_start(); // Start the session
if(isset($_POST['login'])){
    include_once("config.php");

    $userName = $_POST['username'];
    $pass = $_POST['pass'];

    // Prepare statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT userId, username, pass FROM tblUsers WHERE username = ?");
    $stmt->bind_param('s', $userName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user_data = $result->fetch_assoc()) {
        $userId = $user_data['userId'];
        $passWord = $user_data['pass'];

        // Store userId and username in the session
        $_SESSION['userId'] = $userId;
        $_SESSION['username'] = $user_data['username']; // Store the username in the session

        // Verify the password
        if (password_verify($pass, $passWord)) {
            $message = "WELCOME USER! Redirecting to the main menu...";
            echo "<script>showModal('$message'); setTimeout(function(){ window.location.href = 'mainmenu.php'; }, 2000);</script>";
        } else {
            $message = "Incorrect username or password!";
            echo "<script>showModal('$message');</script>";
        }
    } else {
        $message = "Incorrect username or password!";
        echo "<script>showModal('$message');</script>";
    }

    $stmt->close();
}
?>
</body>
</html>
