<?php
error_reporting(E_ALL); // 启用错误显示
ini_set('display_errors',true); // 启用错误显示
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
 * 数据库操作类
 */
class Database {
	private $pdo;

	// 构造函数连接数据库
	public function __construct($host, $dbname, $username, $password) {
		try {
			$this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			die("Connection failed: " . $e->getMessage());
		}
	}

	// 执行查询，返回单个结果
	public function query($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetch();
	}

	// 执行查询，返回多个结果
	public function queryAll($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $stmt->fetchAll();
	}

	// 插入数据，返回插入的ID
	public function insert($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute($params);
		return $this->pdo->lastInsertId();
	}

	// 更新数据
	public function update($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);
		return $stmt->execute($params);
	}

	// 删除数据
	public function delete($sql, $params = []) {
		$stmt = $this->pdo->prepare($sql);
		return $stmt->execute($params);
	}
}

// 初始化全局变量
$set = setget();
$db = new Database($set['mysql_host'], $set['mysql_db_name'], $set['mysql_user'], $set['mysql_pass']);


/**
 * // 数据库使用示例
 * // 查询单条数据
 * $user = $db->query("SELECT * FROM users WHERE username = ?", ['desiredUsername']);
 *
 * // 插入新用户
 * $newUserId = $db->insert("INSERT INTO users (username, password) VALUES (?, ?)", ['newUsername', 'hashedPassword']);
 */



/**
 * 计算并返回输入字符串的加密哈希值。
 *
 * @param string $str 输入的字符串。
 * @return string 返回加密后的哈希值。
 */
function shif($str) {
	// 引入全局变量 $set
	global $set;
	// 获取加密所使用的密钥，来自全局变量 $set
	$key = $set['shif'];
	// 对输入的字符串 $str 进行 MD5 哈希运算
	$str1 = md5((string) $str);
	// 对密钥 $key 进行 MD5 哈希运算
	$str2 = md5($key);
	// 将密钥、加密后的字符串和密钥组合起来，再进行一次 MD5 哈希加密，返回最终结果
	return md5($key . $str1 . $str2 . $key);
}


// 处理登录
if (isset($_POST['nick']) && isset($_POST['pass'])) {	// 检查用户是否已经提交登录表单
    // 使用参数化查询验证用户名和密码
    $query = "SELECT `id`, `pass` FROM `user` WHERE `nick` = :nick LIMIT 1";
    $user = $db->query($query, ['nick' => $_POST['nick']]);

    if ($user && shif($_POST['pass']) == $user['pass']) {  // 比较密码
        // 登录成功
        $_SESSION['id_user'] = $user['id'];

        // 自动登录功能
        if (isset($_POST['aut_save']) && $_POST['aut_save']) {
            setcookie('id_user', $user['id'], time() + 60 * 60 * 24 * 365);
            setcookie('pass', $_POST['pass'], $user['id'], time() + 60 * 60 * 24 * 365);
        }

        // 更新用户的登录时间
        $time = time();  // 当前时间戳
        $updateQuery = "UPDATE `user` SET `date_aut` = :time, `date_last` = :time WHERE `id` = :id LIMIT 1";
        $db->update($updateQuery, ['time' => $time, 'id' => $user['id']]);

        // 记录登录日志
        $logQuery = "INSERT INTO `user_log` (`id_user`, `time`, `ua`, `ip`, `method`) VALUES (:id_user, :time, :ua, :ip, '1')";
        $db->insert($logQuery, [
            'id_user' => $user['id'],
            'time' => $time,
            'ua' => $_SERVER['HTTP_USER_AGENT'],  // 从客户端获取 User-Agent
            'ip' => $_SERVER['REMOTE_ADDR']      // 从客户端获取 IP 地址
        ]);

        // 设置响应为成功
        $response['success'] = true;
        $response['message'] = '登录成功';
    } else {
        // 登录失败
		$response['success'] = false;
        $response['message'] = '用户名或密码不正确';
    }
} else {
	$response['success'] = false;
    $response['message'] = '缺少必要的用户名或密码参数';
}

print_r($response);