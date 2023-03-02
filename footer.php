</div><!-- .wrapper -->
<footer class="footer bts" role="contentinfo" itemscope itemtype="//schema.org/WPFooter">
	<div class="footerin clear">
		<div class="f-name">
			<?php 
				$logo = get_brave_basic_config('logo');
				if (!empty($logo)) :
			?>
				<a id="logo" href="/" title="<?php get_bloginfo('name'); ?>">
					<img style="display:inline-block;max-width:60px;margin: 0 auto" src= "<?php echo $logo ?>">
				</a>
			<?php else : ?>
				<span class="logo i-frog left"></span>
			<?php endif; ?>
		</div>
		<div class="f-items mt5">&copy;<?php echo date("Y"); ?> <?php echo get_bloginfo('name'); ?> Some Rights Reserved
			<div class="mt5"><a href="//github.com/yusn/Brave" class="mr">Brave</a><a href="https://beian.miit.gov.cn"><?php echo get_brave_basic_config('beian'); ?></a></div>
        </div><!-- .f-items -->
	</div><!-- .footerin -->
</footer><!-- #footer -->
<?php wp_footer(); ?>
</body>
</html>