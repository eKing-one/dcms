<?php
/**
 *缩放
 *
 * 适用于 PNG、GIF 和 JPEG 图像。
 * 可以使用单面或双面指示，以百分比或像素进行缩放。
 *
 * @param string 源文件位置
 * @param string 目标文件位置
 * @param integer 最终文件的宽度
 * @param integer 最终文件的高度
 * @param bool 大小以滴数或百分比表示
 * @return bool
 */
function resize($file_input, $file_output, $w_o, $h_o, $percent = false) {
	list($w_i, $h_i, $type) = getimagesize($file_input);

	if (!$w_i || !$h_i) {
		echo '无法获取图像的长度和宽度';
		return;
	}

	$types = array('', 'gif', 'jpeg', 'png');
	$ext = $types[$type];
	if ($ext) {
		$func = 'imagecreatefrom' . $ext;
		$img = $func($file_input);
	} else {
		echo '文件格式不正确';
		return;
	}
	if ($percent) {
		$w_o *= $w_i / 100;
		$h_o *= $h_i / 100;
	}
	if (!$h_o) $h_o = $w_o / ($w_i / $h_i);
	if (!$w_o) $w_o = $h_o / ($h_i / $w_i);
	$img_o = imagecreatetruecolor($w_o, $h_o);
	imagecopyresampled($img_o, $img, 0, 0, 0, 0, $w_o, $h_o, $w_i, $h_i);

	if ($type == 2) {
		return imagejpeg($img_o, $file_output, 100);
	} else {
		$func = 'image' . $ext;
		return $func($img_o, $file_output);
	}
}

/**
 * 图像裁剪
 *
 * 适用于 PNG、GIF 和 JPEG 图像。
 * 修剪既要用绝对长度表示，也可以用相对（负）来表示。
 *
 * @param string 源文件位置
 * @param string 目标文件位置
 * @param array 裁剪坐标
 * @param bool 尺寸以滴数或百分比表示
 * @return bool
 */
function crop($file_input, $file_output, $crop = 'square',$percent = false) {

	list($w_i, $h_i, $type) = getimagesize($file_input);

	if (!$w_i || !$h_i) {
		echo '无法获取图像的长度和宽度';
		return;
	}

	$types = array('','gif','jpeg','png');
	$ext = $types[$type];
	if ($ext) {
		$func = 'imagecreatefrom'.$ext;
		$img = $func($file_input);
	} else {
		echo '文件格式不正确';
		return;
	}

	if ($crop == 'square') {
		$min = $w_i;
		if ($w_i > $h_i) $min = $h_i;
		$w_o = $h_o = $min;
	} else {
		list($x_o, $y_o, $w_o, $h_o) = $crop;
		if ($percent) {
			$w_o *= $w_i / 100;
			$h_o *= $h_i / 100;
			$x_o *= $w_i / 100;
			$y_o *= $h_i / 100;
		}

		if ($w_o < 0) $w_o += $w_i;
		$w_o -= $x_o;
	   	if ($h_o < 0) $h_o += $h_i;
		$h_o -= $y_o;
	}

	$img_o = imagecreatetruecolor($w_o, $h_o);
	imagecopy($img_o, $img, 0, 0, $x_o, $y_o, $w_o, $h_o);

	if ($type == 2) {
		return imagejpeg($img_o, $file_output, 100);
	} else {
		$func = 'image' . $ext;
		return $func($img_o, $file_output);
	}
}
