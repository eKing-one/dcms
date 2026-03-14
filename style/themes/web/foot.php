</td></tr>
</table>
</td></tr>
</table></div></div>
<?php rekl(3); ?>
<table>
	<div id="footer" class="gradient_grey">
		<div class="body_width_limit">
			<span id="copyright">
				<a href="<?php echo $set['siteurl']; ?>/user/users.php">用户列表 (<?php echo dbresult(dbquery("SELECT COUNT(`id`)FROM `user`"),0); ?>)</a>
			</span>
			<span id="copyright">
				<a href="<?php echo $set['siteurl']; ?>/user/online.php">在线 (<?php echo dbresult(dbquery("SELECT COUNT(DISTINCT ul.id_user) AS online_users FROM `user_log` ul WHERE ul.last_online > NOW() - INTERVAL 10 MINUTE AND ul.ban = 0 AND ul.last_online = (SELECT MAX(last_online) FROM `user_log` ul2 WHERE ul2.id_user = ul.id_user AND ul2.last_online > NOW() - INTERVAL 10 MINUTE AND ul2.ban = 0)"), 0); ?>)</a>
			</span>
			<span id="copyright">
				<a href="<?php echo $set['siteurl']; ?>/user/online_g.php">在线游客 (<?php echo dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > " . (time() - 600) . " AND `pereh` > '0'"), 0);?>)</a>
				<a href="?t=wap">Wap版</a>
			</span>
			<span id="language">
				<a href="<?php echo $set['siteurl']; ?>"><span style="text-transform: capitalize;">© <?php echo htmlspecialchars($_SERVER['HTTP_HOST']); ?> - <?php echo date('Y'); ?> </span></a>
			</span>
			<span id="generation">
				<?php
				list($msec, $sec) = explode(chr(32), microtime());
				$page_size = ob_get_length();
				ob_end_flush();
				if (!isset($_SESSION['traf'])) $_SESSION['traf'] = 0;
				$_SESSION['traf'] += $page_size;
				?>
				<a href="<?php echo get_http_type(); ?>://dcms-social.ru"><span style="color:white;">DCMS-Social</span></a>
			</span>                    
		</div>
	</div>
</table>
</body>
</html>
<?php
exit;