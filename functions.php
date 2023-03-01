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

// 加载配置项
include_once(get_template_directory() . '/conf/config.php');
include_once(get_template_directory() . '/plugin/Mobile_Detect.php');

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

/* 提供对外接口
 * $group string 用于组装函数名称
 * $item string 用于组装参数
 * @return json
 *
 * 通过 admin-ajax.php 调用的请求示例:
 * 请求参数: action=get_brave_config&group=basic&item=asset_uri
 * 函数解析: 本请求最后会被组成函数调用 get_brave_basic_config('asset_uri');
**/
add_action('wp_ajax_nopriv_get_brave_config', 'get_brave_config');
add_action('wp_ajax_get_brave_config', 'get_brave_config');
function get_brave_config() {
	$group = sanitize_text_field($_REQUEST['group']);
	$item = sanitize_text_field($_REQUEST['item']);
	$response = [];
	// 暂时只响应 asset_uri
	if (!isset($group) || !isset($item) || $item !== 'asset_uri') {
		exit();
	}
	$func = 'get_brave_' . $group . '_config';
	$response[$item] = $func($item);
	wp_send_json($response);
}

// 脚本配置
function brave_scripts_styles() {
	global $wp_styles;
	$asset_uri = get_brave_basic_config('asset_uri');
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
	add_theme_support('post-formats', get_brave_basic_config('enable_post_format'));
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

function clear_brave_nav_menu_item_id($id, $item, $args) {
	return '';
}

add_filter('nav_menu_item_id', 'clear_brave_nav_menu_item_id', 10, 3);

// 查询过滤
function exclude_brave_post_from_query($query) {
	// 首页排除的格式
	if ($query->is_main_query() && $query->is_home()) {
		$home_tax_query = array(
			array(
				'taxonomy' => 'post_format',
				'field' => 'slug',
				'terms' => get_brave_query_config('home.terms_not_in'),
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
				'terms' => get_brave_query_config('feed.terms_not_in'),
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

function format_brave_title($title, $sep) {
	global $paged, $page;
	if (is_feed()) {
		return $title;
	}
	// Add the site name.
	$title .= get_bloginfo('name', 'display');

	// Add the site description for home/front page.
	$site_description = get_bloginfo('description', 'display');
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
	$format_name = get_brave_basic_config('post_format_name');
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

function set_brave_excerpt_length($length) {
	return get_brave_basic_config('excerpt_length');
}

add_filter('excerpt_length', 'set_brave_excerpt_length', 999);

// Require gallery.php
if (get_post_format() === 'gallery') {
	require_once(get_template_directory() . '/plugin/gallery.php');
}

// 管理员界面动作
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
	function customize_brave_excerpt_more($more) {
		$link = sprintf('<div class="more-link mt20"><a href="%1$s">%2$s</a></div>',
			esc_url(get_permalink(get_the_ID())),
			sprintf(__('Read more %s', 'brave'), '<span class="none">' . esc_html(get_the_title(get_the_ID())) . '</span>')
		);
		return ' &hellip; ' . $link;
	}

	add_filter('excerpt_more', 'customize_brave_excerpt_more');
}

// 搜索框
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

/* get_brave_year_of_age 
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
		if ($interval->format('%m') == '0') {
			if ($interval->format('%d') === '0') {
				$result = $interval->format('出生');
			} else if ($interval->format('%d') > 0) {
				$result = $prefix . $interval->format('%d天');
			}
		} else if ($interval->format('%m') > 0) {
			$result = $prefix . $interval->format('%m个月');
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
	echo $result;
}

function get_brave_age() {
	$key = get_brave_custom_config('date');
	$date = date_create($key); // 转换成日期格式
	return isset($key) ? get_brave_year_of_age($date) : '';
}

function get_brave_role() {
	return printf(get_brave_custom_config('role'));
}

// 展示广告
function display_brave_ad($type) {
	// 管理员不看广告, 不开启不显示广告
	if (current_user_can('administrator') || !get_brave_basic_config('display_ad')) {
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
	$role = get_brave_custom_config('role');
	if ($role) {
		return get_brave_age();
	} else {
		return the_time(__('Y-m-d', 'brave'));
	}
}

// 增加移动设备样式
function add_brave_mobile_class($class) {
	$detect = new Mobile_Detect;
	if ($detect->isMobile()) {
		$class[] = 'mob';
	}
	return $class;
}

add_filter('body_class', 'add_brave_mobile_class');

/* 摘要
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

// get post device info
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
		$class[] = 'item';
	}

	// Remove class from comment_class
	$array_to_remove = [];
	foreach ($class as $key => $val) {
		if (strpos($val, 'comment-author-') !== false || strpos($val, 'by') !== false) {
			$array_to_remove[] = $val;
		}
	}
	return array_diff($class, $array_to_remove);
}

add_filter('comment_class', 'modify_brave_comment_class');

// Customize comment
function brave_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
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
 * $check_key_word String 需要根据此条件来检测异常评论,目前支持 email 或 IP
 * $check_key_word_value String 条件值,即 email 或 IP 的值
 * @return Error Null
 */
function check_brave_comment($check_key_word, $check_key_word_value) {
	// 评论控制阈值参数数组
	$thresholdArray = get_brave_comment_config('threshold');

	/* case 1: 判断是否有垃圾评论: 评论对象存在一条垃圾评论或被移动回收站的评论即不允许继续提交评论 */
	$comment_status = 'spam';
	$comment_status_array = array($comment_status, 'trash'); // 垃圾评论 或 已移到回收站
	// 获取评论条数
	$count_spam = get_brave_comment_info($check_key_word, $comment_status_array, $check_key_word_value);
	$threshold = get_array_key($thresholdArray, $comment_status);
	if ($count_spam > $threshold) {
		return get_brave_comment_error_msg($comment_status, $check_key_word, $count_spam);
	}

	/* case 2: 判断是否有待审核评论: 超过3条待审核的评论将不允许继续提交评论 */
	$comment_status = 'hold';
	$comment_status_array = array($comment_status); // 待审核
	// 获取评论条数
	$count_hold = get_brave_comment_info($check_key_word, $comment_status_array, $check_key_word_value);
	$threshold = get_array_key($thresholdArray, $comment_status);
	if ($count_hold > $threshold) {
		return get_brave_comment_error_msg($comment_status, $check_key_word, $count_hold);
	}

	/* case 3: 判断指定时期内是否有过多评论
	 * 已通过审核的 email 指定时期内的评论超过限制数量, 后续评论将被标记为待审核, 最终会进入 case 2 被阻止.
	**/
	$comment_status = 'approve';
	$comment_status_array = array($comment_status); // 已通过
	$count_approve = get_brave_comment_info($check_key_word, $comment_status_array, $check_key_word_value);
	$threshold = get_array_key($thresholdArray, $comment_status);
	if ($count_approve > $threshold) {
		return add_action('pre_comment_approved', 'modify_brave_comment_approved', 99, 2);
	}
}

/**
 * 获取评论信息
 * $check_key_word String
 * $comment_status_array Array
 * $check_key_word_value
 * @return int
 */
function get_brave_comment_info($check_key_word, $comment_status_array, $check_key_word_value) {
	// 根据 email 查询是否有异常评论
	if ($check_key_word === 'email') {
		$comment_email = $check_key_word_value;
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
		$after_date = get_brave_comment_config($config_key);
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
	if ($check_key_word === 'IP') {
		$comment_config_array = get_brave_comment_config();
		$comment_IP = $check_key_word_value;
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
		$interval = get_array_key($comment_config_array, $check_key_word);
		$comment_approved_time = get_brave_date_string('', $interval);

		// 区别: comment_date 当日时间, comment_date_gmt 格林尼治时间
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
 * $timezone String 时区标识符, 如 UTC
 * $format String 返回格式
 * @return String
 */
function get_brave_date_string($start_date = '', $interval = '0 day', $date_format = 'Y-m-d H:i:s', $timezone = 'PRC', $format = 'string') {
	date_default_timezone_set($timezone);
	$start_date = strlen($start_date) ? date_create($start_date) : date_create();
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

// 上传文件时生成随机文件名
function make_brave_filename_hash($filename) {
	$info = pathinfo($filename);
	$ext = empty($info['extension']) ? '' : '.' . $info['extension'];
	$name = basename($filename, $ext);
	$hash_length = rand(8, 14);
	$hash_string = get_brave_hash($hash_length);
	$filename = $name . "_" . $hash_string . $ext;
	return $filename;
}

add_filter('sanitize_file_name', 'make_brave_filename_hash');

// 生成随机字符串
function get_brave_hash($hash_length = NULL, $hash_mask = NULL) {
	$hash_length = (!is_int($hash_length) || (is_int($hash_length) && abs($hash_length) < 4)) ? rand(8, 14) : abs($hash_length);
	$hash_mask = is_string($hash_mask) ? $hash_mask : 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
	return substr(str_shuffle($hash_mask), -$hash_length);
}

// 开启 session
//session_start();

// 生成评论框名称
function get_comment_text_field() {
	$comment_text_field = get_brave_comment_config('comment_text_field');
	/*
	$comment_text_name_hash = get_array_key('_SESSION', $comment_text_field);
	if (isset($comment_text_name_hash)) {
		// $_SESSION 存在时直接返回 $_SESSION 中存储的值
		return $comment_text_name_hash;
	}
	$sep = '_';
	$hash_length = 8; // 8位长度
	$prefix = get_brave_hash(6, 'LlIiTtTtLlEe') . $sep . get_brave_hash(4, 'SsTtAaRrTt');
	$comment_text_name_hash = $prefix . $sep . get_brave_hash($hash_length);
	// 加入 session
	$_SESSION[$comment_text_field] = $comment_text_name_hash;
	return $comment_text_name_hash;
	*/
	return !empty($comment_text_field) && is_string($comment_text_field) ? $comment_text_field : 'brave_comment';
}

/**
 * 获取数组键值
 * $array string|array string将被转换为同名的超全局变量
 * $key string
 * @return
 * 支持传入超全局变量 https://www.php.net/manual/zh/language.variables.superglobals.php#124171
 */
function get_array_key($array, $key) {
	if (is_string($array)) {
		global $$array;
		return array_key_exists($key, $$array) ? $$array[$key] : null;
	} elseif (is_array($array)) {
		return array_key_exists($key, $array) ? $array[$key] : null;
	}
}

// 阻止浏览器缓存
function clear_brave_cache() {
	nocache_headers();
}

// 发送邮件
function send_brave_mail($subject, $body) {
	$to = get_brave_basic_config('mail.to');
	$headers = array('Content-Type:text/html;charset=UTF-8');
	wp_mail($to, $subject, $body, $headers);
}

// 自定义密码输入框
function customize_brave_password_form() {
	global $post;
	$label = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);
	$form_html = '<form action="' . esc_url(site_url('wp-login.php?action=postpass', 'login_post')) . '" method="post">
	<p class="mb10">' . esc_attr__('已加密，请提供访问密码：', 'brave') . '</p><p class="mb30 clear"><input name="post_password" id="' . $label . '" type="password" class="inp text_box left" size="20" /><input type="submit" name="submit" class="btn submit_btn left" value="' . esc_attr__('提交', 'brave') . '" /></p></form>';
	return $form_html;
}

add_filter('the_password_form', 'customize_brave_password_form');

// Customize protected_title and private_title 
if (current_user_can('administrator')) {
	function customize_brave_protected_title($title) {
		return '<span class="i-key mr-ico"></span>%s';
	}

	add_filter('protected_title_format', 'customize_brave_protected_title');

	function customize_brave_private_title($title) {
		return '<span class="i-lock mr-ico"></span>%s';
	}

	add_filter('private_title_format', 'customize_brave_private_title');
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

// 获取错误信息
function get_brave_error_msg($error_name, $title = NULL) {
	$error_key = get_brave_error_config('code.' . $error_name);
	$error_val = '<strong>错误：</strong>' . get_brave_error_config('msg.' . $error_key);
	return get_brave_die($error_key, $error_val, $title);
}

// 生成错误信息
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

if (!is_user_logged_in()) {
	// 非登陆用户评论预处理
	function preprocess_brave_comment($commentdata) {
		// 防止直接走 wp-comments-post.php
		$check_comment = get_brave_comment_config('check');
		if ($check_comment) {
			$comment_channel_field = get_brave_comment_config('comment_channel_field');
			$has_comment_channel = get_array_key('_POST', $comment_channel_field);
			if (empty($has_comment_channel)) {
				return get_brave_error_msg('channel_error_pc'); // 评论来源异常
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

	/*
		添加统计代码, 不统计登录用户
	*/
	function add_brave_analytics() {
		?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-6512951-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', 'UA-6512951-1');
        </script>
		<?php
	}

	add_action('wp_footer', 'add_brave_analytics');
}

// 设置发布日志者的设备信息
function set_brave_post_device_meta($post_id, $post, $update) {
	// 更新操作不处理
	if ($update) {
		return;
	}

	// 自动保存不处理
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	$current_post_device_name = get_post_meta($post->ID, 'post_device_name', true);
	$current_post_device_ver = get_post_meta($post->ID, 'post_device_ver', true);

	$current_time = date_create(date('Y-m-d H:i:s'));
	$post_time = date_create(get_the_time('Y-m-d H:i:s'));
	if (is_null($post_time)) {
		$post_time = $current_time;
	}
	$interval = date_diff($post_time, $current_time);

	// 已记录的/已发布的/私密的不做更新
	if (current_user_can('edit_post', $post_id) && ($interval->format('%s') <= 12) && !in_array(get_post_status($post_id), array('private', 'publish'))) {
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
		$current_post_device_name = $device_name;
		$current_post_device_ver = $device_ver;
		if (!empty($current_post_device_name)) {
			update_post_meta($post_id, 'post_device_name', $current_post_device_name);
			update_post_meta($post_id, 'post_device_ver', $current_post_device_ver);
		}
	} else {
		return;
	}
}

add_action('save_post', 'set_brave_post_device_meta', 10, 3);

// 设置发布评论者的设备信息
function set_brave_comment_device_meta($comment_ID) {
	$current_comment_device_name = get_comment_meta($comment->ID, 'comment_device_name', true);
	$current_comment_device_ver = get_comment_meta($comment->ID, 'comment_device_ver', true);
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
		$current_comment_device_name = $device_name;
		$current_comment_device_ver = $device_ver;

		if (!empty($current_comment_device_name)) {
			add_comment_meta($comment_ID, 'comment_device_name', $current_comment_device_name);
			add_comment_meta($comment_ID, 'comment_device_ver', $current_comment_device_ver);
		}
	} else {
		return;
	}
}

add_action('wp_insert_comment', 'set_brave_comment_device_meta', 10, 2);

/* 修改符合条件的评论状态为待审核
 * 此函数不能单独使用, 需要将本函数添加为 pre_comment_approved 的动作, 当 wp 核心程序调用 do_action() 时触发 pre_comment_approved 动作并执行本函数
 * $approved int|string|WP_Error
 * $commentdata array
 */
function modify_brave_comment_approved($approved, $commentdata) {
	$key = 'comment_author_' . $check_key_word;
	if (get_array_key($commentdata, $key) === $check_key_word_value) {
		$approved = 0;
	}
	return $approved;
}

/*	
 * 发布时自动置为私密
 * $postarr['ID'] 获取 post_id
 * add_filter 中的第4个参数用于指定给 auto_brave_private_post_format 函数传几个参数
*/
function auto_private_brave_post_format($data, $postarr) {
	$brave_post_format = get_post_format($postarr['ID']);
	$private_format_array = get_brave_custom_config('auto_private_post_format', false);
	if (in_array($brave_post_format, $private_format_array) && $data['post_status'] === 'publish') {
		$data['post_status'] = 'private';
	}
	return $data;
}

add_filter('wp_insert_post_data', 'auto_private_brave_post_format', 10, 2);

/* 异常评论错误提示
 * $comment_status String {spam | hold}
 * $check_type String: {email | IP}
 * $count Int
 */
function get_brave_comment_error_msg($comment_status, $check_type, $count = NULL) {
	$key = $check_type . '_' . $comment_status;
	$error_key = get_brave_error_config('code.' . $key);
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

// 加载 like 插件
include_once(get_template_directory() . '/plugin/like.php');
// 加载广告配置
include_once(get_template_directory() . '/plugin/display_ad.php');

?>