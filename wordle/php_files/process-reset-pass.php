<?php

// Get the token from the POST data
$token = $_POST["token"];

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
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if a user was found
if ($user === null) {
    die("Token not found.");
}

// Check if the token has expired
if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired.");
}

// Only process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $password = trim($_POST['password']);

    // Validate input
    if (empty($password)) {
        echo "Password is empty";
        exit();
    }

    if (strlen($password < 8)) {
        die("Password must be at least 8 characters");
    }
    
    if (! preg_match("/[a-z]/i", $password)) {
        die("Password must contain at least one letter");
    }

    if (! preg_match("/[0-9]/", $password)) {
        die("Password must contain at least one number");
    }

    if ($_POST["password"] !== $_POST["password"]) {
        die("Passwords must match");
    }



    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL query to update the user's password
    $sql = "UPDATE users
            SET password = ?,
                reset_token_hash = NULL,
                reset_token_expires_at = NULL
            WHERE id = ?";

    $stmt = $mysqli->prepare($sql);

    // Use "si" since "id" is an integer
    $stmt->bind_param("si", $hashed_password, $user["id"]);

    if ($stmt->execute()) {
     // Success message
    echo '<div class="message">Password updated. You can now login.</div>';

    // Add JavaScript for the 5-second delay before redirecting
    echo '<script>
            setTimeout(function() {
                window.location.href = "../index.php";
            }, 5000); // Redirect after 4000 milliseconds (4 seconds)
          </script>';
} else {
    echo "Failed to update password: " . $stmt->error;
}

}

?>

<style>
    .message {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: lightgreen;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        font-size: 16px;
        font-weight: bold;
        text-align: center;
        width: auto;
        max-width: 300px;
    }
</style>