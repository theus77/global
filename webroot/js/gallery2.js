	

function loadStackImage(elem){
	$('#GalerieCarousel div.active img').attr('src', $(elem).attr('data-stack-uuid'));
	return false;
}

$(function () {
	
	//event to call to load an image
	$('img.lazy').bind('loadImage', function() {
		if($(this).attr('data-src') !== $(this).attr('src')){
			$(this).load(function(){
				//console.log('loaded');
				$(this).parent().find('.carousel-caption').html('');
			})
			$(this).attr('src', $(this).attr('data-src'));
		}
	});

	
	//event to call to load an image
	$('img.lazy-thumb').bind('loadImage', function() {
		if($(this).attr('data-src') !== $(this).attr('src')){
			$(this).attr('src', $(this).attr('data-src'));
		}
	});	
	
	//event to call to load a google map
	$('div.map-div').bind('loadMap', function() {
				
		//console.log($(element));
		if(!$(this).attr('data-map-set')){
			myLatlngmap = new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng'));
			//console.log(myLatlngmap);
			mapOptions = {
					center: myLatlngmap,
					maxZoom: 16,
					minZoom: 7,
					zoom: 12
			};
			map = new google.maps.Map(this, mapOptions);
			new google.maps.Marker({
				position: myLatlngmap,
				map: map
			});	
			$(this).attr('data-map-set', true);
			
			
		/*if($(this).attr('data-src') !== $(this).attr('src')){
			$(this).load(function(){
				//console.log('loaded');
				$(this).parent().find('.carousel-caption').html('');
			})
			$(this).attr('src', $(this).attr('data-src'));*/
		}
		
	});

	
	/*$(window).hashchange( function(){
		// Alerts every time the hash changes!
		console.log( location.hash );
	})/**/

	
	
	
	//disable autoplay for gallery carrousel
	$('#GalerieCarousel').carousel({
    	pause: true,
    	interval: false
    });

	//disable autoplay for info carrousel
    $('#infoCarousel').carousel({
    	pause: true,
    	interval: false
    });

    
    //on slide //keep other carousels sync
	$('#GalerieCarousel').bind('slide.bs.carousel', function (e) {

		var elem = $(e.relatedTarget);
		
		//todo gestion des hash??
		//location.hash = 'slide='+elem.index();
		
		//var direction = e.direction;
		//console.log(direction);
		var currentIdx = elem.index();
		//keep carousels sync
		$('#infoCarousel').carousel( currentIdx );
		/*console.log(direction);
		if(direction === 'left'){
			$('#infoCarousel').carousel( 'next' );
		}
		else {
			$('#infoCarousel').carousel( 'prev' );
		}*/
		
		$(elem).find('img').trigger('loadImage');
		
		

		$('#map-canvas-'+currentIdx).trigger('loadMap');
		// hide the infos block
		$( ".wrapper-info" ).find( ".infos" ).removeClass( "open" );
		
	});   
	
	
});



$(window).load(function() {
	  
	$('#map-canvas-0').trigger('loadMap');
	
	//asynch load of full quality images
	$('img.preview').each(function(index, element){
		setTimeout( function(){ $(element).trigger('loadImage'); }, 1000*index);
	});
	
	
	$('img.lazy-thumb').each(function(index, element){
		setTimeout( function(){ $(element).trigger('loadImage'); }, 100*index);
	});
	
	
	
	
	  
});
	  

	

	