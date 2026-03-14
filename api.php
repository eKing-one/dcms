<?php
/*
 * MIT License
 * 
 * Copyright (c) 2025 GuGuan123
 * 
 * 本软件基于 MIT 许可证发布。具体许可条款如下：
 * 
 * 允许在本软件及其附带文档文件（以下简称“软件”）的基础上进行修改、复制、分发及/或销售，
 * 且在提供软件的副本时，需附上此许可证声明和版权声明。
 * 
 * 本软件按“原样”提供，不作任何形式的明示或暗示的担保，包括但不限于对适销性、适合某一特定用途的担保。
 * 在任何情况下，无论是在合同诉讼、侵权或其他诉讼中，作者或版权持有者对因使用本软件或其他交易的结果
 * 所产生的任何索赔、损害或其他责任不承担任何责任。
 * 
 * 你可以在 https://choosealicense.com/licenses/mit/ 查看详细的 MIT 原始许可证条款。
 */

require_once 'sys/inc/start.php';
require_once 'sys/inc/compress.php';
require_once 'sys/inc/sess.php';
require_once 'sys/inc/home.php';
require_once 'sys/inc/settings.php';
require_once 'sys/inc/db_connect.php';
require_once 'sys/inc/ipua.php';
require_once 'sys/inc/fnc.php';
require_once 'sys/inc/user.php';

// 检测是否启用了 API
if (empty($set['api']) || $set['api'] == '0') {
	http_response_code(403);
	header('Content-type: application/json');
	die(json_encode([
		'status' => 'error',
		'error' => 'The administrator turned off the API'
	]));
}

$action = $_GET['action'] ?? NULL;

switch ($action) {
	// 处理登录
	case 'login':
		if (isset($_POST['nick']) && isset($_POST['password'])) {
			// 选择了“记住我”
			if (isset($_POST['aut_save']) && $_POST['aut_save'] == '1') {
				$expiration = time() + 60 * 60 * 24 * 365;
			} else {
				$expiration = time() + 3600 * 24;
			}
			$authManagerLoginResult = $authManager->login($_POST['nick'], $_POST['password'], $expiration, 'nick');
			if ($authManagerLoginResult['status']) {
				// 登录成功

				// 在 session 存储用户ID与登录记录ID
				$_SESSION['id_user'] = $authManagerLoginResult['data']['user_id'];
				$_SESSION['login_id'] = $authManagerLoginResult['data']['login_id'];

				setcookie('auth_token', $authManagerLoginResult['data']['token'], $expiration, '/');

				// 设置响应为成功
				$response = [
					'status' => 'success',
					'message' => 'login successful',
					'data' => $authManagerLoginResult['data']
				];
			} else {
				// 登录失败
				http_response_code(403);
				$response = ['status' => 'error', 'message' => 'incorrect username or password'];
			}
		} else {
			http_response_code(403);
			$response = ['status' => 'error', 'message' => 'missing required parameters'];
		}
		break;

	case 'logout':
		// 退出登录
		setcookie('auth_token', '', time() - 3600, '/');
		session_destroy();
		if (isset($user) && $authManager->logout($user['login_id'])) {
			$response['status'] = 'success';
		} else {
			http_response_code(403);
			$response['status'] = 'error';
		}
		break;

	case 'register':
		// 注册
		try {
			if ($set['reg_select'] == 'close') {
				// 管理员已关闭注册
				throw new Exception('registration is closed');
			}

			// 验证验证码
			if (!isset($_POST['captcha']) || !isset($_POST['captcha_token'])) {
				throw new Exception('verification code is required');
			}

			$Captcha = new GuGuan123\dcms\Services\Captcha($set, $db);
			// 验证码验证逻辑
			$validateCaptchaToken = $Captcha->validateToken($_POST['captcha'], $_POST['captcha_token']);
			if ($validateCaptchaToken['status'] != 'success') {
				throw new Exception($validateCaptchaToken['message']);
			}

			// 检查必要参数
			if (!isset($_POST['reg_nick'])) {
				// 缺少昵称参数
				throw new Exception('nick is missing');
			}
			if (!isset($_POST['password'])) {
				// 缺少密码参数
				throw new Exception('password is missing');
			}

			// 先检查邮箱（如果启用了邮件验证）
			if ($set['reg_select'] == 'open_mail' && empty($_POST['email'])) {
				throw new Exception('email is missing');
			}
			if (isset($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				throw new Exception('invalid email address');
			}

			// 检查昵称
			if (!preg_match("#^([A-Za-z0-9\-\_\ ])+$#", $_POST['reg_nick'])) {
				// 昵称含有非法字符
				throw new Exception('invalid characters in nick');
			}
			$nickLength = strlen2($_POST['reg_nick']);
			if ($nickLength < 3) throw new Exception('nick too short');
			if ($nickLength > 32) throw new Exception('nick too long');

			// 检查用户昵称和电子邮件是否已存在
			if ($db->query("SELECT COUNT(*) FROM `user` WHERE `nick` = ?", [$_POST['reg_nick']])['COUNT(*)'] > 0) {
				throw new Exception('nick already registered');
			} elseif (isset($_POST['email']) && $db->query("SELECT COUNT(*) FROM `reg_mail` WHERE `mail` = ?", [$_POST['email']])['COUNT(*)'] != 0) {
				throw new Exception('email already registered');
			}

			// 检查密码
			$passwordLength = strlen2($_POST['password']);
			if ($passwordLength < 6) throw new Exception('password too short');
			if ($passwordLength > 32) throw new Exception('password too long');

			// 如果开启了邮箱验证，创建激活码
			if ($set['reg_select'] == 'open_mail') $activation = md5(random_bytes(16));

			// 注册用户
			$id_reg = $db->insert("INSERT INTO `user` (`nick`, `pass`, `date_reg`, `pol`, `activation`, `email`) VALUES (?, ?, ?, ?, ?, ?)", [
				$_POST['reg_nick'],
				password_hash($_POST['password'], PASSWORD_DEFAULT),
				time(),
				intval((isset($_POST['pol']) && ($_POST['pol'] == '1')) ? 1 : 0),
				($set['reg_select'] == 'open_mail') ? $activation : NULL,
				$_POST['email'] ?? null
			]);

			// 邮件激活逻辑
			if ($set['reg_select'] == 'open_mail') {
				$subject = "帐户激活";
				$regmail = "你好！ {$_POST['reg_nick']}<br />
							要激活您的帐户，请点击链接:<br />
							<a href='" . $set['siteurl'] . "/user/reg.php?id=$id_reg&amp;activation=$activation'>点击激活帐户</a><br />
							如果帐户在24小时内未激活，它将被系统自动删除<br />
							CN_DCMS-Social 管理组";


				// 调用封装的发送邮件函数
				$emailResult = sendEmail($subject, $regmail, $_POST['email'], $_POST['reg_nick']);

				if ($emailResult['status'] == 'success') {
					// 如果邮件发送成功
					$response['status'] = 'success';
					$response['data']['user_id'] = $id_reg;
					$response['message'] = "verification email sent";
				} else {
					// 如果邮件发送失败
					$response['status'] = 'error';
					$response['message'] = $emailResult['message'];
				}
			} else {
				// 如果没有开启邮箱验证，直接注册
				$response['message'] = 'registration successful';
				$response['data']['user_id'] = $id_reg;
				$response['status'] = 'success';
			}
		} catch (Exception $e) {
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();

			// 设置 HTTP Code
			if ($response['message'] == 'registration is closed') {
				http_response_code(405);
			} elseif ($response['message'] == 'nick already registered' || $response['message'] == 'email already registered') {
				http_response_code(403);
			} elseif (isset($emailResult['status']) && $emailResult['status'] == 'error') {
				http_response_code(500);
			} else {
				http_response_code(400);
			}
		}
		break;

	case 'get-captcha-url':
		// 获取 Captcha URL 和 Captcha token
		$Captcha = new GuGuan123\dcms\Services\Captcha($set, $db);
		$response = $Captcha->createToken();
		break;

	case 'activation-account':
		// 激活账号

		if ($set['reg_select'] == 'close') {
			$response['status'] = 'error';
			$response['message'] = "Registration is closed";
		} elseif (isset($_GET['id']) && isset($_GET['activation'])) {
			if ($db->query("SELECT COUNT(*) FROM `user` WHERE `id` = :id AND `activation` = :activation", [':id' => intval($_GET['id']), ':activation' => $_GET['activation']])['COUNT(*)'] == 1) {
				// 更新激活状态
				$db->update("UPDATE `user` SET `activation` = NULL WHERE `id` = :id LIMIT 1", [':id' => intval($_GET['id'])]);
		
				// 获取用户信息
				$user = $db->query("SELECT * FROM `user` WHERE `id` = :id LIMIT 1", [':id' => intval($_GET['id'])]);
		
				// 插入激活邮件记录
				$db->insert("INSERT INTO `reg_mail` (`id_user`, `mail`) VALUES (:id_user, :mail)", [
					':id_user' => $user['id'],
					':mail' => $user['email']
				]);
		
				// 显示激活成功消息并设置会话
				$response['status'] = 'success';
				$response['message'] = "account activated";
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = 'missing parameters';
			http_response_code(400);
		}
		break;

	case 'forgot-password':
		// 忘记密码
		if (isset($_POST['nick']) && isset($_POST['email']) && isset($_POST['captcha']) && isset($_POST['captcha_token'])) {
			// 验证验证码
			if (!isset($_POST['captcha']) || !isset($_POST['captcha_token'])) {
				throw new Exception('verification code is required');
			}

			$Captcha = new GuGuan123\dcms\Services\Captcha($set, $db);
			// 验证码验证逻辑
			$validateCaptchaToken = $Captcha->validateToken($_POST['captcha'], $_POST['captcha_token']);
			if ($validateCaptchaToken['status'] != 'success') {
				$response = array(
					'status' => 'error',
					'message' => $validateCaptchaToken['message']
				);
			} else {
				$result = $db->query("SELECT COUNT(*) FROM `user` WHERE `nick` = :nick", [':nick' => $_POST['nick']]);

				if ($result && $result['COUNT(*)'] == 1) {
					$result = $db->query("SELECT COUNT(*) FROM `user` WHERE `nick` = :nick AND `email` = :email", [
						':nick' => $_POST['nick'],
						':email' => $_POST['email']
					]);
					if ($result && $result['COUNT(*)'] == 1) {
						// 生成链接Token
						$token = bin2hex(random_bytes(32));
						// 插入数据库，存储 token 和创建时间
						$db->query("INSERT INTO `password_reset_tokens` (`user_id`, `token`) VALUES (:user_id, :token)", [
							':user_id' => $userId,
							':token' => $token
						]);

						$user2 = $db->query("SELECT * FROM `user` WHERE `nick` = :nick LIMIT 1", [':nick' => $_POST['nick']]);
						$subject = "密码恢复";
						$regmail = "你好！ $user2[nick]<br />
									您已激活密码恢复<br />
									需重置密码，请点击链接:<br />
									<a href='" . $set['siteurl'] . "/user/pass.php?id={$user2['id']}&amp;token={$token}'>" . $set['siteurl'] . "/user/pass.php?id={$user2['id']}&amp;token={$token}</a><br />
									此链接为一次性有效，成功重置密码或登录后即失效({$user2['nick']})<br />CN_DCMS-Social 管理组<br />";

						// 调用封装的发送邮件函数
						$emailResult = sendEmail($subject, $regmail, $user2['email'], $user2['nick']);

						if ($emailResult['status'] == 'success') {
							// 如果邮件发送成功
							$response['status'] = 'success';
							$response['message'] = "password reset email sent";
						} else {
							// 如果邮件发送失败
							http_response_code(500);
							$response['status'] = 'error';
							$response['message'] = $emailResult['message'];
						}
					} else {
						http_response_code(400);
						$response['status'] = 'error';
						$response['message'] = 'invalid email address';
					}
				} else {
					http_response_code(400);
					$response['status'] = 'error';
					$response['message'] = 'nick not found';
				}
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = 'missing parameters';
		}
		break;

	case 'online-users':
		$results = $db->queryAll('SELECT ul.id, ul.id_user, ul.last_online, ul.url FROM `user_log` ul WHERE ul.last_online > NOW() - INTERVAL 10 MINUTE AND ul.ban = 0 AND ul.last_online = (SELECT MAX(last_online) FROM `user_log` ul2 WHERE ul2.id_user = ul.id_user AND ul2.last_online > NOW() - INTERVAL 10 MINUTE AND ul2.ban = 0) ORDER BY ul.last_online DESC');

		$response = ['status' => 'success', 'users' => array_map(function($user) {
			return [
				'id' => $user['id_user'],
				'last_online' => $user['last_online']
			];
		}, $results)];
		break;

	case 'user-info':
		$user_info = user::get_user(($_GET['id'] ?? ($user ?? 0)));
		if ($user_info) {
			$response = [
				'status' => 'success',
				'data' => [
					'id' => $user_info['id'],
					'nick' => $user_info['nick'],
					'date_reg' => $user_info['date_reg'],
					'balls' => $user_info['balls'],
					'browser' => $user_info['browser'],
					'money' => $user_info['money'],
					'group_name' => $user_info['group_name'],
					'pol' => $user_info['pol'],
					'date_last' => $user_info['date_last']
				]
			];
		} else {
			http_response_code(404);
			$response['status'] = 'error';
		}
		break;

	// 留言板相关
	case 'guest-msg-list':
		$k_post = $db->queryColumn("SELECT COUNT(id) FROM `guest`");
		$k_page = k_page($k_post, $set['p_str']);
		$page = page($k_page);
		$start = $set['p_str'] * $page - $set['p_str'];

		$results = $db->queryAll("SELECT * FROM `guest` ORDER BY id DESC LIMIT $start, $set[p_str]");

		$response = array('status' => 'success', 'data' => $results, 'all_pages' => $k_page);
		break;

	case 'guest-msg-add':
		if (isset($_POST['msg'])) {
			// 检查是否有违禁词
			$mat = antimat($_POST['msg']);
			if ($mat) {
				$response['status'] = 'error';
				$response['message'] = 'forbidden strings: ' . $mat;
			} elseif (strlen2($_POST['msg']) > 1024) {
				$response['status'] = 'error';
				$response['message'] = 'content too long';
			} elseif (strlen2($_POST['msg']) < 2) {
				$response['status'] = 'error';
				$response['message'] = 'content too short';
			} else {
				if (isset($user)) {
					// 获取该用户的上一条消息
					$lastMessage = $db->query('SELECT `msg`, `time` FROM `guest` WHERE id_user = ? ORDER BY `time` DESC LIMIT 1', [($user['id']) ?? 0]);
					if ($lastMessage && $lastMessage['msg'] == $_POST['msg'] && (time() - $lastMessage['time']) < 300) {
						$response['status'] = 'error';
						$response['message'] = 'duplicate content';
					} else {
						// 活动积分的累积
						include_once 'sys/add/user.active.php';
						
						// 添加通知信息
						if (isset($ank_reply['id'])) {
							$notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_reply['id'] . "' LIMIT 1"));
							if ($notifiacation['komm'] == 1 && $ank_reply['id'] != $user['id'])
								dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_reply[id]', 0, 'guest', '$time')");
						}
						$db->insert('INSERT INTO `guest` (id_user, time, msg) values(?, ?, ?)', [$user['id'], $time, $_POST['msg']]);
						$response['status'] = 'success';
					}
				} elseif (isset($set['write_guest']) && $set['write_guest'] == 1) {
					if (isset($_POST['captcha']) && isset($_POST['captcha_token'])) {
						$Captcha = new GuGuan123\dcms\Services\Captcha($set, $db);
						$validateCaptchaToken = $Captcha->validateToken($_POST['captcha'], $_POST['captcha_token']);
						if ($validateCaptchaToken['status'] == 'success') {
							// 获取上一条消息
							$lastMessage = $db->query('SELECT `msg`, `time` FROM `guest` WHERE id_user = ? ORDER BY `time` DESC LIMIT 1', [0]);
							if ($lastMessage && $lastMessage['msg'] == $_POST['msg'] && (time() - $lastMessage['time']) < 300) {
								$response['status'] = 'error';
								$response['message'] = 'duplicate content';
							} else {
								$msgId = $db->insert('INSERT INTO `guest` (id_user, time, msg) values(?, ?, ?)', [0, $time, $_POST['msg']]);
								$response = ['status' => 'success', 'id' => $msgId];
							}
						} else {
							$response['status'] = 'error';
							$response['message'] = $validateCaptchaToken['message'];
						}
					} else {
						$response['status'] = 'error';
						$response['message'] = 'captcha not found';
					}
				} else {
					$response['status'] = 'error';
					$response['message'] = 'not login';
				}
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = 'msg not found';
		}
		break;

	case 'guest-msg-delete':
		if (isset($user)) {
			if (isset($_POST['id'])) {
				$post = $db->query('SELECT * FROM `guest` WHERE `id` = ? LIMIT 1', [$_POST['id']]);
				if (empty($post['id'])) {
					$response = ['status' => 'error', 'message' => 'msg not exist'];
				} else {
					if ($post['id_user'] == 0) {
						$ank['id'] = 0;
						$ank['pol'] = 'guest';
						$ank['level'] = 0;
						$ank['nick'] = '客人';
					} else {
						$ank = user::get_user($post['id_user']);
					}
					if (user_access('guest_delete') || $user['id'] == $post['id_user']) {
						if ($user['id'] != $post['id_user']) admin_log('留言板', '删除邮件', '从中删除消息 ' . $ank['nick']);
						$db->delete('DELETE FROM guest WHERE id = ?', [$post['id']]);
						$response['status'] = 'success';
					} else {
						$response = ['status' => 'error', 'message' => 'no permissions'];
					}
				}
			} else {
				$response = ['status' => 'error', 'message' => 'msg id not found'];
			}
		} else {
			$response = ['status' => 'error', 'message' => 'not login'];
		}
		break;

	case 'guest-users-list':
		$k_post = $db->query("SELECT COUNT(DISTINCT ul.id_user) AS online_users
							FROM `user_log` ul
							WHERE ul.last_online > NOW() - INTERVAL 100 SECOND
								AND ul.ban = 0
								AND ul.url LIKE '/guest/%'
								AND ul.last_online = (
									SELECT MAX(last_online)
									FROM `user_log` ul2
									WHERE ul2.id_user = ul.id_user
										AND ul2.last_online > NOW() - INTERVAL 100 SECOND
										AND ul2.ban = 0
								)");
		$k_page = k_page($k_post['online_users'], $set['p_str']);
		$page = page($k_page);
		$start = $set['p_str'] * $page - $set['p_str'];

		$query = $db->queryAll("SELECT DISTINCT ul.id_user, ul.last_online
								FROM `user_log` ul
								WHERE ul.last_online > NOW() - INTERVAL 100 SECOND
									AND ul.ban = 0
									AND ul.url LIKE '/guest/%'
								ORDER BY ul.last_online DESC
								LIMIT $start, $set[p_str]");

		$response = ['status' => 'success', 'data' => $query, 'all_pages' => $k_page];
		break;

	// 聊天室相关
	case 'chat-rooms-list':
		$results = $db->queryAll('SELECT * FROM `chat_rooms` ORDER BY `pos` ASC');
		$response['status'] = 'success';
		$response['data'] = $results;
		break;

	case 'chat-users-list':
		$results = $db->queryAll('SELECT * FROM `chat_who`');
		$response['status'] = 'success';
		$response['data'] = $results;
		break;

	case 'chat-msg-list':
		if (isset($_GET['room'])) {
			$room = $db->query('SELECT * FROM `chat_rooms` WHERE `id` = ? LIMIT 1', [intval($_GET['room'])]);
			if (empty($room)) {
				$response = ['status' => 'error', 'message' => 'room not found'];
			} else {
				$k_post = $db->queryColumn("SELECT COUNT(*) FROM `chat_post` WHERE `room` = '$room[id]' AND (`privat`='0'" . (isset($user) ? " OR `privat` = '$user[id]'" : null) . ")");
				$k_page = k_page($k_post, $set['p_str']);
				$page = page($k_page);
				$start = $set['p_str'] * $page - $set['p_str'];

				$results = $db->queryAll("SELECT * FROM `chat_post` WHERE `room` = ? AND (`privat`= '0'" . (isset($user) ? " OR `privat` = '$user[id]'" : null) . ") ORDER BY id DESC LIMIT {$start}, {$set['p_str']}", [
					$room['id']
				]);
				$response = ['status' => 'success', 'data' => $results, 'all_pages' => $k_page];
			}
		} else {
			$response = ['status' => 'error', 'message' => 'room id not found'];
		}
		break;

	case 'chat-msg-get':
		if (isset($_GET['room'])) {
			$room = $db->query('SELECT * FROM `chat_rooms` WHERE `id` = ? LIMIT 1', [intval($_GET['room'])]);
			if (empty($room)) {
				$response = ['status' => 'error', 'message' => 'room not found'];
			} else {
				if (isset($_GET['id'])) {
					$results = $db->queryAll("SELECT * FROM `chat_post` WHERE `room` = ? AND (`privat`= '0'" . (isset($user) ? " OR `privat` = '$user[id]'" : null) . ") AND `id` > ? ORDER BY id ASC LIMIT {$set['p_str']}", [
						$room['id'],
						$_GET['id'] // 最后一条已获取的消息ID
					]);
					$response = ['status' => 'success', 'data' => $results];
				} else {
					$response = ['status' => 'error', 'message' => 'msg id not found'];
				}
			}
		} else {
			$response = ['status' => 'error', 'message' => 'room id not found'];
		}
		break;

	case 'chat-msg-add':
		if (isset($user)) {
			if (isset($_GET['room'])) {
				$room = $db->query('SELECT * FROM `chat_rooms` WHERE `id` = ? LIMIT 1', [intval($_GET['room'])]);
				if (empty($room)) {
					$response = ['status' => 'error', 'message' => 'room not found'];
				} elseif (isset($_POST['msg'])) {
					$msg = $_POST['msg'];
					$mat = antimat($msg);
					if ($mat) {
						$response['status'] = 'error';
						$response['message'] = 'forbidden strings: ' . $mat;
					} elseif (strlen2($msg) > 1024) {
						$response['status'] = 'error';
						$response['message'] = 'content too long';
					} elseif (strlen2($msg) < 2) {
						$response['status'] = 'error';
						$response['message'] = 'content too short';
					} else {
						// 获取该用户的上一条消息
						$lastMessage = $db->query('SELECT `msg`, `time` FROM `chat_post` WHERE id_user = ? ORDER BY `time` DESC LIMIT 1', [$user['id']]);
						if ($lastMessage && $lastMessage['msg'] == $msg && (time() - $lastMessage['time']) < 300) {
							$response['status'] = 'error';
							$response['message'] = 'duplicate content';
						} else {
							if (isset($_POST['privat'])) {
								$priv = abs(intval($_POST['privat']));
							} else {
								$priv = 0;
							}
							$msgId = $db->insert('INSERT INTO `chat_post` (`id_user`, `time`, `msg`, `room`, `privat`) values(?, ?, ?, ?, ?)',[
								$user['id'],
								$time,
								$msg,
								$room['id'],
								$priv
							]);
							$response = ['status' => 'success', 'id' => $msgId];
						}
					}
				} else {
					$response = ['status' => 'error', 'message' => 'msg not found'];
				}
			} else {
				$response = ['status' => 'error', 'message' => 'room id not found'];
			}
		} else {
			$response['status'] = 'error';
			$response['message'] = 'not login';
		}
		break;

	// 日记相关
	case 'note-list':
		try {
			// 总日记数
			$k_post = $db->queryColumn('SELECT COUNT(*) FROM `notes`');

			// 处理分页
			$k_page = k_page($k_post, $set['p_str']);
			$page   = page($k_page);
			$start  = $set['p_str'] * $page - $set['p_str'];

			// 排序方式
			$sortir = in_array($_GET['sort'] ?? '', ['count', 'time'], true) ? $_GET['sort'] : 'time';

			// 取当前页数据
			$rows = $db->queryAll("SELECT * FROM `notes` ORDER BY `{$sortir}` DESC LIMIT {$start}, {$set['p_str']}");


			// 循环处理每条日记
			$json = [];
			foreach ($rows as $post) {
				// 权限判断部分
				$allowViewNote = false;
				if ($post['private'] == 0) {
					$allowViewNote = true;
				} else {
					if (isset($user)) {
						if ($post['private'] == 1) {
							$frend = $db->queryColumn('SELECT COUNT(*) FROM `frends` WHERE (`user` = :uid AND `frend` = :author) OR (`user` = :author2 AND `frend` = :uid2) LIMIT 1', [
								':uid'     => $user['id'],
								':author'  => $post['id_user'],
								':author2' => $post['id_user'],
								':uid2'    => $user['id']
							]);
							if ($user['id'] == $post['id_user'] || $frend == 2 || user_access('notes_delete')) $allowViewNote = true;
						} elseif ($post['private'] == 2 && ($user['id'] == $post['id_user'] || user_access('notes_delete'))) {
							$allowViewNote = true;
						}
					}
				}

				$json[$post['id']] = [
					'title'          => $allowViewNote ? $post['name'] : null,
					'date'           => date('Y-m-d H:i:s', $post['time']),
					'id_user'        => $post['id_user'],
					'count'          => $post['count'],
					'id_dir'         => $post['id_dir'],
					'type'           => $post['type'],
					'private'        => $post['private'],
					'share'          => $post['share'],
					'share_id'       => $post['share_id'],
					'share_text'     => $post['share_text'],
					'share_name'     => $post['share_name'],
					'share_id_user'  => $post['share_id_user'],
					'share_type'     => $post['share_type'],
					'share_user'     => $post['share_user']
				];
			}

			$response = [
				'status' => 'success',
				'data'   => $json,
				'all_pages' => $k_page
			];
		} catch (\Exception $e) {
			$response = [
				'status'  => 'error',
				'message' => $e->getMessage()
			];
		}
		break;

	case 'note-get':
		try {
			if (!isset($_GET['id'])) throw new \Exception('id not found');

			$post = $db->query('SELECT * FROM `notes` WHERE `id` = ? LIMIT 1', [$_GET['id']]);

			if (empty($post)) throw new \Exception('note not exist');

			if ($post['private'] == 0) {
				$allowViewNote = true;
			} else {
				if (isset($user)) {
					if ($post['private'] == 1) {
						$frend = $db->queryColumn("SELECT COUNT(*) FROM `frends` WHERE (`user` = ? AND `frend` = ?) OR (`user` = ? AND `frend` = ?) LIMIT 1", [
							$user['id'], $post['id_user'], $post['id_user'], $user['id']
						]);
						if ($user['id'] == $post['id_user'] || $frend == 2  || user_access('notes_delete')) {
							$allowViewNote = true;
						} else {
							$allowViewNote = false;
						}
					} elseif ($post['private'] == 2 && ($user['id'] == $post['id_user'] || user_access('notes_delete'))) {
						$allowViewNote = true;
					} else {
						$allowViewNote = false;
					}
				} else {
					$allowViewNote = false;
				}
			}

			// 评论区
			$note_comment = [];
			if ($allowViewNote) {
				$k_post = $db->queryColumn('SELECT COUNT(*) FROM `notes_komm` WHERE `id_notes` = ?', [$post['id']]);
				$k_page = k_page($k_post, $set['p_str']);
				$page = page($k_page);
				$start = $set['p_str'] * $page - $set['p_str'];

				$comment_rows = $db->queryAll("SELECT * FROM `notes_komm` WHERE `id_notes` = ? ORDER BY `time` LIMIT $start, $set[p_str]", [$post['id']]);
				foreach ($comment_rows as $comment_post) {
					$note_comment[] = $comment_post;
				}
			}

			$response = array(
				'status' => 'success',
				'data' => array(
					'title' => $allowViewNote ? $post['name'] : NULL,
					'msg' => $allowViewNote ? $post['msg'] : NULL,
					'date' => date('Y-m-d H:i:s', $post['time']),
					'tags' => $allowViewNote ? $post['tags'] : NULL,
					'id_user' => $post['id_user'],
					'count' => $post['count'],
					'id_dir' => $post['id_dir'],
					'type' => $post['type'],
					'private' => $post['private'],
					'share' => $post['share'],
					'share_id' => $post['share_id'],
					'share_text' => $post['share_text'],
					'share_name' => $post['share_name'],
					'share_id_user' => $post['share_id_user'],
					'share_type' => $post['share_type'],
					'share_user' => $post['share_user'],
					'comment_list' =>  $note_comment,
					'all_comment_pages' => $k_page
				)
			);
		} catch (\Exception $e) {
			$response = array(
				'status' => 'error',
				'message' => $e->getMessage()
			);
		}
		break;

	case 'note-add':
		try {
			if (isset($user)) throw new \Exception('not login');
			if (empty($_POST['title'])) throw new \Exception('title not found');
			if (empty($_POST['msg'])) throw new \Exception('msg not found');
			if (($user['rating'] < 2 || $user['group_access'] < 6) && $user['id'] != 1746) {
				if (empty($_POST['chislo'])) throw new \Exception('chislo not found');
				if (empty($_POST['captcha_token'])) throw new \Exception('captcha_token not found');
				$Captcha = new GuGuan123\dcms\Services\Captcha($set, $db);
				$validateCaptchaToken = $Captcha->validateToken($_POST['captcha'], $_POST['captcha_token']);
				if ($validateCaptchaToken['status'] != 'success') throw new \Exception($validateCaptchaToken['message']);
			}

			$msg = my_esc($_POST['msg']);
			$id_dir = intval($_POST['id_dir']);
			if (isset($_POST['private'])) {
				$privat = intval($_POST['private']);
			} else {
				$privat = 0;
			}
			if (isset($_POST['private_komm'])) {
				$privat_komm = intval($_POST['private_komm']);
			} else {
				$privat_komm = 0;
			}
			if (strlen2($_POST['title']) > 32) throw new \Exception('title too long');
			if (strlen2($msg) > 30000) throw new \Exception('content too long');
			if (strlen2($msg) < 2) throw new \Exception('content too short');
			$st = $db->insert('INSERT INTO `notes` (`time`, `msg`, `name`, `id_user`, `private`, `private_komm`, `id_dir`, `type`) values(?, ?, ?, ?, ?, ?, ?, ?)', [
				time(),
				$msg,
				$_POST['title'],
				$user['id'],
				$privat,
				$privat_komm,
				$id_dir,
				0
			]);
			if($privat != 2) {
				$db->query('insert into `stena`(`id_stena`,`id_user`,`time`,`info`,`info_1`,`type`) values(?, ?, ?, ?, ?, ?)', [
					$user['id'],
					$user['id'],
					time(),
					'新日记',
					$st,
					'note'
				]);
			}

			$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $user['id'] . "' AND `i` = '1'");
			while ($f = dbarray($q)) {
				$a = user::get_user($f['frend']);
				$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // 常规功能区设置
				if ($f['lenta_notes'] == 1 && $lentaSet['lenta_notes'] == 1 ) // 邮件过滤器
				if (dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'notes' AND `id_file` = '$st' LIMIT 1"), 0) == 0) {
					dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$user[id]', 'notes', '$time', '$st', '1')");
				} else {
					$tape = dbarray(dbquery("SELECT * FROM `tape` WHERE `type` = 'notes' AND `id_file` = '$st'"));
					dbquery("UPDATE `tape` SET `count` = '" . ($tape['count'] + 1) . "', `read` = '0', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'notes' AND `id_file` = '$st' LIMIT 1");
				}
			}
			$response = ['status' => 'success', 'id' => $st];
		} catch (\Exception $e) {
			$response = array(
				'status' => 'error',
				'message' => $e->getMessage()
			);
		}

	default:
		// 检查登录状态
		if (isset($user)) {
			$response = array(
				'status' => 'success',
				'message' => "Hello {$user['nick']}"
			);
		} else {
			$response = array('status' => 'error');
		}
}

header('Content-type: application/json');
echo json_encode($response);
