<?php
	/** 
	 * 日期格式检查
	 * https://www.php.net/manual/zh/function.checkdate.php
	 */
	function brave_check_date($date, $format = 'Y-m-d H:i:s') {
		$the_date = DateTime::createFromFormat($format, $date);
		return $the_date && $the_date->format($format) == $date;
	}
	
	/** 
	 * 数组空值检查
	 * $array 要检查的目标数组
	 * $key_array 由期望目标数组中不能为空的键组成的数组
	 */
	function brave_check_null($array, $key_array) {
		if (!count($key_array)) {
			return;
		}
		$array = $array ? $array : [];
		$result = [];
		foreach ($key_array as $key => $val) {
			$check = $array[$val];
			// [] === $check 也不允许空数组
			if (null === $check || '' === $check) {
				$result[] = $val . '不允许为空';
			}
		}
		if (count($result)) {
			return fm_die( implode(',', $result) );
		}
	}
	
	/** 
	 * 返回错误
	 * $code 错误代码
	 * $message 错误信息
	 */
	function brave_die($message = NULL, $code = -1) {
		// https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/#permissions-callback
		return new WP_Error($code , $message);
	}
	
	/**
	 * 获取数组键值
	 * $array array
	 * $key string | Int 数组键 或 数组索引。 支持多级键, 以.号分隔, 示例: get_array_key(array, 'key1.key2');
	 * @return
	 * wp 已有类似函数 _wp_array_get 对键的使用略有不同
	 */
	function get_array_key($array, $key) {
		if (!is_array($array) || (!is_string($key) && !is_int($key))) {
			return;
		}
		
		// 传入的是键 或 索引
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
	 * 在指定数组上忽略指定键并返回新数组(不改变原数组)
	 * $array array 需要处理的目标数组
	 * $omit_key array 由需要在 $array 中移除的键组成的数组
	 * @return array 返回忽略指定键后的数组结果
	 * array-diff: https://www.php.net/manual/zh/function.array-diff.php
	 */
	function omit_array_key($array, $omit_key) {
		$all_key = array_keys($array);
		$pick_key = array_diff($all_key, $omit_key);
		return pick_array($array, $pick_key);
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
	 * wp 有类似函数 _wp_array_set 支持多级键值设置
	 */
	function set_array_key(&$array, $key, $val) {
		$array[$key] = $val;
	}
	
	/**
	 * 拣选数组键/值/对
	 * $array array 来源数组
	 * $pick_key_array 需要拣选的 $array 的键或索引, 如: ['a', 'b'] / [0, 1]
	 * $return_type string 拣选目标, 支持对 键/值/键值对 的拣选
	 * return array
	 * 模仿 JavaScript lodash 的 pick 方法
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
	
	/**
	 * $array array 需要移除空格的数组
	 * $trim_key_array array 指定对 $array 中哪些键的值做去空格处理
	 * method string 如何去重空白，默认前后都去
	 */
	function trim_array($array, $trim_key_array = [], $method = 'trim') {
		if ( empty($trim_key_array) ) {
			$trim_key_array = array_keys($array);
		}
		foreach($trim_key_array as $val) {
			if ( is_array( $array[$val] ) ) {
				$array[$val] = _trim_array($array[$val], [], $method);
			}
			$array[$val] = $method($array[$val]);
		}
		return $array;
	}
	
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
?>