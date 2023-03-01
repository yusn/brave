<?php get_header(); ?>
	<section class="container clear" role="main">
		<?php if ( have_posts() ) : ?>
			<header class="search-meta pt20 pb20 mb20 clear">
				<div class="inner">
					<?php
						the_archive_title( '<h1 style="font-size:16px" itemprop="headline">', '</h1>' );
						the_archive_description( '<div class="mt10 c4">', '</div>' );
					?>
				</div>
			</header>
		<div class="archive-list">
		<?php while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/content', 'search');
			endwhile;
            	if(function_exists('wp_pagenavi')) :
					wp_pagenavi();
				else :
				 	get_brave_content_nav( 'nav-below' );
				endif;
		?>
		</div>
		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>
		<?php endif; ?>
	</section>
<?php get_footer(); ?>