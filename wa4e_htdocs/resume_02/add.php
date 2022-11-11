<?php
require_once "pdo.php";
require_once "util.php";

session_start();

// You will need to have a section where the user can press a "+" button to add
// up to nine empty position entries. Each position entry includes a year
// (integer) and a description.

// Demand a session
if ( ! isset($_SESSION['name'])) {
    die('ACCESS DENIED');
    return;
}
// If the User wants to cancel, go back to index.php
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

// Record Addition 
if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['headline'], $_POST['summary'] )) {
    // Error Checking and Data Validation

    // This is form validation in util.php. If there is an issue, the function
    // will return an error message in the form of a string which is then set
    // into $_SESSION['error']. If there are no issues, $msg returns 'true'
    // [saying the form validation was successful] and no action is taken.
    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    $msg = validatePosition();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    // Data has been validated

    // Insert Profile form data using util.php function
    insertProfile($pdo);


    // Insert Position form data
    // This says "hey, you just did an INSERT, tell me what key you gave to that
    // last insertion."
    $profile_id = $pdo->lastInsertId();

    // Insert Positions form data using util.php function
    insertPositions($pdo, $_REQUEST['profile_id']);

    $_SESSION['success'] = 'Profile added';
    header('Location: index.php');
    return;
} ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Casey Schmid Profile Add</title>
        <?php require_once "head.php"; ?>
        <style>
            label{
                display: block;
                float: left;
                width : 80px;    
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Add New Profile</h1>
            <?php flashMessages(); ?>
            <form method="post">
                <p>
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" style="width: 300px;"><br/> 
                </p>
                <p>
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" style="width: 300px;"><br/>
                </p>
                <p>
                    <label for="email">Email:</label>
                    <input type="text" name="email" style="width: 300px;"><br/>
                </p>
                <p>
                    <label for="headline">Headline:</label>
                    <input type="text" name="headline" style="width: 420px;"><br/>
                </p>
                <p>
                    <label for="summary">Summary:</label>
                    <textarea name="summary" rows="6" cols="50" style="display: block;"></textarea>
                </p>
                <p>
                    <label for="position">Position:</label> 
                    <input type="submit" id="addPos" name="addPos" value="+">
                </p>
                <!-- This div is for the jQuery to insert the form fields as the button is pressed.  -->
                <div id="position_fields"></div> 
                <p></p>
                <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']?>">
                <input type="submit" name="add" value="Add">
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <script>
                // Remember that JavaScript variables are global unless you specify otherwise.
                // We only want to allow 9 Position fields. This will help us keep track of them. 
                countPos = 0;
                // Waits until the page has completely loaded, then runs this code.
                $(document).ready(function(){
                    // Consoles out that the document is ready.
                    window.console && console.log('Document ready called');
                    // Says "when the button that has this #id is clicked, run this code"
                    $('#addPos').click(function(event){
                        // http://api.jquery.com/event.preventdefault/
                        // In this case, prevents the "submit" action of the button
                        event.preventDefault();
                        // Checks to ensure countPos is < 9. If not = alert()
                        if ( countPos >= 9 ) {
                            alert("Maximum of nine position entries exceeded");
                            return;
                        }
                        // Increments countPos
                        countPos++;
                        // Consoles out status
                        window.console && console.log("Adding position "+countPos);
                        // selects all id's and adds the following HTML in. 
                        $('#position_fields').append(
                            '<div id="position'+countPos+'"> \
                            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
                            <input type="button" value="-" \
                                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                            </div>');
                    });
                });
            </script>
        </div>
    </body>
</html>