<?







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







user_access('adm_set_forum',null,'index.php?'.SID);







adm_check();















$set['title']='Настройки форума';







include_once '../sys/inc/thead.php';







title();







if (isset($_POST['save']))







{







if ($_POST['show_num_post']==1 || $_POST['show_num_post']==0)







{







$temp_set['show_num_post']=intval($_POST['show_num_post']);







}















if ($_POST['echo_rassh_forum']==1 || $_POST['echo_rassh_forum']==0)







{







$temp_set['echo_rassh_forum']=intval($_POST['echo_rassh_forum']);







}















if ($_POST['forum_counter']==1 || $_POST['forum_counter']==0)







{







$temp_set['forum_counter']=intval($_POST['forum_counter']);







}







if (save_settings($temp_set))







{







admin_log('Настройки','Форум','Изменение настроек форума');







msg('Настройки успешно приняты');







}







else







$err='Нет прав для изменения файла настроек';







}







err();







aut();































echo "<form method=\"post\" action=\"?\">\n";















echo "Нумерация постов в форуме:<br />\n<select name=\"show_num_post\">\n";







if ($temp_set['show_num_post']==1)$sel=' selected="selected"';else $sel=NULL;







echo "<option value=\"1\"$sel>Показывать</option>\n";







if ($temp_set['show_num_post']==0)$sel=' selected="selected"';else $sel=NULL;







echo "<option value=\"0\"$sel>Скрывать</option>\n";







echo "</select><br />\n";















echo "Счетчик форума:<br />\n<select name=\"forum_counter\">\n";







if ($temp_set['forum_counter']==1)$sel=' selected="selected"';else $sel=NULL;







echo "<option value=\"1\"$sel>Количество человек</option>\n";







if ($temp_set['forum_counter']==0)$sel=' selected="selected"';else $sel=NULL;







echo "<option value=\"0\"$sel>Посты/Темы</option>\n";







echo "</select><br />\n";































echo "Показ расширений файлов:<br />\n<select name=\"echo_rassh_forum\">\n";







if ($temp_set['echo_rassh_forum']==1)$sel=' selected="selected"';else $sel=NULL;







echo "<option value=\"1\"$sel>Показывать</option>\n";







if ($temp_set['echo_rassh_forum']==0)$sel=' selected="selected"';else $sel=NULL;







echo "<option value=\"0\"$sel>Скрывать *</option>\n";







echo "</select><br />\n";







echo "* Скрывается только в случае, если есть подходящая иконка<br />\n";































echo "<input value=\"Изменить\" name='save' type=\"submit\" />\n";







echo "</form>\n";















if (user_access('adm_panel_show')){







echo "<div class='foot'>\n";







if (user_access('adm_forum_sinc'))







echo "&raquo;<a href='/adm_panel/forum_sinc.php'>Синхронизация таблиц форума</a><br />\n";







echo "&laquo;<a href='/adm_panel/'>В админку</a><br />\n";







echo "</div>\n";







}







include_once '../sys/inc/tfoot.php';







?>