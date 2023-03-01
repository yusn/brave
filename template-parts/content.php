<?php if ( is_home() ) : ?>
	<article class="item post mb40 pb40 bbs" itemscope itemtype="//schema.org/Article">
		<?php if ( is_sticky() && is_home() && ! is_paged() ) : ?>
			<div class="featured-post">
				<?php esc_html__( 'Featured post', 'brave' ); ?>
			</div>
		<?php endif; ?>
		<header class="entry-header">
			<div class="meta c4">
				<span datetime="<?php the_time('H:i'); ?>" pubdate="<?php the_date(); ?>" itemprop="datePublished"><?php the_time(__('Y-m-d' ,'brave')); ?></span><span class="ml12 none" itemprop="author" itemscope itemtype="//schema.org/Person"><?php the_author(); ?></span><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml12">', '</span>' ); ?>
			</div><!-- .meta -->
			<?php the_title( sprintf( '<h2 class="f28 mt5 mb20" itemprop="headline"><a href="%s" class="title" rel="bookmark" itemprop="url">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
			<?php
				if ( ! post_password_required() && ! is_attachment() ) :
					get_brave_post_thumbnail();
				endif;
			?>
		</header><!-- entry-header -->
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
	</article>
<?php elseif ( is_single() ) : ?>
	<article class="post bg-w clear" itemscope itemtype="//schema.org/Article">
		<?php
			$post_feature_vid = get_post_meta($post->ID, 'post_feature_vid', true);
			$post_feature_img = get_post_meta($post->ID, 'post_feature_img', true);
		?>
		<?php if ( $post_feature_vid ) { ?>
			<header class="video-header mb40">
			<video class="sv" muted loop preload autoplay playsinline webkit-playsinline <?php if ( $post_feature_img ) : ?>poster="<?php echo esc_url( $post_feature_img ); ?>"<?php endif; ?>>
				<source src="<?php echo esc_url( $post_feature_vid ); ?>" type="video/mp4">
			</video>
				<div class="sv-inner">
					<div class="inner">
						<?php the_title( '<h1 class="entry-title fwb" itemprop="headline">', '</h1>' ); ?>
						<div class="pt10 c0">
							<span datetime="<?php the_time('H:i'); ?>" pubdate="<?php the_date(); ?>" itemprop="datePublished"><?php the_time(__('Y-m-d' ,'brave')); ?></span><span class="ml12"><?php echo get_post_format_string( get_post_format() ); ?></span><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml12">', '</span>' ); ?>
						</div><!-- .meta -->
					</div>
				</div>
			</header>
		<?php } else { ?>
			<header class="entry-header fu mb40" <?php if ( has_post_thumbnail() ) : ?><?php $post_feature_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' ); ?> style="background-image:url('<?php echo esc_url($post_feature_img[0]); ?>');background-size:cover"<?php endif; ?>>
				<div class="sv-inner">
					<div class="inner">
						<?php the_title( '<h1 class="entry-title fwb" itemprop="headline">', '</h1>' ); ?>
						<div class="pt10 c0">
							<span datetime="<?php the_time('H:i'); ?>" pubdate="<?php the_date(); ?>" itemprop="datePublished"><?php the_time(__('Y-m-d' ,'brave')); ?></span><span class="ml12"><?php echo get_post_format_string( get_post_format() ); ?></span><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml12">', '</span>' ); ?>
						</div><!-- .meta -->
					</div>
				</div>
				
			</header><!-- entry-header -->
		<?php } ?>
		<div class="content entry-content inner" itemprop="articleBody">
		<?php display_brave_ad('c'); ?>
		<?php
			the_content( sprintf(
				__( 'Continue reading %s', 'brave' ),
				the_title( '<span>', '</span>', false ) 
			) );
		?>
		<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'brave' ), 'after' => '</div>' ) ); ?>
		</div>
		<?php display_brave_ad('a'); ?>
	</article>
	<?php get_template_part('p/l', 'list'); ?>
<?php endif; ?>