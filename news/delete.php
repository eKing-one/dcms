<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';

/* Удаление комментариев */

if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `news_komm` WHERE `id` = '".intval($_GET['id'])."'"),0) == 1)
{
	$post = dbassoc(dbquery("SELECT * FROM `news_komm` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
	$ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
	
	if (isset($user) && ($user['level'] > $ank['level']))
	dbquery("DELETE FROM `news_komm` WHERE `id` = '$post[id]'");
	
	$_SESSION['message'] = '评论成功删除';
	
	if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != NULL)
			header("Location: " . htmlspecialchars($_SERVER['HTTP_REFERER']));
		else
			header("Location: index.php?".SID);
		exit;
	}
	
/* Удаление новости */

if (isset($_GET['news_id']) && dbresult(dbquery("SELECT COUNT(*) FROM `news` WHERE `id` = '" . intval($_GET['news_id']) . "'"),0) == 1)
{
	$post = dbassoc(dbquery("SELECT * FROM `news` WHERE `id` = '" . intval($_GET['news_id']) . "' LIMIT 1"));
	if (user_access('adm_news'))
	{
		dbquery("DELETE FROM `news` WHERE `id` = '$post[id]'");
		dbquery("DELETE FROM `news_komm` WHERE `id_news` = '$post[id]'");
		$_SESSION['message'] = '新闻被成功删除';
	}
	
	header("Location: index.php?".SID);
	exit;
}
?>