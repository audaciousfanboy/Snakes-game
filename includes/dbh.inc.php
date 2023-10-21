<?php
$url = 'URL for jawsDB';
$dbparts = parse_url($url);

$hostname = $dbparts['host'];
$username = $dbparts['user'];
$password = $dbparts['pass'];
$database = ltrim($dbparts['path'], '/');

// Connect to the database
$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo '<p class="body-font">Connected to the database</p>';
}

// Check if form data is submitted for inserting data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user name and score from the form
    $userName = $_POST["userName"];
    $score = $_POST["score"];

    // Prepare and execute the SQL insert statement
    $stmt = $conn->prepare("INSERT INTO `score-board` (userId, userScore) VALUES (?, ?)");
    $stmt->bind_param("si", $userName, $score);

    if ($stmt->execute()) {
        // Successfully inserted the score
        echo "Score uploaded successfully.";
    } else {
        // Handle the error if the insertion fails
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}


// Identify the top 3 scores
$thresholdRank = 3;
$query = "SELECT userScore FROM `score-board` ORDER BY userScore DESC LIMIT $thresholdRank";
$result = mysqli_query($conn, $query);

if ($result) {
    $topScores = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $topScores[] = $row['userScore'];
    }

    // Execute a DELETE query to remove scores that are not in the top ranks
    $query = "DELETE FROM `score-board` WHERE userScore NOT IN (" . implode(',', $topScores) . ")";

    if (mysqli_query($conn, $query)) {
        //echo '<p class="body-font">Deleted scores below the top ' . $thresholdRank . '</p>';
        
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn); 
