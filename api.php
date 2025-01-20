<?php
/**
 * 网站的API，用于以提供更多的可玩性
 * 
 * 用更现代的方法重新实现了大部分功能
 * 
 * ** 登录 **
 * curl https://dcms.myredirect.us/api.php --cookie "auth_token=<JSON Web Token>"
 * 或者
 * 
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
	
	// 检查 Authorization 头部中是否有 Bearer Token
	if (isset($_SERVER['HTTP_AUTHORIZATION']) && strpos($_SERVER['HTTP_AUTHORIZATION'], 'Bearer ') === 0) {
		// 提取 JWT（去掉 "Bearer " 前缀）
		$jwt = preg_split('/\s+/', $_SERVER['HTTP_AUTHORIZATION'])[1];
	} elseif (isset($_COOKIE['auth_token'])) {
		$jwt = $_COOKIE['auth_token'];
	} elseif (isset($_SESSION['id_user'])) {
		// 查询用户是否存在
		$result = $db->query("SELECT COUNT(*) FROM `user` WHERE `id` = :id LIMIT 1", [':id' => $_SESSION['id_user']]);

		if ($result && $result['COUNT(*)'] == 1) {
			// 获取用户数据
			$user = $db->query("SELECT * FROM `user` WHERE `id` = :id LIMIT 1", [':id' => $_SESSION['id_user']]);
			
			// 更新用户的最后登录时间
			$db->update("UPDATE `user` SET `date_last` = :time WHERE `id` = :id LIMIT 1", [':time' => time(), ':id' => $user['id']]);
			
			// 设置用户类型为 session
			$user['type_input'] = 'session';

			return $user;
		}
		return false;
	} else {
		return false;
	}

	// 解码 JWT，验证其签名和有效性
	global $set;
	try {
		$decoded = \Firebase\JWT\JWT::decode($jwt, new \Firebase\JWT\Key($set['shif'], 'HS256'));
	} catch (Exception $e) {
		return false; // 解码失败，返回 false
	}

	// 检查 JWT 是否过期
	if ($decoded->exp > time()) {
		// 查询用户是否存在
		$result = $db->query("SELECT COUNT(*) FROM `user` WHERE `id` = :id LIMIT 1", [':id' => $decoded->user_id]);
		if ($result && $result['COUNT(*)'] == 1) {
			// 可以检查数据库中的登录日志，确保 JWT 对应的 log_id 没有标记为"ban"
			$user_log = $db->query("SELECT * FROM `user_log` WHERE `id` = :log_id AND `id_user` = :user_id", [
				'log_id' => $decoded->jwt_id,
				'user_id' => $decoded->user_id
			]);
			if ($user_log && $user_log['ban'] == '0') {
				// 验证通过，说明用户已经登录
				// 更新用户的最后登录时间
				$db->update("UPDATE `user` SET `date_last` = :time WHERE `id` = :id LIMIT 1", [':time' => time(), ':id' => $user_log['id_user']]);

				// 获取用户数据
				$user = $db->query("SELECT * FROM `user` WHERE `id` = :id LIMIT 1", [':id' => $decoded->user_id]);
				return $user;
			}
		}
	}
	return false;
}


function validateCaptchaToken($user_input, $captcha_token) {
	// 解析 captcha_token
	$token_parts = explode('.', $captcha_token);
	if (count($token_parts) !== 2) {
		// captcha_token 格式错误
		return false;
	}
	
	global $set;
	// 解码 base64 编码的 IV 和密文并使用 openssl 解密验证码
	// 验证解密后的验证码是否正确（与用户输入的验证码比较）
	if (openssl_decrypt(base64_decode($token_parts[0]), 'aes-256-cbc', $set['shif'], 0, base64_decode($token_parts[1])) === $user_input) {
		return true;  // 验证通过
	} else {
		return false; // 验证失败
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
















// 处理登录
if (isset($_GET['action']) && $_GET['action'] == 'login') {	// 检查用户是否已经提交登录表单
	if (isset($_POST['nick']) && isset($_POST['password'])) {
		// 使用参数化查询验证用户名和密码
		$user = $db->query("SELECT `id`, `pass` FROM `user` WHERE `nick` = :nick LIMIT 1", ['nick' => $_POST['nick']]);

		if ($user && password_verify($_POST['password'], $user['pass'])) {	// 比较密码
			// 登录成功

			$_SESSION['id_user'] = $user['id'];

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

			setcookie('id_user', $user['id'], $expiration);
			setcookie('auth_token', $jwt, $expiration);

			// 设置响应为成功
			$response['status'] = 'success';
			$response['message'] = '登录成功';
			$response['token'] = $jwt;
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
	setcookie('id_user');
	setcookie('auth_token');
	session_destroy();
	$response['status'] = 'success';

} elseif (isset($_GET['action']) && $_GET['action'] == 'register') {
	// 注册
	try {
		if ($set['reg_select'] == 'close') {
			// 管理员已关闭注册
			throw new Exception('已关闭注册');
		}

		// 检查验证码
		if (isset($_POST['captcha']) && isset($_POST['captcha_token'])) {
			if (!validateCaptchaToken($_POST['captcha'], $_POST['captcha_token'])) {
				throw new Exception('验证码错误');
			}
		} elseif (!isset($_SESSION['captcha']) || $_SESSION['captcha'] != $_POST['captcha']) {
			throw new Exception('验证码错误');
		}

		// 检查必要参数
		if (isset($_POST['reg_nick']) && isset($_POST['password'])) {
			// 检查电子邮件
			if ($set['reg_select'] == 'open_mail') {
				if (!isset($_POST['email']) || $_POST['email'] == NULL) {
					throw new Exception('必须输入电子邮件');
				} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
					throw new Exception('无效的电子邮件格式');
				} elseif ($db->query("SELECT COUNT(*) FROM `reg_mail` WHERE `mail` = ?", [$_POST['email']])['COUNT(*)'] != 0) {
					throw new Exception('使用此电子邮件的用户已注册');
				}
			}

			// 检查昵称
			if ($db->query("SELECT COUNT(*) FROM `user` WHERE `nick` = ?", [$_POST['reg_nick']])['COUNT(*)'] == 0) {
				if (!preg_match("#^([A-z0-9\-\_\ ])+$#ui", $_POST['reg_nick'])) throw new Exception('用户名中有禁字');
				// if (preg_match("#[a-z]+#ui", $_POST['reg_nick'])) throw new Exception('只允许使用英文字母字符');
				if (preg_match("#(^\ )|(\ $)#ui", $_POST['reg_nick'])) throw new Exception('禁止在昵称的开头和结尾使用空格');
				if (getStringLength($_POST['reg_nick']) < 3) throw new Exception('昵称短于2个字符');
				if (getStringLength($_POST['reg_nick']) > 32) throw new Exception('昵称长度超过32个字符');
			} else {
				throw new Exception ('用户名 "' . stripcslashes($_POST['reg_nick']) . '"已登记');
			}

			// 检查密码
			if (getStringLength($_POST['password']) < 6) throw new Exception('为了安全，密码长度不能短于6字');
			if (getStringLength($_POST['password']) > 32) throw new Exception('密码长度超过32字');

				if ($set['reg_select'] == 'open_mail') {
					// 如果开启了注册邮箱验证
					$activation = md5(passgen());
					$db->insert("INSERT INTO `user` (`nick`, `pass`, `date_reg`, `date_last`, `pol`, `activation`, `ank_mail`) VALUES (?, ?, ?, ?, ?, ?, ?)", [
						$_POST['reg_nick'],
						password_hash($_POST['password'], PASSWORD_BCRYPT),
						time(),
						time(),
						intval($_POST['pol']),
						$activation,
						$_POST['ank_mail']
					]);
					$id_reg = dbinsertid();
					$subject = "帐户激活";
					$regmail = "你好！ $_POST[reg_nick]<br />
								要激活您的帐户，请点击链接:<br />
								<a href='http://$_SERVER[HTTP_HOST]/user/reg.php?id=$id_reg&amp;activation=$activation'>http://$_SERVER[HTTP_HOST]/user/reg.php?id=" . dbinsertid() . "&amp;activation=$activation</a><br />
								如果帐户在24小时内未激活，它将被删除<br />
								真诚的，网站管理<br />";
					$adds = "From: \"password@$_SERVER[HTTP_HOST]\" <password@$_SERVER[HTTP_HOST]>";
					//$adds = "From: <$set[reg_mail]>";
					//$adds .= "X-sender: <$set[reg_mail]>";
					$adds .= "Content-Type: text/html; charset=utf-8";
					mail($_POST['ank_mail'], '=?utf-8?B?' . base64_encode($subject) . '?=', $regmail, $adds);
				} else {
					// 未开启邮箱验证，直接注册
					$db->insert("INSERT INTO `user` (`nick`, `pass`, `date_reg`, `date_last`, `pol`) VALUES (?, ?, ?, ?, ?)", [
						$_POST['reg_nick'],
						password_hash($_POST['password'], PASSWORD_BCRYPT),
						time(),
						time(),
						intval($_POST['pol'])
					]);
				}

			$response['status'] = 'success';
			$response['message'] = '注册成功';
			
		} else {
			throw new Exception('缺少必要参数');
		}

	} catch(Exception $e) {
		$response['status'] = 'error';
		$response['message'] = $e->getMessage();
	}


} elseif (isset($_GET['action']) && $_GET['action'] == 'get_captcha_url') {
	// 获取验证码URL

	// 生成5位验证码
	$captcha = '';
	for ($i = 0; $i < 5; $i++) {
		$captcha .= mt_rand(0, 9);
	}

	// 添加过期时间
	$captcha = $captcha . '.' . time() + 300;

	// 生成随机的 iv（初始化向量）
	$iv = openssl_random_pseudo_bytes(16);

	// 加密验证码
	$encrypted_captcha = openssl_encrypt($captcha, 'aes-256-cbc', $set['shif'], 0, $iv);

	// 将 iv 和密文都进行 base64 编码并通过 URL 参数传递
	$encoded_iv = base64_encode($iv);
	$encoded_captcha = base64_encode($encrypted_captcha);

	$response['status'] = 'success';
	// 生成验证码图片URL
	$response['captcha_token'] = "{$encoded_captcha}.{$encoded_iv}";
	$response['captcha_url'] = "/captcha.php?captcha_token={$response['captcha_token']}";
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