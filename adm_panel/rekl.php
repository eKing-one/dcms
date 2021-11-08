<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_rekl',null,'index.php?'.SID);
adm_check();
if (isset($_GET['sel']) && is_numeric($_GET['sel']) && $_GET['sel']>0 && $_GET['sel']<=4)
{
$sel=intval($_GET['sel']);
$set['title']='Реклама';
include_once '../sys/inc/thead.php';
title();

if (isset($_GET['add']) && isset($_POST['name']) && $_POST['name']!=NULL && isset($_POST['link']) && isset($_POST['img']) && isset($_POST['ch']) && isset($_POST['mn']))
{
$ch=intval($_POST['ch']);
$mn=intval($_POST['mn']);
$time_last=time()+$ch*$mn*60*60*24;

if (isset($_POST['dop_str']) && $_POST['dop_str']==1)
$dop_str=1;else $dop_str=0;


$link=stripcslashes(htmlspecialchars($_POST['link']));
$name=stripcslashes(htmlspecialchars($_POST['name']));
$img=stripcslashes(htmlspecialchars($_POST['img']));

dbquery("INSERT INTO `rekl` (`time_last`, `name`, `img`, `link`, `sel`, `dop_str`) VALUES ('$time_last', '$name', '$img', '$link', '$sel', '$dop_str')");

msg('Рекламная ссылка добавлена');


}
elseif (isset($_GET['set']) && dbresult(dbquery("SELECT COUNT(*) FROM `rekl` WHERE `sel` = '$sel' AND `id` = '".intval($_GET['set'])."'"),0)
&& isset($_POST['name']) && isset($_POST['link']) && isset($_POST['img']) && isset($_POST['ch']) && isset($_POST['mn']))
{
$rekl = dbassoc(dbquery("SELECT * FROM `rekl` WHERE `sel` = '$sel' AND `id` = '".intval($_GET['set'])."' LIMIT 1"));
$ch=intval($_POST['ch']);
$mn=intval($_POST['mn']);
if ($rekl['time_last']>time())
$time_last=$rekl['time_last']+$ch*$mn*60*60*24;
else
$time_last=time()+$ch*$mn*60*60*24;

$link=stripcslashes(htmlspecialchars($_POST['link']));
$name=stripcslashes(htmlspecialchars($_POST['name']));
$img=stripcslashes(htmlspecialchars($_POST['img']));

if (isset($_POST['dop_str']) && $_POST['dop_str']==1)
$dop_str=1;else $dop_str=0;
dbquery("UPDATE `rekl` SET `time_last` = '$time_last', `name` = '$name', `link` = '$link', `img` = '$img', `dop_str` = '$dop_str' WHERE `id` = '".intval($_GET['set'])."'");
msg('Рекламная ссылка изменена');


}
elseif (isset($_GET['del']) && dbresult(dbquery("SELECT COUNT(*) FROM `rekl` WHERE `sel` = '$sel' AND `id` = '".intval($_GET['del'])."'"),0))
{

dbquery("DELETE FROM `rekl` WHERE `id` = '".intval($_GET['del'])."' LIMIT 1");
msg('Рекламная ссылка удалена');


}
err();
aut();

$k_post=dbresult(dbquery("SELECT COUNT(*) FROM `rekl` WHERE `sel` = '$sel'"),0);
$k_page=k_page($k_post,$set['p_str']);
$page=page($k_page);
$start=$set['p_str']*$page-$set['p_str'];
$q=dbquery("SELECT * FROM `rekl` WHERE `sel` = '$sel' ORDER BY `time_last` DESC LIMIT $start, $set[p_str]");
echo "<table class='post'>\n";
if ($k_post==0)
{
echo "   <tr>\n";
echo "  <td class='p_t'>\n";
echo "Нет рекламы\n";
echo "  </td>\n";
echo "   </tr>\n";
}

while ($post = dbassoc($q))
{
echo "   <tr>\n";
echo "  <td class='p_t'>\n";
if ($post['img']==NULL)echo "$post[name]<br />\n"; else echo "<a href='$post[img]'>[картинка]</a><br />\n";
if ($post['time_last']>time()) echo "(до ".vremja($post['time_last']).")\n";
else echo "(срок показа истек)\n";
echo "  </td>\n";
echo "   </tr>\n";
echo "   <tr>\n";
echo "  <td class='p_m'>\n";
echo "Ссылка: $post[link]<br />\n";
if ($post['img']!=NULL)
echo "Картинка: $post[img]<br />\n";
if ($post['dop_str']==1)
echo "Переходов: $post[count]<br />\n";
echo "<a href='rekl.php?sel=$sel&amp;del=$post[id]&amp;page=$page'>移走</a><br />\n";


if (isset($_GET['set']) && $_GET['set']==$post['id'])
{
echo "<form method='post' action='rekl.php?sel=$sel&amp;set=$post[id]&amp;page=$page'>\n";
echo "Ссылка:<br />\n<input type=\"text\" name=\"link\" value=\"$post[link]\" /><br />\n";
echo "Название:<br />\n<input type=\"text\" name=\"name\" value=\"$post[name]\" /><br />\n";
echo "Картинка:<br />\n<input type=\"text\" name=\"img\" value=\"$post[img]\" /><br />\n";

if ($post['time_last']>time())echo "Продлить на:<br />\n";
else echo "Продлить до:<br />\n";

echo "<input type=\"text\" name=\"ch\" size='3' value=\"0\" />\n";
echo "<select name=\"mn\">\n";
echo "  <option value=\"1\" selected='selected'>Дней</option>\n";
echo "  <option value=\"7\">Недель</option>\n";
echo "  <option value=\"31\">Месяцев</option>\n";
echo "</select><br />\n";
if ($post['dop_str']==1)$dop=" checked='checked'";else $dop=NULL;
echo "<label><input type=\"checkbox\"$dop name=\"dop_str\" value=\"1\" /> Доп. страница</label><br />\n";
echo "<input value=\"Применить\" type=\"submit\" />\n";
echo "</form>\n";
echo "<a href='rekl.php?sel=$sel&amp;page=$page'>Отмена</a><br />\n";
}
else
echo "<a href='rekl.php?sel=$sel&amp;set=$post[id]&amp;page=$page'>Изменить</a><br />\n";
echo "  </td>\n";
echo "   </tr>\n";
}

echo "</table>\n";
if ($k_page>1)str("rekl.php?sel=$sel&amp;",$k_page,$page); // Вывод страниц



echo "<form class='foot' method='post' action='rekl.php?sel=$sel&amp;add'>\n";
echo "Название:<br />\n<input type=\"text\" name=\"name\" value=\"\" /><br />\n";
echo "Ссылка:<br />\n<input type=\"text\" name=\"link\" value=\"\" /><br />\n";

echo "Картинка:<br />\n<input type=\"text\" name=\"img\" value=\"\" /><br />\n";

echo "Срок действия:<br />\n";

echo "<input type=\"text\" name=\"ch\" size='3' value=\"1\" />\n";
echo "<select name=\"mn\">\n";
echo "  <option value=\"1\">Дней</option>\n";
echo "  <option value=\"7\" selected='selected'>Недель</option>\n";
echo "  <option value=\"31\">Месяцев</option>\n";
echo "</select><br />\n";

echo "<label><input type=\"checkbox\" checked='checked' name=\"dop_str\" value=\"1\" /> Доп. страница</label><br />\n";
echo "<input value=\"Добавить\" type=\"submit\" />\n";
echo "</form>\n";


echo "<div class='foot'>\n";
echo "<a href='rekl.php'>Список рекламы</a><br />\n";
if (user_access('adm_panel_show'))
echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
echo "</div>\n";


include_once '../sys/inc/tfoot.php';
}
$set['title']='Реклама';
include_once '../sys/inc/thead.php';
title();

err();
aut();

echo "<div class='menu'>\n";
echo "<a href='rekl.php?sel=3'>Низ сайта (главная)</a><br />\n";
echo "<a href='rekl.php?sel=4'>Низ сайта (остальные)</a><br />\n";
echo "</div>\n";



if (user_access('adm_panel_show')){
echo "<div class='foot'>\n";
echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";
echo "</div>\n";
}

include_once '../sys/inc/tfoot.php';
?>