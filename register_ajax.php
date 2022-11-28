<?php
$mysqli = new mysqli('localhost', 'jmarks', 'SQL032301', 'calendar');

if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
}
// login_ajax.php

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$username = $json_obj['username'];
$ptpassword = $json_obj['password'];

// $username = $mysqli->real_escape_string($username); //Prevents SQL injection
// $ptPassword =$mysqli->real_escape_string($password);

$hash = password_hash($ptpassword, PASSWORD_BCRYPT); //Hash password

$stmt = $mysqli->prepare("insert into users (user_id, hashed_password) values (?,?)"); // Prepare to add new user info to database
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
    }

$stmt->bind_param('ss', $username, $hash);
$stmt->execute();
$stmt->close();

echo json_encode(array(
    "success" => true
));
exit;
// if(!hash_equals($_SESSION['token'], $_POST['token'])){
//     die("Request forgery detected");
//     }
?>