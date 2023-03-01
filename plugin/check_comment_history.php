<?php

/* check_comment_history
 * 异常评论判断 开始
**/
// 不控制管理员
if (current_user_can('administrator')) {
	return;
}

$comment_email = trim(get_array_key('_POST', 'email'));

// 校验 email 合法性
if (!is_email($comment_email)) {
	return; // 地址无效直接返回(交给系统自身处理, 生成错误)
}

// check_1: 判断是否冒充管理员邮箱
$admin_email = get_bloginfo('admin_email');
if (!current_user_can('administrator') && $comment_email === $admin_email) {
	$code_array = get_brave_info('error_code.impostor');
	clear_brave_cache();
	wp_die('别干坏事! 异常代码: ' . $code_array);
}

// check_2: 根据 email 检测是否存在异常评论
if ($comment_email) {
	$check_key_word = 'email';
	check_brave_comment($check_key_word, $comment_email);
}

// check_3: 根据 IP 地址检测是否存在异常评论
// 获取评论者的 IP
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$get_HTTP_X_FORWARDED_FOR = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	$_SERVER['REMOTE_ADDR'] = trim($get_HTTP_X_FORWARDED_FOR[0]);
}
$comment_IP = trim($_SERVER['REMOTE_ADDR']);
if ($comment_IP) {
	$check_key_word = 'IP';
	check_brave_comment($check_key_word, $comment_IP);
}
/** 异常评论判断 结束 **/