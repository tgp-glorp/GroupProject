<?php
$email = $_POST["email"];

$token = bin2hex(random_bytes(16)); // Generates a random token
$token_hash = hash("sha256", $token); // Hash the token using sha256
$expiry = date("Y-m-d H:i:s", time() + 60 * 30); // Token expiry time (30 minutes)


// Database connection
$serverName = "localhost";
$db_username = "root";
$db_password = "";
$dbName = "mydb";

$conn = new mysqli($serverName, $db_username, $db_password, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$sql = "UPDATE users
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($conn->affected_rows) {

    // Initialize PHPMailer
    require __DIR__ . "/mailer.php";
    $mail = initMailer();  // Get the PHPMailer object from the function

    // Set email sender and recipient
    $mail->setFrom("joeabihabib123@gmail.com", "Wordle Game");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END
    <p>Click <a href="http://localhost:/GroupProject-main/GroupProject-main/wordle/php_files/reset-password.php?token=$token">here</a> to reset your password.</p>
    END;


    try {
        // Send the email
        $mail->send();
        echo "Message sent, please check your inbox.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
} else {
    echo "Error updating the user record.";
}
