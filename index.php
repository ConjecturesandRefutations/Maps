<!-- Display one directory -->

				<!-- Map -->
				<?php
					// Retrieve latitude and longitude custom field value
					$lat_lng = get_post_meta(get_the_ID(), 'business_latitude_and_longitude', true);

					// Split the value into latitude and longitude
					list($latitude, $longitude) = explode(',', $lat_lng);
					?>

					<!-- Output map container -->
					<div id="map" style="width: 100%; height: 400px;"></div>

					<script>
					// Initialize map
					function initMap() {
					var location = { lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?> };
					var map = new google.maps.Map(document.getElementById('map'), {
						zoom: 15,
						center: location
					});
					
					// Add marker
					var marker = new google.maps.Marker({
						position: location,
						map: map
					});
					}
					</script>

					<script src="https://maps.googleapis.com/maps/api/js?key=MY_API_KEY&callback=initMap" async defer></script>

<!-- Display all directories
 --><?php
    $directories_query = new WP_Query(array(
        'post_type' => 'business_directory',
        'posts_per_page' => -1, // Retrieve all directories
    ));

    // Initialize an array to store all coordinates
    $coordinates = array();

    // Loop through each directory to collect coordinates
    if ($directories_query->have_posts()) {
        while ($directories_query->have_posts()) {
            $directories_query->the_post();
            $latLongStr = get_field('business_latitude_and_longitude', get_the_ID());
            if (!empty($latLongStr)) {
                list($latitude, $longitude) = explode(',', $latLongStr);
                $coordinates[] = array('lat' => $latitude, 'lng' => $longitude);
            }
        }
        wp_reset_postdata();
    }
?>

<?php if (!empty($coordinates)) { ?>
    <div id="map" style="width: 100%; height: 400px;"></div>

    <script>
    // Initialize map
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10, // You can adjust the zoom level as needed
            center: { lat: <?php echo $coordinates[0]['lat']; ?>, lng: <?php echo $coordinates[0]['lng']; ?> }
        });

        // Add markers for each directory
        <?php foreach ($coordinates as $coordinate) { ?>
            var location = { lat: <?php echo $coordinate['lat']; ?>, lng: <?php echo $coordinate['lng']; ?> };
            var marker = new google.maps.Marker({
                position: location,
                map: map
            });
        <?php } ?>
    }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=MY_API_KEY&callback=initMap" async defer></script>
<?php } ?>



<!-- Adding Number Clusters to all Directories Map -->

<script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>

<?php
    $directories_query = new WP_Query(array(
        'post_type' => 'business_directory',
        'posts_per_page' => -1, // Retrieve all directories
    ));

    // Initialize an array to store all coordinates
    $coordinates = array();

    // Loop through each directory to collect coordinates
    if ($directories_query->have_posts()) {
        while ($directories_query->have_posts()) {
            $directories_query->the_post();
            $latLongStr = get_field('business_latitude_and_longitude', get_the_ID());
            if (!empty($latLongStr)) {
                list($latitude, $longitude) = explode(',', $latLongStr);
                $coordinates[] = array('lat' => $latitude, 'lng' => $longitude, 'title' => get_the_title());
            }
        }
        wp_reset_postdata();
    }
?>

<?php if (!empty($coordinates)) { ?>
    <div id="map" style="width: 100%; height: 400px;"></div>

    <script>
    // Initialize map
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 10, 
            center: { lat: <?php echo $coordinates[0]['lat']; ?>, lng: <?php echo $coordinates[0]['lng']; ?> }
        });

        // Add markers for each directory
        var markers = [];
        <?php foreach ($coordinates as $key => $coordinate) { ?>
            var location = { lat: <?php echo $coordinate['lat']; ?>, lng: <?php echo $coordinate['lng']; ?> };
            var marker = new google.maps.Marker({
                position: location,
                title: '<?php echo $coordinate['title']; ?>'
            });
            markers.push(marker);
        <?php } ?>

        // Add MarkerClusterer to manage clustering
        var markerCluster = new MarkerClusterer(map, markers, {
            imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m',
            gridSize: 60,
            minimumClusterSize: 1
        });

        // Adjust marker visibility based on zoom level
        google.maps.event.addListener(map, 'zoom_changed', function() {
            var zoomLevel = map.getZoom();
            if (zoomLevel >= 20) { 
                markerCluster.clearMarkers();
                markers.forEach(function(marker) {
                    marker.setMap(map);
                });
            } else {
                markers.forEach(function(marker) {
                    marker.setMap(null);
                });
            }
        });
    }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=MY_API_KEY&callback=initMap" async defer></script>
<?php } ?>