<?php 
	session_start();
	require_once 'php/google-api-php-client/vendor/autoload.php';
?>
<!DOCTYPE html>

<html>
 <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <style>
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
.controls {
  margin-top: 10px;
  border: 1px solid transparent;
  border-radius: 2px 0 0 2px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  height: 32px;
  outline: none;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
}

#pac-input {
  background-color: #fff;
  font-family: Roboto;
  font-size: 15px;
  font-weight: 300;
  margin-left: 12px;
  padding: 0 11px 0 13px;
  text-overflow: ellipsis;
  width: 300px;
}

#pac-input:focus {
  border-color: #4d90fe;
}

.pac-container {
  font-family: Roboto;
}

#type-selector {
  color: #fff;
  background-color: #4d90fe;
  padding: 5px 11px 0px 11px;
}

#type-selector label {
  font-family: Roboto;
  font-size: 13px;
  font-weight: 300;
}

    </style>
    <title>Places Searchbox</title>
    <style>
      #target {
        width: 345px;
      }
    </style>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
          ['Opening Move', 'Percentage'],
          ["Correctly Classified Instances ", 90],
          ["Incorrectly Classified Instances ", 10],
          ["Relative absolute error ", 18.3333],
          ["Root relative squared error   ", 57.951],
          ['Mean absolute error  ', 10]
        ]);

        var options = {
          title: 'Chess opening moves',
          width: 900,
          legend: { position: 'none' },
          chart: { title: 'Result for OneR datamning ',
                   subtitle: 'error percentage' },
          bars: 'horizontal', // Required for Material Bar Charts.
          axes: {
            x: {
              0: { side: 'top', label: 'Percentage'} // Top x-axis.
            }
          },
          bar: { groupWidth: "90%" }
        };

        var chart = new google.charts.Bar(document.getElementById('top_x_div'));
        chart.draw(data, options);
      };
    </script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
          ['0- 3', 3],
          ['1- 3', 3],
          ['2- 2', 2],
          ['3- 2', 2],
        ]);

        // Set chart options
        var options = {'title':'The result from em cluster',
                       'width':400,
                       'height':300};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
    <input id="pac-input" class="controls" type="text" placeholder="Search Box">
    <div id="map"></div>	
		<script>

function initAutocomplete() {
  var map = new google.maps.Map(document.getElementById('map'), {
    center: {lat: -33.8688, lng: 151.2195},
    zoom: 13,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });

  // Create the search box and link it to the UI element.
  var input = document.getElementById('pac-input');
  var searchBox = new google.maps.places.SearchBox(input);
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

  // Bias the SearchBox results towards current map's viewport.
  map.addListener('bounds_changed', function() {
    searchBox.setBounds(map.getBounds());
  });

  var markers = [];
  // [START region_getplaces]
  // Listen for the event fired when the user selects a prediction and retrieve
  // more details for that place.
  searchBox.addListener('places_changed', function() {
    var places = searchBox.getPlaces();

    if (places.length == 0) {
      return;
    }

    // Clear out the old markers.
    markers.forEach(function(marker) {
      marker.setMap(null);
    });
    markers = [];

    // For each place, get the icon, name and location.
    var bounds = new google.maps.LatLngBounds();
    places.forEach(function(place) {
      var icon = {
        url: place.icon,
        size: new google.maps.Size(71, 71),
        origin: new google.maps.Point(0, 0),
        anchor: new google.maps.Point(17, 34),
        scaledSize: new google.maps.Size(25, 25)
      };

      // Create a marker for each place.
      markers.push(new google.maps.Marker({
        map: map,
        icon: icon,
        title: place.name,
        position: place.geometry.location
      }));

      if (place.geometry.viewport) {
        // Only geocodes have viewport.
        bounds.union(place.geometry.viewport);
      } else {
        bounds.extend(place.geometry.location);
      }
    });
    map.fitBounds(bounds);
  });
}


    </script>

   </script>
    <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyB_gqz4X9UFjw4nG01vcxDVwBxQ7Tqf0i4&libraries=places&callback=initAutocomplete"
         async defer></script>
    <link href='https://fonts.googleapis.com/css?family=Cabin' rel='stylesheet' type='text/css'>
	<link rel='stylesheet' type='text/css' href='/css/style.css'>
		
	<div class='content'>
	<?php
		$client = new Google_Client();
		$client->useApplicationDefaultCredentials();
		$client->addScope(Google_Service_Bigquery::BIGQUERY);
		$bigquery = new Google_Service_Bigquery($client);
		$projectId = 'projecttest-2017';

		$request = new Google_Service_Bigquery_QueryRequest();
		$str = '';
	
		$request->setQuery("SELECT id as ID,featurename as freq,Coordinates as location  FROM [Traffic_Count.Bike_shares] WHERE NBBIKES >10;");
		$response = $bigquery->jobs->query($projectId, $request);
		$rows = $response->getRows();

		$str = "<table>".
		"<tr>" .
		"<th>id</th>" .
		"<th>featurename</th>" .
		"<th>location</th>" .
		"</tr>";
		foreach ($rows as $row)
		{
			$str .= "<tr>";

			foreach ($row['f'] as $field)
			{
				$str .= "<td>" . $field['v'] . "</td>";
			}
			$str .= "</tr>";
		}

		$str .= '</table></div>';
        echo "Bike_results";
		echo $str;
		
		$request->setQuery("SELECT name as freq, subTheme as sub,location as place	  FROM [Traffic_Count.landmarks] where theme = 'Place Of Assembly';");
		$response = $bigquery->jobs->query($projectId, $request);
		$rows = $response->getRows();

		$str = "<table>".
		"<tr>" .
		"<th>name</th>" .
		"<th>sub</th>" .
		"<th>place</th>".
		"</tr>";
		foreach ($rows as $row)
		{
			$str .= "<tr>";

			foreach ($row['f'] as $field)
			{
				$str .= "<td>" . $field['v'] . "</td>";
			}
			$str .= "</tr>";
		}

		$str .= '</table></div>';
        echo "landmarks_results";
		echo $str;
		$request->setQuery("SELECT name as Name,location as place  FROM [Traffic_Count.pubilc_toilet] WHERE baby_facil = true;");
		$response = $bigquery->jobs->query($projectId, $request);
		$rows = $response->getRows();

		$str = "<table>".
		"<tr>" .
		"<th>name</th>" .
		"<th>place</th>".
		"</tr>";
		foreach ($rows as $row)
		{
			$str .= "<tr>";

			foreach ($row['f'] as $field)
			{
				$str .= "<td>" . $field['v'] . "</td>";
			}
			$str .= "</tr>";
		}

		$str .= '</table></div>';
        echo"toilet for baby_facil";
		echo $str;
		$request->setQuery("SELECT stop_number as stops,name as Name,address as ad,loaction as place  FROM [Traffic_Count.stops];");
		$response = $bigquery->jobs->query($projectId, $request);
		$rows = $response->getRows();

		$str = "<table>".
		"<tr>" .
		"<th>stops</th>" .
		"<th>name</th>" .		
		"<th>place</th>".
		"<th>location</th>".
		"</tr>";
		foreach ($rows as $row)
		{
			$str .= "<tr>";

			foreach ($row['f'] as $field)
			{
				$str .= "<td>" . $field['v'] . "</td>";
			}
			$str .= "</tr>";
		}

		$str .= '</table></div>';
        echo "stops in freezone";
		echo $str;
	
	
$primes = file('gs://s3497391-storage2/j48.txt');
$arrlength=count($primes);

echo "result for j48 !".'</br>';
foreach($primes as $v){
		echo "<font color=\"white\">".$v."</font>".'<br>';
}
$primes = file('gs://s3497391-storage2/apriori.txt');
$arrlength=count($primes);

echo "result  for apriori!".'</br>';
foreach($primes as $v){
		echo "<font color=\"white\">".$v."</font>".'<br>';
}

?>
	</div>

	 <div id="chart_div"></div>
	 <div id="top_x_div" style="width: 900px; height: 500px;"></div>
</body>
</html>
