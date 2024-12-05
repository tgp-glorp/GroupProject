<?php

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

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

$sql = "SELECT * FROM users
        WHERE account_activation_hash = ?";

$stmt =$conn->prepare($sql);

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

$sql = "UPDATE users
        SET account_activation_hash = NULL
        WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $user["id"]);

$stmt->execute();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Activated</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../CSS/Login.css">
</head>
<body>

    <h1>Account Activated</h1>  

    <p>Account activated successfully. You can now
       <a href="../Account.php">log in</a>.</p>

</body>
</html>