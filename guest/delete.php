<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `guest` WHERE `id` = '".intval($_GET['id'])."'"),0) == 1)
{
	$post = dbassoc(dbquery("SELECT * FROM `guest` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
	if ($post['id_user'] == 0)
	{
		$ank['id'] = 0;
		$ank['pol'] = 'guest';
		$ank['level'] = 0;
		$ank['nick'] = '客人';
	}
	else
	$ank = user::get_user($post['id_user']);
	if (user_access('guest_delete'))
	{
		admin_log('留言板', '删除邮件', '从中删除消息 ' . $ank['nick']);
		dbquery("DELETE FROM `guest` WHERE `id` = '$post[id]'");
	}
}
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL)
header('Location: ' . my_esc($_SERVER['HTTP_REFERER']));
else
header('Location: index.php?' . session_id());
