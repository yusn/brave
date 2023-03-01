<?php get_header(); ?>
	<div class="bg-w clear" role="main">
		<div class="inner">
			<?php while ( have_posts() ) : the_post(); ?>
				<article class="img-att mt40 mb40">
					<header class="entry-header">
						<h1 class="f28"><?php the_title(); ?></h1>
						<div class="meta mt10 mb10 c3">
							<?php
							$metadata = wp_get_attachment_metadata();
							if ( $metadata ) {
								printf( '<span datetime="%3$s" pubdate>%4$s</span><span class="ml12" title="%1$s">%1$s</span>@%5$s&times;%6$s',
									esc_html_x( 'full size', 'Used before full size attachment link.', 'brave' ),
									esc_url( wp_get_attachment_url() ),
									esc_attr( get_the_date( 'c' ) ),
									esc_html( get_the_date('Y-m-d') ),
									absint( $metadata['width'] ),
									absint( $metadata['height'] ),
									get_the_author_link()
								);
							}
						?><?php edit_post_link( __( 'Edit', 'brave' ), '<span class="ml12">', '</span>' ); ?>
						</div>
					</header>
					
					<div class="content entry-content">
						<div class="center">
						<?php
							$image_size = apply_filters( 'brave_attachment_size', 'medium' );
							echo wp_get_attachment_image( get_the_ID(), $image_size );
						?>
						</div>
				</article>
			<?php endwhile; ?>
		</div>
		<!--
		<nav class="po pt20 bts clear">
			<div class="inner">
				<div class="nav-previous"><?php previous_image_link( $size = 'thumbnail', $text = false ) ?></div>
				<div class="nav-next"><?php next_image_link( $size = 'thumbnail', $text = false ); ?></div>
			</div>
		</nav>
		-->
	</div>
<?php get_footer(); ?>