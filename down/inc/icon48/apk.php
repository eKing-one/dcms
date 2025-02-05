<?php
/**
 * 用于显示apk文件的图标
 * 此库在最新版本的apk文件中不再可用，因为 MANIFEST.MF 文件不再包含APK文件列表信息
 */

// 检查是否已经存在图标文件，若不存在则进行图标提取
if (!test_file(H . "files/screens/48/{$post['id']}.png")) {
	// 如果没有图标文件，清除 session 中的图标记录
	$_SESSION['file_icon'] = null;

	// 包含必要的 zip 解压库
	include_once H . 'sys/inc/zip.php';

	// 创建一个 PclZip 对象，用于操作 APK 文件
	$zip = new PclZip($file);

	// 提取 APK 中的 MANIFEST.MF 文件内容
	$content = $zip->extract(PCLZIP_OPT_BY_NAME, 'META-INF/MANIFEST.MF', PCLZIP_OPT_EXTRACT_AS_STRING);

	// 将 MANIFEST.MF 内容按 'Name: ' 分割，获取可能的文件名列表
	$i5 = explode('Name: ', $content[0]['content']);

	// 如果分割后有多个文件名，则开始检查每个文件
	if (count($i5) > 1) {
		for ($i = 0; $i <= count($i5); $i++) {
			// 如果当前文件名不为空，检查是否包含图标信息
			if (isset($i5[$i]) && !empty($i5[$i])) {
				// 按照 'icon' 字段分割，查找图标路径
				$i6 = explode('icon', $i5[$i]);

				// 遍历每个文件部分
				for ($i2 = 0; $i2 <= count($i6); $i2++) {
					// 如果当前部分包含有效的图标信息（.png 文件）
					if (count(explode('icon', $i5[$i])) > 1 && count(explode('.png', $i5[$i])) > 1) {
						// 正则替换，提取有效的 PNG 图标路径
						$i5[$i] = preg_replace('#(png)(.*)(=)#isU', 'png', $i5[$i]);
						// 保存图标路径到 session
						$_SESSION['file_icon'] = trim($i5[$i]);
					}
				}
			}
		}

		/*
		// 如果没有从 MANIFEST.MF 文件中提取到图标路径，再尝试其他方式查找图标
		if ($_SESSION['file_icon'] == null) {
			for ($i0 = 0; $i0 <= count($i5); $i0++) {
				// 这一行似乎在尝试对字符串进一步处理，但它存在逻辑错误，可能会导致无法正确执行
				$i60 = explode('', $i5[$i0]); // 空字符的分割是无效的，应该有实际的分隔符
				for ($i20 = 0; $i20 <= count($i60); $i20++) {
					// 同样检查并提取 PNG 图标路径
					if (count(explode('', $i5[$i0])) > 1 && count(explode('.png', $i5[$i0])) > 1) {
						$i5[$i0] = preg_replace('#(png)(.*)(=)#isU', 'png', $i5[$i0]);
						$_SESSION['file_icon'] = trim($i5[$i0]);
					}
				}
			}
		}
		*/

		// 如果图标路径已获取
		$icon = '' . $_SESSION['file_icon'];
		if ($icon == NULL) $icon = false;

		// 如果存在有效的图标路径，开始下载图标并保存
		if ($icon) {
			// 从 APK 中提取图标文件
			$content = $zip->extract(PCLZIP_OPT_BY_NAME, $icon, PCLZIP_OPT_EXTRACT_AS_STRING);

			// 将提取的图标内容写入临时文件
			$j = fopen(H . "sys/tmp/{$sess}.png", 'w');
			fwrite($j, $content[0]['content']);
			fclose($j);

			// 设置文件权限
			chmod(H . "sys/tmp/{$sess}.png", 0777);

			// 复制图标文件到目标位置
			copy(H . "sys/tmp/{$sess}.png", H . "files/screens/48/{$post['id']}.png");

			// 调整图标尺寸为 50x50 并保存
			resize(H . "files/screens/48/{$post['id']}.png", H . "files/screens/48/{$post['id']}.png", 50, 50);
			chmod(H . "files/screens/48/{$post['id']}.png", 0777);

			// 删除临时图标文件
			unlink(H . "sys/tmp/{$sess}.png");
		}

		// 清空 session 中的图标记录
		$_SESSION['file_icon'] = null;
	}
}

// 如果图标文件已经存在，则直接显示该图标
if (test_file(H . "files/screens/48/{$post['id']}.png")) {
	echo "<img src='/files/screens/48/{$post['id']}.png' alt='*' /><br />";
}

// 清空 session 中的图标记录（即使已经显示了图标）
$_SESSION['file_icon'] = null;