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

only_reg();
$set['title']='Мои настройки';
include_once '../../sys/inc/thead.php';
title();

if (isset($_POST['save'])){


if (isset($_POST['add_konts']) && ($_POST['add_konts']==2 || $_POST['add_konts']==1 || $_POST['add_konts']==0))
{
$user['add_konts']=intval($_POST['add_konts']);
dbquery("UPDATE `user` SET `add_konts` = '$user[add_konts]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='Ошибка режима добавления контактов';
if (isset($_POST['set_files']) && ($_POST['set_files']==1 || $_POST['set_files']==0))
{
$user['set_files']=intval($_POST['set_files']);
dbquery("UPDATE `user` SET `set_files` = '$user[set_files]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='Ошибка режима файлов';/*Метка 18+ */
if (isset($_POST['metka']) && ($_POST['metka']==1 || $_POST['metka']==0))
{
$user['abuld']=intval($_POST['metka']);
dbquery("UPDATE `user` SET `abuld` = '$user[abuld]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='Ошибка метки 18+';

if (isset($_POST['show_url']) && ($_POST['show_url']==1 || $_POST['show_url']==0))
{
$user['show_url']=intval($_POST['show_url']);
dbquery("UPDATE `user` SET `show_url` = '$user[show_url]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='Ошибка режима местоположения';
if (isset($_POST['set_time_chat']) && (is_numeric($_POST['set_time_chat']) && $_POST['set_time_chat']>=0 && $_POST['set_time_chat']<=900))
{
$user['set_time_chat']=intval($_POST['set_time_chat']);
$set['time_chat']=$user['set_time_chat'];
dbquery("UPDATE `user` SET `set_time_chat` = '$user[set_time_chat]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='Ошибка во времени автообновления';

if (isset($_POST['set_news_to_mail']) && $_POST['set_news_to_mail']==1)
{
$user['set_news_to_mail']=1;
dbquery("UPDATE `user` SET `set_news_to_mail` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['set_news_to_mail']=0;
dbquery("UPDATE `user` SET `set_news_to_mail` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}if (isset($_POST['set_them']) && preg_match('#^([A-z0-9\-_\(\)]+)$#ui', $_POST['set_them']) && is_dir(H.'style/themes/'.$_POST['set_them']))
{
$user['set_them']=$_POST['set_them'];
dbquery("UPDATE `user` SET `set_them` = '$user[set_them]' WHERE `id` = '$user[id]' LIMIT 1");
}
elseif (isset($_POST['set_them2']) && preg_match('#^([A-z0-9\-_\(\)]+)$#ui', $_POST['set_them2']) && is_dir(H.'style/themes/'.$_POST['set_them2']))
{
$user['set_them2']=$_POST['set_them2'];
dbquery("UPDATE `user` SET `set_them2` = '$user[set_them2]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='Ошибка применения темы';if (isset($_POST['set_p_str']) && is_numeric($_POST['set_p_str']) && $_POST['set_p_str']>0 && $_POST['set_p_str']<=100)
{
$user['set_p_str']=intval($_POST['set_p_str']);
$set['p_str']=$user['set_p_str'];
dbquery("UPDATE `user` SET `set_p_str` = '$user[set_p_str]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='Неправильное количество пунктов на страницу';

if (isset($_POST['set_timesdvig']) && (is_numeric($_POST['set_timesdvig']) && $_POST['set_timesdvig']>=-12 && $_POST['set_timesdvig']<=12))
{
$user['set_timesdvig']=intval($_POST['set_timesdvig']);
dbquery("UPDATE `user` SET `set_timesdvig` = '$user[set_timesdvig]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err='Неправильное количество пунктов на страницу';

if (!isset($err)){$_SESSION['message'] = 'Изменения успешно приняты';header("Location: ?"); exit;}
}
err();
aut();

echo "<div id='comments' class='menus'>";

echo "<div class='webmenu'>";
echo "<a href='/user/info/settings.php' class='activ'>Общие</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/tape/settings.php'>Лента</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/discussions/settings.php'>Обсуждения</a>";
echo "</div>"; 

echo "<div class='webmenu last'>";
echo "<a href='/user/notification/settings.php'>Уведомления</a>";
echo "</div>"; echo "<div class='webmenu last'>";
echo "<a href='/user/info/settings.privacy.php' >Приватность</a>";
echo "</div>"; 
echo "<div class='webmenu last'>";echo "<a href='/user/info/secure.php' >Пароль</a>";echo "</div>"; 
echo "</div>";

echo "<form method='post' action='?$passgen'>";

echo "Автообновление в чате:<br /><input type='text' name='set_time_chat' value='$set[time_chat]' maxlength='3' /><br />";
echo "Пунктов на страницу:<br /><input type='text' name='set_p_str' value='$set[p_str]' maxlength='3' /><br />";

echo "Тема (".($webbrowser?'WEB':'WAP')."):<br /><select name='set_them".($webbrowser?'2':null)."'>";
$opendirthem=opendir(H.'style/themes');
while ($themes=readdir($opendirthem)){
// пропускаем корневые папки и файлы
if ($themes=='.' || $themes=='..' || !is_dir(H."style/themes/$themes"))continue;
// пропускаем темы для определенных браузеров
if (file_exists(H."style/themes/$themes/.only_for_".($webbrowser?'wap':'web')))continue;

echo "<option value='$themes'".($user['set_them'.($webbrowser?'2':null)]==$themes?" selected='selected'":null).">".trim(file_get_contents(H.'style/themes/'.$themes.'/them.name'))."</option>";
}
closedir($opendirthem);
echo "</select><br />";echo "Выгрузка файлов:<br /><select name='set_files'>";
echo "<option value='1'".($user['set_files']==1?" selected='selected'":null).">Показывать поле</option>";
echo "<option value='0'".($user['set_files']==0?" selected='selected'":null).">Не использовать выгрузку</option>";
echo "</select><br />";

echo "Местоположение:<br /><select name='show_url'>";
echo "<option value='1'".($user['show_url']==1?" selected='selected'":null).">Показывать</option>";
echo "<option value='0'".($user['show_url']==0?" selected='selected'":null).">Скрывать</option>";
echo "</select><br />";

echo "Добавление контактов:<br /><select name='add_konts'>";
echo "<option value='2'".($user['add_konts']==2?" selected='selected'":null).">При чтении сообщений</option>";
echo "<option value='1'".($user['add_konts']==1?" selected='selected'":null).">При написании сообщения</option>";
echo "<option value='0'".($user['add_konts']==0?" selected='selected'":null).">Только вручную</option>";
echo "</select><br />";

echo "Время<br /><select name=\"set_timesdvig\"><br />";
for ($i=-12;$i<12;$i++){
echo "<option value='$i'".($user['set_timesdvig']==$i?" selected='selected'":null).">".date("G:i", $time+$i*60*60)."</option>";}
echo "</select><br />";

if ($user['ank_mail'])
echo "<label><input type='checkbox' name='set_news_to_mail'".($user['set_news_to_mail']?" checked='checked'":null)." value='1' /> Получать новости на E-mail</label><br />";
echo "Показ эротического материала без предупреждений:<br />";echo "<input name='metka'".($user['abuld']==0?" checked='checked'":null)."  type='radio' value='0' />Вкл ";echo "<input name='metka'".($user['abuld']==1?" checked='checked'":null)."  type='radio' value='1' />Выкл<br />";

echo "<input type='submit' name='save' value='Сохранить' />";
echo "</form>";

echo "<div class=\"foot\">";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php?id=$user[id]'>$user[nick]</a> | ";
echo '<b>Общие</b>';
echo "</div>";
include_once '../../sys/inc/tfoot.php';
?>