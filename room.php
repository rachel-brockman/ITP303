<?php
// var_dump($_GET);
if (!isset($_GET["id"]) || empty($_GET["id"])) {
  $error = "Invalid classroom ID.";
} else {
  require "config/config.php";
  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  if ($mysqli->connect_errno) {
    echo $mysqli->connect_error;
    exit();
  }
  $mysqli->set_charset("utf-8");

  $sql = "SELECT classrooms.id, building_code, room_number, usc_id FROM classrooms
    JOIN buildings
    ON buildings.id = classrooms.buildings_id
    WHERE classrooms.id = " . $_GET['id'] . ";";

  $results = $mysqli->query($sql);
  if (!$results) {
    echo $mysqli->error;
    exit();
  }

  $sql_classes = "SELECT course_number, course_title, m, tu, w, th, f, sa, su, start_time_24, end_time_24 FROM classes
  JOIN classrooms
  ON classes.classrooms_id = classrooms.id
  JOIN buildings
  ON buildings.id = classrooms.buildings_id
  WHERE classrooms.id = " . $_GET['id'] . ";";

  $results_classes = $mysqli->query($sql_classes);
  if (!$results_classes) {
    echo $mysqli->error;
    exit();
  }


  $row = $results->fetch_assoc();


  $classes = [];

  while ($row_class = $results_classes->fetch_assoc()) {
    $days_of_week = [];
    if ($row_class['su']) {
      array_push($days_of_week, '0');
    }
    if ($row_class['m']) {
      array_push($days_of_week, '1');
    }
    if ($row_class['tu']) {
      array_push($days_of_week, '2');
    }
    if ($row_class['w']) {
      array_push($days_of_week, '3');
    }
    if ($row_class['th']) {
      array_push($days_of_week, '4');
    }
    if ($row_class['f']) {
      array_push($days_of_week, '5');
    }
    if ($row_class['sa']) {
      array_push($days_of_week, '6');
    }
    $classes_for_cal = array("title" => $row_class['course_number'], "daysOfWeek" => $days_of_week, "startTime" => $row_class['start_time_24'], "endTime" => $row_class['end_time_24']);
    array_push($classes, $classes_for_cal);
  }


  $js_array = json_encode($classes);

  date_default_timezone_set("America/Los_Angeles");
  $time = date("H:i:s");
  $day = date("l");

  $formatted_time = date("g:i A");
  $formatted_day = date("l, F  d, Y");

  if ($day == 'Monday') {
    $abb = 'm';
  } elseif ($day == 'Tuesday') {
    $abb = 'tu';
  } elseif ($day == 'Wednesday') {
    $abb = 'w';
  } elseif ($day == 'Thursday') {
    $abb = 'th';
  } elseif ($day == 'Friday') {
    $abb = 'f';
  } elseif ($day == 'Saturday') {
    $abb = 'sa';
  } elseif ($day == 'Sunday') {
    $abb = 'su';
  }


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

  $sql_final = "SELECT * FROM allRooms
  WHERE classroom_id NOT IN(
	  SELECT classroom_id FROM occupiedNow
  ) AND classroom_id = " . $_GET['id'] . ";";

  $results_final = $mysqli->query($sql_final);
  if (!$results_final) {
    $mysqli->error;
    exit();
  }

  $avaliable = $results_final->fetch_assoc();

  if (!empty($avaliable) != 0) {
    $open = True;
  } else {
    $open = False;
  }
  // var_dump($open);
  $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>USC Classroom Finder | <?php echo $row['building_code'] . " " . $row['room_number']; ?></title>
</head>
<link rel="stylesheet" href="navbar.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link href="fullcalendar/lib/main.css" rel="stylesheet" />
<script src="fullcalendar/lib/main.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var today = new Date();
    var time = today.getHours() + ":" + ('0' + today.getMinutes()).slice(-2);
    // console.log(time);
    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        start: '',
        end: ''
      },
      initialView: 'timeGridWeek',
      nowIndicator: true,
      views: {
        timeGridWeek: {
          allDaySlot: false,
        }
      },
      dayHeaderFormat: {
        weekday: 'short'
      },
      navLinkDayClick: false,
      scrollTime: time,
      events: <?php echo $js_array; ?>
    });
    calendar.render();
  });
</script>
<style>
  .top {
    margin-top: 10px;
  }

  .top h1 {
    margin: 0px;
  }

  a {
    text-decoration: none;
  }

  h3 {
    margin: 0px;
  }

  #calendar {
    overflow-x: hidden;
    overflow-y: scroll;
    max-width: 1280px;
    height: 100%;
    margin-top: -20px;

  }

  .stuff {
    height: calc(100vh - 70px);
  }

  .smaller-thing {
    height: 80%;
  }

  .green {
    color: #6DB866;
  }

  .red {
    color: #CB0600;
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
      <?php if (isset($error) && !empty($error)) : ?>
        <div class="text-danger">
          <?php echo $error; ?>
        </div>
      <?php else : ?>
        <div class="container stuff">
          <div class="row top">
            <h1><?php echo $row['building_code'] . " " . $row['room_number'] ?> -
              <?php if ($open) : ?>
                <span class="green">Available Now</span>
              <?php else : ?>
                <span class="red">Not Currently Available</span>
              <?php endif; ?>
            </h1>
          </div>
          <div class="row">
            <?php if (!empty($row['usc_id'])) : ?>
              <a href="<?php echo "https://web-app.usc.edu/web/its/spaces/room-finder/details.php?id=" . $row['usc_id']; ?>" target="_blank">View Details</a>
            <?php endif; ?>
          </div>
          <div class="row">
            <div class="col">
              <h3>Schedule</h3>
            </div>
          </div>
          <div class="smaller-thing">
            <div id='calendar'></div>
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

</html>