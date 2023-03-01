<article class="<?php if ( ! is_single() ) { ?>item <?php } ?><?php if ( is_single() ) { ?>bcv-in mt40 <?php } ?>post mb40 pt40 pb40 bp" itemscope itemtype="//schema.org/Blog">
	<?php
		if ( is_single() ) :
			the_title( '<h1 class="f28 mb20 fwt" itemprop="headline">', '</h1>' );
		else :
			the_title( sprintf( '<h2 class="f28 mb20" itemprop="headline"><a class="title" href="%s" rel="bookmark" itemprop="url">', esc_url( get_permalink() ) ), '</a></h2>' );
		endif;
	?>
	
	<?php if ( is_search() ) : ?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div>
	<?php else : ?>
		<div class="content entry-content" itemprop="articleBody">
			<?php if ( is_single() ) {
				display_brave_ad('b');
			} ?>
			<?php the_content( __( 'Continue reading', 'brave' ) ); ?>
		</div>
	<?php endif; ?>
		
	<footer class="fat-meta mt30 pt20 btd c4 clear">
		<span class="left" pubdate="<?php the_date(); ?>" datetime="<?php the_time('H:i'); ?>" itemprop="datePublished"><?php the_time(__('Y-m-d' ,'brave')); ?></span><span class="right"></span><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml12">', '</span>' ); ?>
	</footer>
</article>
<?php if ( is_single() && ! is_paged() ) { ?>
	<?php get_template_part('p/l', 'aside'); ?>
<?php } ?>