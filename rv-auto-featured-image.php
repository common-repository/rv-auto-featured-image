<?
/**
 * Plugin Name:       RV Auto Featured Image
 * Plugin URI:        https://wordpress.org/plugins/rv-auto-featured-image
 * Description:       This plugin will automatically picks the first image rrom your post content and set it as feature image if you forget to add feature image to your post.
 * Version:           1.0.0
 * Author:            maheshmaharjan, ranivibe
 * Author URI:        https://www.mahesh-maharjan.com.np
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html 

 * Auto Add Featured Image is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * RV Auto Featured Image is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with RV Auto Featured Image. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( function_exists( 'add_theme_support' ) ) {

     add_theme_support( 'post-thumbnails' ); // This should be in your theme. But we add this here because this way we can have featured images before swicth to a theme that supports them.
 
     function rv_auto_featured_image_add_thumbnail( $post ) {
 
         $already_has_thumb = has_post_thumbnail();
         $post_type = get_post_type( $post->ID );
         $exclude_types = array( '' );
         $exclude_types = apply_filters( 'eat_exclude_types', $exclude_types );
 
         // do nothing if the post has already a featured image set
         if ( $already_has_thumb ) {
             return;
         }
 
         // do the job if the post is not from an excluded type
         if ( ! in_array( $post_type, $exclude_types ) ) {
             // get first attached image
             $img = rv_auto_featured_image_catch_that_image( $post );
             $attachment_id = attachment_url_to_postid( $img );
             
             if ( $attachment_id ) {
                 // add attachment ID
                 add_post_meta( $post->ID, '_thumbnail_id', $attachment_id, true );
             }
             
         }
     }
 
     // set featured image before post is displayed (for old posts)
     add_action( 'the_post', 'rv_auto_featured_image_add_thumbnail' );
 
     // hooks added to set the thumbnail when publishing too
     add_action( 'new_to_publish', 'rv_auto_featured_image_add_thumbnail' );
     add_action( 'draft_to_publish', 'rv_auto_featured_image_add_thumbnail' );
     add_action( 'pending_to_publish', 'rv_auto_featured_image_add_thumbnail' );
     add_action( 'future_to_publish', 'rv_auto_featured_image_add_thumbnail' );


    function rv_auto_featured_image_catch_that_image( $post ) {
        // Find the images in the post_content.
        $output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
        $first_img = isset( $matches[1][0] ) ? $matches[1][0]:'';

        // if no images found, do nothing
        if ( empty ( $first_img ) ) {
            return false;
        }
        return $first_img;
    }
}