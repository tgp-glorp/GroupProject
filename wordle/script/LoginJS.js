const container = document.getElementById("container");
const registerBtn = document.getElementById("register");
const loginBtn = document.getElementById("login");
const regName = document.getElementById("regName");
const regMail = document.getElementById("regMail");
const regPass =document.getElementById("regPass");
const logMail = document.getElementById("logMail");
const logPass = document.getElementById("logPass");

registerBtn.addEventListener("click", () => {
  container.classList.add("active");
});

loginBtn.addEventListener("click", () => {
  container.classList.remove("active");
});

regName.addEventListener("input", () => checkName(regName.value));

regMail.addEventListener("input", () => checkMail(regMail.value));

logMail.addEventListener("input",()=> loggingMail(logMail.value));

regPass.addEventListener("input", () => {
  input = document.getElementById("regPass").value;
  if (input.length < 8) {
    showMessage("Pass","Your Password Must Be 8+ characters", "red");
  }

  if (!/[a-z]/i.test(input)) {
    showMessage("Pass","Your password must contain 1+ letters", "red");
  }

  if (!/[0-9]/.test(input)) {
    showMessage("Pass","Your password must contain 1+ numbers", "red");
  }
});

logPass.addEventListener("input", () => {
  input = document.getElementById("logPass").value;
  if (input.length < 8) {
    showMessage("LogPass","Your Password Must Be 8+ characters", "red");
  }

  if (!/[a-z]/i.test(input)) {
    showMessage("LogPass","Your password must contain 1+ letters", "red");
  }

  if (!/[0-9]/.test(input)) {
    showMessage("LogPass","Your password must contain 1+ numbers", "red");
  }
});

function checkName(nameVal) {
  console.log(nameVal);
  sendMessage("checkName", nameVal);
}

function checkMail(mailVal) {
  console.log(mailVal);
  sendMessage("checkMail", mailVal);
}

function loggingMail(mailVal){
  console.log(mailVal);
  sendMessage("logMail",mailVal);
}




function showMessage(element,message, color) {
  const messageElement = document.getElementById("hidden"+element);

  // Set the message content dynamically
  messageElement.textContent = message;

  // Make the message visible
  messageElement.style.display = "block";

  // Set the background color dynamically
  messageElement.style.backgroundColor = "white";

  messageElement.style.color = color;

  setTimeout(() => {
    messageElement.style.display = "none";
  }, 3 * 1000);
}
