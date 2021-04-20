<?php
 // Prevent direct file access
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

class WPSM_Widget extends WP_Widget {

	private $basedir;
	private $slug;
	private $api_key;
    
    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
			
        $this->slug = str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
        $this->basedir = plugins_url() . '/' . str_replace(basename( __FILE__), "" ,plugin_basename(__FILE__));
        
        $widget_ops = array(
            'classname' => 'Sendy_Multilist',
            'description' => 'Adds a subscribe widget, for one or more Sendy lists, to your WordPress website',
        );
			
        parent::__construct( 'Sendy_Multilist', 'Multilist Subscribe for Sendy', $widget_ops );
        
        $this->define_endpoint();
        
        $this->define_post_notification();
    }

	public function define_endpoint() {
		add_action( 'rest_api_init', function () {
            register_rest_route( 'wplikeapro-sendy/v1', '/subscribe/', array(
                'methods' => 'POST',
                'callback' => array($this, 'subscribe'),
				'permission_callback' => '__return_true',
				'args' => array(
					'name'=>[],
					'email'=>[],
					'list'=>[]
				)
            ));
		});
	}
	
	public function define_post_notification() {
		add_action('transition_post_status', array($this, 'send_new_post'), 10, 3);
	}
	
	public function subscribe(WP_REST_Request $request) {
		$name = $request->get_param( 'name' );
		$email = $request->get_param( 'email' );
		$post_id = $request->get_param( 'list' );
		
		//validate
		if (!is_email($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		    print "$email is not a valid email address";
		    return;
		}
		
		if (filter_var($name, FILTER_SANITIZE_STRING) !== $name) {
		    print "$name is not a name";
		    return;
		}
		
		$list_data = $this->get_list_data($post_id);
		$list_id = $list_data['id'];
		if (!$list_id) {
		    print "Mailing list not found";
		    return;
		}
		
		$sendy = $this->init_sendy($list_id);

		$results = $sendy->subscribe(array(
			'name'=>$name,
			'email' => $email, //this is the only field required by sendy
			'api_key' => $this->api_key //add api key
		));
		
		if (strpos($results["message"], 'Already subscribed.') !== false) {
			print 'Unable to subscribe that email.';
		    return;
		} else {
			print $results["message"];
		    return;
		}
	}
	
	function send_new_post($new_status, $old_status, $post) {
	    if( ! ('publish' === $new_status && 'publish' !== $old_status)) { //new posts only
	        return;
	    }
	  	
	    $query = new WP_Query(array(
		    'post_type' => 'sendyemailtemplates',
		    'posts_per_page' => -1,
		    'post_status' => 'publish'
		));
		
		$list_data = null;
		foreach($query->posts as $list) {
			$tmp = $this->get_list_data($list->ID);
			if ($tmp['notification_post_type'] == $post->post_type) {
				$list_data = $tmp;
				break;
			}
		}

		if ($list_data == null) {
		    print "<!-- SENDY ERROR: list data not found -->";
				return;
		}
		
		if ($list_data['campaign_status'] != 'active') { // 'active' by default (aka. when empty)
		    print "<!-- SENDY ERROR: campaign disabled for {$list_data['notification_post_type']} -->";
		    return;
		}
		
		$list_id = $list_data['id'];
    	if (!$list_id) {
		    print "<!-- SENDY ERROR: Mailing list not found -->";
		    return;
		}
		
		//EMAIL SUBJECT
		if (strpos($list_data['email_subject'], '[post_title]') !== false) {
			$list_data['email_subject'] = str_replace('[post_title]', $post->post_title, $list_data['email_subject']);
		}
		
		//EMAIL MESSAGE
		$list_data['email_message'] = $this->nl2br2($list_data['email_message']);
		
		if (strpos($list_data['email_message'], '[post_excerpt]') !== false) {
			$excerpt = $post->post_excerpt;
			if (!$excerpt) {
				$readmore = stripos($post->post_content, "<!--more-->");
				if ($readmore === false) {
					$excerpt = $post->post_content;
				} else {
					$excerpt = substr($post->post_content, 0, $readmore);
				}
			}
			
			$list_data['email_message'] = str_replace('[post_excerpt]', $excerpt, $list_data['email_message']);
		}

		if (strpos($list_data['email_message'], '[post_content]') !== false) {
			$list_data['email_message'] = str_replace('[post_content]', $post->post_content, $list_data['email_message']);
		}
		
		if (strpos($list_data['email_message'], '[read_more]') !== false) {
			$list_data['email_message'] = str_replace('[read_more]', '<a href="'.$post->guid.'">Read More</a>', $list_data['email_message']);
		}
		
		if (strpos($list_data['email_message'], '[post_title]') !== false) {
			$list_data['email_message'] = str_replace('[post_title]', '<a href="'.$post->guid.'">'.$post->post_title.'</a>', $list_data['email_message']);
		}
		
		if (strpos(strtolower($list_data['email_message']), '[name,fallback=') !== false) {
			$list_data['email_message'] = str_replace('[name,fallback=', '[Name,fallback=', $list_data['email_message']);
		}
		
		if (strpos($list_data['email_message'], '[post_url]') !== false) {
			$list_data['email_message'] = str_replace('[post_url]', $post->guid, $list_data['email_message']);
		}
		
		
		if (strpos($list_data['email_message'], '[featured_image]') !== false ||
				strpos($list_data['email_message'], '[featured_image_url]') !== false) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
			$img_url = '';
			$img_tag = '';
			if($image)  {
				$img_url = $image[0];
				$img_tag = '<img src="'.$image[0].'" />';
			}
			
			$list_data['email_message'] = str_replace('[featured_image_url]', $img_url, $list_data['email_message']);
			$list_data['email_message'] = str_replace('[featured_image]', $img_tag, $list_data['email_message']);
		}
		
		//handle WordPress shortcodes
		$list_data['email_message'] = apply_filters( 'the_content', $list_data['email_message']);
		$list_data['email_message'] = do_shortcode( $list_data['email_message'] );

		$sendy = $this->init_sendy($list_id);
		
		$campaign = array(
			'from_name' => $list_data['from_name'],
			'from_email' => $list_data['from_email'],
			'reply_to' => $list_data['from_email'],
			'subject' => $list_data['email_subject'],
			'plain_text' => strip_tags($list_data['email_message']), // (optional).
			'html_text' => $list_data['email_message'],
			'list_ids' => $list_data['id'], // Required only if you set send_campaign to 1. //TODO: add ability to send 1 campaign to multiple lists
			//'brand_id' => 0, // Required only if you are creating a 'Draft' campaign.
			'query_string' => $post->post_title, // eg. Google Analytics tags.
			'send_campaign' => 1 // Set to 1 if you want to send the campaign as well and not just create a draft. Default is 0.
		);

		$results = $sendy->createCampaign($campaign);
	}
		
    /**
     * Outputs the content of the widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance ) {
    		$plugin_name = basename(wpsm_server_plugin_directory());
				wp_enqueue_script( $plugin_name.'-script', plugins_url( 'js/widget.js', dirname(__FILE__) ), array('jquery') );

        // outputs the content of the widget
        echo $args['before_widget'];
        
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
        }
        
        if ( ! empty( $instance['desc'] ) ) {
            echo apply_filters( 'widget_desc', $instance['desc'] );
        }

        ?>
    		<form method="POST" id="sendy-subscribe-form" action="<?php echo $this->basedir; ?>subscribe.php">
	            <p class="sendy-name-wrapper">
	            <label for="sendy-name"><?php _e( 'Name:' ); ?></label>
	            <input class="widefat" id="sendy-name" name="sendy-name" type="text">
	            </p>
	            <p class="sendy-email-wrapper">
	            <label for="sendy-email"><?php _e( 'Email:' ); ?></label>
	            <input class="widefat" id="sendy-email" name="sendy-email" type="text">
	            </p>
	            <p>
        <?php
				
        $lists = $this->array_keys($instance, 'sendy_campaign_template_');
				
        $list_header_visible = false;
        foreach($lists as $list => $on) {
        	if ($on != 'on') { continue; }
        	
        	if (!$list_header_visible) {
        		echo '<b>Subscribe to:</b>';
        		$list_header_visible = true;
        	}
					
					$post_id = str_replace('sendy_campaign_template_', '', $list);
					$list_data = $this->get_list_data($post_id);
					$list_title = $list_data['title'];
					
          echo '<br/>';
          echo '<input type="checkbox" checked="checked" name="sendy-lists[]" value="'. $post_id . '" /> '.$list_title;
					
        }
        
        ?>
        		</p>
            <p>
        			<input class="widefat btn btn-primary" id="sendy-subscribe-submit" name="sendy-subscribe-submit" value="Subscribe me!" type="submit">
            </p>
            </form>
            <p id="sendy-note"></p>
        <?php

        echo $args['after_widget'];
    }


    /**
     * Outputs the options form on admin for the subscribe widget
     *
     * @param array $instance The widget options
     */
    public function form( $instance ) {
        // outputs the options form on admin
        $title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Subscribe', 'text_domain' );
        $desc = ! empty( $instance['desc'] ) ? $instance['desc'] : __( 'Subscribe to get regular updates.', 'text_domain' );
        $sendy_installation = ! empty( $instance['sendy_installation'] ) ? $instance['sendy_installation'] : __( 'https://example.com/sendy', 'text_domain' );
        $sendy_api = ! empty( $instance['sendy_api'] ) ? $instance['sendy_api'] : __( '', 'text_domain' );

        $this->printAdminField('title', 'Title:', $title);
        $this->printAdminField('desc', 'Description:', $desc);
        $this->printAdminField('sendy_installation', 'Sendy Installation URL:', $sendy_installation);
        $this->printAdminField('sendy_api', 'Sendy API key:', $sendy_api);
        //checkboxes for which Sendy Email Templates to include
        $query = new WP_Query(array(
			    'post_type' => 'sendyemailtemplates',
			    'posts_per_page' => -1,
			    'post_status' => 'publish'
			));
			
			foreach($query->posts as $post) {
				$list_data = $this->get_list_data($post->ID);
				$list_title = $list_data['title'];
				$checkbox_name = 'sendy_campaign_template_'.$post->ID;
				$checkbox_value = ! empty( $instance[$checkbox_name] ) ? $instance[$checkbox_name] : __( '', 'text_domain' );
				$this->printAdminCheckbox($checkbox_name, $list_title, $checkbox_value);
			}
    }


    /**
     * Processing widget options on save
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     */
    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
        foreach( $new_instance as $key => $value )
        {
            $updated_instance[$key] = sanitize_text_field($value);
        }

        return $updated_instance;
    }
    
    private function printAdminField($fieldname, $label, $value) {
        ?>
            <p>
            <label for="<?php echo $this->get_field_id( $fieldname ); ?>"><?php _e( $label ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( $fieldname ); ?>" name="<?php echo $this->get_field_name( $fieldname ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>">
            </p>
        <?php
    }
    
    private function printAdminCheckbox($fieldname, $label, $value) {
        ?>
            <p>
		        <input class="checkbox" type="checkbox" <?php checked( $value, 'on' ); ?> id="<?php echo $this->get_field_id( $fieldname ); ?>" name="<?php echo $this->get_field_name( $fieldname ); ?>" /> 
            <label for="<?php echo $this->get_field_id( $fieldname ); ?>"><?php _e( $label ); ?></label>
            </p>
        <?php
    }
    
    private function array_keys($array, $searchkey) {
		$searchkey_length = strlen($searchkey);
		$tmp = array();
		foreach ($array  as $k => $v) {
			if (substr($k, 0, $searchkey_length) == $searchkey) {
				$tmp[$k] = $v;
			}
		}
		
		return $tmp;
    }
    
    private function nl2br2($string) { 
        $string = str_replace(array("\r\n", "\r", "\n"), "<br />", $string); 
        return $string; 
    } 
    
    private function get_list_data($post_id) {
		$list_data = get_post_meta($post_id);
		$list = array();
		$list['title'] = $list_data['wpsm_list_title'][0];
		$list['id'] = $list_data['wpsm_list_id'][0];
		$list['campaign_status'] = $list_data['wpsm_campaign_status'][0];
		$list['notification_post_type'] = $list_data['wpsm_notification_post_type'][0];
		$list['from_name'] = $list_data['wpsm_from_name'][0];
		$list['from_email'] = $list_data['wpsm_from_email'][0];
		$list['email_subject'] = $list_data['wpsm_email_post_notification_subject'][0];
		$list['email_message'] = $list_data['wpsm_email_post_notification_subscribe_message'][0];

		return $list;
    }
    
    private function init_sendy($list_id) {
        
        $widget_options_all = get_option($this->option_name);
        $options = $widget_options_all[ $this->number ];
        $this->api_key = $options["sendy_api"];
        
        $config = array(
        	'api_key' => $options["sendy_api"], //your API key is available in Settings
        	'installation_url' => $options["sendy_installation"],  //Your Sendy installation
        	'list_id' => $list_id
        );
        
        if ( ! class_exists( '\\SendyPHP\\SendyPHP') ) {
        	require_once wpsm_server_plugin_directory() . '/vendor/chrisnesbit1/wpsendyphp/src/SendyPHP.php';
        }
        
        return new \SendyPHP\SendyPHP($config);
		
    }
}