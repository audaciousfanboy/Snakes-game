const scoreTrack = document.getElementById("scoreTrack");
const highScoreTrack = document.getElementById("highScoreTrack");
const instructionAlert = document.getElementById("instructionAlert");
const highScoreForm = document.getElementById("highScoreForm");

//BOARD
var blockSize = 25,
  rows = 20,
  cols = 20,
  board,
  context;

//SNAKE HEAD
var snakeX = blockSize * 5;
var snakeY = blockSize * 5;
var velocityX = 0,
  velocityY = 0;
// SNAKE BODY
var snakeBody = [];

//SNAKE FOOD
var foodX;
var foodY;

//SCORE  CHANGE IF LALAGAY SA DATA BASE
let score = 0;
let highScore = Math.max(...leaderboardJSON.map((entry) => entry.userScore));
highScoreTrack.textContent = `High Score : ${highScore}`;

//GAMEOVER
var gameOver = false;

//On Start-UP
window.onload = function () {
  board = document.getElementById("board");
  board.height = rows * blockSize;
  board.width = cols * blockSize;
  context = board.getContext("2d"); //to draw on the board

  document.addEventListener("keyup", changeDirection);
  document.addEventListener("keyup", function (event) {
    // Check if any key is pressed
    if (instructionAlert) {
      // Hide the text message by changing its style
      instructionAlert.style.display = "none";
    }
  });
  placeFood();

  //Update Consistently every 100millis
  setInterval(update, 1000 / 10);
};

function update() {
  if (gameOver) {
    return;
  }
  //Board Style
  context.fillStyle = "rgba(158,192,145,255)";
  context.fillRect(0, 0, board.width, board.height);
  context.strokeStyle = "Black";
  context.lineWidth = 7;
  context.strokeRect(0, 0, board.width, board.height);

  //Food Style
  context.fillStyle = "rgb(76,92,68)";
  context.fillRect(foodX, foodY, blockSize, blockSize);

  //Check if snake ate the food
  if (snakeX == foodX && snakeY == foodY) {
    snakeBody.push([foodX, foodY]);
    placeFood();
    score++;
    highScore = score >= highScore ? score : highScore;
    localStorage.setItem("highScoreTrack", highScore);
    scoreTrack.textContent = `${score}`;
    highScoreTrack.textContent = `High Score : ${highScore}`;
  }
  for (let i = snakeBody.length - 1; i > 0; i--) {
    snakeBody[i] = snakeBody[i - 1];
  }
  if (snakeBody.length) {
    snakeBody[0] = [snakeX, snakeY];
  }
  context.fillStyle = "Black";
  snakeX += velocityX * blockSize;
  snakeY += velocityY * blockSize;
  context.fillRect(snakeX, snakeY, blockSize, blockSize);

  for (let i = 0; i < snakeBody.length; i++) {
    context.fillRect(snakeBody[i][0], snakeBody[i][1], blockSize, blockSize);
  }

  //GAME OVER IF
  if (
    snakeX < 0 ||
    snakeX > cols * blockSize ||
    snakeY < 0 ||
    snakeY > rows * blockSize
  ) {
    gameOver = true;
    showAlert();
    //Check if a score is beaten
    for (let x = 0; x < leaderboardJSON.length; x++) {
      if (score > leaderboardJSON[x].userScore) {
        highScoreAlert.style.display = "block";
        highScoreAlert.addEventListener("submit", (e) => {
          e.preventDefault();
          const userName = document.getElementById("userName").value;
          submitHighScore(userName, score);
        });
        break;
      }
    }
  }

  for (let i = 0; i < snakeBody.length; i++) {
    if (snakeX == snakeBody[i][0] && snakeY == snakeBody[i][1]) {
      gameOver = true;
      showAlert();
      yesBtn.addEventListener("click", yesAlert);
      noBtn.addEventListener("click", noAlert);
    }
  }
}

function placeFood() {
  foodX = Math.floor(Math.random() * cols) * blockSize;
  foodY = Math.floor(Math.random() * rows) * blockSize;
}

function changeDirection(e) {
  if (e.code == "ArrowUp" && velocityY != 1) {
    velocityX = 0;
    velocityY = -1;
  } else if (e.code == "ArrowDown" && velocityY != -1) {
    velocityX = 0;
    velocityY = 1;
  } else if (e.code == "ArrowLeft" && velocityX != 1) {
    velocityX = -1;
    velocityY = 0;
  } else if (e.code == "ArrowRight" && velocityX != -1) {
    velocityX = 1;
    velocityY = 0;
  }
}

//Game over
const customAlert = document.getElementById("gameOverAlert");
const yesBtn = document.getElementById("yesBtn");
const noBtn = document.getElementById("noBtn");

// Function to show the custom alert
function showAlert() {
  customAlert.style.display = "block";
  yesBtn.addEventListener("click", yesAlert);
  noBtn.addEventListener("click", noAlert);
}

// Function to close the custom alert
function yesAlert() {
  customAlert.style.display = "none";
  location.reload();
  //localStorage.clear();
}
// Function to close window when no is selected
function noAlert() {
  window.close();
}

// Function to Submit Score to DB
function submitHighScore(userName, score) {
  // Send Name and Score to PHP Script
  const submitButton = document.getElementById("submitButton");
  fetch("./includes/dbh.inc.php", {
    method: "POST",
    body: `userName=${userName}&score=${score}`,
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
    .then((response) => {
      if (response.ok) {
        highScoreAlert.style.display = "none";
        submitButton.disabled = true;
      } else {
        alert("Error submitting high score.");
      }
    })
    .catch((error) => console.error(error));
}
