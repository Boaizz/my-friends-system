<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="index.php">My Friend System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto"> 
            <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="about.php">About</a>
            </li>
            <?php
            // Start the session
            session_start();
            if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true){
                if (isset($_SESSION["email"]) && isset($_SESSION["profile_name"])) {
                    // Display the user's profile name and logout button
                    echo "<li class='nav-item'>
                    <span class='profile-name'>User: <b>
                    <a class='nav-link' href = 'friendlist.php'>".  $_SESSION['profile_name'] . "</a></b></span></li>
                    <li><a class='nav-link' href='logout.php'><b>Logout</b></a></li>";
                } else {
                    // Display the "Login" and "Sign Up" buttons
                    echo "<li class='nav-item'><a class='nav-link' href='login.php'>Login</a></li>";
                    echo "<li class='nav-item'><a class='nav-link' href='signup.php'>Sign Up</a></li>";
                }
            } else {
                // Display the "Login" and "Sign Up" buttons
                echo "<li class='nav-item'><a class='nav-link' href='login.php'>Login</a></li>";
                echo "<li class='nav-item'><a class='nav-link' href='signup.php'>Sign Up</a></li>";
            }
            ?>
        </ul>
    </div>
</nav>
