CKEDITOR.dialog.add("flickr", function(editor) {
	
	return {
		title : 'Flickr',
		minWidth : 810,
		minHeight : 620,
		onOk: function() {
			//alert('onOk');
		},
		onLoad: function() {
//			return false;
			//alert('onLoad');
		},
		onShow: function() {
//			return false;
			//alert('onShow');
		},
		resizable: 'none',
		contents: [{
		        id: 'page1',  /* not CSS ID attribute! */
		        label: 'Page1',
		        accessKey: 'F',
		        elements: [{
	                type: 'html',
	                html: '<iframe src="' + Eehive_flickr.browserUrl + '" width="783" height="560">'
				}]
		    }]/*content definition, basically the UI of the dialog*/
	};
});
