<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>My Friend System</title>
    <meta name="description" content="assignment2">
    <meta name="keywords" content="103797499">
    <meta name="author" content="Dang Khanh Toan Nguyen">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<?php
    // Include the navigation bar using PHP
    include('functions/navbar.php');
?>
<body>
    <div class="container mt-5">
       
        <div class="wrapper">
            <div class="intro">My Friend System</div>
            <div class="intro">Assignment Home Page</div>
            <div class="description mb-2">Name: Dang Khanh Toan Nguyen</div>
            <div class="description mb-2">Student ID: 103797499</div>
            <div class="description mb-2">Email: <a href="mailto:103797499@student.swin.edu">103797499@student.swin.edu</a></div>
            <div class="description mb-4">I declare that this assignment is my individual work. I have not worked collaboratively, nor have I copied from any other student’s work or from any other source</div>
            <p><?php
            // Include settings and database connection
                include('functions/settings.php');
                // Check if the 'friends' table exists, if not, create it
                $table_query = "SELECT friend_id FROM friends";
                $table_query_result = $conn ->query($table_query);
                if (empty($table_query_result)) {
                    // Create 'friends' table if it doesn't exist
                    $table_query = "CREATE TABLE IF NOT EXISTS friends (
                        friend_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        friend_email VARCHAR(50) NOT NULL,
                        password VARCHAR(20) NOT NULL,
                        profile_name VARCHAR(30) NOT NULL,
                        date_started DATE NOT NULL,
                        num_of_friends INT UNSIGNED
                    )";
                    $table_query_result = $conn ->query($table_query);
                    if (!$table_query_result){
                    echo $conn-> error;
                    }
                }
                 // Check if the 'myfriends' table exists, if not, create it
                $table_query1 = "SELECT friend_id1 FROM myfriends";
                $table_query_result1 = $conn ->query($table_query1);
                if (empty($table_query_result1)) {

                    // Create 'myfriends' table if it doesn't exist
                    $table_query1 = "CREATE TABLE myfriends (
                        friend_id1 INT NOT NULL,
                        friend_id2 INT NOT NULL,
                        PRIMARY KEY (friend_id1, friend_id2),
                        CONSTRAINT fk_friend1 FOREIGN KEY (friend_id1) REFERENCES friends(friend_id),
                        CONSTRAINT fk_friend2 FOREIGN KEY (friend_id2) REFERENCES friends(friend_id),
                        CHECK (friend_id1 != friend_id2)
                    )";
                    $table_query_result1 = $conn ->query($table_query1);
                    if (!$table_query_result1){
                    echo $conn-> error;
                    }
                }
                // Populate 'friends' table with dummy data if it's empty
                $conn ->query("ALTER TABLE friends AUTO_INCREMENT = 1");
                $result = $conn ->query("SELECT COUNT(*) FROM friends");
                $row =  $result -> fetch_row();
                if ($row[0] == 0) {
                    $friends_query = "INSERT INTO friends (friend_email, password, profile_name, date_started) VALUES 
                    ('toan@example.com', '1', 'Toan', '2023-01-01'),
                    ('dat@example.com', '2', 'Dat', '2023-02-01'),
                    ('tung@example.com', '3', 'Tung', '2023-03-01'),
                    ('hung@example.com', '4', 'Hung', '2023-04-01'),
                    ('khoa@example.com', '5', 'Khoa', '2023-04-01'),
                    ('huy@example.com', '6', 'Huy', '2023-05-01'),
                    ('duc@example.com', '7', 'Duc', '2023-06-01'),
                    ('minh@example.com', '8', 'Minh', '2023-07-01')
                    ";

                    if ($conn ->query($friends_query)) {
                        echo "<div class='content success'><p>Users added successfully</p></div>";
                    } else {
                        echo "<div class='content error'><p>Error adding users: </p>" . $conn-> error . "</div>";
                    }
                }
                // Populate 'myfriends' table with dummy data if it's empty
                $conn ->query("ALTER TABLE myfriends AUTO_INCREMENT = 1");
                $result = $conn ->query("SELECT COUNT(*) FROM myfriends");
                $row =  $result -> fetch_row();
                if ($row[0] == 0) {
                    $myfriends_query = "INSERT IGNORE INTO myfriends (friend_id1, friend_id2) VALUES
                    (1, 2), (1, 3), (1, 4),
                    (2, 4), (2, 5), (2, 6),
                    (3, 4), (3, 5), (3, 7),
                    (4, 5), (4, 6), (4, 8)

                    ";

                    if ($conn ->query($myfriends_query)) {
                        echo "<div class='content success'><p>MyFriends added successfully</p></div>";
                    } else {
                        echo "<div class='content error'><p>Error adding MyFriends: " . $conn-> error . "</p></div>";
                    }
                }

                // Count the number of friends for each user and update the 'num_of_friends' column in the 'friends' table
                $result = $conn ->query("UPDATE friends
                SET num_of_friends = (
                    SELECT COUNT(*)
                    FROM myfriends
                    WHERE (friend_id1 = friends.friend_id OR friend_id2 = friends.friend_id)
                );");
                if ($result) {
                    echo "<div class='content success'><p>The num_of_friends column in the friends table has been updated.</p></div>";
                } else {
                    echo "<div class='content error'><p>Error updating the num_of_friends column: " . $conn-> error . "</p></div>";
                }

                if ($table_query_result1 && $table_query_result)
                {
                    echo "<p>Tables successfully created and populated with data.</p>";
                }
                // Close the database connection
                $conn->close();
                ?></p>
            <div class="row">
                <div class="col-sm">
                    <a href="signup.php" >Sign-Up</a>
                </div>
                <div class="col-sm">
                    <a href="login.php" >Log-In</a>
                </div>
                <div class="col-sm">
                    <a href="about.php" >About</a>
                </div>
            </div>   
        </div>
    </div>

</body>
<?php include 'functions/footer.php'; ?>
</html>