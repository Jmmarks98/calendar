<?php
$mysqli = new mysqli('localhost', 'jmarks', 'SQL032301', 'calendar');

if($mysqli->connect_errno) {
        printf("Connection Failed: %s\n", $mysqli->connect_error);
        exit;
}
// eventAdd_ajax.php

header("Content-Type: application/json"); // Since we are sending a JSON response here (not an HTML document), set the MIME Type to application/json

//Because you are posting the data via fetch(), php has to retrieve it elsewhere.
$json_str = file_get_contents('php://input');
//This will store the data into an associative array
$json_obj = json_decode($json_str, true);

//Variables can be accessed as such:
$id = $json_obj['id'];
$accessor = $json_obj['username'];

$stmt = $mysqli->prepare("select owner from events where event_id=".$id);
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
    }

$stmt->execute();
$stmt->bind_result($eventOwner);
$stmt->fetch();
$stmt->close();
if($accessor == $eventOwner){
    $stmt = $mysqli->prepare('delete from events where event_id='.$id); // Delete the event
    if(!$stmt) {
        printf("Query not added: %s \n",$mysqli->error);
    }
    $stmt->execute();
    $stmt->close();
}
echo json_encode(array(
    "success" => true
));
exit;
?>