<!-- also going to have to check availability against reservations table -->
<?php
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    $error = "Invalid URL.";
} else {
    // var_dump($_GET);
    // $time = $_GET['time'];
    // $abb = $_GET['weekday'];
    // echo $time . " " . $abb;
    require "config/config.php";

    // DB Connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        echo $mysqli->connect_error;
        exit();
    }

    $mysqli->set_charset('utf8');

    $sql = "SELECT reservations.id, name, time, day, room_number, building_code, buildings.id AS building_id, classrooms.id AS classroom_id FROM reservations
    JOIN classrooms ON
    classrooms.id = reservations.classrooms_id
    JOIN buildings
    ON buildings.id = classrooms.buildings_id
    WHERE reservations.id = " . $_GET['id'] . ";";

    $results = $mysqli->query($sql);
    if (!$results) {
        $mysqli->error;
        exit();
    }
    $row_res = $results->fetch_assoc();
    $res_name = $row_res['name'];
    $res_building = $row_res['building_id'];
    $res_classroom = $row_res['classroom_id'];
    $res_time = $row_res['time'];
    $res_day = $row_res['day'];
    // echo ($res_name . " " . $res_building . " " . $res_classroom . " " . $res_time . " " . $res_day);

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

    #time-back {
        display: none;
    }

    #submit-btn {
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
            <div class="container stuff">
                <div class="row centered">
                    <div class="col-3 mt-1">
                        <a href="view_reservations.php" id="back-button">&laquo; Back</a>
                    </div>
                </div>
                <?php if (isset($error) && !empty($error)) : ?>
                    <div class="text-danger">
                        <?php echo $error; ?>
                    </div>
                <?php else : ?>
                    <div class="row centered">
                        <div class="col mb-1 info">
                            Edit Reservation
                        </div>
                    </div>
                    <div class="container form-stuff">
                        <form id="res-form" action="edit_confirmation.php" method="POST">
                            <div class="form-group row">
                                <label for="name-id" class="col-5 col-lg-3 col-form-label text-sm-right">Name: <span class="text-danger">*</span></label>
                                <div class="col-7 col-lg-9">
                                    <input type="text" class="form-control centered" id="name-id" name="name">
                                </div>
                            </div> <!-- .form-group -->
                            <div class="form-group row" id="day-row">
                                <label for="day-id" class="col-5 col-lg-3 col-form-label text-sm-right">Day: <span class="text-danger">*</span></label>
                                <div class="col-7 col-lg-9">
                                    <select class="form-control centered" id="day-id" name="">
                                        <option value="m">Monday</option>
                                        <option value="tu">Tuesday</option>
                                        <option value="w">Wednesday</option>
                                        <option value="th">Thursday</option>
                                        <option value="f">Friday</option>
                                        <option value="sa">Saturday</option>
                                        <option value="su">Sunday</option>
                                    </select>
                                </div>
                            </div> <!-- .form-group -->
                            <div class="form-group row" id="time-row">
                                <label for="time-id" class="col-5 col-lg-3 col-form-label text-sm-right">Time: <span class="text-danger">*</span></label>
                                <div class="col-7 col-lg-9">
                                    <input type="time" class="form-control centered" id="time-id" name="">
                                </div>
                            </div>
                            <div class="form-group row" id="building-row">
                                <label for="building-id" class="col-5 col-lg-3 col-form-label text-sm-right">Building: <span class="text-danger">*</span></label>
                                <div class="col-7 col-lg-9">
                                    <select class="form-control centered" id="building-id" name="building">
                                        <option value="0" selected disabled>-- Select --</option>
                                        <?php while ($row_building = $results_buildings->fetch_assoc()) : ?>

                                            <option value="<?php echo $row_building['id']; ?>">
                                                <?php echo $row_building['building_code']; ?>
                                            </option>

                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div> <!-- .form-group -->
                            <div class="form-group row" id="room-row">
                                <label for="room-id" class="col-5 col-lg-3 col-form-label text-sm-right">Room: <span class="text-danger">*</span></label>
                                <div class="col-7 col-lg-9">
                                    <select class="form-control centered" id="room-id" name="room">
                                        <option value="0" selected disabled>-- Select --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="container centered">
                                <div class="row">
                                    <div class="col-5 col-lg-3 mt-2">
                                        <p class="text-danger" id="warning">Please fill out all required fields!</p>
                                    </div>
                                    <div class="col-7 col-lg-9 mt-2">
                                        <a class="btn btn-primary" id="time-done">Click here when you're done editing the day and time!</a>
                                        <div class="row">
                                            <a class="btn btn-primary col-10" id="time-back">Click here to edit the day and time again!</a>
                                            <button type="submit" class="btn btn-primary col-2" id="submit-btn">Submit</button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group row hidden">
                                <div class="col-7 col-lg-9">
                                    <input type="text" class="form-control centered" id="res-id" name="res_id" value="<?php echo $_GET['id']; ?>">
                                </div>
                            </div> <!-- .form-group -->
                            <div class="form-group row hidden">
                                <div class="col-7 col-lg-9">
                                    <input type="text" class="form-control centered" id="fake-day" name="day">
                                </div>
                            </div> <!-- .form-group -->
                            <div class="form-group row hidden">
                                <div class="col-7 col-lg-9">
                                    <input type="text" class="form-control centered" id="fake-time" name="time">
                                </div>
                            </div> <!-- .form-group -->
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
    let timeInput = document.querySelector("#time-id");
    let dayInput = document.querySelector("#day-id");
    let now = new Date("1970-01-01 " + "<?php echo $res_time ?>");
    let day = "<?php echo $res_day ?>";
    // let abb = "";
    // if (day == 0) {
    //     abb = 'su';
    // } else if (day == 1) {
    //     abb = 'm';
    // } else if (day == 2) {
    //     abb = 'tu';
    // } else if (day == 3) {
    //     abb = 'w';
    // } else if (day == 4) {
    //     abb = 'th';
    // } else if (day == 5) {
    //     abb = 'f';
    // } else if (day == 6) {
    //     abb = 'sa';
    // }

    setDay(dayInput, day);

    function setDay(selectObj, valToSet) {
        for (var i = 0; i < selectObj.options.length; i++) {
            if (selectObj.options[i].value == valToSet) {
                selectObj.options[i].selected = true;
                return;
            }
        }
    }
    let timeDone = document.querySelector("#time-done");
    let goBack = document.querySelector("#time-back");
    let submit = document.querySelector("#submit-btn");
    let warning = document.querySelector("#warning");
    timeInput.value = ("0" + now.getHours()).slice(-2) + ":" + ("0" + now.getMinutes()).slice(-2);
    document.querySelector("#name-id").value = "<?php echo $res_name; ?>";
    document.querySelector("#room-id").disabled = true;
    document.querySelector("#building-id").disabled = true;
    $("#time-done").on("click", function() {
        warning.style.display = "none";
        if (document.querySelector("#time-id").value == "" || document.querySelector("#time-id").value.length == 0) {
            warning.style.display = "block";
        } else {
            document.querySelector("#day-id").disabled = true;
            document.querySelector("#time-id").disabled = true;
            document.querySelector("#room-id").disabled = false;
            document.querySelector("#building-id").disabled = false;
            document.querySelector("#fake-day").value = document.querySelector("#day-id").value;
            document.querySelector("#fake-time").value = document.querySelector("#time-id").value;
            submit.style.display = "block";
            goBack.style.display = "block";
            timeDone.style.display = "none";
        }
    });

    $("#time-back").on("click", function() {
        document.querySelector("#day-id").disabled = false;
        document.querySelector("#time-id").disabled = false;
        document.querySelector("#room-id").disabled = true;
        document.querySelector("#building-id").disabled = true;
        submit.style.display = "none";
        goBack.style.display = "none";
        timeDone.style.display = "block";
    });

    $("#submit-btn").on("click", function() {
        event.preventDefault();

        let name = document.querySelector("#name-id").value;
        let building = document.querySelector("#building-id").value;
        let room = document.querySelector("#room-id").value;
        let time = document.querySelector("#time-id").value;
        warning.style.display = "none";
        // console.log(name + building + room);
        if (name == "" || name.length == 0 || building == 0 || room == 0 || time == "" || time.length == 0) {
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
        let time = timeInput.value;
        let day = dayInput.value;
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
</script>

</html>