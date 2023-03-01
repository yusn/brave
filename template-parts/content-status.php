<article class="<?php if ( ! is_single() ) { ?>item item-s-b <?php } ?><?php if ( is_single() ) { ?>bcv-in mt40 mb40 inner <?php } ?>post fat bg-w clear" itemscope itemtype="//schema.org/BlogPosting">
	<header class="status-meta mb5 clear">
		<div class="meta">
			<div class="c4">
				<span datetime="<?php the_time('H:i'); ?>" pubdate="<?php the_date(); ?>" itemprop="datePublished"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . '前'; ?><?php if (get_post_status($post->ID) === 'private') { ?><span class="ml">私有</span><?php } ?></span><?php get_brave_post_meta(); ?><?php get_brave_post_device(); ?>
			</div>
			<div class="status-name c5 none" itemprop="author" itemscope itemtype="//schema.org/Person"><?php the_author(); ?></div>
		</div>
		
	</header>
	
	<div class="content status-content entry-content mb10" itemscope itemprop="articleBody">
		<?php the_content(__('Continue reading','brave' )); ?>
	</div><!-- .status-content -->
	<footer class="meta-bottom c4">
		<?php echo get_simple_likes_button( get_the_ID() ); ?><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml">', '</span>' ); ?>
	</footer>
</article>
<?php if ( is_single() && ! is_paged() ) { ?>
	<?php get_template_part('p/l', 'status'); ?>
<?php } ?>