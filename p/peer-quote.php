<?php
	$compare_role = 'chat';
	$query_arg = get_brave_peer_post_query($compare_role);
	if (empty($query_arg)) {
		return;
	}
	query_posts($query_arg);
	if (have_posts()) :
?>
<div class="lhn pb30 bg-w">
	<ul class="inner">
		<h3 class="fwt mb10 c5"><?php get_brave_peer_title($compare_role); ?></h3>
		<?php while ( have_posts() ) : the_post(); ?>
			<li class="lsn bts">
				<a class="srl" href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
					<div class="c4 r-date"><?php the_time('Y-m-d'); ?></div>
				</a>
			</li>
		<?php endwhile; ?>
	</ul>
</div>
<?php 
	endif;
	wp_reset_query();
?>