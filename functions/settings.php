<?php
    $host = "localhost";
    $user = "root";
    $pwd ="";
    $sql_db = "";
    $conn = new mysqli($host,$user,$pwd,$sql_db);
    
    // Create a new MySQLi database connection
    if ($conn -> connect_errno)
    {
    // If there is an error in connecting to the database, display an error message
    echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
    }
    
?>
