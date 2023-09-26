<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php extract(pick_array(get_brave_config('basic'), ['asset_uri', 'site_name', 'home_url', 'logo'])); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<link rel="profile" href="//gmpg.org/xfn/11" />
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0, user-scalable=yes'/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="<?php echo $site_name; ?>">
<link rel="apple-touch-startup-image" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2)" href="<?php echo $asset_uri; ?>/launch-m.png">
<link rel="apple-touch-startup-image" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)" href="<?php echo $asset_uri; ?>/launch-m.png">
<title><?php wp_title( '-', true, 'right' ); ?></title>
<?php
// 保持入参 $key 为函数内部要取的变量名
function get_brave_seo($key) {
	global $post;
	
	$keywords = ''; $description = ''; $author = '';
	
	/* 获取用户名称, query 外层需要借助 get_queried_object
	 * https://developer.wordpress.org/reference/functions/get_queried_object/
	**/
	$obj = get_queried_object();
	if($obj && isset($obj->ID)){
		$author = get_the_author_meta('display_name', $obj->post_author); 
	}
	
	if (is_home() || is_front_page()) {
		$description = ''; // 首页描述
		$keywords = ''; // 首页关键字
	}
	
	if (is_single()) {
		$description = get_brave_excerpt('200');
		$keywords = '';
		$tags = wp_get_post_tags($post->ID);

		foreach ($tags as $tag ) {
			$keywords = $keywords . $tag->name . ", ";
		}
	}
	return $$key;
}
?>
<meta name="description" content="<?php echo get_brave_seo('description'); ?>" /> 
<meta name="keywords" content="<?php echo get_brave_seo('keywords'); ?>" />
<?php
if ( is_singular() && pings_open() ) {
	printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
}
?>
<!-- Open Graph data -->
<?php if ( is_single() || is_page() ) { ?>
	<?php if ( is_page() ) { ?>
	<meta property="og:type" content="page" />
	<?php } else { ?>
	<meta property="og:type" content="article" />
	<?php } ?>
	<meta property="og:title" content="<?php wp_title( '-', true, 'right' ); ?>" />
	<meta property="article:author" content="<?php echo get_brave_seo('author'); ?>" />
	<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>" />
	<meta property="article:published_time" content="<?php echo get_post_time('Y-m-d T H:i:s'); ?>" />
	<?php
		if ( has_post_thumbnail() ) {
			$sight_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' ); 
			echo '<meta property="og:image" content="' . esc_attr( $sight_image[0] ) . '" />';
		} else {
			echo '<meta property="og:image" content="' . $asset_uri . '/app-touch-icon.png" />';
		}
	?>
<?php } elseif ( is_page() || is_archive() ) { ?>
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php wp_title( '-', true, 'right' ); ?>" />
	<?php if ( is_home() ) { ?>
		<meta property="og:url" content="<?php echo $home_url; ?>" />
	<?php } else { ?>
		<meta property="og:url" content="<?php echo esc_url( get_permalink() ); ?>" />
	<?php } ?>
	<meta property="og:image" content="<?php echo $asset_uri; ?>/app-touch-icon.png" />
<?php } ?>

<link rel="shortcut icon" href="<?php echo $asset_uri; ?>/favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo $asset_uri; ?>/shortcut-icon.png">
<link rel="apple-touch-icon" href="<?php echo $asset_uri; ?>/app-touch-icon.png">

<link rel="stylesheet" id="style" href="<?php echo $asset_uri; ?>/style.min.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?php echo $asset_uri; ?>/like.css" type="text/css" media="all" />
<!--[if lt IE 9]>
<script src="<?php echo $asset_uri; ?>/html5.js" type="text/javascript"></script>
<![endif]-->
<!--[if IE]>
<link rel="stylesheet" href="<?php echo $asset_uri; ?>/ie.css" type="text/css" media="all" />
<![endif]-->
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header id="header" class="bg-w" role="banner">
	<div class="headerin">
		<div class="site-title frog" itemscope itemtype="//schema.org/WPHeader">
			<?php
				if (is_front_page() && is_home()) {
					$element = 'h1';
				} else {
					$element = 'span';
				}
				echo '<a href="' . $home_url . '"><' . $element . ' class="site-name" title="' . $site_name . '">' . $site_name . '</' . $element . '></a>';
			?>
			<div id="menu-toggle"><span id="toggle" class="i-menu touch-easy"></span></div>
			<?php 
				if (!empty($logo)) :
			?>
				<a id="site-logo" href="/" title="<?php echo $site_name; ?>">
				<img style="display:inline-block;max-width:60px;margin: 0 auto;vertical-align:middle" src= "<?php echo $logo ?>"></a>
			<?php else : ?>
				<a class="i-frog" id="site-logo" href="/" title="<?php echo $site_name; ?>"></a>
			<?php endif; ?>
		</div>
		<ul id="menu" class="menu" role="navigation" itemscope itemtype="//schema.org/SiteNavigationElement">
			<div class="searchbox">
				<form id="search" action="<?php echo $home_url; ?>" method="get">
					<span id="menu-search" class="i-search c3"></span>
					<label title="Search" for="search">
						<input class="inp s-text" type="text" name="s" placeholder="..." value="" />
					</label>
				</form>
			</div>
			<?php wp_nav_menu(array( 'theme_location' => 'primary', 'menu_class' => 'nav-menu', 'container' => '') ); ?>
		</ul>
	</div>
</header>
<div class="wrapper mt">