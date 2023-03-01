<?php get_header(); ?>
	<div class="clear" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<?php if ( is_single() ) : ?>
				<?php get_template_part( 'template-parts/content', get_post_format() ); ?>
				<div class="p-n">
					<div class="inner pt20 pb20 bts bbs clear">
						<span class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'brave' ) . '</span> %title' ); ?></span>
						<span class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'brave' ) . '</span>' ); ?></span>
					</div>
				</div>
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
<?php //get_sidebar(); ?>
<?php get_footer(); ?>