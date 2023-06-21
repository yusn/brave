<?php

/* --长恨春归无觅处, 不知转入此中来-- */

// 配置类
class Config {
	// 私有变量
	private $config_array;
	// 初始化私有变量(声明时不能使用表达式)
	public function __construct() {
		$this->config_array = array(
			/***** 可修改的配置项 开始 *****/
			'basic' => array(
				'excerpt_length' => 200, // 摘要的长度
				'display_ad' => false, // boolean 广告控制 true 显示广告, false 不显示
				'home_url' => get_bloginfo('url'), // get_bloginfo 请参照 https://developer.wordpress.org/reference/functions/get_bloginfo/
				'site_name' => get_bloginfo('name'),
				'site_description' => get_bloginfo('description'),
				'time_zone' => wp_timezone(), // timezone 时区
				'asset_uri' => get_stylesheet_directory_uri() . '/assets', // css, js 文件路径
				'logo' => '', // logo url 填入 logo url 将覆盖默认的图标
				'beian' => '此处填写备案号', // 备案号
				'enable_post_format' => array(
					'standard', 'aside', 'chat', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio'
				), // 要启用的格式
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
				), // 自定义格式名称
				'mail' => array(
					'receiver' => get_bloginfo('admin_email'), // 邮件接收人
				),
				'auto_space' => false, // boolean 是否启用自动空格(true 发布或更新日志将自动在汉字和单词间加空格)
				'auto_space_field' => array('post_title', 'post_content', 'post_excerpt'), // array 需要自动空格的字段（目前仅限 POST、PAGE）
			),
			'query' => array(
				// feed 过滤参数
				'feed' => array(
					'after_date' => '7 days ago', // feed 输出内容的的起始时间
					'terms_not_in' => array(), // 排除的日志格式
				),
				// 首页查询过滤参数
				'home' => array(
					'terms_not_in' => array('post-format-aside', 'post-format-status', 'post-format-chat', 'post-format-quote', 'post-format-audio', 'post-format-video', 'post-format-link'),
				),
				// Here 页面模板
				'Here' => array(
					'terms_in' => array('post-format-status'),
				),
				// Blog 页面模板
				'Blog' => array(
					'terms_in' => array('post-format-aside', 'post-format-video'),
				),
				'filter_video' => false, // 是否过滤视频, 开启后视频对访客不可见
			),
			// 定制配置参数
			'custom' => array(
				'date' => array(
				),
				'role' => array(
				),
				'peer_title' => '',
				'auto_private_post_format' => array(), // 发布时自动加密的格式, 如希望 status 和 link 在发布时自动加密, 请填入: 'status', 'link'
			),
			// 错误配置参数
			'error' => array(
				// 错误代码 具体详情见错误详情
				'code' => array(
					'email_spam' => '1001',
					'email_hold' => '1002',
					'IP_spam'    => '1003',
					'IP_hold'    => '1004',
					'impostor'   => '1005',
					'channel_error_hash_empty' => '1006',
					'trackback_disabled'       => '1007',
					'empty_comment'            => '1008',
					'channel_error'            => '1009',
					'channel_hash_check_fail'  => '1010',
					'channel_error_fake_chnl'  => '1011',
				),
				// 错误详情
				'msg' => array(
					'1001' => 'email 存在垃圾评论。',
					'1002' => 'email 存在待审核评论。',
					'1003' => 'IP 存在垃圾评论。',
					'1004' => 'IP 存在待审核评论。',
					'1005' => '冒用管理员邮箱。',
					'1006' => '未启用的评论来源。',
					'1007' => '不接受 Trackback。',
					'1008' => '请输入您的评论内容。',
					'1009' => '未启用的评论来源。',
					'1010' => '未启用的评论来源。',
					'1011' => '伪造评论来源。',
				),
				'is_report_error' => true, // 遇到错误时是否发邮件 
			),
	
			// 评论控制参数 评论对象, 指依据评论者的 email 和 IP 确定的评论发起人, 下称评论对象或该对象
			'comment' => array(
				'check' => true, // boolean 是否启用评论控制
				'comment_check_key' => 'BRAVE', // string 用于校验评论来源是否合法的(可自定义, 透明值无关紧要, 生成md5 hash 使用)
				'form_action_dir' => home_url(''), // 评论处理文件所在的目录
				'comment_channel_field' => 'comment_channel', // 用于标记评论来源的字段, 这个字段对用户是透明的, 防止直接走 wp-comments-post.php
				'IP' => '1 day ago', // 根据 IP 控制异常评论的开始时间范围: 自当前时间按此值倒退(-1 day 等同于 1 day ago)
				'email' => array( // 根据 email 控制异常评论参照的开始时间
					'spam' => NULL, // 根据 email 控制垃圾评论参照的开始时间: NULL 不限日期, 参考全部评论
					'hold' => '30 days ago', // 根据 email 控制待审核评论参照的开始时间
					'approve' => '1 hour ago', // 根据 email 控制已审核评论参照的开始时间
				),
				'comment_text_field' => 'real_comment', // 评论文本框字段名称
				// comment_status_convert_array 项是固定值不可修改
				'comment_status_convert_array' => array(
					'hold' => '0',
					'spam' => 'spam',
					'trash' => 'trash',
					'approve' => '1',
				),
				'threshold' => array( // 评论控制数量阈值, 超过阈值将触发错误处理逻辑
					'spam' => 1, // 垃圾评论零容忍, 该对象存在 1 条垃圾评论或放在回收站的评论即触发
					'hold' => 3, // 为 3 则最多容许该对象生成 3 条待审核评论, 超过则不允许继续提交评论
					'approve' => 9, // 已通过的评论, 为 9 则指定时间段内该对象存在 9 条评论后, 后续该对象的评论会被置为待审核
				),
			),
			/***** 可修改的配置项 结束 *****/
		);
	}
	
	/* 取分组数组内的元素 */
	public function get_config($group) {
		$group = $this->config_array[$group];
		return function() use($group) {
			return $group;
		};
	}
	
	/* 获取广告 */
	public function display_ad($ad_name) {
		$display_ad = $this->config_array['basic']['display_ad'];
		if (display_ad && is_callable($this->$ad_name())) {
			return $ad_name();
		}
	}
};

?>
