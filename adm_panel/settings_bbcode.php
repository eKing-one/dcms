<?
//返回管理面板
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

// 用户和管理权限验证
user_access('adm_set_sys',null,'index.php?'.SID);
adm_check();

$set['title']='BBCode设置';
include_once '../sys/inc/thead.php';
title();

// 处理表单提交
if (isset($_POST['save'])) {
	if (isset($_POST['bb_i']) && $_POST['bb_i']==1)$temp_set['bb_i']=1; else $temp_set['bb_i']=0;
	if (isset($_POST['bb_u']) && $_POST['bb_u']==1)$temp_set['bb_u']=1; else $temp_set['bb_u']=0;
	if (isset($_POST['bb_b']) && $_POST['bb_b']==1)$temp_set['bb_b']=1; else $temp_set['bb_b']=0;
	if (isset($_POST['bb_big']) && $_POST['bb_big']==1)$temp_set['bb_big']=1; else $temp_set['bb_big']=0;
	if (isset($_POST['bb_small']) && $_POST['bb_small']==1)$temp_set['bb_small']=1; else $temp_set['bb_small']=0;
	if (isset($_POST['bb_code']) && $_POST['bb_code']==1)$temp_set['bb_code']=1; else $temp_set['bb_code']=0;
	if (isset($_POST['bb_red']) && $_POST['bb_red']==1)$temp_set['bb_red']=1; else $temp_set['bb_red']=0;
	if (isset($_POST['bb_yellow']) && $_POST['bb_yellow']==1)$temp_set['bb_yellow']=1; else $temp_set['bb_yellow']=0;
	if (isset($_POST['bb_green']) && $_POST['bb_green']==1)$temp_set['bb_green']=1; else $temp_set['bb_green']=0;
	if (isset($_POST['bb_blue']) && $_POST['bb_blue']==1)$temp_set['bb_blue']=1; else $temp_set['bb_blue']=0;
	if (isset($_POST['bb_white']) && $_POST['bb_white']==1)$temp_set['bb_white']=1; else $temp_set['bb_white']=0;
	if (isset($_POST['bb_size']) && $_POST['bb_size']==1)$temp_set['bb_size']=1; else $temp_set['bb_size']=0;
	if (isset($_POST['bb_http']) && $_POST['bb_http']==1)$temp_set['bb_http']=1; else $temp_set['bb_http']=0;
	if (isset($_POST['bb_url']) && $_POST['bb_url']==1)$temp_set['bb_url']=1; else $temp_set['bb_url']=0;
	if (isset($_POST['bb_img']) && $_POST['bb_img']==1)$temp_set['bb_img']=1; else $temp_set['bb_img']=0;
	if (isset($_POST['bb_external_img']) && $_POST['bb_external_img']==1)$temp_set['bb_external_img']=1; else $temp_set['bb_external_img']=0;

	// 保存设置到配置文件
	if (save_settings($temp_set)) {
		admin_log('设置','系统','更改BBCode参数');
		msg('设置已成功接受');
	} else {
		$err='没有更改设置文件的权限';
	}
}

err();
aut();

// 设置表单内容
echo "<form method='post' action='?$passgen'>";
echo "<label><input type='checkbox'".($temp_set['bb_i']?" checked='checked'":null)." name='bb_i' value='1' />斜体[i]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_u']?" checked='checked'":null)." name='bb_u' value='1' /> 下划线 [u]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_b']?" checked='checked'":null)." name='bb_b' value='1' /> 粗体 [b]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_big']?" checked='checked'":null)." name='bb_big' value='1' /> 大字号 [big]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_small']?" checked='checked'":null)." name='bb_small' value='1' /> 小字号 [small]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_code']?" checked='checked'":null)." name='bb_code' value='1' /> 突出显示PHP代码 [code]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_red']?" checked='checked'":null)." name='bb_red' value='1' /> 红色文字 [red]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_yellow']?" checked='checked'":null)." name='bb_yellow' value='1' /> 黄色文字 [yellow]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_green']?" checked='checked'":null)." name='bb_green' value='1' /> 绿色文本 [green]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_blue']?" checked='checked'":null)." name='bb_blue' value='1' /> 蓝色文字 [blue]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_white']?" checked='checked'":null)." name='bb_white' value='1' /> 白色文字 [white]*</label><br />";
echo "<label><input type='checkbox'".($temp_set['bb_size']?" checked='checked'":null)." name='bb_size' value='1' /> 字体大小</label><br />";
echo "[size=字体大小]文字[/size]<br />";
echo "<label><input type='checkbox'".($temp_set['bb_http']?" checked='checked'":null)." name='bb_http' value='1' /> 突出显示链接</label><br />";
echo "http://...<br />";
echo "<label><input type='checkbox'".($temp_set['bb_url']?" checked='checked'":null)." name='bb_url' value='1' /> 插入链接</label><br />";
echo "[url=链接地址]链接名称[/url]<br />";
echo "<label><input type='checkbox'".($temp_set['bb_img']?" checked='checked'":null)." name='bb_img' value='1' /> 插入图像</label><br />";
echo "<label style='margin-left:10px;'><input type='checkbox'".($temp_set['bb_external_img']?" checked='checked'":null)." name='bb_external_img' value='1' /> 允许外部图像</label><br />";
echo "[img]图片URL[/img]<br />";
echo "<br />";
echo "* 需要一个尾部标签<br />";
echo "<input value='保存' name='save' type='submit' />";
echo "</form>";

if (user_access('adm_panel_show')) {
	echo "<div class='foot'>";
	echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';