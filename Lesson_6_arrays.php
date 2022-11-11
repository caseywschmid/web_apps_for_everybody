<?php


$stuff = array("Hi", "There");
echo $stuff[1], "\n";

$stuff = array("name" => "Casey", "course" => "WA4E");
echo $stuff["course"], "\n";

// The function print_r() shows PHP data - it is good for debugging
// print_r is more for if you know what data you're going to get back
// echoing the 'pre' tags is how you get it to print nicely on a web page
$stuff = array("name" => "Casey", "course" => "WA4E");
echo ("<pre>\n");
print_r($something);
echo ("\n</pre>\n");

// var_dump() is like print_r() but much more detail

// You can add things to the end of an array by using blank brakets
// Says "stick it at the end"
$va = array();
$va[] = "Hello";
$va[] = "World";

// You can also add to arrays by nameing key value pairs
$za = array();
$za["name"] = "Casey";
$za["course"] = "WA4E";

// Looping through an array
// If you don't have keys, the keys are the numerical indexed position
// This loop works if you have gaps in your array
$stuff = array("name" => "Casey", "course" => "WA4E");
foreach($stuff as $k => $v){
    echo "Key = ", $k, " Val = ", $v, "\n" ;
}

// Counted Loop
// This requires that the array has no gaps
$stuff = array("Casey", "WA4E");
for($i = 0; $i < count($stuff); $i++) {
    echo "I=", $i, " Val=", $stuff[$i],"\n";
}

// You can make arrays of arrays

// ARRAY FUNCTIONS

// array_key_exists($key, $array) // Returns TRUE if key is sey in the array
// isset($array['key']); // Returns TRUE if key is set in the array 
// count($array) // How many elements are in the array
// is_array($array) // Returns TRUE if a variable is an array
// sort($array) // Sorts the array values (loses key) - Worst Sort
    // This might be a cool way to go through a counted loop
// ksort($array) // Sorts the array by key
// asort($array) // Sorts the array by value, keeping key association
// shuffle($array) // Shuffles the array into random order


$za = array();
$za["name"] = "Casey";
$za["course"] = "WA4E";

if  (array_key_exists('course', $za)) {
    echo ("Course Exists\n");
} else {
    echo ("Course Does Not Exist\n");
}

// This was used a lot in PHP 6 and earlier
// There is a new operator in PHP 7 that make this much easier.
echo isset($za['name']) ? "name is set\n" : "name is not set\n";
echo isset($za['addr']) ? "addr is set\n" : "addr is not set\n";

// Null coalescing operator 
// The null coalescing operator (??) has been added as syntactic sugar for the
// common case of needing to use a ternary in conjunction with isset(). 
// It returns its first operand if it exists and is not null; otherwise it 
// returns its second operand.


// Fetches the value of $_GET['user'] and returns 'nobody'
// if it does not exist.
$username = $_GET['user'] ?? 'nobody';
// This is equivalent to:
$username = isset($_GET['user']) ? $_GET['user'] : 'nobody';

// Coalescing can be chained: this will return the first
// defined value out of $_GET['user'], $_POST['user'], and
// 'nobody'.
$username = $_GET['user'] ?? $_POST['user'] ?? 'nobody';

// Equivalents to above examples are as follows
$name = $za['name'] ?? "name is not set";
$addr = $za['addr'] ?? "addr is not set";
echo ("Name=$name\n");
echo ("Addr=$addr\n");

// Exploding Arrays
$inp = "This is a sentance with seven words";
$temp = explode(' ', $inp);
    // explode([delimiter], [array])
print_r($temp);
