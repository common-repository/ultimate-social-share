<?php if ( ! defined( 'ABSPATH' )  ) { die; } // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'ultimatesocialshare';
$file_path = plugin_dir_url(__DIR__).'assets/images/socialicons/';

//
// Create options
//
CSF::createOptions( $prefix, array(
  'menu_title' => 'Social Sharing',
  'menu_slug'  => 'ultimate_social_sharings',
  'menu_icon'   => 'dashicons-align-wided',
) );





CSF::createSection( $prefix, array(
  'title'  => 'General Settings',
  'icon'   => 'fas fa-cogs',
  'fields' => array(


array(
  'id'            => 'general',
  'type'          => 'tabbed',
  //'title'         => 'Tabbed',
  'tabs'          => array(
    

array(
      'title'     => 'Inline Buttons',
      'icon'      => 'fas fa-ellipsis-h',
      'fields'    => array(
       array(
  'id'     => 'inline',
  'type'   => 'fieldset',
  'fields' => array(
  
  		    // A Heading
array(
  'type'    => 'notice',
  'style'   => 'success',
  'content' => '<b>Inline Buttons</b>',
  ),

	array(
  'id'      => 'enabled',
  'type'    => 'switcher',
  'title'    => 'Enable Inline Buttons',
  'desc'   => '',
),



array(
  'id'          => 'post_types',
  'type'        => 'select',
  'title'       => 'Post types',
  'placeholder' => 'Select Post types to display Social Media icons within.',
  'options'     => 'post_types',
  'chosen'      => true,
  'multiple'    => true,
  'sortable'    => true,
   'default'     => 'post'
 
),


array(
  'id'        => 'social_networks',
  'type'      => 'image_select',
  'title'     => 'Social Networks',
  'multiple'  => true,
  'options'   => array(
		'facebook'   => $file_path.'facebook.png',
		'tumblr'		=> $file_path.'tumblr.png',
		'twitter'    => $file_path.'twitter.png',
		'pinterest'  =>$file_path.'pinterest.png',
		'email'	     => $file_path.'email.png',	
		'whatsapp'   => $file_path.'whatsapp.png',
		'linkedin'   => $file_path.'linkedin.png',
		'reddit'	 => $file_path.'reddit.png',
		'print'	     => $file_path.'print.png',		
		'buffer'     => $file_path.'buffer.png',
		'flipboard'  => $file_path.'flipboard.png',
		'hackernews' => $file_path.'hackernews.png',
		'line'   => $file_path.'line.png',
		'mix'		 => $file_path.'mix.png',
		'pocket'	 => $file_path.'pocket.png',
		'sms'        => $file_path.'sms.png',
		'vkontakte'		=> $file_path.'vkontakte.png',
		'xing'		 => $file_path.'xing.png',
  ),

),




array(
  'id'          => 'position',
  'type'        => 'select',
  'title'       => 'Button Position',
  'placeholder' => 'Select an option',
  'options'     => array(
    ''  => 'Above Content',
    'below'  => 'Below Content',
    'both'  => 'Above & Below Content',
	'neither'  => 'Neither',
  ),
  'default'     => ''
),



array(
  'id'      => 'breakpoint',
  'type'    => 'number',
  'title'   => 'Mobile Breakpoint',
  'default' => 1000,
  'unit' => 'px',
),




	array(
  'id'      => 'hide_above_breakpoint',
  'type'    => 'switcher',
  'title'    => 'Hide Above Breakpoint',
  'desc'   => '',
),



	array(
  'id'      => 'hide_below_breakpoint',
  'type'    => 'switcher',
  'title'    => 'Hide Below Breakpoint',
  'desc'   => '',
),
),
),
),
),










),
),





  )
) );







// Styling Settings


CSF::createSection( $prefix, array(
  'title'  => 'Style Settings',
  'icon'   => 'fas fa-fill-drip',
  'fields' => array(
  







array(
  'id'            => 'general',
  'type'          => 'tabbed',
  //'title'         => 'Tabbed',
  'tabs'          => array(
    array(
      'title'     => 'Inline Buttons',
      'icon'      => 'fas fa-ellipsis-h',
      'fields'    => array(
       array(
  'id'     => 'inline',
  'type'   => 'fieldset',
  'fields' => array(
    
	    // A Heading
array(
  'type'    => 'notice',
  'style'   => 'success',
  'content' => '<b>Inline Buttons</b>',
  
),
	
	array(
  'id'        => 'style',
  'type'      => 'select',
  'title'     => 'Button Style',
  'options'   => array(
    '' => 'Solid',
    'inverse' => 'Inverse',
    'solid-inverse-border' => 'Bordered Label',
	'solid-inverse' => 'Minimal Label',
	'full-inverse' => 'Minimal',
  ),
  'default'   => array( '' )
),


array(
  'id'        => 'layout',
  'type'      => 'select',
  'title'     => 'Button Layout',
  'options'   => array(
    '' => 'Auto Width',
	'1-col' => '1 Column',
	'2-col' => '2 Columns',
	'3-col' => '3 Columns',
	'4-col' => '4 Columns',
	'5-col' => '5 Columns',
	'6-col' => '6 Columns',
  ),
),

array(
  'id'        => 'alignment',
  'type'      => 'select',
  'title'     => 'Button Alignment',
  'options'   => array(
    '' => 'Left',
	'right' => 'Right',
	'center' => 'Center',

  ),
  'dependency' => array( 'layout', '==', '' ),

),


array(
  'id'        => 'size',
  'type'      => 'select',
  'title'     => 'Button Size',
  'options'   => array(
    'small' => 'Small',
	'' => 'Medium',
	'large' => 'Large',

  ),
    'default'   => array( '' )
),
	
array(
  'id'        => 'shape',
  'type'      => 'select',
  'title'     => 'Button Shape',
  'options'   => array(
    '' => 'Squared',
	'rounded' => 'Rounded',
	'circular' => 'Circular',

  ),
    'default'   => array( '' )
),

array(
  'id'        => 'border_radius',
  'type'      => 'number',
  'title'     => 'Border Radius',
    'unit' => 'px',
   'dependency' => array( 'shape', '==', 'rounded' ),
),


array(
  'id'        => 'button_color',
  'type'      => 'color',
  'title'     => 'Button Color',
),

array(
  'id'        => 'button_hover_color',
  'type'      => 'color',
  'title'     => 'Button on-hover color',
),



	array(
  'id'      => 'labels',
  'type'    => 'switcher',
  'title'    => 'Show Labels',
  'desc'   => '',
),

	array(
  'id'      => 'hide_labels_mobile',
  'type'    => 'switcher',
  'title'    => 'Hide Labels on Mobile',
  'desc'   => '',
   'dependency' => array( 'labels', '==', 'true' ),
),

	array(
  'id'      => 'total_share_count',
  'type'    => 'switcher',
  'title'    => 'Show Total Count',
  'desc'   => '',
),


array(
  'id'        => 'total_share_count_position',
  'type'      => 'select',
  'title'     => 'Total Share Count Position',
  'options'   => array(
    '' => 'Squared',
	'before' => 'Before',
	'' => 'After',

  ),
   'dependency' => array( 'total_share_count', '==', 'true' ),
    'default'   => array( '' )
),



array(
  'id'        => 'total_share_count_color',
  'type'      => 'color',
  'title'     => 'Total Share Count Color',
),


	array(
  'id'      => 'network_share_counts',
  'type'    => 'switcher',
  'title'    => 'Network Share Counts',
  'desc'   => '',
),


	array(
  'id'      => 'remove_spacing',
  'type'    => 'switcher',
  'title'    => 'Remove Spacing',
  'desc'   => '',
),	
	
	
	
	
	
	

	
  ),
),
      )
    ),
	
	

  )
),




  )
) );