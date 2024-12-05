<?php
session_start();  // Start session if not already started

$sessionLimit = 600;


if (!isset($_SESSION["start_time"])) {
  $_SESSION["start_time"] = time();
}

$sessionTimer = time() - $_SESSION["start_time"];

if(isset($_GET["reset_timer"])){
  $_SESSION["start_time"]=time();
  exit();
}

if ($sessionTimer > $sessionLimit) {
  $_SESSION["enter"] = false;
  $_SESSION["logged in"] = false;
  echo "<script>alert('BOOO') </script>";
  session_unset();
  session_destroy();
  header("Location: Account.php");
  exit();
}


$remainingTime = $sessionLimit - $sessionTimer;

$_SESSION['remaining_time'] = $remainingTime;

?>
<!-- Script to reset session timer on user activity using fetch -->
<script>
// Variable to store the timeout reference
let timeoutRef;

// Function to reset the session timer when there's activity (e.g., mouse movement)
function resetTimer() {
    fetch('sessionManager.php?reset_timer=true') // Send a request to reset the session timer
        .then(response => response.text())
        .catch(error => console.error('Error resetting timer:', error));
    
    // Reset the session timeout timer
    clearTimeout(timeoutRef);
    timeoutRef = setTimeout(function() {
        alert("Your session has expired due to inactivity!");
        window.location.href = '../index.php'; // Redirect after alert
    }, <?php echo ($sessionLimit - $sessionTimer) * 1000; ?>); // session timeout
}

// Monitor user activity (mouse movement, keyboard, etc.)
document.addEventListener('mousemove', resetTimer);
document.addEventListener('keydown', resetTimer);
document.addEventListener('click', resetTimer);

// Trigger the session timeout alert as soon as the page loads
resetTimer();
</script>
