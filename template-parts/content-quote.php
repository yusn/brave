<article class="<?php if ( ! is_single() ) { ?>item <?php } ?><?php if ( is_single() ) { ?>bcv-in mt40 <?php } ?>post mb40 pt40 pb40 bp" itemscope itemtype="//schema.org/Article">
	<?php
		if ( is_single() ) :
			the_title( '<h1 class="f28 mb20" itemprop="headline">', '</h1>' );
		else :
			the_title( sprintf( '<h2 class="f28 mb20" itemprop="headline"><a class="title" href="%s" rel="bookmark" itemprop="url">', esc_url( get_permalink() ) ), '</a></h2>' );
	endif; ?>
		<?php if ( is_search() ) : ?>
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div>
		<?php else : ?>
			<div class="content entry-content" itemprop="articleBody">
				<?php the_content( __( 'Continue reading', 'brave' ) ); ?>
			</div>
		<?php endif; ?>
		
	<footer class="fat-meta mt30 pt20 btd c4 f14 clear">
		<span class="left role"><?php get_brave_role(); ?></span><span class="left ml12" datetime="<?php the_time('H:i'); ?>" pubdate="<?php the_date(); ?>" itemprop="datePublished"><?php get_brave_age(); ?><span class="ml12"><?php the_time('H:i'); ?><?php get_brave_post_device(); ?></span></span><span class="right"></span><?php edit_post_link(__( 'Edit', 'brave' ), '<span class="ml12 none">', '</span>' ); ?>
	</footer>
</article>

<?php if ( is_single() && ! is_paged() ) { ?>
	<?php get_template_part('p/l', 'quote'); ?>
<?php } ?>