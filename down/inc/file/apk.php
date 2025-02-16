<?php
/**
 * 用于显示apk文件的图标
 * 此库在最新版本的apk文件中不再可用，因为 MANIFEST.MF 文件不再包含APK文件列表信息
 */

// 检查是否存在名为文件ID的PNG图标文件，如果不存在则执行后续操作
if (!test_file(H . "files/screens/128/{$file_id['id']}.png")) {
	// 如果没有找到图标，清除会话中存储的图标信息
	$_SESSION['file_icon'] = null;
	
	// 引入zip文件处理库
	include_once H . 'sys/inc/zip.php';
	
	// 创建一个PclZip实例，读取APK文件
	$zip = new PclZip($file);
	
	// 提取APK文件中的"MANIFEST.MF"文件的内容作为字符串
	$content = $zip->extract(PCLZIP_OPT_BY_NAME, "META-INF/MANIFEST.MF", PCLZIP_OPT_EXTRACT_AS_STRING);
	
	// 通过"Name: "分割文件内容，这样可以找到文件列表
	$i5 = explode('Name: ', $content[0]['content']);
	
	// 如果分割后的数组中有多个元素
	if (count($i5) > 1) {
		// 遍历所有的元素，查找包含"icon"的字段
		for ($i = 0; $i <= count($i5); $i++) {
			if (isset($i5[$i]) && !empty($i5[$i])) {
				// 使用"icon"作为分隔符，进一步拆分
				$i6 = explode('icon', $i5[$i]);
				
				// 检查分割后的结果，寻找符合条件的PNG图标路径
				for ($i2 = 0; $i2 <= count($i6); $i2++) {
					// 如果找到PNG图标文件路径并且格式正确，则将图标路径存入会话中
					if (count(explode('icon', $i5[$i])) > 1 && count(explode('.png', $i5[$i])) > 1) {
						$i5[$i] = preg_replace('#(png)(.*)(=)#isU', 'png', $i5[$i]);
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

		// 从会话中获取图标路径并确保其非空
		$icon = '' . $_SESSION['file_icon'];
		if ($icon == NULL) $icon = false;

		// 如果图标路径存在，则从APK文件中提取该图标并保存
		if ($icon) {
			// 提取图标内容
			$content = $zip->extract(PCLZIP_OPT_BY_NAME, $icon, PCLZIP_OPT_EXTRACT_AS_STRING);
			
			// 将图标内容写入临时文件
			$j = fopen(H."sys/tmp/{$sess}.png", 'w');
			fwrite($j, $content[0]['content']);
			fclose($j);
			
			// 设置临时文件的权限为可读写
			chmod(H . "sys/tmp/{$sess}.png", 0777);
			
			// 将临时文件复制到指定路径
			copy(H . "sys/tmp/{$sess}.png", H."files/screens/128/{$file_id['id']}.png");
			
			// 设置目标图标文件的权限
			chmod(H . "files/screens/128/$file_id[id].png", 0777);
			
			// 删除临时文件
			unlink(H . "sys/tmp/{$sess}.png");
		}

		// 清空会话中的图标路径
		$_SESSION['file_icon']=null;
	}
}

// 如果找到了图标文件，则显示该图标
if (is_file(H . "files/screens/128/{$file_id['id']}.png") && $file_id['ras']=='apk') {
	echo "<img src='/files/screens/128/{$file_id['id']}.png' alt='*' /><br />";
}

// 如果文件描述不为空，则显示文件描述
if ($file_id['opis'] != NULL) {
	echo "资料描述: ";
	echo output_text($file_id['opis']);
	echo "<br />";
}
