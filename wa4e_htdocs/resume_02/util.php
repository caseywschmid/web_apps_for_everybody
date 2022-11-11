<?php
require_once "pdo.php";

// util.php

// This function prints out the flash messages for the $_SESSION variables that
// get set throughout the site.
function flashMessages(){
    if ( isset($_SESSION['error']) ) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }
    if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
    }
}


// This validates the form fields to ensure they are all filled out and that the email field has an @ symbol. 
// If there is an error, this function returns a string that is then set into $_SESSION['error'].
// If there are no issues, returns 'true'.
function validateProfile(){
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || 
        strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || 
        strlen($_POST['summary']) < 1) {
        return "All fields are required";
    }
    if ((str_contains($_POST['email'],'@')) == False){
        return "Email must have an at-sign (@)";
    }
    return true;
}


// This loops through each of the 9 possible position fields and ensures that
// they are filled out and that the year entry is a numeric value. It uses the
// same trick from above that returns a string that gets put into
// $_SESSION['error'] and returns 'true' if there are no issues. 
function validatePosition(){
    for($i=1; $i<=9; $i++) {
        // if there aren't 9 entries keep going...
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i]; 
        if ( strlen($year) < 1 || strlen($desc) < 1) {
            return "All fields are required";
        }

        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}




// NOTE: What fetchAll() does:
//     $profiles = array();
//     while ( $row = $stmt->fetch(PDO::FETCH_ASSOC)){
//         $positions[] = $row;
//     }
function loadPositions($pdo, $profile_id){
    $sql = "SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':prof' => $profile_id));
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $positions;
}

// Code to take the user input form data and insert it into the database. 
function insertProfile($pdo){
    $sql = "INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) 
    VALUES ( :uid, :fn, :ln, :em, :he, :su)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
    ':uid' => $_SESSION['user_id'],
    ':fn'=> $_POST['first_name'],
    ':ln'=> $_POST['last_name'],
    ':em'=> $_POST['email'],
    ':he'=> $_POST['headline'],
    ':su'=> $_POST['summary']
    ));
}


// Loops through each position entry, pulls out the data and inserts it into
// the database.
// $rank is just a way to order them in the same order they were entered. 
function insertPositions($pdo, $profile_id){
    $rank = 1;
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        $sql = "INSERT INTO Position (profile_id, rank, year, description) 
                VALUES ( :pid, :rank, :year, :desc)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
        );
        $rank++;
    }
}

?>




