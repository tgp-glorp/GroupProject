<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (strlen($_POST["password"]) < 8) {
        die("Password must be at least 8 characters");
    }
    
    if (! preg_match("/[a-z]/i", $_POST["password"])) {
        die("Password must contain at least one letter");
    }

    if (! preg_match("/[0-9]/", $_POST["password"])) {
        die("Password must contain at least one number");
    }

    if ($_POST["password"] !== $_POST["password"]) {
        die("Passwords must match");
    }


    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $activation_token = bin2hex(random_bytes(16)); // Generates a random token
    $activation_token_hash = hash("sha256", $activation_token); // Hash the token using sha256

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

    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email already exists";
    } else {
        // Check if username already exists
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Username already exists, show a message
            echo "Name already exists";
        } else {
             $sql = "INSERT INTO users (username, email, password,account_activation_hash) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $username, $email, $hashed_password, $activation_token_hash);


            if ($stmt->execute()) {

                  // Initialize PHPMailer
    require __DIR__ . "/mailer.php";
    $mail = initMailer();  // Get the PHPMailer object from the function

    // Set email sender and recipient
    $mail->setFrom("joeabihabib123@gmail.com", "Wordle Game");
    $mail->addAddress($email);
    $mail->Subject = "Acount Activation";
    $mail->Body = <<<END
    <p>Click <a href="http://localhost:/Gin440/GroupProject/wordle/php_files/activate_account.php?token=$activation_token">here</a> to activate account.</p>

    END;

    try {
        // Send the email
        $mail->send();
        echo "Message sent, please check your inbox.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        exit;
    }
                //header("Location:/Final Project/index.php");
            } 
        }
    }

    // Clean up
    $stmt->close();
    $conn->close();
} 
