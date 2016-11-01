
$(window).load(function() {
	$("img.lazy").show().lazyload({
	    effect : "fadeIn",
	    threshold : 200
	});
});

$(document).ready(function() {
 	// Nicescroll : https://github.com/inuyaksa/jquery.nicescroll
	// add scrolling to the photos galeries
	var niceScrollConfig = {
		cursorcolor:"#7C7B7B",
		cursorborderradius:0,
		cursorminheight: 32,
		spacebarenabled: true,
		railpadding: { top: 0, right: 0, left: 0, bottom: 0 },
		background: "#000",
		cursorwidth: "15",
		cursorborder: "0",
		touchbehavior: false, // enable cursor-drag scrolling like touch devices in desktop computer
    	hwacceleration: true,
    	grabcursorenabled: true,
    	enabletranslate3d: true,
    	autohidemode: "scroll",
    	smoothscroll: true,
		cursorfixedheight: "50"
    };


	$( ".galerie-thumb-scroll" ).niceScroll(niceScrollConfig);
	$( ".carousel-inner .left-panel" ).niceScroll(niceScrollConfig);
	$( ".carousel-inner .right-panel" ).niceScroll(niceScrollConfig);
	
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

	$( "a[class^='technics']" ).on( "click", function() {
		
		var collapseToOpen = $(this).attr('class');
		$( ".panel-heading h4 a").removeClass( "in" ).attr( "aria-expanded",  "false" ).addClass( "collapsed" );
		$( "div.panel-collapse").removeClass( "in" ).attr( "aria-expanded",  "false" );
		$( "#" +  collapseToOpen).prev( "div" ).find( "a" ).attr( "aria-expanded",  "true" ).removeClass( "collapsed" );
		$( "#" +  collapseToOpen).addClass( "in" ).attr( "aria-expanded",  "true" );
		
	}); 


	// scrollto opened accordion after collapse is fired
	$('#accordion').on('shown.bs.collapse', function () {

	  var panel = $(this).find('.in');
	  $('html, body').animate({
	        scrollTop: panel.offset().top + (-200)
	  }, 500);
	});



	function split( val ) {
	 	return val.split( /,\s*/ );
   	}
   	function extractLast( term ) {
	 	return split( term ).pop();
   	}	

//   $( ".QuickSearchInput" )
//	 // don't navigate away from the field on tab when selecting an item
//	 .bind( "keydown", function( event ) {
//	   if ( event.keyCode === $.ui.keyCode.TAB &&
//		   $( this ).autocomplete( "instance" ).menu.active ) {
//		 event.preventDefault();
//	   }
//	 })
//	 .autocomplete({
//	   source: function( request, response ) {
//		 $.getJSON( $(".QuickSearchInput").data("search-url"), {
//		   q: extractLast( request.term )
//		 }, response );
//	   },
//	   search: function() {
//		 // custom minLength
//		 var term = extractLast( this.value );
//		 if ( term.length < 2 ) {
//		   return false;
//		 }
//	   },
//	   focus: function() {
//		 // prevent value inserted on focus
//		 return false;
//	   },
//	   select: function( event, ui ) {
//		 var terms = split( this.value );
//		 // remove the current input
//		 terms.pop();
//		 // add the selected item
//		 terms.push( ui.item.value );
//		 // add placeholder to get the comma-and-space at the end
//		 terms.push( "" );
//		 this.value = terms.join( ", " );
//		 return false;
//	   }
//	 });

	 /* simple sidebar */
	 /*
	$( "#galerie-info" ).simplerSidebar({
	    opener: '#toggle-sidebar',
	    sidebar: {
	      align: 'right', //or 'right' - This option can be ignored, the sidebar will automatically align to right.
	      width: 300, //You can ignore this option, the sidebar will automatically size itself to 300px.
	      closingLinks: '.close-sidebar' // If you ignore this option, the plugin will look for all links and this can be buggy. Choose a class for every object inside the sidebar that once clicked will close the sidebar.
	    }
  	});
  	*/

	// Search animation
	$( ".search-trigger" ).on( "click", function() {
		$( ".search-form" ).slideToggle();
		$( "nav.navbar-inverse.navbar-fixed-top" ).toggleClass( "black" );
	});

	// same height for left-panel, right-panel and middle-panel
	if ( $( ".carousel-inner").length >= 1) {

    	$( ".carousel-inner .item.active div[class*='col-xs-']").height($( ".carousel-inner .item.active .middle-panel img" ).height());
		
		$(window).resize(function(){
			$( ".carousel-inner .item.active div[class*='col-xs-']").height($( ".carousel-inner .item.active .middle-panel img" ).height());
		});

		$(document).ajaxComplete(function() {
			console.log('ajax' + $( ".carousel-inner .item.active .middle-panel img" ).height() );
			$( ".carousel-inner .item.active div[class*='col-xs-']").height($( ".carousel-inner .item.active .middle-panel img" ).height());
		});

	}

	$("label img").on("click", function() {
        $(this).prev( "input" ).prop( "checked", true);
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
				  "/img/bg_01.jpg"
				, "/img/bg_02.jpg"
				, "/img/bg_11.jpg"
				, "/img/bg_04.jpg"
				, "/img/bg_05.jpg"
				, "/img/bg_12.jpg"
				, "/img/bg_06.jpg"
				, "/img/bg_07.jpg"
				, "/img/bg_08.jpg"
				, "/img/bg_09.jpg"
				, "/img/bg_10.jpg"
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



	

	
	
	


