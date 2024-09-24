<?php
class RegistrationClass {
    private $userId;
    private $email;
    private $username;
    private $password;
    private $confirmPassword;

    public function set_userId($userId) {
        $this->userId = $userId;
    }

    public function set_email($email) {
        $this->email = $email;
    }

    public function set_userName($username) {
        $this->username = $username;
    }

    public function set_passWord($password) {
        $this->password = $password;
    }

    public function set_confirmPassword($confirmPassword) {
        $this->confirmPassword = $confirmPassword;
    }

    public function get_userId() {
        return $this->userId;
    }

    public function get_email() {
        return $this->email;
    }

    public function get_userName() {
        return $this->username;
    }

    public function get_passWord() {
        return $this->password;
    }

    public function get_confirmPassword() {
        return $this->confirmPassword;
    }
}

if (isset($_POST['register'])) {
    include_once('config.php');
    $user = new RegistrationClass();
    $tempUname = $_POST['userName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if (isset($_POST['terms']) && $_POST['password'] === $_POST['confirmPassword']) {
        if (strlen($_POST['password']) >= 8) {
            $query = "SELECT username FROM tblUsers WHERE username = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('s', $tempUname);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo '<script>alert("The username you input is already taken!")</script>';
            } else {
                $user->set_email($email);
                $user->set_userName($tempUname);
                $user->set_passWord(password_hash($password, PASSWORD_DEFAULT));

                $insert_query = "INSERT INTO tblUsers (email, username, pass) VALUES (?, ?, ?)";
                $insert_stmt = $mysqli->prepare($insert_query);
                $insert_stmt->bind_param('sss', $user->get_email(), $user->get_userName(), $user->get_passWord());
                
                if ($insert_stmt->execute()) {
                    header("Location: login.php");
                    exit();
                } else {
                    echo '<script>alert("Error registering user. Please try again later.")</script>';
                }
            }
            $stmt->close();
        } else {
            echo '<script>alert("Your password is too short!")</script>';
        }
    } else {
        echo '<script>alert("Passwords do not match or terms not agreed!")</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Registration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./registration.css">
    <script src="https://apis.google.com/js/platform.js" async defer></script>
    <style>
        /* Add this CSS to style the buttons */
        .registration-form {
            max-width: 400px; /* Set a max width for the form */
            margin: auto; /* Center the form */
        }

        .registration-form button,
        .google-button {
            display: block; /* Make buttons block elements */
            width: 100%; /* Makes both buttons the same width */
            padding: 10px; /* Adds padding for better appearance */
            margin-top: 10px; /* Adds space between buttons */
            font-size: 16px; /* Sets a consistent font size */
            text-align: center; /* Centers the text */
            border: none; /* Removes default border */
            border-radius: 5px; /* Rounds the corners */
            cursor: pointer; /* Changes cursor to pointer on hover */
        }

        .google-button {
            background-color: #4285F4; /* Google blue color */
            color: white; /* White text color */
            text-decoration: none; /* Removes underline from link */
        }

        .google-button:hover {
            background-color: #357AE8; /* Darker blue on hover */
        }
    </style>
</head>
<body>
    <?php
    require_once 'vendor/autoload.php';

    // init configuration
    $clientID = '969593921684-iu9vfmgpe3gla9h8ovbqdi4nbbthbb6b.apps.googleusercontent.com';
    $clientSecret = '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';
    $redirectUri = 'http://localhost/capstone-project-app/registration.php';

    // create Client Request to access Google API
    $client = new Google_Client();
    $client->setClientId($clientID);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUri);
    $client->addScope("email");
    $client->addScope("profile");

    // authenticate code from Google OAuth Flow
    if (isset($_GET['code'])) {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token['access_token']);

        // get profile info
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        $email =  $google_account_info->email;
        $name =  $google_account_info->name;

        // now you can use this profile info to create account in your website and make user logged in.
    }
    ?>
    <div class="registration-form">
        <h2>Account Registration</h2>
        <form method="POST" id="registrationForm">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="userName" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>

            <input type="checkbox" id="terms" name="terms" required> I agree to the terms and conditions.

            <button type="submit" name="register">Register Account</button>

            <a href="<?php echo $client->createAuthUrl()?>" id="googleSignInBtn" class="google-button">
                <i class="fa-brands fa-google"></i>
                Register with Google
            </a>

            <p>Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
    <div id="googleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Google Registration</h2>
            <p>Registering with Google. Please wait...</p>
        </div>
    </div>
    <p class="copyright">@ All Rights Reserved</p>
    <script>
        // Show error modal
        function showError(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').style.display = 'block';
        }

        // Close modal function
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        // Handle form validation
        document.getElementById('registrationForm').addEventListener('submit', function(event) {
            var email = document.getElementById('email').value;
            var username = document.getElementById('username').value;
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            var terms = document.getElementById('terms').checked;

            if (!terms) {
                event.preventDefault();
                showError('You must agree to the terms and conditions.');
                return;
            }

            if (password !== confirmPassword) {
                event.preventDefault();
                showError('Passwords do not match.');
                return;
            }

            if (password.length < 8) {
                event.preventDefault();
                showError('Password must be at least 8 characters long.');
                return;
            }
        });
    </script>
</body>
</html>
