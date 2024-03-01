<?php

// remove emoji
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');
remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

remove_filter('the_excerpt', 'wpautop');
remove_filter('the_excerpt', 'wptexturize');
remove_filter('the_content', 'wptexturize');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('wp_head', 'wp_print_scripts');
remove_action('wp_head', 'wp_print_head_scripts');
remove_action('pre_post_update', 'wp_save_post_revision');

// remove max-image-preview
remove_filter ('wp_robots', 'wp_robots_max_image_preview_large');

// remove_action('wp_head', 'index_rel_link');
// remove_action('wp_head', 'adjacent_posts_rel_link');
// remove_action('wp_head', 'wp_shortlink_wp_head');

// Remove wp-json and HTTP header link 
remove_action('wp_head', 'rest_output_link_wp_head');

/* remove global-styles-inline-css
 * https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
 * https://wordpress.org/support/topic/remove-global-styles-inline-css/
 */
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');

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

// Require Tool
require_once(get_template_directory() . '/plugin/tool.php');

/**
 * 提供获取配置的对外接口
 * $group string
 * $item string
 * @return json
 *
 * 函数解析: 本请求最后会被组成函数调用 get_brave_config(basic, asset_uri);
 * 
 * @see: https://developer.wordpress.org/reference/hooks/wp_ajax_action/
 * wp_send_json: https://developer.wordpress.org/reference/functions/wp_send_json/
 */
// add_action('wp_ajax_nopriv_get_brave_config_intf', 'get_brave_config_intf');
// add_action('wp_ajax_get_brave_config_intf', 'get_brave_config_intf');

function get_brave_config_intf($request_data) { 
	$group = sanitize_text_field($request_data['group']);
	$item = sanitize_text_field($request_data['item']);
	// 暂时只响应 asset_uri
	if (!isset($group) || !isset($item) || $group !== 'basic' || $item !== 'asset_uri') {
		exit();
	}
	$response = [];
	$func = 'get_brave_config';
	if (function_exists($func)) {
		$response[$item] = $func($group, $item);
		return $response;
	}
}

// 脚本配置
function brave_scripts_styles() {
	$asset_uri = get_brave_config('basic', 'asset_uri');
	if (!is_admin()) {
		wp_deregister_script('jquery');
		wp_enqueue_script('family', $asset_uri . '/family.min.js', '', false, true);
		wp_localize_script('family', '_brave', array('nonce' => wp_create_nonce('wp_rest')));
	}
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply', '', '', false, true);
	}
    wp_dequeue_style('classic-theme-styles');
	wp_dequeue_style( 'wp-block-library' );
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
	register_nav_menu('primary', __('Primary Menu', 'brave')); // 注册菜单以供 wp_nav_menu() 使用
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
function filter_brave_query($query) {
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

add_action('pre_get_posts', 'filter_brave_query');

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
            <a href="<?php the_permalink(); ?>">
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
function get_brave_content_nav($method = 'next') {
	global $wp_query;
	if ($wp_query->max_num_pages > 1) : ?>
        <div class="pagination inner clear">
			<?php
				if ($method === 'next') {
					next_posts_link(__('&hellip;', 'brave'));
				} else {
					previous_posts_link(__('上一页...', 'brave' ));
				}
			?>
        </div>
	<?php endif;
}

// add next pagination style class
function add_brave_next_class() {
	return 'class = "next"';
}

add_filter('next_posts_link_attributes', 'add_brave_next_class');

// 评论是降序显示的, 最新的在前, 故下一页其实是上一页
add_filter('previous_comments_link_attributes', 'add_brave_next_class');

// add prev pagination style class
function add_brave_prev_post_link_class() {
	return 'class = "prev"';
}

add_filter('previous_posts_link_attributes', 'add_brave_prev_post_link_class');

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

function get_brave_role($role = NULL) {
	$role = $role ? $role : get_post_format();
	return printf(get_brave_config('custom', 'role.' . $role));
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
	$detect = get_mobileDetect();
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
	$group = get_post_meta($post->ID);
	// 纬度, 经度, 城市, 坐标公开状态(1公开,0不公开),天气, 温度
	$group = pick_array($group, ['geo_latitude', 'geo_longitude', 'geo_city', 'geo_public', 'wx_weather', 'wx_temp']);
	array_walk($group, function($val, $key) use (&$group) {$group[$key] = $val[0];});
	extract($group, EXTR_SKIP);

	$html_string = '';
	if (isset($geo_public)) {
		$local = $geo_public === '1' ? 'i-local' : 'i-pin';
		if ($geo_public === '1' || current_user_can('administrator')) {
			if (isset($geo_latitude) && isset($geo_longitude) ) {
				$html_string .= '<span class="ml"><a class="gmap" href="//maps.google.com/maps?q=$geo_latitude,$geo_longitude&hl=zh-cn&t=m&z=15" itemprop="map" itemtype="//schema.org/Place"><span class="' . $local . '"></span></a></span>';
			}
			if (isset($geo_city)) {
				$html_string .= '<span class="ml-tiny font-tiny">' . $geo_city . '</span>';
			}
		} elseif ($geo_public === '0') {
			if (isset($geo_latitude) && isset($geo_longitude) ) {
				$html_string .= '<span class="' . $local . ' ml" itemprop="map" itemtype="//schema.org/Place"></span>';
			}
		}
	}
	// 天气
	if (isset($wx_weather)) {
		$html_string .= '<span class="ml">' . $wx_weather . '</span>';
	}
	// 温度
	if (isset($wx_temp)) {
		$html_string .= '<span class="ml">' . $wx_temp . '</span>';
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
			case 'Safari':
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
function replace_brave_avatar($avatar_url) {
	return str_replace(array('www.gravatar.com', 'secure.gravatar.com', '1.gravatar.com', '2.gravatar.com'), 'cravatar.cn', $avatar_url);
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
            <span class='mr12 c4 f14'><?php comment_type(); ?></span><?php comment_author_link(); ?>
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
					<?php comment_reply_link(array_merge($args, array('reply_text' => __('<span class="i-reply"></span>Reply', 'brave'), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
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
		$comment_status = pick_array($status_convert_array, $comment_status_array);
		$comment_status = "'" . implode("','", $comment_status) . "'";
		/* 置换 $comment_status_array 为 sql 条件 结束 */

		// 获取查询开始时间
		$interval = get_array_key($comment_config_array, $check_key);
		$comment_approved_time = add_brave_interval('now', $interval);

		// 区别: comment_date 系统设置的时区时间, comment_date_gmt 格林尼治时间
		global $wpdb;
		$comments = $wpdb->get_row($wpdb->prepare("SELECT count(1) as ttl_comment FROM $wpdb->comments WHERE comment_approved in ($comment_status) and comment_date >= %s and comment_author_IP = %s", $comment_approved_time, $comment_IP));

		// 返回评论条数
		return $comments->ttl_comment;
	}
}

// 获取同期日志标题
function get_brave_peer_title($compare_role) {
	return printf(get_brave_config('custom', 'peer_title')) . get_brave_role($compare_role); 
}

/**
 * 添加 WHERE 条件和 JOIN 子句
 * $date_array array 格式对应的日期数组
 * $interval string 日期或时间间隔
 */
function filter_brave_peer_query($date_array, $interval) {
	// 添加 WHERE 条件, 组成 (() OR ()) 这样的条件
	global $wpdb;
	$date_where = [];
	foreach($date_array as $role => $date) {
		$from_date = add_brave_interval($date, $interval - 3 . ' day');
		$to_date = add_brave_interval($date, $interval + 4 . ' day -1 second');
		array_push($date_where, "($wpdb->posts.post_date BETWEEN '$from_date' AND '$to_date' AND $wpdb->terms.slug IN ( 'post-format-$role' ) )");
	}
	$where_str = "(" . implode(" OR ", $date_where) . ")";
	
	add_filter('get_date_sql', function ($where) use($where_str, $wpdb) {
		return $where . "AND $wpdb->term_taxonomy.taxonomy IN ( 'post_format' ) AND " . $where_str;
	}, 10, 1);
	
	// 添加 JOIN 子句
	function add_brave_join_clause($join, $wp_query) {
		global $wpdb;
		$join = "INNER JOIN $wpdb->term_relationships ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
				INNER JOIN $wpdb->terms ON $wpdb->term_relationships.term_taxonomy_id = $wpdb->terms.term_id
				INNER JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id";
		return $join;
	}
	
	add_filter( 'posts_join', 'add_brave_join_clause', 10, 2);
}

/**
 * 获取同期日志查询参数
 * $compare_format string 指定需要获取其同期日志的格式
 * @return array 返回查询参数数组
 */
function get_brave_peer_post_query($compare_format = NULL) {
	$date_array = get_brave_config('custom', 'date');
	if (empty($date_array) || count($date_array) < 2) {
		return;
	}
	// 计算日期差, 返回相差的天数
	$start_date = $date_array[get_post_format()];
	$end_date = get_the_time('Y-m-d'); // 当前日志的发布时间
	$interval = get_brave_interval($start_date, $end_date, '%a');
	if (empty($compare_format)) {
		$date_array = omit_array_key($date_array, [get_post_format()]);
	} else {
		$date_array = pick_array($date_array, [$compare_format]);
	}
	// 处理查询条件, 以获取同期日志
	filter_brave_peer_query($date_array, $interval);
	// 返回查询参数
	return array(
		'orderby' => 'rand',
		'showposts' => 4,
		'post__not_in'   => array(get_the_ID(), get_option('sticky_posts')),
		'date_query' => array('inclusive' => true), // 保留此行, 否则 filter_brave_peer_query 里的过滤器挂不上
	);
}

/**
 * 获取两个日期的间隔
 * $start_date string
 * $end_date string
 * @return string 默认获取的间隔单位为天数 https://www.php.net/manual/zh/datetime.diff.php
 */
function get_brave_interval($start_date, $end_date, $format = '%a', $timezone = NULL) {
	$timezone = $timezone ? $timezone : get_brave_config('basic', 'time_zone');
	$interval = date_diff(date_create($end_date, $timezone), date_create($start_date, $timezone));
	return $interval->format($format);
}

/**
 * 增加日期间隔
 * $start_date String 开始日期,
 * $interval String 默认实时
 * $date_format String 时间格式 默认 Y-m-d H:i:s
 * $timezone timezone 时区类型
 * @return String 返回增加间隔后的日期字符串
 */
function add_brave_interval($start_date = 'now', $interval = '0 day', $date_format = 'Y-m-d H:i:s', $timezone = NULL) {
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
function append_brave_filename_hash($filename) {
	// 修复通过 wordPress APP 上传图片两次附加随机字符串的问题, mw_newMediaObject 会多调用一次 sanitize_file_name
	static $cache = [];
	if (isset($cache[$filename])) {
		return $filename;
	};
	$file_ext = pathinfo($filename, PATHINFO_EXTENSION);
	$file_name = pathinfo($filename, PATHINFO_FILENAME);
	$hash_string = get_brave_hash(rand(8, 16));
	$filename = $file_name . '_' . $hash_string . '.' . $file_ext;
	$cache[$filename] = 1;
	return $filename;
}

add_filter('sanitize_file_name', 'append_brave_filename_hash');

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
function send_brave_mail($to, $title, $body, $header = NULL) {
	$header = $header ? $header : array('Content-Type: text/html; charset=UTF-8');
	wp_mail($to, $title, $body, $header);
}

add_action('set_brave_async_mail', 'send_brave_mail', 10, 4);

// 异步发送邮件
function send_brave_async_mail($to, $title, $body, $header = NULL) {
    wp_schedule_single_event(time() + 300, 'set_brave_async_mail', array($to, $title, $body, $header));
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

// 是否开启评论检测
function is_brave_check_comment() {
	// 已登陆用户不检测, 未登陆用户通过配置文件来控制是开启评论检测
	return is_user_logged_in() ? false : get_brave_config('comment', 'check');
}

// 评论预处理
function preprocess_brave_comment($commentdata) {
	// 修复 WordPress APP 缺失评论来源无法回复评论的问题
	if (is_user_logged_in()) {
		return $commentdata;
	}
	// 防止直接走 wp-comments-post.php
	$is_check = is_brave_check_comment();
	if ($is_check) {
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

add_filter('preprocess_comment', 'preprocess_brave_comment');

// 添加统计代码, 不统计登录用户
function add_brave_analytics() {
	if (!is_user_logged_in() && function_exists(get_brave_analytics())) {
		return get_brave_analytics();
	}
}

add_action('wp_footer', 'add_brave_analytics');

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
		'~[ ]*([「」『』（）〈〉《》【】〔〕〖〗〘〙〚〛])[ ]*~u',
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
	/* $update: 内容未变动, 点更新按钮为 true; 内容有改变 $update 为 false
	 * $parent_id: wp_get_post_parent_id 要看有没使用保存版本, 有使用(内容未变更)就等于 0, 没有使用(内容有变更)大于 0
	 * 点新建会触发 save_post, 此时生成 $post_id, wp_get_post_parent_id 返回值是 0, update 是 false
	 * 内容为空, 点保存草稿/发布 都不会触发 save_post, 此时发布不会成功
	 * 自动保存不会触发 save_post
	 * 发布前点保存, 且内容有变更触发 save_post, wp_get_post_parent_id 返回当前日志 id(>0), update 为 false
	 */
	$parent_id = wp_get_post_parent_id($post_id);
	// $current_meta = get_post_meta($post_id, 'post_device_name', true);
	// 只在新建时增加设备信息
	if ($update === true || $parent_id !== 0) {
		return;
	}
	
	$detect = get_mobileDetect();
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
		add_post_meta($post_id, 'post_device_name', $device_name);
		add_post_meta($post_id, 'post_device_ver', $device_ver);
	}
}

add_action('save_post', 'set_brave_post_device_meta', 10, 3);

// 保存评论者的设备信息
function set_brave_comment_device_meta($comment_ID) {
	if (!current_user_can('administrator') && !is_admin()) {
		$detect = get_mobileDetect();
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
 * 过滤日志中的视频, 默认未开启, 自传视频体积太大
 */
function hidden_brave_video($output, $atts, $video, $post_id, $library) {
    $filter_video = get_brave_config('query', 'filter_video');
	if ($filter_video && !current_user_can('administrator')) {
		$output = '[视频暂不可见]';
	}
	return $output;
}

add_filter('wp_video_shortcode', 'hidden_brave_video', 10, 5);

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
	
	// 先发邮件报告
	$is_report_error = get_brave_config('error', 'is_report_error');
	if ($is_report_error) {
		$body = $error_name . '(' . $error_key . '): ' . $error_val;
		$title = $title ? $title : 'ERROR REPORT';
		$to = get_brave_config('basic', 'mail.receiver');
		send_brave_async_mail($to, $title, $body, NULL);
	}
	return get_brave_die($error_key, $error_val, $title);
}

/**
 * 获取配置
 * $group string
 * $item string
 * @return string | array
 */
function get_brave_config($group, $item = NULL) {
	static $config, $cached = [];
	
	// step1. 有缓存的直接取缓存
	if (isset($cached[$group])) {
		return empty($item) ? $cached[$group] : get_array_key($cached[$group], $item);
	}
	
	// step2. 没有缓存的重新取
	if (!isset($config)) {
		include_once(get_template_directory() . '/conf/config.php');
		$config = new Config();
	}
	// 获取闭包函数, 闭包函数返回 $group 键对应的元素
	$func = $config->get_config($group);
	$group_array = $func();
	
	// 不传 $item 或 传入空值, 缓存并返回整个 $group; 传 $item, 返回 $item 路径下的值
	return empty($item) ? $cached[$group] = $group_array : get_array_key($group_array, $item);
}

/**
 * 获取 MobileDetect 对象实例
 */
function get_mobileDetect() {
	static $cache = [], $key = 'mobileDetect';
	return isset($cache[$key]) ? $cache[$key] : $cache[$key] = new Detection\MobileDetect;
}


/**** 加载插件 START ****/

// 加载 MobileDetect 插件
include_once(get_template_directory() . '/plugin/MobileDetect/MobileDetect.php');

// 加载 like 插件
include_once(get_template_directory() . '/plugin/like.php');

// 加载广告配置
include_once(get_template_directory() . '/plugin/display_ad.php');

// Require gallery.php
require_once(get_template_directory() . '/plugin/gallery.php');

/**** 加载插件 END ****/

/**
 * 替换默认前缀 wp-json, 注意: 每一次更改此前缀都需要到【设置】-【固定链接】, 点一下保存【更改按钮】, 以刷新路由重定向规则使之生效
 *
 * @see https://developer.wordpress.org/reference/hooks/rest_url_prefix/
 */
function rename_brave_rest_url_prefix() {
	return 'api';
}

add_filter('rest_url_prefix', 'rename_brave_rest_url_prefix'); 

// printf(rest_get_url_prefix()); // 验证前缀是否被替换

/**
 * 注册 REST 路由
 */

$brave_namespace = '/v1';
function register_brave_router() {
	global $brave_namespace; // wp 为系统保留使用, 不建议使用
	register_rest_route(
		$brave_namespace,
		'/post_like',
		get_brave_router('post_like'),
	);
	register_rest_route(
		$brave_namespace,
		'/get_config',
		get_brave_router('get_config'),
	);
}

add_action('rest_api_init', 'register_brave_router');

/**
 * Register meta keys for posts
 * rest api posts 接口只能在 meta 中使用这里注册的自定义字段
 */
function register_brave_post_meta() {
	$brave_post_meta = array('geo_latitude', 'geo_longitude', 'geo_city', 'geo_public', 'wx_weather', 'wx_temp', 'post_device_name', 'post_device_ver');
	$arg = array(
					'single'       => true,
					'type'         => 'string',
					'default'      => '',
					'show_in_rest' => true,
				);
	array_walk($brave_post_meta, function ($val, $key, $arg) {register_meta('post', $val, $arg);}, $arg);
}

add_action('init', 'register_brave_post_meta');


// 获取路由参数选项
function get_brave_router($route) {
	$router_config = array(
		'post_like' => array(
			'methods'  => 'POST', // HTTP METHOD, 支持逗号分割的字符串, 或字符串数组, 如: 'GET,POST' 或 array('POST', 'PUT');
            'callback' => 'process_simple_like', // 处理路由请求的最终函数
			'permission_callback' => '__return_true', // 这是一个回调函数, 若对外公开需要返回 true; 否则, 返回 false. 可以通过此回调函数来判断处理用户权限. for security
		),
		'get_config' => array(
			'methods'  => 'POST',
            'callback' => 'get_brave_config_intf',
			'permission_callback' => '__return_true',
		),
	);
	return $router_config[$route];
}


/**
 * Filters the response immediately after executing any REST API callbacks
 *
 * @see https://developer.wordpress.org/reference/hooks/rest_request_after_callbacks/
 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
 */
function handle_brave_rest_request_after_callbacks( $response, $handler, $request ) {
	global $brave_namespace;
	if (!str_starts_with($request->get_route(), $brave_namespace)) {
		return $response;
	}
	// $http_status_code = 200; // rest_authorization_required_code();
	$response_array;
	if (is_wp_error( $response )) {
		$response_array = array(
			'code'    => -1,
			'error_code' => $response->get_error_code(),
			'error_message' => $response->get_error_message(),
		);
	} else if ($response instanceof Exception) {
		$response_array = array(
			'code'    => -1,
			'error_message' => $response->getMessage(),
		);
	} else {
		$response_array = array(
			'code'    => 0,
			'data'    => $response,
		);
	}
	$response = new WP_REST_Response(
		array_merge(
			$response_array,
			array(
				// 'request' => $request->get_params(),
				'method'  => $request->get_method(),
			),
		),
		// $http_status_code,
	);
	return $response;
}

add_filter('rest_request_after_callbacks', 'handle_brave_rest_request_after_callbacks', 9, 3);



/**
 * Filters the REST API dispatch request result
 *
 * @see https://developer.wordpress.org/reference/hooks/rest_dispatch_request/
 */
function handle_brave_rest_dispatch_request($null, $request, $route, $handler) {
	global $brave_namespace;
	if (str_starts_with($request->get_route(), $brave_namespace)) {
		return call_user_func( $handler['callback'], wp_unslash($request->get_json_params()) );
	}
}

add_filter('rest_dispatch_request', 'handle_brave_rest_dispatch_request', 10, 4);

/**
 * Sets the default exception handler if an exception is not caught within a try/catch block.
 * Execution will stop after the callback is called.
 */
set_exception_handler('conn_global_exception_cb');
function conn_global_exception_cb(Throwable $exception) {
	$err = 'Uncaught exceptiod: ' . $exception->getMessage();
	echo $err;
}
?>
