<!-- book -->	
<section id="book" class="section wow fadeInUp">
	<div class="container">	
		<input id="pac-input" class="controls" type="text" placeholder="<?php echo __('Boite de recherche');?>">
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
		     	 var north = <?php echo $mapBB['north']; ?>;
		    	 var south = <?php echo $mapBB['south']; ?>;
		     	 var west = <?php echo $mapBB['west']; ?>;
		    	 var est = <?php echo $mapBB['est']; ?>;	   	 var southwest = new google.maps.LatLng(south, west);
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
						content: '<label class="control-label" for="target-'+counter+'"><?php echo __('Informations à propos de la cible'); ?></label>'
						+'<textarea data-target="input[name=\'data[Booking][target]['+counter+'][comment]\']" onChange="$(this.dataset.target).attr(\'value\', this.value);" class="form-control polygon-info" id="target-'+counter+'"></textarea>'+
						'<a href="#" data-target="'+counter+'"  onclick="removeMarker('+counter+'); '+
						'$(\'input[name=\\\'data[Booking][target]['+counter+'][comment]\\\']\').detach(); '+
						'$(\'input[name=\\\'data[Booking][target]['+counter+'][polygon]\\\']\').detach(); '+
						'return false;"><?php echo __('Effacer ce repère');?></a>'
					});

					$('form#BookingBookForm').append('<input name="data[Booking][target]['+counter+'][comment]" type="hidden" value="empty"/>');

					$('form#BookingBookForm').append('<input name="data[Booking][target]['+counter+'][polygon]" type="hidden" value="'+encodeURIComponent(JSON.stringify(polygon.getPath().getArray()))+'"/>');

					drawingManager.setDrawingMode(null);
					polygon.getPath().getAt(0);
					infowindow.setPosition(polygon.getPath().getAt(0));

		        	google.maps.event.addDomListener(polygon, 'click', function(event) {
		        		infowindow.open(map,polygon);
		        	});
		        	counter += 1;
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
			<div class="col-md-12">
				<div class="simple-box2">
					<div class="content">
						<div class="col-md-7" id="map-ctp">
							<p>
							<?php echo $this->i18n->w('book.enter-your-markers'); ?>
							</p>
							<div class="outer">
	   							<div class="inner" id="map-canvas">
	        						<?php echo __('Connection à Google Map en cours ...'); ?>
	    						</div>
	    						<img src="data:image/gif;base64,R0lGODlhEAAJAIAAAP///wAAACH5BAEAAAAALAAAAAAQAAkAAAIKhI+py+0Po5yUFQA7" class="scaling-image" /> <!-- don't specify height and width so browser resizes it appropriately -->
							</div>
						</div>
						<div  class="col-md-5">
							<?php echo $this->i18n->w('book.enter-your-coordinates'); ?>
							<?php echo $this->element('contact'); ?>
						</div>
						<div class="clearfix"></div>
					</div>							
				</div>
			</div>
		</div>
	</div>
</section>