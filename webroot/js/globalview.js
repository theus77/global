$(function(){
  var
    $win = $(window),
    $filter = $('.navbar'),
    $filterSpacer = $('<div />', {
      "class": "filter-drop-spacer",
      "height": $filter.outerHeight()
    }),
    $toggleButton = $('#navbar-toggle');
  

  var $currentMarginTop = 0;
  
  var $handleNavPosition = (function(){  
	if(!$filter.hasClass('navbar-fixed-top') && ($win.scrollTop() > $filter.offset().top)){
      $filter.before($filterSpacer);
      $currentMarginTop = $filter.css("margin-top");
      $filter.css("margin-top", 0);
      $filter.addClass("navbar-fixed-top");
    } else if ($filter.hasClass('navbar-fixed-top') && $win.scrollTop() < $filterSpacer.offset().top){
      $filter.css("margin-top", $currentMarginTop);
      $filter.removeClass("navbar-fixed-top");
      $filterSpacer.remove();
    }
  });

// Same height for for home boxes.
$('.groupToMatch').matchHeight();



// Center our flights informations boxes on the home.
/*
$(window).load( function() {
	var flightWrapperTop = (h - $('.groupToMatch').height()) / 3;
	$( ".flight-wrapper" ).css('margin-top', flightWrapperTop + 'px');
})
*/

// Replace the h2 at the good place for design.
var boxeTitle = $( ".groupToMatch" ).find( "h2" ); 
boxeTitle.each( function() {
	var groupToMatch = $(this).parent( ".groupToMatch" );
	groupToMatch.find( ".text-wrapper p:first-child" ).prepend( $(this) );
});


/* smooth scrolling for nav sections */
$('#nav .navbar-nav li>a').click(function(){
  var link = $(this).attr('href');
  var posi = $(link).offset().top;
  $('body,html').animate({scrollTop:posi},700);
});

 var $handleResize = (function(){
   if($('#navbar-toggle:visible').length){
   		$filterSpacer.detach();
		$filter.css("margin-top", 0);
		$filterSpacer.prependTo("body");
        $filter.addClass("navbar-fixed-top");   	
   }
   else{
   	   var $diff = $win.height() - $('#BeforeTheNav').offset()['top'] - $('.navbar').height();
   	   $filterSpacer.detach();
   	   $filter.removeClass("navbar-fixed-top");
   	   if($diff > 0){
   	   	 $filter.css("margin-top", $diff+"px");
   	   }
   	   else {
   	   	$filter.css("margin-top", 0);
   	   }
   	   $handleNavPosition();
   }

   //$('#sortableFlights').css("height", "auto");
   var $diff = $win.height() - $('#BeforeTheNav').offset()['top'] - $('.navbar').height() ;
   //if($diff != $currentMarginTop){
	//$currentMarginTop = $diff;
	//$handleNavPosition();
	/*if($diff < 0){

	}
	else{
		$currentMarginTop = $diff;
		$filter.css("margin-top", $diff+"px");
		$filter.removeClass("navbar-fixed-top");
		$filterSpacer.remove();
	}   */	
  // }


  
 });


  $win.scroll($handleNavPosition);
  if($('#sortableFlights').length){
  	$win.resize($handleResize);
	$handleResize();
  }	
	
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
}); // end anonymous function



// Wrap IIFE around your code
(function($, viewport){

    // Executes only in XS breakpoint
    if( viewport.is('xs') ) {
        // mobile
        console.log('small');
    }

    // Executes in SM, MD and LG breakpoints
    if( viewport.is('>=sm') ) {
        // bigger than mobile
        console.log('small2');
        var h = window.innerHeight;
		$( "#sortableFlights" ).vegas({
		    slides: [
		        { src: "img/beautiful-01.jpg" },
		        { src: "img/beautiful-02.jpg" },
		        { src: "img/beautiful-03.jpg" },
		        { src: "img/beautiful-04.jpg" }
		    ],
		    init: function (globalSettings) {
		        $( ".vegas-container" ).css('height', h + 'px');
		    }
		});

		// Parallax Homepage ScrollMagic - https://github.com/janpaepke/ScrollMagic
		// init controller
		var controller = new ScrollMagic.Controller({globalSceneOptions: {triggerHook: "onEnter", duration: "200%"}});

		// build scenes
		new ScrollMagic.Scene({triggerElement: "#parallax1"})
						.setTween("#parallax1 > div", {y: "80%", ease: Linear.easeNone})
						.addTo(controller);

		new ScrollMagic.Scene({triggerElement: "#parallax2"})
						.setTween("#parallax2 > div", {y: "80%", ease: Linear.easeNone})
						.addTo(controller);

						
		// change behaviour of controller to animate scroll instead of jump
		controller.scrollTo(function (newpos) {
			TweenMax.to(window, 0.5, {scrollTo: {y: newpos}});
		});

		//  bind scroll to anchor links
		$(document).on("click", "a[href^='#']", function (e) {
			var id = $(this).attr("href");
			if ($(id).length > 0) {
				e.preventDefault();

				// trigger scroll
				controller.scrollTo(id);

					// if supported by the browser we can even update the URL.
				if (window.history && window.history.pushState) {
					history.pushState("", document.title, id);
				}
			}
		});

		$(".search-trigger").click(function () {
    		$(".search-toggle").animate({
       	 	width: 'toggle'
    	});
});
    }

    // Executes in XS and SM breakpoints
    if( viewport.is('<md') ) {
        // ...
    }

    // Execute code each time window size changes
    $(window).resize(
        viewport.changed(function(){
            if( viewport.is('xs') ) {
                // ...
            }
        })
    );

})(jQuery, ResponsiveBootstrapToolkit);