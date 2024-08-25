<?php
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

//屏蔽 Notice 报错
error_reporting(E_ALL || ~E_NOTICE);

$set['title']='编辑个人资料';
include_once '../../sys/inc/thead.php';
title();
aut();
if (isset($_GET['set']))
{
	$get = htmlspecialchars($_GET['set']);
	if (isset($_GET['act']) && $_GET['act']=='ank')
	$get2 = "act=ank&amp;";
	elseif (isset($_GET['act']) && $_GET['act']=='ank_web')
	$get2 = "act=ank_web&amp;";
	else
	$get2 = null;
if (isset($_POST['save']) && isset($_GET['set'])){
//----------昵称------------//
if (isset($_GET['set']) && $_GET['set']=='nick' && $user['set_nick'] == 1){
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '".my_esc($_POST['nick'])."'"),0)==0)
{
$nick=my_esc($_POST['nick']);
if(!preg_match("/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u",$nick))$err = '不要在名字里面整些特殊符号，请只使用字母、数字和汉字';
if (strlen2($nick)<2)$err[]='昵称字数少于 2 字';
if (strlen2($nick)>32)$err[]='昵称字数多于 32 字';
}
else $err[]='用户名 "'.stripcslashes(htmlspecialchars($_POST['nick'])).'" 已存在';
if (isset($_POST['nick']) && !isset($err))
{
$user['nick'] = $_POST['nick'];
dbquery("UPDATE `user` SET `nick` = '".my_esc($user['nick'])."' , `set_nick` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
}
//----------姓名------------//
if (isset($_GET['set']) && $_GET['set']=='name'){
if (isset($_POST['ank_name']) && preg_match('/[\x{4e00}-\x{9fa5}]+/u', $_POST['ank_name']))
{
$user['ank_name']=$_POST['ank_name'];
dbquery("UPDATE `user` SET `ank_name` = '".my_esc($user['ank_name'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='无效的命名';
}

//----------出生日期------------//
if (isset($_GET['set']) && $_GET['set']=='date'){
if (isset($_POST['ank_d_r']) && (is_numeric($_POST['ank_d_r']) && $_POST['ank_d_r']>0 && $_POST['ank_d_r']<=31 || $_POST['ank_d_r']==NULL))
{
$user['ank_d_r']= (int) $_POST['ank_d_r'];
if ($user['ank_d_r']==null)$user['ank_d_r']='null';
dbquery("UPDATE `user` SET `ank_d_r` = $user[ank_d_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_d_r']=='null')$user['ank_d_r']=NULL;
}
else $err[]='无效的生日格式';
if (isset($_POST['ank_m_r']) && (is_numeric($_POST['ank_m_r']) && $_POST['ank_m_r']>0 && $_POST['ank_m_r']<=12 || $_POST['ank_m_r']==NULL))
{
$user['ank_m_r']= (int) $_POST['ank_m_r'];
if ($user['ank_m_r']==null)$user['ank_m_r']='null';
dbquery("UPDATE `user` SET `ank_m_r` = $user[ank_m_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_m_r']=='null')$user['ank_m_r']=NULL;
}
else $err[]='出生月份格式无效';
if (isset($_POST['ank_g_r']) && (is_numeric($_POST['ank_g_r']) && $_POST['ank_g_r']>0 && $_POST['ank_g_r']<=date('Y') || $_POST['ank_g_r']==NULL))
{
$user['ank_g_r']= (int) $_POST['ank_g_r'];
if ($user['ank_g_r']==null)$user['ank_g_r']='null';
dbquery("UPDATE `user` SET `ank_g_r` = $user[ank_g_r] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_g_r']=='null')$user['ank_g_r']=NULL;
}
else $err[]='出生年份格式无效';
}
//---------------城市----------------//
if (isset($_GET['set']) && $_GET['set']=='gorod'){
if (isset($_POST['ank_city']) && preg_match('/[\x{4e00}-\x{9fa5}]+/u', $_POST['ank_city']))
{
$user['ank_city']=$_POST['ank_city'];
dbquery("UPDATE `user` SET `ank_city` = '".my_esc($user['ank_city'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='城市名称格式不正确';
}
//--------------qq----------------//
if (isset($_GET['set']) && $_GET['set']=='icq'){
if (isset($_POST['ank_icq']) && (is_numeric($_POST['ank_icq']) && strlen($_POST['ank_icq'])>=5 && strlen($_POST['ank_icq'])<=10 || $_POST['ank_icq']==NULL))
{
$user['ank_icq']=$_POST['ank_icq'];
if ($user['ank_icq']==null)$user['ank_icq']='null';
dbquery("UPDATE `user` SET `ank_icq` = $user[ank_icq] WHERE `id` = '$user[id]' LIMIT 1");
if ($user['ank_icq']=='null')$user['ank_icq']=NULL;
}
else $err[]='无效的QQ格式';
}
//-------------------skype---------------//
if (isset($_GET['set']) && $_GET['set']=='skype'){
if (isset($_POST['ank_skype']) && preg_match('#^([A-z0-9 \-]*)$#ui', $_POST['ank_skype']))
{
$user['ank_skype']=$_POST['ank_skype'];
dbquery("UPDATE `user` SET `ank_skype` = '".my_esc($user['ank_skype'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='无效的微信账号';
}
//----------------email------------------//
if (isset($_GET['set']) && $_GET['set']=='mail'){
if (isset($_POST['set_show_mail']) && $_POST['set_show_mail']==1)
{
$user['set_show_mail']=1;
dbquery("UPDATE `user` SET `set_show_mail` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
else
{
$user['set_show_mail']=0;
dbquery("UPDATE `user` SET `set_show_mail` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['ank_mail']) && ($_POST['ank_mail']==null || preg_match('#^[A-z0-9-\._]+@[A-z0-9]{2,}\.[A-z]{2,4}$#ui',$_POST['ank_mail'])))
{
$user['ank_mail']=$_POST['ank_mail'];
dbquery("UPDATE `user` SET `ank_mail` = '$user[ank_mail]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]='无效的电子邮件';
}

//----------------手机号码------------------//
if (isset($_GET['set']) && $_GET['set']=='mobile'){
if (isset($_POST['ank_n_tel']) && (is_numeric($_POST['ank_n_tel']) && strlen($_POST['ank_n_tel'])>=5 && strlen($_POST['ank_n_tel'])<=11 || $_POST['ank_n_tel']==NULL))
{
$user['ank_n_tel']=$_POST['ank_n_tel'];
dbquery("UPDATE `user` SET `ank_n_tel` = '$user[ank_n_tel]' WHERE `id` = '$user[id]' LIMIT 1");
}
else $err[]= '电话号码格式不正确';
}

//-----------------性别-----------------//
if (isset($_GET['set']) && $_GET['set']=='pol'){
if (isset($_POST['pol']) && $_POST['pol']==1)
{
$user['pol']=1;
dbquery("UPDATE `user` SET `pol` = '1' WHERE `id` = '$user[id]' LIMIT 1");
}
if (isset($_POST['pol']) && $_POST['pol']==0)
{
$user['pol']=0;
dbquery("UPDATE `user` SET `pol` = '0' WHERE `id` = '$user[id]' LIMIT 1");
}
}
//----------------关于我-------------//
if (isset($_GET['set']) && $_GET['set']=='osebe'){
if (isset($_POST['ank_o_sebe']) && strlen2($_POST['ank_o_sebe'])<=512)
{
if (preg_match('#[^\u4e00-\u9fa5\p{P}A-z0-9 _\-\=\+\(\)\*\!\?\.,]#ui',$_POST['ank_o_sebe']))$err[]='禁止字符用于”关于我”字段';
else {
$user['ank_o_sebe'] = $_POST['ank_o_sebe'];
dbquery("UPDATE `user` SET `ank_o_sebe` = '".my_esc($user['ank_o_sebe'])."' WHERE `id` = '$user[id]' LIMIT 1");
}
}
else $err[]= '你应该少写一些关于你自己的东西 :)';
}

//----------------чем занимаюсь-------------//

if (!isset($err))
{
$_SESSION['message'] = '更改已成功接受';
	dbquery("UPDATE `user` SET `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1");
		if (isset($_GET['act']) && $_GET['act']=='ank')
			header("Location: /user/info/anketa.php?".SID);
		elseif (isset($_GET['act']) && $_GET['act']=='ank_web')
			header("Location: /user/info.php".SID);
		else
			header("Location: /user/info/edit.php?".SID);
			exit;
}
}
err();
	echo "<form method='post' action='?".$get2."set=$get'>";
	if (isset($_GET['set']) && $_GET['set']=='nick' && $user['set_nick'] == 1)
	echo "<div class='mess'>注意！您只能更改一次昵称！</div> 账号:<br /><input type='text' name='nick' value='".htmlspecialchars($user['nick'],false)."' maxlength='32' /><br />";
	
	if (isset($_GET['set']) && $_GET['set']=='name')
	echo "真实名字:<br /><input type='text' name='ank_name' value='".htmlspecialchars($user['ank_name'],false)."' maxlength='32' /><br />";

	if (isset($_GET['set']) && $_GET['set']=='date'){
	echo '出生日期:<br />';
	//年
	echo '<select name="ank_g_r">';
    if (!empty($user['ank_g_r']))  echo '<option  value=""></option>';
		echo '<option selected="'.$user['ank_g_r'].'" value="'.$user['ank_g_r'].'" >'.$user['ank_g_r'].'</option>';
		for( $i = date("Y")-16; $i >= 1940; $i--) {
		echo '<option  value="' . $i . '">' . $i . '</option>';
		}
	echo '</select><br/>';
//月
	echo '<select name="ank_m_r">
	<option selected="'.$user['ank_m_r'].'" value="'.$user['ank_m_r'].'" >'.$user['ank_m_r'].'<option>	
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
//日
	echo '<select name="ank_d_r">
	<option selected="'.$user['ank_d_r'].'" value="'.$user['ank_d_r'].'" >'.$user['ank_d_r'].'<option>
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
	}
	if (isset($_GET['set']) && $_GET['set']=='pol'){
	echo "性别:<br /> <input name='pol' type='radio' ".($user['pol']==1?' checked="checked"':null)." value='1' />男<br />
	<input name='pol' type='radio' ".($user['pol']==0?' checked="checked"':null)." value='0' />女<br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='gorod')
	echo "城市:<br /><input type='text' name='ank_city' value='$user[ank_city]' maxlength='32' /><br />";
	if (isset($_GET['set']) && $_GET['set']=='icq')
	echo "QQ:<br /><input type='text' name='ank_icq' value='$user[ank_icq]' maxlength='10' /><br />";
	if (isset($_GET['set']) && $_GET['set']=='skype')
	echo "微信<br /><input type='text' name='ank_skype' value='$user[ank_skype]' maxlength='16' /><br />";
	if (isset($_GET['set']) && $_GET['set']=='mail'){
	echo "E-mail:<br />
		<input type='text' name='ank_mail' value='$user[ank_mail]' maxlength='32' /><br />
		<label><input type='checkbox' name='set_show_mail'".($user['set_show_mail']==1?' checked="checked"':null)." value='1' /> 在资料中显示电子邮件</label><br />";
	}
	if (isset($_GET['set']) && $_GET['set']=='mobile')
	echo "电话号码:<br /><input type='text' name='ank_n_tel' value='$user[ank_n_tel]' maxlength='11' /><br />";
	if (isset($_GET['set']) && $_GET['set']=='osebe')
	echo "关于我:<br /><input type='text' name='ank_o_sebe' value='$user[ank_o_sebe]' maxlength='512' /><br />";
	echo "<input type='submit' name='save' value='保存' /></form>";
}else{
echo "<div class='nav2'>";
echo "基本信息";
echo "</div>";
echo "<div class='nav1'>";
echo "<img src='/style/icons/str.gif' alt='*'>  <b>用户名</b> &#62; $user[login]<br />";
if ($user['set_nick'] == 1)
{
echo "<a href='?set=nick'> <img src='/style/icons/str.gif' alt='*'>  <b>昵称</b></a>";
if ($user['nick']!=NULL)
echo " &#62; $user[nick]<br />";
else
echo "<br />";
}else{
	echo "<img src='/style/icons/str.gif' alt='*'>  <b>昵称</b> &#62; $user[nick]<br />";
}
echo "<a href='?set=name'> <img src='/style/icons/str.gif' alt='*'>  姓名</a>";
if ($user['ank_name']!=NULL)
echo " &#62; $user[ank_name]<br />";
else
echo "<br />";
echo "<a href='?set=pol'> <img src='/style/icons/str.gif' alt='*'>  性别</a> &#62; ".(($user['pol']==1)?'男':'女')."<br />";
echo "<a href='?set=gorod'> <img src='/style/icons/str.gif' alt='*'>  城市</a>";
if ($user['ank_city']!=NULL)
echo " &#62; $user[ank_city]<br />";
else
echo "<br />";
echo "<a href='?set=date'> <img src='/style/icons/str.gif' alt='*'>  出生日期</a> ";
if($user['ank_d_r']!=NULL && $user['ank_m_r']!=NULL && $user['ank_g_r']!=NULL)
echo " &#62;$user[ank_g_r]/$user[ank_m_r]/$user[ank_d_r]<br />";
elseif($user['ank_d_r']!=NULL && $user['ank_m_r']!=NULL)
echo " &#62; $user[ank_m_r]/$user[ank_d_r]<br />";
else
echo "<br />";
echo "<a href='?set=osebe'> <img src='/style/icons/str.gif' alt='*'>  关于我</a>";
if ($user['ank_o_sebe'])echo " > ".htmlspecialchars($user['ank_o_sebe'])."<br />";
else
echo "<br />";
echo "<a href='?set=mobile'> <img src='/style/icons/str.gif' alt='*'> 电话号码 </a> ";
if ($user['ank_n_tel'])echo "&#62; $user[ank_n_tel]<br />";
else
echo "<br />";
echo "<a href='?set=icq'> <img src='/style/icons/str.gif' alt='*'>  QQ</a> ";
if ($user['ank_icq'])echo "&#62; $user[ank_icq]<br />";
else
echo "<br />";
echo "<a href='?set=mail'> <img src='/style/icons/str.gif' alt='*'>  E-Mail</a> ";
if ($user['ank_mail'])echo "&#62; $user[ank_mail]<br />";
else
echo "<br />";
echo "<a href='?set=skype'> <img src='/style/icons/str.gif' alt='*'> 微信 </a> "; 
if ($user['ank_skype'])echo "&#62; $user[ank_skype]<br />";
else
echo "<br />";
echo "</div>";
}
echo "<div class='foot'><img src='/style/icons/str.gif' alt='*'> <a href='anketa.php'>查看资料</a><br />";
if(isset($_SESSION['refer']) && $_SESSION['refer']!=NULL && otkuda($_SESSION['refer']))
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='$_SESSION[refer]'>".otkuda($_SESSION['refer'])."</a><br />";
echo '</div>';
include_once '../../sys/inc/tfoot.php';