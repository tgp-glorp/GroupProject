<?php

// Start the session to store success or error messages
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/Login.CSS">
    <script src="script/WS.js" defer></script>
    <title>Login Page</title>
    <style>
    .hiddenMessage {
      display: none;
      /* Initially hidden */
      font-size: 16px;
      font-family: Arial, sans-serif;
      color: black;
    }
  </style>
</head>

<body>


    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="php_files/login.php" method="POST">
                <h1>Sign In</h1>
                <div class="social-icons">
                    <div class="social-icons">
                        <a href="#" class="icon">W</a>
                        <a href="#" class="icon">O</a>
                        <a href="#" class="icon">R</a>
                        <a href="#" class="icon">D</a>
                        <a href="#" class="icon">L</a>
                        <a href="#" class="icon">E</a>
                    </div>
                </div>
                <span> use your email password</span>
                <input type="email" name="email" id="logMail" placeholder="Email" required>
                <p id="hiddenlogMail" class="hiddenMessage"></p>
                <input type="password" name="password" min-length="8" id="logPass" placeholder="Password" required>
                <p id="hiddenLogPass" class="hiddenMessage"></p>
                <a href="php_files/forgot-pass.php">Forgot Your Password?</a>
                
                <button type="submit">Sign In</button>
            </form>

        </div>
        <div class="form-container sign-in">
            <form action="php_files/Register.php" method="POST">
                <h1>Create Account</h1>
                <div class="social-icons">
                    <a href="#" class="icon">W</a>
                    <a href="#" class="icon">O</a>
                    <a href="#" class="icon">R</a>
                    <a href="#" class="icon">D</a>
                    <a href="#" class="icon">L</a>
                    <a href="#" class="icon">E</a>
                </div>
                <span>use your email for registration</span>
                <input type="text" name="username" id="regName" placeholder="Name" required >
                <p id="hiddenName" class="hiddenMessage"></p>
                <input type="email" name="email" id="regMail" placeholder="Email" required>
                <p id="hiddenMail" class="hiddenMessage"></p>
                <input type="password" name="password" id="regPass" placeholder="Password" required>
                <p id="hiddenPass" class="hiddenMessage"></p>

                <!-- Display Success or Error Message -->
   <?php
    // Check if a session message is set and display it
    if (isset($_SESSION['message'])) {
        echo "<p style='color: red;'>" . $_SESSION['message'] . "</p>";
        // Clear the message after displaying it
        unset($_SESSION['message']);
    }
    ?>
                <button type="submit">Sign Up</button>
            </form>

        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Don't have an account press this button</p>
                    <button class="hidden" id="login">Register</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Already have an account press this button</p>
                    <button class="hidden" id="register">LogIn</button>
                </div>
            </div>
        </div>
    </div>

    <script src="script/LoginJS.js" defer></script>
</body>

</html>