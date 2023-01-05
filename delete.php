<?php
require "config/config.php";
$isDeleted = false;

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $error = "Invalid reservation.";
} else {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        echo $mysqli->connect_error;
        exit();
    }

    $statement = $mysqli->prepare("DELETE FROM reservations WHERE id = ?");
    $statement->bind_param("i", $_GET["id"]);

    $executed = $statement->execute();
    if (!$executed) {
        echo $mysqli->error;
        exit();
    }

    // Check that only one row was affected
    if ($statement->affected_rows == 1) {
        $isDeleted = true;
    }

    $statement->close();

    $mysqli->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>USC Classroom Finder | Delete Reservation</title>
    <link rel="stylesheet" href="navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="time_based_results.css">
    <style>
        .centered {
            text-align: center;
        }

        .everything {
            background-image: url("Media/football.webp");
        }
    </style>
</head>

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

                <div class="row mt-4">
                    <div class="col-12">
                        <?php if (isset($error) && !empty($error)) : ?>
                            <div class="text-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($isDeleted) : ?>
                            <div class="text-success">Reservation was successfully deleted.</div>
                        <?php endif; ?>

                    </div> <!-- .col -->
                </div> <!-- .row -->
                <div class="row mt-4 mb-4">
                    <div class="col-12">
                        <a href="view_reservations.php" role="button" class="btn btn-primary">Back to all reservations</a>
                    </div> <!-- .col -->
                </div> <!-- .row -->

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

</html>