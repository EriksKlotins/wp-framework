
window.getAPFCustomMediaUploaderSelectObject = function()
{
	return wp.media.view.MediaFrame.Select.extend({

		initialize: function() {
			wp.media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				multiple:  true,
				editing:   false,
				state:    'insert'
			});

			this.createSelection();
			this.createStates();
			this.bindHandlers();
			this.createIframeStates();
		},

		createStates: function() {
			var options = this.options;

			// Add the default states.
			this.states.add([
				// Main states.
				new wp.media.controller.Library({
					id:         'insert',
					title:      'Insert Media',
					priority:   20,
					toolbar:    'main-insert',
					filterable: 'image',
					library:    wp.media.query( options.library ),
					multiple:   options.multiple ? 'reset' : false,
					editable:   true,

					// If the user isn't allowed to edit fields,
					// can they still edit it locally?
					allowLocalEdits: true,

					// Show the attachment display settings.
					displaySettings: true,
					// Update user settings when users adjust the
					// attachment display settings.
					displayUserSettings: true
				}),

				// Embed states.
				new wp.media.controller.Embed(),
			]);


			if ( wp.media.view.settings.post.featuredImageId ) {
				this.states.add( new wp.media.controller.FeaturedImage() );
			}
		},

		bindHandlers: function() {
			// from Select
			this.on( 'router:create:browse', this.createRouter, this );
			this.on( 'router:render:browse', this.browseRouter, this );
			this.on( 'content:create:browse', this.browseContent, this );
			this.on( 'content:render:upload', this.uploadContent, this );
			this.on( 'toolbar:create:select', this.createSelectToolbar, this );
			//

			this.on( 'menu:create:gallery', this.createMenu, this );
			this.on( 'toolbar:create:main-insert', this.createToolbar, this );
			this.on( 'toolbar:create:main-gallery', this.createToolbar, this );
			this.on( 'toolbar:create:featured-image', this.featuredImageToolbar, this );
			this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

			var handlers = {
					menu: {
						'default': 'mainMenu'
					},

					content: {
						'embed':          'embedContent',
						'edit-selection': 'editSelectionContent'
					},

					toolbar: {
						'main-insert':      'mainInsertToolbar'
					}
				};

			_.each( handlers, function( regionHandlers, region ) {
				_.each( regionHandlers, function( callback, handler ) {
					this.on( region + ':render:' + handler, this[ callback ], this );
				}, this );
			}, this );
		},

		// Menus
		mainMenu: function( view ) {
			view.set({
				'library-separator': new wp.media.View({
					className: 'separator',
					priority: 100
				})
			});
		},

		// Content
		embedContent: function() {
			var view = new wp.media.view.Embed({
				controller: this,
				model:      this.state()
			}).render();

			this.content.set( view );
			view.url.focus();
		},

		editSelectionContent: function() {
			var state = this.state(),
				selection = state.get('selection'),
				view;

			view = new wp.media.view.AttachmentsBrowser({
				controller: this,
				collection: selection,
				selection:  selection,
				model:      state,
				sortable:   true,
				search:     false,
				dragInfo:   true,

				AttachmentView: wp.media.view.Attachment.EditSelection
			}).render();

			view.toolbar.set( 'backToLibrary', {
				text:     'Return to Library',
				priority: -100,

				click: function() {
					this.controller.content.mode('browse');
				}
			});

			// Browse our library of attachments.
			this.content.set( view );
		},

		// Toolbars
		selectionStatusToolbar: function( view ) {
			var editable = this.state().get('editable');

			view.set( 'selection', new wp.media.view.Selection({
				controller: this,
				collection: this.state().get('selection'),
				priority:   -40,

				// If the selection is editable, pass the callback to
				// switch the content mode.
				editable: editable && function() {
					this.controller.content.mode('edit-selection');
				}
			}).render() );
		},

		mainInsertToolbar: function( view ) {
			var controller = this;

			this.selectionStatusToolbar( view );

			view.set( 'insert', {
				style:    'primary',
				priority: 80,
				text:     'Select Image',
				requires: { selection: true },

				click: function() {
					var state = controller.state(),
						selection = state.get('selection');

					controller.close();
					state.trigger( 'insert', selection ).reset();
				}
			});
		},

		featuredImageToolbar: function( toolbar ) {
			this.createSelectToolbar( toolbar, {
				text:  'Set Featured Image',
				state: this.options.state || 'upload'
			});
		},

		mainEmbedToolbar: function( toolbar ) {
			toolbar.view = new wp.media.view.Toolbar.Embed({
				controller: this,
				text: 'Insert Image'
			});
		}		
	});
}


jQuery( document ).ready( function(){

				// Global Function Literal 
				setAPFImageUploader = function( strInputID, fMultiple, fExternalSource, strTitle ) 
				{

					/*
						Set up remove link
					 */
					jQuery( '#select_image_' + strInputID+'_remove' ).click(function()
					{
						jQuery( '#image_preview_container_' + strInputID ).hide();	
						jQuery( '#select_image_' + strInputID+'_remove' ).hide();
						jQuery( 'input#' + strInputID ).val('');
						jQuery( '#select_image_' + strInputID ).show();
						return false;
					});

					/*
						Display correct link depending on field value
					 */
				//	console.log("jQuery( '#select_image_' + strInputID+'_remove' ).val()",jQuery( '#select_image_' + strInputID+'_remove' ).val());
					if (jQuery( 'input#' + strInputID).val()=='')
					{
						console.log('et');
						jQuery( '#select_image_' + strInputID ).show();
						jQuery( '#select_image_' + strInputID +'_remove').hide();
					}
					else
					{
						jQuery( '#select_image_' + strInputID ).hide();
						jQuery( '#select_image_' + strInputID +'_remove').show();
					}




					jQuery( '#select_image_' + strInputID ).unbind( 'click' );	// for repeatable fields
					jQuery( '#select_image_' + strInputID ).click( function( e ) {
						
						window.wpActiveEditor = null;						
						e.preventDefault();
						
						// If the uploader object has already been created, reopen the dialog
						if ( custom_uploader ) {
							custom_uploader.open();
							return;
						}					
						
						// Store the original select object in a global variable
						oAPFOriginalImageUploaderSelectObject = wp.media.view.MediaFrame.Select;
						
						// Assign a custom select object.
						wp.media.view.MediaFrame.Select = fExternalSource ? getAPFCustomMediaUploaderSelectObject() : oAPFOriginalImageUploaderSelectObject;
						//console.log(strTitle);
						var custom_uploader = wp.media({
							title: strTitle,
							button: {
								text: 'Apstiprināt'
							},
							library     : { type : 'image' },
							multiple: fMultiple  // Set this to true to allow multiple files to be selected
						});
			
						// When the uploader window closes, 
						custom_uploader.on( 'close', function() {

							var state = custom_uploader.state();
							
							// Check if it's an external URL
							if ( typeof( state.props ) != 'undefined' && typeof( state.props.attributes ) != 'undefined' ) 
								var image = state.props.attributes;	
							
							// If the image variable is not defined at this point, it's an attachment, not an external URL.
							if ( typeof( image ) !== 'undefined'  ) {
								setPreviewElement( strInputID, image );
							} else {
								
								var selection = custom_uploader.state().get( 'selection' );
								selection.each( function( attachment, index ) {
									attachment = attachment.toJSON();
									if( index == 0 ){	
										// place first attachment in field
										setPreviewElement( strInputID, attachment );
									} else{
										
										var field_container = jQuery( '#' + strInputID ).closest( '.admin-page-framework-field' );
										var new_field = addAPFRepeatableField( field_container.attr( 'id' ) );
										var strInputIDOfNewField = new_field.find( 'input' ).attr( 'id' );
										setPreviewElement( strInputIDOfNewField, attachment );
			
									}
								});				
								
							}
							
							// Restore the original select object.
							wp.media.view.MediaFrame.Select = oAPFOriginalImageUploaderSelectObject;
											
						});
						
						// Open the uploader dialog
						custom_uploader.open();											
						return false;       
					});	
				
					var setPreviewElement = function( strInputID, image ) {

						// Escape the strings of some of the attributes.
						var strCaption = jQuery( '<div/>' ).text( image.caption ).html();
						var strAlt = jQuery( '<div/>' ).text( image.alt ).html();
						var strTitle = jQuery( '<div/>' ).text( image.title ).html();
						
						// If the user want the attributes to be saved, set them in the input tags.
						jQuery( 'input#' + strInputID ).val( image.url );		// the url field is mandatory so it does not have the suffix.
						jQuery( 'input#' + strInputID + '_id' ).val( image.id );
						jQuery( 'input#' + strInputID + '_width' ).val( image.width );
						jQuery( 'input#' + strInputID + '_height' ).val( image.height );
						jQuery( 'input#' + strInputID + '_caption' ).val( strCaption );
						jQuery( 'input#' + strInputID + '_alt' ).val( strAlt );
						jQuery( 'input#' + strInputID + '_title' ).val( strTitle );
						jQuery( 'input#' + strInputID + '_align' ).val( image.align );
						jQuery( 'input#' + strInputID + '_link' ).val( image.link );
						
						// Update up the preview
						jQuery( '#image_preview_' + strInputID ).attr( 'data-id', image.id );
						jQuery( '#image_preview_' + strInputID ).attr( 'data-width', image.width );
						jQuery( '#image_preview_' + strInputID ).attr( 'data-height', image.height );
						jQuery( '#image_preview_' + strInputID ).attr( 'data-caption', strCaption );
						jQuery( '#image_preview_' + strInputID ).attr( 'alt', strAlt );
						jQuery( '#image_preview_' + strInputID ).attr( 'title', strTitle );
						jQuery( '#image_preview_' + strInputID ).attr( 'src', image.url );
						jQuery( '#image_preview_container_' + strInputID ).show();				
						jQuery( '#select_image_' + strInputID+'_remove' ).hide();
					}
				}		
			});