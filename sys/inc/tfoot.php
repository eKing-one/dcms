<?php
if (file_exists(H . "style/themes/$set[set_them]/foot.php")) {
	include_once H . "style/themes/$set[set_them]/foot.php";
} else {
	list($msec, $sec) = explode(chr(32), microtime());
	echo "<div class='foot'>";
	echo '<a href="' . $set['siteurl'] . '">网站首页</a><br>';
	echo '<a href="' . $set['siteurl'] . '/user/users.php">注册用户: ' . dbresult(dbquery("SELECT COUNT(*) FROM `user`"), 0) . '</a><br />';
	echo '<a href="' . $set['siteurl'] . '/user/online.php">在线用户: ' . dbresult(dbquery("SELECT COUNT(DISTINCT ul.id_user) AS online_users FROM `user_log` ul WHERE ul.last_online > NOW() - INTERVAL 10 MINUTE AND ul.ban = 0 AND ul.last_online = (SELECT MAX(last_online) FROM `user_log` ul2 WHERE ul2.id_user = ul.id_user AND ul2.last_online > NOW() - INTERVAL 10 MINUTE AND ul2.ban = 0)"), 0) . '</a><br />';
	echo '<a href="' . $set['siteurl'] . '/user/online_g.php">在线游客: ' . dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > " . (time() - 600) . " AND `pereh` > '0'"), 0) . '</a><br />';
	$page_size = ob_get_length();
	ob_end_flush();
	if(!isset($_SESSION['traf'])) $_SESSION['traf'] = 0;
	$_SESSION['traf'] += $page_size;
	echo '页面大小: ' . round($page_size / 1024, 2). ' KB<br />页面生成: ' . round($_SESSION['traf'] / 1024, 2) . ' KB <br />执行时间: ' . round(($sec + $msec) - $conf['headtime'], 3) . '秒' ;
	echo '</div>';
	echo '<div class="rekl">';
	rekl(3);
	echo "</div>";
	echo "</div></body></html>";
}
exit;
