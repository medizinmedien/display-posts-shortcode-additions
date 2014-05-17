<?php
/**
 * Plugin Name: Display Posts Shortcode Additions
 * Description: 1.) Display up to 3 post tags within <code>[display-posts]</code> shortcode listings when <code>include_excerpt="true"</code>. 2.) Individual image sizes are created on-the-fly by e.g. <code>image_size="300x150"</code>.
 * Version: 0.3
 * Author: Frank Stürzebecher
 * Author URI: http://netzklad.de
 *
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

!defined( 'ABSPATH' ) && exit;

define( 'MAX_TAGS_FOR_DPSHORTCODE', 3 );

/**
 * Display up to 3 tags in post listings when excerpts are requested.
 */
function dspa_post_tags( $output, $original_atts, $image, $title, $date, $excerpt, $inner_wrapper, $content, $class ) {

	$posttags = get_the_tags();

	if ( $posttags ) {
		$excerpt_dash = '<span class="excerpt-dash">-</span>';
		$tag_dash     = '<span class="tag-dash">-</span>';
		$tags_output = '';
		$cnt = 1;
		foreach( $posttags as $tag ) {
			if( $cnt > MAX_TAGS_FOR_DPSHORTCODE )
				break;
			$tags_output .= '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a> ';
			$cnt++;
		}
		$output = str_replace(
			$excerpt_dash,
			$tag_dash . ' <span class="dpshortcode-tags">' . $tags_output . '</span>' . $excerpt_dash,
			$output
		);
	}

	return $output;
}
add_filter( 'display_posts_shortcode_output', 'dspa_post_tags', 10, 9 );


/**
 * If a given image_size attribute has a value like "200x120" then, if not
 * existent, the image will be created and displayed without scaling.
 * Works independently from usual WordPress image sizes.
 */
function dpsa_resize_image( $output, $original_atts, $image, $title, $date, $excerpt, $inner_wrapper, $content, $class ) {

	// Quit, if we don't have a custom image size like e.g. "200x120".
	if( !count( $img_w_h = explode( 'x', $original_atts['image_size'] ) ) == 2 ) {
		return $output;
	}
	elseif( !is_numeric( $img_w_h[0] ) || !is_numeric( $img_w_h[1] ) ) {
		return $output;
	}

	global $post;
	require_once('inc/aq_resizer.php');
	$thumb = get_post_thumbnail_id();
	
	// Get URL to image ('full' for best scaling results).
	$img_url = wp_get_attachment_url( $thumb, 'full' );

	// Params in order: base image url, width, height, crop, return url, upscale.
	// See for more: https://github.com/syamilmj/Aqua-Resizer/wiki
	$new_img = aq_resize( $img_url, $img_w_h[0], $img_w_h[1], true, true, true );

	$new_img_html = '<a class="image" href="' . get_permalink() . '"><img class='
		. '"attachment-' . esc_attr( $original_atts['image_size'] ) . '" src="'
		. $new_img . '" alt="'.get_the_title().'" width="' . esc_attr( $img_w_h[0] )
		. '" height="' . esc_attr( $img_w_h[1] ) . '" /></a> ';

	return str_replace( $image, $new_img_html, $output );
}
add_filter( 'display_posts_shortcode_output', 'dpsa_resize_image', 10, 9 );

