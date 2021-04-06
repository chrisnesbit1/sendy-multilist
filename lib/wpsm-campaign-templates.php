<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

function Sendy_Campaign_Templates_init() {
	register_post_type( 'sendyemailtemplates', array(
		'labels'            => array(
			'name'                => __( 'Sendy Campaign Templates', 'sendy-multilist-subscriber-widget' ),
			'singular_name'       => __( 'Sendy Campaign Template', 'sendy-multilist-subscriber-widget' ),
			'all_items'           => __( 'Sendy Campaign Templates', 'sendy-multilist-subscriber-widget' ),
			'new_item'            => __( 'New Sendy Campaign Template', 'sendy-multilist-subscriber-widget' ),
			'add_new'             => __( 'Add New', 'sendy-multilist-subscriber-widget' ),
			'add_new_item'        => __( 'Add New Sendy Campaign Template', 'sendy-multilist-subscriber-widget' ),
			'edit_item'           => __( 'Edit Sendy Campaign Template', 'sendy-multilist-subscriber-widget' ),
			'view_item'           => __( 'View Sendy Campaign Template', 'sendy-multilist-subscriber-widget' ),
			'search_items'        => __( 'Search Sendy Campaign Templates', 'sendy-multilist-subscriber-widget' ),
			'not_found'           => __( 'No Sendy Campaign Template found', 'sendy-multilist-subscriber-widget' ),
			'not_found_in_trash'  => __( 'No Sendy Campaign Template found in trash', 'sendy-multilist-subscriber-widget' ),
			'parent_item_colon'   => __( 'Parent Sendy Campaign Template', 'sendy-multilist-subscriber-widget' ),
			'menu_name'           => __( 'Sendy Campaign Templates', 'sendy-multilist-subscriber-widget' ),
		),
		'public'            => true,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'supports'          => array( 'title' ),
		'has_archive'       => true,
		'rewrite'           => true,
		'query_var'         => true,
		'menu_icon'         => 'dashicons-admin-post',
	) );
}
add_action( 'init', 'Sendy_Campaign_Templates_init' );

function Sendy_Campaign_Templates_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['Sendy_Campaign_Template'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Sendy Campaign Template updated. <a target="_blank" href="%s">View Sendy Campaign Template</a>', 'sendy-multilist-subscriber-widget'), esc_url( $permalink ) ),
		2 => __('Sendy Campaign Template updated.', 'sendy-multilist-subscriber-widget'),
		3 => __('Sendy Campaign Template deleted.', 'sendy-multilist-subscriber-widget'),
		4 => __('Sendy Campaign Template updated.', 'sendy-multilist-subscriber-widget'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Sendy Campaign Template restored to revision from %s', 'sendy-multilist-subscriber-widget'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Sendy Campaign Template published. <a href="%s">View Sendy Campaign Template</a>', 'sendy-multilist-subscriber-widget'), esc_url( $permalink ) ),
		7 => __('Sendy Campaign Template saved.', 'sendy-multilist-subscriber-widget'),
		8 => sprintf( __('Sendy Campaign Template submitted. <a target="_blank" href="%s">Preview Sendy Campaign Template</a>', 'sendy-multilist-subscriber-widget'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Sendy Campaign Template scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Sendy Campaign Template</a>', 'sendy-multilist-subscriber-widget'),
		// translators: Publish box date format, see http://php.net/date
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Sendy Campaign Template draft updated. <a target="_blank" href="%s">Preview Sendy Campaign Template</a>', 'sendy-multilist-subscriber-widget'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'Sendy_Campaign_Templates_updated_messages' );

/* Post List */
//add meaningful columns
add_filter('manage_sendyemailtemplates_posts_columns', 'wpsm_sendyemailtemplates_table_head');
function wpsm_sendyemailtemplates_table_head( $defaults ) {
    $defaults['list_title']    = 'List Title';
    $defaults['list_id']   = 'List ID';
    $defaults['campaign_status'] = 'Campaign Status';
    $defaults['notification_post_type'] = 'Post Type';
    return $defaults;
}
//fill the columns with data
add_action( 'manage_sendyemailtemplates_posts_custom_column', 'wpsm_sendyemailtemplates_table_content', 10, 2 );
function wpsm_sendyemailtemplates_table_content( $column_name, $post_id ) {
	  $prefix = 'wpsm_';
	  $value = get_post_meta( $post_id, $prefix.$column_name, true );
	  
	  switch($column_name) {
	  	case 'list_title':
  		case 'list_id':
  		case 'notification_post_type':
	      print $value;
	      break;
      case 'campaign_status':
	      print empty($value) || $value == 'active' ? '<span class="wpsm_active_campaign">Active</span>' : '<span class="wpsm_inactive_campaign">Inactive</span>';
	      break;
    }
}
//add sorting to the columns
add_filter( 'manage_edit-sendyemailtemplates_sortable_columns', 'wpsm_sendyemailtemplates_table_sorting' );
function wpsm_sendyemailtemplates_table_sorting( $columns ) {
  $columns['list_title'] = 'list_title';
  $columns['list_id'] = 'list_id';
  $columns['campaign_status'] = 'campaign_status';
  $columns['notification_post_type'] = 'notification_post_type';
  return $columns;
}
add_filter( 'request', 'wpsm_sendyemailtemplates_column_orderby' );
function wpsm_sendyemailtemplates_column_orderby( $vars ) {
    if ( isset( $vars['orderby'] ) && 'list_title' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'wpsm_list_title',
            'orderby' => 'meta_value'
        ) );
    }
    if ( isset( $vars['orderby'] ) && 'list_id' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'wpsm_list_id',
            'orderby' => 'meta_value'
        ) );
    }
    if ( isset( $vars['orderby'] ) && 'campaign_status' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'wpsm_campaign_status',
            'orderby' => 'meta_value'
        ) );
    }
    if ( isset( $vars['orderby'] ) && 'notification_post_type' == $vars['orderby'] ) {
        $vars = array_merge( $vars, array(
            'meta_key' => 'wpsm_notification_post_type',
            'orderby' => 'meta_value'
        ) );
    }

    return $vars;
}



/* The Add/Edit Post page */
//meta boxes
add_action('wp_loaded', function(){

  $prefix = 'wpsm_';
  
  /* 
   * configure your meta box
   */
  $config = array(
    'id'             => $prefix.'meta_box',          // meta box id, unique per meta box
    'title'          => 'Sendy List Notification Settings',          // meta box title
    'pages'          => array('sendyemailtemplates'),      // post types, accept custom post types as well, default is array('post'); optional
    'context'        => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'priority'       => 'high',            // order of meta box: high (default), low; optional
    'fields'         => array(),            // list of meta fields (can be added by field arrays)
    'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );
  
	if ( ! class_exists( 'AT_Meta_Box') ) {
		require_once wpsm_server_plugin_directory() . '/vendor/bainternet/My-Meta-Box/meta-box-class/my-meta-box-class.php';
	}

  $my_meta =  new AT_Meta_Box($config);
   
  $my_meta->addText($prefix.'list_title',array('name'=> 'List Title'));
  $my_meta->addText($prefix.'list_id',array('name'=> 'List ID'));
  //$my_meta->addCheckbox($prefix.'list_selected_default',array('name'=> 'Select this list, in subscribe forms, by default?'));

	$options = array();
  $options['active'] = 'Active';
  $options['inactive'] = 'Inactive';
	$my_meta->addSelect($prefix.'campaign_status', $options, array(
		'name'=> 'Campaign Status', 
		'desc' => 'If Campaign Status is "Inactive", no post notifications will be generated by this Campaign Template. However, all fields below "Campaign Status" will continue to be stored in the database.', 
		'std' => 'active'
	));
  
  $post_types = get_post_types( array( 'public' => true ), 'names' ); 
	$options = array();
  foreach($post_types as $post_type => $label) {
    $options[$post_type] = $label;
  }
  $my_meta->addSelect($prefix.'notification_post_type', $options, array('name'=> 'Post Type to hook notification emails to', 'std'=> array('post')));

  $my_meta->addText($prefix.'from_name',array('name'=> 'From Name'));
  $my_meta->addText($prefix.'from_email',array('name'=> 'From Email'));
  $my_meta->addText($prefix.'email_post_notification_subject',array(
    'name'=> 'Post Notification Email Subject',
    'desc' =>"Please insert [post_title] into the subject where you want the post title to be included. Visit your Sendy list's settings page to configure optin settings and subscribe/unsubscribe email messages.")
  );
  $my_meta->addWysiwyg($prefix.'email_post_notification_subscribe_message',array(
  	'name'=> 'Post Notification Email Message Body',
  	'desc' => '<b><u>Multilist Subscribe, for Sendy</u></b> *Shortcodes supported only by this plugin<br/>'
  						.'[post_title] The title of the post you\'re publishing (this will be included as a hyperlink to your post)</br>'
  						.'[post_content] The body of your post (WordPress shortcodes, within the post\'s content, are supported)</br>'
  						.'[post_excerpt] The excerpt of your post (the entire post_content will be used if no excerpt can be determined. Additionally, WordPress shortcodes, within the post\'s excerpt, are supported)</br>'
  						.'[read_more] The words "read more" will be included as a link to your post</br>'
  						.'[post_url] Just the actual link to your post. Great for use with your own buttons!</br>'
  						.'[featured_image] the post\'s featured image will be displayed in the templaet</br>'
  						.'[featured_image_url] Just the actual link to the featured image. This can be handy if you need special formatting in the email campaign template.</br>'
							.'<br/><b><u><a href="https://sendy.co">Sendy.co</a> Shortcodes</u></b> *Provided by Sendy.co, and only available to Sendy</br>'
  						.'[Name,fallback=] *This shortcode injects the subscribers name, or a fallback phrase that you specify if no name exists</br>'
  						.'[Email] *The subscriber\'s email will be included as a link to open a new email to the reader</br>'
  						.'[webversion] *A link to the web version of the email, to be viewed in a browser</br>'
  						.'[unsubscribe] *A link the subscriber can use to unsubscribe from your email list</br>'
  						.'</br><b>WordPress shortcodes are also supported in the Email Message Body.</b>'
	));
  
  //Finish Meta Box Declaration 
  $my_meta->Finish();
});
