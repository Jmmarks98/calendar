<?php
$mysqli = new mysqli('localhost', 'jmarks', 'SQL032301', 'calendar');

if($mysqli->connect_errno) {
        echo json_encode(array(
                "success" => false,
                "message"=>"connection failed"
        ));

        exit;
}
// login_ajax.php

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$username = (string)$json_obj['username'];
$password = $json_obj['password'];
//This is equivalent to what you previously did with $_POST['username'] and $_POST['password']

// Check to see if the username and password are valid.  (You learned how to do this in Module 3.)
$stmt = $mysqli->prepare("SELECT COUNT(*), user_id, hashed_password FROM users WHERE user_id=?");

$valid = false;
if($stmt){
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($cnt, $user_id, $pwd_hash);
    $stmt->fetch();
    $pwd_guess = (string)$password;
    if($cnt == 1 && password_verify($pwd_guess, $pwd_hash)){
        // Login succeeded!
        // Set bool to true
        $valid = true;
        }
    }
else{
    echo json_encode(array(
                "success" => false
        ));
        exit;
    }
if($valid){
    ini_set("session.cookie_httponly", 1);
    session_start();
    $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION['username'] = $username;

    echo json_encode(array(
            "success" => true
    ));
    exit;
}
else{
    echo json_encode(array(
            "success" => false,
            "message" => "Incorrect Username or Password"
    ));
    exit;
}
?>
