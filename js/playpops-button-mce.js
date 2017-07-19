// JavaScript Document
(function() {
    
	tinymce.PluginManager.add('playpops_button', function( editor, url )
	{
        
		editor.addButton( 'playpops_button', {
            //text: 'My test button',
			title: 'insert youtube video',
            //icon: 'wp_code',
			url:'',
			image : url + '/playpops-icon.png',
            onclick: function() 
			{
    			
				editor.windowManager.open( {
					title: 'Insert Youtube Video',
					body: [{
						type: 'textbox',
						name: 'videourl',
						label: 'Youtube Video Url'
					},
					{
						type: 'textbox',
						name: 'width',
						label: 'Width'
					},
					{
						type: 'textbox',
						name: 'height',
						label: 'Height'
					}],
					onsubmit: function( e ) {
						editor.insertContent( '[playpops width="' + e.data.width + '" height="' + e.data.height + '"]' + e.data.videourl + '[/playpops]');
					}
				});
			}
    	});
	});
})();