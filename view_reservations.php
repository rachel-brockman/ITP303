<?php
require "config/config.php";

// DB Connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    echo $mysqli->connect_error;
    exit();
}
$mysqli->set_charset('utf8');

$sql = "SELECT reservations.id, name, time, day, room_number, building_code FROM reservations
JOIN classrooms ON
classrooms.id = reservations.classrooms_id
JOIN buildings
ON buildings.id = classrooms.buildings_id;";

$results = $mysqli->query($sql);
if (!$results) {
    $mysqli->error;
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USC Classroom Finder | Reservations</title>
</head>
<link rel="stylesheet" href="navbar.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" href="time_based_results.css">
<style>
    .centered {
        text-align: center;
        display: block;
    }

    .info {
        margin-bottom: 0px;
    }

    .results {
        margin-bottom: 5px;
    }

    .everything {
        background-image: url("Media/village.jpeg");
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
                        <a class="nav-link active" aria-current="page" href="view_reservations.php">View Reservations</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="everything">
        <div class="vert-center">
            <div class="container stuff centered">
                <p class="info">Current Room Reservations</p>
                <div class="results">
                    <div class="row">
                        <div class="col-6" class="">
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-2"></div>
                                <div class="col-8">Name</div>
                            </div>
                        </div>
                        <div class="col-2" class="">
                            Room
                        </div>
                        <div class="col-2" class="">
                            Day
                        </div>
                        <div class="col-2" class="">
                            Time
                        </div>
                    </div>
                    <?php while ($row = $results->fetch_assoc()) : ?>
                        <!-- <a href="<?php echo "edit_reservation.php?id=" . $row['id']; ?>"> -->
                        <div class="row res" id="<?php echo $row['id']; ?>">
                            <div class="col-6" class="" id="">
                                <div class="row">
                                    <div class="col-2"><a class="btn btn-primary pt-0 pb-0" href="<?php echo "edit_reservation.php?id=" . $row['id']; ?>">Edit</a></div>
                                    <div class="col-2"><a class="btn btn-danger pt-0 pb-0" onclick="return confirm('Are you sure you want to delete this reservation?');" href="<?php echo "delete.php?id=" . $row['id']; ?>">Delete</a></div>
                                    <div class="col-8"><?php echo $row['name']; ?></div>
                                </div>
                            </div>
                            <div class="col-2" class="" id="">
                                <?php echo $row['building_code'] . " " . $row['room_number']; ?>
                            </div>
                            <div class="col-2" class="" id="">
                                <?php
                                if ($row['day'] == "m") {
                                    echo "Monday";
                                } else if ($row['day'] == "tu") {
                                    echo "Tuesday";
                                } else if ($row['day'] == "w") {
                                    echo "Wednesday";
                                } else if ($row['day'] == "th") {
                                    echo "Thursday";
                                } else if ($row['day'] == "f") {
                                    echo "Friday";
                                } else if ($row['day'] == "sa") {
                                    echo "Saturday";
                                } else if ($row['day'] == "su") {
                                    echo "Sunday";
                                }
                                ?>
                            </div>
                            <div class="col-2" class="" id="">
                                <?php echo $row['time']; ?>
                            </div>
                        </div>
                        <!-- </a> -->
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid footer">
        <hr>
        <p>© 2022 Rachel Brockman</p>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>

</script>

</html>