<?php
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
user_access('adm_panel_show', null, '/index.php?' . SID);
if (isset($_SESSION['adm_auth']) && $_SESSION['adm_auth'] > $time || isset($_SESSION['captcha']) && isset($_POST['chislo']) && $_SESSION['captcha'] == $_POST['chislo']) {
  $_SESSION['adm_auth'] = $time + setget("timeadmin", 1000);
  if (isset($_GET['go']) && $_GET['go'] != null) {
    header('Location: ' . base64_decode($_GET['go']));
    exit;
  }
  $set['title'] = '管理面板';
  include_once '../sys/inc/thead.php';
  title();
  err();
  aut();
  echo "<div class='mess'>";
  echo "<center><span style='font-size:16px;'><strong>DCMS-Social v.$set[dcms_version]</strong></span></center>";
  echo "<center><span style='font-size:14px;'> 官方支持网站 <a href='https://dcms-social.ru'>https://dcms-social.ru</a></span></center>";
  echo "";
  if (status_version() >= 0) {
    echo "<center> <font color='green'>最新版本</font>		</center>	";
  } else {
    echo "<center>	 <font color='red'>有个新版本 - " . version_stable() . "! <a href='/adm_panel/update.php'>更详细</a></font>		</center>	";
  }
  echo "</div>";
  if (user_access('adm_info')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a target='_blank' href='http://dcms-social.ru'>支持论坛</a></div>";
  if (user_access('adm_info')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='info.php'>一般资料</a></div>";
  if (user_access('adm_statistic')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='statistic.php'>网站统计</a></div>";
  if (user_access('adm_show_adm')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='administration.php'>管理工作</a></div>";
  if (user_access('adm_log_read')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='adm_log.php'>网站日志</a></div>";
  if (user_access('adm_menu')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='menu.php'>主页设置</a></div>";
  if (user_access('adm_rekl')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='rekl.php'>网站广告</a></div>";
  if (user_access('adm_news')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='/news/add.php'>新闻中心</a></div>";
  if (user_access('adm_set_sys')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_sys.php'>系统设置</a></div>";
  if (user_access('adm_set_sys')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_bbcode.php'>BBCode设置</a></div>";
  if ($user['level'] > 3) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='/user/gift/create.php'>礼物</a></div>";
  if ($user['level'] > 3) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='smiles.php'>表情符号</a></div>";
  if (user_access('adm_set_forum')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_forum.php'>论坛设置</a></div>";
  if (user_access('adm_set_user')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_user.php'>用户设置</a></div>";
  if (user_access('adm_accesses')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='accesses.php'>用户组权限</a></div>";
  if (user_access('adm_banlist')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='banlist.php'>禁止名单</a></div>";
  if (user_access('adm_set_loads')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_loads.php'>下载设置</a></div>";
  if (user_access('adm_set_chat')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_chat.php'>聊天设置</a></div>";
  if (user_access('adm_set_photo')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_photo.php'>照片库设置</a></div>";
  if (user_access('adm_forum_sinc')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='forum_sinc.php'>论坛表的同步</a></div>";
  if (user_access('adm_ref')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='referals.php'>转介服务</a></div>";
  if (user_access('adm_ip_edit')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='opsos.php'>编辑IP运营商</a></div>";
  if (user_access('adm_ban_ip')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='ban_ip.php'>禁止IP地址(范围)</a></div>";
  if (user_access('adm_mysql')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='mysql.php'>MySQL查询</a></div>";
  if (user_access('adm_mysql')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='tables.php'>导入SQL</a></div>";
  if (user_access('adm_themes')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='themes.php'>设计主题</a></div>";
  if (user_access('adm_themes')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='style.php'>编辑当前CSS</a></div>";
  $opdirbase = @opendir(H . 'sys/add/admin');
  while ($filebase = @readdir($opdirbase))
    if (preg_match('#\.php$#i', $filebase))
      include_once(H . 'sys/add/admin/' . $filebase);
  @closedir($opdirbase);
} else {
  $set['title'] = '防止自动更改';
  include_once '../sys/inc/thead.php';
  title();
  err();
  aut();
  echo "<form method='post' action='?gen=$passgen&amp;" . (isset($_GET['go']) ? "go=$_GET[go]" : null) . "'>";
  echo "<img src='/captcha.php?$passgen&amp;SESS=$sess' width='100' height='30' alt='核证号码' /><br />从图片中输入数字:<br //><input name='chislo' size='5' maxlength='5' value='' type='text' /><br/>";
  echo "<input type='submit' value='下一步' />";
  echo "</form>";
}
include_once '../sys/inc/tfoot.php';
