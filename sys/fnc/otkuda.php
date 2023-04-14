<?php
if (isset($_SESSION['refer']) && $_SESSION['refer'] != NULL && !preg_match('#(rules)|(smiles)|(secure)|(aut)|(reg)|(umenu)|(zakl)|(mail)|(anketa)|(settings)|(avatar)|(info)\.php#',$_SERVER['SCRIPT_NAME']))
$_SESSION['refer'] = NULL;
function otkuda($ref)
{
	if (preg_match('#^/forum/#', $ref))
		$mesto = ' 正在 <a href="/forum/">论坛</a> ';
	elseif (preg_match('#^/chat/#', $ref))
		$mesto = ' 正在 <a href="/chat/">聊天室</a> ';
	elseif (preg_match('#^/news/#', $ref))
		$mesto = ' 正在阅读 <a href="/news/">新闻中心</a> ';
	elseif (preg_match('#^/guest/#', $ref))
		$mesto = ' 正在查看 <a href="/guest/">留言板</a> ';
	elseif (preg_match('#^/user/users\.php#', $ref))
		$mesto = ' 正在查看 <a href="/user/users.php">用户</a> ';
	elseif (preg_match('#^/online\.php#', $ref))
		$mesto = ' 正在查看 <a href="/user/online.php">在线用户</a> ';
	elseif (preg_match('#^/online_g\.php#', $ref))
		$mesto = ' 看看谁进来了 <a href="/user/online_g.php">在线游客</a> ';
	elseif (preg_match('#^/reg\.php#', $ref))
		$mesto = ' 正在 <a href="/user/reg.php">注册</a> ';
	elseif (preg_match('#^/down/#', $ref))
		$mesto = ' 坐在 <a href="/down/">下载中心</a> ';
	elseif (preg_match('#^/aut\.php#', $ref))
		$mesto = ' 正在 <a href="/user/aut.php">登录网站</a> ';
	elseif (preg_match('#^/index\.php#', $ref))
		$mesto = ' 访问 <a href="/index.php">网站主页</a> ';
	elseif (preg_match('#^/\??$#', $ref))
		$mesto = ' 访问 <a href="/index.php">网站主页</a> ';
	else
		$mesto = ' 从某个地方来到 <a href="/index.php">网站</a> ';
	return $mesto;
}
?>