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
$set['title']='用户个人资料 '.$ank['nick'];
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['save'])){
if (isset($_POST['nick']) && $_POST['nick']!=$ank['nick'])
{
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '".my_esc($_POST['nick'])."'"),0)==1)
$err='Ник '.$_POST['nick'].' 已经很忙了';
elseif (user_access('user_change_nick'))
{
$nick=my_esc($_POST['nick']);
if( !preg_match("#^([A-zА-я0-9\-\_\ ])+$#ui", $nick))$err[]='昵称中有禁字';
if (strlen2($nick)<3)$err[]='短昵称';
if (strlen2($nick)>32)$err[]='昵称长度超过32个字符';
if (!isset($err))
{
admin_log('用户','更改昵称',"尼克 $ank[nick] 改为 $nick");
$ank['nick']=$nick;
dbquery("UPDATE `user` SET `nick` = '$nick' WHERE `id` = '$ank[id]' LIMIT 1");
}
}
else $err[]='您没有更改用户昵称的权限';
}
if (isset($_POST['set_show_icon']) && ($_POST['set_show_icon']==1 || $_POST['set_show_icon']==0))
{
$ank['set_show_icon']=$_POST['set_show_icon'];
dbquery("UPDATE `user` SET `set_show_icon` = '$ank[set_show_icon]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='头像模式错误';
if (isset($_POST['set_translit']) && ($_POST['set_translit']==1 || $_POST['set_translit']==0))
{
$ank['set_translit']=$_POST['set_translit'];
dbquery("UPDATE `user` SET `set_translit` = '$ank[set_translit]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='音译模式错误';
if (isset($_POST['set_files']) && ($_POST['set_files']==1 || $_POST['set_files']==0))
{
$ank['set_files']=$_POST['set_files'];
dbquery("UPDATE `user` SET `set_files` = '$ank[set_files]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='文件模式错误';
if (isset($_POST['set_time_chat']) && (is_numeric($_POST['set_time_chat']) && $_POST['set_time_chat']>=0 && $_POST['set_time_chat']<=900))
{
$ank['set_time_chat']=$_POST['set_time_chat'];
dbquery("UPDATE `user` SET `set_time_chat` = '$ank[set_time_chat]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='自动更新时间错误';
if (isset($_POST['set_p_str']) && (is_numeric($_POST['set_p_str']) && $_POST['set_p_str']>0 && $_POST['set_p_str']<=100))
{
$ank['set_p_str']=$_POST['set_p_str'];
dbquery("UPDATE `user` SET `set_p_str` = '$ank[set_p_str]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='每页项目数量不正确';
if (isset($_POST['ank_name']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_name']))
{
$ank['ank_name']=esc(stripcslashes(htmlspecialchars($_POST['ank_name']))) ;
dbquery("UPDATE `user` SET `ank_name` = '$ank[ank_name]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='你在名称字段中犯了一个错误';
if (isset($_POST['ank_d_r']) && (is_numeric($_POST['ank_d_r']) && $_POST['ank_d_r']>0 && $_POST['ank_d_r']<=31 || $_POST['ank_d_r']==NULL))
{
$ank['ank_d_r']=$_POST['ank_d_r'];
if ($ank['ank_d_r']==null)$ank['ank_d_r']='null';
dbquery("UPDATE `user` SET `ank_d_r` = $ank[ank_d_r] WHERE `id` = '$ank[id]' LIMIT 1");
if ($ank['ank_d_r']=='null')$ank['ank_d_r']=NULL;
}
else $err='无效的生日格式';
if (isset($_POST['ank_m_r']) && (is_numeric($_POST['ank_m_r']) && $_POST['ank_m_r']>0 && $_POST['ank_m_r']<=12 || $_POST['ank_m_r']==NULL))
{
$ank['ank_m_r']=$_POST['ank_m_r'];
if ($ank['ank_m_r']==null)$ank['ank_m_r']='null';
dbquery("UPDATE `user` SET `ank_m_r` = $ank[ank_m_r] WHERE `id` = '$ank[id]' LIMIT 1");
if ($ank['ank_m_r']=='null')$ank['ank_m_r']=NULL;
}
else $err='无效的出生月份格式';
if (isset($_POST['ank_g_r']) && (is_numeric($_POST['ank_g_r']) && $_POST['ank_g_r']>0 && $_POST['ank_g_r']<=date('Y') || $_POST['ank_g_r']==NULL))
{
$ank['ank_g_r']=$_POST['ank_g_r'];
if ($ank['ank_g_r']==null)$ank['ank_g_r']='null';
dbquery("UPDATE `user` SET `ank_g_r` = $ank[ank_g_r] WHERE `id` = '$ank[id]' LIMIT 1");
if ($ank['ank_g_r']=='null')$ank['ank_g_r']=NULL;
}
else $err='无效的出生年份格式';
if (isset($_POST['ank_city']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_city']))
{
$ank['ank_city']=esc(stripcslashes(htmlspecialchars($_POST['ank_city'])));
dbquery("UPDATE `user` SET `ank_city` = '$ank[ank_city]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='你在城市领域犯了一个错误';
if (isset($_POST['ank_icq']) && (is_numeric($_POST['ank_icq']) && strlen($_POST['ank_icq'])>=5 && strlen($_POST['ank_icq'])<=9 || $_POST['ank_icq']==NULL))
{
$ank['ank_icq']=$_POST['ank_icq'];
if ($ank['ank_icq']==null)$ank['ank_icq']='null';
dbquery("UPDATE `user` SET `ank_icq` = $ank[ank_icq] WHERE `id` = '$ank[id]' LIMIT 1");
if ($ank['ank_icq']=='null')$ank['ank_icq']=NULL;
}
else $err='无效的ICQ格式';
if (isset($_POST['ank_skype']) && preg_match('#^([A-z0-9 \-]*)$#ui', $_POST['ank_skype']))
{
$ank['ank_skype']=$_POST['ank_skype'];
if ($ank['ank_skype']==null)$ank['ank_skype']='null';
dbquery("UPDATE `user` SET `ank_skype` = '".my_esc($ank['ank_skype'])."' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err[]="无效的Skype登录";
if (isset($_POST['ank_n_tel']) && (is_numeric($_POST['ank_n_tel']) && strlen($_POST['ank_n_tel'])>=5 && strlen($_POST['ank_n_tel'])<=11 || $_POST['ank_n_tel']==NULL))
{
$ank['ank_n_tel']=$_POST['ank_n_tel'];
dbquery("UPDATE `user` SET `ank_n_tel` = '$ank[ank_n_tel]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='无效的电话号码格式';
if (isset($_POST['ank_mail']) && ($_POST['ank_mail']==null || preg_match('#^[A-z0-9-\._]+@[A-z0-9]{2,}\.[A-z]{2,4}$#ui',$_POST['ank_mail'])))
{
$ank['ank_mail']=$_POST['ank_mail'];
dbquery("UPDATE `user` SET `ank_mail` = '$ank[ank_mail]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err[]='无效电子邮件';
if (isset($_POST['ank_o_sebe']) && preg_match('#^([A-zА-я \-]*)$#ui', $_POST['ank_o_sebe']))
{
$ank['ank_o_sebe']=esc(stripcslashes(htmlspecialchars($_POST['ank_o_sebe'])));
dbquery("UPDATE `user` SET `ank_o_sebe` = '$ank[ank_o_sebe]' WHERE `id` = '$ank[id]' LIMIT 1");
}
else $err='你在这个领域犯了一个关于你自己的错误';
if (isset($_POST['new_pass']) && strlen2($_POST['new_pass'])>5)
{
admin_log('用户','更改密码',"给用户 '$ank[nick]' 已设置新密码");
dbquery("UPDATE `user` SET `pass` = '".shif($_POST['new_pass'])."' WHERE `id` = '$ank[id]' LIMIT 1");
}
if (user_access('user_change_group') && isset($_POST['group_access']))
{
if (dbresult(dbquery("SELECT COUNT(*) FROM `user_group` WHERE `id` = '".intval($_POST['group_access'])."' AND `level` < '$user[level]'"),0)==1)
{
if ($ank['group_access']!=intval($_POST['group_access']))
{
admin_log('用户','状态更改',"用户 '$ank[nick]': 状况 '$ank[group_name]' 改为 '".dbresult(dbquery("SELECT `name` FROM `user_group` WHERE `id` = '".intval($_POST['group_access'])."'"),0)."'");
$ank['group_access']=intval($_POST['group_access']);
dbquery("UPDATE `user` SET `group_access` = '$ank[group_access]' WHERE `id` = '$ank[id]' LIMIT 1");
}
}
}
if (($user['level']>=3 || $ank['id']==$user['id']) && isset($_POST['balls']) && is_numeric($_POST['balls'])){
$ank['balls']=intval($_POST['balls']);
dbquery("UPDATE `user` SET `balls` = '$ank[balls]' WHERE `id` = '$ank[id]' LIMIT 1");}
admin_log('用户','个人资料',"编辑用户配置文件 '$ank[nick]' (id#$ank[id])");
if (!isset($err))msg('更改已成功接受');
}
err();
aut();
echo "<form method='post' action='user.php?id=$ank[id]'>
尼克:<br /><input".(user_access('user_change_nick')?null:' disabled="disabled"')." type='text' name='nick' value='$ank[nick]' maxlength='32' /><br />
	真实姓名:<br /><input type='text' name='ank_name' value='$ank[ank_name]' maxlength='32' /><br />";
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
	echo "城市:<br /><input type='text' name='ank_city' value='$ank[ank_city]' maxlength='32' /><br />
	ICQ:<br /><input type='text' name='ank_icq' value='$ank[ank_icq]' maxlength='9' /><br />
	Skype 登入<br />
		<input type='text' name='ank_skype' value='$ank[ank_skype]' maxlength='16' /><br />
	E-mail:<br /><input type='text' name='ank_mail' value='$ank[ank_mail]' maxlength='32' /><br />
	电话号码:<br /><input type='text' name='ank_n_tel' value='$ank[ank_n_tel]' maxlength='11' /><br />
	关于我:<br /><input type='text' name='ank_o_sebe' value='$ank[ank_o_sebe]' maxlength='512' /><br />";
echo "聊天中自动更新:<br /><input type='text' name='set_time_chat' value='$ank[set_time_chat]' maxlength='3' /><br />";
echo "每页积分:<br /><input type='text' name='set_p_str' value='$ank[set_p_str]' maxlength='3' /><br />";
echo "图标:<br /><select name=\"set_show_icon\">";
if ($ank['set_show_icon']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>展示</option>";
if ($ank['set_show_icon']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>藏起来</option>";
echo "</select><br />";
echo "音译,音译:<br /><select name=\"set_translit\">";
if ($ank['set_translit']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>由选择</option>";
if ($ank['set_translit']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>从来没有</option>";
echo "</select><br />";
echo "上传文件:<br /><select name=\"set_files\">";
if ($ank['set_files']==1)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"1\"$sel>展场</option>";
if ($ank['set_files']==0)$sel=' selected="selected"';else $sel=NULL;
echo "<option value=\"0\"$sel>否定使用上传</option>";
echo "</select><br />";
if ($user['level']<3)$dis=' disabled="disabled"';else $dis=NULL;
echo "分数:<br /><input type='text'$dis name='balls' value='$ank[balls]' /><br />";
echo "团体:<br /><select name='group_access'".(user_access('user_change_group')?null:' disabled="disabled"')."><br />";
$q=dbquery("SELECT * FROM `user_group` ORDER BY `level`,`id` ASC");
while ($post = dbassoc($q))
{
echo "<option value='$post[id]'".($post['level']>=$user['level']?" disabled='disabled'":null)."".($post['id']==$ank['group_access']?" selected='selected'":null).">".$post['name']."</option>";
}
echo "</select><br />";
echo "新密码:<br /><input type='text' name='new_pass' value='' /><br />";
echo "<input type='submit' name='save' value='保存' />";
echo "</form>";
echo "<div class='foot'>";
echo "&raquo;<a href=\"/mail.php?id=$ank[id]\">写一封信</a><br />";
echo "&laquo;<a href=\"/info.php?id=$ank[id]\">到问卷</a><br />";
if (user_access('adm_panel_show'))
echo "&laquo;<a href='/adm_panel/'>到管理面板</a><br />";
echo "</div>";
include_once '../sys/inc/tfoot.php';
?>