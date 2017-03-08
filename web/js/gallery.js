	$(function() {
		
		$('#GalerieCarousel').bind('slide.bs.carousel', function (e) {
			var elem = e.relatedTarget;
			//console.log($(elem).find('img'));
			$(elem).find('img').trigger('loadImage');
		});

		
	});
	




	
	$(window).load(function() {
		
		$('#GalerieCarousel').bind('slide.bs.carousel', function (e) {
			//console.log(imageInfoUrl);  
			var url = $(e.relatedTarget).attr('data-image-info');
			
			
			
			//console.log($(e.relatedTarget).index());
			$('#infoCarousel').carousel( $(e.relatedTarget).index() );
			
			
			loadImageInfo(url);
			//console.log(slideTo); 
		});
		
		loadImageInfo($( '#GalerieCarousel .item').attr('data-image-info'));
		
		
		
		$('img.toLoad').bind('loadImage', function() {
			if($(this).attr('data-src') !== $(this).attr('src')){
				//console.log($(this).attr('data-src'));
				$(this).attr('src', $(this).attr('data-src'));
			}
		});
		
		$('img.preview').each(function(index, element){
			setTimeout( function(){ $(element).trigger('loadImage'); }, 1000*index);
		});

		$('img.thumb').each(function(index, element){
			setTimeout( function(){ $(element).trigger('loadImage'); }, 100*index);
		});
		
		

	});
	
	function loadStackImage(elem){
		$('#GalerieCarousel div.active img').attr('src', '/img/'+$(elem).attr('data-stack-uuid')+'/preview.png');
		
		return false;
	}
	
	function loadImageInfo(url){
		
		//var url = imageInfoUrl+'/'+uuid+'.json';
		 $('div#galerie-thumb-child > div').html(' ');
		 //$('ul#keywordList').html(' ');
		 //$('ul#detailList').html(' ');
		 //$('ul#placesList').html(' ');
		var jqxhr = $.ajax( url )
		  .done(function(data) {
			  //console.log( data );
			  
			  $.each(data['stack'], function( index, value ) {
				  //console.log( value );
				  $('div#galerie-thumb-child > div').append('<div class=""><a onclick="javascript: return loadStackImage(this);" href="#" class="thumbnail" data-stack-uuid="'+value['Version']['encodedUuid']+'"><img src="/img/'+value['Version']['encodedUuid']+'/thumb.png" class="img-responsive" alt=""></a></div>');
				});

			  /*$.each(data['places'], function( index, value ) {				  
				  $('ul#placesList').append('<li>'+value['link']+'</li>');
				});

			  $.each(data['version']['Keyword'], function( index, value ) {
				  $('ul#keywordList').append('<li>'+value['link']+'</li>');
				});
			  
			  $('#GalerieCarousel div.carousel-caption').html(data['prop']['ObjectName']);
			 
			  $('ul#detailList').html(
					  '<li>Photographe: '+data['prop']['Byline']+'</li>'+
					  '<li>Date: '+data['version']['Version']['formatedDate']+'</li>'+
					  '<li>Dimensions: '+data['prop']['PixelSize']+' pixels</li>'
			  );
			  

			  $('#versionTitle').html(data['prop']['ObjectName']);
			  
			  
				$('#GalerieCarousel').carousel('pause');
				
				

				 $('#map-canvas').html(' ');
				 //console.log(data['version']['Version']['exifLatitude']);
				
				 var myLatlng = new google.maps.LatLng(parseFloat(data['version']['Version']['exifLatitude']), parseFloat(data['version']['Version']['exifLongitude']));
					
				 var mapOptions = {
				          center: myLatlng,//{lat: parseFloat(data['version']['Version']['exifLatitude']), lng: parseFloat(data['version']['Version']['exifLongitude'])},
				          maxZoom: 16,
				          minZoom: 7,
				          zoom: 12
				        };
				var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);/**/
				
				var marker = new google.maps.Marker({
				      position: myLatlng,
				      map: map,
				  });
			  
			  //console.log(map);
			  /**/
		  })
		  .fail(function() {
			  //console.log( "error" );
		  })
		  .always(function() {
			  //console.log( "complete" );
		  });		
	}

	  
	  

	

	