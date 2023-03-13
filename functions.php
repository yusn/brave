<?php

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter('the_excerpt', 'wpautop');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('the_content', 'wptexturize');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
// remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
remove_action('wp_head', 'wp_print_scripts');
remove_action('wp_head', 'wp_print_head_scripts', 9);
remove_action('wp_head', 'wp_enqueue_scripts', 1);
remove_action('pre_post_update', 'wp_save_post_revision');
// remove_action('wp_head', 'index_rel_link');
// remove_action('wp_head', 'adjacent_posts_rel_link');
// remove_action('wp_head', 'wp_shortlink_wp_head');

// Remove embed
remove_action('rest_api_init', 'wp_oembed_register_route');
remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10, 4);
remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
remove_filter('oembed_response_data', 'get_oembed_response_data_rich', 10, 4);
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');

// Remove REST API
add_filter('rest_enabled', '__return_false');
add_filter('rest_jsonp_enabled', '__return_false');

// Remove wp-json and HTTP header link 
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);

// add_action('wp_footer', 'wp_print_scripts', 5);
// add_action('wp_footer', 'wp_print_head_scripts', 5);

// script 移到底部
add_action('wp_footer', 'wp_enqueue_scripts', 5);

// Remove default gallery style
add_filter('use_default_gallery_style', '__return_false');

// Disable image scaling
add_filter('big_image_size_threshold', '__return_false');

// Disable xmlrpc
// add_filter('xmlrpc_enabled', '__return_false');

// add_filter('wp_image_maybe_exif_rotate', '__return_zero', 10, 2);

// Remove srcset attribute on img label
add_filter('wp_calculate_image_srcset_meta', '__return_null');

// Disable_image_sizes
add_filter('intermediate_image_sizes_advanced', 'disable_image_sizes');
function disable_image_sizes($brave_img_sizes) {
	// unset($brave_img_sizes['thumbnail']);
	// unset($brave_img_sizes['medium']);
	// unset($brave_img_sizes['large']);
	unset($brave_img_sizes['small']);
	unset($brave_img_sizes['medium_large']);
	unset($brave_img_sizes['1536x1536']);
	unset($brave_img_sizes['2048x2048']);
	return $brave_img_sizes;
}

/**
 * 提供获取配置的对外接口
 * $group string
 * $item string
 * @return json
 *
 * 通过 admin-ajax.php 调用的请求示例:
 * 请求参数: action=get_brave_config_intf&group=basic&item=asset_uri
 * 函数解析: 本请求最后会被组成函数调用 get_brave_config(basic, asset_uri);
 */
add_action('wp_ajax_nopriv_get_brave_config_intf', 'get_brave_config_intf');
add_action('wp_ajax_get_brave_config_intf', 'get_brave_config_intf');

function get_brave_config_intf() {
	$group = sanitize_text_field($_REQUEST['group']);
	$item = sanitize_text_field($_REQUEST['item']);
	$response = [];
	// 暂时只响应 asset_uri
	if (!isset($group) || !isset($item) || $group !== 'basic' || $item !== 'asset_uri') {
		exit();
	}
	$func = 'get_brave_config';
	if (function_exists($func)) {
		$response[$item] = $func($group, $item);
		wp_send_json($response);
	}
}

// 脚本配置
function brave_scripts_styles() {
	global $wp_styles;
	$asset_uri = get_brave_config('basic', 'asset_uri');
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_enqueue_script('family', $asset_uri . '/family.min.js');
	}
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
	// 移除不需要的 CSS
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('classic-theme-styles');
}

add_action('wp_enqueue_scripts', 'brave_scripts_styles');

// 移除 css/js 文件的版本号
function remove_wp_ver_css_js($src) {
	if (strpos($src, 'ver='))
		$src = remove_query_arg('ver', $src);
	return $src;
}

add_filter('style_loader_src', 'remove_wp_ver_css_js', 9999);
add_filter('script_loader_src', 'remove_wp_ver_css_js', 9999);

// Set basic options
function brave_setup() {
	load_theme_textdomain('brave');
	add_editor_style();
	add_theme_support('title-tag');
	add_theme_support('automatic-feed-links');
	add_theme_support('post-formats', get_brave_config('basic', 'enable_post_format'));
	register_nav_menu('primary', __('Primary Menu', 'brave'));
	add_theme_support('post-thumbnails');
	add_image_size('large', 1200, '', true);
	add_image_size('medium', 1200, '', true);
	add_image_size('small', 400, '', true);

	set_post_thumbnail_size(1200, '', true);
	add_theme_support('html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'script',
		'style',
	));
}

add_action('after_setup_theme', 'brave_setup');

if (!isset($content_width)) {
	$content_width = 1200;
}

function clear_brave_nav_menu_item_id($item_id, $item, $args) {
	return '';
}

add_filter('nav_menu_item_id', 'clear_brave_nav_menu_item_id', 10, 3);

// 查询过滤
function exclude_brave_post_from_query($query) {
	$query_config_array = get_brave_config('query');
	// 首页排除的格式
	if ($query->is_main_query() && $query->is_home()) {
		$home_tax_query = array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => get_array_key($query_config_array, 'home.terms_not_in'),
				'operator' => 'NOT IN',
			)
		);
		$query->set('tax_query', $home_tax_query);
	}
	// feed 过滤
	if ($query->is_feed()) {
		// feed 排除的格式
		$feed_tax_query = array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => get_array_key($query_config_array, 'feed.terms_not_in'),
				'operator' => 'NOT IN'
			)
		);
		$query->set('tax_query', $feed_tax_query);
		/* feed 输出的日期范围
		$feed_date_query = array(
			array(
				'column' => 'post_date', // column 默认就是 post_date
				'after'  => $feed_config_array['after_date'],
			)
		);
		$query->set('date_query', $feed_date_query);
		*/
	}
	if (!current_user_can('administrator')) {
		// 排除加密的
		$query->set('has_password', false);
		//exclude pages from search results
		if ($query->is_search) {
			$query->set('post_type', 'post');
		}
	}
}

add_action('pre_get_posts', 'exclude_brave_post_from_query');

// 格式化标题
function format_brave_title($title, $sep) {
	global $paged, $page;
	if (is_feed()) {
		return $title;
	}
	$basic_config_array = get_brave_config('basic');
	// Add the site name.
	$title .= $basic_config_array['site_name'];;

	// Add the site description for home/front page.
	$site_description = $basic_config_array['site_description'];
	if ($site_description && (is_home() || is_front_page()))
		$title = $title . ' ' . $sep . ' ' . $site_description;

	//add a page number if necessary.
	if ($paged >= 2 || $page >= 2) {
		$title = $title . ' ' . $sep . sprintf(__('Page %s', 'brave'), max($paged, $page));
	}
	return $title;
}

add_filter('wp_title', 'format_brave_title', 10, 2);

// Rename post format
function rename_brave_post_formats($translation, $text, $context, $domain) {
	$format_name = get_brave_config('basic', 'post_format_name');
	if ($context == 'Post format') {
		$translation = str_replace(array_keys($format_name), array_values($format_name), $text);
	}
	return $translation;
}

add_filter('gettext_with_context', 'rename_brave_post_formats', 10, 4);

// Reset post image size attribute
function reset_brave_content_image_sizes_attr($sizes, $size) {
	$width = $size[0];
	840 <= $width && $sizes = '(max-width: 1024px) 100vw, (max-width: 1439px) 1200px, (min-width: 1440px) 1200px';
	if ('page' === get_post_type()) {
		840 > $width && $sizes = '(max-width: ' . $width . 'px) 100vw, ' . $width . 'px';
	} else {
		840 > $width && 600 <= $width && $sizes = '(max-width: 720px) 100vw, (max-width: 909px) 100vw, (max-width: 984px) 100vw, (max-width: 1362px) 100vw, 800px';
		600 > $width && $sizes = '(max-width: ' . $width . 'px) 100vw, ' . $width . 'px';
	}
	return $sizes;
}

add_filter('wp_calculate_image_sizes', 'reset_brave_content_image_sizes_attr', 10, 2);

// Post thumbnail
function get_brave_post_thumbnail() {
	if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
		return;
	}
	if (is_singular()) : ?>
		<?php the_post_thumbnail('medium', array('class' => 'mb10')); ?>
	<?php else : ?>
        <div class="mb10">
            <a href="<?php the_permalink(); ?>" aria-hidden="true">
				<?php the_post_thumbnail('medium', array('alt' => the_title_attribute('echo=0'))); ?>
            </a>
        </div>
	<?php endif;
}

// 设置摘要长度
function set_brave_excerpt_length($length) {
	return get_brave_config('basic', 'excerpt_length');
}

add_filter('excerpt_length', 'set_brave_excerpt_length', 999);

// 非管理员界面动作
if (!is_admin()) {
	// Remove mediaelement
	function remove_brave_mediaelement_scripts() {
		wp_dequeue_script('wp-mediaelement');
		wp_deregister_script('wp-mediaelement');
	}

	add_action('wp_print_scripts', 'remove_brave_mediaelement_scripts', 100);

	function remove_brave_mediaelement() {
		return '';
	}

	add_filter('wp_video_shortcode_library', 'remove_brave_mediaelement');

	// read more
	function custom_brave_excerpt_more($more) {
		$link = sprintf('<div class="more-link mt20"><a href="%1$s">%2$s</a></div>',
			esc_url(get_permalink(get_the_ID())),
			sprintf(__('Read more %s', 'brave'), '<span class="none">' . esc_html(get_the_title(get_the_ID())) . '</span>')
		);
		return ' &hellip; ' . $link;
	}

	add_filter('excerpt_more', 'custom_brave_excerpt_more');
}

// 自定义搜索框
function get_brave_search_form($form) {
	$form = '<form id="search" role="search" method="get" action="' . home_url('/') . '" >
	<input class="b_r inp text_box" type="text" value="' . get_search_query() . '" name="s" /><input type="submit" class="b_r btn" value="' . esc_attr__('搜索', 'brave') . '" />
	</form>';
	return $form;
}

add_filter('get_search_form', 'get_brave_search_form');

// Load more
function get_brave_content_nav() {
	global $wp_query;
	if ($wp_query->max_num_pages > 1) : ?>
        <div class="next inner clear">
			<?php next_posts_link(__('加载更多&hellip;', 'brave')); ?>
        </div>
	<?php endif;
}

/**
 * get_brave_year_of_age
 * $birth_date date
 * @return string
 */
function get_brave_year_of_age($birth_date) {
	$post_date = date_create(get_the_time('Y-m-d'));
	$interval = date_diff($birth_date, $post_date);
	$prefix = '';
	if ($interval->format('%R') === '-') {
		$prefix = '出生前';
	}
	$result = '';
	if ($interval->format('%y') === '0') {
		if ($interval->format('%m') === '0') {
			if ($interval->format('%d') === '0') {
				$result = '出生';
			} else if ($interval->format('%d') > 0) {
				$result = $interval->format('%d天');
			}
		} else if ($interval->format('%m') > 0) {
			$result = $interval->format('%m个月');
		}
	} else if ($interval->format('%y') > 0) {
		if ($interval->format('%m') === '0') {
			if ($interval->format('%d') === '0') {
				$result = $interval->format('%y岁生日');
			} else if ($interval->format('%d') > 0) {
				$result = $interval->format('%y岁');
			}
		} else if ($interval->format('%m') > 0) {
			$result = $interval->format('%y岁%m个月');
		}
	}
	echo $prefix . $result;
}

function get_brave_age($format = NULL) {
	$format = empty($format) ? get_post_format() : $format;
	$key = get_brave_config('custom', 'date.' . $format);
	$date = date_create($key); // 转换成日期格式
	return isset($key) ? get_brave_year_of_age($date) : '';
}

function get_brave_role() {
	$format = get_post_format();
	return printf(get_brave_config('custom', 'role.' . $format));
}

// 展示广告
function display_brave_ad($type) {
	// 管理员不看广告, 不开启不显示广告
	if (current_user_can('administrator') || !get_brave_config('basic', 'display_ad')) {
		return;
	}
	// 拼接成函数名
	$func = 'ad_' . $type;
	if (function_exists($func)) {
		return $func();
	}
}

// 搜索页面的日期显示格式
function get_brave_search_date() {
	$format = get_post_format();
	$role = get_brave_config('custom', 'role.' . $format);
	if (!empty($role)) {
		return get_brave_age($format);
	} else {
		return the_time(__('Y-m-d', 'brave'));
	}
}

// 增加移动设备样式
function add_brave_mobile_class($class) {
	$detect = new Mobile_Detect;
	if ($detect->isMobile()) {
		set_array_key($class, '', 'mob');
	}
	return $class;
}

add_filter('body_class', 'add_brave_mobile_class');

/**
 * 摘要
 * $length int 输出摘要的长度
 * @return string
 */
function get_brave_excerpt($length = NULL) {
	global $post;
	$length = !empty($length) && is_int($length) ? abs($length) : 100;
	$get_content = get_the_excerpt($post->ID);
	$excerpt_string = mb_substr($get_content, 0, $length, 'utf8');
	if (mb_strlen($get_content) > $length) {
		$excerpt_string = $excerpt_string . '...';
	}
	return $excerpt_string;
}

// 获取日志 meta
function get_brave_post_meta() {
	global $post;
	// 坐标
	$meta_geo_lat = get_post_meta($post->ID, 'geo_latitude', true);
	$meta_geo_lon = get_post_meta($post->ID, 'geo_longitude', true);
	$meta_geo_city = get_post_meta($post->ID, 'geo_city', true); // 城市
	$meta_geo_public = get_post_meta($post->ID, 'geo_public', true); // 坐标公开状态：1公开/0私有
	$meta_wx_wx = get_post_meta($post->ID, 'wx_weather', true);
	$meta_wx_temp = get_post_meta($post->ID, 'wx_temp', true); // 温度

	$html_string = '';

	if ($meta_geo_lat && $meta_geo_lon) {
		if ($meta_geo_public === 1) {
			$html_string .= '<span class="ml"><a class="gmap" href="//maps.google.com/maps?q=$meta_geo_lat,$meta_geo_lon&hl=zh-cn&t=m&z=15" itemprop="map" itemtype="//schema.org/Place"><span class="i-local"></span></a></span>';
			if ($meta_geo_city) {
				$html_string .= '<span>' . $meta_geo_city . '</span>';
			}
		} elseif ($meta_geo_public === 0) {
			if (current_user_can('manage_options')) {
				$html_string .= '<span class="ml"><a class="gmap" href="//maps.google.com/maps?q=$meta_geo_lat,$meta_geo_lon&hl=zh-cn&t=m&z=15" itemprop="map" itemtype="//schema.org/Place"><span class="i-pin"></span></a></span>';
			} else {
				$html_string .= '<span class="i-pin ml" itemprop="map" itemtype="//schema.org/Place"></span>';
			}
		}
	}
	// 天气
	if ($meta_wx_wx) {
		$html_string .= '<span class="ml">' . $meta_wx_wx . '</span>';
	}
	// 温度
	if ($meta_wx_temp) {
		$html_string .= '<span class="ml">' . $meta_wx_temp . '</span>';
	}

	if ($html_string) {
		echo $html_string;
	}
}

// 获取日志的发布来源 (发布日志的设备信息)
function get_brave_post_device() {
	global $post;
	$post_device_name = get_post_meta($post->ID, 'post_device_name', true);
	$post_device_ver = get_post_meta($post->ID, 'post_device_ver', true);
	$post_device_ver = str_replace(array('_', ' ', '/'), '.', $post_device_ver);// 版本号替换，将_空格/的间隔统一替换为.
	$post_device_ver = ' ' . substr($post_device_ver, 0, strpos($post_device_ver, '.'));// 移除第一个小数点后的数字
	if (!empty($post_device_name)) {
		$post_device_pfx = 'via '; // 前缀
		$post_device = $post_device_pfx . $post_device_name;

		switch ($post_device_name) {
			/*
			case 'iPhone':
			case 'iPad':
			case 'Android':
			case 'Android Tablet':
			case 'Kindle':
			case 'Chrome':
			case 'Safari':
			case 'Firefox':
			*/
			default:
				$html_string = '<span class="ml">' . $post_device . '</span>';
				break;
		}
		echo $html_string;
	}
}

// get comment device info
function get_brave_comment_device() {
	global $comment;
	$comment_device_name = get_comment_meta($comment->comment_ID, 'comment_device_name', true);
	$comment_device_ver = get_comment_meta($comment->comment_ID, 'comment_device_ver', true);
	$comment_device_ver = str_replace(array('_', ' ', '/'), '.', $comment_device_ver); // 版本号替换，将_空格/的间隔统一替换为.
	$comment_device_ver = ' ' . substr($comment_device_ver, 0, strpos($comment_device_ver, '.')); // 移除第一个小数点后的数字
	if (!empty($comment_device_name)) {
		$comment_device_pfx = 'via '; // 前缀
		$comment_device = $comment_device_pfx . $comment_device_name;

		switch ($comment_device_name) {
			/*
			case 'iPhone':
			case 'iPad':
			case 'Android':
			case 'Android Tablet':
			case 'Kindle':
			*/
			case 'Chrome':
				$html_string = '<span class="ml c3 f14">' . $comment_device . $comment_device_ver . '</span>';
				break;
			case 'Safari':
				$html_string = '<span class="ml c3 f14">' . $comment_device . $comment_device_ver . '</span>';
				break;
			case 'Firefox':
				$html_string = '<span class="ml c3 f14">' . $comment_device . $comment_device_ver . '</span>';
				break;
			default:
				$html_string = '<span class="ml c3 f14">' . $comment_device . '</span>';
				break;
		}
		echo $html_string;
	}
}

// Replace gravatar url
function replace_brave_avatar($avatar) {
	$avatar = str_replace(array('www.gravatar.com', 'secure.gravatar.com', '1.gravatar.com', '2.gravatar.com'), 'dn-qiniu-avatar.qbox.me', $avatar);
	return $avatar;
}

add_filter('get_avatar', 'replace_brave_avatar', 10, 3);

function modify_brave_comment_class($class) {
	// Add class to parent comment, for Infinite Ajax Scroll
	$current_comment = get_comment();
	if (empty($current_comment->comment_parent)) {
		set_array_key($class, '', 'item');
	}

	// Remove class from comment_class
	foreach ($class as $key => $val) {
		if (strpos($val, 'comment-author-') !== false || strpos($val, 'by') !== false) {
			remove_array_key($class, $key);
		}
	}
	return $class;
}

add_filter('comment_class', 'modify_brave_comment_class');

// Custom comment
function brave_comment($comment, $args, $depth) {
	switch ($comment->comment_type) {
		case 'pingback' :
		case 'trackback' :
			?>
            <li id="comment-<?php comment_ID(); ?>" <?php comment_class('comment'); ?>>
            <span class='mr12 c4 f14'><?php comment_type(); ?></span><?php comment_author_link(); ?><?php edit_comment_link(__('Edit', 'brave'), '<span class="c3 ml12 f14">', '</span>'); ?>
			<?php
			break;
		default :
			// Proceed with normal comments.
			global $post;
			?>
            <li id="li-comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
            <div id="comment-<?php comment_ID(); ?>">
				<?php
				printf('<div class="comment-avatar left">%1$s</div><div class="cm">%2$s%3$s<span class="ml12 c4 f14" datetime="%4$s %5$s" itemprop="datePublished">%6$s</span>',
					get_avatar($comment, 48),
					get_comment_author_link(),
					($comment->user_id === $post->post_author) ? '<span class="i-pen ml12 c3 f14">' . __('', 'brave') . '</span>' : '',
					get_comment_date(),
					get_comment_time('H:i'),
					human_time_diff(get_comment_time('U'), current_time('timestamp')) . '前'
				);
				?>
				<?php get_brave_comment_device(); ?>
				<?php if ('0' == $comment->comment_approved) : ?>
                    <div class="c6 mt20"><?php esc_html_e('Your comment is awaiting moderation.', 'brave'); ?></div>
				<?php endif; ?>
                <div class="comment-content">
					<?php comment_text(); ?>
                </div>
                <div class="c4 f14">
					<?php comment_reply_link(array_merge($args, array('reply_text' => __('<span class="i-reply"></span>Reply', 'brave'), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?><?php edit_comment_link(__('Edit', 'brave'), '<span class="ml12 c3">', '</span>'); ?>
                </div>
            </div><!-- cm end -->
            </div>
			<?php
			break;
	}
}

/**
 * 检测异常评论
 * $check_key String 需要根据此条件来检测异常评论,目前支持 email 或 IP
 * $check_key_value String check_key 的条件值, 即 email 或 IP 的值
 * @return Error Null
 */
function check_brave_comment($check_key, $check_key_value) {
	// 评论控制阈值参数数组
	$threshold_array = get_brave_config('comment', 'threshold');

	/* case 1: 判断是否有垃圾评论: 评论对象存在的垃圾评论或已被移到回收站的评论数量超过 $threshold 配置的阈值, 即不再允许继续提交评论 */
	$comment_status = 'spam';
	$comment_status_array = array($comment_status, 'trash'); // 垃圾评论 或 已移到回收站
	// 获取评论条数
	$count_spam = get_brave_comment_info($check_key, $comment_status_array, $check_key_value);
	$threshold = get_array_key($threshold_array, $comment_status);
	if ($count_spam >= $threshold) {
		return get_brave_comment_error_msg($comment_status, $check_key, $count_spam);
	}

	/* case 2: 判断是否有待审核评论: 待审核评论数超过 $threshold 配置的阈值将不允许继续提交评论 */
	$comment_status = 'hold';
	$comment_status_array = array($comment_status); // 待审核
	// 获取评论条数
	$count_hold = get_brave_comment_info($check_key, $comment_status_array, $check_key_value);
	$threshold = get_array_key($threshold_array, $comment_status);
	if ($count_hold >= $threshold) {
		return get_brave_comment_error_msg($comment_status, $check_key, $count_hold);
	}

	/* case 3: 判断指定时期内是否有过多评论
	 * 已通过审核的 email 指定时期内的评论超过 $threshold 配置的阈值, 后续评论将被标记为待审核, 若继续添加评论最终将会进入 case 2 以被阻止告终
	**/
	$comment_status = 'approve';
	$comment_status_array = array($comment_status); // 已通过
	$count_approve = get_brave_comment_info($check_key, $comment_status_array, $check_key_value);
	$threshold = get_array_key($threshold_array, $comment_status);
	if ($count_approve >= $threshold) {
		return add_action('pre_comment_approved', 'modify_brave_comment_to_approved', 99, 2);
	}
}

/**
 * 获取评论条数
 * $check_key String 根据此条件统计评论数量, 如 Email 或 IP
 * $comment_status_array Array 需要统计的评论状态
 * $check_key_value $check_key 的条件值
 * @return int
 */
function get_brave_comment_info($check_key, $comment_status_array, $check_key_value) {
	
	// 获取评论配置
	$comment_config_array = get_brave_config('comment');
	
	// 根据 email 查询是否有异常评论
	if ($check_key === 'email') {
		$comment_email = $check_key_value;
		$comment_field_array = array(
			'count' => true, // 返回评论条数
			'status' => $comment_status_array, // 评论状态:['spam,'trash']/['hold']
			'author_email' => $comment_email,
		);
		if (in_array('spam', $comment_status_array)) {
			$config_key = 'email.spam';
		} else if (in_array('hold', $comment_status_array)) {
			$config_key = 'email.hold';
		} else if (in_array('approve', $comment_status_array)) {
			$config_key = 'email.approve';
		}
		$after_date = get_array_key($comment_config_array, $config_key);
		$date_query_array = array();
		if (!!$after_date) {
			$date_query_array = array(
				'date_query' => array(
					'after' => $after_date, // 添加日期范围
					'inclusive' => true,
				)
			);
		}
		$arg = array_merge($date_query_array, $comment_field_array);
		// 返回评论条数
		return get_comments($arg);
	}

	// 根据 IP 查询是否有异常评论
	if ($check_key === 'IP') {
		$comment_IP = $check_key_value;
		/* 置换 $comment_status_array 为 sql 条件 开始 */
		$status_convert_array = get_array_key($comment_config_array, 'comment_status_convert_array');
		$count_status = count($comment_status_array);
		$comment_status = array();
		for ($i = 0; $i < $count_status; $i++) {
			$key = get_array_key($comment_status_array, $i);
			$val = get_array_key($status_convert_array, $key);
			$comment_status[$i] = $val;
		}
		$comment_status = "'" . implode("','", $comment_status) . "'";
		/* 置换 $comment_status_array 为 sql 条件 结束 */

		// 获取查询开始时间
		$interval = get_array_key($comment_config_array, $check_key);
		$comment_approved_time = get_brave_date_string('now', $interval);

		// 区别: comment_date 系统设置的时区时间, comment_date_gmt 格林尼治时间
		global $wpdb;
		$comments = $wpdb->get_row($wpdb->prepare("SELECT count(1) as ttl_comment FROM $wpdb->comments WHERE comment_approved in ($comment_status) and comment_date >= %s and comment_author_IP = %s", $comment_approved_time, $comment_IP));

		// 返回评论条数
		return $comments->ttl_comment;
	}
}

/**
 * 获取时间字符串
 * $start_date String 开始日期,
 * $interval String 默认实时
 * $date_format String 时间格式 默认 Y-m-d H:i:s
 * $timezone timezone 时区类型
 * @return String
 */
function get_brave_date_string($start_date = 'now', $interval = '0 day', $date_format = 'Y-m-d H:i:s', $timezone = NULL) {
	$timezone = $timezone ? $timezone : get_brave_config('basic', 'time_zone');
	$start_date = date_create($start_date, $timezone);
	return date_format(date_add($start_date, date_interval_create_from_date_string($interval)), $date_format);
}

// 添加 @user
function add_brave_comment_at($comment_text, $comment = '') {
	if ($comment->comment_parent > 0) {
		$comment_text = '<a href="#comment-' . $comment->comment_parent . '">@' . get_comment_author($comment->comment_parent) . '</a> ' . $comment_text;
	}
	return $comment_text;
}

add_filter('comment_text', 'add_brave_comment_at', 20, 2);

/**
 * 上传文件自动在文件名后面附加随机字符串 (为了安全)
 * https://developer.wordpress.org/reference/hooks/sanitize_file_name/
*/
function auto_brave_filename_hash($filename) {
	$path_array = pathinfo($filename);
	$file_ext = empty($path_array['extension']) ? '' : '.' . $path_array['extension'];
	$base_name = basename($filename, $file_ext);
	$hash_length = rand(8, 16);
	$hash_string = get_brave_hash($hash_length);
	return $base_name . '_' . $hash_string . $file_ext;
}

add_filter('sanitize_file_name', 'auto_brave_filename_hash');

/**
 * 校验散列值
 * $sys_hash string 系统生成的散列值
 * $user_hash string 用户传入的散列值
 * @return boolean 校验通过返回 true; 否则, 返回 false
*/
function check_brave_secure_auth($sys_hash, $user_hash) {
	return hash_equals($sys_hash, $user_hash);
}

// 生成评论框名称
function get_brave_comment_text_field() {
	$comment_text_field = get_brave_config('comment', 'comment_text_field');
	return (!empty($comment_text_field) && is_string($comment_text_field)) ? $comment_text_field : 'brave_comment';
}

// 阻止浏览器缓存
function clear_brave_cache() {
	nocache_headers();
}

// 发送邮件
function send_brave_mail($subject, $body) {
	$to = get_brave_config('basic', 'mail.to');
	$headers = array('Content-Type:text/html;charset=UTF-8');
	wp_mail($to, $subject, $body, $headers);
}

// 自定义密码输入框
function custom_brave_password_form() {
	global $post;
	$label = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);
	$form_html = '<form action="' . esc_url(site_url('wp-login.php?action=postpass', 'login_post')) . '" method="post">
	<p class="mb10">' . esc_attr__('已加密，请提供访问密码：', 'brave') . '</p><p class="mb30 clear"><input name="post_password" id="' . $label . '" type="password" class="inp text_box left" size="20" /><input type="submit" name="submit" class="btn submit_btn left" value="' . esc_attr__('提交', 'brave') . '" /></p></form>';
	return $form_html;
}

add_filter('the_password_form', 'custom_brave_password_form');

// Custom protected_title and private_title 
if (current_user_can('administrator')) {
	function custom_brave_protected_title($title) {
		return '<span class="i-key mr-ico"></span>%s';
	}

	add_filter('protected_title_format', 'custom_brave_protected_title');

	function custom_brave_private_title($title) {
		return '<span class="i-lock mr-ico"></span>%s';
	}

	add_filter('private_title_format', 'custom_brave_private_title');
}

// 拼 sql 条件过滤加密的
/*
function exclude_protected($where) {
	global $wpdb;
	return $where .= " AND {$wpdb->posts}.post_password = '' ";
}

function exclude_protected_action($query) {
	add_filter('posts_where', 'exclude_protected');
}
add_action('pre_get_posts', 'exclude_protected_action');
*/

function add_brave_post_format_to_title_rss($title) {
	$post_format = get_post_format();
	$format_name = get_post_format_string($post_format);
	return $format_name . ': ' . $title;
}

add_filter('the_title_rss', 'add_brave_post_format_to_title_rss');

if (!is_user_logged_in()) {
	// 非登陆用户评论预处理
	function preprocess_brave_comment($commentdata) {
		// 防止直接走 wp-comments-post.php
		$check_comment = get_brave_config('comment', 'check');
		if ($check_comment) {
			$comment_channel_field = get_brave_config('comment', 'comment_channel_field');
			$comment_channel_val = trim(get_array_key($_POST, $comment_channel_field));
			if (empty($comment_channel_val)) {
				return get_brave_error_msg('channel_error_hash_empty'); // 评论来源异常
			}
			// 散列校验
			$sys_hash = get_brave_secure_auth('comment', 'comment_check_key');
			$is_pass = check_brave_secure_auth($sys_hash, $comment_channel_val);
			if (!$is_pass) {
				return get_brave_error_msg('channel_hash_check_fail'); // 评论来源异常
			}
		}

		// 禁止外部 trackback,
		$comment_type = get_array_key($commentdata, 'comment_type');
		if ($comment_type === 'trackback') {
			return get_brave_error_msg('trackback_disabled'); // 拒绝 trackback
		}
		return $commentdata;
	}

	add_filter('preprocess_comment', 'preprocess_brave_comment', 1);

	// 添加统计代码, 不统计登录用户
	function add_brave_analytics() {
		if (function_exists(get_brave_analytics())) {
			return get_brave_analytics();
		}
	}
	
	add_action('wp_footer', 'add_brave_analytics');
}

/**
 * 正则替换
 * $date string|array 需要搜索替换的原始数据
 * $pattern_array array 模式数组
 * $replacement_array array 替换数组
 * @return string|array 取决于 $date 的类型
 * preg_replace https://www.php.net/manual/zh/function.preg-replace.php
 */
function get_brave_replace(&$data, $pattern_array, $replacement_array) {
	$data = preg_replace($pattern_array, $replacement_array, $data);
}

// 发布或更新日志时自动增加汉字和英文字符间的空格
function auto_brave_post_space($data, $postarr, $unsanitized_postarr, $update) {
	$pick_field_array = get_brave_config('basic', 'auto_space_field');
	$pattern_array = array(
		/** Pattern 来源
		 * @author    Tunghsiao Liu <t@sparanoid.com>
		 * @link      https://sparanoid.com/
		 * @copyright Sparanoid
		 * GitHub Plugin URI: https://github.com/sparanoid/space-lover
		 */
		// Space for opneing (Ps) and closing (Pe) punctuations
		'~(\p{Han})([a-zA-Z0-9\p{Ps}\p{Pi}])(?![^<]*>)~u',
		'~([a-zA-Z0-9\p{Pe}\p{Pf}])(\p{Han})(?![^<]*>)~u',
		// Space for general punctuations
		'~([!?‽:;,.%])(\p{Han})~u',
		'~(\p{Han})([@$#])~u',
		// Space fix for 'ampersand' character https://regex101.com/r/hU3wD2/13
		'~(&amp;?(?:amp)?;) (\p{Han})(?![^<]*>)~u',
		// Space for HTML tags
		'~(\p{Han})(<(?!ruby)[a-zA-Z]+?[^>]*?>)([a-zA-Z0-9\p{Ps}\p{Pi}@$#])~u',
		'~(\p{Han})(<\/(?!ruby)[a-zA-Z]+>)([a-zA-Z0-9])~u',
		'~([a-zA-Z0-9\p{Pe}\p{Pf}!?‽:;,.%])(<(?!ruby)[a-zA-Z]+?[^>]*?>)(\p{Han})~u',
		'~([a-zA-Z0-9\p{Ps}\p{Pi}!?‽:;,.%])(<\/(?!ruby)[a-zA-Z]+>)(\p{Han})~u',
		'~[ ]*([「」『』（）〈〉《》【】〔〕〖〗〘〙〚〛])[ ]*~u',
	);
	$replacement_array = array('\1 \2', '\1 \2', '\1 \2', '\1 \2', '\1\2', '\1 \2\3', '\1\2 \3', '\1 \2\3', '\1\2 \3', '\1',);
	$pick_data = pick_array($data, $pick_field_array);
	get_brave_replace($pick_data, $pattern_array, $replacement_array);
	return array_merge($data, $pick_data);
}

if (get_brave_config('basic', 'auto_space')) {
	add_filter( 'wp_insert_post_data', 'auto_brave_post_space', 10, 4);
}

// 发布日志时保存发布者的设备信息
function set_brave_post_device_meta($post_id, $post, $update) {
	// 更新操作不处理, 自动保存不处理
	if ($update || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)) {
		return;
	}
	
	$detect = new Mobile_Detect;
	if ($detect->isMobile() && !$detect->isTablet()) {
		// 手机
		if ($detect->isiPhone()) {
			$device_name = 'iPhone';
			$device_ver = $detect->version('iOS');
		} else if ($detect->isAndroidOS()) {
			$device_name = 'Android';
			$device_ver = $detect->version('Android');
		}
	} else if ($detect->isMobile() && $detect->isTablet()) {
		// 平板
		if ($detect->isiPad()) {
			$device_name = 'iPad';
			$device_ver = $detect->version('iOS');
		} else if ($detect->isKindle()) {
			$device_name = 'Kindle';
		} else if ($detect->isAndroidOS()) {
			$device_name = 'Android Tablet';
			$device_ver = $detect->version('Android');
		}
	} else {
		// 桌面
		if ($detect->version('Chrome')) {
			$device_name = 'Chrome';
			$device_ver = $detect->version('Chrome');
		} else if ($detect->version('Firefox')) {
			$device_name = 'Firefox';
			$device_ver = $detect->version('Firefox');
		} else if ($detect->version('Safari')) {
			$device_name = 'Safari';
			$device_ver = $detect->version('Safari');
		}
	}
	
	if (!empty($device_name)) {
		update_post_meta($post_id, 'post_device_name', $device_name);
		update_post_meta($post_id, 'post_device_ver', $device_ver);
	}
}

add_action('save_post', 'set_brave_post_device_meta', 10, 3);

// 保存评论者的设备信息
function set_brave_comment_device_meta($comment_ID) {
	if (!current_user_can('administrator') && !is_admin()) {
		$detect = new Mobile_Detect;
		if ($detect->isMobile() && !$detect->isTablet()) {
			// 手机
			if ($detect->isiPhone()) {
				$device_name = 'iPhone';
				$device_ver = $detect->version('iOS');
			} else if ($detect->isAndroidOS()) {
				$device_name = 'Android';
				$device_ver = $detect->version('Android');
			}
		} else if ($detect->isMobile() && $detect->isTablet()) {
			// 平板
			if ($detect->isiPad()) {
				$device_name = 'iPad';
				$device_ver = $detect->version('iOS');
			} else if ($detect->isKindle()) {
				$device_name = 'Kindle';
			} else if ($detect->isAndroidOS()) {
				$device_name = 'Android Tablet';
				$device_ver = $detect->version('Android');
			}
		} else {
			// 桌面
			if ($detect->version('Chrome')) {
				$device_name = 'Chrome';
				$device_ver = $detect->version('Chrome');
			} else if ($detect->version('Firefox')) {
				$device_name = 'Firefox';
				$device_ver = $detect->version('Firefox');
			} else if ($detect->version('Safari')) {
				$device_name = 'Safari';
				$device_ver = $detect->version('Safari');
			}
		}

		if (!empty($device_name)) {
			add_comment_meta($comment_ID, 'comment_device_name', $device_name);
			add_comment_meta($comment_ID, 'comment_device_ver', $device_ver);
		}
	} else {
		return;
	}
}

add_action('wp_insert_comment', 'set_brave_comment_device_meta', 10, 1);

/**
 * 修改符合条件的评论状态为待审核
 * 此函数不能单独使用, 需要将本函数添加为 pre_comment_approved 的动作, 当 wp 核心程序调用 do_action() 时触发 pre_comment_approved 动作并执行本函数
 * $approved int|string|WP_Error
 * $commentdata array
 */
function modify_brave_comment_to_approved($approved, $commentdata) {
	return $approved = 0;
}

/**
 * 指定格式的日志, 发布时自动置为私密
 * $postarr['ID'] 获取 post_id
*/
function auto_private_brave_post_format($data, $postarr) {
	$format = get_post_format($postarr['ID']);
	$private_format_array = get_brave_config('custom', 'auto_private_post_format');
	if (in_array($format, $private_format_array) && $data['post_status'] === 'publish') {
		$data['post_status'] = 'private';
	}
	return $data;
}

add_filter('wp_insert_post_data', 'auto_private_brave_post_format', 12, 2);

/**
 * 异常评论错误提示
 * $comment_status String {spam | hold}
 * $check_type String: {email | IP}
 * $count Int
 */
function get_brave_comment_error_msg($comment_status, $check_type, $count = NULL) {
	$key = $check_type . '_' . $comment_status;
	$error_key = get_brave_config('error', 'code.' . $key);
	switch ($comment_status) {
		case 'spam': // 垃圾评论错误提示
			$error_val = '系统检测到你可能有异常评论，我们不允许你继续提交评论！';
			break;
		case 'hold': // 待审核评论错误提示
			$error_val = '您已有' . $count . '条待审核评论，通过审核前我们不允许您继续提交评论！';
			break;
		default:
			return;
	}
	if ($error_val) {
		$error_val = '<strong>错误：</strong>' . $error_val;
		/*
		$subject = '异常垃圾评论警告';
		$body = 'via stop_spam' . $error_val;
		send_brave_mail($subject, $body);
		*/
		return get_brave_die($error_key, $error_val);
	}
}

/**
 * 获取错误信息
 * $error_name 错误代码 config.php 'error' -> 'code' 下配置的错误代码
 * $title string 错误页面标题 (网页标题)
 * @return function
 */
function get_brave_error_msg($error_name, $title = NULL) {
	$error_key = get_brave_config('error', 'code.' . $error_name);
	$error_val = '<strong>错误：</strong>' . get_brave_config('error', 'msg.' . $error_key);
	return get_brave_die($error_key, $error_val, $title);
}

/**
 * 获取配置
 * $group string
 * $item string
 * @return string | array
 */
function get_brave_config($group, $item = NULL) {
	static $conf_obj, $cached_result = array();
	
	// step1. 有缓存的直接取缓存
	if (isset($cached_result[$group])) {
		return empty($item) ? $cached_result[$group] : get_array_key($cached_result[$group], $item);
	}
	
	// step2. 没有缓存的重新取
	if (!isset($conf_obj)) {
		include_once(get_template_directory() . '/conf/config.php');
		$conf_obj = new Config();
	}
	// 获取闭包函数, 闭包函数返回 $group 键对应的元素
	$func = $conf_obj->get_config($group);
	$group_array = $func();
	
	// 缓存 $group
	$cached_result[$group] = $group_array;
	
	// 不传 $item 或 传入空值, 返回整个 $group; 传 $item, 返回 $item 路径下的值
	return empty($item) ? $group_array : get_array_key($group_array, $item);
}


/**** 加载插件 START ****/

// 加载 Mobile_Detect 插件
include_once(get_template_directory() . '/plugin/Mobile_Detect.php');

// 加载 like 插件
include_once(get_template_directory() . '/plugin/like.php');

// 加载广告配置
include_once(get_template_directory() . '/plugin/display_ad.php');

// Require gallery.php
require_once(get_template_directory() . '/plugin/gallery.php');

/**** 加载插件 END ****/


/**** Tool START ****/

/**
 * 生成随机字符串
 * $hash_length int 想要的随机字符串长度 低于 8 位会自动重置为 8 - 18 的随机长度
 * $hash_mask string 随机字符串的来源
 */
function get_brave_hash($hash_length = NULL, $hash_mask = NULL) {
	$hash_length = (!is_int($hash_length) || (is_int($hash_length) && abs($hash_length) < 8)) ? rand(8, 18) : abs($hash_length);
	$hash_mask = is_string($hash_mask) ? $hash_mask : 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
	return substr(str_shuffle($hash_mask), -$hash_length);
}

/**
 * 获取散列值
 * $group string
 * $item string 通过 $group 和 $item 获取对应配置中的字符串
 * $schema string 可选, 默认使用 wp_config.php 配置的 AUTH_KEY 或 AUTH_SALT
 * @return boolean 校验通过返回 true; 否则,返回 false
 * 
 * wp_hash https://developer.wordpress.org/reference/functions/wp_hash/
 * wp_salt https://developer.wordpress.org/reference/functions/wp_salt/
*/
function get_brave_secure_auth($group, $item, $schema = 'auth') {
	return wp_hash(get_brave_config($group, $item), $schema);
}

/**
 * 获取数组键值
 * $array array
 * $key string | Int 数组键 或 数组索引。 支持多级键, 以.号分隔, 示例: get_array_key(array, 'key1.key2');
 * @return
 */
function get_array_key($array, $key) {
	if (!is_array($array) || (!is_string($key) && !is_int($key))) {
		return;
	}
	
	// 传入的是索引
	if (is_int($key)) {
		return array_key_exists($key, $array) ? $array[$key] : NULL;
	}
	
	// 传入的是键
	$item_array = explode('.', $key);
	$count_item = count($item_array);
	for ($i = 0; $i < $count_item; $i++) {
		$key = $item_array[$i];
		if (!is_array($array)) {
			$array = NULL; // 非数组无法获取下一层, 置为 NULL 并跳出
			break;
		} else {
			$array = array_key_exists($key, $array) ? $array[$key] : NULL; // 获取下一层, 获取不存在的 key 会报警告(Notice)
		}
	}
	return $array;
}

/**
 * 移除数组键值
 * $array array  PHP 函数调用默认是值传递, 此处改用引用传递
 * $key string
 * @return
 * 引用传递函数参数 https://www.php.net/manual/zh/functions.arguments.php 示例3
 */
function remove_array_key(&$array, $key) {
	if (array_key_exists($key, $array)) {
		unset($array[$key]);
	}
}

/**
 * 设置数组键值
 * $array array
 * $key string
 * @return
 */
function set_array_key(&$array, $key, $val) {
	$array[$key] = $val;
}

/**
 * 生成错误信息: 其实就是对 wp_die 的包装
 * $error_key int|string 错误代码
 * $error_val string 错误详情
 * $title string 错误页面标题 (网页标题)
 * @return WP_Error
 */
function get_brave_die($error_key, $error_val, $title = NULL) {
	$title = empty($title) ? '出现异常, 请确认!' : $title; // eg: Comment Submission Failure
	clear_brave_cache();
	wp_die(
		'<p>' . $error_val . '代码: ' . $error_key . '</p>',
		__($title),
		array(
			'response' => $error_key,
			'back_link' => true,
		)
	);
}

/**
 * 拣选数组键/值/对
 * $array array 来源数组
 * $pick_key_array 需要拣选的 $array 的键或索引, 如: ['a', 'b'] / [0, 1]
 * $return_type string 拣选目标, 支持对 键/值/键值对 的拣选
 * return array
 * 试验中...
 */
function pick_array($array, $pick_key_array, $return_type = NULL) {
	// 移除 $pick_key_array 里不存在于 $array 的值
	$pick_key_array = array_filter($pick_key_array, function ($val) use ($array) {
		return array_key_exists($val, $array);
	});
	extract($array, EXTR_SKIP);
	$array = compact($pick_key_array);
	if (is_string($return_type)) {
		$key_array = ['k', 'key', 'keys'];
		$val_array = ['v', 'val', 'value', 'values'];
		return in_array($return_type, $key_array) ? array_keys($array) : (in_array($return_type, $val_array) ? array_values($array) : []);
	}
	return $array;
}

/**** Tool END ****/
?>