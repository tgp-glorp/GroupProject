const sessionId = document.cookie.replace(
  /(?:(?:^|.*;\s*)PHPSESSID\s*=\s*([^;]*).*$)|^.*$/,
  "$1"
);
const title=document.getElementsByTagName("title")[0];
const socket = new WebSocket("ws://localhost:8080?type="+title.textContent+"&session_id="+ sessionId);

let sender="";
let endGameTimeout;
let counter=0;
socket.onopen = function () {
  console.log("Connection established");
  setWord();
  getUserData();
};

socket.onmessage = function (event) {
  const response = JSON.parse(event.data);
  console.log("Complete parsed JSON",response);
  if(response["action"]=="userData"){
    const user=document.getElementById("profUsername");
    console.log(response["username"]);
    user.textContent=response["username"];
    const ID=document.getElementById("profId");
    ID.textContent=response["id"];
    const gPlayed=document.getElementById("profGplayed");
    let games_played=response["games_played"];
    gPlayed.textContent=games_played;
    const gWon=document.getElementById("profGWon");
    let games_won=response["games_won"];
    gWon.textContent=games_won;
    const winRate=document.getElementById("winRate");
    let winrate=Math.floor((games_won/games_played)*100);
    winRate.textContent=winrate+"%";
  }
  if (response["action"] == "word") {
    if (response.error) {
      console.log("Error: " + response.error);
    } else {
      word = response["response"];
      console.log("Received word: " + word);
    }
  }
  if(response["action"]== "ReceiveChallenge"){
    let message=response["message"];
    showChallengePopup(message);
    sender=response["from"];
    console .log("From",sender);
  }
  if(response["action"]=="checkName"){
    check=response["response"];
    console.log(check);
    if(check){
      showMessage("Name","Username Already Exists","red");
    }
  }
  if(response["action"]=="checkMail"){
    check=response["response"];
    console.log(check);
    if(check){
      showMessage("Mail","Email Already Exists","red");
    }
  }
  if(response["action"]=="logMail"){
    check=response["response"];
    console.log(check);
    if(!check){
      showMessage("logMail","Email doesn't exist","red");
    }
  }
  if(response["action"]=="resetWordle"){
    resetWordle();
    word=response["response"];
    console.log(word);
  }
  if(response["action"]=="endGame"){
    alert("GAME OVER!");
    isGameOver=true;
    toggleButton();
  }
};

function getUserData(){
  sendMessage("getUserData","a");
}


// function takeWord(word) {
//   return new Promise((resolve, reject) => {
//     word = word.toLowerCase();
//     console.log(word);
//     sendMessage("CheckWord",word);
//     socket.onmessage=(event)=>{
//       response=JSON.parse(event.data);
//       responseStatus=response["response"];
//       // Handle the response based on the 'status' field
//       if (responseStatus === "success") {
//         console.log("The word exists in the database.");
//         resolve(true);
//       } else if (responseStatus === "not_found") {
//         console.log("The word does not exist in the database.");
//         resolve(false);
//       } else if (responseStatus === "error") {
//         console.log("Error: " + response.message);
//         resolve(false);
//       }
//     }
//   });
// }

function setWord() {
  if (socket.readyState == WebSocket.OPEN) {
    sendMessage("GetWord", "Bla");
  }
}



function takeChallenge() {
  if (socket.readyState == WebSocket.OPEN) {
    sendMessage("1PChal", "Bla");
  }
}

function sendTimedChallenge(){
  resetWordle();
  const input=document.getElementById("ChallengeInput");
  data=input.value;
  if(data==null){
    return;
  }
  hideButton();
  setTimeout(sendMessage("TimedChallenge",data),5 * 1000);
}

function acceptTimedChallenge(){
  resetWordle();
  sendMessage("resetWordle",sender);
  console.log(sender);
  hideButton();
  endGameTimeout=setTimeout(endGame,90* 1000)//1:30 minutes , *1000 since ms
}

function endGame(){
  if(endGameTimeout){
    clearTimeout(endGameTimeout)
  }
  sendMessage("endGame",sender);
  alert("GAME OVER!");
  isGameOver=true;
  toggleButton();
}

function decideWinner(){
  sendMessage("winner",sender);
}

function sendMessage(action, data) {
  const message = JSON.stringify({ action, data });
  socket.send(message);
}

// function ping(){
//   sendMessage("pong","");
// }



// if(response["action"]=="ping"){
//   counter++;
//   console.log("ping"+counter);
//   setTimeout(ping(),10*1000);
//   console.log("pong"+counter);
// }