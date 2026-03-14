<?php
require_once 'sys/inc/start.php';
require_once 'sys/inc/compress.php';
require_once 'sys/inc/sess.php';
require_once 'sys/inc/home.php';
require_once 'sys/inc/settings.php';
require_once 'sys/inc/db_connect.php';
require_once 'sys/inc/ipua.php';
require_once 'sys/inc/fnc.php';
require_once 'sys/inc/user.php';
$set['title'] = '外部链接跳转';
require_once 'sys/inc/thead.php';
title();

$decoded_url = base64_decode($_GET['url'] ?? '');
$goId = intval($_GET['go'] ?? false);

if (empty($decoded_url) || empty($goId) || (dbresult(dbquery("SELECT COUNT(*) FROM `rekl` WHERE `id` = '{$goId}'"), 0) == 0 && !preg_match('#^(https?://|//)#', $decoded_url))) {
	http_response_code(404);
} elseif (preg_match('#^(https?://|//)#', $decoded_url)) {
	if (isset($_SESSION['adm_auth'])) unset($_SESSION['adm_auth']);
	// 如果是“//”开头，补全当前页面的协议
	$final_url = preg_match('#^//#', $decoded_url) ? get_http_type() . ':' . $decoded_url : $decoded_url;
	header("Location: " . $final_url);
} else {
	$rekl = dbassoc(dbquery("SELECT * FROM `rekl` WHERE `id` = '" . intval($_GET['go']) . "'"));
	dbquery("UPDATE `rekl` SET `count` = '" . ($rekl['count'] + 1) . "' WHERE `id` = '{$rekl['id']}'");
	if (isset($_SESSION['adm_auth'])) unset($_SESSION['adm_auth']);
	header("Refresh: 2; url={$rekl['link']}");
	echo "外部链接跳转提示<br />
		  你点击了不属于本站的链接,点击后会使你离开本站
		  本站不保证链接的安全性，请谨慎访问，防止感染病毒或上当受骗。<br />";
	echo "你访问的链接是：<b><a href=\"{$rekl['link']}\">{$rekl['link']}</a></b><br />";
	echo "访问次数: {$rekl['count']}<br />";
}

require_once 'sys/inc/tfoot.php';
