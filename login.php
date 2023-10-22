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

<?php
    // Include settings and start a PHP session
    require_once('functions/settings.php');
    include('functions/utils.php');

      // Check if the user is already logged in, if yes, redirect to friendlist.php
    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
        header('Location: friendlist.php');
        exit;
    }
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve and sanitize user input
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        // Check if the user entered any data
        if (empty($email) || empty($password)) {
            $error = "Please enter your email address and password.";
        } else {
            // Prepare a SQL query with a prepared statement to retrieve user data
            $query = "SELECT * FROM friends WHERE friend_email=?";
            $stmt = $conn-> prepare($query);
            $stmt -> bind_param("s", $email);
            $stmt -> execute();
            $result = $stmt->get_result();
            if (!$result) {
                die("Query failed: " . $conn-> error);
            }
            $row = $result -> fetch_assoc();;
            if ($row) {
                if ($password == $row['password']) {
                    // If the password matches, set session variables and redirect to friendlist.php
                    $_SESSION['is_logged_in'] = true;
                    $_SESSION['email'] = $email;
                    $_SESSION['profile_name'] = $row['profile_name'];

                    // Redirect to friendlist.php
                    header('Location: friendlist.php');
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
            //Close the connection
            $conn->close();
        }
    }
    
?>
<body>
<div class="container">
    <div class="box-container">
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
            <!-- Display error message if there's an error -->
        <?php endif; ?>
        <form method="POST" action="">
            <label for="email">Email address:</label>
                <input type="text" name="email" id="email" placeholder="Email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            <br>
            <label for="password">Password:</label>
                <input type="password" name="password" id="password"  placeholder="Password"  value="<?php echo isset($error) ? '' : ''; ?>">
            <br>
            <input type="submit" value="Log In">
        </form>
    </div>
</div>

<div class="button-container">
    <p><a class="button" href="index.php">Back to Home</a></p>
</div>
</body>
<?php include 'functions/footer.php'; ?>
</html>