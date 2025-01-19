<?php
class captcha
{
	var $str;
	var $x = 100;
	var $y = 40;
	var $img;
	var $gif = false;
	var $png = false;
	var $jpg = false;

	function __construct($str) {
		if (!function_exists('gd_info')) {
			header('Location: /style/errors/gd_err.gif');
			exit;
		}
		if (imagetypes() & IMG_PNG) $this->png = true;
		if (imagetypes() & IMG_GIF) $this->gif = true;
		if (imagetypes() & IMG_JPG) $this->jpg = true;
		$this->str = $str;
		$this->img = imagecreatetruecolor($this->x, $this->y);
		imagefill($this->img, 0, 0, imagecolorallocate($this->img, 255, 255, 255));
	}

	function create() {
		for ($i = 0; $i < 5; $i++) {
			$n = $this->str[$i];
			if ($this->png) $num[$n] = imagecreatefrompng(H . '/style/captcha/' . $n . '.png');
			elseif ($this->gif) $num[$n] = imagecreatefromgif(H . '/style/captcha/' . $n . '.gif');
			elseif ($this->jpg) $num[$n] = imagecreatefromjpeg(H . '/style/captcha/' . $n . '.jpg');
			imagecopy($this->img, $num[$n], $i * 15 + 10, 8, 0, 0, 15, 20);
		}
	}

	function MultiWave() {
		$width = imagesx($this->img);
		$height = imagesy($this->img);
		$img2 = imagecreatetruecolor($width, $height);
		$rand1 = mt_rand(700000, 1000000) / 15000000;
		$rand2 = mt_rand(700000, 1000000) / 15000000;
		$rand3 = mt_rand(700000, 1000000) / 15000000;
		$rand4 = mt_rand(700000, 1000000) / 15000000;
		// фазы
		$rand5 = mt_rand(0, 3141592) / 1000000;
		$rand6 = mt_rand(0, 3141592) / 1000000;
		$rand7 = mt_rand(0, 3141592) / 1000000;
		$rand8 = mt_rand(0, 3141592) / 1000000;
		// амплитуды
		$rand9 = mt_rand(400, 600) / 100;
		$rand10 = mt_rand(400, 600) / 100;

		for ($x = 0; $x < $width; $x++) {
			for ($y = 0; $y < $height; $y++) {
				// координаты пикселя-первообраза.
				$sx = $x + (sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6)) * $rand9;
				$sy = $y + (sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8)) * $rand10;

				// первообраз за пределами изображения
				if ($sx < 0 || $sy < 0 || $sx >= $width - 1 || $sy >= $height - 1) {
					$color = 255;
					$color_x = 255;
					$color_y = 255;
					$color_xy = 255;
				} else { // цвета основного пикселя и его 3-х соседей для лучшего антиалиасинга
					$color = (imagecolorat($this->img, $sx, $sy) >> 16) & 0xFF;
					$color_x = (imagecolorat($this->img, $sx + 1, $sy) >> 16) & 0xFF;
					$color_y = (imagecolorat($this->img, $sx, $sy + 1) >> 16) & 0xFF;
					$color_xy = (imagecolorat($this->img, $sx + 1, $sy + 1) >> 16) & 0xFF;
				}

				// сглаживаем только точки, цвета соседей которых отличается
				if ($color == $color_x && $color == $color_y && $color == $color_xy) {
					$newcolor = $color;
				} else {
					$frsx = $sx - floor($sx); //отклонение координат первообраза от целого
					$frsy = $sy - floor($sy);
					$frsx1 = 1 - $frsx;
					$frsy1 = 1 - $frsy;
					// вычисление цвета нового пикселя как пропорции от цвета основного пикселя и его соседей
					$newcolor = floor($color * $frsx1 * $frsy1 +
						$color_x * $frsx * $frsy1 +
						$color_y * $frsx1 * $frsy +
						$color_xy * $frsx * $frsy);
				}
				imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newcolor, $newcolor, $newcolor));
			}
		}

		$this->img = $img2;
	}

	function colorize($value = 90) {
		if (function_exists('imagefilter'))
			imagefilter($this->img, IMG_FILTER_COLORIZE, mt_rand(0, $value), mt_rand(0, $value), mt_rand(0, $value));
	}

	function output($q = 50) {
		ob_end_clean();
		if ($this->jpg) {
			header("Content-type: image/jpeg");
			imagejpeg($this->img, null, $q);
		} elseif ($this->png) {
			header("Content-type: image/png");
			imagepng($this->img);
		} elseif ($this->gif) {
			header("Content-type: image/gif");
			imagegif($this->img);
		}
		exit;
	}
}
