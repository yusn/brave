<article class="inner type-page mt40 mb40" itemscope itemtype="//schema.org/Article">
	<div class="mb30">
		<?php the_title( '<h1 class="f28" itemprop="headline">', '</h1>' ); ?>
		<?php edit_post_link( __( 'Edit', 'brave' ), '<div class="mt10 c4 clear">', '</div>' ); ?>
	</div>
	<div class="content entry-content" itemprop="articleBody">
		<?php the_content(); ?>
	</div>
</article>