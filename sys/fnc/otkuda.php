<?php
if (isset($_SESSION['refer']) && $_SESSION['refer'] != NULL && !preg_match('#(rules)|(smiles)|(secure)|(aut)|(reg)|(umenu)|(zakl)|(mail)|(anketa)|(settings)|(avatar)|(info)\.php#',$_SERVER['SCRIPT_NAME'])) $_SESSION['refer'] = NULL;

/**
 * 该函数用于根据传入的 URL 路径，生成一个描述用户从哪里来的文字，并提供相应的超链接。
 * 
 * @param string $ref 需要处理的 URL 路径，通常是 `$_SERVER['REQUEST_URI']` 或类似的 URL 地址
 * 
 * @return string 返回一个带有描述和链接的 HTML 片段，描述用户当前所在的页面或从哪个页面跳转而来
 * 
 * 函数逻辑说明：
 * - 如果 URL 路径包含某些特定的字符串（如 `/forum/`, `/chat/` 等），则返回一个描述用户所在页面的文字，
 *   例如：正在访问论坛、聊天室、新闻中心等，并附上该页面的链接。
 * - 如果 URL 路径不符合上述规则，则默认返回一个通用描述，指明用户是从某个地方访问的首页。
 * 
 * 用法示例：
 * - 如果传入的 `$ref` 是 `/forum/some-thread`，函数会返回：
 *     ' 正在 <a href="/forum/">论坛</a> '
 * - 如果传入的 `$ref` 是 `/chat/`，函数会返回：
 *     ' 正在 <a href="/chat/">聊天室</a> '
 * 
 * 该函数适用于动态生成页面导航和描述，常用于记录用户的来源页面。
 */
function otkuda($ref) {
	$ref = $ref ?? '';
	// 判断用户是否从论坛页面来
	if (preg_match('#^/forum/#', $ref)) {
		$mesto = ' 正在 <a href="/forum/">论坛</a> ';
		// 判断用户是否从聊天室页面来
	} elseif (preg_match('#^/chat/#', $ref)) {
		$mesto = ' 正在 <a href="/chat/">聊天室</a> ';
		// 判断用户是否从新闻中心页面来
	} elseif (preg_match('#^/news/#', $ref)) {
		$mesto = ' 正在阅读 <a href="/news/">新闻中心</a> ';
		// 判断用户是否从留言板页面来
	} elseif (preg_match('#^/guest/#', $ref)) {
		$mesto = ' 正在查看 <a href="/guest/">留言板</a> ';
		// 判断用户是否从用户页面来
	} elseif (preg_match('#^/user/users\.php#', $ref)) {
		$mesto = ' 正在查看 <a href="/user/users.php">用户</a> ';
		// 判断用户是否从在线用户页面来
	} elseif (preg_match('#^/online\.php#', $ref)) {
		$mesto = ' 正在查看 <a href="/user/online.php">在线用户</a> ';
		// 判断用户是否从在线游客页面来
	} elseif (preg_match('#^/online_g\.php#', $ref)) {
		$mesto = ' 看看谁进来了 <a href="/user/online_g.php">在线游客</a> ';
		// 判断用户是否从注册页面来
	} elseif (preg_match('#^/reg\.php#', $ref)) {
		$mesto = ' 正在 <a href="/user/reg.php">注册</a> ';
		// 判断用户是否从下载中心页面来
	} elseif (preg_match('#^/down/#', $ref)) {
		$mesto = ' 坐在 <a href="/down/">下载中心</a> ';
		// 判断用户是否从登录页面来
	} elseif (preg_match('#^/aut\.php#', $ref)) {
		$mesto = ' 正在 <a href="/user/aut.php">登录网站</a> ';
		// 判断用户是否从主页页面来
	} elseif (preg_match('#^/index\.php#', $ref)) {
		$mesto = ' 访问 <a href="/index.php">网站主页</a> ';
		// 判断用户是否直接访问首页或其他路径
	} elseif (preg_match('#^/\??$#', $ref)) {
		$mesto = ' 访问 <a href="/index.php">网站主页</a> ';
		// 默认情况下，说明用户来自其他页面
	} else {
		$mesto = ' 从某个地方来到 <a href="/index.php">网站</a> ';
	}
	// 返回生成的描述文字
	return $mesto;
}