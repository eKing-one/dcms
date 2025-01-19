<?php
define('H', $_SERVER['DOCUMENT_ROOT'] . '/');
session_name('SESS');
session_start();
$show_all = true; //为大家展示, 否则无法完成注册。
$_SESSION['captcha'] = '';

// 生成代码
for ($i = 0; $i < 5; $i++) {
	$_SESSION['captcha'] .= mt_rand(0, 9);
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

$captcha = new captcha($_SESSION['captcha']);
$captcha->create();
$captcha->MultiWave(); // 图像失真
$captcha->colorize();
$captcha->output();
