jQuery(function($) {

	//register plugin with tinymce editor
	tinymce.PluginManager.add('ultimatesocialshare_click_to_tweet', function(editor, url) {

		//click to tweet button
		editor.addButton('ultimatesocialshare_click_to_tweet', {
			icon 	: 'ultimatesocialshare-ctt-icon',
			tooltip : 'ultimatesocialshare ' + ultimatesocialshare.translations.ctt.tooltip,
			cmd     : 'ultimatesocialshare_click_to_tweet'
		});

		//click to tweet button command
		editor.addCommand('ultimatesocialshare_click_to_tweet', function() {

			editor.windowManager.open({
				id		 : 'ultimatesocialshare_click_to_tweet_popup',
				title 	 : ultimatesocialshare.translations.ctt.title,
				minWidth : 750,
				buttons	 : [
					{
						text 		: ultimatesocialshare.translations.ctt.submit,
						classes 	: 'primary abs-layout-item',
						minWidth 	: 130,
						onclick		: 'submit'
					}
				],
				body  	 : [
					{
						type 		: 'textbox',
						id			: 'ultimatesocialshare_ctt_tweet_share',
						name		: 'ultimatesocialshare_ctt_tweet_share',
						label		: ultimatesocialshare.translations.ctt.body.tweet,
						multiline	: true,
						minWidth	: 400,
						minHeight	: 100
					},
					{
						type		: 'listbox',
						id			: 'ultimatesocialshare_ctt_theme',
						name		: 'ultimatesocialshare_ctt_theme',
						label		: ultimatesocialshare.translations.ctt.body.theme.title,
						values		: [
							{
								text  : ultimatesocialshare.translations.ctt.body.theme.values.default,
								value : ''
							},
							{
								text  : ultimatesocialshare.translations.ctt.body.theme.values.simple,
								value : 'simple'
							},
							{
								text  : ultimatesocialshare.translations.ctt.body.theme.values.simplealt,
								value : 'simple-alt'
							},
							{
								text  : ultimatesocialshare.translations.ctt.body.theme.values.bordered,
								value : 'bordered'
							}
						]
					},
					{
						type 		: 'textbox',
						id			: 'ultimatesocialshare_ctt_cta_text',
						name		: 'ultimatesocialshare_ctt_cta_text',
						label		: ultimatesocialshare.translations.ctt.body.ctatext
					},
					{
						type		: 'listbox',
						id			: 'ultimatesocialshare_ctt_cta_position',
						name		: 'ultimatesocialshare_ctt_cta_position',
						label		: ultimatesocialshare.translations.ctt.body.ctaposition.title,
						values		: [
							{
								text  : ultimatesocialshare.translations.ctt.body.ctaposition.values.default,
								value : ''
							},
							{
								text  : ultimatesocialshare.translations.ctt.body.ctaposition.values.left,
								value : 'left'
							}
						]
					},
					{
						type		: 'checkbox',
						id			: 'ultimatesocialshare_ctt_remove_url',
						name		: 'ultimatesocialshare_ctt_remove_url',
						label		: ultimatesocialshare.translations.ctt.body.removeurl.title,
						text 		: ultimatesocialshare.translations.ctt.body.removeurl.text
					},
					{
						type		: 'checkbox',
						id			: 'ultimatesocialshare_ctt_remove_username',
						name		: 'ultimatesocialshare_ctt_remove_username',
						label		: ultimatesocialshare.translations.ctt.body.removeuser.title,
						text 		: ultimatesocialshare.translations.ctt.body.removeuser.text
					},
					{
						type		: 'checkbox',
						id			: 'ultimatesocialshare_ctt_hide_hashtags',
						name		: 'ultimatesocialshare_ctt_hide_hashtags',
						label		: ultimatesocialshare.translations.ctt.body.hidehash.title,
						text 		: ultimatesocialshare.translations.ctt.body.hidehash.text,
						checked       : true
					}
					
				],
				onsubmit : function(e) {

					var shortcode = '';

					//build shortcode
					if(e.data.ultimatesocialshare_ctt_tweet_share) {

						shortcode = '[ultimatesocialshare_tweet';
						shortcode += ' tweet="' + e.data.ultimatesocialshare_ctt_tweet_share + '"';

						if(e.data.ultimatesocialshare_ctt_theme != 0) {
							shortcode += ' theme="' + e.data.ultimatesocialshare_ctt_theme + '"';
						}	

						if(e.data.ultimatesocialshare_ctt_cta_text) {
							shortcode += ' cta_text="' + e.data.ultimatesocialshare_ctt_cta_text + '"';
						}	

						if(e.data.ultimatesocialshare_ctt_cta_position != 0) {
							shortcode += ' cta_position="' + e.data.ultimatesocialshare_ctt_cta_position + '"';
						}	

						if(e.data.ultimatesocialshare_ctt_remove_url) {
							shortcode += ' remove_url="true"';
						}
							
						if(e.data.ultimatesocialshare_ctt_remove_username) {
							shortcode += ' remove_username="true"';
						}

						if(e.data.ultimatesocialshare_ctt_hide_hashtags) {
							shortcode += ' hide_hashtags="true"';
						}
							
						shortcode += ']';
					}

					//output shortcode
					if(shortcode) {
						editor.insertContent(shortcode);
					}
				}
			});

			//base variables
			$ultimatesocialshare_tweet = $('#ultimatesocialshare_ctt_tweet_share');
			$permalink = $('#sample-permalink');

			//calculate initial counts
			var initial_char_count = 280;
			var username_length = (ultimatesocialshare.twitter_username && $("#ultimatesocialshare_ctt_remove_username").hasClass("mce-checked") != true) ? (ultimatesocialshare.twitter_username.length + 6) : 0;
			var url_length = ($permalink && $("#ultimatesocialshare_ctt_remove_url").hasClass("mce-checked") != true) ? 24 : 0;

			//print character count element
			$ultimatesocialshare_tweet.after('<p id="ultimatesocialshare_tweet_length">' + get_char_count() + ' ' + ultimatesocialshare.translations.ctt.body.charcount + '</p>');

			//adjust heights to make room for the character count
			$ultimatesocialshare_ctt_container = $('#ultimatesocialshare_click_to_tweet_popup-body');
			$ultimatesocialshare_ctt_container.height($ultimatesocialshare_ctt_container.height() + 25);
			$ultimatesocialshare_tweet_container = $ultimatesocialshare_tweet.closest('.mce-formitem');
			$ultimatesocialshare_tweet_container.height($ultimatesocialshare_tweet_container.height() + 25);
			$ultimatesocialshare_tweet_container.siblings('.mce-formitem').each(function() {
				$(this).css('top', parseInt( $(this).css('top'), 10) + 25);
			});
			
			//update character count on keyup
			$ultimatesocialshare_tweet.keyup(function() {
				var char_count = get_char_count();
				$('#ultimatesocialshare_tweet_length').html(char_count + ' ' + ultimatesocialshare.translations.ctt.body.charcount);
				$('#ultimatesocialshare_tweet_length').removeClass();
				if(char_count < 0) {
					$('#ultimatesocialshare_tweet_length').addClass('ultimatesocialshare_tweet_length_negative');
				}
			});

			//update username length if removed
			$('#ultimatesocialshare_ctt_remove_username').click( function() {
				if($(this).attr('aria-checked') == "true") {
					username_length = (ultimatesocialshare.twitter_username.length + 6);
				} else {
					username_length = 0;
				}
				$ultimatesocialshare_tweet.trigger('keyup');
			});

			//update url length if removed
			$('#ultimatesocialshare_ctt_remove_url').click( function() {
				if($(this).attr('aria-checked') == "true") {
					url_length = 24
				}
				else {
					url_length = 0;
				}
				$ultimatesocialshare_tweet.trigger('keyup');
			});

			//return calculated character count
			function get_char_count() {
				return initial_char_count - username_length - url_length - $ultimatesocialshare_tweet.val().replace(/(<([^>]+)>)/ig, "").length;
			}
		});
	});
});