<?php
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/user.php';
$set['title']='外部链接跳转提示';
include_once 'sys/inc/thead.php';
title();

if (!isset($_GET['go']) || (dbresult(dbquery("SELECT COUNT(*) FROM `rekl` WHERE `id` = '".intval($_GET['go'])."'"),0)==0 && !preg_match('#^https?://#',@base64_decode($_GET['go'])))) {
	header("Location: index.php?".SID);
	exit;
}
if (preg_match('#^(ht|f)tps?://#',base64_decode($_GET['go']))) {
	if (isset($_SESSION['adm_auth'])) unset($_SESSION['adm_auth']);
	header("Location: ".base64_decode($_GET['go']));
	exit;
} else {
	$rekl = dbassoc(dbquery("SELECT * FROM `rekl` WHERE `id` = '".intval($_GET['go'])."'"));
	dbquery("UPDATE `rekl` SET `count` = '".($rekl['count']+1)."' WHERE `id` = '$rekl[id]'");
	if (isset($_SESSION['adm_auth'])) unset($_SESSION['adm_auth']);
	header("Refresh: 2; url=$rekl[link]");
	echo "外部链接跳转提示<br />
	      你点击了不属于本站的链接,点击后会使你离开本站
	      本站不保证链接的安全性，请谨慎访问，防止感染病毒或上当受骗。<br />";
	echo "你访问的链接是：<b><a href=\"$rekl[link]\">$rekl[link]</a></b><br />";
	echo "访问次数: $rekl[count]<br />";
}
include_once 'sys/inc/tfoot.php';