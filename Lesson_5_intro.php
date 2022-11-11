<?php
// Variable names start with dollar signs ($)
// If you forget the dollar sign, it may or may not blow up on you.
// You don't have to do anything special for strings that span multiple lines
// You use the '.' for concatenation, NOT the '+'
// Single quotes are NOT the same thing as double quotes
// You use 'echo' as a print statement

// DOUBLE QUOTED STRINGS
echo "this is a simple string\n";

echo "You can also have embedded newlines in 
strings like this. No issues with strings breaking
across multiple lines.";

// Outputs:     This will expand:
//              a newline
echo "This will expand: \na newline";

//When you use double quotes, it looks for a variable. 
// Outputs:     Variables do 12
$expand = 12;
echo "Variables do $expand\n";

// SINGLE QUOTED STRINGS

echo 'this is a simple string';

echo 'You can also have embedded newlines in
strings this way as it is
okay to do';

// Outputs: Arnold once said: "I'll be back"
echo 'Arnold once said: "I\'ll be back"';

// Outputs: You deleted C:\*.*?
echo 'You deleted C:\\*.*?';

// Outputs: You deleted C:\*.*?
echo 'You deleted C:\*.*?';

// Outputs: This will not expand: \n a newline
echo 'This will not expand: \n a newline';

// Outputs: Variables do not $expand $either
echo 'Variables do not $expand $either';

// Use single quotes normally and only use double quotes when you have a good 
// reason and intention to do so.

/* You can do comments like this */

// There's a print function also 

// Increment / Decrement ( ++ / -- )
    // Dont do this 
// String concatenation ( . )
    // If you want a space you have to add it in yourself
// Equality ( == / != )
// Identity ( === / !== )
    // Use this when you're comparing to TRUE and FALSE
// Ternary ( ?: )
    // Don't use this but you'll see code with this used. 
    // It's from PHP 5
    // $variable = [question] ? [value if true] : [value if false]
// Side-effect Assignment ( += / -= / .= / etc. )
    // $out = 'Hello';
    // $out = $out . ' ';
    // $out .= 'World!'     // This is the same as the line above it. 
// Ignore the rarely used bitwise operators ( << / >> / ^ / | / & )

// echo doesn't show FALSE!!!!!!!

// Logical Operators
// == - equal to (this is a question)
// != - NOT equal to
// < - Less than
// > - Greater than
// <= - Less than or equal to
// >= - Greater than or equal to
// && - And \
// || - Or  - Python just uses words for these
// ! - Not  /

// Here is how you structure an IF statement
$ans = 42;
if ($ans == 42) {
    print "Hello world!\n";
} else {
    print "Wrong Answer\n";
}

if ($a > $b) {
    echo "a is bigger than b";
} elseif ($a == $b) {
    echo "a is equal to b";
} else {
    echo "a is smaller than b";
}

// Here is a WHILE loop 
$fuel = 10;
while ($fuel > 1) {
    print 'Vroom Vroom\n';
    $fuel = $fuel - 1;
}

// Here is a DO-WHILE loop 
// This excecutes at least once
$count = 1;
do {
    echo "$count times 5 is " . $count * 5;
    echo "\n";
} while (++$count <= 5);

// a FOR loop is the simplest way to contruct a counted loop 
for ($count=1; $count<=6; $count++){
    echo "$count times 6 is " . $count * 6;
    echo "\n";
}

// Breaking out of a loop 
// The 'break' statement ends the current loop and jumps to the statement 
// immediately following the loop. 
// It is like a loop test that can happen anywhere in the body of a loop.
for($count=1; $count<=600; $count++){
    if ($count == 5){
        break;
    }
    echo "Count: $count\n";
}
echo "Done\n";

// Finishing an iteration with 'continue'
for($count=1; $count<=10; $count++){
    if (($count % 2) == 0) continue;
    echo "Count: $count\n";
}
echo "Done\n";

?>

