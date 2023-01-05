<?php
require "config/config.php";
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    echo $mysqli->connect_error;
    exit();
}

$mysqli->set_charset('utf8');

$sql_occupied_now = "CREATE OR REPLACE VIEW occupiedNow AS
SELECT building_code, room_number, classrooms.id AS classroom_id, buildings.id AS building_id FROM classes
JOIN classrooms
    ON classes.classrooms_id = classrooms.id
JOIN buildings
    ON buildings.id = classrooms.buildings_id
WHERE ('" . $_GET['time'] . "' BETWEEN start_time_24 AND end_time_24) AND (" . $_GET['day'] . "= True);";


$results_on = $mysqli->query($sql_occupied_now);
if (!$results_on) {
    $mysqli->error;
    exit();
}

$sql_all_rooms = "CREATE OR REPLACE VIEW allRooms AS
SELECT building_code, room_number, classrooms.id AS classroom_id, buildings.id AS building_id FROM classrooms
JOIN buildings
    ON buildings.id = classrooms.buildings_id;";

$results_all = $mysqli->query($sql_all_rooms);
if (!$results_all) {
    $mysqli->error;
    exit();
}

$sql = "SELECT * FROM allRooms
WHERE classroom_id NOT IN(
    SELECT classroom_id FROM occupiedNow
) AND building_id = " . $_GET['building_id'] . ";";

$results = $mysqli->query($sql);
if (!$results) {
    $mysqli->error;
    exit();
}
$results_array = [];

while($row = $results->fetch_assoc()){
	array_push($results_array, $row);
}
// Convert the array into a json string so that the frontend can read it
echo json_encode($results_array);

$mysqli->close();
?>