<?php
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
user_access('adm_set_sys',null,'index.php?'.SID);
adm_check();
$set['title']='系统设置';
include_once '../sys/inc/thead.php';
title();

if (isset($_POST['save'])) {
	// Shaman
	$temp_set['title']=esc(stripcslashes(htmlspecialchars($_POST['title'])),1);
	// 这是我的末日
	$temp_set['mail_backup']=esc($_POST['mail_backup']);
	$temp_set['p_str']=intval($_POST['p_str']);
	dbquery("ALTER TABLE `user` CHANGE `set_p_str` `set_p_str` INT( 11 ) DEFAULT '$temp_set[p_str]'");
	if (!preg_match('#\.\.#',$_POST['set_them']) && is_dir(H.'style/themes/'.$_POST['set_them'])) {
		$temp_set['set_them']=$_POST['set_them'];
		dbquery("ALTER TABLE `user` CHANGE `set_them` `set_them` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '$temp_set[set_them]'");
	}
	if (!preg_match('#\.\.#',$_POST['set_them2']) && is_dir(H.'style/themes/'.$_POST['set_them2'])) {
		$temp_set['set_them2']=$_POST['set_them2'];
		dbquery("ALTER TABLE `user` CHANGE `set_them2` `set_them2` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '$temp_set[set_them2]'");
	}
	if ($_POST['show_err_php']==1 || $_POST['show_err_php']==0) {
		$temp_set['show_err_php']=intval($_POST['show_err_php']);
	}
	if (isset($_POST['antidos']) && $_POST['antidos']==1)
	$temp_set['antidos']=1; else $temp_set['antidos']=0;
	if (isset($_POST['antimat']) && $_POST['antimat']==1)
	$temp_set['antimat']=1; else $temp_set['antimat']=0;
	$temp_set['meta_keywords']=esc(stripcslashes(htmlspecialchars($_POST['meta_keywords'])),1);
	$temp_set['background'] = esc(stripcslashes(htmlspecialchars($_POST['background'])), 1);
	$temp_set['meta_description']=esc(stripcslashes(htmlspecialchars($_POST['meta_description'])),1);
	$temp_set['toolbar'] = intval($_POST['toolbar']);
	$temp_set['exit'] = intval($_POST['exit']);
	$temp_set['timeadmin'] = intval($_POST['timeadmin']);
	$temp_set['job'] = intval($_POST['job']);
	$temp_set['replace'] = intval($_POST['replace']);
	if ($_POST['replace'] != 1) {}
	$temp_set['main'] = esc(stripcslashes(htmlspecialchars(($_POST['main']))));
	$temp_set['header'] = esc(stripcslashes(htmlspecialchars(($_POST['header']))));
	$temp_set['get_ip_from_header'] = in_array($_POST['get_ip_from_header'], ['auto', 'disabled', 'X-Forwarded-For', 'X-Real-IP', 'CF-Connecting-IP', 'True-Client-IP']) ? $_POST['get_ip_from_header'] : 'auto';
	if (save_settings($temp_set)) {
		admin_log('设置', '系统', '更改系统设置');
		msg('已成功接受设置');
	} else {
		$err = '无权更改设置文件';
	}
	header( "Location: " . $_SERVER [ "REQUEST_URI" ]);
	exit();
}

err();
aut();

echo "<form method=\"post\" action=\"?\">";
echo "网站名称:<br /><input name=\"title\" value=\"$temp_set[title]\" type=\"text\" /><br />";
echo "每页显示:<br /><input name=\"p_str\" value=\"$temp_set[p_str]\" type=\"text\" /><br />";
echo "主页:<br /><input name=\"main\" value=\"".setget('main',"")."\" type=\"text\" /><br />";
echo "Admin Toolbar:<br />
<select name='toolbar'>
	<option ".(setget('toolbar',1)==1? " selected ":null)." value='1'>是</option>
	<option ".(setget('toolbar',1)==0? " selected ":null)." value='0'>没有</option>
</select>
<br />";

echo "管理会话超时秒数:<br /><input name=\"timeadmin\" value='".setget('timeadmin',1000)."' type=\"text\" /><br />";

/*
echo '网站背景:<br />
<input type="color"  name="background"
					 value="'.setget('background').'">
<br />';
*/

echo "网站运行状态：<br />
<select name='job'>
	<option ".(setget('job',1)==1? " selected ":null)." value='1'>已启用</option>
	<option ".(setget('job',1)==0? " selected ":null)." value='0'>已禁用</option>
</select>
<br />";

echo "退出账号确认：<br />
<select name='exit'>
	<option ".(setget('exit',1)==1? " selected ":null)." value='1'>开启</option>
	<option ".(setget('exit',1)==0? " selected ":null)." value='0'>关闭</option>
</select>
<br />";

echo "网站标题栏：<br />
<select name='header'>
	<option ".(setget('header',"index")=="index"? " selected ":null)." value='index'>仅在首页</option>
	<option ".(setget('header',"all")=="all"? " selected ":null)." value='all'>在所有页面上</option>
</select>
<br />";

echo "从请求标头获取用户IP：<br />
<select name='get_ip_from_header'>
	<option ".(setget('get_ip_from_header',"auto")=="auto"? " selected ":null)." value='auto'>自动识别</option>
	<option ".(setget('get_ip_from_header',"disabled")=="disabled"? " selected ":null)." value='enable'>禁用</option>
	<option ".(setget('get_ip_from_header',"X-Forwarded-For")=="X-Forwarded-For"? " selected ":null)." value='X-Forwarded-For'>X-Forwarded-For</option>
	<option ".(setget('get_ip_from_header',"X-Real-IP")=="X-Real-IP"? " selected ":null)." value='X-Real-IP'>X-Real-IP</option>
	<option ".(setget('get_ip_from_header',"CF-Connecting-IP")=="CF-Connecting-IP"? " selected ":null)." value='CF-Connecting-IP'>CF-Connecting-IP</option>
	<option ".(setget('get_ip_from_header',"True-Client-IP")=="True-Client-IP"? " selected ":null)." value='True-Client-IP'>True-Client-IP</option>
</select>
<br />";

/*
echo "  通过文件夹安装插件 /Replace/:<br />
<select name='replace'>
	<option ".(setget('replace',1)==1? " selected ":null)." value='1'>包括</option>
	<option ".(setget('replace',1)==0? " selected ":null)." value='0'>断开</option>
</select>
<br />";
*/

echo "网站默认主题 (WAP移动端):<br /><select name='set_them'>";
$opendirthem=opendir(H.'style/themes');
while ($themes=readdir($opendirthem)) {
	// пропускаем корневые папки и файлы
	if ($themes=='.' || $themes=='..' || !is_dir(H."style/themes/$themes"))continue;
	// пропускаем темы для web браузеров
	if (test_file2(H."style/themes/$themes/.only_for_web"))continue;
	echo "<option value='$themes'".($temp_set['set_them']==$themes?" selected='selected'":null).">".trim(file_get_contents(H.'style/themes/'.$themes.'/them.name'))."</option>";
}
closedir($opendirthem);
echo "</select><br />";

echo "网站默认主题 (PC端):<br /><select name='set_them2'>";
$opendirthem=opendir(H.'style/themes');
while ($themes=readdir($opendirthem)){
	// пропускаем корневые папки и файлы
	if ($themes=='.' || $themes=='..' || !is_dir(H."style/themes/$themes"))continue;
	// пропускаем темы для wap браузеров
	if (file_exists(H."style/themes/$themes/.only_for_wap"))continue;
	echo "<option value='$themes'".($temp_set['set_them2']==$themes?" selected='selected'":null).">".trim(file_get_contents(H.'style/themes/'.$themes.'/them.name'))."</option>";
}
closedir($opendirthem);
echo "</select><br />";

echo "关键词 (META):<br />";
echo "<textarea name='meta_keywords'>$temp_set[meta_keywords]</textarea><br />";
echo "资料描述 (META):<br />";
echo "<textarea name='meta_description'>$temp_set[meta_description]</textarea><br />";
echo "<label><input type='checkbox'".($temp_set['antidos']?" checked='checked'":null)." name='antidos' value='1' /> 反Dos*</label><br />";
echo "<label><input type='checkbox'".($temp_set['antimat']?" checked='checked'":null)." name='antimat' value='1' /> 反CC</label><br />";
echo "php解释器错误:<br /><select name=\"show_err_php\">";
echo "<option value='0'".($temp_set['show_err_php']==0?" selected='selected'":null).">隐藏</option>";
echo "<option value='1'".($temp_set['show_err_php']==1?" selected='selected'":null).">显示</option>";
echo "</select><br />";
echo "备份用电子邮件：<br /><input type='text' name='mail_backup' value='$temp_set[mail_backup]'  /><br />";
echo "<br />";
echo "* 防止Dos攻击 - 防范来自同一IP地址的频繁请求<br />";
echo "<input value=\"修改\" name='save' type=\"submit\" />";
echo "</form>";
if (user_access('adm_panel_show')) {
	echo "<div class='foot'>";
	echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';