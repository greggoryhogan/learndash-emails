<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://mynameisgregg.com
 * @since      1.0.0
 *
 * @package    Wp_Lde
 * @subpackage Wp_Lde/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Lde
 * @subpackage Wp_Lde/public
 * @author     Greggory Hogan <hello@mynameisgregg.com>
 */
class Wp_Lde_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->wp_cbf_options = get_option($this->plugin_name);


	}


	
	/*
     *
     * Create ld-email cpt for use in admin
     * 
	 */

	// Wrap images with figure tag - Credit: Robert O'Rourke - http://bit.ly/1q0WHFs
	public function create_plugin_cpt(){
		$name = 'Learndash Emails';
        $singular_name = 'Learndash Email';
        register_post_type( 
            strtolower( 'ld-email' ),
            array(
                'labels' => array(
                    'name'               => _x( $name, 'post type general name' ),
                    'singular_name'      => _x( $singular_name, 'post type singular name'),
                    'menu_name'          => _x( $name, 'admin menu' ),
                    'name_admin_bar'     => _x( $singular_name, 'add new on admin bar' ),
                    'add_new'            => _x( 'Add New', strtolower( $name ) ),
                    'add_new_item'       => __( 'Add New ' . $singular_name ),
                    'new_item'           => __( 'New ' . $singular_name ),
                    'edit_item'          => __( 'Edit ' . $singular_name ),
                    'view_item'          => __( 'View ' . $singular_name ),
                    'all_items'          => __( 'All ' . $name ),
                    'search_items'       => __( 'Search ' . $name ),
                    'parent_item_colon'  => __( 'Parent :' . $name ),
                    'not_found'          => __( 'No ' . strtolower( $name ) . ' found.'),
                    'not_found_in_trash' => __( 'No ' . strtolower( $name ) . ' found in Trash.' ),
                ),
                'public'             => true,
                'has_archive'        => false,
                'hierarchical'       => false,
                'rewrite'            => array( 'slug' => $name ),
                'menu_icon'          => 'dashicons-carrot',
                'exclude_from_search' => true,
                'publicaly_queryable' => false,
                'register_meta_box_cb' => array( $this, 'lde_email_cpt_metaboxes' )
            )
        );
	}

    /*
     *
     * Add meta boxes to cpt
     * 
     */
    public function lde_email_cpt_metaboxes() {
        add_meta_box(
            'email-trigger',
            __( 'Email Trigger', 'wp-lde' ),
            array( $this, 'lde_email_cpt_metaboxes_callback' ),
            'ld-email'
        );
    }

    public function select_ld_email_learner_types() {
        $options = array (
            'Group Leader' => 'group_leader',
            'Learner' => 'learner',
        );
        
        return $options;
    }
    public function select_ld_email_actions() {
        $options = array (
            'Learner Completes' => 'learner_completes',
            'Learner Registers' => 'learner_registers',
        );
        
        return $options;
    }

    
    /*
     *
     * Add meta boxes to cpt Callback
     * 
     */

    public function lde_email_cpt_metaboxes_callback() {
        global $post;

        $repeatable_fields = get_post_meta($post->ID, 'repeatable_fields', true);
        $recipient_options = $this->select_ld_email_learner_types();
        $action_options = $this->select_ld_email_actions();

        wp_nonce_field( 'hhs_repeatable_meta_box_nonce', 'hhs_repeatable_meta_box_nonce' );
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function( $ ){
            $( '#add-row' ).on('click', function() {
                var row = $( '.empty-row.screen-reader-text' ).clone(true);
                row.removeClass( 'empty-row screen-reader-text' );
                row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
                return false;
            });
        
            $( '.remove-row' ).on('click', function() {
                $(this).parents('tr').remove();
                return false;
            });
        });
        </script>
    
        <table id="repeatable-fieldset-one" width="100%">
        <!--<thead>
            <tr>
                <th>Send email to</th>
                <th>Action</th>
                <th>Timeframe</th>
                <th width="8%"></th>
            </tr>
        </thead>-->
        <tbody>
        <?php
        
        if ( $repeatable_fields ) :
        
        foreach ( $repeatable_fields as $field ) {
        ?>
        <tr>
            <td>Send email to 
                <select name="receipient[]">
                <?php foreach ( $recipient_options as $label => $value ) : ?>
                <option value="<?php echo $value; ?>"<?php selected( $field['select'], $value ); ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
                </select>
            </td>
        
            <td>After 
                <select name="actions[]">
                <?php foreach ( $action_options as $label => $value ) : ?>
                <option value="<?php echo $value; ?>"<?php selected( $field['select'], $value ); ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
                </select>
            </td>
        
            <td><input type="text" class="widefat" name="url[]" value="<?php if ($field['url'] != '') echo esc_attr( $field['url'] ); else echo 'http://'; ?>" /></td>
        
            <td><a class="button remove-row" href="#">Remove</a></td>
        </tr>
        <?php
        }
        else :
        // show a blank one
        ?>
        <tr>
            <td>Send email to 
                <select name="recipient[]">
                <?php foreach ( $recipient_options as $label => $value ) : ?>
                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
                </select>
            </td>
        
            <td>After 
                <select name="actions[]">
                <?php foreach ( $action_options as $label => $value ) : ?>
                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
                </select>
            </td>
        
            <td><input type="text" class="widefat" name="url[]" value="http://" /></td>
        
            <td><a class="button remove-row" href="#">Remove</a></td>
        </tr>
        <?php endif; ?>
        
        <!-- empty hidden one for jQuery -->
        <tr class="empty-row screen-reader-text">
            <td>Send email to 
                <select name="recipient[]">
                <?php foreach ( $recipient_options as $label => $value ) : ?>
                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
                </select>
            </td>
        
            <td>After 
                <select name="actions[]">
                <?php foreach ( $action_options as $label => $value ) : ?>
                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
                </select>
            </td>
            
            <td><input type="text" class="widefat" name="url[]" value="http://" /></td>
            
            <td><a class="button remove-row" href="#">Remove</a></td>
        </tr>
        </tbody>
        </table>
        
        <p><a id="add-row" class="button" href="#">Add another</a></p>
        <?php
    }

    /*
     *
     * Save metabox data 
     * 
     */ 
    public function save_lde_email_cpt_metaboxes() {
        global $post; 
        if ($post->post_type != 'ld-email'){
            return;
        }
        if ( ! isset( $_POST['hhs_repeatable_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['hhs_repeatable_meta_box_nonce'], 'hhs_repeatable_meta_box_nonce' ) )
            return;
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
        
        if (!current_user_can('edit_post', $post_id))
            return;
        
        $old = get_post_meta($post_id, 'repeatable_fields', true);
        $new = array();
        $options = select_ld_email_learner_types();
        
        $names = $_POST['name'];
        $selects = $_POST['select'];
        $urls = $_POST['url'];
        
        $count = count( $names );
        
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $names[$i] != '' ) :
                $new[$i]['name'] = stripslashes( strip_tags( $names[$i] ) );
                
                if ( in_array( $selects[$i], $options ) )
                    $new[$i]['select'] = $selects[$i];
                else
                    $new[$i]['select'] = '';
            
                if ( $urls[$i] == 'http://' )
                    $new[$i]['url'] = '';
                else
                    $new[$i]['url'] = stripslashes( $urls[$i] ); // and however you want to sanitize
            endif;
        }

        if ( !empty( $new ) && $new != $old )
            update_post_meta( $post_id, 'repeatable_fields', $new );
        elseif ( empty($new) && $old )
            delete_post_meta( $post_id, 'repeatable_fields', $old );
    }

    /*
     *
     * Remove public access from ld-email cpt
     * 
     */
    public function cpt_redirects(){
		global $wp_query;
        if ( is_archive('ld-email') || is_singular('ld-email') ) :
            $url   = get_bloginfo('url');
            wp_redirect( esc_url_raw( $url ), 301 );
            exit();
        endif;
	}

    

}
