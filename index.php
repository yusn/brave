<?php get_header(); ?>
	<?php get_brave_content_nav('prev'); ?>
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
	<?php get_brave_content_nav( 'next' );
	?>
<?php get_footer(); ?>