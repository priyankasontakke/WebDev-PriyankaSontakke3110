<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Smart Farming Management System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>

body{
    font-family:Arial,sans-serif;
    background:#f4f8f3;
}

.navbar{
    background:#02502b;
    padding:12px 0;
}

.navbar-brand{
    font-weight:bold;
    font-size:24px;
}

.nav-link{
    color:white!important;
    margin-left:15px;
    font-weight:500;
}

.nav-link:hover{
    color:#ffc107!important;
}

.hero{
    min-height:520px;

    background:
    linear-gradient(
        rgba(0,80,30,.60),
        rgba(0,80,30,.60)
    ),
    url("https://images.unsplash.com/photo-1625246333195-78d9c38ad449");

    background-size:cover;
    background-position:center;

    display:flex;
    align-items:center;

    color:white;
}

.hero h1{
    font-size:48px;
    font-weight:bold;
}

.hero p{
    font-size:20px;
    line-height:1.6;
}

.btn-custom{
    background:#ffc107;
    color:#000;
    font-weight:bold;
    border:none;
    padding:12px 25px;
    border-radius:8px;
}

.section-title{
    color:#198754;
    font-weight:bold;
    margin-bottom:35px;
}

.dashboard-card{
    border:none;
    border-radius:15px;
    padding:25px;

    box-shadow:0 4px 15px rgba(0,0,0,.10);

    transition:.3s;
}

.dashboard-card:hover{
    transform:translateY(-7px);
}

.icon{
    font-size:42px;
    color:#198754;
}

.value{
    font-size:30px;
    font-weight:bold;
    color:#333;
}

.feature-card{
    border:none;
    border-radius:15px;
    padding:30px;
    height:100%;

    background:white;

    box-shadow:0 3px 12px rgba(0,0,0,.08);
}

.about{
    background:white;
}

.form-control{
    padding:12px;
    border-radius:8px;
}

footer{
    background:#146c43;
    color:white;
    padding:25px;
}

.location-box{
    background:white;
    border-radius:15px;
    padding:22px;

    box-shadow:
    0 3px 12px rgba(0,0,0,.08);
}

@media(max-width:768px){

    .hero{
        min-height:450px;
    }

    .hero h1{
        font-size:35px;
    }

    .hero p{
        font-size:17px;
    }

    .nav-link{
        margin-left:0;
    }

}

</style>

</head>

<body>


<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg navbar-dark">

<div class="container">

<a class="navbar-brand" href="#home">

<i class="bi bi-flower1"></i>

Smart Farming

</a>


<button
class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#navbarMenu">

<span class="navbar-toggler-icon"></span>

</button>


<div class="collapse navbar-collapse" id="navbarMenu">

<ul class="navbar-nav ms-auto">

<li class="nav-item">
<a class="nav-link" href="#home">
Home
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="#dashboard">
Dashboard
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="#features">
Features
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="soil_test.php">
<i class="bi bi-clipboard2-pulse"></i>
Soil Testing
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="#about">
About
</a>
</li>

<li class="nav-item">
<a class="nav-link" href="#contact">
Contact
</a>
</li>

<li class="nav-item">

<a class="nav-link" href="login.php">

<i class="bi bi-box-arrow-in-right"></i>

Login

</a>

</li>

</ul>

</div>

</div>

</nav>



<!-- ================= HERO ================= -->

<section class="hero" id="home">

<div class="container">

<div class="row">

<div class="col-lg-7">

<h1>

Smart Farming
Management System

</h1>

<p class="mt-3">

Manage your farm intelligently using
modern technology, location-based weather
monitoring and smart irrigation solutions.

</p>

<button
class="btn btn-custom mt-3"
onclick="goToDashboard()">

<i class="bi bi-speedometer2"></i>

View Dashboard

</button>

</div>

</div>

</div>

</section>



<!-- ================= DASHBOARD ================= -->

<section class="py-5" id="dashboard">

<div class="container">

<h2 class="text-center section-title">

Farm Monitoring Dashboard

</h2>



<!-- LOCATION -->

<div class="location-box mb-4">

<h5>

<i class="bi bi-geo-alt-fill text-success"></i>

Farm Location

</h5>


<div class="row g-2 mt-2">

<div class="col-md-7">

<input
type="text"
id="locationInput"
class="form-control"
placeholder="Enter location e.g. Parbhani">

</div>


<div class="col-md-2">

<button
class="btn btn-success w-100"
onclick="searchLocation()">

<i class="bi bi-search"></i>

Search

</button>

</div>


<div class="col-md-3">

<button
class="btn btn-outline-success w-100"
onclick="useMyLocation()">

<i class="bi bi-crosshair"></i>

Use My Location

</button>

</div>

</div>


<div
id="locationStatus"
class="mt-3 text-muted">

Enter your farm location or use your current location.

</div>

</div>



<!-- DASHBOARD CARDS -->

<div class="row g-4">



<!-- SOIL MOISTURE -->

<div class="col-md-6 col-lg-3">

<div class="card dashboard-card text-center">

<i class="bi bi-droplet icon"></i>

<h5 class="mt-3">

Soil Moisture

</h5>

<div class="value">

<span id="moisture">

65

</span>%

</div>

<p
id="moistureStatus"
class="text-success">

Optimal

</p>

</div>

</div>



<!-- TEMPERATURE -->

<div class="col-md-6 col-lg-3">

<div class="card dashboard-card text-center">

<i class="bi bi-thermometer-half icon"></i>

<h5 class="mt-3">

Temperature

</h5>

<div class="value">

<span id="temperature">

--

</span>°C

</div>

<p class="text-success">

Location Weather

</p>

</div>

</div>



<!-- HUMIDITY -->

<div class="col-md-6 col-lg-3">

<div class="card dashboard-card text-center">

<i class="bi bi-cloud-drizzle icon"></i>

<h5 class="mt-3">

Humidity

</h5>

<div class="value">

<span id="humidity">

--

</span>%

</div>

<p class="text-success">

Location Weather

</p>

</div>

</div>



<!-- IRRIGATION -->

<div class="col-md-6 col-lg-3">

<div class="card dashboard-card text-center">

<i class="bi bi-water icon"></i>

<h5 class="mt-3">

Irrigation

</h5>

<div
class="value"
id="irrigation">

OFF

</div>

<p
id="irrigationStatus"
class="text-secondary">

Waiting for moisture data

</p>

</div>

</div>


</div>

</div>

</section>



<!-- ================= FEATURES ================= -->

<section
class="py-5 bg-light"
id="features">

<div class="container">

<h2 class="text-center section-title">

Smart Farming Features

</h2>


<div class="row g-4">


<div class="col-md-6 col-lg-3">

<div class="feature-card text-center">

<i class="bi bi-moisture icon"></i>

<h4 class="mt-3">

Soil Monitoring

</h4>

<p>

Monitor soil moisture and maintain
suitable conditions for healthy
crop growth.

</p>

</div>

</div>



<div class="col-md-6 col-lg-3">

<div class="feature-card text-center">

<i class="bi bi-clipboard2-pulse icon"></i>

<h4 class="mt-3">
Soil Testing
</h4>

<p>
Enter soil test results to get a soil health
score, fertiliser advice and suitable crops.
</p>

<a href="soil_test.php" class="btn btn-success btn-sm">
Open Soil Testing
</a>

</div>

</div>

<div class="col-md-6 col-lg-3">

<div class="feature-card text-center">

<i class="bi bi-cloud-sun icon"></i>

<h4 class="mt-3">

Weather Monitoring

</h4>

<p>

Monitor temperature and humidity
according to the selected farm location.

</p>

</div>

</div>



<div class="col-md-6 col-lg-3">

<div class="feature-card text-center">

<i class="bi bi-water icon"></i>

<h4 class="mt-3">

Smart Irrigation

</h4>

<p>

Automatically show irrigation ON
when soil moisture becomes too low.

</p>

</div>

</div>


</div>

</div>

</section>



<!-- ================= ABOUT ================= -->

<section
class="about py-5"
id="about">

<div class="container">

<div class="row align-items-center">


<div class="col-md-6">

<h2 class="section-title">

About Smart Farming

</h2>

<p>

Smart Farming Management System uses
modern technology to monitor and manage
farming activities.

</p>

<p>

The system can display location-based
temperature and humidity.

</p>

<p>

Soil moisture and irrigation can later
be connected to a real IoT sensor.

</p>

</div>


<div class="col-md-6 text-center">

<i
class="bi bi-tree-fill"
style="font-size:180px;color:#198754;">

</i>

</div>


</div>

</div>

</section>



<!-- ================= CONTACT ================= -->

<section
class="py-5 bg-light"
id="contact">

<div class="container">

<h2 class="text-center section-title">

Contact Us

</h2>


<div class="row justify-content-center">

<div class="col-md-6">


<form onsubmit="showMessage(event)">


<input
type="text"
class="form-control mb-3"
placeholder="Enter Name"
required>


<input
type="email"
class="form-control mb-3"
placeholder="Enter Email"
required>


<textarea
class="form-control mb-3"
rows="4"
placeholder="Enter Message"
required>
</textarea>


<button
type="submit"
class="btn btn-success w-100">

<i class="bi bi-send"></i>

Send Message

</button>


</form>

</div>

</div>

</div>

</section>



<!-- ================= FOOTER ================= -->

<footer class="text-center">

<p class="mb-1">

© 2026 Smart Farming Management System

</p>

<small>

Developed using HTML, CSS, JavaScript & Bootstrap

</small>

</footer>



<script
src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>



<!-- ================= JAVASCRIPT ================= -->

<script>


/* ================= DASHBOARD ================= */

function goToDashboard(){

document
.getElementById("dashboard")
.scrollIntoView({

behavior:"smooth"

});

}



/* ================= CONTACT ================= */

function showMessage(event){

event.preventDefault();

alert(
"Thank you! Your message has been submitted."
);

}



/* ================= IRRIGATION ================= */

function setIrrigation(moisture){

const irrigation =
document.getElementById("irrigation");

const status =
document.getElementById("irrigationStatus");

const moistureStatus =
document.getElementById("moistureStatus");


/*

If moisture < 40
Irrigation ON

If moisture >= 40
Irrigation OFF

*/

if(moisture < 40){

irrigation.innerText = "ON";

irrigation.className =
"value text-danger";

status.innerText =
"Soil is dry - Irrigation ON";

status.className =
"text-danger";

moistureStatus.innerText =
"Low";

moistureStatus.className =
"text-danger";

}

else{

irrigation.innerText = "OFF";

irrigation.className =
"value text-success";

status.innerText =
"Soil moisture sufficient - Irrigation OFF";

status.className =
"text-success";


if(moisture >= 60){

moistureStatus.innerText =
"Optimal";

moistureStatus.className =
"text-success";

}

else{

moistureStatus.innerText =
"Moderate";

moistureStatus.className =
"text-warning";

}

}

}



/* ================= WEATHER ================= */

async function getWeather(
lat,
lon,
locationName
){

try{


document
.getElementById("locationStatus")
.innerHTML =

"<strong>Location:</strong> "
+
locationName
+
" | Loading weather...";


/*

Open-Meteo API

Temperature
Humidity

*/

const url =

`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m&timezone=auto`;


const response =
await fetch(url);


if(!response.ok){

throw new Error(
"Weather request failed"
);

}


const data =
await response.json();


/* Temperature */

document
.getElementById("temperature")
.innerText =

Math.round(
data.current.temperature_2m
);


/* Humidity */

document
.getElementById("humidity")
.innerText =

Math.round(
data.current.relative_humidity_2m
);


/* Location */

document
.getElementById("locationStatus")
.innerHTML =

"<strong>Selected Location:</strong> "
+
locationName
+
" <span class='badge bg-success'>Weather Updated</span>";


}

catch(error){

document
.getElementById("locationStatus")
.innerText =

"Weather data could not be loaded.";

}

}



/* ================= SEARCH LOCATION ================= */

async function searchLocation(){

const location =
document
.getElementById("locationInput")
.value
.trim();


if(!location){

alert(
"Please enter a location."
);

return;

}


document
.getElementById("locationStatus")
.innerText =
"Searching location...";


try{


/*

Convert location
into latitude longitude.

*/

const url =

`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(location)}&count=1&language=en&format=json`;


const response =
await fetch(url);


const data =
await response.json();


if(
!data.results ||
data.results.length === 0
){

document
.getElementById("locationStatus")
.innerText =

"Location not found. Try another name.";

return;

}


const place =
data.results[0];


const name =

[
place.name,
place.admin1,
place.country

]
.filter(Boolean)
.join(", ");


/* Get weather */

await getWeather(

place.latitude,
place.longitude,
name

);



/*

DEMO SOIL MOISTURE

Real sensor can be connected later.

*/

const demoMoisture = 65;


document
.getElementById("moisture")
.innerText =
demoMoisture;


setIrrigation(
demoMoisture
);


}

catch(error){

document
.getElementById("locationStatus")
.innerText =

"Could not search this location.";

}

}



/* ================= MY LOCATION ================= */

function useMyLocation(){


if(!navigator.geolocation){

alert(
"Your browser does not support location sharing."
);

return;

}


document
.getElementById("locationStatus")
.innerText =

"Requesting your location...";


navigator.geolocation.getCurrentPosition(

async function(position){


const lat =
position.coords.latitude;


const lon =
position.coords.longitude;


/* Get weather */

await getWeather(

lat,
lon,
"My Current Location"

);



/*

DEMO SOIL MOISTURE

Real IoT sensor required
for actual soil moisture.

*/

const demoMoisture = 65;


document
.getElementById("moisture")
.innerText =
demoMoisture;


setIrrigation(
demoMoisture
);


},


function(){

document
.getElementById("locationStatus")
.innerText =

"Location permission was denied. Please allow location access in your browser.";

}

);

}



/* ================= INITIAL ================= */

setIrrigation(65);


</script>


</body>

</html>