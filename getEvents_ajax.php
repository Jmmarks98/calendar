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
$username = $json_obj['username'];


$stmt = $mysqli->prepare("select event_id, name, year, month, day, time, tag from events where owner=(?) OR share!='no' order by time asc"); //checks if owner is the same as username or the event is public
if(!$stmt){
    printf("Query Prep Failed: %s\n", $mysqli->error);
    exit;
    }
$stmt->bind_param('s', $username);
$stmt->execute();
if(!$stmt->bind_result($id, $name, $year, $month, $day, $time, $tag)){
    echo json_encode(array(
        "success" => "bind fail"
    ));
    exit;
}
$events = array();
while($stmt->fetch()){
    array_push($events, array(htmlentities($id), htmlentities($name), htmlentities($year), htmlentities($month), htmlentities($day), htmlentities($time), htmlentities($tag)));
}
$stmt->close();

echo json_encode(array(
    "success" => $events
));
exit;
?>
