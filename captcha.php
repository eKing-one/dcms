<?php
define('H', $_SERVER['DOCUMENT_ROOT'] . '/');

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

function decrypt_captcha_token($captcha_token) {
	// 解析 captcha_token
	$token_parts = explode('.', $captcha_token);
	if (count($token_parts) !== 2) {
		throw new Exception('captcha_token 参数格式不正确');
	}

	// 使用 openssl 解密
	$decrypted_captcha_token = openssl_decrypt(base64_decode($token_parts[0]), 'aes-256-cbc', setget()['shif'], 0, base64_decode($token_parts[1]));

	$decrypted_captcha_token_parts = explode('.', $decrypted_captcha_token);
	if (count($decrypted_captcha_token_parts) !== 2) {
		throw new Exception('captcha_token 参数格式不正确');
	}

	if ($decrypted_captcha_token_parts[1] < time()) {
		throw new Exception('captcha_token 已过期');
	}

	// 检查解码后的验证码是否是5位纯数字
	if (preg_match('/^\d{5}$/', $decrypted_captcha_token_parts[0])) {
		// 返回解密后的验证码
		return $decrypted_captcha_token_parts[0];
	}
	throw new Exception("验证码“{$decrypted_captcha_token_parts[0]}”不是5位纯数字");
}

class captcha
{
	var $str; // 验证码字符
	var $x = 100; // 图像宽度
	var $y = 40; // 图像高度
	var $img; // 图像资源
	var $gif = false; // 是否支持GIF格式
	var $png = false; // 是否支持PNG格式
	var $jpg = false; // 是否支持JPG格式

	// 构造函数，初始化验证码字符串和图像资源
	function __construct($str) {
		// 检查GD库是否启用
		if (!function_exists('gd_info')) {
			header('Location: /style/errors/gd_err.gif');
			exit;
		}
		// 检查支持的图像格式
		if (imagetypes() & IMG_PNG) $this->png = true;
		if (imagetypes() & IMG_GIF) $this->gif = true;
		if (imagetypes() & IMG_JPG) $this->jpg = true;
		$this->str = $str;
		$this->img = imagecreatetruecolor($this->x, $this->y); // 创建真彩色图像
		imagefill($this->img, 0, 0, imagecolorallocate($this->img, 255, 255, 255)); // 填充背景色为白色
	}

	// 创建验证码图像
	function create() {
		for ($i = 0; $i < 5; $i++) {
			$n = $this->str[$i]; // 获取验证码字符串的每个字符
			// 根据支持的图像格式加载对应的数字图像
			if ($this->png) $num[$n] = imagecreatefrompng(H . '/style/captcha/' . $n . '.png');
			elseif ($this->gif) $num[$n] = imagecreatefromgif(H . '/style/captcha/' . $n . '.gif');
			elseif ($this->jpg) $num[$n] = imagecreatefromjpeg(H . '/style/captcha/' . $n . '.jpg');
			// 将数字图像复制到验证码图像上
			imagecopy($this->img, $num[$n], $i * 15 + 10, 8, 0, 0, 15, 20);
		}
	}

	// 为验证码图像添加波浪效果
	function MultiWave() {
		$width = imagesx($this->img); // 获取图像宽度
		$height = imagesy($this->img); // 获取图像高度
		$img2 = imagecreatetruecolor($width, $height); // 创建新的图像资源
		// 随机生成波浪的参数
		$rand1 = mt_rand(700000, 1000000) / 15000000;
		$rand2 = mt_rand(700000, 1000000) / 15000000;
		$rand3 = mt_rand(700000, 1000000) / 15000000;
		$rand4 = mt_rand(700000, 1000000) / 15000000;
		$rand5 = mt_rand(0, 3141592) / 1000000;
		$rand6 = mt_rand(0, 3141592) / 1000000;
		$rand7 = mt_rand(0, 3141592) / 1000000;
		$rand8 = mt_rand(0, 3141592) / 1000000;
		$rand9 = mt_rand(400, 600) / 100;
		$rand10 = mt_rand(400, 600) / 100;

		// 遍历每个像素点并根据波浪效果进行修改
		for ($x = 0; $x < $width; $x++) {
			for ($y = 0; $y < $height; $y++) {
				// 计算波浪变换后的像素坐标
				$sx = $x + (sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6)) * $rand9;
				$sy = $y + (sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8)) * $rand10;

				// 如果变换后的坐标超出图像边界
				if ($sx < 0 || $sy < 0 || $sx >= $width - 1 || $sy >= $height - 1) {
					$color = 255;
					$color_x = 255;
					$color_y = 255;
					$color_xy = 255;
				} else {
					// 获取原始像素和邻近像素的颜色值，用于抗锯齿处理
					$color = (imagecolorat($this->img, $sx, $sy) >> 16) & 0xFF;
					$color_x = (imagecolorat($this->img, $sx + 1, $sy) >> 16) & 0xFF;
					$color_y = (imagecolorat($this->img, $sx, $sy + 1) >> 16) & 0xFF;
					$color_xy = (imagecolorat($this->img, $sx + 1, $sy + 1) >> 16) & 0xFF;
				}

				// 如果颜色相同，则不进行平滑处理
				if ($color == $color_x && $color == $color_y && $color == $color_xy) {
					$newcolor = $color;
				} else {
					// 计算新像素颜色
					$frsx = $sx - floor($sx); // 计算原像素的偏差
					$frsy = $sy - floor($sy);
					$frsx1 = 1 - $frsx;
					$frsy1 = 1 - $frsy;
					// 计算新的颜色值
					$newcolor = floor($color * $frsx1 * $frsy1 +
						$color_x * $frsx * $frsy1 +
						$color_y * $frsx1 * $frsy +
						$color_xy * $frsx * $frsy);
				}
				// 设置新的像素点
				imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newcolor, $newcolor, $newcolor));
			}
		}

		// 更新图像为带有波浪效果的图像
		$this->img = $img2;
	}

	// 为验证码图像添加颜色化效果
	function colorize($value = 90) {
		if (function_exists('imagefilter'))
			imagefilter($this->img, IMG_FILTER_COLORIZE, mt_rand(0, $value), mt_rand(0, $value), mt_rand(0, $value));
	}

	// 输出验证码图像
	function output($q = 50) {
		ob_end_clean(); // 清除输出缓存
		if ($this->jpg) {
			header("Content-type: image/jpeg");
			imagejpeg($this->img, null, $q); // 输出JPG格式图像
		} elseif ($this->png) {
			header("Content-type: image/png");
			imagepng($this->img); // 输出PNG格式图像
		} elseif ($this->gif) {
			header("Content-type: image/gif");
			imagegif($this->img); // 输出GIF格式图像
		}
		exit;
	}
}


try {
	if (isset($_GET['captcha_token'])) {
		$captcha_code = decrypt_captcha_token($_GET['captcha_token']);
	} else {
		throw new Exception('无 captcha_token 参数');
	}
} catch(Exception $e) {
	session_name('SESS');
	session_start();
	// 随机生成5位数字
	for ($i = 0; $i < 5; $i++) {
		$captcha_code .= mt_rand(0, 9);
	}
	$_SESSION['captcha'] = $captcha_code;
}

$captcha = new captcha($captcha_code);
$captcha->create();
$captcha->MultiWave(); // 图像失真
$captcha->colorize();
$captcha->output();
