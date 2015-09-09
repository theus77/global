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


  $('.groupToMatch').matchHeight({
	  //target: $('#myCarousel')
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
	
  
});
