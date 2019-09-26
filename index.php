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
	if (in_category('pbl-group-work')){
		switch_to_blog( $destination );
		$pbl_cat_id = get_category_by_slug('pbl-group-work')->term_id;
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


//GFORM VERSION
add_action( 'gform_after_submission', 'gform_onl_post_replicator', 10, 2 );
function gform_onl_post_replicator( $entry, $form ) {
    //getting post
    $post = get_post( $entry['post_id'] );
    $author = $post->post_author;
    $title = $post->post_title;
 	$content = $post->post_content;
 	$image_url = get_the_post_thumbnail_url($entry['post_id']);
 	$all_cats = [];
 	if ($entry['9']){
	 		$pbl_group = sanitize_title(trim_cat_to_text($entry['9']));
 		}
 	if ($entry['2']){
 		 	$focus = sanitize_title(trim_cat_to_text($entry['2']));
 	}

 	var_dump($pbl_group);
	var_dump($focus); 	

 	$destination = 3; //onl192  --- should be 3 for production
	if (in_category('pbl-group-work', $entry['post_id'])){
		switch_to_blog( $destination );
		if(get_category_by_slug('pbl-group-work')){
			$pbl_cat_id = get_category_by_slug('pbl-group-work')->term_id;
			array_push($all_cats, (int)$pbl_cat_id);
		} else {
			$pbl_cat_id = 1;
			array_push($all_cats, $pbl_cat_id);			
		}
		if(get_category_by_slug($pbl_group)){
			$pbl_group_id = get_category_by_slug($pbl_group)->term_id;
			array_push($all_cats, (int)$pbl_group_id);
		}
		if(get_category_by_slug($focus)){
			$focus_id = get_category_by_slug($focus)->term_id;
			array_push($all_cats, (int)$focus);
		}
		//$group_cat_id = get_category_by_slug('category-slug'); //need to get this from blog url
		$new_post = array(
		  'post_title'    => $title,
		  'post_content'  => '<img src="'.$image_url.'">' . $content,
		  'post_status'   => 'publish',
		  'post_author'   => $author,
		  'post_category' => $all_cats,
		);
		 
		// Insert the post into the database
		wp_insert_post( $new_post );

    	restore_current_blog();
	}
   
}

function trim_cat_to_text($text){
	$length = strlen($text);
	$colon = strpos($text, ':');
	$text = substr($text, 0, $colon);
	return $text;
}


if ( ! function_exists('write_log')) {
   function write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}