<?php

// Get the token from the URL
$token = $_GET["token"];

// Hash the token using SHA-256
$token_hash = hash("sha256", $token);

// Database connection details
$serverName = "localhost";
$db_username = "root";
$db_password = "";
$dbName = "mydb"; // Replace with your actual database name

// Create the connection to the database
$mysqli = new mysqli($serverName, $db_username, $db_password, $dbName);

// Check for any connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Prepare the SQL query to find the user with the matching reset token hash
$sql = "SELECT * FROM users WHERE reset_token_hash = ?";

// Prepare and bind the statement
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);

// Execute the statement
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();

// Fetch the user data
$user = $result->fetch_assoc();

// Check if a user was found
if ($user === null) {
    die("Token not found.");
}

// Check if the token has expired
if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/Login.css">
    <title>Reset Password</title>
    
    <style>
        /* Scoped styles for the reset password form */
        .reset-password-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
            margin: 0 auto;
        }

        .reset-password-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .reset-password-form label {
            font-size: 14px;
            margin-bottom: 5px;
        }

       /* Light Blue Color for the button */
       .reset-password-form button {
            padding: 15px;
            background-color: #4A90E2;  /* Light blue background */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        
        }

        .reset-password-form button:hover {
            background-color: #357ABD;  /* Darker blue on hover */
        }
    </style>
</head>
<body>

    <h1>Reset Password</h1>

    <!-- Add a specific class to scope the styles for this page -->
    <div class="reset-password-form">
    <form method="post" action="process-reset-pass.php" onsubmit="return validatePasswords()">

<input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

<label for="password">New password</label>
<input type="password" id="password" name="password" required>

<label for="password_confirmation">Repeat password</label>
<input type="password" id="password_confirmation" name="password_confirmation" required>

<button type="submit">Send</button>

</form>

    </div>

</body>

<script>
    function validatePasswords() {
        // Get the values of the password fields
        var password = document.getElementById('password').value;
        var passwordConfirmation = document.getElementById('password_confirmation').value;

        // Check if the passwords match
        if (password !== passwordConfirmation) {
            alert('The passwords do not match. Please try again.');
            return false;  // Prevent form submission
        }

        return true;  // Allow form submission
    }
</script>
</html>
