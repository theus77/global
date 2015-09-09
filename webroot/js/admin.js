
function inlineEdit(element){
	if(!$(this).attr('contenteditable')){
		console.log('update text...');
		$(this).attr('contenteditable', true);
		editor = CKEDITOR.inline(this);			
		//var updateUrl = $(this).attr('data-update-url');
		editor.on( 'change', onWysiwygChange);
	}
	else{
		//$(this).attr('contenteditable', false);
	}
}


function onWysiwygChange(evt){
	
	updateContent(jQuery( evt.editor.element.$ ).attr('data-update-url'), evt.editor.getData());
    
}

function updateContent(url, data){	
	$.ajax({
		type: "POST",
		url: url,
		data: { content: data }
	}).fail(function() {
		alert( "Impossible de sauver la dernière modification!" );
	});
}

$(function () {	
	$('div.wysiwygContent').dblclick(inlineEdit);
	
	$('div.wysiwyg').each(inlineEdit);
	
	
	$('input.ckeditor').each(function(){
	});
	
	$('input.singleline').on('input', function(){
		//console.log($(this).attr('value'));
		//console.log(this.value);
		updateContent($(this).attr('data-update-url'), this.value);
	});

	
	
	$( "#sortableFlights" ).sortable({
		stop: function( event, ui ) {
			$(this).find('> div').each(function(idx, element){
				data = $(element).data('object');
				data.Flight.weight = idx;
				$.ajax({
					type: "POST",
					url: $(element).data('url'),
					data: { data }
				}).fail(function() {
					alert( "Impossible de sauver la dernière modification!" );
				});  
			  });
		  }
	});
	
    $( "#sortableFlights" ).disableSelection();	
    
	$( "#sortableGallery" ).sortable({
		stop: function( event, ui ) {
			$(this).find('> div').each(function(idx, element){
				data = $(element).data('object');
				data.Gallery.weight = idx;
				$.ajax({
					type: "POST",
					url: $(element).data('url'),
					data: { data }
				}).fail(function() {
					alert( "Impossible de sauver la dernière modification!" );
				});  
			  });
		  }
	});
	
    $( "#sortableGallery" ).disableSelection();
});
