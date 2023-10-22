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
<body>
    <?php include 'functions/navbar.php'; ?>
    <div class="container mt-5">
    <ol>
                <li class="question">
                    What is the PHP version installed in mercury?
                </li>
                <ul>
                    <li>My PHP version is: <strong><?php echo phpversion() ?></strong>.
                    </li>
                </ul>
                <hr>
                <li class="question">
                    What tasks you have not attempted or not completed?
                </li>
                <ul>
                   <li>None</li>
                </ul>
                <hr>
                <li class="question">
                    What special features have you done, or attempted, in creating the site that we should know about?
                </li>
                <ul>
                    <li>
                        I have also added the Navbar and Footer for the website. The website is also designed to make it responsive for tablets and mobile devices.
                    </li>
                    <li>
                        When user log in, the username is display the top right corner to identify current user. 
                    </li>
                    <li>
                        I have done the <a href="friendadd.php">Add Friend</a> and count the number of mutual friends between current user and other non-friend users. There is also a button to display the name of all mutual friends.
                    </li>
                </ul>
                <hr>
                <li class="question">
                    Which parts did you have trouble with?
                </li>
                <ul>
                   <li>None</li>
                </ul>
                <hr>
                <li class="question">
                    What would you like to do better next time?
                </li>
                <ul>
                   <li>I would like to generate password hasshes using one-way hashing algorithms for enhancing security.</li>
                </ul>
                <hr>
                <li class="question">
                    A screen shot of a discussion response that answered someone’s thread in the unit’s
                    discussion board for Assignment 2?
                </li>
                <ul>
                    <li>
                        <img class="image" src="./images/screen2.png" id="discussion" alt="discussion">
                    </li>
                    <li>
                        <img class="image" src="./images/screen1.png" id="discussion" alt="discussion">
                    </li>
                    <li>
                    I have joined the discussion on Canvas to solve a problem related to myfriends and friends table.
                    </li>
                </ul>
            </ol>
    </div>
    <div class="button-container">
		<a class="button" href='friendlist.php'>Friend List</a> 
		<a class="button" href='friendadd.php'>Add Friend</a> 
		<a class="button" href='index.php'>Back to Home Page</a>
	</div>
</body>
<?php include 'functions/footer.php'; ?>
</html>