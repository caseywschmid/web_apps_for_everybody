<?php
require_once "pdo.php";

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
    if ( isset($_SESSION['delete']) ) {
        echo('<p>'.htmlentities($_SESSION['delete'])."</p>\n");
        unset($_SESSION['delete']); 
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
function validatePositions(){
    for($i=1; $i<=9; $i++) {
        // if there aren't 9 entries keep going...
        if ( ! isset($_POST['posyear'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $posyear = $_POST['posyear'.$i];
        $desc = $_POST['desc'.$i]; 
        if ( strlen($posyear) < 1 || strlen($desc) < 1) {
            return "All Position fields are required";
        }
        if ( ! is_numeric($posyear) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}

// This loops through each of the 9 possible education fields and ensures that
// they are filled out and that the year entry is a numeric value. It uses the
// same trick from above that returns a string that gets put into
// $_SESSION['error'] and returns 'true' if there are no issues.
function validateEducation(){
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edyear'.$i]) ) continue;
        if ( ! isset($_POST['school'.$i]) ) continue;
        $edyear = $_POST['edyear'.$i];
        $school = $_POST['school'.$i]; 
        if ( strlen($edyear) < 1 || strlen($school) < 1) {
            return "All Education fields are required";
        }
        if ( ! is_numeric($edyear) ) {
            return "Education year must be numeric";
        }
        return true;
    }   
}

// Retrieves stored Positions
function loadPositions($pdo, $profile_id){
    $sql = "SELECT * FROM Position WHERE profile_id = :prof ORDER BY rank";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':prof' => $profile_id));
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $positions;
}

// Retrieves stored Education
function loadEducation($pdo, $profile_id) {
    $sql = "SELECT Education.profile_id, Education.rank, Education.year, Institution.name 
            FROM Education JOIN Institution
            ON Education.institution_id = Institution.institution_id 
            WHERE profile_id = :prof ORDER BY rank";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':prof' => $profile_id));
    $education = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $education;
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
        if ( ! isset($_POST['posyear'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['posyear'.$i];
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

// Loops through each position entry, pulls out the data and inserts it into
// the database.
// $rank is just a way to order them in the same order they were entered. 
function insertEducation($pdo, $profile_id){
    $rank = 1;
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edyear'.$i]) ) continue;
        if ( ! isset($_POST['school'.$i]) ) continue;
        $edyear = $_POST['edyear'.$i];
        $school = $_POST['school'.$i];
        // Look up school to see if it exists in the database
        $sql = "SELECT institution_id FROM Institution WHERE name = :school";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':school' => $school));
        $instid = $stmt->fetch(PDO::FETCH_ASSOC);
        // If the school is not yet in the database, add it, then add the
        // Education info from the form
        if ($instid == Null) {
            $sql = "INSERT INTO Institution (name) Values (:name)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':name' => $_POST['school'.$i]));    
            $instid = $pdo->lastInsertId();
            $sql = "INSERT INTO Education (profile_id, rank, year, institution_id) 
                    VALUES ( :pid, :rank, :edyear, :instid)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':edyear' => $edyear,
                ':instid' => $instid)
            );
        // If the school is already in the database, go ahead an add the form info.      
        } else {
            $sql = "INSERT INTO Education (profile_id, rank, year, institution_id) 
                    VALUES ( :pid, :rank, :edyear, :instid)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':edyear' => $edyear,
                ':instid' => $instid['institution_id'])
            );
        }
        $rank++;
    }
}
?>