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
  if (user_access('adm_info')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='update.php'>更新</a></div>\n";

  if (user_access('adm_info')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='info.php'>总体信息</a></div>\n";
  if (user_access('adm_statistic')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='statistic.php'>网站统计</a></div>\n";
  if (user_access('adm_show_adm')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='administration.php'>管理人员</a></div>\n";
  if (user_access('adm_log_read')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='adm_log.php'>管理操作日志</a></div>\n";
  
  if (user_access('adm_menu')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='menu.php'>主菜单</a></div>\n";
  if (user_access('adm_rekl')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='rekl.php'>广告</a></div>\n";
  if (user_access('adm_news')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='/news/add.php'>新闻</a></div>\n";
  
  if (user_access('adm_set_sys')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_sys.php'>系统设置</a></div>\n";
  if (user_access('adm_set_sys')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='rights.php'>文件夹权限</a></div>\n";
  if (user_access('adm_set_sys')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_bbcode.php'>BBcode设置</a></div>\n";
  if ($user['level'] > 3) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='/user/gift/create.php'>礼物</a></div>\n";
  if ($user['level'] > 3) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='smiles.php'>表情符号</a></div>\n";
  if (user_access('adm_set_forum')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_forum.php'>论坛设置</a></div>\n";
  
  if (user_access('adm_set_user')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_user.php'>用户设置</a></div>\n";
  if (user_access('adm_accesses')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='accesses.php'>用户组权限</a></div>\n";
  if (user_access('adm_banlist')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='banlist.php'>封禁列表</a></div>\n";
  if (user_access('adm_set_loads')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_loads.php'>下载设置</a></div>\n";
  if (user_access('adm_set_chat')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_chat.php'>聊天室设置</a></div>\n";
  
  if (user_access('adm_set_foto')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='settings_foto.php'>相册设置</a></div>\n";
  
  if (user_access('adm_forum_sinc')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='forum_sinc.php'>论坛表格同步</a></div>\n";
  if (user_access('adm_ref')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='referals.php'>推荐</a></div>\n";
  if (user_access('adm_ip_edit')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='opsos.php'>编辑IP操作者</a></div>\n";
  if (user_access('adm_ban_ip')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='ban_ip.php'>IP地址封禁（范围）</a></div>\n";
  
  if (user_access('adm_mysql')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='mysql.php'>MySQL查询</a></div>\n";
  if (user_access('adm_mysql')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='tables.php'>上传表格</a></div>\n";
  if (user_access('adm_themes')) echo "<div class='main'><img src='/style/icons/str.gif' alt=''/> <a href='themes.php'>主题样式</a></div>\n";
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
  echo "<img src='/captcha.php?$passgen&amp;SESS=$sess' width='100' height='30' alt='验证码' /><br />从图片中输入数字:<br //><input name='chislo' size='5' maxlength='5' value='' type='text' /><br/>";
  echo "<input type='submit' value='下一步' />";
  echo "</form>";
}
include_once '../sys/inc/tfoot.php';
