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

try {
	// 检查是否有Imagick扩展，没有就用GD
	$driverClass = extension_loaded('imagick') ? \Intervention\Image\Drivers\Imagick\Driver::class : \Intervention\Image\Drivers\Gd\Driver::class;
	$manager = new \Intervention\Image\ImageManager(new $driverClass());

	// 读取图片
	$image = $manager->read($file);

	// 自适应把宽缩到48
	$image->scale(width: 48, height: 48);

	// 编码并保存
	$image->toJpeg(90)->save(H . "files/down/screens/48/{$post['id']}.jpg");

	// 输出结果
	echo "<img src='{$set['siteurl']}/files/down/screens/48/{$post['id']}.jpg' alt='scr...' /><br />";

} catch (\Exception $e) {
	echo '喵呜... 又炸了：' . $e->getMessage();
}
