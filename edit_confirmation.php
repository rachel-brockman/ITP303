<?php

if (!isset($_POST['name']) || empty($_POST['name']) || !isset($_POST['building']) || empty($_POST['building']) || !isset($_POST['room']) || empty($_POST['room']) || !isset($_POST['time']) || empty($_POST['time']) || !isset($_POST['day']) || empty($_POST['day']) || !isset($_POST['res_id']) || empty($_POST['res_id'])) {
    $error = "Invalid URL.";
} else {
    $name = $_POST['name'];
    $building = $_POST['building'];
    $room = $_POST['room'];
    $time = $_POST['time'];
    $day = $_POST['day'];
    if ($day == "m") {
        $fullDay = "Monday";
    } else if ($day == "tu") {
        $fullDay = "Tuesday";
    } else if ($day == "w") {
        $fullDay = "Wednesday";
    } else if ($day == "th") {
        $fullDay = "Thursday";
    } else if ($day == "f") {
        $fullDay = "Friday";
    } else if ($day == "sa") {
        $fullDay = "Saturday";
    } else if ($day == "su") {
        $fullDay = "Sunday";
    }
    require "config/config.php";

    // DB Connection
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        echo $mysqli->connect_error;
        exit();
    }

    $mysqli->set_charset('utf8');

    $statement = $mysqli->prepare("UPDATE reservations SET name = ?, classrooms_id = ?, day = ?, time = ? WHERE reservations.id = ?");

    //2. bind variable to placeholders by stating each user input variable along with its type
    // 1st arg: the data type of the user input that's expected
    // 2+ args: the user input varaibles, in order of the SQL statement
    $statement->bind_param("sissi", $name, $room, $day, $time, $_POST["res_id"]);

    $executed = $statement->execute();

    if (!$executed) {
        echo $mysqli->error;
        exit();
    }

    //use affected rows to see if the update was successful
    if ($statement->affected_rows == 1) {
        $isUpdated = true;
    }

    $statement->close();
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USC Classroom Finder | Reservation Confirmation</title>
</head>
<link rel="stylesheet" href="navbar.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" href="time_based_results.css">
<style>
    .centered {
        text-align: center;
    }

    .everything {
        background-image: url("Media/quad.webp");
    }

    #yay {
        margin-top: 10px;
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
            <div class="container stuff centered">
                <?php if (isset($error) && !empty($error)) : ?>
                    <div class="text-danger">
                        <?php echo $error; ?>
                    </div>
                <?php else : ?>
                    <?php if ($isUpdated) : ?>
                        <h3 id="yay">Success!</h3>
                        <a href="view_reservations.php" class="btn btn-primary mb-2">View all reservations</a>
                    <?php endif; ?>
                <?php endif; ?>
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
    function to12HourFormat(date = (new Date)) {
        return {
            hours: ((date.getHours() + 11) % 12 + 1),
            minutes: (date.getMinutes() < 10 ? '0' : '') + date.getMinutes(),
            meridian: (date.getHours() >= 12) ? 'PM' : 'AM',
        };
    }
    let time = to12HourFormat(new Date("1970-01-01 " + "<?php echo $time; ?>"));
    document.querySelector("#res-time").innerHTML = time.hours + ":" + time.minutes + " " + time.meridian;
</script>

</html>