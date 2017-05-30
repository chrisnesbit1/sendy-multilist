<?php

// //Meta Box
// /**
// * Register meta box(es).
// */
// function wpsm_register_meta_boxes() {
//     add_meta_box( 'wpsm_metabox', __( 'Email Settings', 'textdomain' ), 'wpsm_metabox_display_callback', 'sendyemailtemplates' );
// }
// add_action( 'add_meta_boxes', 'wpsm_register_meta_boxes' );
 
// /**
// * Meta box display callback.
// *
// * @param WP_Post $post Current post object.
// */
// function wpsm_metabox_display_callback( $post ) {
//   // Display code/markup goes here. Don't forget to include nonces!
  
//   $post_types = get_post_types(array(), 'object');
  
//   wpsm_metabox_print_field('from_name', 'From (Name):', $from_name);
//   wpsm_metabox_print_field('from_email', 'From (Email):', $from_email);
//   wpsm_metabox_print_field('email_subject_prefix', 'Email Subject Prefix (inserted before Post Title):', $email_subject_prefix);
//   wpsm_metabox_print_field('associated_post_type', 'Associated Post Type:', $associated_post_type, $post_types);
//   wpsm_metabox_print_field('recipient_list', 'Sendy List to receive these emails:', $recipient_list);
  
//   //TODO: move sendy config items here. then replace them in the widget with a dropdown of sendy email templates (for teach newsletter type)
// }
 
// /**
// * Save meta box content.
// *
// * @param int $post_id Post ID
// */
// function wpsm_save_meta_box( $post_id ) {
//     // Save logic goes here. Don't forget to include nonce checks!
// }
// add_action( 'save_post', 'wpsm_save_meta_box' );

// function wpsm_metabox_print_field($fieldname, $label, $value, $options) {
//   ? >
//       <p>
//       <label for="<?php echo $fieldname; ? >"><?php _e( $label ); ? ></label>
//   <?php if ($options) { ? >
//       <select class="widefat" id="<?php echo $fieldname; ? >" name="<?php echo $fieldname; ? >" type="text" value="<?php echo esc_attr( $value ); ? >">
// 				<?php foreach($options as $option) { ? >
// 	 			<option value="<?php echo $option->labels->singular_name;? >"><?php echo $option->labels->singular_name;? ></option>
// 	 			<?php } ? >
//   		</select>	
//   <?php } else { ? >
//       <input class="widefat" id="<?php echo $fieldname; ? >" name="<?php echo $fieldname; ? >" type="text" value="<?php echo esc_attr( $value ); ? >">
//       </p>
//   <?php
//   }
// }
// return;
class WPSM_Email_Settings_MetaBox extends WPSM_MetaBox
{
		public function add()
		{
			parent::add();
		}
		
		public function html($post)
		{
			
		}
		
		public function print_field($fieldname, $label, $value, $options)
		{
			
		}
}

abstract class WPSM_MetaBox
{
		private $key = "_wpsm_es_key";
		private $title = "Email Settings";
    private $screens = array('sendyemailtemplates');
    private $fields = array();
		
    public function add()
    {
        foreach ($this->screens as $screen) {
            add_meta_box(
                $screen.$this->key,          // Unique ID
                $this->title, // Box title
                array($this, 'html'),   // Content callback, must be of type callable
                $screen                  // Post type
            );
        }
    }
 
    public function save($post_id)
    {
        foreach ($this->$fields as $field) {
	        $field_id = $field['id'];
	        if (array_key_exists($field_id, $_POST)) {
	            update_post_meta(
	                $post_id,
	                $this->key,
	                $field_id
	            );
	        }
        }
    }
 
 		abstract public function html($post);
   // public function html($post)
   // {
   //     // $value = get_post_meta($post->ID, $this->key, true);
   //     // ? >
   //     // <label for="wporg_field">Description for this field</label>
   //     // <select name="wporg_field" id="wporg_field" class="postbox">
   //     //     <option value="">Select something...</option>
   //     //     <option value="something" <?php selected($value, 'something'); ? >>Something</option>
   //     //     <option value="else" <?php selected($value, 'else'); ? >>Else</option>
   //     // </select>
   //     // < ? php
   // }
    
    abstract function print_field($fieldname, $label, $value, $options);
   // private function print_field($fieldname, $label, $value, $options) {
   // 	// 	print "<p>\r\n";
	  //   //   print "<label for='{$fieldname}'>{$label}</label>\r\n";
	  // 		// if ($options) {
	  //   //   		print "<select class='widefat' id='{$fieldname}' name='{$fieldname}' type='text' value='" . esc_attr( $value ) . "'>\r\n";
			// 		// 	foreach($options as $option) {
		 //		// 				print "<option value='{$option->labels->singular_name}'>{$option->labels->singular_name}</option>\r\n";
	 	// 		// 		}
	  // 		// 		print "</select>\r\n";
	  // 		// } else {
	  //   //   		print "<input class='widefat' id='{$fieldname}' name='{$fieldname}' type='text' value='" .  esc_attr( $value ) . "'>\r\n";
	  //   //   }
	  //   //   print "</p>\r\n";
	  // }
}
 
//add_action('add_meta_boxes', ['WPOrg_Meta_Box', 'add']);
//add_action('save_post', ['WPOrg_Meta_Box', 'save']);