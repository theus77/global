<input id="pac-input" class="controls" type="text" placeholder="Search Box">

<script type="text/javascript" src="<?php 
	echo 'https://maps.googleapis.com/maps/api/js?libraries=drawing,places&key='.Configure::read('googleApiKey');
?>">
</script>


<script type="text/javascript">


	var polygons = [];
	var markers = [];

	function removeMarker(index){
		polygon = polygons[index];
		polygon.setMap(null);
	}

	function initialize() {
      	 var north = <?php echo $place['Place']['maxLatitude']; ?>;
    	 var south = <?php echo $place['Place']['minLatitude']; ?>;
     	 var west = <?php echo $place['Place']['minLongitude']; ?>;
    	 var est = <?php echo $place['Place']['maxLongitude']; ?>;
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


        var input = /** @type {HTMLInputElement} */(
        	      document.getElementById('pac-input'));


	      
	      
		map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

		 var searchBox = new google.maps.places.SearchBox(
				    /** @type {HTMLInputElement} */(input));

				  // Listen for the event fired when the user selects an item from the
				  // pick list. Retrieve the matching places for that item.
				  google.maps.event.addListener(searchBox, 'places_changed', function() {
				    var places = searchBox.getPlaces();

				    if (places.length == 0) {
				      return;
				    }
				    for (var i = 0, marker; marker = markers[i]; i++) {
				      marker.setMap(null);
				    }

				    // For each place, get the icon, place name, and location.
				    markers = [];
				    var bounds = new google.maps.LatLngBounds();
				    for (var i = 0, place; place = places[i]; i++) {
				      var image = {
				        url: place.icon,
				        size: new google.maps.Size(71, 71),
				        origin: new google.maps.Point(0, 0),
				        anchor: new google.maps.Point(17, 34),
				        scaledSize: new google.maps.Size(25, 25)
				      };

				      // Create a marker for each place.
				      var marker = new google.maps.Marker({
				        map: map,
				        icon: image,
				        title: place.name,
				        position: place.geometry.location
				      });

				      markers.push(marker);

				      bounds.extend(place.geometry.location);
				    }

				    map.fitBounds(bounds);
				  });


		
        
        var drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.POLYGON,
            drawingControl: true,
            drawingControlOptions: {
//               position: google.maps.ControlPosition.TOP_CENTER,
              drawingModes: [
//                 google.maps.drawing.OverlayType.MARKER,
//                 google.maps.drawing.OverlayType.CIRCLE,
                google.maps.drawing.OverlayType.POLYGON,
//                 google.maps.drawing.OverlayType.POLYLINE,
//                 google.maps.drawing.OverlayType.RECTANGLE
              ]
            },
//             markerOptions: {
//               icon: 'images/beachflag.png'
//             },
//             circleOptions: {
//               fillColor: '#ffff00',
//               fillOpacity: 1,
//               strokeWeight: 5,
//               clickable: false,
//               editable: true,
//               zIndex: 1
// 			}
		});

        var counter = 0;

        google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {

			polygons[counter] = polygon;

			var infowindow = new google.maps.InfoWindow({
				content: '<label class="control-label" for="target-'+counter+'"><?php echo __('Informations à propos de la cible'); ?></label><textarea class="form-control" id="target-'+counter+'"></textarea><a href="#" onclick="removeMarker('+counter+'); return false;"><?php echo __('Effacer ce repère');?></a>'
			});

			drawingManager.setDrawingMode(null);
			polygon.getPath().getAt(0);
			infowindow.setPosition(polygon.getPath().getAt(0));
			
        	google.maps.event.addDomListener(polygon, 'click', function(event) {
        		infowindow.open(map,polygon);
        	});
            	
        });

		
        drawingManager.setMap(map);


		/****
		google.maps.event.addDomListener(map, 'click', function(event) {
			

			var marker = new google.maps.Marker({
			    position: event.latLng,
			    map: map,
			    draggable:true,
			    title:"Drag me!"
			});
			markers[counter] = marker;


		
			var infowindow = new google.maps.InfoWindow({
				content: '<label class="control-label" for="target-'+counter+'"><?php echo __('Informations à propos de la cible'); ?></label><textarea class="form-control" id="target-'+counter+'"></textarea><a href="#" onclick="removeMarker('+counter+'); return false;"><?php echo __('Effacer ce repère');?></a>'
			});

			++counter;
			
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map,marker);
			});

			
		}); ***/
 
		map.setCenter(bounds.getCenter()); //or use custom center
		map.fitBounds(bounds);
		//map.setZoom(map.getZoom()+1);

		// Bias the SearchBox results towards places that are within the bounds of the
		  // current map's viewport.
	  google.maps.event.addListener(map, 'bounds_changed', function() {
	    var bounds = map.getBounds();
	    searchBox.setBounds(bounds);
	  });
		 
	
	}
    google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    
    

<div class="row">
<h1><?php echo __('Reserver pour le vol "%s"', $flight['Flight']['name']);?></h1>

</div>

<div class="row">

<div class="col-md-8" id="map-ctp">
<?php echo $this->i18n->w('book.enter-your-markers'); ?> 
	<div class="outer">
	    <div class="inner" id="map-canvas">
	        <?php echo __('Connection à Google Map en cours ...'); ?>
	    </div>
	    <img src="data:image/gif;base64,R0lGODlhEAAJAIAAAP///wAAACH5BAEAAAAALAAAAAAQAAkAAAIKhI+py+0Po5yUFQA7" class="scaling-image" /> <!-- don't specify height and width so browser resizes it appropriately -->
	</div>
</div>
<div  class="col-md-4">

<?php echo $this->i18n->w('book.enter-your-coordinates'); ?> 


<!-- Form Name -->

<form>
<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="name">Nom et prénom</label>  
  <div class="col-md-8">
  <input id="name" name="name" type="text" placeholder="nom et prénom" class="form-control input-md" required>
    
  </div>
</div>

<!-- Prepended checkbox -->
<div class="form-group">
  <label class="col-md-4 control-label" for="tva">Numéro de TVA</label>
  <div class="col-md-8">
    <div class="input-group">
      <span class="input-group-addon">     
          <input type="checkbox">     
      </span>
      <input id="tva" name="tva" class="form-control" type="text" placeholder="numéro de TVA">
    </div>
    <p class="help-block">Si d'application</p>
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="email">Adresse email</label>  
  <div class="col-md-8">
  <input id="email" name="email" type="text" placeholder="email" class="form-control input-md">
    
  </div>
</div>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="phone">Numéro de téléphone</label>  
  <div class="col-md-8">
  <input id="phone" name="phone" type="text" placeholder="téléphone" class="form-control input-md">
    
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="send">Demande d'information</label>
  <div class="col-md-8">
    <button id="send" name="send" class="btn btn-primary">Envoyer</button>
  </div>
</div>

</div>


</div>
</form>
