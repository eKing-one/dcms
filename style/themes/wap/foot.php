<?php
list($msec, $sec) = explode(chr(32), microtime());
if ($_SERVER['PHP_SELF'] != '/index.php') {
	echo '<div class="foot"><img src="/style/icons/icon_glavnaya.gif" alt="*" /> <a href="/index.php">返回首页</a></div>';
}
?>
<div class="copy">
	&copy; <a href="<?php echo get_http_type(); ?>://<?php echo text($_SERVER['HTTP_HOST']); ?>" style="text-transform: capitalize;"><?php echo text($_SERVER['HTTP_HOST']); ?></a> - <?php echo date('Y'); ?> 
</div>
<div class="foot">
	在网站上: 
	<a href="/user/online.php"><?php echo dbresult(dbquery("SELECT COUNT(DISTINCT ul.id_user) AS online_users FROM `user_log` ul WHERE ul.last_online > NOW() - INTERVAL 10 MINUTE AND ul.ban = 0 AND ul.last_online = (SELECT MAX(last_online) FROM `user_log` ul2 WHERE ul2.id_user = ul.id_user AND ul2.last_online > NOW() - INTERVAL 10 MINUTE AND ul2.ban = 0)"), 0); ?></a> &amp;
	<a href="/user/online_g.php"><?php echo dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > ".(time()-600)." AND `pereh` > '0'"), 0); ?></a>
	<?php if (!$set['web']) echo ' | <a href="/?t=web">电脑版</a>'; ?>
</div>
<div class="rekl">
	<?php
	$page_size = ob_get_length(); 
	ob_end_flush(); 
	rekl(3);
	?>
	页面执行时间: <?php echo round(($sec + $msec) - $conf['headtime'], 3); ?>秒
</div>
</div> <!-- 页面正文结束 -->
</body>
</html>