Download ZIP and extract.
  Open the folder using VSCode or an IDE.
  In the terminal run the following:
  php GroupProject-main/wordle/WS.php

  (if opening the folder of the outer GroupProject-main)

  Make sure the command aligns with your file path whether opening the outer GroupProject-main or the inner one as well as if any folder name changes have been made

  This applies to the links found in send-pass-reset and Register.php (line 86) whereby path changes will prevent the localhost from properly finding the page

  Opening in localhost will result in starting at the account page

  An account must be created to start playing

  The password for registering must contain 8 letters , 1+ letters and 1+ numbers

  If it works an email will be sent to verify, once verified you can login with your account and start playing

  For the php to function properly a database must be found with the following properties:

  the db name is mydb
  
  users table with the following:
  int id auto increment primary key (the specific size is not an issue)
  username unique (varchar 16 is a reasonable size)
  password (varchar of size of 60~70 for hashing to function properly)
  reset_token_hash (varchar 64 and default null)
  reset_token_expires_at (datetime and default null null)
  account_activation_hash (varchar 64 unique default null)
  games_won int of any size default 0
  games_played int of any size default 0

  user_sessions table with the following:
  session_id primary key (varchar 40)
  user_id int
  - not needed but preferable:
  - last_activity timestamp (can be null, default value CURRENT_TIMESTAMP)

  Note:The games_won and games_played should have been implemented in a seperate table however this was not done due to the
  lack of time to properly setup a way to manage the connections in the db
  
  A few changes have been made after the comments received namely:
  - The keyboard coloring issue has been (comment in the code above the updateKeyboard function in wordleProject.js shows where the issue was)
  - The focus inconvenience has been fixed by checking if the input field is not focused rather than if wordle is focused 

-Welcome to my website
