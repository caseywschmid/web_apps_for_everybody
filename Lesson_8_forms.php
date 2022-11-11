// $_GET - Parameters are placed on the URL
// $_POST - The URL is retrieved and parameters are appended to the request in the HTTP connection
    // You can send a lot more data this way

// If you're adding or deleting something from a website that should be a POST request

// Form Input Types
// Text - regular text area for names and whatnot
// Submit - button to submit
    // type=submit name=[name] value=[text you want to apprear on the button]
// Password - hides the text - not encrypted to the server unless its https://
// Radio - Chooses one and only one of a set of options
    // type=radio: name=[same name]: value=[choices]
// Checkbox - a series of things that can be all turned on or off
    // type=checkbox name=[different name] value=[choice - default is 'on']
// Dropdown - provides a dropdown of choices
// Text Area - a place for long blocks of text
// Multiple Select - Tacky, most people don't use this. User experience is bad

// HTML 5 special inputs
// type=color - color picker that returns hex colors
// type=date - provides calendar dropdown returns text string
// type=email - default validation that requires email 
// type=number - provide range and gives you a dropdown - validation for number in range
// type=url - demands valid url


// PHP Contraction 
// <?php echo ($oldguess); ?>
// <?= $oldguess ?>

// Never display user entered data without ESCAPING it 
// Use HTML Entities
// Google HTML Injection

// MODEL - VIEW - CONTROLLER
// How you structure you PHP code

// Model- silent part - Handles incoming data at the top - produce no output 
// View - produce the page output below that - templating
// Context - information passed from the Model to the View


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
        <title>
            A Guessing Game
        </title>
    </head>
    <body>
        <p>
            Guessing Game... 
        </p>
        <?php if ($message !== false) {
            echo ("<p>$message</p>\n");
        }?>
        <form method="post">
            <p>
                <label for="guess">
                    Input Guess
                </label>
                <input type="text" name="guess" id="guess" size="40" value="<?= htmlentities($oldguess)?>">
                    <!-- value = "<?=htmlentities([variable])?>" tells forms to leave input data there. -->
                <input type="submit"/>
            </p>
        </form>
    </body>
</html>