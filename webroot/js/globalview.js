
$(window).load(function() {
	$("img.lazy").show().lazyload({
	    effect : "fadeIn",
	    threshold : 200
	});
});

(function($){
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
	// Admin collapse galery 
	$( ".admin-galery" ).on( "click", "h2", function() {
		$(this).next( "form" ).toggle();
	});


 	$( "#toggle-sidebar, .close-side" ).on( "click", "a",  function (e) {
	 	e.preventDefault();
	 });

 		// Open thumb in galerie page.
	var childOpened;
	//var oldChildOpened;
	$( "#galerie-thumb-child span[class*='child-']" ).hide();
	$( "a[class*='parent-']" ).on( "click", function() {
		var child = ".child-" + $(this).attr( "data-slide-to" );
		$( "#galerie-thumb-child span[class*='child-']" ).not($(this)).hide().parent().show();
		$( child ).show();
	});

	$( ".trigger-expand" ).on( "click", "a", function(e){
		e.preventDefault();
		if ( typeof childOpened === "undefined" ) {
			// on page load
			$( "#galerie-filmstrip #galerie-thumb-child .galerie-thumb-scroll" ).show();
			childOpened = $( "#galerie-thumb-child span.child-0" );
			$( "#galerie-thumb-bro .parent-0" ).addClass( "active" );
		}
		else {
			// we have a visible child because a parent was clicked
			childOpened = $( "#galerie-thumb-child span[class*='child-']:visible" );
		}
		childOpened.toggle();
		// animate effect to show/hide the expand collapse
		$(this).find( "span" )
			.toggleClass( "glyphicon-chevron-down" )
			.toggleClass( "glyphicon-chevron-up" );
		$(this).parent().prev().animate({
  			height: "toggle",
  			opacity: "toggle"
		});
	});

	// thumbnail add active class
	$( ".thumbnail" ).on( "click", function() {
		$(this).addClass( "active" ).siblings().removeClass( "active");
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

	 /* simple sidebar */
	$( "#galerie-info" ).simplerSidebar({
	    opener: '#toggle-sidebar',
	    sidebar: {
	      align: 'right', //or 'right' - This option can be ignored, the sidebar will automatically align to right.
	      width: 300, //You can ignore this option, the sidebar will automatically size itself to 300px.
	      closingLinks: '.close-sidebar' // If you ignore this option, the plugin will look for all links and this can be buggy. Choose a class for every object inside the sidebar that once clicked will close the sidebar.
	    }
  	});

	// Search animation
	$( ".search-trigger" ).on( "click", function() {
		$( ".search-form" ).slideToggle();
	});

	
	$( ".image-box" ).matchHeight();
	$( "#flights .col-md-4 .box" ).matchHeight();

	//prevent # links from moving to top	
	$('a[href="#"][data-top!=true]').click(function(e){
		e.preventDefault();
	});
	
	
	//	SMOOTH SCROLL
	smoothScroll.init({
		offset: 0
	});
	//	SCROLL
	$(window).scroll(function() {    
		var scroll = $(window).scrollTop();

		if (scroll >= 100) {
			$(".navbar-inverse").addClass("navbar-scroll");
		} else {
			$(".navbar-inverse").removeClass("navbar-scroll");
		}
				
	});
	// onepage active link
	var sections = $('section')
		  , nav = $('nav')
		  , nav_height = nav.outerHeight();
		 
		$(window).on('scroll', function () {
		  var cur_pos = $(this).scrollTop();
		 
		  sections.each(function() {
			var top = $(this).offset().top - nav_height,
				bottom = top + $(this).outerHeight();
		 
			if (cur_pos >= top && cur_pos <= bottom) {
			  nav.find('a').removeClass('active');
			  sections.removeClass('active');
		 
			  $(this).addClass('active');
			  nav.find('a[href="#'+$(this).attr('id')+'"]').addClass('active');
			}
		  });
		});

		//	SLIDER BACKGROUND  (BACKSTRETCH)
		if($('.slider-background').length > 0){
			 $.backstretch([
				  "/img/bg1.jpg"
				, "/img/bg2.jpg"
				, "/img/bg3.jpg"
				, "/img/bg4.jpg"
				, "/img/bg5.jpg"
			  ], {duration: 4000, fade: 1000});
		}
		// menu toggle
		$( ".menu-toggle" ).click(function() {
			 $( ".menu" ).slideToggle( "slow", function() {
				// Animation complete.
			  });
			
		});	

		//scrolltotop Check to see if the window is top if not then display button
		$(window).scroll(function(){
			if ($(this).scrollTop() > 100) {
				$('.scrollToTop').fadeIn();
			} else {
				$('.scrollToTop').fadeOut();
			}
		});
		
		//Click event to scroll to top
		$('.scrollToTop').click(function(){
			$('html, body').animate({scrollTop : 0},800);
			return false;
		});

		$(window).load(function() { // makes sure the whole site is loaded
			$('#status').fadeOut(); // will first fade out the loading animation
			$('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
			$('body').delay(350).css({'overflow':'visible'});
		})

		// BANNER CAROUSEL	
		
	/*
		if($('#slider').length > 0){	
			var owl = $("#slider");
		 
			owl.owlCarousel({			 
				autoplay: true,
    			autoplayHoverPause: true,
    			items: 1,
    			loop: true,
				pagination : true
			});
		  
			// Custom Navigation Events
			$(".next").click(function(){
				owl.trigger('owl.next');
			})
			$(".prev").click(function(){
				owl.trigger('owl.prev');
			})
		}
		*/

		//	WOW
	
	var wow = new WOW(
	  {
		boxClass:     'wow',      // animated element css class (default is wow)
		animateClass: 'animated', // animation css class (default is animated)
		offset:       100,          // distance to the element when triggering the animation (default is 0)
		mobile:       true,       // trigger animations on mobile devices (default is true)
		live:         true,       // act on asynchronously loaded content (default is true)
		callback:     function(box) {
		  // the callback is fired every time an animation is started
		  // the argument that is passed in is the DOM node being animated
		},
		scrollContainer: null // optional scroll container selector, otherwise use window
	  }
	);
	wow.init();

		

})(jQuery); // end anonymous function


	
	// PORTFOLIO FILTER
	/*	
	$(document).ready(function() {
	  $('ul#filter a').click(function() {
		$(this).css('outline','none');
		$('ul#filter .current').removeClass('current');
		$(this).parent().addClass('current');
		
		var filterVal = $(this).text().toLowerCase().replace(' ','-');
		

		
		if(filterVal == 'all') {
		  $('ul#portfolio-filter li.hidden').fadeIn('slow').removeClass('hidden');
		} else {
		  $('ul#portfolio-filter li').each(function() {
							
			if(!$(this).hasClass(filterVal)) {
			  $(this).fadeOut('normal').addClass('hidden');

				
			} else {
			$(this).fadeIn('slow').removeClass('hidden');

			}
		  });
		}
		
		return false;
	  });
	});
	*/


	

	
	
	


