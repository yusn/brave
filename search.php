<?php get_header(); ?>
	<div class="clear" role="main" itemscope itemtype="//schema.org/SearchResultsPage">
		
		<?php if ( ! is_user_logged_in() && empty( get_search_query() ) ) { ?>
			<div class="archive-list mt20">
				<article class="error inner mt40 mb40 pb40">
					<header class="entry-header mb30">
						<h1 class="f28"><?php _e( '搜索关键字不能为空', 'brave' ); ?></h1>
					</header>
					<div class="content entry-content">
						<div class="mb40"><?php _e( '请输入关键字重新搜索。', 'brave' ); ?></div>
					</div><!-- .entry-content -->
				</article>
			</div>
		<?php } else { ?>
			<?php if ( have_posts() ) { ?>
				<header class="search-meta pt20 pb20 clear">
					<div class="inner">
						<div style="font-size:16px;color:#8C8C8C" itemprop="headline">
							<?php $count = $wp_query->found_posts; ?>
							<?php if ( ! empty( get_search_query() ) ) : ?>
							找到<?php printf( _n( '%d', '%d', $count, 'brave' ), $count); ?>个含有 <?php printf( __( '%s', 'brave' ), '[' . get_search_query() . ']' ); ?> 的结果
							<?php elseif ( is_user_logged_in() && empty( get_search_query() ) ) : ?>
							找到<?php printf( _n( '%d', '%d', $count, 'brave' ), $count); ?>个结果
							<?php endif; ?>
						</div>
					</div>
				</header>
				<div class="container content archive-list mt20">
				<?php /* Start the Loop */
					while ( have_posts() ) : the_post();
						get_template_part( 'template-parts/content', 'search');
					endwhile;
					get_brave_content_nav( 'nav-below' );
				?>
			<?php } else {
				get_template_part( 'template-parts/content', 'none' );
			} ?>
		<?php } ?>
		</div>
	</div>
<?php get_footer(); ?>