$(document).ready(function() {
		
	
	// Apply FancyBox to the Choose Photo button
	openFancyBox();
	
	
	// Apply FancyBox to the images
	$("a.singleImage").fancybox({
		'transition': 'fade',
		'overlayOpacity': 0.75							
	});
	
	
	// Trash existing image selection and startover
	// Uses the links REL attribute to determine which image to trash
	$("div.flickrContainer  a.flickrTrash").live('click',function() {
		
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

	
	function openFancyBox() {
		// Open the Fancybox iframe
		$("a.flickrInput").fancybox({
			'width'		: 783,
			'height'	: 560,
			'type'		: 'iframe',
			'transition': 'fade',
			'overlayOpacity': 0.75,
			'cyclic'	: false,
			'showNavArrows' : false,
			'padding'	: 0,
			'scrolling'	: 'no'
		});
	}
	
	
	function escapejQuery(str) {
		var str = str;
		str1 = str.replace(/\[/g,'0').replace(/\]/g,'0');
		return str1;
	}
	
	
});