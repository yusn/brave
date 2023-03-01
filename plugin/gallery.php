<?php
// Custom filter function to modify default gallery shortcode output
function brave_post_gallery( $output, $attr ) {
	// Initialize
	global $post, $wp_locale;
	// Gallery instance counter
	static $instance = 0;
	$instance++;
	// Validate the author's orderby attribute
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( ! $attr['orderby'] ) unset( $attr['orderby'] );
	}

	// Get attributes from shortcode
	extract( shortcode_atts( array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'li',
		'icontag'    => 'div',
		'captiontag' => 'div',
		'columns'    => 4,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
	), $attr ) );
	// Initialize
	$id = intval( $id );
	$attachments = array();
	if ( $order == 'RAND' ) $orderby = 'none';
	if ( ! empty( $include ) ) {
		// Include attribute is present
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array( 'include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );

		// Setup attachments array
		foreach ( $_attachments as $key => $val ) {
			$attachments[ $val->ID ] = $_attachments[ $key ];
		}
	} else if ( ! empty( $exclude ) ) {
		// Exclude attribute is present 
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );

		// Setup attachments array
		$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
	} else {
		// Setup attachments array
		$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
	}
	if ( empty( $attachments ) ) return '';
	// Filter gallery differently for feeds
	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment ) $output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
		return $output;
	}
	// Filter tags and attributes
	$itemtag = tag_escape( $itemtag );
	$captiontag = tag_escape( $captiontag );
	$columns = intval( $columns );
	$itemwidth = $columns > 0 ? floor( 100 / $columns ) : 100;
	$float = is_rtl() ? 'right' : 'left';
	$selector = "gallery-{$instance}";

	// Filter gallery CSS
	$output = apply_filters( 'gallery_style', "
		<style type='text/css'>
			#{$selector} {margin:12px -6px 24px}#{$selector} li{padding:0;border:0 none}#{$selector} li:before{content:'';}#{$selector} .gallery-item {float:{$float}}.gallery img{margin-top:0}.gallery-img{padding:6px}.gallery-caption{line-height:1.5}.gallery-columns-5 .gallery-item:nth-child(5n+1){clear:none}
			@media screen and (min-width:1024px) {.gallery-item{width:25%}}
			@media screen and (max-width:1023px) {.gallery-item{width:25%}}
			@media screen and (max-width:719px) {.gallery-item{width:50%}}
			</style>
		<!-- see gallery_shortcode() in wp-includes/media.php -->
		<ul id='$selector' class='gallery galleryid-{$id} c4 mb20 clear'>"
	);
	// Iterate through the attachments in this gallery instance
	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
		// Attachment link
		$link = isset( $attr['link'] ) && 'file' == $attr['link'] ? wp_get_attachment_link( $id, $size, false, false ) : wp_get_attachment_link( $id, $size, true, false ); 
		// Start itemtag
		$output .= "<{$itemtag} class='gallery-item'>";
		$output .= "
		<{$icontag} class='gallery-img'>
			$link
		</{$icontag}>";
		if ( $captiontag && trim( $attachment->post_excerpt ) ) {
			$output .= "<{$captiontag} class='gallery-caption f14'>".wptexturize($attachment->post_excerpt)."</{$captiontag}>";
		}
		// End itemtag
		$output .= "</{$itemtag}>";
		// Line breaks by columns set
		if($columns > 0 && ++$i % $columns == 0) $output .= '';
	}
	// End gallery output
	$output .= "</ul>";
	return $output;
}
// Apply filter to default gallery shortcode
add_filter( 'post_gallery', 'brave_post_gallery', 10, 2 );
?>