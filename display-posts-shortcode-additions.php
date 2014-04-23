<?php
/**
 * Plugin Name: Display Posts Shortcode Additions
 * Description: Actually tag support only. Display a maximum of 3 tags within [display-posts] shortcode listings. Excerpts have to be enabled by <code>include_excerpt="1"</code>. Tags are displayed just before the excerpt dash separator. No options.
 * Version: 0.2
 * Author: Frank Stürzebecher
 * Author URI: http://netzklad.de
 *
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

!defined( 'ABSPATH' ) && exit;

define( 'MAX_TAGS_FOR_DPSHORTCODE', 3 );

/**
 * Implementation of filter 'display_posts_shortcode_output' which
 * is applied by plugin Display Posts Shortcode.
 */
function tags_for_dpshortcode( $output, $original_atts, $image, $title, $date, $excerpt, $inner_wrapper, $content, $class ) {

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
add_filter( 'display_posts_shortcode_output', 'tags_for_dpshortcode', 10, 9 );
