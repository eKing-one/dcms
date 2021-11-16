<?
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/shif.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('user_prof_edit',null,'index.php?'.SID);
adm_check();
if (isset($_GET['id']))$ank['id']=intval($_GET['id']);
else {header("Location: /index.php?".SID);exit;}
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `id` = '$ank[id]' LIMIT 1"),0)==0){header("Location: /index.php?".SID);exit;}
$ank=get_user($ank['id']);
if ($user['level']<=$ank['level']){header("Location: /index.php?".SID);exit;}
$set['title']='Профиль пользователя '.$ank['nick'];
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save'])){
if (isset($_POST['nick']) && $_POST['nick']!=$ank['nick'])
{
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '".my_esc($_POST['nick'])."'"),0)==1)
$err='Ник '.$_POST['nick'].' уже занят';
elseif (user_access('user_change_nick'))
{
$nick=my_esc($_POST['nick']);
if( !preg_match("#^([A-zА-я0-9\-\_\ ])+$#ui", $nick))$err[]='В нике присутствуют запрещенные символы';
if (strlen2($nick)<3)$err[]='Короткий ник';
if (strlen2($nick)>32)$err[]='Длина ника превышает 32 символа';
if (!isset($err))
{
admin_log('Пользователи','Изменение ника',"Ник $ank[nick] изменен на $nick");
$ank['nick']=$nick;
dbquery("UPDATE `user` SET `nick` = '$nick' WHERE `id` = '$ank[id]' LIMIT 1");
}
}
else $err[]='У Вас нет привилегий на изменение ника пользователя';
}
if (isset($_POST['set_show_icon']) && ($_POST['set_show_icon']==1 || $_POST['set_show_icon']==0))
{
$ank['set_show_icon']=$_POST['set_show_icon'];
dbquery("UPDATE `user` SET `set_show_icon` = '$ank[set_show_icon]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='Ошибка режима аватара';
if (isset($_POST['set_translit']) && ($_POST['set_translit']==1 || $_POST['set_translit']==0))
{
$ank['set_translit']=$_POST['set_translit'];
dbquery("UPDATE `user` SET `set_translit` = '$ank[set_translit]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='Ошибка режима транслита';
if (isset($_POST['set_files']) && ($_POST['set_files']==1 || $_POST['set_files']==0))
{
$ank['set_files']=$_POST['set_files'];
dbquery("UPDATE `user` SET `set_files` = '$ank[set_files]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='Ошибка режима файлов';
if (isset($_POST['set_time_chat']) && (is_numeric($_POST['set_time_chat']) && $_POST['set_time_chat']>=0 && $_POST['set_time_chat']<=900))
{
$ank['set_time_chat']=$_POST['set_time_chat'];
dbquery("UPDATE `user` SET `set_time_chat` = '$ank[set_time_chat]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='Ошибка во времени автообновления';
if (isset($_POST['set_p_str']) && (is_numeric($_POST['set_p_str']) && $_POST['set_p_str']>0 && $_POST['set_p_str']<=100))
{
$ank['set_p_str']=$_POST['set_p_str'];
dbquery("UPDATE `user` SET `set_p_str` = '$ank[set_p_str]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='Неправильное количество пунктов на страницу';
if (isset($_POST['ank_name']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_name']))
{
$ank['ank_name']=esc(stripcslashes(htmlspecialchars($_POST['ank_name']))) ;
dbquery("UPDATE `user` SET `ank_name` = '$ank[ank_name]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='Вы ошиблись в поле имя';
if (isset($_POST['ank_d_r']) && (is_numeric($_POST['ank_d_r']) && $_POST['ank_d_r']>0 && $_POST['ank_d_r']<=31 || $_POST['ank_d_r']==NULL))
{
$ank['ank_d_r']=$_POST['ank_d_r'];
if ($ank['ank_d_r']==null)$ank['ank_d_r']='null';
dbquery("UPDATE `user` SET `ank_d_r` = $ank[ank_d_r] WHERE `id` = '$ank[id]' LIMIT 1");
if ($ank['ank_d_r']=='null')$ank['ank_d_r']=NULL;
}
else $err='Неверный формат дня рождения';
if (isset($_POST['ank_m_r']) && (is_numeric($_POST['ank_m_r']) && $_POST['ank_m_r']>0 && $_POST['ank_m_r']<=12 || $_POST['ank_m_r']==NULL))
{
$ank['ank_m_r']=$_POST['ank_m_r'];
if ($ank['ank_m_r']==null)$ank['ank_m_r']='null';
dbquery("UPDATE `user` SET `ank_m_r` = $ank[ank_m_r] WHERE `id` = '$ank[id]' LIMIT 1");
if ($ank['ank_m_r']=='null')$ank['ank_m_r']=NULL;
}
else $err='Неверный формат месяца рождения';
if (isset($_POST['ank_g_r']) && (is_numeric($_POST['ank_g_r']) && $_POST['ank_g_r']>0 && $_POST['ank_g_r']<=date('Y') || $_POST['ank_g_r']==NULL))
{
$ank['ank_g_r']=$_POST['ank_g_r'];
if ($ank['ank_g_r']==null)$ank['ank_g_r']='null';
dbquery("UPDATE `user` SET `ank_g_r` = $ank[ank_g_r] WHERE `id` = '$ank[id]' LIMIT 1");
if ($ank['ank_g_r']=='null')$ank['ank_g_r']=NULL;
}
else $err='Неверный формат года рождения';
if (isset($_POST['ank_city']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_city']))
{
$ank['ank_city']=esc(stripcslashes(htmlspecialchars($_POST['ank_city'])));
dbquery("UPDATE `user` SET `ank_city` = '$ank[ank_city]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='Вы ошиблись в поле город';
if (isset($_POST['ank_icq']) && (is_numeric($_POST['ank_icq']) && strlen($_POST['ank_icq'])>=5 && strlen($_POST['ank_icq'])<=9 || $_POST['ank_icq']==NULL))
{
$ank['ank_icq']=$_POST['ank_icq'];
if ($ank['ank_icq']==null)$ank['ank_icq']='null';
dbquery("UPDATE `user` SET `ank_icq` = $ank[ank_icq] WHERE `id` = '$ank[id]' LIMIT 1");
if ($ank['ank_icq']=='null')$ank['ank_icq']=NULL;
}
else $err='Неверный формат ICQ';
if (isset($_POST['ank_skype']) && preg_match('#^([A-z0-9 \-]*)$#ui', $_POST['ank_skype']))
{
$ank['ank_skype']=$_POST['ank_skype'];
if ($ank['ank_skype']==null)$ank['ank_skype']='null';
dbquery("UPDATE `user` SET `ank_skype` = '".my_esc($ank['ank_skype'])."' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err[]='Неверный логин Skype';
if (isset($_POST['ank_n_tel']) && (is_numeric($_POST['ank_n_tel']) && strlen($_POST['ank_n_tel'])>=5 && strlen($_POST['ank_n_tel'])<=11 || $_POST['ank_n_tel']==NULL))
{
$ank['ank_n_tel']=$_POST['ank_n_tel'];
dbquery("UPDATE `user` SET `ank_n_tel` = '$ank[ank_n_tel]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='Неверный формат номера телефона';
if (isset($_POST['ank_mail']) && ($_POST['ank_mail']==null || preg_match('#^[A-z0-9-\._]+@[A-z0-9]{2,}\.[A-z]{2,4}$#ui',$_POST['ank_mail'])))
{
$ank['ank_mail']=$_POST['ank_mail'];
dbquery("UPDATE `user` SET `ank_mail` = '$ank[ank_mail]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err[]='Неверный E-mail';
if (isset($_POST['ank_o_sebe']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_o_sebe']))
{
$ank['ank_o_sebe']=esc(stripcslashes(htmlspecialchars($_POST['ank_o_sebe'])));
dbquery("UPDATE `user` SET `ank_o_sebe` = '$ank[ank_o_sebe]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='Вы ошиблись в поле о себе';
if (isset($_POST['new_pass']) && strlen2($_POST['new_pass'])>5)
{
admin_log('Пользователи','Смена пароля',"Пользователю '$ank[nick]' установлен новый пароль");
dbquery("UPDATE `user` SET `pass` = '".shif($_POST['new_pass'])."' WHERE `id` = '$ank[id]' LIMIT 1");
}
if (user_access('user_change_group') && isset($_POST['group_access']))
{
if (dbresult(dbquery("SELECT COUNT(*) FROM `user_group` WHERE `id` = '".intval($_POST['group_access'])."' AND `level` < '$user[level]'"),0)==1)
{
if ($ank['group_access']!=intval($_POST['group_access']))
{
admin_log('Пользователи','Изменение статуса',"Пользователь '$ank[nick]': Статус '$ank[group_name]' изменен на '".dbresult(dbquery("SELECT `name` FROM `user_group` WHERE `id` = '".intval($_POST['group_access'])."'"),0)."'");
$ank['group_access']=intval($_POST['group_access']);
dbquery("UPDATE `user` SET `group_access` = '$ank[group_access]' WHERE `id` = '$ank[id]' LIMIT 1");
}
}
}
if (($user['level']>=3 || $ank['id']==$user['id']) && isset($_POST['balls']) && is_numeric($_POST['balls'])){
$ank['balls']=intval($_POST['balls']);
dbquery("UPDATE `user` SET `balls` = '$ank[balls]' WHERE `id` = '$ank[id]' LIMIT 1");}
admin_log('Пользователи','Профиль',"Редактирование профиля пользователя '$ank[nick]' (id#$ank[id])");
if (!isset($err))msg('Изменения успешно приняты');
}
err();
aut();
echo "<form method='post' action='user.php?id=$ank[id]'>
	Ник:<br /><input".(user_access('user_change_nick')?null:' disabled="disabled"')." type='text' name='nick' value='$ank[nick]' maxlength='32' /><br />
	Имя в реале:<br /><input type='text' name='ank_name' value='$ank[ank_name]' maxlength='32' /><br />";
	echo 'Дата рождения:<br />
	<select name="ank_d_r">
	<option selected="'.$ank['ank_d_r'].'" value="'.$ank['ank_d_r'].'" >'.$ank['ank_d_r'].'<option>
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	<option value="13">13</option>
	<option value="14">14</option>
	<option value="15">15</option>
	<option value="16">16</option>
	<option value="17">17</option>
	<option value="18">18</option>
	<option value="19">19</option>
	<option value="20">20</option>
	<option value="21">21</option>
	<option value="22">22</option>
	<option value="23">23</option>
	<option value="24">24</option>
	<option value="25">25</option>
	<option value="26">26</option>
	<option value="27">27</option>
	<option value="28">28</option>
	<option value="29">29</option>
	<option value="30">30</option>
	<option value="31">31</option>
	</select>';
	echo '<select name="ank_m_r">
	<option selected="'.$ank['ank_m_r'].'" value="'.$ank['ank_m_r'].'" >'.$ank['ank_m_r'].'<option>	
	<option value="1">1</option>
	<option value="2">2</option>
	<option value="3">3</option>
	<option value="4">4</option>
	<option value="5">5</option>
	<option value="6">6</option>
	<option value="7">7</option>
	<option value="8">8</option>
	<option value="9">9</option>
	<option value="10">10</option>
	<option value="11">11</option>
	<option value="12">12</option>
	</select>';
	echo '<select name="ank_g_r">
	<option selected="'.$ank['ank_g_r'].'" value="'.$ank['ank_g_r'].'" >'.$ank['ank_g_r'].'<option><option value="1996">1996</option><option value="1995">1995</option><option value="1994">1994</option><option value="1993">1993</option><option value="1992">1992</option><option value="1991">1991</option><option value="1990">1990</option><option value="1989">1989</option><option value="1988">1988</option><option value="1987">1987</option><option value="1986">1986</option><option value="1985">1985</option><option value="1984">1984</option><option value="1983">1983</option><option value="1982">1982</option><option value="1981">1981</option><option value="1980">1980</option><option value="1979">1979</option><option value="1978">1978</option><option value="1977">1977</option><option value="1976">1976</option><option value="1975">1975</option><option value="1974">1974</option><option value="1973">1973</option><option value="1972">1972</option><option value="1971">1971</option><option value="1970">1970</option><option value="1969">1969</option><option value="1968">1968</option><option value="1967">1967</option><option value="1966">1966</option><option value="1965">1965</option><option value="1964">1964</option><option value="1963">1963</option><option value="1962">1962</option><option value="1961">1961</option><option value="1960">1960</option><option value="1959">1959</option><option value="1958">1958</option><option value="1957">1957</option><option value="1956">1956</option><option value="1955">1955</option><option value="1954">1954</option><option value="1953">1953</option><option value="1952">1952</option><option value="1951">1951</option><option value="1950">1950</option><option value="1949">1949</option><option value="1948">1948</option><option value="1947">1947</option><option value="1946">1946</option><option value="1945">1945</option><option value="1944">1944</option><option value="1943">1943</option><option value="1942">1942</option><option value="1941">1941</option><option value="1940">1940</option><option value="1939">1939</option><option value="1938">1938</option><option value="1937">1937</option><option value="1936">1936</option><option value="1935">1935</option><option value="1934">1934</option><option value="1933">1933</option><option value="1932">1932</option><option value="1931">1931</option><option value="1930">1930</option><option value="1929">1929</option><option value="1928">1928</option><option value="1927">1927</option><option value="1926">1926</option><option value="1925">1925</option><option value="1924">1924</option><option value="1923">1923</option><option value="1922">1922</option><option value="1921">1921</option><option value="1920">1920</option>
	</select><br/>';
	echo "Город:<br /><input type='text' name='ank_city' value='$ank[ank_city]' maxlength='32' /><br />
	ICQ:<br /><input type='text' name='ank_icq' value='$ank[ank_icq]' maxlength='9' /><br />
	Skype логин<br />
		<input type='text' name='ank_skype' value='$ank[ank_skype]' maxlength='16' /><br />
	E-mail:<br /><input type='text' name='ank_mail' value='$ank[ank_mail]' maxlength='32' /><br />
	Номер телефона:<br /><input type='text' name='ank_n_tel' value='$ank[ank_n_tel]' maxlength='11' /><br />
	О себе:<br /><input type='text' name='ank_o_sebe' value='$ank[ank_o_sebe]' maxlength='512' /><br />";
echo "Автообновление в чате:<br /><input type='text' name='set_time_chat' value='$ank[set_time_chat]' maxlength='3' /><br />";
echo "Пунктов на страницу:<br /><input type='text' name='set_p_str' value='$ank[set_p_str]' maxlength='3' /><br />";
echo "Иконки:<br /><select name=\"set_show_icon\">";
if ($ank['set_show_icon']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>Показывать</option>";
if ($ank['set_show_icon']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>Скрывать</option>";
echo "</select><br />";
echo "Транслит:<br /><select name=\"set_translit\">";
if ($ank['set_translit']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>По выбору</option>";
if ($ank['set_translit']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>Никогда</option>";
echo "</select><br />";
echo "Выгрузка файлов:<br /><select name=\"set_files\">";
if ($ank['set_files']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>Показывать поле</option>";
if ($ank['set_files']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>Не использовать выгрузку</option>";
echo "</select><br />";
if ($user['level']<3)$dis=' disabled="disabled"';else $dis=NULL;
echo "Баллы:<br /><input type='text'$dis name='balls' value='$ank[balls]' /><br />";
echo "Группа:<br /><select name='group_access'".(user_access('user_change_group')?null:' disabled="disabled"')."><br />";
$q=dbquery("SELECT * FROM `user_group` ORDER BY `level`,`id` ASC");
while ($post = dbassoc($q))
{
echo "<option value='$post[id]'".($post['level']>=$user['level']?" disabled='disabled'":null)."".($post['id']==$ank['group_access']?" selected='selected'":null).">".$post['name']."</option>";
}
echo "</select><br />";
echo "Новый пароль:<br /><input type='text' name='new_pass' value='' /><br />";
echo "<input type='submit' name='save' value='Сохранить' />";
echo "</form>";
echo "<div class='foot'>";
echo "&raquo;<a href=\"/mail.php?id=$ank[id]\">Написать сообщение</a><br />";
echo "&laquo;<a href=\"/info.php?id=$ank[id]\">В анкету</a><br />";
if (user_access('adm_panel_show'))
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
?>