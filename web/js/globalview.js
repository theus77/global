
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

	$( "a[class^='collapse'], a[class^='technics']" ).on( "click", function() {
		var collapseToOpen = $(this).attr('class');
		$( ".panel-heading h4 a").removeClass( "in" ).attr( "aria-expanded",  "false" ).addClass( "collapsed" );
		$( "div.panel-collapse").removeClass( "in" ).attr( "aria-expanded",  "false" );
		$( "#" +  collapseToOpen).prev( "div" ).find( "a" ).attr( "aria-expanded",  "true" ).removeClass( "collapsed" );
		$( "#" +  collapseToOpen).addClass( "in" ).attr( "aria-expanded",  "true" );
	}); 

 	$( "#toggle-sidebar, .close-side" ).on( "click", "a",  function (e) {
	 	e.preventDefault();
	 });

 		// Open thumb in galerie page.
	var childOpened;
	//var oldChildOpened;
	$( "div[class*='child-']" ).hide();
	$( "div.child-0" ).show();
	$( "#galerie-thumb-bro .parent-0" ).addClass( "active" );
	$( "a[class*='parent-']" ).on( "click", function() {
		var child = ".child-" + $(this).attr( "data-slide-to" );
		$( "div[class*='child-']" ).not($(this)).hide().parent().show();
		$( child ).show();
	});

	$( ".trigger-expand" ).on( "click", "a", function(e){
		e.preventDefault();
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
		$( "nav.navbar-inverse.navbar-fixed-top" ).toggleClass( "black" );
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



	

	
	
	

