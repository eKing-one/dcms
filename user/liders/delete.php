<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `id_user` = '".intval($_GET['id'])."'"),0)==1)
{
	if (isset($user) && $user['level'] > 2)
	{
		dbquery("DELETE FROM `liders` WHERE `id_user` = '" . intval($_GET['id']) . "'");
		$_SESSION['message'] = '删除成功';
	}
}
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL)
header("Location: " . $_SERVER['HTTP_REFERER']);
else
header("Location: index.php?".SID);
exit;
?>