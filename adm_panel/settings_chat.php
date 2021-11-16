<?到管理面板
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
$temp_set=$set;
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_set_chat',null,'index.php?'.SID);
adm_check();
$set['title']='Настройки чата';
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save']))
{
$temp_set['time_chat']=intval($_POST['time_chat']);
dbquery("ALTER TABLE `user` CHANGE `set_time_chat` `set_time_chat` INT( 11 ) DEFAULT '$temp_set[time_chat]'");
$temp_set['umnik_new']=intval($_POST['umnik_new']);
$temp_set['umnik_help']=intval($_POST['umnik_help']);
$temp_set['umnik_time']=intval($_POST['umnik_time']);
$temp_set['shutnik_new']=intval($_POST['shutnik_new']);
if(preg_match("#^([A-zА-я0-9\-\_\ ])+$#ui", $_POST['chat_shutnik']) && strlen2($_POST['chat_shutnik'])>2 && strlen2($_POST['chat_shutnik'])<=32)
$temp_set['chat_shutnik']=$_POST['chat_shutnik'];
if(preg_match("#^([A-zА-я0-9\-\_\ ])+$#ui", $_POST['chat_umnik']) && strlen2($_POST['chat_umnik'])>2 && strlen2($_POST['chat_umnik'])<=32)
$temp_set['chat_umnik']=$_POST['chat_umnik'];
if (save_settings($temp_set))
{
admin_log('Настройки','Система','Изменение параметров чата');
msg('Настройки успешно приняты');
}
else
$err='Нет прав для изменения файла настроек';
}
err();
aut();
echo "<form method=\"post\" action=\"?\">";
echo "Автообновление в чате:<br /><input type='text' name='time_chat' value='$temp_set[time_chat]' maxlength='3' /><br />";
echo "Таймаут между вопросами (умник в чате):<br /><input type='text' name='umnik_new' value='$temp_set[umnik_new]' maxlength='3' /><br />";
echo "Таймаут между подсказками (умник в чате):<br /><input type='text' name='umnik_help' value='$temp_set[umnik_help]' maxlength='3' /><br />";
echo "Общее время ожидание ответа (умник в чате):<br /><input type='text' name='umnik_time' value='$temp_set[umnik_time]' maxlength='3' /><br />";
echo "Таймаут между шутками (шутник в чате):<br /><input type='text' name='shutnik_new' value='$temp_set[shutnik_new]' maxlength='3' /><br />";
echo "Ник шутника:<br /><input type='text' name='chat_shutnik' value='$temp_set[chat_shutnik]' /><br />";
echo "Ник умника:<br /><input type='text' name='chat_umnik' value='$temp_set[chat_umnik]' /><br />";
echo "<input value=\"Изменить\" name='save' type=\"submit\" />";
echo "</form>";
echo "<div class='foot'>";
echo "&raquo;<a href='/adm_panel/chat_shut.php'>Шутки</a><br />";
echo "&raquo;<a href='/adm_panel/chat_vopr.php'>Вопросы викторины</a><br />";
if (user_access('adm_panel_show'))
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
?>