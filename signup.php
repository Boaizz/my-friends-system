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
<?php
    require_once('functions/settings.php');
    include('functions/utils.php');
    // Check if the user is already logged in, if yes, redirect to friendlist.php
    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
        header('Location: friendlist.php');
        exit;
    }
    // Check if the user submits the registration form
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Retrieve form data and validate
        $email = $conn -> real_escape_string(sanitizeInput($_POST["email"]));
        $profile_name = $conn -> real_escape_string(sanitizeInput($_POST["profile_name"]));
        $password = $conn -> real_escape_string(sanitizeInput($_POST["password"]));
        $confirm_password = $conn -> real_escape_string(sanitizeInput($_POST["confirm_password"]));

        $errors = [];
        //Check errors for validation user input
        if (empty($email)) {
            $errors[0] = "Email is required";
        } else if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $email)) {
            $errors[0] = "Invalid email format";
        } else {
            $query = "SELECT * FROM friends WHERE friend_email='$email'";
            $result = $conn -> query ($query);
            if ($result -> num_rows > 0) {
                $errors[0] = "Email is already used!";
            }
        }
        if (empty($profile_name)) {
            $errors[1] = "Profile name is required";
        } else if (!preg_match("/^[a-zA-Z ]*$/", $profile_name)) {
            $errors[1] = "Profile name must contain only letters and spaces!";
        }

        if (empty($password)) {
            $errors[2] = "Password is required";
        } else if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#$@!%&*?])[A-Za-z\d#$@!%&*?]{8,30}$/", $password)) {
            $errors[2] = "Password must contain minimum eight characters, at least one letter, one number and one special character!";
        }

        if (empty($confirm_password)) {
            $errors[3] = "Please confirm your password!";
        } else if ($password !== $confirm_password) {
            $errors[3] = "Password does not match!";
        }
        // If there are no errors, insert data into friends table
        if (count($errors) == 0) {
            $date_started = date("Y-m-d");
            $num_of_friends = 0;
            $query = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends)
                VALUES ('$email', '$password', '$profile_name', '$date_started', '$num_of_friends')";
            if ($conn -> query ($query)) {
                // Set session variables for the logged-in user
                $_SESSION["is_logged_in"] = true;
                $_SESSION["email"] = $email;
                $_SESSION["profile_name"] = $profile_name;
                // Redirect to friendadd.php
                header("Location: friendadd.php");
                exit();
            } else {
                $errors[4] = "Error: " . $conn-> error;
            }
            } else {
                // Set session variables for profile name and email to retain user input on error
                $_SESSION["email"] = $email;
                $_SESSION["profile_name"] = $profile_name;
        }
}

?>
<body>
<div class="container">
<div class="box-container">
    <h2 style="margin-bottom:10px;">Registration Page</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?php echo isset($_SESSION["email"]) ? $_SESSION["email"] : ""; ?>">
        <?php if (isset($errors[0])) { ?>
            <span class="error"><?php echo $errors[0]; ?></span>
        <?php } ?>
        <br>

        <label for="profile_name">Profile Name:</label>
        <input type="text" id="profile_name" name="profile_name" value="<?php echo isset($_SESSION["profile_name"]) ? $_SESSION["profile_name"] : ""; ?>">
        <?php if (isset($errors[1])) { ?>
            <span class="error"><?php echo $errors[1]; ?></span>
        <?php } ?>
        <br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
        <?php if (isset($errors[2])) { ?>
            <span class="error"><?php echo $errors[2]; ?></span>
        <?php } ?>
        <br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password">
        <?php if (isset($errors[3])) { ?>
            <span class="error"><?php echo $errors[3]; ?></span>
        <?php } ?>
        <br>

        <input type="submit" value="Register">
        <input type="button" value="Reset" onclick="location.href='<?php echo $_SERVER["PHP_SELF"] . '?clear=true'; ?>'">
        <?php
        // Check if 'clear' parameter is set in the URL and is equal to "true"
        if (isset($_GET["clear"]) && $_GET["clear"] == "true") {
            // Redirect to the current PHP page (self) to clear the session data
            unset($_SESSION["profile_name"]);
            unset($_SESSION["email"]);
            header("Location: " . $_SERVER["PHP_SELF"]);
            exit();
        }
        ?>
    </form>
</div>
<div class="button-container">
    <p><a class="button" href="index.php">Back to Home</a></p>
</div>
<?php $conn->close(); //Close the connection?>
</body>
<?php
    include('functions/footer.php');
?>
</html>