// Function to open the modal
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.style.display = 'block';
}

// Function to close the modal
function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.style.display = 'none';
}

// Add event listeners for all open modal buttons
const openModalBtns = document.querySelectorAll('[data-modal-open]');
openModalBtns.forEach(function (btn) {
  btn.addEventListener('click', function (e) {
      e.preventDefault(); // Prevent default anchor behavior
      const modalId = this.getAttribute('data-modal-open');
      openModal(modalId);
  });
});

// Add event listeners for all close modal buttons
const closeModalBtns = document.querySelectorAll('.close-btn');
closeModalBtns.forEach(function (btn) {
  btn.addEventListener('click', function () {
      const modalId = this.getAttribute('data-modal');
      closeModal(modalId);
  });
});

// Function to close the modal if clicking outside the modal content
window.addEventListener('click', function (event) {
  // Check if the clicked element is the modal background (outside modal content)
  if (event.target.classList.contains('modal')) {
      event.target.style.display = 'none'; // Close the modal
  }
});


// Challenge popup on other user
function showChallengePopup(message) {
  
  const popup = document.createElement("div");
  popup.id = "challengePopup";
  popup.style.position = "fixed";
  popup.style.top = "50%";
  popup.style.left = "50%";
  popup.style.transform = "translate(-50%, -50%)";
  popup.style.backgroundColor = "white";
  popup.style.border = "2px solid black";
  popup.style.padding = "20px";
  popup.style.zIndex = "1000";
  popup.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.2)";
  
  // Add the challenge message
  popup.innerHTML = `
    <p>${message}</p>
    <button id="acceptChallenge">Accept</button>
    <button id="noChallenge">No</button>
  `;

  // Append the popup to the body
  document.body.appendChild(popup);

  // Handle button clicks
  document.getElementById("acceptChallenge").onclick = () => {
    alert("Challenge accepted!"); // Replace with actual logic
    acceptTimedChallenge();
    document.body.removeChild(popup);
  };

  document.getElementById("noChallenge").onclick = () => {
    alert("Challenge rejected.");
    document.body.removeChild(popup);
  };
}


