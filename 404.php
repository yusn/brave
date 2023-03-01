<?php get_header(); ?>

<div class="bg-w clear" role="main">
		<article class="n-f-404 center inner mt20 mb40">
			<div class="content entry-content">
				<h1 style="font-size:40px;font-size:2.5rem"><?php esc_html_e( '404', 'brave' ); ?></h1>
				<div class="mt10 mb20"><?php esc_html_e( '抱歉，页面不存在，何不试试搜索功能', 'brave' ); ?></div>
				<?php get_search_form(); ?>
			</div>
		</article>
	</div>
<?php get_footer(); ?>