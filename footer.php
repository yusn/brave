</div><!-- .wrapper -->
<footer class="footer bts" role="contentinfo" itemscope itemtype="//schema.org/WPFooter">
	<div class="footerin clear">
		<div class="f-name">
			<?php 
				// 获取变量 $logo, $beian
				extract(pick_array(get_brave_config('basic'), ['logo', 'beian', 'site_name']));
				if (!empty($logo)) :
			?>
				<a id="logo" href="/" title="<?php echo $site_name; ?>">
					<img style="display:inline-block;max-width:60px;margin: 0 auto" src= "<?php echo $logo ?>">
				</a>
			<?php else : ?>
				<span class="logo i-frog left"></span>
			<?php endif; ?>
		</div>
		<div class="f-items mt5">&copy;<?php echo date("Y"); ?> <?php echo $site_name; ?> Some Rights Reserved
			<div class="mt5"><a href="//github.com/yusn/Brave" class="mr">Brave</a><a href="https://beian.miit.gov.cn"><?php echo $beian; ?></a></div>
        </div><!-- .f-items -->
	</div><!-- .footerin -->
</footer><!-- #footer -->
<?php wp_footer(); ?>
</body>
</html>