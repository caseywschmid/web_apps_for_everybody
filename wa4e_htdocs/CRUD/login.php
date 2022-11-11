<?php
session_start();
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
if ( ! isset($_POST['email']) && isset($_POST['pass'])) {
    echo '<a href="login.php">Please log in</a>';
    header("Location: login.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Password is php123

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    unset ($_SESSION['email']); // Logout anyone logged in
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['error'] = "User name and Password are required";
        header("Location: login.php");
        return;
    } elseif ((str_contains($_POST['email'],'@')) == False){
        $_SESSION['error'] = "Email must have an at-sign (@)";
        header("Location: login.php");
        return;     
    } else {
        $check = hash('md5', $salt.$_POST['pass']);
        if ( $check == $stored_hash ) {
            $_SESSION['name'] = $_POST['email'];
            $_SESSION['success'] = "Logged in successfully";
            error_log("Login success ".$_POST['email']);
            header("Location: index.php");
            return;
        } else {
            $_SESSION['error'] = "Incorrect Password";
            error_log("Login fail ".$_POST['email']." $check");
            header("Location: login.php");
            return; 
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Casey Schmid Autos Login</title>
    </head>
    <body>
        <div class="container">
            <h1>Please Log In</h1>
            <?php
            if ( isset($_SESSION['error']) ) {
                echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
                unset($_SESSION['error']);
            }
            ?>
            <form method="POST">
                <p>
                    <label for="nam">User Name</label>
                    <input type="text" name="email" id="nam"><br/>
                </p>
                <p>
                    <label for="id_1723">Password</label>
                    <input type="text" name="pass" id="id_1723"><br/>
                </p>
                <input type="submit" value="Log In">
                <input type="submit" name="cancel" value="Cancel">
            </form>
        </div>
    </body>
</html>