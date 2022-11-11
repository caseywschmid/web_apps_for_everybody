<?php
require_once "pdo.php";
require_once "util.php";

session_start();


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
if (isset($_POST['first_name'], $_POST['last_name'], $_POST['email'], 
        $_POST['headline'], $_POST['summary'], $_POST['profile_id'] )) {
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

    $msg = validatePositions();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    $msg = validateEducation();
    if (is_string($msg)) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    // Data has been validated

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
    
    // Delete old Education data
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
    
    
    // Insert Positions form data using util.php function
    // We can access the profile_id using the superglobal
    insertPositions($pdo, $_REQUEST['profile_id']);
    
    // Insert Education form data using util.php function
    insertEducation($pdo, $_REQUEST['profile_id']);

    // Inserts Complete -> success message
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
// Load up the education rows
$education = loadEducation($pdo, $_REQUEST['profile_id']);
$education_to_json = json_encode((array)$education);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Casey Schmid Profile Edit</title>
        <?php require_once "head.php"; ?>
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
                <input type="submit" name="add" value="Save">
                <input type="submit" name="cancel" value="Cancel">
            </form>
            <script>
                countPos = 0;
                countEd = 0;
                // Waits until the page has completely loaded, then runs this code.
                $(document).ready(function(){
                    // Consoles out that the document is ready.
                    window.console && console.log('Document ready called');

                    // Echo out PHP $education array to JS variable
                    // Don't forget the extra semi-colon!!!
                    var education = <?php echo $education_to_json ?>;;
                    window.console && console.log(education.length);
                    window.console && console.log(education);

                    // Loads stored Education
                    for (i = 0; i < education.length; i++) {
                        countEd++;
                        window.console && console.log("Adding old education "+countEd);
                        $('#education_fields').append(
                            '<div id="education'+countEd+'"> \
                            <label for="year">Year:</label> \
                            <input type="text" name="edyear'+countEd+'" value="'+education[i].year+'" /> \
                            <input type="button" value="-" \
                                onclick="$(\'#education'+countEd+'\').remove();return false;"></p> \
                            <label for="school">School:</label> \
                            <input type="text" class="school" name="school'+countEd+'" size="80" value="'+education[i].name+'" /> \
                            </div><p></p>');
                        $('.school').autocomplete({source: "school.php"
                        });
                    }

                    // Echo out PHP $profiles array to JS variable
                    // Don't forget the extra semi-colon!!!
                    var profiles = <?php echo $positions_to_json ?>;;
                    window.console && console.log(profiles.length);
                    window.console && console.log(profiles);
                    
                    // Loads stored Positions
                    for (i = 0; i < profiles.length; i++) {
                        countPos++;
                        window.console && console.log("Adding old position "+countPos);
                        $('#position_fields').append(
                            '<div id="position'+countPos+'"> \
                            <p>Year: <input type="text" name="posyear'+countPos+'" value="'+profiles[i].year+'" /> \
                            <input type="button" value="-" \
                                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                            <textarea name="desc'+countPos+'" rows="8" cols="80">'+profiles[i].description+'</textarea>\
                            </div><p></p>');
                    }

                    // Adds Education fields (up tp 9)
                    $('#addEd').click(function(event){
                        event.preventDefault();
                        if ( countEd >= 9 ) {
                            alert("Maximum of nine education entries exceeded");
                            return;
                        }
                        countEd++;
                        window.console && console.log("Adding education "+countEd);
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

                    // Adds Position fields (up to 9)
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
                            <p>Year: <input type="text" name="posyear'+countPos+'" value="" /> \
                            <input type="button" value="-" \
                                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
                            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea> \
                            </div><p></p>');
                    });
                });
            </script>
        </div>
    </body>
</html>