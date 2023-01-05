<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>USC Classroom Finder | Time Search</title>
</head>
<link rel="stylesheet" href="navbar.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<link rel="stylesheet" href="time_based_results.css">
<style>
  .centered {
    text-align: center;
    display: block;
  }

  #time {
    width: 100%;
    margin-bottom: 10px;
    padding: 5px;
  }

  .everything {
    height: calc(100vh - 56px);
    background-image: url("Media/usc.jpg");
    background-size: cover;
  }

  .vert-center {
    /* height: 25vh; */
    background-color: white;
    margin-top: auto;
    margin-bottom: auto;
    display: inline-block;
    vertical-align: middle;
    box-shadow: rgba(0, 0, 0, 0.3) 0px 19px 38px, rgba(0, 0, 0, 0.22) 0px 15px 12px;
  }

  .info {
    padding-top: 10px;
    font-size: large;
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
            <a class="nav-link active" href="home.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="open_now.php">Open Now</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="building.php">Search by Building</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="time.php">Search by Time</a>
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
      <div class="container-fluid">
        <div class="row centered">
          <p class="info">Select a time and day of the week to see room availability.</p>
        </div>

        <form action="time_results.php" method="GET">
          <div class="row centered">
            <div class="col-6 me-auto ms-auto">
              <div class="row">
                <div class="col-5 p-0">
                  <input type="time" id="time" name="time">
                </div>
                <div class="col-5 p-0">
                  <select name="weekday" id="weekday-dropdown" class="form-select centered dropdown" name="day">
                    <option value="m">Monday</option>
                    <option value="tu">Tuesday</option>
                    <option value="w">Wednesday</option>
                    <option value="th">Thursday</option>
                    <option value="f">Friday</option>
                    <option value="sa">Saturday</option>
                    <option value="su">Sunday</option>
                  </select>
                </div>
                <div class="col-2 p-0">
                  <button type="submit" class="btn btn-primary">Search</button>
                </div>
              </div>
            </div>
          </div>
        </form>
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
  let timeInput = document.querySelector("#time");
  let dayInput = document.querySelector("#weekday-dropdown");
  let now = new Date();
  let day = now.getDay();
  let abb = "";
  if (day == 0) {
    abb = 'su';
  } else if (day == 1) {
    abb = 'm';
  } else if (day == 2) {
    abb = 'tu';
  } else if (day == 3) {
    abb = 'w';
  } else if (day == 4) {
    abb = 'th';
  } else if (day == 5) {
    abb = 'f';
  } else if (day == 6) {
    abb = 'sa';
  }

  setDay(dayInput, abb);

  function setDay(selectObj, valToSet) {
    for (var i = 0; i < selectObj.options.length; i++) {
      if (selectObj.options[i].value == valToSet) {
        selectObj.options[i].selected = true;
        return;
      }
    }
  }

  timeInput.value = ("0" + now.getHours()).slice(-2) + ":" + ("0" + now.getMinutes()).slice(-2);
</script>

</html>