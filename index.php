<?php get_header(); ?>
<div class="container egret clear" role="main">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) : the_post();
			get_template_part( 'template-parts/content', get_post_format() );
		endwhile;
	else :
		get_template_part( 'template-parts/content', 'none' );
	endif;
	?>
	</div>
	<?php get_brave_content_nav( 'nav-below' ); ?>
<?php get_footer(); ?>