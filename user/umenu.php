<?php
require_once '../sys/inc/start.php';
require_once '../sys/inc/compress.php';
require_once '../sys/inc/sess.php';
require_once '../sys/inc/home.php';
require_once '../sys/inc/settings.php';
require_once '../sys/inc/db_connect.php';
require_once '../sys/inc/ipua.php';
require_once '../sys/inc/fnc.php';
require_once '../sys/inc/user.php';
if (isset($user)) $ank['id'] = $user['id'];
only_reg();
$set['title'] = '个人中心';
require_once '../sys/inc/thead.php';
title();
aut();

echo "<div class='main'>";
echo user::avatar($ank['id']);
echo "	" . user::nick($ank['id'], 0);
echo "</div>";


// 检查用户是否成功登录
if (isset($_GET['nick']) && isset($_GET['pass'])) {
	echo '<div class="mess">';
	echo '如果您的浏览器不支持Cookie，您可以创建一个自动登录链接<br />';
	echo '<input type="text" value="' . $set['siteurl'] . '/user/login.php?id=' . $user['id'] . '&amp;pass=' . text($_GET['pass']) . '" /><br />';
	echo '</div>';	
}
?>
<div class="main" id="umenu">
<img src='../style/icons/icon_stranica.gif' alt='' /> <a href='info.php'>我的页面</a><br />
</div>
<div class="main" id="umenu">
<img src='../style/icons/apply14.png' alt='' /> <a href='daily_checkin.php'>每日签到</a><br />
</div>
<div class="main" id="umenu">
<img src='../style/icons/anketa.gif' alt='' /> <a href='daily_news.php'>每日新闻</a><br />
</div>
<div class="main" id="umenu">
<img src='../style/my_menu/ank.png' alt='' /> <a href='info/anketa.php'>个人资料</a> [<a href='info/edit.php'>编辑</a>]<br />
</div>
<div class="main" id="umenu">
<img src="../style/my_menu/secure.png" alt="" /> <a href="info/secure.php">更改密码</a><br />
</div>
<div class="main" id="umenu">
<img src="../style/my_menu/rules.png" alt="" /> <a href="rules.php">规则</a><br />
</div>
<div class="main" id="umenu">
<img src="../style/my_menu/set.png" alt="" /> <a href="my_aut.php">登录历史</a><br />
</div>
<div class="main" id="umenu">
<img src="../style/icons/settings.png" alt="" /> <a href="info/settings.php">我的设置</a><br />
</div>


<?php
// 管理权限
if (user_access('adm_panel_show')) {
	echo '<div class="main" id="umenu">';
	echo '<img src="../style/my_menu/adm_panel.png" alt="" /> <a href="../adm_panel/">管理面板</a><br />';
	echo '</div>';
}

// 仅适用于wap
if ($set['web'] == false) {
	echo '<div class="main" id="umenu">';
	echo '<a href="exit.php"><img src="../style/icons/delete.gif" /> 退出登录 ' . user::nick($user['id'], 0, 0, 0) . '</a><br />';
	echo '</div>';
}
require_once '../sys/inc/tfoot.php';