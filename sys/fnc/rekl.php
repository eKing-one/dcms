<?php
/**
 * 显示指定类型的广告内容
 *
 * 该函数根据传入的选择参数 $sel 从数据库中查询广告数据，并根据条件输出广告的链接或图片。
 * 支持动态调整广告显示逻辑，并处理不同的跳转方式。
 *
 * @param int $sel 广告类型选择参数，用于筛选广告数据
 * @return void 无返回值，直接输出广告 HTML 内容
 */
function rekl($sel) {
	global $set; // 引用全局配置变量 $set，用于判断是否在新窗口打开链接

	// 如果 $sel 为 3 且当前页面不是首页，则将 $sel 调整为 4
	if ($sel == 3 && $_SERVER['PHP_SELF'] != '/index.php') {
		$sel = 4;
	}

	// 查询数据库，获取符合条件的广告数据
	// 条件：广告类型为 $sel 且最后显示时间大于当前时间，按 ID 升序排列
	$q = dbquery("SELECT * FROM `rekl` WHERE `sel` = '$sel' AND `time_last` > '" . time() . "' ORDER BY id ASC");

	// 循环处理每条广告记录
	while ($post = dbassoc($q)) {
		// 如果广告类型为 2，显示特定的广告图标
		if ($sel == 2) {
			echo icons('rekl.png', 'code'); // 输出广告图标 HTML
		}

		// 根据 $post['dop_str'] 判断链接类型并生成 <a> 标签
		if ($post['dop_str'] == 1) {
			// 如果 dop_str 为 1，使用站内跳转链接 /go.php
			echo '<a' . ($set['web'] ? ' target="_blank"' : null) . ' href="//' . $_SERVER['SERVER_NAME'] . '/go.php?go=' . $post['id'] . '">';
		} else {
			// 否则直接使用广告记录中的外部链接
			echo '<a' . ($set['web'] ? ' target="_blank"' : null) . ' href="' . $post['link'] . '">';
		}

		// 判断是否有图片，若无则显示广告名称，否则显示图片
		if ($post['img'] == NULL) {
			echo $post['name']; // 输出广告名称文本
		} else {
			// 输出广告图片，包含 alt 属性以提高可访问性
			echo '<img src="' . $post['img'] . '" alt="' . $post['name'] . '" />';
		}

		// 闭合 <a> 标签并添加换行符
		echo '</a><br />';
	}
}
