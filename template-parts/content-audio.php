<article class="<?php if ( ! is_single() ) { ?>item item-s-b <?php } ?><?php if ( is_single() ) { ?>bcv-in mt40 mb40 inner <?php } ?>post fat bg-w" itemscope itemtype="//schema.org/BlogPosting">
	<div class="status-main">
		
		<?php
		if ( is_single() ) :
			the_title( '<h1 class="c5 fwt mb10" itemprop="headline">[段子]', '</h1>' );
		else :
			the_title( sprintf( '<h2 class="fwt mb10" itemprop="headline"><a class="c5" href="%s" rel="bookmark" itemprop="url">[段子]', esc_url( get_permalink() ) ), '</a></h2>' );
		endif;
	?>
		<div class="content status-content entry-content" itemprop="articleBody">
			<?php the_content(__('Continue reading','brave' )); ?>
		</div><!-- .status-content -->
	<footer class="mt10 pt10 btd c4 clear">
		<span class="left f14">
			<span class="mr" datetime="<?php the_time('H:i'); ?>" pubdate="<?php the_date(); ?>" itemprop="datePublished"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . '前'; ?></span><?php echo get_simple_likes_button( get_the_ID() ); ?><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml">', '</span>' ); ?>
		</span>
		<span class="right">
		</span>
	
	</footer>
	</div>
	
</article>
<?php if ( is_single() && ! is_paged() ) { ?>
	<?php get_template_part('p/l', 'audio'); ?>
<?php } ?>