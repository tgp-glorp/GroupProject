<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wordle</title>

  <!--Icons-->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="CSS/wordleDesignCSS.css">
  <link rel="stylesheet" href="CSS/wordleLast.css">
  <script src="script/WordleProject.js" defer></script>
  <script src="script/projectmodal.js" defer></script>
  <script src="script/WS.js" defer></script>
  <style>
   
      .center {
            display: flex;            /* Enables flexbox */
            justify-content: center;  /* Centers horizontally */
            align-items: center;      /* Centers vertically */
          
        }
    
    .restart-button {
      background-color: #6aaa64;
      /* A Wordle green for success */
      color: #ffffff;
      /* White text */
      font-size: 16px;
      font-weight: bold;
      padding: 10px 20px;
      left: 50%;
      border: none;
      border-radius: 8px;
      /* Rounded corners */
      cursor: pointer;
      box-shadow: 0 4px #4c8b4a;
      /* Shadow for a 3D button effect */
      transition: transform 0.2s, background-color 0.2s;
    }

    .restart-button:hover {
      background-color: #5c9960;
      /* Slightly darker green for hover effect */
      transform: scale(1.05);
      /* Subtle enlarge on hover */
    }

    .restart-button:active {
      background-color: #4c8b4a;
      /* Even darker green for active effect */
      box-shadow: 0 2px #366335;
      /* Reduced shadow for "pressed" look */
      transform: translateY(2px);
      /* Move button down when clicked */
    }

    .restart-button:disabled {
      background-color: #cfcfcf;
      /* Gray color for disabled */
      color: #7a7a7a;
      /* Dim text color */
      cursor: not-allowed;
      /* Disable cursor pointer */
      box-shadow: none;
      /* No shadow */
    }
  </style>
</head>

<body>
  <?php
  session_start();  // Start session if not already started
  
  //$sessionLimit =10; for testing
  $sessionLimit = 600; //10 mins of inactivity
  
  if (!isset($_SESSION["start_time"])) {
    $_SESSION["start_time"] = time();
  }

  $sessionTimer = time() - $_SESSION["start_time"];

  if (isset($_GET["reset_timer"])) {
    $_SESSION["start_time"] = time();
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

  <header>
    <nav>
      <ul id="menu">
        <li class="menu_element_container"> <a class="menu_element" href="#" data-modal-open="modal1"><i
              class="fas fa-question"></i> About Us</a> </li>
        <li class="menu_element_container"> <a class="menu_element" href="#" data-modal-open="modal2"><i
              class="fas fa-cog"></i> Settings
          </a> </li>
        <li class="menu_element_container"> <a class="menu_element" href="#" data-modal-open="modal3"><i
              class="fas fa-question"></i> How
            to Play </a> </li>
        <li class="menu_element_container"> <a class="menu_element" href="#" data-modal-open="modal4"><i
              class="fas fa-flag-checkered"></i> Challenge
          </a> </li>
        <li class="menu_element_container"> <a class="menu_element" href="#" data-modal-open="modal5"> <i
              class="fas fa-user-circle"></i> Profile </a> </li>
    </nav>
  </header>
  <main>
    <div id="container">

      <div class="wordle-grid">
        <form id="wordleForm" class="wordle-grid">

        </form>

      </div>
    <div class="center">
      <button class="restart-button" id="restart-button" onclick="restartWordle()">Restart</button>
    </div>
      <div id="keyboard-container">
        <div id="keyboard"></div>
      </div>
  </main>


  <!-- Modal 1 -->
  <div class="modal" id="modal1">
    <div class="modal-content">
      <h2>About Us:</h2>
      <div class="challenge-container">
        <p>We are a team of passionate developers—Fadi Dagher, Joe Abi Habib, and Rabih Hasbani—who came together to
          create an exciting project: an online Wordle game.
          Our project is a modern twist on the popular word-guessing game, featuring a database-powered backend to
          enhance the gaming experience. By leveraging our skills in coding, database management, and creative thinking,
          we designed this platform to be interactive, user-friendly, and dynamic.
          With a shared vision for innovation and teamwork, we aim to continue exploring opportunities to build unique
          and engaging projects.
        </p>
        <p>Be sure to check out our previous work—each of us has a dedicated website showcasing our past projects and
          achievements!</p>
        <ul>
          <li><a href="https://fadidagherweb.infinityfreeapp.com/?i=1" target="_blank"><i
                class="fas fa-globe"></i>https://fadidagherweb.infinityfreeapp.com/?i=1</a></li>
          <li><a href="https://joeabihabibweb2.infinityfreeapp.com/?i=2" target="_blank"><i
                class="fas fa-globe"></i>https://joeabihabibweb2.infinityfreeapp.com/?i=2</a></li>
          <li><a href="http://rabiehhasbani.liveblog365.com/?i=1" target="_blank"><i
                class="fas fa-globe"></i>http://rabiehhasbani.liveblog365.com/?i=1</a></li>
        </ul>
      </div>
      <button class="close-btn" data-modal="modal1">&#10005;</button>
    </div>
  </div>


  <!-- Modal 4 -->
  <div class="modal" id="modal4">
    <div class="modal-content">
      <h2>Send Challenge</h2>
      <div class="challenge-container">
        <p>Please enter your friend's username to send them a timed challenge of 2.5 minutes.</p>
        <form method="POST">
          <label for="ChallengeInput"> Timed Challenge </label>
          <input type="text" name="username" placeholder="Username" id="ChallengeInput" required>
        </form>
        <button onclick="sendTimedChallenge()">Send Challenge</button>
        </button>
      </div>
      <button class="close-btn" data-modal="modal4">&#10005;</button>
    </div>
  </div>

  <!-- Modal 5 -->
  <div class="modal" id="modal5">
    <div class="modal-content">
      <h2><i class="fas fa-user-circle"></i> Profile</h2>
      <div class="profile-container">
        <div class="custom-line"></div>
        <p>UserName: <span id="profUsername"></span></p>
        <div class="custom-line"></div>
        <p>ID: #<span id="profId"></span></p>
        <div class="statistics">
          <h2><i class="fas fa-chart-bar"></i> Statistics</h2>
          <p><i class="fas fa-gamepad"></i> Games Played: <span id="profGplayed"></span></p>
          <div class="custom-line"></div>
          <p><i class="fas fa-trophy"></i> Games Won: <span id="profGWon"></span></p>
          <div class="custom-line"></div>
          <p><i class="fas fa-chart-line"></i> % Of Wins: <span id="winRate"></span></p>
        </div>
      </div>
      <button class="close-btn" data-modal="modal5">&#10005;</button>
      <!-- Logout Button -->
      <form method="post">
        <button type="submit" name="logout" class="logout-button">Logout</button>
      </form>
    </div>
  </div>

  <!-- Modal 2 -->
  <div class="modal" id="modal2">
    <div class="modal-content">
      <h2>Settings</h2>
      <div class="custom-line"></div>
      <div class="switch-container">
        <p><strong><i class="fas fa-moon"></i> Dark Mode</strong></p>
        <label class="switch">
          <input type="checkbox" id="darkModeToggle">
          <span class="slider"></span>
        </label>
        <script>
          const toggle = document.getElementById('darkModeToggle');

          toggle.addEventListener('change', () => {
            document.body.classList.toggle('dark-mode');
            const keyboard = document.getElementById('keyboard');
            keyboard.classList.toggle('dark-mode');

            // Ensure modal content is targeted
            const modalContent = document.querySelector('.modal-content');
            if (modalContent) {
              modalContent.classList.toggle('dark-mode');
            }
          });
        </script>
      </div>
      <button class="close-btn" data-modal="modal2">&#10005;</button>
    </div>
  </div>

  <!-- Modal 3 -->
  <div class="modal" id="modal3">
    <div class="modal-content" s>
      <h2>Wordle Game: Overview and Rules</h2>
      <div class="howtoplay-container">
        <p><strong>Wordle</strong> is a popular word puzzle game where players try to guess a hidden 5-letter word
          within
          six attempts. It became widely known for its simple yet addictive gameplay and daily challenges that encourage
          players to share their results.</p>

        <h3>How to Play</h3>
        <p>Players start by entering any 5-letter word as their first guess.</p>

        <div class="bigletters">
          <p style="background-color: green;">T</p>
          <p style="background-color: green;">A</p>
          <p style="background-color: green;">B</p>
          <p style="background-color: green;">L</p>
          <p style="background-color: green;">E</p>
        </div>

        <p>After each guess, the game provides feedback by coloring the tiles of the letters:</p>
        <div class="bigletters">
          <p style="background-color: green;">T</p>
          <p style="background-color: yellow;">A</p>
          <p style="background-color: gray;">B</p>
          <p style="background-color: GRAY;">L</p>
          <p style="background-color: yellow;">E</p>
        </div>
        <ul>
          <li><span style="color: green;">Green</span>: The letter is correct and in the right position.</li>
          <li><span style="color: yellow;">Yellow</span>: The letter is in the word but in the wrong position.</li>
          <li><span style="color: gray;">Gray</span>: The letter is not in the word at all.</li>
        </ul>

        <h3>Tips for Winning Wordle</h3>
        <ul>
          <li><strong>Start with common vowels:</strong> Use words that include common vowels like A, E, I, O, and U to
            identify them early.</li>
          <li><strong>Use diverse letters:</strong> Avoid repeating letters in your initial guesses to cover more
            possibilities.</li>
          <li><strong>Look for patterns:</strong> Based on feedback, focus on narrowing down the possible combinations.
          </li>
          <li><strong>Be strategic with placement:</strong> Pay attention to both the position and frequency of letters
            to
            refine guesses.</li>
        </ul>
      </div>
      <button class="close-btn" data-modal="modal3">&#10005;</button>
    </div>
  </div>

</body>

<script>
  // Variable to store the timeout reference
  let timeoutRef;

  // Function to reset the session timer when there's activity (e.g., mouse movement)
  function resetTimer() {
    fetch('wordle.php?reset_timer=true') // Send a request to reset the session timer
      .then(response => response.text())
      .catch(error => console.error('Error resetting timer:', error));

    // Reset the session timeout timer
    clearTimeout(timeoutRef);
    timeoutRef = setTimeout(function () {
      alert("Your session has expired due to inactivity!");
      window.location.href = 'Account.php'; // Redirect after alert
    }, <?php echo ($sessionLimit - $sessionTimer) * 1000; ?>); // session timeout
  }

  // Monitor user activity (mouse movement, keyboard, etc.)
  document.addEventListener('mousemove', resetTimer);
  document.addEventListener('keydown', resetTimer);
  document.addEventListener('click', resetTimer);

  // Trigger the session timeout alert as soon as the page loads
  resetTimer();
</script>




</html>