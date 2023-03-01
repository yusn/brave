<article class="<?php if ( ! is_single() ) { ?>item <?php } ?><?php if ( is_single() ) { ?>bcv-in mt40 <?php } ?>post inner">
	<header class="mb10"><?php _e( 'Link', 'brave' ); ?></header>
	<div class="content entry-content">
		<?php the_content( __( 'Continue reading', 'brave' ) ); ?>
	</div>
	<footer class="meta c3">
		<span class="meta-author"><?php the_author_posts_link(); ?></span><?php the_time(__('Y-m-d' ,'brave')); ?><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml12">', '</span>' ); ?>
	</footer>
</article>