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
if (isset ($user) && $user['level'] < 3)
header("Location: /");
if (isset($_GET['del']) && dbresult(dbquery("SELECT COUNT(*) FROM `rules_p` WHERE `id` = '".intval($_GET['del'])."'"),0)==1)
{
	$post=dbassoc(dbquery("SELECT * FROM `rules_p` WHERE `id` = '".intval($_GET['del'])."' LIMIT 1"));
	$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
	if (isset($user) && ($user['level']>$ank['level']))
	dbquery("DELETE FROM `rules_p` WHERE `id` = '$post[id]'");
}
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=NULL)
header("Location: ".$_SERVER['HTTP_REFERER']);
else
header("Location: post.php?".session_id());
?>