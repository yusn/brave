<article class="item post mb20 pb20 bbs clear" itemscope itemtype="//schema.org/SearchResultsPage">
	<div class="f14 c4 mb10">
		<span datetime="<?php the_time('H:i'); ?>" pubdate="<?php the_date(); ?>" itemprop="datePublished">
			<?php get_brave_search_date(); ?>
		</span><span class="ml12"><?php echo get_post_format_string( get_post_format() ); ?></span><?php
			if ( has_post_format('status') ) {
				get_brave_post_meta();
				get_brave_post_device();
			}
		?>
		<?php 
			$post_feature_video = get_post_meta($post->ID, 'post_feature_vid', true);
			$post_feature_img = get_post_meta($post->ID, 'post_feature_img', true);
		?>
	
	<?php if ( $post_feature_video ) : ?>
		<a class="c5" href="<?php the_permalink() ?>">
			<video class="mt10" style="width:100%" muted loop preload autoplay playsinline webkit-playsinline <?php if ( $post_feature_img ) : ?>poster="<?php echo esc_url( $post_feature_img ); ?>"<?php endif; ?>>
				<source src="<?php echo esc_url( $post_feature_video ); ?>" type="video/mp4">
			</video>
		</a>
	<?php elseif ( has_post_thumbnail() ) : ?>
		<a class="c5" href="<?php the_permalink() ?>">
			<?php $sight_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' ); ?>
			<img class="mb5" src="<?php echo esc_url($sight_image[0]); ?>">
		</a>
	<?php endif; ?>
	</div>
	
	<div class="excerpt" itemscope itemprop="articleBody">
		<?php
			if ( $post_feature_video ) {
				the_title( sprintf( '<h2 class="fwt srch" rel="bookmark"><a href="%s" >[视频]', esc_url( get_permalink() ) ), '</a></h2>' );
			} else {
				the_title( sprintf( '<h2 class="fwt srch" rel="bookmark"><a href="%s" >', esc_url( get_permalink() ) ), '</a></h2>' );
			};
		?>
		<?php echo get_brave_excerpt(100); ?>
	</div>
</article>