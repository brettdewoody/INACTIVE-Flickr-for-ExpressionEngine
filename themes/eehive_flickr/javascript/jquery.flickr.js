$(document).ready(function() {
	
	// Apply FancyBox to thumbnail images
	$("a.singleImage").live('click', function(e) {
		e.preventDefault();

		$.fancybox({
			'type' : 'image',
			'href' : $(this).attr('href'),
			'transition': 'fade',
			'overlayOpacity': 0.75							
		});
	});
	
	
	// Trash existing image selection and startover
	// Uses the links REL attribute to determine which image to trash
	$("div.flickrContainer a.flickrTrash").live('click',function(e) {
		e.preventDefault();
		
		// Get the REL attribute from the trash link
		var field_name = escapejQuery($(this).attr('rel'));
		
		// Get the parent container of the trash link
		var parentClass = $(this).parents('.flickrContainer').attr('id');
		//alert(parentClass);
		
		// Remove the VALUE from the field input
		$("input#" + field_name).attr('value','');
		
		// Hide the image chooser
		$("div#" + field_name + "_image").css("display","none");
				
		// Display the image thumbnail and URL
		$("div#" + field_name + "_chooser").css("display","block");
		
	});

	// live bind fancybox so that new matrix rows work
	$('a.flickrInput').live('click', function(e) {
		e.preventDefault();
		
		$.fancybox({
			'width'		: 780,
			'height'	: 620,
			'type'		: 'iframe',
			'href'		: $(this).attr('href'),
			'transition': 'fade',
			'overlayOpacity': 0.75,
			'cyclic'	: false,
			'showNavArrows' : false,
			'padding'	: 0,
			'scrolling'	: 'no'
		});
	});

	// rather than replace with zero, simply escape them with double slashes
	function escapejQuery(str) {
		return str.replace(/\[/g,'\\[').replace(/\]/g,'\\]');
	}
	
});