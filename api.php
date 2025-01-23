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



error_reporting(E_ALL); // 启用错误显示
ini_set('display_errors',true); // 启用错误显示


session_name('SESS');
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

// 加载网站设置
function setget() {
	$set = array();
	$set_default = array();
	$set_dynamic = array();
	$set_replace = array();

	// 正在加载默认设置。消除未定义变量的缺失
	$default = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/sys/dat/default.ini', true);
	$set_default = $default['DEFAULT'];
	$set_replace = $default['REPLACE'];

	if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/sys/dat/settings.php')) {
		$set_dynamic = include_once($_SERVER['DOCUMENT_ROOT'] . '/sys/dat/settings.php');
	} else {
		http_response_code(506);
		exit;
	}
	return array_merge($set_default, $set_dynamic, $set_replace);
}

/**
 * Database 类用于简化与 MySQL 数据库的交互。
 * 通过 PDO (PHP Data Objects) 提供的 API 提供常见的数据库操作方法，
 * 包括查询单条记录、查询多条记录、插入、更新和删除操作。
 * 
 * 使用示例：
 * 
 * // 创建数据库连接
 * $db = new Database('localhost', 'test_db', 'root', 'password');
 * 
 * // 查询单条记录
 * $result = $db->query('SELECT * FROM users WHERE id = ?', [1]);
 * print_r($result);
 * 
 * // 查询多条记录
 * $results = $db->queryAll('SELECT * FROM users');
 * print_r($results);
 * 
 * // 插入新记录并获取插入的 ID
 * $insertId = $db->insert('INSERT INTO users (name, email) VALUES (?, ?)', ['John Doe', 'john@example.com']);
 * echo "Inserted ID: " . $insertId;
 * 
 * // 更新记录
 * $updated = $db->update('UPDATE users SET email = ? WHERE id = ?', ['newemail@example.com', 1]);
 * echo $updated ? 'Update successful' : 'Update failed';
 * 
 * // 删除记录
 * $deleted = $db->delete('DELETE FROM users WHERE id = ?', [1]);
 * echo $deleted ? 'Delete successful' : 'Delete failed';
 */
class Database {
	// PDO 实例，负责与数据库的实际连接
	private $pdo;

	/**
	 * 构造函数，用于建立数据库连接
	 * 
	 * @param string $host 数据库主机地址
	 * @param string $dbname 数据库名
	 * @param string $username 数据库用户名
	 * @param string $password 数据库密码
	 * 
	 * 构造函数会在类实例化时尝试连接数据库，若连接失败则抛出异常并终止执行。
	 */
	public function __construct($host, $dbname, $username, $password) {
		try {
			// 创建 PDO 实例并进行数据库连接
			$this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
			
			// 设置 PDO 错误模式为异常
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// 设置默认的查询结果获取模式为关联数组
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			// 连接失败时，输出错误信息并终止脚本执行
			http_response_code(506);
			die(json_encode([
				'status' => 'error',
				'error' => "Database connection failed: " . $e->getMessage()
			]));
		}
	}

	/**
	 * 执行查询并返回单个结果。
	 * 
	 * @param string $sql SQL 查询语句
	 * @param array $params 查询时的参数，默认为空数组
	 * 
	 * @return mixed 返回查询结果，如果没有结果则返回 false
	 */
	public function query($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);	// 准备 SQL 语句
		$stmt->execute($params);			// 执行查询，传入参数
		return $stmt->fetch();				// 获取单行结果
	}

	/**
	 * 执行查询并返回多个结果。
	 * 
	 * @param string $sql SQL 查询语句
	 * @param array $params 查询时的参数，默认为空数组
	 * 
	 * @return array 返回查询结果的数组，如果没有结果则返回空数组
	 */
	public function queryAll($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);	// 准备 SQL 语句
		$stmt->execute($params);			// 执行查询，传入参数
		return $stmt->fetchAll();			// 获取所有结果
	}

	/**
	 * 插入数据到数据库，并返回插入的记录的 ID。
	 * 
	 * @param string $sql 插入数据的 SQL 语句
	 * @param array $params 插入时的参数，默认为空数组
	 * 
	 * @return string 返回插入数据的最后插入 ID
	 */
	public function insert($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);	// 准备 SQL 语句
		$stmt->execute($params);			// 执行插入操作，传入参数
		return $this->pdo->lastInsertId();	// 返回最后插入记录的 ID
	}

	/**
	 * 更新数据库中的数据。
	 * 
	 * @param string $sql 更新数据的 SQL 语句
	 * @param array $params 更新时的参数，默认为空数组
	 * 
	 * @return bool 返回执行成功与否，成功则返回 true，失败返回 false
	 */
	public function update($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);	// 准备 SQL 语句
		return $stmt->execute($params);		// 执行更新操作
	}

	/**
	 * 删除数据库中的数据。
	 * 
	 * @param string $sql 删除数据的 SQL 语句
	 * @param array $params 删除时的参数，默认为空数组
	 * 
	 * @return bool 返回执行成功与否，成功则返回 true，失败返回 false
	 */
	public function delete($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);	// 准备 SQL 语句
		return $stmt->execute($params);		// 执行删除操作
	}
}

// 初始化全局变量
$set = setget();
$db = new Database($set['mysql_host'], $set['mysql_db_name'], $set['mysql_user'], $set['mysql_pass']);

// 检测是否启用了 API
if (empty($set['api']) || $set['api'] == '0') {
	http_response_code(403);
	die(json_encode([
		'status' => 'error',
		'error' => 'The administrator turned off the API'
	]));
}

/**
 * 获取客户端 IP 和 User-Agent
 * @return array
 */
function get_client_details() {
	global $set;

	// 从数据库获取 CDN IP 范围
	function get_cdn_ips() {
		global $db;
		// 查询 'cdn_ips' 表中的所有数据
		$result = $db->queryAll("SELECT `ip_range` FROM `cdn_ips`"); // 执行查询并获取所有结果

		// 检查查询结果是否有数据
		if (count($result) > 0) {
			// 使用 array_column 提取所有 ip_range
			return array_column($result, 'ip_range');
		} else {
			return []; // 如果没有数据，返回空数组
		}
	}

	// 检查一个IP是否在特定IP范围内
	function isIpInRange($ip, $ranges) {
		$ipAddress = \IPLib\Factory::addressFromString($ip);
		foreach ($ranges as $range) {
			if ($range->contains($ipAddress)) {
				return true;
			}
		}
		return false;
	}

	// 读取 CDN IP 列表并创建 Range 数组
	$cdnIpRanges = array_map(function ($cidr) {
		return \IPLib\Factory::parseRangeString($cidr);
	}, get_cdn_ips());

	// 获取客户端 IP 地址
	$ip = '';
	switch ($set['get_ip_from_header']) {
		case 'Forwarded':
			if (!empty($_SERVER['HTTP_FORWARDED']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
				foreach (array_map('trim', explode(',', $_SERVER['HTTP_FORWARDED'])) as $part) {
					if (stripos($part, 'for=') !== false) {
						$ip = trim(str_ireplace('for=', '', $part));
						break;
					}
				}
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			break;
		case 'X-Forwarded-For':
			if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
				foreach (array_map('trim', explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $ip) {
					if (isIpInRange($ip, $cdnIpRanges)) {
						continue;
					}
					break;
				}
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			break;

		case 'X-Real-IP':
			if (!empty($_SERVER['HTTP_X_REAL_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
				$ip = $_SERVER['HTTP_X_REAL_IP'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			break;

		case 'CF-Connecting-IP':
			if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
				$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			break;

		case 'True-Client-IP':
			if (!empty($_SERVER['HTTP_TRUE_CLIENT_IP']) && isIpInRange($_SERVER['REMOTE_ADDR'], $cdnIpRanges)) {
				$ip = $_SERVER['HTTP_TRUE_CLIENT_IP'];
			} else {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			break;

		case 'disabled':
		default:
			$ip = $_SERVER['REMOTE_ADDR'];
			break;
	}

	// 获取 User-Agent
	$ua = 'N/A';
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$ua = $_SERVER['HTTP_USER_AGENT'];
		$result = UAParser\Parser::create()->parse($ua);
		if (isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']) && stripos($ua, 'Opera') !== false) {
			$ua_om = preg_replace('#[^a-z_\. 0-9\-]#iu', null, strtolower($_SERVER['HTTP_X_OPERAMINI_PHONE_UA']));
			$ua = $result->toString();
			$ua = $ua . '(' . $ua_om . ')';
		} else {
			$ua = $result->toString();
		}
	}

	return [
		'ip' => $ip,
		'ua' => $ua
	];
}
/**
 * $ip = $clientDetails['ip'];
 * $ua = $clientDetails['ua'];
 */
$clientDetails = get_client_details();


function checkLoginStatus() {
	global $db;
	global $set;

	function jwt_get_user_info($jwt, $set, $db) {
		// 解码 JWT，验证其签名和有效性
		try {
			$decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($set['shif'], 'HS256'));
		} catch (Exception $e) {
			return false; // 解码失败，返回 false
		}

		// 检查 JWT 是否过期
		if ($decoded->exp > time()) {
			// 查询用户是否存在
			return get_user_info($decoded->user_id, $decoded->jwt_id, $db);
		} else {
			return false; // JWT 过期，返回 false
		}
	}


	function get_user_info($user_id, $log_id, $db) {
		// 查询用户是否存在
		$result = $db->query("SELECT COUNT(*) FROM `user` WHERE `id` = :id LIMIT 1", [':id' => $user_id]);
		if ($result && $result['COUNT(*)'] == 1) {
			// 检查数据库中的登录日志，确保 JWT 对应的 log_id 没有标记为"ban"
			$user_log = $db->query("SELECT * FROM `user_log` WHERE `id` = :log_id AND `id_user` = :user_id", [
				'log_id' => $log_id,
				'user_id' => $user_id
			]);
			if ($user_log && $user_log['ban'] == '0') {
				// 验证通过，说明用户已经登录
				// 更新用户的最后登录时间
				$db->update("UPDATE `user` SET `date_last` = :time WHERE `id` = :id LIMIT 1", [
					':time' => time(),
					':id' => $user_log['id_user']
				]);

				// 返回用户数据
				return $db->query("SELECT * FROM `user` WHERE `id` = :id LIMIT 1", [':id' => $user_id]);
			} else {
				return false; // 登录记录被 ban，返回 false
			}
		} else {
			return false; // 用户不存在，返回 false
		}
	}


	// 检查 Authorization 头部中是否有 Bearer Token
	if (isset($_SERVER['HTTP_AUTHORIZATION']) && strpos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ') === 0) {
		// 提取 JWT（去掉 "Bearer " 前缀）
		$user = jwt_get_user_info(preg_split('/\s+/', $_SERVER['HTTP_AUTHORIZATION'])[1], $set, $db);
		// 设置用户类型为 token
		if ($user) $user['type_input'] = 'token';
	} elseif (isset($_COOKIE['auth_token'])) {
		// 从 Cookie 提取 JWT
		$user = jwt_get_user_info($_COOKIE['auth_token'], $set, $db);
		// 设置用户类型为 cookie
		if ($user) $user['type_input'] = 'cookie';
	} elseif (isset($_SESSION['id_user']) && isset($_SESSION['login_id'])) {
		// 使用 session 检测登录状态
		$user = get_user_info($_SESSION['id_user'], $_SESSION['login_id'], $db);
		// 设置用户类型为 session
		if ($user) $user['type_input'] = 'session';
	} else {
		return false;
	}
	return $user;
}


function validateCaptchaToken($user_input, $captcha_token) {
	global $set;
	global $db;
	// 解析 captcha_token
	$token_parts = explode('.', $captcha_token);
	if (count($token_parts) !== 2) {
		// captcha_token 格式错误
		return [
			'status' => 'error',
			'message' => 'captcha_token 格式错误'
		];
	}

	// 解密并拆分 Token
	$decrypted_captcha_token = explode('.', openssl_decrypt(base64_decode($token_parts[0]), 'aes-256-cbc', $set['shif'], 0, base64_decode($token_parts[1])));
	if (count($decrypted_captcha_token) !== 2) {
		return [
			'status' => 'error',
			'message' => 'captcha_token 格式不正确'
		];
	} elseif ($decrypted_captcha_token[1] < time()) {
		return [
			'status' => 'error',
			'message' => 'captcha_token 已过期'
		];
	}
	// 查询数据库，检查 token 是否存在且未使用
	$token_record = $db->query("SELECT * FROM captcha_tokens WHERE captcha_token = ? AND status = 'unused'", [$captcha_token]);

	if (!$token_record) {
		return ['status' => 'error', 'message' => 'captcha_token 无效或已被使用'];
	}
	// 验证解密后的验证码是否正确（与用户输入的验证码比较）
	if ($decrypted_captcha_token[0] === $user_input) {
		// 验证通过，更新 token 状态为 'used'
		$db->update("UPDATE captcha_tokens SET status = 'used' WHERE captcha_token = ?", [$captcha_token]);
		return [
			'status' => 'success'
		];
	} else {
		// 验证失败
		return [
			'status' => 'error',
			'message' => '验证码错误'
		];
	}
}


// 计算字符串长度
function getStringLength($str) {
	if (extension_loaded('iconv')) {	// 检查 iconv 扩展是否可用
		// 使用 iconv_strlen()，如果 iconv 扩展可用
		return iconv_strlen($str, 'UTF-8');
	} elseif (extension_loaded('mbstring')) {	// 检查 mbstring 扩展是否可用
		// 使用 mb_strlen()，如果 mbstring 扩展可用
		return mb_strlen($str, 'UTF-8');
	} else {
		// 如果两者都不可用，使用 strlen() 来获取字节长度
		return strlen($str);
	}
}


// 删除过期的captcha_token
$db->query("DELETE FROM captcha_tokens WHERE expires_at < NOW()");



// 处理登录
if (isset($_GET['action']) && $_GET['action'] == 'login') {	// 检查用户是否已经提交登录表单
	if (isset($_POST['nick']) && isset($_POST['password'])) {
		// 使用参数化查询验证用户名和密码
		$user = $db->query("SELECT `id`, `pass` FROM `user` WHERE `nick` = :nick LIMIT 1", ['nick' => $_POST['nick']]);

		if ($user && password_verify($_POST['password'], $user['pass'])) {	// 比较密码
			// 登录成功


			// 更新用户的登录时间
			$updateQuery = "UPDATE `user` SET `date_aut` = :time, `date_last` = :time WHERE `id` = :id LIMIT 1";
			$db->update($updateQuery, ['time' => time(), 'id' => $user['id']]);

			// 记录登录日志
			$logQuery = "INSERT INTO `user_log` (`id_user`, `date`, `ua`, `ip`, `method`) VALUES (:id_user, :date, :ua, :ip, '1')";
			$log_id = $db->insert($logQuery, [
				'id_user' => $user['id'],
				'date' => date('Y-m-d H:i:s'),
				'ua' => $clientDetails['ua'],	// 从客户端获取 User-Agent
				'ip' => $clientDetails['ip']			// 从客户端获取 IP 地址
			]);

			// 在 session 存储用户ID与登录记录ID
			$_SESSION['id_user'] = $user['id'];
			$_SESSION['login_id'] = $log_id;

			// 选择了“记住我”
			if (isset($_POST['aut_save']) && $_POST['aut_save']) {
				$expiration = time() + 60 * 60 * 24 * 365;
			} else {
				$expiration = time() + 3600;
			}

			$payload = array(
				"iat" => time(),
				"exp" => $expiration,
				"jwt_id" => $log_id,
				"user_id" => $user['id'],
				"username" => $_POST['nick']
			);

			$jwt = \Firebase\JWT\JWT::encode($payload, $set['shif'], 'HS256');

			setcookie('id_user', $user['id'], $expiration, '/');
			setcookie('auth_token', $jwt, $expiration, '/');

			// 设置响应为成功
			$response['status'] = 'success';
			$response['message'] = '登录成功';
			$response['data']['user_id'] = $user['id'];
			$response['data']['token'] = $jwt;
		} else {
			// 登录失败
			http_response_code(403);
			$response['status'] = 'error';
			$response['message'] = '用户名或密码不正确';
		}
	} else {
		http_response_code(403);
		$response['status'] = 'error';
		$response['message'] = '缺少必要参数';
	}



} elseif (isset($_GET['action']) && $_GET['action'] == 'logout') {
	// 退出登录
	setcookie('id_user', '', time() - 3600, '/');
	setcookie('auth_token', '', time() - 3600, '/');
	session_destroy();
	$response['status'] = 'success';



} elseif (isset($_GET['action']) && $_GET['action'] == 'register') {
	// 注册
	try {
		if ($set['reg_select'] == 'close') {
			// 管理员已关闭注册
			throw new Exception('已关闭注册');
		}

		// 验证验证码
		if (!isset($_POST['captcha']) || !isset($_POST['captcha_token'])) {
			throw new Exception('验证码不能为空');
		}
	
		// 优化验证码验证逻辑
		$validateCaptchaToken = validateCaptchaToken($_POST['captcha'], $_POST['captcha_token']);
		if ($validateCaptchaToken['status'] != 'success') {
			throw new Exception($validateCaptchaToken['message']);
		}
	
		// 检查必要参数
		if (!isset($_POST['reg_nick'])) {
			throw new Exception('缺少昵称');
		}
		if (!isset($_POST['password'])) {
			throw new Exception('缺少密码');
		}
	
		// 先检查邮箱（如果启用了邮件验证）
		if ($set['reg_select'] == 'open_mail' && empty($_POST['email'])) {
			throw new Exception('缺少电子邮件');
		}
		if (isset($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			throw new Exception('无效的电子邮件');
		}
	
		// 检查昵称
		if (!preg_match("#^([A-Za-z0-9\-\_\ ])+$#", $_POST['reg_nick'])) {
			throw new Exception('用户名包含非法字符');
		}
		$nickLength = getStringLength($_POST['reg_nick']);
		if ($nickLength < 3) throw new Exception('昵称短于3个字符');
		if ($nickLength > 32) throw new Exception('昵称长度超过32个字符');
	
		// 检查用户昵称和电子邮件是否已存在
		if ($db->query("SELECT COUNT(*) FROM `user` WHERE `nick` = ?", [$_POST['reg_nick']])['COUNT(*)'] > 0) {
			throw new Exception('用户名已注册');
		} elseif (isset($_POST['email']) && $db->query("SELECT COUNT(*) FROM `reg_mail` WHERE `mail` = ?", [$_POST['email']])['COUNT(*)'] != 0) {
			throw new Exception('电子邮件已注册');
		}
	
		// 检查密码
		$passwordLength = getStringLength($_POST['password']);
		if ($passwordLength < 6) throw new Exception('密码长度不能短于6个字符');
		if ($passwordLength > 32) throw new Exception('密码长度超过32个字符');
	
		// 如果开启了邮箱验证，创建激活码
		if ($set['reg_select'] == 'open_mail') $activation = md5(passgen());
	
		// 注册用户
		$id_reg = $db->insert("INSERT INTO `user` (`nick`, `pass`, `date_reg`, `date_last`, `pol`, `activation`, `email`) VALUES (?, ?, ?, ?, ?, ?, ?)", [
			$_POST['reg_nick'],
			password_hash($_POST['password'], PASSWORD_DEFAULT),
			time(),
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
						<a href='http://{$_SERVER['HTTP_HOST']}/user/reg.php?id=$id_reg&amp;activation=$activation'>点击激活帐户</a><br />
						如果帐户在24小时内未激活，它将被删除。<br />
						真诚的，网站管理团队";
	
			$headers = [
				'From' => "password@{$_SERVER['HTTP_HOST']}",
				'Content-Type' => 'text/html; charset=utf-8',
			];
	
			// 使用 PHP 的 mail() 函数发送激活邮件
			// 之后会考虑替换为 PHPMailer 来提升邮件的可靠性
			mail($_POST['email'], '=?utf-8?B?' . base64_encode($subject) . '?=', $regmail, $headers);
			$response['message'] = '已发送电子邮件，等待验证';
		} else {
			// 如果没有开启邮箱验证，直接注册
			$response['message'] = '注册成功';
		}
	
		$response['status'] = 'success';
		$response['data']['user_id'] = $id_reg;
	
	} catch (Exception $e) {
		$response['status'] = 'error';
		$response['message'] = $e->getMessage();
	}



} elseif (isset($_GET['action']) && $_GET['action'] == 'get_captcha_url') {
	// 获取 Captcha URL 和 Captcha token

	// 生成5位验证码
	$captcha_value = rand(10000, 99999);
	$expiry_time = time() + 600;  // 设置过期时间为 10 分钟后

	// 生成随机的 iv（初始化向量）
	$iv = openssl_random_pseudo_bytes(16);

	$response['status'] = 'success';
	// 给验证码添加过期时间，加密后进行 base64 编码，与 base64 编码过的 iv 拼装在一起作为 captcha_token
	$response['captcha_token'] = base64_encode(openssl_encrypt($captcha . '.' . time() + 600, 'aes-256-cbc', $set['shif'], 0, $iv)) . '.' . base64_encode($iv);
	// 生成验证码图片 URL
	$response['captcha_url'] = "/captcha.php?captcha_token={$response['captcha_token']}";

	// 插入数据库，保存生成的 token，状态为 'unused'
	$db->insert("INSERT INTO captcha_tokens (captcha_token, expires_at, status) VALUES (?, FROM_UNIXTIME(?), 'unused')", [
		$response['captcha_token'],
		$expiry_time
	]);


} elseif (isset($_GET['action']) && $_GET['action'] == 'activation-account') {
	// 激活账号

	if ($set['reg_select'] == 'close') {
		$response['status'] = 'error';
		$response['message'] = "已关闭注册";
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
			$response['message'] = "账号 {$user['nick']} 已激活";
		}
	}



} else {
	// 检查登录状态
	$user_info = checkLoginStatus();
	if ($user_info) {
		$response['status'] = 'success';
		$response['message'] = "Hello {$user_info['nick']}";
	} else {
		$response['status'] = 'error';
	}
}

header('Content-type: application/json');
echo json_encode($response);