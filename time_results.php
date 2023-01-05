<?php
if (!isset($_GET["time"]) || empty($_GET["time"]) || !isset($_GET["weekday"]) || empty($_GET["weekday"])) {
    $error = "Invalid URL.";
} else {
    // var_dump($_GET);
    $time = $_GET['time'];
    $abb = $_GET['weekday'];
    // echo $time . " " . $abb;
    require "config/config.php";

    // DB Connection
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
    WHERE ('" . $time . "' BETWEEN start_time_24 AND end_time_24) AND (" . $abb . "= True);";


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
    );";

    $results = $mysqli->query($sql);
    if (!$results) {
        $mysqli->error;
        exit();
    }

    $building_sql = "SELECT * FROM buildings";
    $results_buildings = $mysqli->query($building_sql);
    if (!$results_buildings) {
        $mysqli->error;
        exit();
    }

    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USC Classroom Finder | Time Search Results</title>
</head>
<link rel="stylesheet" href="navbar.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" href="time_based_results.css">
<style>
    .centered {
        text-align: center;
        display: block;
    }

    #back-button {
        text-decoration: none;
        color: black;
    }

    #back-button:hover {
        color: #666;
    }

    .results {
        height: 100%;
        overflow-y: scroll;
    }

    .detail-col {
        visibility: hidden;
    }

    .dropdown {
        /* width: 70%; */
        margin-left: auto;
        margin-right: auto;
    }

    #no-results {
        display: none;
        color: red;
        text-decoration: none;
        background-color: blanchedalmond;
        padding: 12px;
        border: 1px solid bisque;
        margin-top: -1px;
    }

    .stuff {
        height: calc(100vh - 56px);
    }

    .smaller-thing {
        height: 72%;
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" id="logo" href="home.php"><strong>We Are <span class="red">S</span>earching for <span class="red">C</span>lassrooms</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="open_now.php">Open Now</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="building.php">Search by Building</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="time.php">Search by Time</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reserve.php">Reserve a Room</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_reservations.php">View Reservations</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="everything">
        <div class="vert-center">
            <div class="container centered stuff">
                <div class="row">
                    <div class="col-3 mt-1">
                        <a href="time.php" id="back-button">&laquo; Back</a>
                    </div>
                </div>
                <?php if (isset($error) && !empty($error)) : ?>
                    <div class="text-danger">
                        <?php echo $error; ?>
                    </div>
                <?php else : ?>
                    <div class="row">
                        <div class="col mb-1 info">
                            Classrooms available at <span id="search-params"></span>
                        </div>
                    </div>
                    <div class="container centered smaller-thing">
                        <div class="col header">
                            <div class="row">
                                <div class="col-6">
                                    <!-- <button class="btn">Building</button> -->
                                    <select name="buildings" id="building-dropdown" class="form-select centered dropdown" autocomplete="off">
                                        <option value="0" selected="selected">All Buildings</option>

                                        <!-- Rating dropdown options here -->
                                        <?php while ($row = $results_buildings->fetch_assoc()) : ?>
                                            <option value="<?php echo $row["id"]; ?>">
                                                <?php echo $row["building_code"]; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-6"><button class="btn room-btn disabled">Room Number</button></div>
                            </div>
                        </div>

                        <div class="results">
                            <?php while ($row = $results->fetch_assoc()) : ?>
                                <div class="room <?php echo $row['building_id'] ?>">
                                    <div class="row main-info">
                                        <div class="col-6 building"><?php echo $row['building_code']; ?></div>
                                        <div class="col-6 number"><?php echo $row['room_number']; ?></div>
                                    </div>
                                </div>
                                <div class="col supplemental">
                                    <div class="row other-time">
                                        <div class="col-6">Available Until:</div>
                                        <div class="col-6">
                                            <a href="<?php echo "room.php?id=" . $row['classroom_id']; ?>" class="link full-schedule">View Full Schedule</a>
                                        </div>
                                    </div>
                                    <div class="row more">
                                        <div class="col-6 availability" id="<?php echo $row['building_code'] . $row['room_number'] . "-availability"; ?>"></div>
                                        <div class="col-6 detail-col" id="<?php echo $row['building_code'] . $row['room_number']; ?>">
                                            <a href="" target="_blank" class="link details">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            <div class="row main-info" id="no-results">
                                <div class="col-8 me-auto ms-auto">There are no rooms available in this building at the time you selected.</div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>

    <?php endif; ?>
    </div>
    </div>
    <div class="container-fluid footer">
        <hr>
        <p>© 2022 Rachel Brockman</p>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="classroom_results.js"></script>
<script>
    let searchedTime = to12HourFormat(new Date("1970-01-01 " + "<?php echo $time; ?>"));
    // console.log(searchedTime);
    let searchParams = document.querySelector("#search-params");
    let searchedDay = "<?php echo $abb ?>";
    let fullDay = ""
    if (searchedDay == "m") {
        fullDay = "Monday";
    } else if (searchedDay == "tu") {
        fullDay = "Tuesday";
    } else if (searchedDay == "w") {
        fullDay = "Wednesday";
    } else if (searchedDay == "th") {
        fullDay = "Thursday";
    } else if (searchedDay == "f") {
        fullDay = "Friday";
    } else if (searchedDay == "sa") {
        fullDay = "Saturday";
    } else if (searchedDay == "su") {
        fullDay = "Sunday";
    }
    searchParams.innerHTML = searchedTime.hours + ':' + searchedTime.minutes + ' ' + searchedTime.meridian + " on " + fullDay + "s:"

    $("#building-dropdown").on("change", function() {
        let optionSelected = $("option:selected", this);
        let valueSelected = this.value;
        let errorMessage = document.querySelector("#no-results");
        // console.log(valueSelected);
        let rooms = document.getElementsByClassName('room');
        let count = 0;
        for (i = 0; i < rooms.length; i++) {
            rooms[i].style.display = "none";
            errorMessage.style.display = "none";
            if (valueSelected != 0) {
                if (rooms[i].classList.contains(valueSelected)) {
                    count += 1;
                    rooms[i].style.display = "";
                }
                console.log("count: " + count);
                if (count == 0) {
                    errorMessage.style.display = "block";
                }
            } else {
                rooms[i].style.display = ""
            }
        }
    });

    $(".room").on("click", function() {
        if ($(this).next().hasClass("expanded")) {
            let building = $(this).find(".building").html();
            let room = $(this).find(".number").html();
            let time = <?php echo json_encode($time); ?>;
            let detailsCode = "#" + building + room;

            ajaxGet("get_link.php?building=" + building + "&room=" + room, function(results) {
                let JSresult = JSON.parse(results);
                // console.log(JSresult);
                let details = $(detailsCode);
                let usc_id = JSresult[0].usc_id
                if (usc_id) {
                    details.css("visibility", "visible");
                    let link = "https://web-app.usc.edu/web/its/spaces/room-finder/details.php?id=" + usc_id;
                    let newHTML = "<a href='" + link + "' target='_blank' class='link details'>View Details</a>"
                    details.html(newHTML);
                }
            });
        }
    });

    $(".room").on("click", function() {
        if ($(this).next().hasClass("expanded")) {
            let building = $(this).find(".building").html();
            let room = $(this).find(".number").html();
            let time = <?php echo json_encode($time); ?>;
            let detailsCode = "#" + building + room;
            ajaxGet("get_availability.php?building=" + building + "&room=" + room + "&day=" + <?php echo json_encode($abb); ?>, function(results) {
                let JSresult = JSON.parse(results);
                let timeArray = [];
                let availabilityId = detailsCode + "-availability";
                let avail = $(availabilityId);
                for (let i = 0; i < JSresult.length; i++) {
                    if (JSresult[i].start_time_24 > time) {
                        timeArray.push(JSresult[i].start_time_24);
                    }
                }
                if (timeArray.length != 0) {
                    let nextTime = to12HourFormat(new Date("1970-01-01 " + timeArray[0]));
                    avail.html(nextTime.hours + ":" + nextTime.minutes + " " + nextTime.meridian);
                } else {
                    avail.html("Available for the rest of the day!");
                }
            });
        }
    });
</script>

</html>