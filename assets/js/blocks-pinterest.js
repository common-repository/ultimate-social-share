//pinterest image block attributes
(function() {

	//setup constants
	const el = wp.element.createElement;
	const { __ } = wp.i18n;
	
	//register block attributes for existing image block
	function ultimatesocialshareRegisterImageBlockAttributes(settings, name) {

		//disregard if not an image block
		if(name !== 'core/image') {
			return settings;
		}

		//add pinterest attributes to existing attributes
		settings.attributes = Object.assign(settings.attributes, {
			ultimatesocialsharePinTitle : {
				attribute : 'data-pin-title',
				type 	  : 'string',
				selector  : 'img',
				source    : 'attribute',
				default   : ''
			},
			ultimatesocialsharePinDescription : {
				attribute : 'data-pin-description',
				type 	  : 'string',
				selector  : 'img',
				source    : 'attribute',
				default   : ''
			},
			ultimatesocialsharePinRepinID : {
				attribute : 'data-pin-id',
				type 	  : 'string',
				selector  : 'img',
				source	  : 'attribute',
				default   : ''
			},
			ultimatesocialsharePinNoPin : {
				attribute : 'data-pin-nopin',
				type  	  : 'boolean',
				selector  : 'img',
				source    : 'attribute',
				default   : ''
			}
		});

		//extend raw <img> HTML transformation
		settings.transforms.from[0] = lodash.merge(settings.transforms.from[0], {
			schema: {
				figure: {
					children: {
						a: {
							children: {
								img: {
									attributes: ['src', 'alt', 'data-pin-title', 'data-pin-description', 'data-pin-id', 'data-pin-nopin']
								}
							}
						},
						img: {
							attributes: ['src', 'alt', 'data-pin-title', 'data-pin-description', 'data-pin-id', 'data-pin-nopin']
						}
					}
				}
			}
		});

		return settings;
	}
	wp.hooks.addFilter('blocks.registerBlockType', 'ultimatesocialshare/image', ultimatesocialshareRegisterImageBlockAttributes);

	var newClientIDs = [];

	//register inspector controls for existing image block
	var ultimatesocialshareRegisterImageBlockInspectorControls = wp.compose.createHigherOrderComponent(function(BlockEdit) {

		return function(props) {

			//disregard if not an image block
			if(props.name !== 'core/image') {
				return el(BlockEdit, props);
			}

			//check existing attributes
			if(typeof props.attributes.id == 'undefined') {

				if(newClientIDs.indexOf(props.clientId ) === -1) {
					newClientIDs.push(props.clientId);
				}

				return el(BlockEdit, props);
			}

			//set  attributes from attachment if we need to
			if(newClientIDs.indexOf(props.clientId) !== -1) {

				var attachment = wp.media.attachment(props.attributes.id);

				props.setAttributes({
					ultimatesocialsharePinTitle : attachment.get('ultimatesocialshare_pin_title'),
					ultimatesocialsharePinDescription : attachment.get('ultimatesocialshare_pin_description'),
					ultimatesocialsharePinRepinID : attachment.get('ultimatesocialshare_pin_repin_id'),
					ultimatesocialsharePinNoPin : attachment.get('ultimatesocialshare_pin_nopin')
				});

				newClientIDs.splice(newClientIDs.indexOf(props.clientId), 1);
			}

			return el(wp.element.Fragment, {},
				el(wp.blockEditor.InspectorControls, {},

					//ultimatesocialshare panel section
					el(wp.components.PanelBody, {title : 'ultimatesocialshare'},

						//pin title
						el(wp.components.TextControl, {
							value    : props.attributes.ultimatesocialsharePinTitle,
							label    : __('Pin Title', 'ultimatesocialshare'),
							onChange : function( new_value ) {
								props.setAttributes({ultimatesocialsharePinTitle : new_value});
							}
						}),

						//pin description
						el(wp.components.TextareaControl, {
							value    : props.attributes.ultimatesocialsharePinDescription,
							label    : __('Pin Description', 'ultimatesocialshare'),
							help 	 : __("Pinterest does not yet support passing both a title and description from a pin. We've added both fields in advance, but currently, only the title will be sent to Pinterest.", 'ultimatesocialshare'),
							onChange : function (new_value) {
								props.setAttributes({ultimatesocialsharePinDescription : new_value});
							}
						}),

						//pin repin id
						el(wp.components.TextControl, {
							value    : props.attributes.ultimatesocialsharePinRepinID,
							label    : __('Pin Repin ID', 'ultimatesocialshare'),
							onChange : function(new_value) {
								props.setAttributes({ultimatesocialsharePinRepinID : new_value});
							}
						}),

						//disable pinning
						el(wp.components.ToggleControl, {
							checked  : props.attributes.ultimatesocialsharePinNoPin,
							label    : __('Disable Pinning', 'ultimatesocialshare'),
							onChange : function(new_value) {
								props.setAttributes({ultimatesocialsharePinNoPin : new_value});
							}
						})
					)
				),
				el(BlockEdit, props)
			);
		}
	});
	wp.hooks.addFilter('editor.BlockEdit', 'ultimatesocialshare/image', ultimatesocialshareRegisterImageBlockInspectorControls);

	//save our custom image block attributes when saving existing image block
	function ultimatesocialshareSaveImageBlockAttributes(element, blockType, attributes) {

		//disregard if not an image block
		if(blockType.name !== 'core/image') {
			return element;
		}

		var pinData = [];

		//build array of data that needs saving
		if(!lodash.isEmpty(attributes.ultimatesocialsharePinTitle)) {
			pinData.push({
				attribute : 'data-pin-title',
				value     : attributes.ultimatesocialsharePinTitle
			});
		}

		if(!lodash.isEmpty(attributes.ultimatesocialsharePinDescription)) {
			pinData.push({
				attribute : 'data-pin-description',
				value     : attributes.ultimatesocialsharePinDescription
			});
		}

		if(!lodash.isEmpty(attributes.ultimatesocialsharePinRepinID)) {
			pinData.push({
				attribute : 'data-pin-id',
				value     : attributes.ultimatesocialsharePinRepinID
			});
		}

		if(attributes.ultimatesocialsharePinNoPin) {
			pinData.push({
				attribute : 'data-pin-nopin',
				value     : 'true'
			});
		}

		//return original element if no custom data is present
		if(lodash.isEmpty(pinData)) {
			return element;
		}

		//convert element to string
		var elementString = wp.element.renderToString(element);

		//loop through our custom input data
		for(index in pinData) {

			var attribute = pinData[index]['attribute'];
			var value = pinData[index]['value'].replace(/\"/g, '').replace(/</g, '').replace(/>/g, '').replace(/&/g, '&amp;');

			//make sure the attribute doesn't already exist in the element
			if(elementString.indexOf(attribute) !== -1) {
				continue;
			}

			//add attribute/value data to image tag
			elementString = elementString.replace('<img ', '<img ' + attribute + '="' + value + '" ');
		}

		return el(wp.element.RawHTML, {}, elementString);
	}
	wp.hooks.addFilter('blocks.getSaveElement', 'ultimatesocialshare/image', ultimatesocialshareSaveImageBlockAttributes, 50);
})();