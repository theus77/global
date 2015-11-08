(function($, viewport){
  var
    $win = $(window),
    $filter = $('.navbar'),
    $filterSpacer = $('<div />', {
      "class": "filter-drop-spacer",
      "height": $filter.outerHeight()
    }),
    $toggleButton = $('#navbar-toggle');

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
		groupToMatch.find( ".text-wrapper p:first-child" ).prepend( $(this) );
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
	 		$(this).parent().next().toggleClass( "open" );
	 })

	// open thumb in galerie
	$( ".trigger-expand" ).on( "click", "a", function(){
		//console.log($(this).parent().prev());
		if($(this).parent().hasClass( "open" )) {
			$(this).parent().removeClass( "open");
			$(this).parent().prev().animate({
		    height: "0"
		  }, 200);
		}
		else {
			$(this).parent().addClass( "open");
			$(this).parent().prev().animate({
			    height: "250"
			  }, 200, function() {
			    
					 // scrollpane
					 $( ".galerie-thumb-scroll" ).jScrollPane(
						{
							horizontalDragMinWidth: 20,
							horizontalDragMaxWidth: 20
						}
					);

			  });
		}
	})
	//  bind scroll to anchor links
	$(document).on("click", "a", function (e) {
		var url = $(this).attr("href");
		var idx = url.indexOf("#")
		var id = idx != -1 ? url.substring(idx) : "";

		if ($(id).length > 0) {
			e.preventDefault();

			// trigger scroll
			$("html, body").animate({ scrollTop: $(id).offset().top - 250 }, 1000);

			// if supported by the browser we can even update the URL.
			if (window.history && window.history.pushState) {
				history.pushState("", document.title, id);
			}
		}
	});

	// bootstrap jquery mediqueries.
	// load vegas slideshow and parallax only if the breakpoint is bigger than 'md'.
	$(window).load(
	 	viewport.changed(function(){

		    if( viewport.is('>=sm') ) {		
		    // Remove navbar-fixed-bottom class when we scroll
				if ( $( "body#homepage" ).length ) {
					// Add fixed effect to navbar top menu
					$( "nav.navbar" ).addClass( "navbar-fixed-bottom" );
					$(window).scroll( function() {
					    var height = $(window).scrollTop();
					    var number = $( "nav.navbar" ).attr( "data-offset-top" );
					    
					    if( height > number ) {
					        $( "nav.navbar" ).removeClass( "navbar-fixed-bottom" );
					    }
					    else {
					    	$( "nav.navbar" ).addClass( "navbar-fixed-bottom" );
					    }
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
				// Search animation.
				$( ".search-trigger" ).on ( "click", function () {
					$( ".search-toggle" ).animate({
					 		width: 'toggle'
					});
				});
			}
			else {
				// scrollpane
					 $( ".galerie-thumb-scroll" ).jScrollPane(
						{
							horizontalDragMinWidth: 20,
							horizontalDragMaxWidth: 20
						}
					);
				if ( $( "body#homepage" ).length ) {
					$( "nav#nav" ).removeAttr( "data-spy data-offset-top" ).removeClass( "navbar-fixed-bottom affix-top" );
				}
			}
		}) // end window.changed
	); // end window.load

    // Execute code each time window size changes
    $(window).resize(
        viewport.changed( function(){
        	//console.log('current resize' + viewport.current());
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
            	$( "#homepage .groupToMatch, .box2 .col-md-4" ).matchHeight( 'remove' );          	
            	// we are under md breakpoints
		    	if ( $( "body#homepage" ).length ) {
		    		$( "#sortableFlights" ).vegas( "destroy" );
		    		$( "nav#nav" ).removeClass( "navbar-fixed-bottom" );
		    		$( "nav#nav" ).removeAttr( "data-spy data-offset-top" ).removeClass( "navbar-fixed-bottom affix-top" );
		    	}
		    	if ( $( ".parallaxParent" ).length ) {   			        	
		        	controller.destroy();
			    	controller = null;
			    	scene1.destroy();
			    	scene2.destroy();
			    	if( $( "body.default" ).length) {
				    	scene3.destroy();
				    	scene4.destroy();
			    	}			    	
		    	}
		    }    
        })
    );



})(jQuery, ResponsiveBootstrapToolkit); // end anonymous function

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
