// $_GET - Parameters are placed on the URL
// $_POST - The URL is retrieved and parameters are appended to the request in the HTTP connection
    // You can send a lot more data this way

// If you're adding or deleting something from a website that should be a POST request


// PHP Contraction 
// <?php echo ($oldguess); ?>
// <?= $oldguess ?>

// Never display user entered data without ESCAPING it 
// Use HTML Entities

// Separates out the parts of the request resonse cycle 

<?php
    $oldguess = '';
    $message = false;
    if (isset($_POST['guess'])) {
       $oldguess = $_POST['guess'] + 0;
        // Neat way to convert to a number
       if ($oldguess == 42) {
        $message = "Great Job!";
       } elseif ($oldguess < 42) {
        $message = "Too low";
       } else {
        $message = "Too high";
       }
    }
?>
<html>
<head>
    <title>A Guessing Game</title>
</head>
<body>
    <p>Guessing Game...</p>
    <?php if ($message !== false) {
        echo ("<p>$message</p>\n");
    }?>
    <form method="post">
        <p>
            <label for="guess">
                Input Guess
            </label>
            <input type="text" name="guess" id="guess" size="40" value="<?= htmlentities($oldguess)?>">
                // value = "<?=htmlentities([variable])?>" tells forms to leave input data there.
            <input type="submit"/>
        </p>
    </form>
</body>
</html>


// The real way to do the code above is with sessions.
// You never ever want to load data for the page with POST data. 


<?php
    // Start a session
    session_start();
    // These are the GET variables
    $oldguess = '';
    $message = false;
    // IF you have POST data, process it
    if (isset($_POST['guess'])) {
       $oldguess = $_POST['guess'] + 0;
       // Stores $oldguess as a GET request for later
       $_SESSION['guess'] = $oldguess;
       if ($oldguess == 42) {
       // Stores message as a GET request for later
        $_SESSION['message'] = "Great Job!";
       } elseif ($oldguess < 42) {
        $_SESSION['message'] = "Too low";
       } else {
        $_SESSION['message'] = "Too high";
       }
       // Redirect back to ourselves - refresh the page with the new GET data
       // When the page loads again, it'll have GET data and skip the POST processing part. 
       header("Location: guess.php");
       return;
    }
?>
// Your HTML has to be able to determine whether its on the first or second GET request. 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A Guessing Game</title>
</head>
<body style="font-family: sans-serif;">
    <?php
    // Says "If there is something in the $_SESSION array for the key 'guess'
    // [the only way that could have happened is if they submitted some POST
    // data through the form by playing the game], then set the $oldguess
    // variable to what is stored there, otherwise, this is the first time
    // someone is going through the game and there is no $oldguess so set it to
    // nothing [blank].
    $oldguess = isset($_SESSION['guess']) ? $_SESSION['guess'] : '';
    // Same as above, checks to see if theres a message stored, stores is again
    // if there is and sets it to false if there isn't. 
    $message = isset($_SESSION['message']) ? $_SESSION['message'] : false;
    ?>
    <p>Guessing Game...</p>
    <?php if ($message !== false) {
        echo ("<p>$message</p>\n");
    }?>
    <form method="post">
        <p>
            <label for="guess">
                Input Guess
            </label>
            // value = "<?=htmlentities([variable])?>" tells forms to leave input data there.
            <input type="text" name="guess" id="guess" 
                    size="40" value="<?= htmlentities($oldguess)?>">
            <input type="submit"/>
        </p>
    </form>
</body>
</html>

//      *********************************************************************
//      |                        LOGIN PAGE TEMPLATE                        |
//      *********************************************************************

//      *********************************************
//      |                  LOGIN.PHP                |
//      *********************************************
<?php
    session_start();
    // If someone tried to log in by entering a username and password...
    if ( isset($_POST['account']) && isset($_POST['password'])) {
        // Log out anyone who was logged in before
        unset($_SESSION['account']);
        // If the entered password equals umsi
        if ( $_POST['password'] == 'umsi') {
            // Set the session account to the entered account and
            $_SESSION['account'] = $_POST['account'];
            // set a flash message 
            $_SESSION['success'] = "Logged in.";
            // then redirect to the app page which will use the $_SESSION['account'] 
            header('Location: app.php');
            return;
        // Otherwise
        } else {
            // Set a flash message 
            $_SESSION['error'] = "Incorrect Password";
            // and reload the login page
            header('Location: login.php');
            return;
        } 
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In Page</title>
</head>
<body style="font-family: sans-serif;">
    <h1>Please Log In</h1> 
    <?php
        if (isset($_SESSION['error'])){
            echo ('<p style="color:red".'.$_SESSION['error']."</p>\n");
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo ('<p style="color:green".'.$_SESSION['success']."</p>\n");
            unset($_SESSION['success']);
        }
    ?>
    <form method="post">
        <p>Account: <input type="text" name="account" value=""></p>
        <p>Password: <input type="text" name="password" value=""></p>
        // password is 'usmi'
        <p><input type="submit" value="Log In"></p>
        <a href="app.php">Cancel</a>
    </form>   
</body>
</html>

//      *********************************************
//      |                  APP.PHP                  |
//      *********************************************

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cool App Page</title>
</head>
<body style="font-family=sans-serif;">
    <h1>Cool Application</h1>
    <?php
        if (isset($_SESSION['success'])){
            echo('<p style="color:green">'.$_SESSION["success"]."</p>\n");
            unset($_SESSION["success"]);
        }
        // Check if we're logged in. If not, present please log in message.
        if ( ! isset($_SESSION['account'])){ ?>
            <p>Please <a href="login.php">Log In</a> to start.</p>
            // if you are logged in, present your app.
            <?php } else { ?>
            <p>This is where a cool application would be.</p>
            <p>Please <a href="logout.php">Log Out</a> when you are done. </p>
            <?php } ?>
</body>
</html>

//      *********************************************
//      |                  LOGOUT.PHP               |
//      *********************************************

<?php
session_start();
session_destroy();
header('Location: app.php');