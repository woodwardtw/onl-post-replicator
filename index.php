<?php 
/*
Plugin Name: ONL Post Replicator
Plugin URI:  https://github.com/
Description: For taking posts in the *** category and replicating it to a certain site
Version:     1.0
Author:      Tom Woodward
Author URI:  http://altlab.vcu.edu
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function onl_post_replicator($ID, $post) {
	//get post data
	$author = $post->post_author; /* Post author ID. */
    $title = $post->post_title;
    $content = $post->post_content;
    
	//switch to other site to make the post
	$destination = 3; //onl192  --- should be 3 for production
	if (in_category('pbl-groupwork')){
		switch_to_blog( $destination );
		$pbl_cat_id = get_category_by_slug('pbl-groupwork')->term_id;
		//$group_cat_id = get_category_by_slug('category-slug'); //need to get this from blog url
		$new_post = array(
		  'post_title'    => $title,
		  'post_content'  => $content,
		  'post_status'   => 'publish',
		  'post_author'   => $author,
		  'post_category' => array( $pbl_cat_id ),
		);
		 
		// Insert the post into the database
		remove_action('publish_post', 'onl_post_replicator');//CURSED LOOP!!!!!!!
		wp_insert_post( $new_post );
		add_action('publish_post', 'onl_post_replicator');

    	restore_current_blog();
	}
}
add_action( 'publish_post', 'onl_post_replicator', 10, 2 );




if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}