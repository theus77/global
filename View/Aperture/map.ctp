<script type="text/javascript" src="<?php 
	echo 'https://maps.googleapis.com/maps/api/js?key='.Configure::read('googleApiKey');
?>">
</script>

<script type="text/javascript">

	function CenterControl(controlDiv, map) {

	  // Set CSS for the control border
	  var controlUI = document.createElement('div');
	  controlUI.style.backgroundColor = '#fff';
	  controlUI.style.border = '2px solid #fff';
	  controlUI.style.borderRadius = '3px';
	  controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
	  //controlUI.style.cursor = 'pointer';
	  controlUI.style.marginTop = '5px';
	  controlUI.style.textAlign = 'center';
	  controlUI.title = '<?php echo __('Vers la page de recherche thématique'); ?>';
	  controlDiv.appendChild(controlUI);

	  // Set CSS for the control interior
	  var controlText = document.createElement('a');
	  controlText.setAttribute('href', '<?php echo $this->Html->url(
				array('controller' => 'aperture', 'action' => 'library', 'language' => Configure::read('Config.language')));?>');
	  controlText.style.color = 'rgb(25,25,25)';
	  controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
	  controlText.style.fontSize = '16px';
	  controlText.style.lineHeight = '38px';
	  controlText.style.paddingLeft = '5px';
	  controlText.style.paddingRight = '5px';
	  controlText.innerHTML = '<?php echo __('Recherche thématique'); ?>';
	  controlUI.appendChild(controlText);

	  // Setup the click event listeners: simply set the map to
	  // Chicago
	  google.maps.event.addDomListener(controlUI, 'click', function() {
	    map.setCenter(chicago)
	  });

	}

	function initialize() {
      	 var north = <?php echo $location['Place']['maxLatitude']; ?>;
    	 var south = <?php echo $location['Place']['minLatitude']; ?>;
     	 var west = <?php echo $location['Place']['minLongitude']; ?>;
    	 var est = <?php echo $location['Place']['maxLongitude']; ?>;
	   	 var southwest = new google.maps.LatLng(south, west);
		 var northeast = new google.maps.LatLng(north, est);
    	 
    	 var bounds = new google.maps.LatLngBounds(southwest, northeast);
         


		var mapOptions = {
          center: {lat: 50.9538632, lng: 4.2199824},
          maxZoom: 19,
          minZoom: 7,
          zoom: 9,
          tilt: 0,
          mapTypeId: google.maps.MapTypeId.HYBRID
        };
        var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	 
	   	 map.setCenter(bounds.getCenter()); //or use custom center
		 map.fitBounds(bounds);
		 map.setZoom(map.getZoom()-1);



		  var centerControlDiv = document.createElement('div');
		  var centerControl = new CenterControl(centerControlDiv, map);

		  centerControlDiv.index = 1;
		  map.controls[google.maps.ControlPosition.TOP_LEFT].push(centerControlDiv);
		 

		var markers = [];

		  google.maps.event.addListener(map, 'zoom_changed', function() {
				for (var i = 0; i < markers.length; i++) {
					markers[i].setMap(null);
				}
				markers = [];			  
		  });
		
		  google.maps.event.addListener(map, 'idle', function() {
			  var bounds = map.getBounds();
				 var data = {
						 zoom: map.getZoom(),
						 north: bounds.getNorthEast().lat(),
						 south: bounds.getSouthWest().lat(),
						 east: bounds.getNorthEast().lng(),
						 west: bounds.getSouthWest().lng() };

				 


				 
		  		$.getJSON( "/aperture/markers/language:<?php echo Configure::read('Config.language'); ?>.json", data, function(result) {
						$.each( result.markers, function( key, val ) {
							//console.log(val);
							var myLatlng = new google.maps.LatLng(val['lat'], val['lng']);
							var marker = new google.maps.Marker({
							      position: myLatlng,
							      map: map,
							      title: val['label']
							  });
							  //console.log(val);
							
							  						
							var infowindow = new google.maps.InfoWindow({
								content: val['content']
							  });
							 google.maps.event.addListener(marker, 'click', function() {
								    infowindow.open(map,marker);
							 });
							 markers.push(marker);
						});
					});
			  });
		 

		 
	
	}
    google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    
<div class="row" id="map-ctp">
	<div class="col-xs-12">
		<div class="outer">
		    <div class="inner" id="map-canvas">
		        <?php echo __('Connection à Google Map en cours ...'); ?>
		    </div>
		    <img src="data:image/gif;base64,R0lGODlhEAAJAIAAAP///wAAACH5BAEAAAAALAAAAAAQAAkAAAIKhI+py+0Po5yUFQA7" class="scaling-image" /> <!-- don't specify height and width so browser resizes it appropriately -->
		</div>
	</div>	   
</div>


 