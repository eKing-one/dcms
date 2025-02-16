<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
$temp_set = $set;
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';
user_access('adm_set_sys', null, 'index.php');
adm_check();
$set['title'] = '电子邮箱发件设置';
include_once '../sys/inc/thead.php';
title();

// 处理保存设置
if (isset($_POST['save'])) {
	try {
		// 检查 SMTP 服务器地址是否设置正确
		if ($_POST['mail_transport_type'] == 'smtp' && !filter_var($_POST['smtp_host'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) throw new Exception('SMTP 服务器地址错误');
		// 检查 SMTP 服务器端口设置是否合法
		if ($_POST['mail_transport_type'] == 'smtp' && !preg_match('/^(?:[1-9][0-9]{0,4}|[1-9][0-9]{0,3}|[1-5][0-9]{0,4}|6[0-4][0-9]{0,3}|6553[0-5])$/', $_POST['smtp_port'])) throw new Exception('SMTP 服务器端口号错误');

		$temp_set['mail_transport_type'] = esc($_POST['mail_transport_type']);
		$temp_set['set_email_from'] = esc($_POST['set_email_from']);
		$temp_set['set_email_reply_to'] = esc($_POST['set_email_reply_to']);
		$temp_set['smtp_host'] = esc($_POST['smtp_host']);
		if (isset($_POST['smtp_auth']) && $_POST['smtp_auth'] == 1) {
			$temp_set['smtp_auth'] = 1;
		} else {
			$temp_set['smtp_auth'] = 0;
		}
		$temp_set['smtp_username'] = esc($_POST['smtp_username']);
		$temp_set['smtp_password'] =esc($_POST['smtp_password']);
		$temp_set['smtp_port'] = esc($_POST['smtp_port']);
		$temp_set['smtp_secure'] = esc($_POST['smtp_secure']);
		if (save_settings($temp_set)) {
			admin_log('设置', '系统', '更改电子邮箱发件设置');
			msg('已成功接受设置');
		} else {
			throw new Exception('更改配置文件失败');
		}
		header( "Location: " . $_SERVER [ "REQUEST_URI" ]);
		exit;
	} catch (Exception $e) {
		$err[] = $e->getMessage();
	}
}

if (isset($_POST['send'])) {
	// 调用封装的发送邮件函数
	$emailResult = sendEmail($_POST['test_email_title'], $_POST['test_email_body'], $_POST['test_email_address'], $_POST['test_email_name']);

	if ($emailResult['status'] == 'success') {
		// 如果邮件发送成功，更新数据库
		msg("已发送电子邮件到 {$_POST['test_email_address']}");
	} else {
		// 如果邮件发送失败
		$err[] = $emailResult['message'];
	}
}
err();
aut();
?>


<form method="post">
	电子邮箱发件方式：<br />
	<select name="mail_transport_type">
		<option <?php echo (setget('mail_transport_type', 'mail') == 'mail' ? 'selected="selected"' : null); ?> value="mail">PHP mail()</option>
		<option <?php echo (setget('mail_transport_type', 'mail') == 'smtp' ? 'selected="selected"' : null); ?> value="smtp">SMTP</option>
	</select><br />



	* 以下设置仅在 SMTP 模式可用：<br />
	发件人地址：<br />
	<input name="set_email_from" value="<?php echo ($set['set_email_from'] ?? NULL); ?>" type="email" /><br />
	发件人名称：<br />
	<input name="set_email_from_name" value="<?php echo ($set['set_email_from_name'] ?? NULL); ?>" type="text" /><br />
	回复地址：<br />
	<input name="set_email_reply_to" value="<?php echo ($set['set_email_reply_to'] ?? NULL); ?>" type="email" /><br />
	回复地址名称：<br />
	<input name="set_email_reply_to_name" value="<?php echo ($set['set_email_reply_to_name'] ?? NULL); ?>" type="text" /><br />

	SMTP 服务器地址：<br />
	<input name="smtp_host" value="<?php echo ($set['smtp_host'] ?? NULL); ?>" type="text" /><br />
	SMTP 服务器端口：<br />
	<input name="smtp_port" value="<?php echo ($set['smtp_port'] ?? NULL); ?>" type="text" /><br />

	是否启用 SMTP 验证：<br />
	<input type="checkbox" <?php if (isset($temp_set['smtp_auth'])) {echo ($temp_set['smtp_auth'] == '1' ? 'checked="checked"' : null);} ?> name="smtp_auth" value="1" /><br />

	SMTP 用户名：<br />
	<input name="smtp_username" value="<?php echo ($set['smtp_username'] ?? NULL); ?>" type="text" /><br />
	SMTP 密码：<br />
	<input name="smtp_password" value="<?php echo ($set['smtp_password'] ?? NULL); ?>" type="text" /><br />

	SMTP 加密方式：<br />
	<select name="smtp_secure">
		<option <?php echo (setget('smtp_secure', 'tls') == 'tls' ? 'selected="selected"' : null); ?> value="mail">TLS</option>
		<option <?php echo (setget('smtp_secure', 'tls') == 'ssl' ? 'selected="selected"' : null); ?> value="smtp">SSL</option>
		<option <?php echo (setget('smtp_secure', 'tls') == 'none' ? 'selected="selected"' : null); ?> value="smtp">不加密</option>
	</select><br />

	<input value="修改" name='save' type="submit" />
</form>

<form method="post">
	电子邮件发送测试<br />
	收件人地址：<br /><input name="test_email_address" type="email" /><br />
	收件人名称：<br /><input name="test_email_name" type="text" /><br />
	标题：<br /><input name="test_email_title" type="text" /><br />
	内容：<br /><textarea name='test_email_body'></textarea><br />
	<input value="发送" name='send' type="submit" />
</form>

<?php
if (user_access('adm_panel_show')) {
	echo "<div class='foot'>";
	echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
	echo "</div>";
}
include_once '../sys/inc/tfoot.php';