<!-- also going to have to check availability against reservations table -->
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

    // $sql_occupied_now = "CREATE OR REPLACE VIEW occupiedNow AS
    // SELECT building_code, room_number, classrooms.id AS classroom_id, buildings.id AS building_id FROM classes
    // JOIN classrooms
    //     ON classes.classrooms_id = classrooms.id
    // JOIN buildings
    //     ON buildings.id = classrooms.buildings_id
    // WHERE ('" . $time . "' BETWEEN start_time_24 AND end_time_24) AND (" . $abb . "= True);";


    // $results_on = $mysqli->query($sql_occupied_now);
    // if (!$results_on) {
    //     $mysqli->error;
    //     exit();
    // }

    // $sql_all_rooms = "CREATE OR REPLACE VIEW allRooms AS
    // SELECT building_code, room_number, classrooms.id AS classroom_id, buildings.id AS building_id FROM classrooms
    // JOIN buildings
    //     ON buildings.id = classrooms.buildings_id;";

    // $results_all = $mysqli->query($sql_all_rooms);
    // if (!$results_all) {
    //     $mysqli->error;
    //     exit();
    // }

    // $sql = "SELECT * FROM allRooms
    // WHERE classroom_id NOT IN(
    //     SELECT classroom_id FROM occupiedNow
    // );";

    // $results = $mysqli->query($sql);
    // if (!$results) {
    //     $mysqli->error;
    //     exit();
    // }

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
    <title>USC Classroom Finder | Reserve a Room</title>
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

    .form-stuff {
        width: 60vw;
    }

    #warning {
        font-size: small;
        display: none;
    }

    .hidden {
        display: none;
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
                        <a class="nav-link active" aria-current="page" href="reserve.php">Reserve a Room</a>
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
            <div class="container stuff">
                <div class="row centered">
                    <div class="col-3 mt-1">
                        <a href="reserve.php" id="back-button">&laquo; Back</a>
                    </div>
                </div>
                <?php if (isset($error) && !empty($error)) : ?>
                    <div class="text-danger">
                        <?php echo $error; ?>
                    </div>
                <?php else : ?>
                    <div class="row centered">
                        <div class="col mb-1 info">
                            Currently making a reservation for <span id="search-params"></span>
                        </div>
                    </div>
                    <div class="container form-stuff">
                        <form id="res-form" action="reserve_confirmation.php" method="POST">
                            <div class="form-group row">
                                <label for="name-id" class="col-5 col-lg-3 col-form-label text-sm-right">Name: <span class="text-danger">*</span></label>
                                <div class="col-7 col-lg-9">
                                    <input type="text" class="form-control centered" id="name-id" name="name">
                                </div>
                            </div> <!-- .form-group -->

                            <div class="form-group row">
                                <label for="building-id" class="col-5 col-lg-3 col-form-label text-sm-right">Building: <span class="text-danger">*</span></label>
                                <div class="col-7 col-lg-9">
                                    <select class="form-control centered" id="building-id" name="building">
                                        <option value="0" selected disabled>-- Select --</option>
                                        <?php while ($row = $results_buildings->fetch_assoc()) : ?>
                                            <option value="<?php echo $row['id']; ?>">
                                                <?php echo $row['building_code']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div> <!-- .form-group -->
                            <div class="form-group row">
                                <label for="room-id" class="col-5 col-lg-3 col-form-label text-sm-right">Room: <span class="text-danger">*</span></label>
                                <div class="col-7 col-lg-9">
                                    <select class="form-control centered" id="room-id" name="room">
                                        <option value="0" selected disabled>-- Select --</option>
                                    </select>
                                </div>
                            </div> <!-- .form-group -->
                            <div class="form-group row hidden">
                                <div class="col-7 col-lg-9">
                                    <input type="text" class="form-control centered" id="time-id" name="time" value="<?php echo $time; ?>">
                                </div>
                            </div> <!-- .form-group -->
                            <div class="form-group row hidden">
                                <div class="col-7 col-lg-9">
                                    <input type="text" class="form-control centered" id="day-id" name="day" value="<?php echo $abb; ?>">
                                </div>
                            </div> <!-- .form-group -->

                            <div class="container centered">
                                <div class="row">
                                    <div class="col-5 col-lg-3 mt-2">
                                        <p class="text-danger" id="warning">Please fill out all required fields!</p>
                                    </div>
                                    <div class="col-7 col-lg-9 mt-2">
                                        <button type="submit" class="btn btn-primary" id="submit-btn">Submit</button>
                                    </div>

                                </div>
                            </div>
                        </form>

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
<!-- <script src="classroom_results.js"></script> -->
<script>
    document.querySelector("#room-id").disabled = true;
    // document.querySelector("#submit-btn").disabled = true;
    $("#submit-btn").on("click", function() {
        event.preventDefault();
        let warning = document.querySelector("#warning");
        let name = document.querySelector("#name-id").value;
        let building = document.querySelector("#building-id").value;
        let room = document.querySelector("#room-id").value;
        warning.style.display = "none";
        // console.log(name + building + room);
        if (name == "" || name.length == 0 || building == 0 || room == 0) {
            // console.log("error");
            warning.style.display = "block";
        } else {
            // console.log("submit");
            $("#res-form").submit();
        }
    });

    $("#building-id").on("change", function() {
        let valueSelected = this.value;
        let roomSelector = document.querySelector("#room-id");
        let time = "<?php echo $time; ?>";
        let day = "<?php echo $abb; ?>";
        // console.log(valueSelected);
        ajaxGet("available_rooms.php?building_id=" + valueSelected + "&time=" + time + "&day=" + day, function(results) {
            let JSresult = JSON.parse(results);
            while (roomSelector.firstChild) {
                roomSelector.removeChild(roomSelector.lastChild);
            }
            // console.log(JSresult);
            if (JSresult.length == 0) {

                let opt = document.createElement('option');
                // opt.disabled = true;
                opt.value = 0;
                opt.innerHTML = "No rooms available at selected time.";
                roomSelector.appendChild(opt);
            } else {
                for (let i = 0; i < JSresult.length; i++) {
                    let opt = document.createElement('option');
                    opt.value = JSresult[i].classroom_id;
                    opt.innerHTML = JSresult[i].room_number;
                    roomSelector.appendChild(opt);
                }
                document.querySelector("#room-id").disabled = false;
            }

        });
    });

    function to12HourFormat(date = (new Date)) {
        return {
            hours: ((date.getHours() + 11) % 12 + 1),
            minutes: (date.getMinutes() < 10 ? '0' : '') + date.getMinutes(),
            meridian: (date.getHours() >= 12) ? 'PM' : 'AM',
        };
    }


    function ajaxGet(endpointUrl, returnFunction) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', endpointUrl, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState == XMLHttpRequest.DONE) {
                if (xhr.status == 200) {
                    returnFunction(xhr.responseText);
                } else {
                    alert('AJAX Error.');
                    console.log(xhr.status);
                }
            }
        }
        xhr.send();
    };
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
    searchParams.innerHTML = searchedTime.hours + ':' + searchedTime.minutes + ' ' + searchedTime.meridian + " on " + fullDay + "."
</script>

</html>