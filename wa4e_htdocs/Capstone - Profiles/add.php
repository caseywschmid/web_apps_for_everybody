<?php
require_once "pdo.php";
require_once "util.php";

session_start();

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
if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], 
            $_POST['headline'], $_POST['summary'] )) {
    // Error Checking and Data Validation

    // Form validation in util.php. If there is an issue, the function
    // will return an error message in the form of a string which is then set
    // into $_SESSION['error']. If there are no issues, $msg returns 'true'
    // [saying the form validation was successful] and no action is taken.
    $msg = validateProfile();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    $msg = validatePositions();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    $msg = validateEducation();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    // Data has been validated

    // Insert Profile form data using util.php function
    insertProfile($pdo);
    
    // Gets the id from the SQL INSERT above and returns it
    $profile_id = $pdo->lastInsertId();
    // Insert Positions form data using util.php function
    // Since these are new, they need to go to the newest profile_id 
    insertPositions($pdo, $profile_id);
    
    // Insert Education form data using util.php function
    insertEducation($pdo, $profile_id);
    
    // Inserts Complete -> success message
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
    <title>Profile Add</title>
    <?php require_once "head.php"; // Bootsrap CSS and jQuery for JS ?>
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
                <textarea name="summary" rows="6" cols="50" 
                        style="display: block;"></textarea>
            </p>
            <p>
                <label for="education">Education:</label> 
                <input type="submit" id="addEd" name="addPos" value="+">
            </p>
            <div id="education_fields"></div> 
            <p></p>
            <p>
                <label for="position">Position:</label> 
                <input type="submit" id="addPos" name="addPos" value="+">
            </p>
            <div id="position_fields"></div> 
            <p></p>
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']?>">
            <input type="submit" name="add" value="Add">
            <input type="submit" name="cancel" value="Cancel">
        </form>
        <script>
            countPos = 0;
            countEd = 0;
            // Once the document fully loads, run this code
            $(document).ready(function(){
                window.console && console.log('Document ready called');

                // Adds Education fields to the form with "+" button.
                $('#addEd').click(function(event){
                    event.preventDefault();
                    // Only allow 9 entries
                    if ( countEd >= 9 ) {
                        alert("Maximum of nine education entries exceeded");
                        return;
                    }
                    countEd++;
                    window.console && console.log("Adding education "+countEd);
                    // Addition of HTML for new Education form fields
                    $('#education_fields').append(
                        '<div id="education'+countEd+'"> \
                        <label for="year">Year:</label> \
                        <input type="text" name="edyear'+countEd+'" value="" /> \
                        <input type="button" value="-" \
                            onclick="$(\'#education'+countEd+'\').remove();return false;"></p> \
                        <label for="school">School:</label> \
                        <input type="text" class="school" name="school'+countEd+'" size="80"></input>\
                        </div><p></p>');
                    // Autocomplete for the School Field. 
                    $('.school').autocomplete({source: "school.php"
                    });
                });
                
                // Adds Position fields to the form with "+" button.
                $('#addPos').click(function(event){
                    event.preventDefault();
                    if ( countPos >= 9 ) {
                        alert("Maximum of nine position entries exceeded");
                        return;
                    }
                    countPos++;
                    window.console && console.log("Adding position "+countPos);
                    // Addition of HTML for new Position form fields
                    $('#position_fields').append(
                        '<div id="position'+countPos+'"> \
                        <label for="year">Year:</label> \
                        <input type="text" name="posyear'+countPos+'" value="" /> \
                        <input type="button" value="-" \
                        onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                        <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
                        </div><p></p>'
                    );
                });    
            });
        </script>
    </div>
</body>
</html>