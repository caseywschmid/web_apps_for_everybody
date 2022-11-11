<?php
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';
$stored_hash = '1a52e17fa899cf40fb04cfc42e6352f1';  // Password is php123
$failure = false;  // If we have no POST data

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['who']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['who']) < 1 || strlen($_POST['pass']) < 1 ) {
        $failure = "Email and Password are required";
    } elseif ((str_contains($_POST['who'],'@')) == false){
        // another way to do this validation is as follows:
        // elseif ( strpos($_POST['who'], '@') === false )
        $failure = "Email must have an at-sign (@)";      
    } else {
        $check = hash('md5', $salt.$_POST['pass']);
        if ( $check == $stored_hash ) {
            header("Location: autos.php?name=".urlencode($_POST['who']));
                // header() calls must come before any HTML content
                // There are two special types of header() calls:
                    // HTTP - used for error codes
                    // Location: - used for redirects
            error_log("Login success ".$_POST['who']);
            return;
        } else {
            $failure = "Incorrect password";
            error_log("Login fail ".$_POST['who']." $check");
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
            if ( $failure !== false ) {
                echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
            }
            ?>
            <form method="POST">
                <p>
                    <label for="nam">Email</label>
                    <input type="text" name="who" id="nam"><br/>
                </p>
                <p>
                    <label for="id_1723">Password</label>
                    <input type="password" name="pass" id="id_1723"><br/>
                </p>
                <input type="submit" value="Log In">
                <input type="submit" name="cancel" value="Cancel">
            </form>
        </div>
    </body>
</html>
