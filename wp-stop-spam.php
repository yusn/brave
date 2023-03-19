<?php

/**
 * 来源 wp-comments-post.php.
 */
if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
	$protocol = $_SERVER['SERVER_PROTOCOL'];
	if ( ! in_array( $protocol, array( 'HTTP/1.1', 'HTTP/2', 'HTTP/2.0', 'HTTP/3' ), true ) ) {
		$protocol = 'HTTP/1.0';
	}

	header( 'Allow: POST' );
	header( "$protocol 405 Method Not Allowed" );
	header( 'Content-Type: text/plain' );
	exit;
}

/**
 * 根据路径定义 __ROOT__
 * 当前文件要么放在网站根目录, 要么放在 wp-content 之后
 */
$current_dir = dirname(__FILE__);
$position = stripos($current_dir, 'wp-content');
if ($position === false) {
	define('__ROOT__', dirname(__FILE__));
} else {
	$arr = explode(DIRECTORY_SEPARATOR, substr($current_dir, $position));
	$depth = count($arr) + 1;
	define('__ROOT__', dirname(__FILE__, $depth));
}

/**
 * 加载运行环境.
 */
require_once(__ROOT__ . '/wp-load.php');

// 检查是否开启评论控制
$is_check = false;
if (function_exists('get_brave_config')) {
	$comment_config_array = get_brave_config('comment');
	$is_check = $comment_config_array['check'];
}

if ($is_check) {
	
	/* --千里之行, 始于足下-- */
	
	// step.0: 清缓存
	nocache_headers();
	
	// step.1: 默认评论字段必须为空, 且不能存在 comment_channel
	$comment = trim(get_array_key($_POST, 'comment'));
	$comment_channel_field = $comment_config_array['comment_channel_field'];
	$has_comment_channel = array_key_exists($comment_channel_field, $_POST);
	if (!empty($comment) || $has_comment_channel) {
		return get_brave_error_msg('channel_error'); // 评论来源异常
	}
	if ($has_comment_channel) {
		return get_brave_error_msg('channel_error_fake_chnl'); // 试图伪造评论来源标记
	}
	
	
	// step.2: 自定义评论框必须不能为空
	$comment_text_field = get_brave_comment_text_field();
	$real_comment = trim(get_array_key($_POST, $comment_text_field));
	if (empty($real_comment)) {
		return get_brave_error_msg('empty_comment'); // 评论内容为空
	}
	
	/** 
	 * step.3: 字段校验, 至此至少评论是空的, 否则检测不到错误意味着评论已经写入数据库了
	 * 来自 wp-comments-post.php
	*/
	$error_obj = wp_handle_comment_submission( wp_unslash( $_POST ) );
	$error_title = 'Comment Submission Failure';
	if ( is_wp_error( $error_obj ) ) {
		$code = $error_obj->get_error_code();
		if ( $code !== 'require_valid_comment' ) {
			$error_key = (int) $error_obj->get_error_data();
			$error_val = $error_obj->get_error_message();
			return get_brave_die($error_key, $error_val, $error_title); // 返回错误
		}
	}
	
	// step.4: 执行异常评论检测
	require_once(get_template_directory() . '/plugin/check_comment_history.php');
	
	// step.5: 通过检测, 替换评论框
	$_POST['comment'] = $real_comment;
	
	// step.6: 添加来源 防止直接走 wp-comments-post.php
	$_POST[$comment_channel_field] = get_brave_secure_auth('comment', 'comment_check_key');
}

// 最后, 交给系统处理
require_once(__ROOT__ . '/wp-comments-post.php');
