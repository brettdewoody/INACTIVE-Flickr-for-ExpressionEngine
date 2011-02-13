CKEDITOR.plugins.add('flickr',{
	requires: ['iframedialog'],
	init:function(editor){
		CKEDITOR.dialog.addIframe(
			'flickr_dialog',
			'Flickr',
			Eehive_flickr.browserUrl,
			810,
			620,
			function () {
				// fix for bug in CKEditor where iframe height is not 100%.
				// FYI - this is Prototype.js based code
				// it is basically finding the iframe element and then finding it's ancestor with a class of "cke_dialog_page_content" and setting it's height to 100%.
				$('.cke_dialog_page_contents').css({ 
					height: '100%'
				});
		});
		var cmd = editor.addCommand('flickr', {exec:flickr_onclick});
		cmd.modes = {
			wysiwyg:1,
			source:1
		};
		cmd.canUndo = false;
		editor.ui.addButton('Flickr',
			{
				label: 'Flickr',
				command: 'flickr',
				icon:this.path+"images/flickr.gif"

			});
	}
});

function flickr_onclick(e)
{
	// run when custom button is clicked
	e.openDialog('flickr_dialog');
}
