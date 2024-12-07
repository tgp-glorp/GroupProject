const gridnumbers = 30; // 6 rows of 5 letters each
let word = "";
let tabIndex = 1;
let isGameOver = false;
let isKeyboardFocused = false;
const grid = document.querySelector("#wordleForm");

function refresh() {
  location.reload();
}
function wordleCreator() {
  for (let x = 1; x <= gridnumbers; x++) {
    const y = document.createElement("input");
    y.autocomplete = "off"; // Disable browser autofill
    y.type = "text";
    y.style.height = "60%";
    y.style.width = "60%";
    y.id = "b" + x;
    y.maxLength = 1;
    y.className = "wordle-tile";
    if (x > 5) {
      y.disabled = true;
    }
    y.addEventListener("click", () => {
      tabIndex = x; // Synchronize tabIndex with the clicked field
    });
    grid.appendChild(y);
  }
  const firstInput = document.getElementById("b1");
  firstInput.focus();
}

function removeChildren(element) {
  while (element.firstChild) {
    element.removeChild(element.firstChild);
  }
}

function resetWordle() {
  grid.innerHTML="";
  wordleCreator();
  isGameOver = false;
}

function handleKey(event) {
  if (isGameOver) return;

  const currentField = document.getElementById(`b${tabIndex}`);

  if (event.key === "Backspace") {
    if (!currentField.disabled) {
      if (currentField.value.length > 0) {
        currentField.value = "";
      } else if (tabIndex > 1 && tabIndex % 5 !== 1) {
        tabIndex--;
        const previousField = document.getElementById(`b${tabIndex}`);
        previousField.value = "";
        previousField.focus();
      }
    }
  } else if (event.key.length === 1 && /[a-zA-Z]/.test(event.key)) {
    if (!currentField.disabled && currentField.value === "") {
      event.preventDefault();
      currentField.value = event.key.toUpperCase();
      if (tabIndex % 5 !== 0) {
        tabIndex++;
        document.getElementById(`b${tabIndex}`).focus();
      }
    }
  }
}

async function inputManager(event) {
  hideButton();
  if (isGameOver || event.key !== "Enter") return;
  let userInput = "";
  let inputId = [];

  for (let i = tabIndex - 4; i <= tabIndex; i++) {
    const block = document.getElementById(`b${i}`);
    userInput += block.value;
    inputId.push(block.id);
  }

  if (userInput.length !== 5) {
    alert("Please enter exactly 5 letters.");
    return;
  }
  userInput = userInput.toLowerCase();
  check = takeWord(userInput);
  if (!check) {
    alert("word doesn't exist");
    return;
  }
  for (let i = tabIndex - 4; i <= tabIndex; i++) {
    const block = document.getElementById(`b${i}`);
    block.disabled = true;
  }

  if (tabIndex < gridnumbers) {
    for (let i = tabIndex + 1; i <= tabIndex + 5; i++) {
      document.getElementById(`b${i}`).disabled = false;
    }
    tabIndex++;
    document.getElementById(`b${tabIndex}`).focus();
  }

  gameLogic(userInput, inputId);
}

function gameLogic(userInput, inputId) {
  word = word.toLowerCase();
  userInput = userInput.toLowerCase();
  let wordMap = new Map();

  // Create a map for the word
  for (let i = 0; i < word.length; i++) {
    wordMap.set(word[i], (wordMap.get(word[i]) || 0) + 1);
  }

  // First pass: Check for correct letters (green)
  for (let i = 0; i < userInput.length; i++) {
    const elem = document.getElementById(inputId[i]);
    if (word[i] === userInput[i]) {
      elem.classList.add("green");
      updateKeyboard(userInput[i], "green"); // Update keyboard
      wordMap.set(userInput[i], wordMap.get(userInput[i]) - 1);
    }
  }

  // Second pass: Check for misplaced letters (yellow) and incorrect letters (gray)
  for (let i = 0; i < userInput.length; i++) {
    const elem = document.getElementById(inputId[i]);
    if (!elem.classList.contains("green")) {
      if (word.includes(userInput[i]) && wordMap.get(userInput[i]) > 0) {
        elem.classList.add("yellow");
        updateKeyboard(userInput[i], "yellow"); // Update keyboard
        wordMap.set(userInput[i], wordMap.get(userInput[i]) - 1);
      } else {
        elem.classList.add("gray");
        updateKeyboard(userInput[i], "gray"); // Update keyboard
      }
    }
  }

  if (userInput === word) {
    setTimeout(alert("You win!"), 1 * 1000);
    decideWinner();
    endGame();
    isGameOver = true;
    toggleButton();
  } else if (tabIndex >= gridnumbers) {
    alert(`You lose! The word was ${word}`);
    sendMessage("gameDone", "");
    isGameOver = true;
    toggleButton();
  }
}

function createKeyboard() {
  const keyboard = document.getElementById("keyboard");
  const keys = [
    ..."ABCDEFG",
    ..."HIJKLMN",
    "Enter",
    ..."OPQRSTU",
    ..."VWXYZ",
    "⌫",
  ];

  let row = document.createElement("div");
  row.classList.add("row");

  keys.forEach((key) => {
    const button = document.createElement("button");
    button.classList.add("key");
    button.textContent = key;
    button.setAttribute("data-key", key.toUpperCase()); // Ensure key matches input

    if (key === "Enter") {
      button.id = "enter";
      button.classList.add("special-key");
    } else if (key === "⌫") {
      button.id = "backspace";
      button.classList.add("special-key");
    }

    button.addEventListener("click", () => handleVirtualKey(key));

    row.appendChild(button);

    if (key === "G" || key === "N" || key === "U" || key === "⌫") {
      keyboard.appendChild(row);
      row = document.createElement("div");
      row.classList.add("row");
    }
  });

  if (row.children.length > 0) {
    keyboard.appendChild(row);
  }
}

function handleVirtualKey(key) {
  if (isGameOver) return;
  isKeyboardFocused = true;
  if (key === "Enter") {
    const enterEvent = new KeyboardEvent("keydown", { key: "Enter" });
    document.dispatchEvent(enterEvent);
  } else if (key === "⌫") {
    const backspaceEvent = new KeyboardEvent("keydown", { key: "Backspace" });
    document.dispatchEvent(backspaceEvent);
  } else {
    const letterEvent = new KeyboardEvent("keydown", { key: key });
    document.dispatchEvent(letterEvent);
  }
}

function updateKeyboard(key, status) {
  const keyButton = document.querySelector(`.key[data-key="${key.toUpperCase()}"]`);
  if (keyButton) {
    // Prioritize green over all other statuses
    if (status === "green") {
      keyButton.classList.remove("yellow", "gray");
      keyButton.classList.add("green");
    }
    // Yellow is next priority, but not if it's already green
    else if (status === "yellow" && !keyButton.classList.contains("green")) {
      keyButton.classList.remove("gray");
      keyButton.classList.add("yellow");
    }
    // Gray is the lowest priority, apply only if neither green nor yellow
    else if (
      status === "gray" &&
      !keyButton.classList.contains("green") &&
      !keyButton.classList.contains("yellow")
    ) {
      keyButton.classList.add("gray");
    }
  }
}

function inputFocus() {
  const challInput=document.getElementById("ChallengeInput");
  
  let checker= challInput == document.activeElement ? true: false;
  console.log(checker);
  return checker;
}

//to show the restart game button once the game ends and before it starts
function toggleButton(){
  const resetButton=document.getElementById("restart-button");
  if(resetButton.style.display=="none"){
    resetButton.style.display="block";
  }
}

function hideButton(){
  const resetButton=document.getElementById("restart-button");
  resetButton.style.display="none";
}

document.addEventListener("DOMContentLoaded", () => {
  wordleCreator();
  createKeyboard();
});

document.addEventListener("keydown", (event) => {
  if (!inputFocus() || isKeyboardFocused) {
    isKeyboardFocused = false;
    handleKey(event);
    inputManager(event);
  }
});

let wordList = [];

// Fetch the JSON file
fetch("../filteredFiveLetterWords.json") // Replace with your JSON file's path
  .then((response) => {
    if (!response.ok) {
      throw new Error("Network response was not ok");
    }
    return response.json();
  })
  .then((data) => {
    wordList = data; // Store the word list in the array
    console.log("Word list loaded:", wordList);
  })
  .catch((error) => {
    console.error("Error loading the JSON file:", error);
  });

function takeWord(input) {
  if (wordList.includes(input)) {
    return true;
  }
  return false;
}

function gameOverTest() {
  console.log("Is it game over?", isGameOver);
  setTimeout(gameOverTest, 10 * 1000);
}

function restartWordle(){
  resetWordle();
  setWord();
}

addEventListener("DOMContentLoaded", gameOverTest);

