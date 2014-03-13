jQuery(document).ready(function()
{//*****************************************************************************

	if(typeof wp.media !== 'undefined'){
		wp.media.singleImage = 
		{
			currentDomain : '',	// kādā laukā likt bildes id, tiek uzstādīts pie click
			currentSelection : null, // tas, kas tika izveleets
			get: function() {
				console.log('singleImage.get()');
				return 0;
			},

			set: function( selection ) 
			{
				if (selection !== null)
				{
					
					var id = selection.id;
					var thumbnail = selection.attributes.url;
					var dot = thumbnail.lastIndexOf('.');
					thumbnail = thumbnail.substring(0,dot)+'-150x150'+thumbnail.substring(dot);
					console.log(thumbnail);

					console.log('singleImage.set()', selection);
					jQuery('input[name="'+wp.media.singleImage.currentDomain+'"]').val(id);
					jQuery('img.image-field[data-domain="'+wp.media.singleImage.currentDomain+'"]').attr('src', thumbnail);
					jQuery('a.image-field.select[data-domain="'+wp.media.singleImage.currentDomain+'"]').hide();
					jQuery('a.image-field.remove[data-domain="'+wp.media.singleImage.currentDomain+'"]').show();

				}
				else
				{

					jQuery('input[name="'+wp.media.singleImage.currentDomain+'"]').val('');
					jQuery('img.image-field[data-domain="'+wp.media.singleImage.currentDomain+'"]').attr('src', null);
					jQuery('a.image-field.remove[data-domain="'+wp.media.singleImage.currentDomain+'"]').hide();
					jQuery('a.image-field.select[data-domain="'+wp.media.singleImage.currentDomain+'"]').show();
					wp.media.singleImage.currentDomain = '';
					wp.media.singleImage.currentSelection = null;
				}
				
				// var settings = wp.media.view.settings;

				// settings.post.featuredImageId = id;

				// wp.media.post( 'set-post-thumbnail', {
				// 	json:         true,
				// 	post_id:      settings.post.id,
				// 	thumbnail_id: settings.post.featuredImageId,
				// 	_wpnonce:     settings.post.nonce
				// }).done( function( html ) {
				// 	$( '.inside', '#postimagediv' ).html( html );
				// });
			},

			frame: function() {
				if ( this._frame )
					return this._frame;

				this._frame = wp.media({
					state: 'featured-image',
					states: [ new wp.media.controller.FeaturedImage() ]
				});

				this._frame.on( 'toolbar:create:featured-image', function( toolbar ) {
					this.createSelectToolbar( toolbar, {
						text: wp.media.view.l10n.setFeaturedImage
					});
				}, this._frame );

				this._frame.state('featured-image').on( 'select', this.select );
				return this._frame;
			},

			select: function()
			{
				var settings = wp.media.view.settings,
					selection = this.get('selection').single();
				//console.log('singleImage.select()', selection.attributes);
				wp.media.singleImage.set( selection ? selection : null);//selection ? selection.id : -1 );
			},

			init: function() {
				// Open the content media manager to the 'featured image' tab when
				// the post thumbnail is clicked.
				console.log('singleImage.init()');


				jQuery('input.image-field').each(function(i,item)
				{
					var value = jQuery(item).val();
				
					if (value)
					{
						jQuery('a.image-field.remove').show();
						jQuery('a.image-field.select').hide();
					}
					else
					{
						jQuery('a.image-field.remove').hide();
						jQuery('a.image-field.select').show();
					}
				});

				jQuery('a.image-field.select').click(function(event)
				{
					var domain = jQuery(this).attr('data-domain');
					wp.media.singleImage.currentDomain = domain;
					console.log(domain);
					wp.media.singleImage.frame().open();
					return false;
				});

				jQuery('a.image-field.remove').click(function(event)
				{
					var domain = jQuery(this).attr('data-domain');
					var domain = jQuery(this).attr('data-domain');
					wp.media.singleImage.currentDomain = domain;
					wp.media.singleImage.set(null);
				
					return false;
				});

				
			}
		};

		jQuery( wp.media.singleImage.init );
	}


//******************************************************************************
	
	jQuery('.custom-media').click(function(){
		var name = jQuery(this).attr('media-domain');
		var value = jQuery('input[name=\"'+name+'\"]').val();
		
		
		window.last_send_to_editor = window.send_to_editor;
		window.last_wpActiveEditor = wpActiveEditor;
		wpActiveEditor =  name+'galleryCallback';
		window.send_to_editor = function(result)
		{
			console.log(result);
			jQuery('input[name=\"'+name+'\"]').val(result);
			window.send_to_editor = window.last_send_to_editor;
			wpActiveEditor = window.last_wpActiveEditor;
			delete window.last_send_to_editor;
			delete window.last_wpActiveEditor;
		};

		
		if (!value)
			value = '[gallery ids=""]';
		
		var shortcode = wp.shortcode.next( 'gallery', value );
		shortcode = shortcode.shortcode;
		var attachments = wp.media.gallery.attachments(shortcode );
		var	selection = new wp.media.model.Selection( attachments.models, {
					props:    attachments.props.toJSON(),
					multiple: true
				}); 

				selection.gallery = attachments.gallery;

				// Fetch the query's attachments, and then break ties from the
				// query to allow for sorting.
				selection.more().done( function() {
					// Break ties with the query.
					selection.props.set({ query: false });
					selection.unmirror();
					selection.props.unset('orderby');
				});

		wp.media.editor.add(wpActiveEditor,{ state:'gallery-edit', selection:selection});
		wp.media.editor.open(wpActiveEditor);
	});
});