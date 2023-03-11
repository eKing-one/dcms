<?
include_once '../../../sys/inc/start.php';
include_once '../../../sys/inc/compress.php';
include_once '../../../sys/inc/sess.php';
include_once '../../../sys/inc/home.php';
include_once '../../../sys/inc/settings.php';
include_once '../../../sys/inc/db_connect.php';
include_once '../../../sys/inc/ipua.php';
include_once '../../../sys/inc/fnc.php';
include_once '../../../sys/inc/user.php';
if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `adm_chat` WHERE `id` = '".intval($_GET['id'])."'"),0)==1)
{
$post=dbassoc(dbquery("SELECT * FROM `adm_chat` WHERE `id` = '".intval($_GET['id'])."' LIMIT 1"));
if ($post['id_user']==0)
{
$ank['id']=0;
$ank['pol']='guest';
$ank['level']=0;
$ank['nick']='客人';
}
else
$ank=user::get_user($post['id_user']);
if (user_access('guest_delete'))
{
admin_log('客座','删除消息',"将消息从 $ank[nick]");
dbquery("DELETE FROM `adm_chat` WHERE `id` = '$post[id]'");
}
$_SESSION['message'] = '邮件已成功删除';
header("Location: index.php?");
exit;
}
