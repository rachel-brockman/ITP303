<?php
require "config/config.php";

// DB Connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
  echo $mysqli->connect_error;
  exit();
}

$mysqli->set_charset('utf8');

$building_sql = "SELECT * FROM buildings";
$results_buildings = $mysqli->query($building_sql);
if (!$results_buildings) {
  $mysqli->error;
  exit();
}

$classroom_sql = "SELECT buildings.id AS building_id, classrooms.id AS classroom_id, building_code, room_number from buildings
JOIN classrooms
ON classrooms.buildings_id = buildings.id;";
$results_classrooms = $mysqli->query($classroom_sql);
if (!$results_classrooms) {
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
  <title>USC Classroom Finder | Building Search</title>
</head>
<link rel="stylesheet" href="navbar.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
<style>
  .centered {
    text-align: center;
  }

  .search {
    text-align: center;
    /* margin: auto; */
  }

  #building-ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    max-height: 70vh;
    overflow-x: hidden;
    overflow-y: scroll;
  }

  #building-ul li {
    border: 1px solid #ddd;
    margin-top: -1px;
    background-color: #f6f6f6;
    padding: 12px;
    color: black;
    display: block;
  }

  #building-ul li:hover {
    background-color: #eee;
  }

  #buildings {
    padding: 5px 5px 5px 10px;
    border: 1px solid #ddd;
    margin-left: 12px;
    display: block;
  }

  #search-col {
    text-align: center;
  }

  .room-result {
    border: 1px solid bisque;
    margin-right: 3px;
    margin-top: -1px;
    background-color: blanchedalmond;
    padding: 12px;
    color: black;
  }

  .room-result:hover {
    background-color: bisque;
  }

  #building-container {
    text-align: center;
    height: 67%;
    overflow-x: hidden;
    overflow-y: scroll;
  }

  .result-header {
    margin-right: 3px;
    border: 1px solid bisque;
    padding: 5px;
  }

  .rooms {
    max-height: 70vh;
    overflow-x: hidden;
    overflow-y: scroll;
  }

  .link {
    text-decoration: none;
  }

  #clear-button {
    margin-left: -24px;
  }

  .selected {
    background-color: #d6d6d6 !important;
  }

  .stuff {
    height: calc(100vh - 56px);
  }

  /* .smaller-thing {
    height: 100%;
    overflow-x: hidden;
    overflow-y: scroll;
  } */
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
            <a class="nav-link active" aria-current="page" href="building.php">Search by Building</a>
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
      <div class="container-fluid stuff">
        <div class="row centered">
          <p class="info">Select a building to view its classrooms. Click on a room to see its schedule.</p>
        </div>

        <div class="row search">
          <div class="col-4" id="search-col">
            <div class="row">
              <input type="text" id="buildings" placeholder="Search" autocomplete="off" class="col-8">
              <button class="col-4 btn btn-primary btn-sm" id="clear-button">Clear</button>
            </div>
          </div>
          <div class="col-8">
            <div class="row result-header">
              <div class="col-6 centered">
                Building
              </div>
              <div class="col-6 centered">
                Room Number
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-4" id="building-container">
            <ul id="building-ul">
              <?php while ($row = $results_buildings->fetch_assoc()) : ?>
                <li class="building-li" , id="<?php echo $row['id']; ?>"><?php echo $row['building_code']; ?></li>
              <?php endwhile; ?>
            </ul>
          </div>
          <div class="col-8 rooms">
            <?php while ($row = $results_classrooms->fetch_assoc()) : ?>
              <a href="<?php echo "room.php?id=" . $row['classroom_id']; ?>" class="link <?php echo $row['building_id']; ?>" id="<?php echo "room-" . $row['classroom_id']; ?>">
                <div class="row room-result">
                  <div class="col-6 centered building-code">
                    <?php echo $row['building_code']; ?>
                  </div>
                  <div class="col-6 centered room-number">
                    <?php echo $row['room_number']; ?>
                  </div>
                </div>
              </a>
            <?php endwhile; ?>
          </div>
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
  $("#buildings").on("keyup", function() {
    let input = document.getElementById("buildings");
    let filter = input.value.toUpperCase();
    let ul = document.getElementById("building-ul");
    let li = ul.getElementsByTagName('li');
    for (i = 0; i < li.length; i++) {
      txtValue = li[i].textContent || li[i].innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        li[i].style.display = "";
      } else {
        li[i].style.display = "none";
      }
    }
  });


  $(".building-li").on("click", function() {
    event.preventDefault();
    // console.log($(this).attr('id'));
    let clickedId = $(this).attr('id');
    let buildingName = $(this).html();
    let ul = document.getElementById("building-ul");
    let li = ul.getElementsByTagName('li');
    for (i = 0; i < li.length; i++) {
      li[i].classList.remove("selected");
    }
    $(this).addClass("selected");
    $("#buildings").val(buildingName);
    let rooms = document.getElementsByClassName('link');
    for (i = 0; i < rooms.length; i++) {
      rooms[i].style.display = "";
      if (!rooms[i].classList.contains(clickedId)) {
        rooms[i].style.display = "none";
      }
    }

  });

  $("#clear-button").on("click", function() {
    event.preventDefault();
    $("#buildings").val("");
    let ul = document.getElementById("building-ul");
    let li = ul.getElementsByTagName('li');
    for (i = 0; i < li.length; i++) {
      li[i].style.display = "";
      li[i].classList.remove("selected");
    }
    let rooms = document.getElementsByClassName('link');
    for (i = 0; i < rooms.length; i++) {
      rooms[i].style.display = "";
    }
  });
</script>

</html>