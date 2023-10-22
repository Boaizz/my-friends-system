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
    include('functions/navbar.php');
?>
<body>
<div class="container">
		<div class="box-container">
			<h2 style="margin-bottom: 5px;">User Profile</h2>
			<?php
			// Check if the user is logged in and display their profile information if available
			if (isset($_SESSION['profile_name'])) {
				echo "<ul class='info'>";
				echo "<li><b>Name: </b>" . $_SESSION['profile_name'] . "</li>";
				echo "<li><b>Email: </b>" .$_SESSION['email']. "</li>";
				echo "</ul>";
			} else {
				echo "<p>Please log in to view your profile.</p>";
			}
			?>      
			<?php
			// Include settings and check if the user is not logged in, redirect to login page
            require_once('functions/settings.php');
			if (!isset($_SESSION['email'])) {
				header("Location: login.php");
				exit();
			}
			// Retrieve the profile name of the logged-in user
			$email = $_SESSION['email'];
			$fetch_username_query = "SELECT profile_name FROM friends WHERE friend_email='$email'";
			$result = $conn -> query ($fetch_username_query);
			$row = $result -> fetch_assoc();
			$profile_name = $row['profile_name'];
			?>

			<h2 style="margin-bottom: 5px;"><?php echo "$profile_name's Friend List Page"; ?></h2>

			<?php
			// Fetch the user's friends and display them in a table
            $fetch_user_friends = "SELECT friends.friend_id, friends.profile_name 
            FROM friends
            INNER JOIN myfriends ON (friends.friend_id = myfriends.friend_id1 OR friends.friend_id = myfriends.friend_id2)
            WHERE (myfriends.friend_id1 = (SELECT friend_id FROM friends WHERE friend_email = '$email')
               OR myfriends.friend_id2 = (SELECT friend_id FROM friends WHERE friend_email = '$email'))
               AND friends.friend_email != '$email'";
			$result = $conn -> query ($fetch_user_friends);
			$total_friends = $result -> num_rows;
			// Display the list of friends of user on the page as a table
			echo "<p>Total number of friends is " . $total_friends . "</p>";
			echo "<table>";
			echo "<thead><tr><th>Friend Name</th><th>Action</th></tr></thead>";
			echo "<tbody>";
			while ($row = $result -> fetch_assoc()) {
				$friend_id = $row['friend_id'];
				$friend_name = $row['profile_name'];
				// Query to fetch the total number of friends for the current user
				$fetch_friends_query = $conn->query("SELECT * FROM myfriends WHERE friend_id1=$friend_id");
				$total_friends = $fetch_friends_query->num_rows;
				 // Display the friend's name and an option to remove the friend
				echo "<tr>
					<td>$friend_name</td>
					<td>
                        <form method='post' action='functions/remove_friend.php'>
                            <input type='hidden' name='friend_id' value='$friend_id'>
                            <button type='submit' name='remove_friend'>Remove Friend</button>
                        </form>
                    </td>
				</tr>";
			}
			echo "</tbody>";
			echo "</table>";
			?>
		</div>
	</div>
	<div class="button-container">
		<p><a class="button" href='friendadd.php'>Add Friends</a> 
		<a class="button" href='logout.php'>Log out</a> 
		<a class="button" href='index.php'>Back to Home Page</a></p>
	</div>
</body>
<?php include 'functions/footer.php'; ?>
</html>