<?php
/**
 * Template Name: Blog
 */
get_header(); ?>
<?php 
	if ( current_user_can('administrator') ) {
		$post_status_array = array('publish','private');
	} else {
		$post_status_array = array('publish');
	}
	$args = array(
				'post_type'      => 'post',
				'post_status'    => $post_status_array,
				'posts_per_page' => 5,
				'order'          => 'DESC',
				'paged'          => $paged,
				'tax_query'      => array(
					array(
						'taxonomy' => 'post_format',
						'field'    => 'slug',
						'terms'    => get_brave_config('query', 'Blog.terms_in'),
					),
				),
			);
	query_posts($args);
?>
<div class="container owl clear" role="main">
	<?php
		while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/content', get_post_format() );
		endwhile;
	?>
</div>
<?php get_brave_content_nav( 'next' ); ?>
<?php get_footer(); ?>