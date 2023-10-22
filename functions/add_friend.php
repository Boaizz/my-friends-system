<?php
// Start a session to access session variables
session_start();
// Retrieve the user's email from the session
$email = $_SESSION['email'];
// Include the database connection
require_once('settings.php');
// Check if user click on add friend
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_friend"])) {
    $friend_id = $_POST["friend_id"]; 
    // Query to get the current user's friend_id based on their email
    $logged_in_user_id_query = $conn -> query("SELECT friend_id FROM friends WHERE friend_email = '$email'");
    $logged_in_user_id = $logged_in_user_id_query -> fetch_assoc()["friend_id"];
    // Query to insert a new friend relationship to 'myfriends' table
    $sql = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES($logged_in_user_id, $friend_id)";

    if ($conn -> query($sql)) {
          // Count the number of friends for the current user and the new friend
        $logged_in_user_friend_count = $conn -> query("SELECT * FROM myfriends WHERE friend_id1 = $logged_in_user_id OR friend_id2 = $logged_in_user_id") -> num_rows;
        $removed_friend_friend_count = $conn -> query("SELECT * FROM myfriends WHERE friend_id1 = $friend_id OR friend_id2 = $friend_id")  -> num_rows;
         // Update the friend count for both the current user and the new added friend in the 'friends' table
        $conn -> query("UPDATE friends SET friend_count = $logged_in_user_friend_count WHERE friend_id = $logged_in_user_id");
        $conn -> query("UPDATE friends SET friend_count = $removed_friend_friend_count WHERE friend_id = $friend_id");
        // Redirect to the friendadd.php page after successfully adding the friend
        header("Location: ../friendadd.php");
        exit();
    } else {
        // Error with SQL query
        echo "Error: " . $conn-> error;
    }
} else {
    // Error with request method
    echo "Invalid request.";
}
// Close the database connection
$conn->close();

?>



