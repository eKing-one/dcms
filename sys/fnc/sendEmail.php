<?php
/**
 * 发送邮件的函数
 *
 * @param string $subject 邮件主题
 * @param string $body 邮件内容（HTML格式）
 * @param string $recipientEmail 收件人的电子邮件地址
 * @param string $recipientName 收件人的姓名
 * @return array 发送邮件的结果，包含状态和消息
 */
function sendEmail($subject, $body, $recipientEmail, $recipientName) {
	global $set;
	if ($set['mail_transport_type'] == 'smtp') {
		// 创建 PHPMailer 实例
		$mail = new PHPMailer\PHPMailer\PHPMailer(true);
		try {
			// 服务器设置
			$mail->isSMTP();
			$mail->Host = $set['smtp_host'];											// SMTP 服务器（替换为你自己的 SMTP 服务器）
			$mail->SMTPAuth = ($set['smtp_auth'] == '1' ? true : false);				// 启用 SMTP 验证
			$mail->Username = $set['smtp_username'];									// SMTP 用户名
			$mail->Password = $set['smtp_password'];									// SMTP 密码
			if ($set['smtp_secure'] == 'tls') {
				$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // 使用 TLS 加密
			} elseif ($set['smtp_secure'] == 'ssl') {
				$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SSL;      // 使用 SSL 加密
			} else {
				$mail->SMTPSecure = NULL;                                               // 不加密，使用纯文本传输
			}
			$mail->Port = (int)$set['smtp_port'];										// SMTP 端口号

			// 发件人设置
			$mail->setFrom($set['set_email_from'], $set['set_email_from_name'] ?? '');
			$mail->addReplyTo($set['set_email_reply_to'], $set['set_email_reply_to_name'] ?? '');

			// 收件人设置
			$mail->addAddress($recipientEmail, $recipientName);

			// 内容设置
			$mail->isHTML(true);  // 邮件内容为 HTML 格式
			$mail->Subject = '=?utf-8?B?' . base64_encode($subject) . '?=';
			$mail->Body = $body;

			// 发送邮件
			$mail->send();
			return ['status' => 'success', 'message' => '邮件已成功发送。'];

		} catch (PHPMailer\PHPMailer\Exception $e) {
			return ['status' => 'error', 'message' => '邮件发送失败: ' . $mail->ErrorInfo];
		}
	} else {
		mail($recipientEmail, '=?utf-8?B?' . base64_encode($subject), $body);
	}
}