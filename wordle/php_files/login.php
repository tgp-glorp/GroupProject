<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include("sessionManager.php");
// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo "Both email and password are required.";
        exit();
    }

    // Database connection
    $serverName = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbName = "mydb"; // Change this to your database name

    $conn = new mysqli($serverName, $db_username, $db_password, $dbName);

    // Check connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to fetch the user data
    $sql = "SELECT *
            FROM users 
            WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if ($user && $user["account_activation_hash"] === null) {

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Start session and store user data

            echo "userId" . $user["id"];
            $_SESSION["user_id"] = $user["id"];

            $_SESSION["username"] = $user["username"];

            $_SESSION["logged in"] = true;

            $_SESSION["start_time"] = time();

            $session_id = session_id();

            $user_id = $user["id"];

            var_dump($session_id, $user_id);

            $sql = "INSERT 
                    INTO user_sessions (session_id, user_id)
                    VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                echo "prepare failed";
            }
            $stmt->bind_param("si", $session_id, $user_id);
            if (!$stmt->execute()) {
                die("Database error: " . $stmt->error); // Debug database error
            }
            header("Location:../wordle.php");
            exit();  // Ensure no further code is executed after the redirect
        } else {
            // Incorrect password
            echo "Incorrect password. Please try again.";
        }
    } else {
        // No user found with that email
        echo "No user found with that email.";
    }

    // Clean up
    $stmt->close();
    $conn->close();
} else {
    echo "Please submit the form.";
}
?>