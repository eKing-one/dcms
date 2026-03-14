<?php if (isset($user)): 
	/*
	=================================
	邮件
	=================================
	*/
	$k_new=dbresult(dbquery("SELECT COUNT(`mail`.`id`) FROM `mail`
	LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'
	WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` IS NULL OR `users_konts`.`type` = 'common' OR `users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'"), 0);
	$k_new_fav=dbresult(dbquery("SELECT COUNT(`mail`.`id`) FROM `mail`
	LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'
	WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'"), 0);

	/*
	=================================
	信息中心
	=================================
	*/
	$lenta = dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$user[id]' AND `read` = '0' "), 0);

	/*
	=================================
	讨论
	=================================
	*/
	$discuss = dbresult(dbquery("SELECT COUNT(`count`) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0' "), 0); // Обсуждения

	/*
	=================================
	关于我的
	=================================
	*/
	$k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // 通知

	/*
	=================================
	好友列表
	=================================
	*/
	$k_f = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$user[id]' LIMIT 1"), 0);
	?>

	<a href="<?php echo $set['siteurl']; ?>/user/info.php?"><span class="link_title">
		<img src="<?php echo $set['siteurl']; ?>/style/themes/web/images/user.png" alt=""/>
		<br/>个人主页</span>
	</a>

	<?php if ($k_new!=0 && $k_new_fav==0): ?>
		<a href='<?php echo $set['siteurl']; ?>/user/new_mess.php'><span class='link_title'><img src='<?php echo $set['siteurl']; ?>/style/themes/web/images/mail.png' alt=''/>  <b class='count'>+<?php echo $k_new; ?></b><br/> 邮件 </span></a>
	<?php else: ?>
		<a href='<?php echo $set['siteurl']; ?>/user/conts.php'><span class='link_title'><img src='<?php echo $set['siteurl']; ?>/style/themes/web/images/mail.png' alt=''/><br/>邮件</span></a>
	<?php endif; ?>

	<a href='<?php echo $set['siteurl']; ?>/user/tape/index.php'>
		<span class='link_title'>
			<img src='<?php echo $set['siteurl']; ?>/style/themes/web/images/lenta.png' alt=''/>
			<?php if($lenta > 0): ?> <b class='count'>+<?php echo $lenta; ?></b><?php endif; ?><br/>
			信息中心
		</span>
	</a>

	<a href='<?php echo $set['siteurl']; ?>/user/discussions/index.php'>
		<span class='link_title'>
			<img src='<?php echo $set['siteurl']; ?>/style/themes/web/images/disc.png' alt=''/>
			<?php if ($discuss > 0): ?> <b class='count'>+<?php echo $discuss; ?></b><?php endif; ?><br/>
			讨论
		</span>
	</a>

	<?php if ($k_notif > 0): ?>
	<a href='<?php echo $set['siteurl']; ?>/user/notification/index.php'>
		<span class='link_title'>
			<img src='<?php echo $set['siteurl']; ?>/style/themes/web/images/notif2.png' alt=''/>
			<b class='count'>+<?php echo $k_notif; ?></b><br/>
			关于我的
		</span>
	</a>
	<?php endif; ?>

	<?php if ($k_f > 0): ?>
		<a href='<?php echo $set['siteurl']; ?>/user/frends/new.php'>
			<span class='link_title'>
				<img src='<?php echo $set['siteurl']; ?>/style/themes/web/images/frend.png' alt=''/>
				<b class='count'>+<?php echo $k_f; ?></b><br/>
				好友列表
			</span>
		</a>
	<?php else: ?>
		<a href="<?php echo $set['siteurl']; ?>/user/frends/?id=<?php echo $user['id']; ?>">
			<span class="link_title">
				<img src="<?php echo $set['siteurl']; ?>/style/themes/web/images/frend.png" alt=""/><br/>
				好友列表
			</span>
		</a>
	<?php endif; ?>

	<a href="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
		<span class="link_title">
			<img src="<?php echo $set['siteurl']; ?>/style/themes/web/images/refresh.png"/><br/>
			刷新
		</span>
	</a>

<?php elseif ($_SERVER['PHP_SELF'] != '/user/aut.php' && $_SERVER['PHP_SELF'] != '/user/reg.php'): ?>

	<a href="#user" rel="facebox"><span class="link_title2"><img src="<?php echo $set['siteurl']; ?>/style/themes/web/images/key.png" alt=""/><br />登录/注册</span></a>

	<div id="user" style="display:none;">
		<div class = 'foot'>登录账号</div>

		<form class='mess' method='post' action='<?php echo $set['siteurl']; ?>/user/login.php'>
			用户名:<br>
			<input type='text' name='nick' maxlength='32' /><br>
			密码:<br>
			<input type='password' name='pass' maxlength='32' /><br>
			<label><input type='checkbox' name='aut_save' value='1' /> 保存cookie</label><br>
			<input type='submit' value='登录'> <a href='<?php echo $set['siteurl']; ?>/user/pass.php'>密码恢复</a><br>
		</form>
		<br>
		<div class = 'foot'>注册</div>
		<form class='mess' method='post' action='<?php echo $set['siteurl']; ?>/user/reg.php'>
		选择用户名 [A-z0-9 -_]:<br>
		<input type='text' name='nick' maxlength='32' /><br>
		注册即代表你同意网站<a href='<?php echo $set['siteurl']; ?>/user/rules.php'>规则</a><br>
		<input type='submit' value='继续'>
		</form>
		<br>
	</div>

<?php endif;
