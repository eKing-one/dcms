<?
if (isset($_GET['act']) && isset($_GET['ok']) && $_GET['act']=='set' && isset($_POST['name']))
{

$name=$_POST['name'];
$opis=$_POST['opis'];

if (strlen2($name)<3)$err='名字太短了';
if (strlen2($name)>32)$err='名字太低了';
$name=my_esc($name);
$opis=my_esc($opis);

if (!isset($err)){
$razd=dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));
admin_log('论坛','部分',"重命名部分 '$razd[name]' в '$name'");

dbquery("UPDATE `forum_r` SET `name` = '$name', `opis` = '$opis' WHERE `id` = '$razdel[id]' LIMIT 1");
$razdel=dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '$razdel[id]' LIMIT 1"));
msg('更改已成功接受');
}
}

$razd=dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '".intval($_GET['id_razdel'])."' AND `id_forum` = '".intval($_GET['id_forum'])."' LIMIT 1"));

if (isset($_GET['act']) && isset($_GET['ok']) && $_GET['act']=='mesto' && isset($_POST['forum']) && is_numeric($_POST['forum'])
&& dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE `id` = '".intval($_POST['forum'])."'"),0)==1)
{
$forum_new['id']=intval($_POST['forum']);
$forum_old=$forum;
dbquery("UPDATE `forum_p` SET `id_forum` = '$forum_new[id]' WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'");
dbquery("UPDATE `forum_t` SET `id_forum` = '$forum_new[id]' WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'");
dbquery("UPDATE `forum_r` SET `id_forum` = '$forum_new[id]' WHERE `id_forum` = '$forum[id]' AND `id` = '$razdel[id]'");


$forum=dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '$forum_new[id]' LIMIT 1"));


admin_log('论坛','部分',"转移分区'$razd[name]'从子论坛'$forum_old[name]' в '$forum[name]'");

msg('该部分已成功迁移');

}

if (isset($_GET['act']) && isset($_GET['ok']) && $_GET['act']=='delete')
{

dbquery("DELETE FROM `forum_r` WHERE `id` = '$razdel[id]'");
dbquery("DELETE FROM `forum_t` WHERE `id_razdel` = '$razdel[id]'");
dbquery("DELETE FROM `forum_p` WHERE `id_razdel` = '$razdel[id]'");
admin_log('论坛','部分',"删除分区'$razd[name]'从子论坛'$forum[name]'");
msg('该部分已成功删除');
err();
aut();
echo "<a href=\"/forum/$forum[id]/\">到子论坛</a><br />\n";
echo "<a href=\"/forum/\">到论坛</a><br />\n";
include_once '../sys/inc/tfoot.php';
}
?>