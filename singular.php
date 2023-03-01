<?php get_header(); ?>
	<div class="clear" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php if ( is_single() ) : ?>
				<?php get_template_part( 'template-parts/content', get_post_format() ); ?>
				<?php
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
				?>
			<?php elseif ( is_page() ) : ?>
				<?php get_template_part( 'template-parts/content', 'page' ); ?>
			<?php endif; ?>
		<?php endwhile; ?>
	</div>
<?php get_footer(); ?>