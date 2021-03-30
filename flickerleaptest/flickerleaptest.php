<?php
/*
Plugin Name: Flicker Leap Test
Plugin URI: https://flickerleap.com/
Description: Flicker Leap Assessment
Version: 1.0
Author: Tebogo Moopelwa
Author URI: https://flickerleap.com/
Text Domain: flickerleap
*/

/* Register events post type */
function register_flickerleap_events()
{
    $labels = array(
        'name'               => _x( 'Events',''),
        'singular_name'      => _x( 'Event',''),
        'add_new'            => _x( 'Add New', 'event' ),
        'add_new_item'       => __( 'Add New Event' ),
        'edit_item'          => __( 'Edit Event' ),
        'new_item'           => __( 'New Event' ),
        'all_items'          => __( 'All Events' ),
        'view_item'          => __( 'View Event' ),
        'search_items'       => __( 'Search Events' ),
        'not_found'          => __( 'No events found' ),
        'not_found_in_trash' => __( 'No events found in the Trash' ), 
        'parent_item_colon'  => ’,
        'menu_name'          => 'Events'
      );

      $args = array(
        'labels'              => $labels,
        'description'         => 'List of events',
        'public'              => true,
        'supports'            => array( 'title', 'editor', 'thumbnail' ),
        'has_archive'         => true,
        'capability_type'     => 'post',
        'show_in_rest'        => true,
        'publicly_queryable'  => true,
        'query_var'           => true,
        'show_ui'             => true,
        'show_in_nav_menus'   => true,
        'show_in_menu'        => true, 
        'rewrite'             => array('slug' => 'events'),

      );

    register_post_type( 'events', $args ); 
}
add_action('init', 'register_flickerleap_events');

/* Add event details box */
function add_event_details_meta() {
    $screens = [ 'events' ];
    foreach ( $screens as $screen ) {
        add_meta_box(
            'event_box_id',
            'Event Details', 
            'show_event_details_meta', 
            $screen             
        );
    }
}
add_action( 'add_meta_boxes', 'add_event_details_meta' );

/* Show event details meta box */
function show_event_details_meta( $post ) {

    wp_nonce_field( plugin_basename( __FILE__ ), 'show_event_details_meta_nonce' );
    
    ?>
    <label for="event_date"><?php _e( 'Event Date:' ); ?></label>
    <input type="text" name="event_date" value=""  placeholder="dd/mm/yyyy" /><br/><br/>
    <label for="ticket_price"><?php _e( 'Ticket Price:' ); ?></label>
    <input type="text" name="ticket_price" value="" placeholder="Enter ticket price(R 55.00)"/><br/><br/>
    <label for="tickets_avail"><?php _e( 'Tickets Available:' ); ?></label>
    <input type="text" name="tickets_avail" value="" placeholder="Enter number of tickets available"/><br/>
    <?php
}
/* Save event details meta */
function save_event_details( $post_id )
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
    return;
  
    if ( !wp_verify_nonce( $_POST['show_event_details_meta_nonce'], plugin_basename( __FILE__ ) ) )
    return;
  
    if ( 'page' == $_POST['post_type'] ) {
      if ( !current_user_can( 'edit_page', $post_id ) )
      return;
    } else {
      if ( !current_user_can( 'edit_post', $post_id ) )
      return;
    }
    $event_date = $_POST['event_date'];
    update_post_meta( $post_id, 'event_date', $event_date );
    
    $ticket_price = $_POST['ticket_price'];
    update_post_meta( $post_id, 'ticket_price', $ticket_price );

    $tickets_avail = $_POST['tickets_avail'];
    update_post_meta( $post_id, 'tickets_avail', $tickets_avail );
}  

add_action( 'save_post', 'save_event_details' );
