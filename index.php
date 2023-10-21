<?php require_once 'includes/dbh.inc.php';?>
<!DOCTYPE html>
<head>
    <title>Snake Game</title>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="images/snake-icon.png">
    <meta name="viewport" , content="width-device-width, ,initial-scale-1.0" />
    <!--STYLES and FONTS HERE-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="styles.css" />


</head>


<body>

<div class="main-back-ground"> 
  <h1 class="no-cursor title-font">SNAKE</h1>

  <!--Score Board-->
  <table class="table-position"> 
    <tr> 
      <th  class="no-cursor body-font" >Score : <span id="scoreTrack"> 0</span></h2> 
      <th class="no-cursor body-font" id="highScoreTrack"> </th>
    </tr>
  </table>

  <!--Instructions-->
  <div class="instruction-alert" id="instructionAlert">
    <p class="body-font">Use Arrow keys to move the snake</p>
  </div>

  <!--Game Over Alert-->
  <div id="gameOverAlert" class="gameOver-Alert">
    <h2 class="no-cursor title-font">Game Over</h2>
    <p class="no-cursor body-font">Try again?</p>
    <button class="no-cursor btn-design body-font" id="yesBtn">Yes</button>
    <button class="no-cursor btn-design body-font" id="noBtn">No, Close Game</button>
  </div>
  <!--High Score Alert -->
  <div id="highScoreAlert" class="highScore-Alert">
    <h2 class="no-cursor title-font">Congratulations!</h2>
    <p class="no-cursor body-font">You got a high score </p>
    <p class="no-cursor body-font">Enter your Name:</p>
    <form id="highScoreForm" method="post" action="./includes/dbh.inc.php">
      <input type="text" name="userName" class="name-field body-font" id="userName" placeholder="Enter Name (Max 10 Characters)" maxlength="10">
      <input class="no-cursor btn-design body-font" type="submit" value="Submit" id="submitButton">
    </form>
    </div>
  
  <!-- Canvas-->
  <canvas id="board"></canvas>
 
  
  <!--Leader Board-->
  <h1 class="no-cursor title-font">Top Scores</h1>
  <?php
   // Re-establish a new database connection for retrieving data
   $conn = mysqli_connect($hostname, $username, $password, $database);
  if (!$conn) {
     die("Connection failed: " . mysqli_connect_error());
  }
  // Execute query to retrieve scores
  $query = "SELECT userId, userScore FROM `score-board` ORDER BY userScore DESC LIMIT 3";
  $result = mysqli_query($conn, $query);
  
  if ($result) {
     // Display the scores in an HTML table
     $leaderboardData=array();
      echo '<table class="score-board-table-position">';
      echo '<tr><th class="no-cursor body-font table-header" >Name</th><th class="no-cursor body-font table-header">Score</th></tr>';

      while ($row = mysqli_fetch_assoc($result)) {
          echo '<tr>';
          echo '<td class="no-cursor body-font" >' . $row['userId'] . '</td>';
          echo '<td class="no-cursor body-font" >' . $row['userScore'] . '</td>';
          echo '</tr>';
          $leaderboardData[]=$row;
      }

      echo '</table>';
      echo '<script>';
      echo 'var leaderboardJSON = ' . json_encode($leaderboardData) . ';';
      echo '</script>';
  } else {
     echo "Error: " . mysqli_error($conn);
  }
  ?>

<!--script-->
<script src="script.js"></script>
</div>
</body>

  </body>
</html>
