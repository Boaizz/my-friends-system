<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>My Friend System</title>
    <meta name="description" content="assignment2">
    <meta name="keywords" content="">
    <meta name="author" content="Dang Khanh Toan Nguyen">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<?php
      // Include the navigation bar
    include('functions/navbar.php');
?>
<body>
<div class="container">
		<div class="box-container">
            <h2>Add Friends</h2>
            <?php
             // Include the database settings and check if the user is logged in
            require_once('functions/settings.php');
            if (!isset($_SESSION['email'])) {
                header("Location: login.php");
                exit();
            }
            // Retrieve the profile name and friend ID of the current user
            $email = $_SESSION['email'];
            $current_user_query = "SELECT friend_id, profile_name FROM friends WHERE friend_email='$email'";
            $result = $conn -> query($current_user_query);
            $row = $result -> fetch_assoc();
            $profile_name = $row['profile_name'];
            $friend_id = $row['friend_id'];

           
            // Retrieve the count of registered users who are not already friends of the current user
            $count_non_friend_query = "SELECT COUNT(*) AS row_count
                    FROM friends f 
                    WHERE f.friend_email != '$email' 
                    AND f.friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = $friend_id)";
            $result_count = $conn -> query($count_non_friend_query);
            $total_non_friends = $result_count -> fetch_assoc()['row_count'];

            
            // SETTING UP THE PAGINATION
            // Set the page number
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            // Number of results to display per page
            $resultsPerPage = 5;
            // Calculate the offset for the SQL query based on the current page
            $offset = ($page - 1) * $resultsPerPage;
            // Calculate the total number of pages needed for pagination
            $total_pages = ceil($total_non_friends /  $resultsPerPage);

            // Query to retrieve the list of registered users who are not already friends of the current user for the current page
            $list_non_friends = "SELECT f.profile_name, f.friend_id
                FROM friends f
                LEFT JOIN (
                    SELECT DISTINCT friend_id1 AS friend_id
                    FROM myfriends
                    WHERE friend_id2 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
                    UNION ALL
                    SELECT DISTINCT friend_id2 AS friend_id
                    FROM myfriends
                    WHERE friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
                ) mf ON f.friend_id = mf.friend_id
                WHERE mf.friend_id IS NULL
                AND f.friend_email != '$email'
                LIMIT 5 OFFSET $offset";
            $result_non_friends = $conn -> query($list_non_friends);
          

            // Query to calculate the total number of friends
            $count_total_friends_query =  "SELECT friends.friend_id, friends.profile_name 
                                    FROM friends
                                    INNER JOIN myfriends ON (friends.friend_id = myfriends.friend_id1 OR friends.friend_id = myfriends.friend_id2)
                                    WHERE (myfriends.friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
                                    OR myfriends.friend_id2 = (SELECT friend_id FROM friends WHERE friend_email = '$email'))
                                    AND friends.friend_email != '$email'";
            $total_friends = $conn -> query($count_total_friends_query) -> num_rows;
            echo "<p style='margin-bottom: 3px;'>Total number of friends is $total_friends</p>";

            // Display the list of registered users that is not friend with current user on the page
            if ($result_non_friends !== false && $result_non_friends -> num_rows > 0) {
                // Display the list of registered users that is not friend with current user on the page
                echo "<table>";
                echo "<thead>
                        <tr>
                            <th>Profile Name</th>
                            <th>Mutual Friends</th>
                            <th>Action</th>
                        </tr>
                      </thead>";
                
                while ($row = $result_non_friends -> fetch_assoc()) {
                    $friend_id = $row['friend_id'];
                    $friend_name = $row['profile_name'];
                    // Query to count the number of mutual friends between the current user and another user that is not friend with current user
                    $mutual_friend_count_query ="SELECT COUNT(*) AS mutual_friends_count
                    FROM (
                        SELECT
                            CASE 
                                WHEN friend_id1 = f1.friend_id THEN friend_id2
                                ELSE friend_id1
                            END AS mutual_friend
                        FROM myfriends
                        INNER JOIN friends f1 ON (myfriends.friend_id1 = f1.friend_id OR myfriends.friend_id2 = f1.friend_id)
                        WHERE f1.friend_email = '$email'
                    ) AS user1_friends
                    INNER JOIN (
                        SELECT
                            CASE 
                                WHEN friend_id1 = '$friend_id' THEN friend_id2
                                ELSE friend_id1
                            END AS mutual_friend
                        FROM myfriends
                        WHERE friend_id1 =  '$friend_id' OR friend_id2 =  '$friend_id'
                    ) AS user2_friends ON user1_friends.mutual_friend = user2_friends.mutual_friend";
                    
                    // Execute the SQL query
                    $result_mutual_friends = $conn -> query($mutual_friend_count_query);
                    if (!$result_mutual_friends) {
                        // If query failed to execute then handle error and print error message
                        echo "Error: " .  $conn-> error;
                    } else {
                    // Fetch the result
                    $mutual_friends_row = $result_mutual_friends -> fetch_assoc();
                    $mutual_friends_count = $mutual_friends_row['mutual_friends_count'];
                    // Display the non-friend user's information and mutual friend count
                    echo "<tr>
                            <td>$friend_name</td>
                            <td>$mutual_friends_count <a href='?show_mutual_friends=" . (isset($_GET['show_mutual_friends']) && $_GET['show_mutual_friends'] == $friend_id ? '' : $friend_id) . "'>" . (isset($_GET['show_mutual_friends']) && $_GET['show_mutual_friends'] == $friend_id ? 'Hide' : 'Show') . "</a></td>
                            <td>
                            <form method='post' action='functions/add_friend.php'>
                                <input type='hidden' name='friend_id' value='$friend_id'>
                                <button type='submit' name='add_friend'>Add Friend</button>
                            </form>
                            </td>
                          </tr>";
                    
                    // If showing, fetch and display the names of mutual friends
                    if (isset($_GET['show_mutual_friends']) && $_GET['show_mutual_friends'] == $friend_id) {
                        // Fetch and display the names of mutual friends
                        $mutual_friends_query = "SELECT f.profile_name, f.friend_id
                        FROM friends f
                        WHERE f.friend_id IN (
                            SELECT DISTINCT mutual_friend
                            FROM (
                                SELECT friend_id2 AS mutual_friend
                                FROM myfriends
                                WHERE friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
                                UNION ALL
                                SELECT friend_id1 AS mutual_friend
                                FROM myfriends
                                WHERE friend_id2 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
                            ) AS user1_friends
                            WHERE mutual_friend IN (
                                SELECT DISTINCT mutual_friend
                                FROM (
                                    SELECT friend_id2 AS mutual_friend
                                    FROM myfriends
                                    WHERE friend_id1 = '$friend_id'
                                    UNION ALL
                                    SELECT friend_id1 AS mutual_friend
                                    FROM myfriends
                                    WHERE friend_id2 = '$friend_id'
                                ) AS user2_friends
                            )
                        );";
                        $result_mutual_friends_names = $conn -> query($mutual_friends_query);

                        if ($result_mutual_friends_names) {
                            $mutual_friends_names = [];
                           
                            while ($mutual_friend = $result_mutual_friends_names -> fetch_assoc()) {
                                $mutual_friends_names[] = $mutual_friend['profile_name'];
                            }
                            echo "<tr><td>Friend with:</td><td colspan='3'>";
                            echo implode(', ', $mutual_friends_names);
                            echo "</td></tr>";
                            }
                        }             
                    }
                }
                echo "</table>";
            } else {
                    // Display a message indicating that there are no registered users who are not already your friends
                echo "<p>There are no registered users who are not already your friends.</p>";
            }
            // Display pagination controls
            echo "<div class='pagination-container'>";
            echo "<ul class='pagination'>";
            // If the current page exceeds the total number of pages, redirect to the last page
             if ($page > $total_pages) {
                header("Location: friendadd.php?page=$total_pages");
                exit();
            }
            // Display 'Previous' link if not on the first page
            if ($page > 1) {
                echo "<li><a href='friendadd.php?page=".($page-1)."'>Previous</a></li>";
            }
            // If there are no non-friends and not on the first page, redirect to the previous page
            if ($result_non_friends !== false && $result_non_friends -> num_rows == 0 && $page > 1) {
                header("Location: friendadd.php?page=" . ($page - 1));
                exit;
            }
            // Display page number links
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    echo "<li class='active'><a href='friendadd.php?page=$i'>$i</a></li>";
                } else {
                    echo "<li><a href='friendadd.php?page=$i'>$i</a></li>";
                }
            }
            // Display 'Next' link if not on the last page
            if ($page < $total_pages) {
                echo "<li><a href='friendadd.php?page=".($page+1)."'>Next</a></li>";
            }

            echo "</ul>";
            echo "</div>";

            // Close the database connection
            $conn->close();
            ?>
        </div>
    </div>
    <div class="button-container">
		<a class="button" href='friendlist.php'>Friend List</a> 
		<a class="button" href='logout.php'>Log out</a> 
		<a class="button" href='index.php'>Back to Home Page</a>
	</div>
</body>
<?php include 'functions/footer.php'; ?>
</html>
