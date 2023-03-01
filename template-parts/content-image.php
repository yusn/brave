<?php if ( is_single() ) { ?>
	<article class="post pb40">
		<header class="entry-header fu" <?php if ( has_post_thumbnail() ) : ?><?php $sight_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' ); ?> style="background-image:url('<?php echo esc_url($sight_image[0]); ?>');background-size:cover"<?php endif; ?>>
			<div class="inner">
			<div class="get">
				<h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>
				<div class="meta mb10 c0">
					<span itemprop="author" itemscope itemtype="//schema.org/Person"><?php the_author(); ?></span><span class="ml12" datetime="<?php the_time('H:i'); ?>" pubdate="<?php the_date(); ?>" itemprop="datePublished"><?php the_time(__('Y-m-d' ,'brave')); ?></span><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml12">', '</span>' ); ?>
				</div>
			</div>
			</div>
		</header>
		<div class="content gallery-content leo inner clear" itemprop="articleBody">
			<?php the_content( __( 'Continue reading', 'brave' ) ); ?>
		</div>
		<div class="clear"></div>
<?php } else { ?>
	<article class="item post pb40 vicuna clear">
		<header class="entry-header pt40 pb40" <?php if ( has_post_thumbnail() ) : ?><?php $sight_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' ); ?> style="background-image:url('<?php echo esc_url($sight_image[0]); ?>');background-size:cover"<?php endif; ?>>
			<div class="inner">
				<div class="get">
					<h2 class="entry-title c0 center" itemprop="headline">
						<a href="<?php the_permalink(); ?>" class="title" title="<?php echo esc_attr( sprintf( __( '%s', 'brave' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
					</h2>
					<div class="meta inner c0 center">
						<span itemprop="author" itemscope itemtype="//schema.org/Person"><?php the_author(); ?></span><span class="ml12" datetime="<?php the_time('H:i'); ?>" pubdate="<?php the_date(); ?>" itemprop="datePublished"><?php the_time(__('Y-m-d' ,'brave')); ?></span><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml12">', '</span>' ); ?>
					</div>
				</div>
			</div>
			<div class="gallery-content inner" itemprop="articleBody">
				<?php the_content( __( 'Continue reading', 'brave' ) ); ?>
			</div>
		</header>
		<div class="inner pb40 bbs"></div>
<?php } ?>
	</article>