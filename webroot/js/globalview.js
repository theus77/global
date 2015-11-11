// Parallax Homepage ScrollMagic
// https://github.com/janpaepke/ScrollMagic
function parallax() {	
	// init controller
	var controller = new ScrollMagic.Controller({globalSceneOptions: {triggerHook: "onEnter", duration: "200%"}});
	// build scenes
	var scene1 = new ScrollMagic.Scene({triggerElement: "#parallax1"})
					.setTween("#parallax1 > div", {y: "80%", ease: Linear.easeNone})
					.addTo(controller);

	var scene2 = new ScrollMagic.Scene({triggerElement: "#parallax2"})
					.setTween("#parallax2 > div", {y: "80%", ease: Linear.easeNone})
					.addTo(controller);

	if( $( "body.default" ).length) { 
		var scene3 = new ScrollMagic.Scene({triggerElement: "#parallax3"})
						.setTween("#parallax3 > div", {y: "80%", ease: Linear.easeNone})
						.addTo(controller);
		var scene4 = new ScrollMagic.Scene({triggerElement: "#parallax4"})
						.setTween("#parallax4 > div", {y: "80%", ease: Linear.easeNone})
						.addTo(controller);
	}		
 }
// vegas slideshow
// https://github.com/jaysalvat/vegas
function vegas() {

	var h = window.innerHeight;
	$( "#sortableFlights" ).vegas({
	    slides: [
	        { src: "img/slideshow/beautiful-01.jpg" },
	        { src: "img/slideshow/beautiful-02.jpg" },
	        { src: "img/slideshow/beautiful-03.jpg" },
	        { src: "img/slideshow/beautiful-04.jpg" }
	    ],
	    init: function (globalSettings) {
	        $( ".vegas-container" ).css('height', h + 'px');
	    }
	});

}


(function($, viewport){
  var
    $win 			= $(window),
    $filter 		= $('.navbar'),
    $filterSpacer 	= $('<div />', {
      "class": "filter-drop-spacer",
      "height": $filter.outerHeight()
    }),
    $toggleButton 	= $('#navbar-toggle');

	// Add active class on menu
	var url = location.pathname + window.location.hash;
	if (url != '/' ) {
		// console.log(url);
		$( 'nav .nav a[href="' + url + '"]' ).parents( "li" ).addClass( "active" );
		$( "nav .nav a" ).on("click", function() {
			$(this).parents( "li" ).addClass( "active" ).siblings().removeClass( "active");
		})
	}

	// Replace the h2 at the good place for design.
	var boxeTitle = $( ".groupToMatch" ).find( "h2" ); 
	boxeTitle.each( function() {
		var groupToMatch = $(this).parent( ".groupToMatch" );
		groupToMatch.find( ".text-wrapper p:first-child" ).wrap( "<div class='heading-wrapper' />");
		groupToMatch.find( ".heading-wrapper" ).prepend( $(this) );
	});


	function split( val ) {
	 	return val.split( /,\s*/ );
   	}
   	function extractLast( term ) {
	 	return split( term ).pop();
   	}	

   $( ".QuickSearchInput" )
	 // don't navigate away from the field on tab when selecting an item
	 .bind( "keydown", function( event ) {
	   if ( event.keyCode === $.ui.keyCode.TAB &&
		   $( this ).autocomplete( "instance" ).menu.active ) {
		 event.preventDefault();
	   }
	 })
	 .autocomplete({
	   source: function( request, response ) {
		 $.getJSON( $(".QuickSearchInput").data("search-url"), {
		   q: extractLast( request.term )
		 }, response );
	   },
	   search: function() {
		 // custom minLength
		 var term = extractLast( this.value );
		 if ( term.length < 2 ) {
		   return false;
		 }
	   },
	   focus: function() {
		 // prevent value inserted on focus
		 return false;
	   },
	   select: function( event, ui ) {
		 var terms = split( this.value );
		 // remove the current input
		 terms.pop();
		 // add the selected item
		 terms.push( ui.item.value );
		 // add placeholder to get the comma-and-space at the end
		 terms.push( "" );
		 this.value = terms.join( ", " );
		 return false;
	   }
	 });


	// show/hide infos.
	$( ".wrapper-info" ).find( "span" ).on( "click", function() {
		$(this).toggleClass( "open" );
		// smoth scroll
		var offsetTop 	= $( "#infoCarousel" ).offset().top;
		$(this).parent().next().toggleClass( "open" );
		var trigger = $( "#galerie-thumb-bro .trigger-expand" ).find( "span" );
		// hide opened galeries scroll
		if ( trigger.hasClass( "glyphicon-chevron-up" ) ) {
			console.log('visible');
			$( ".trigger-expand" ).find( "span" )
				.toggleClass( "glyphicon-chevron-down" )
				.toggleClass( "glyphicon-chevron-up" );
			$( ".galerie-thumb-scroll" ).animate({
					height: "toggle",
					opacity: "toggle"
			});
		}
		// center on the page
		var heightWindow = $(window).height();
		var topElement = (heightWindow - $( ".galerie-info" ).height()) / 2 + $(window).scrollTop();
		$( ".galerie-info" ).css({position: 'absolute', top: topElement});
		// google map refresh ... googlemap doesn't like show/hide
		var center = map.getCenter();
        google.maps.event.trigger(map, 'resize');
        map.setCenter(center);

	});

	$( ".wrapper-info .infos" ).find( "h3" ).on( "click", function() {
		$(this).toggleClass( "open" );
		$(this).next( "ul" ).toggle();
	});

	// Nicescroll : https://github.com/inuyaksa/jquery.nicescroll
	// add scrolling to the photos galeries
	$( ".galerie-thumb-scroll" ).niceScroll({
		cursorcolor:"#7C7B7B",
		cursorborderradius:0,
		cursorwidth: "15",
		cursorborder: "1px solid #ddd",
		cursorfixedheight: "100",
		background: "transparent"
	});

	// Open thumb in galerie page.
	$( ".trigger-expand" ).on( "click", "a", function(e){
		e.preventDefault();
		$(this).find( "span" )
			.toggleClass( "glyphicon-chevron-down" )
			.toggleClass( "glyphicon-chevron-up" );
		$(this).parent().prev().animate({
  			height: "toggle",
  			opacity: "toggle"
		});
	})

	// thumbnail add active class
	$( ".thumbnail" ).on( "click", function() {
		$(this).addClass( "active" ).siblings().removeClass( "active");
	})

	// if we have a hash on load. Scroll to the id
	$(window).load(function () {
		if ( window.location.hash ) {
			// scroll
			var id = window.location.hash;
			$("html, body").animate({ scrollTop: $(id).offset().top - 150}, 1000);
		}
	});
	//  Bind scroll to anchor links.
	$(document).on("click", "a", function (e) {
		var url = $(this).attr("href");
		var idx = url.indexOf("#")
		var id 	= idx != -1 ? url.substring(idx) : "";

		if ($(id).length > 0) {
			e.preventDefault();
			console.log('top : ' +$(id).offset().top);
			// trigger scroll
			$("html, body").animate({ scrollTop: $(id).offset().top - 150}, 1000);

			// if supported by the browser we can even update the URL.
			if (window.history && window.history.pushState) {
				history.pushState("", document.title, id);
			}
		}
	});

	// Search animation.
	$( ".search-trigger" ).on ( "click", function () {
		$( ".search-toggle" ).animate({
		 		width: 'toggle'
		});
	});

	// Bootstrap jquery mediaqueries.
	// Load vegas slideshow and parallax only if the breakpoint is bigger than 'md'.
	$(window).load(
	 	viewport.changed(function(){
	 		//console.log('1 current resize : ' + viewport.current());
		    if( viewport.is('>=sm') ) {	
		    	// Use affix fonctionality only on homepage
				$('#nav').affix({offset: {top: 300} });
		    	if ( $( "body#homepage" ).length ) {
		    		$('#nav').addClass( "navbar-fixed-bottom" );
				    $("#nav").on('affix.bs.affix', function(){
				    	// I need to remove these class for dropdown menu
				    	// after affix is fired
				        $(this).toggleClass( "navbar-fixed-bottom" );
				    });
				}
				// Same height for home boxes.
				$( "#homepage .groupToMatch, .box2 .col-md-4" ).matchHeight();
				// Parallax Homepage ScrollMagic - https://github.com/janpaepke/ScrollMagic
	     		if ( $( ".parallaxParent" ).length ) {
					parallax();
				}
				if ( $( "body#homepage" ).length ) {
					vegas();
				}

			}

			if( viewport.is('<lg') ) {
				if ( $( "body#homepage" ).length ) {
					// destroy affix for mobile
					$(window).off('.affix')
					$( "nav#nav" ).removeData('bs.affix').removeClass('affix affix-top affix-bottom')
					$( "nav#nav" ).removeClass( "navbar-fixed-bottom" );
				}
			}
		}) // end window.changed
	); // end window.load

    // Execute code each time window size changes
    $(window).resize(
        viewport.changed( function(){
        	//console.log('2 current resize :' + viewport.current());
            if( viewport.is('>=sm') ) {
				// Same height for home boxes.
				$( "#homepage .groupToMatch, .box2 .col-md-4" ).matchHeight();
				// Parallax Homepage ScrollMagic - https://github.com/janpaepke/ScrollMagic
                if ( $( ".parallaxParent" ).length ) {
					parallax();
				}
				if ( $( "body#homepage" ).length ) {
					vegas();
				}            
            }
            else {
            	//console.log('3 current resize :' + viewport.current());
            	$( "#homepage .groupToMatch, .box2 .col-md-4" ).matchHeight( 'remove' );          	
            	// we are under md breakpoints
		    	if ( $( "body#homepage" ).length ) {
		    		$( "#sortableFlights" ).vegas( "destroy" );
		    	}
		    	if ( $( ".parallaxParent" ).length ) {   			        	
		        	//controller.destroy();
			    	controller = null;
			    	if ( $( "body#homepage" ).length ) {
				    	scene1.destroy();
				    	scene2.destroy();
			    	}
			    	if( $( "body.default" ).length) {
				    	scene3.destroy();
				    	scene4.destroy();
			    	}			    	
		    	}
		    }  
		    if( viewport.is('<lg') ) {
				if ( $( "body#homepage" ).length ) {
					// destroy affix for mobile
					$(window).off('.affix')
					$( "nav#nav" ).removeData('bs.affix').removeClass('affix affix-top affix-bottom')
					$( "nav#nav" ).removeClass( "navbar-fixed-bottom" );
				}
			}
        }) // end window.changed
    ); // end window.resize

})(jQuery, ResponsiveBootstrapToolkit); // end anonymous function

