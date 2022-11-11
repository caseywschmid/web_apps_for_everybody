<?php
require_once "pdo.php";
require_once "util.php";

session_start();

// Will support the addition of new position entries, the deletion of any or all
// of the existing entries, and the modification of any of the existing entries.
// After the "Save" is done, the data in the database should match whatever
// positions were on the screen and in the same order as the positions on the
// screen.

// Demand a session
if ( ! isset($_SESSION['user_id'])) {
    die('ACCESS DENIED');
    return;
}
// If the User presses the 'cancel' button
if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

// Ensure $_REQUEST parameter is present
if ( ! isset($_REQUEST['profile_id'])) {
    $_SESSION['error'] = 'Missing profile_id';
    header('Location: index.php');
    return;
}

// Load Profile to be edited
$sql = "SELECT * FROM Profile WHERE profile_id = :pid AND user_id = :uid";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':pid' => $_REQUEST['profile_id'], ':uid' => $_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
if ($profile === false) {
    $_SESSION['error'] = 'Could not load profile';
    header('Location: index.php');
    return;
}

// Profile Edit
if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['headline'], $_POST['summary'], $_POST['profile_id'] )) {
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

    // Actual Edit
    $sql = "UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su
            WHERE profile_id = :pid AND user_id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array( 
        ':pid' => $_REQUEST['profile_id'],
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
    );
    
    // Delete old Positions data
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
    
    // Insert Position form data
    // This says "hey, you just did an INSERT, tell me what key you gave to that
    // last insertion."
    $profile_id = $pdo->lastInsertId();

    // Insert Positions form data using util.php function
    insertPositions($pdo, $_REQUEST['profile_id']);

    $_SESSION['success'] = 'Profile updated';
    header('Location: index.php');
    return;
}

$fn = htmlentities($profile['first_name']);
$ln = htmlentities($profile['last_name']); 
$em = htmlentities($profile['email']); 
$he = htmlentities($profile['headline']);
$su = htmlentities($profile['summary']);
$id = $profile['profile_id']; 

// Load up the position rows
$positions = loadPositions($pdo, $_REQUEST['profile_id']);
$positions_to_json = json_encode((array)$positions);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Casey Schmid Profile Edit</title>
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
            <h1>Edit Profile</h1>
            <?php flashMessages(); ?>
            <form method="post" action="edit.php">
                <input type="hidden" name="profile_id" value="<?= $id ?>"><br/>
                <p>
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" value="<?= $fn ?>" style="width: 300px;"><br/> 
                </p>
                <p>
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" value="<?= $ln ?>" style="width: 300px;"><br/>
                </p>
                <p>
                    <label for="email">Email:</label>
                    <input type="text" name="email" value="<?= $em ?>" style="width: 300px;"><br/>
                </p>
                <p>
                    <label for="headline">Headline:</label>
                    <input type="text" name="headline" value="<?= $he ?>" style="width: 420px;"><br/>
                </p>
                <p>
                    <label for="summary">Summary:</label> 
                    <textarea name="summary" rows="6" cols="50"><?= $su ?></textarea><br/>
                </p>
                <p>
                    <label for="position">Position:</label> 
                    <input type="submit" id="addPos" name="addPos" value="+">
                </p>
                <div id="position_fields">
                </div> 
                <p></p>
                <input type="submit" name="add" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <script>
                countPos = 0;
                // Waits until the page has completely loaded, then runs this code.
                $(document).ready(function(){
                    // Consoles out that the document is ready.
                    window.console && console.log('Document ready called');
                    // Echo out PHP $profiles array to JS variable
                    var profiles = <?php echo $positions_to_json ?>;;
                    window.console && console.log(profiles.length);
                    window.console && console.log(profiles);
                    for (i = 0; i < profiles.length; i++) {
                        countPos++;
                        window.console && console.log("Adding old position "+countPos);
                        $('#position_fields').append(
                            '<div id="position'+countPos+'"> \
                            <p>Year: <input type="text" name="year'+countPos+'" value="'+profiles[i].year+'" /> \
                            <input type="button" value="-" \
                                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                            <textarea name="desc'+countPos+'" rows="8" cols="80">'+profiles[i].description+'</textarea>\
                            <p></p></div>');
                    }
                    // Says "when the button that has this id is clicked, run this code"
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
                            <p></p></div>');
                    });
                });
            </script>
        </div>
    </body>
</html>