<?php

// 基础配置
function get_brave_basic_config($option = NULL) {
	$key = isset($option) ? 'basic.' . $option : 'basic';
	return get_brave_info($key);
}

// 评论配置
function get_brave_comment_config($option = NULL) {
	$key = isset($option) ? 'comment.' . $option : 'comment';
	return get_brave_info($key);
}

// 定制配置
function get_brave_custom_config($option, $append_format = true) {
	$format = get_post_format();
	$key = $append_format ? 'custom.' . $option . '.' . $format : 'custom.' . $option;
	return get_brave_info($key);
}

// 错误配置
function get_brave_error_config($option = NULL) {
	$key = isset($option) ? 'error.' . $option : 'error';
	return get_brave_info($key);
}

// 查询配置
function get_brave_query_config($option = NULL) {
	$key = isset($option) ? 'query.' . $option : 'query';
	return get_brave_info($key);
}

/**
 * 配置信息
 * $key String 支持字符串间以.号分隔层级, 目前只支持3个层级(即最多只能包含两个.号)
 * @return array | string
 */
function get_brave_info($key) {
	$config_array = array(
		'basic' => array(
			'excerpt_length' => 200, // 摘要的长度
			'display_ad' => false, // boolean 广告控制 true 显示广告, false 不显示
			'home_url' => home_url('/'),
			'site_name' => get_bloginfo('name'),
			'time_zone' => timezone_open(get_option('timezone_string')), // timezone 时区
			'asset_uri' => get_stylesheet_directory_uri() . '/assets', // css, js 文件路径
			'logo' => '', // logo url
			'beian' => '此处填写备案号', // 备案号
			'enable_post_format' => array(
				'standard', 'aside', 'status', 'chat', 'quote'
			),
			'post_format_name' => array(
				'Standard' => '标准',
				'Aside' => '博客',
				'Image' => '图像',
				'Video' => '视频',
				'Quote' => '引用',
				'Link' => '链接',
				'Gallery' => '图集',
				'Status' => '状态',
				'Audio' => '音频',
				'Chat' => '聊天'
			),
			'send_mail' => array(
				'to' => get_bloginfo('admin_email'), // 邮件接收人
			),
		),
		'query' => array(
			// feed控制参数
			'feed' => array(
				'after_date' => '7 days ago', // feed 输出内容的的起始时间
				'terms_not_in' => array(), // 排除的日志格式
			),
			// 首页查询控制参数
			'home' => array(
				'terms_not_in' => array('post-format-aside', 'post-format-status', 'post-format-chat', 'post-format-quote', 'post-format-audio', 'post-format-video', 'post-format-link'),
			),
			// 足迹
			'Here' => array(
				'terms_in' => array('post-format-status'),
			),
			// 博客
			'Blog' => array(
				'terms_in' => array('post-format-aside', 'post-format-video'),
			),
		),
		// 定制配置参数
		'custom' => array(
			'date' => array(
			),
			'role' => array(
			),
			'auto_private_post_format' => array(), // 发布时自动加密的格式
		),
		// 错误配置参数
		'error' => array(
			// 错误代码 具体详情见错误详情
			'code' => array(
				'email_spam' => '1001',
				'email_hold' => '1002',
				'IP_spam' => '1003',
				'IP_hold' => '1004',
				'impostor' => '1005',
				'channel_error' => '1006',
				'trackback_disabled' => '1007',
				'empty_comment' => '1008',
				'channel_error_wss' => '1009',
			),
			// 错误详情
			'msg' => array(
				'1001' => 'email 存在垃圾评论。',
				'1002' => 'email 存在待审核评论。',
				'1003' => 'IP 存在垃圾评论。',
				'1004' => 'IP 存在待审核评论。',
				'1005' => '请不要冒用管理员邮箱。',
				'1006' => '未启用的评论来源。',
				'1007' => '不接受 Trackback。',
				'1008' => '请输入您的评论内容。',
				'1009' => '未启用的评论来源。',
			),
		),

		// 评论控制参数 评论对象, 指依据评论者的 email 和 IP 确定的评论发起人, 下称评论对象或该对象
		'comment' => array(
			'check' => true, // boolean 是否启用评论控制
			'form_action_dir' => home_url(''), // 评论处理文件所在的目录
			'comment_channel_field' => 'comment_channel', // 用于标记评论来源的字段, 这个字段对用户是透明的, 防止直接走 wp-comments-post.php
			'IP' => '1 day ago', // 根据 IP 控制异常评论的开始时间范围: 自当前时间按此值倒退(-1 day 等同于 1 day ago)
			'email' => array( // 根据 email 控制异常评论参照的开始时间
				'spam' => NULL, // 根据 email 控制垃圾评论参照的开始时间: NULL 不限日期, 参考全部评论
				'hold' => '30 days ago', // 根据 email 控制待审核评论参照的开始时间
				'approve' => '1 hour ago', // 根据 email 控制已审核评论参照的开始时间
			),
			'comment_text_field' => 'little_star', // 评论文本框字段名称
			'comment_status_convert_array' => array(
				'hold' => '0',
				'spam' => 'spam',
				'trash' => 'trash',
				'approve' => '1',
			),
			'threshold' => array( // 评论控制数量阈值, 实际值等于 n+1 , 超过阈值将触发错误处理逻辑
				'spam' => 0, // 垃圾评论零容忍, 该对象存在一条垃圾评论或放在回收站的评论即触发
				'hold' => 2, // 为 2 则最多容许该对象生成3条待审核评论, 超过则不允许继续提交评论
				'approve' => 9, // 已通过的评论, 为 9 则指定时间段内该对象存在 10 条评论后, 后续该对象的评论会被置为待审核
			),
		),
	);
	$result;
	$key_array = explode('.', $key);
	$count_key = count($key_array);
	if ($count_key > 3) {
		$count_key = 3;
	} else if ($count_key === 0) {
		return;
	}
	for ($i = 0; $i < $count_key; $i++) {
		$config_array = get_array_key($config_array, get_array_key($key_array, $i));
		$result = $config_array;
	}
	return $result;
}

?>
