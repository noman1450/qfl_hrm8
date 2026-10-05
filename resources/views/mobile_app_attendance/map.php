<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Get the leaflet CSS file -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin="" />
    </head>
    <body>
        <!-- Specify the map and it's dimensions -->
        <div id="map" style="width: 400px; height: 350px"></div>
        <!-- Get the leaflet JavaScript file -->
        <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>
        <script>
            var locations = [<?php echo $locations;?>];
            var map = L.map('map').setView([<?php echo $setlocations;?>], 16);
            mapLink = '<a href="http://openstreetmap.org">OpenStreetMap</a>';
              L.tileLayer(
                'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                  attribution: '&copy; ' + mapLink + ' Contributors',
                  maxZoom: 18,
                }).addTo(map);

              for (var i = 0; i < locations.length; i++) {
                marker = new L.marker([locations[i][1], locations[i][2]]).bindPopup(locations[i][0]).addTo(map).openPopup();
              }
        </script>
    </body>
</html>
