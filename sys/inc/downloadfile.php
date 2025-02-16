<?php
function DownloadFile($filename, $name, $mimetype = NULL) {
	// 检查文件
	if (!file_exists($filename)) {
		http_response_code(404);
		die('找不到文件');
	}

	ob_end_clean();
	$size = filesize($filename);
	$from = 0;
	$to = $size - 1; // Default to the last byte of the file
	$fileMd5 = md5_file($filename);

	// 处理Range请求
	if (isset($_SERVER['HTTP_RANGE'])) {
		if (preg_match('#bytes=([0-9]+)-([0-9]+)#i', $_SERVER['HTTP_RANGE'], $range)) {
			if ($range[1] > $to || $range[2] > $to) {
				http_response_code(416);
				exit;
			}
			$from = $range[1];
			$to = $range[2];
		} elseif (preg_match('#bytes=([0-9]+)-#i', $_SERVER['HTTP_RANGE'], $range)) {
			if ($range[1] > $to) {
				http_response_code(416);
				exit;
			}
			$from = $range[1];
		} elseif (preg_match('#bytes=-([0-9]+)#i', $_SERVER['HTTP_RANGE'], $range)) {
			if ($range[1] > $to) {
				http_response_code(416);
				exit;
			}
			$from = $size - $range[1];
		}
		http_response_code(206);
		header('Content-Range: bytes ' . $from . '-' . $to . '/' . $size);
	}

	// 设置ETag和其他头信息
	header('ETag: "' . $fileMd5 . '"');
	header('Accept-Ranges: bytes');
	header('Content-Length: ' . ($to - $from + 1));
	if ($mimetype) header('Content-Type: ' . $mimetype);
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filename)) . ' GMT');
	header('Content-Description: File Transfer');
	header('Content-Disposition: attachment; filename="' . rawurlencode($name) . '"');

	// 检查浏览器是否提供了有效的缓存
	if ((isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($filename)) ||
		(isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $fileMd5)) {
		http_response_code(304);
		exit;
	}

	// 打开文件并读取内容
	$f = fopen($filename, 'rb');
	if (fseek($f, $from, SEEK_SET) !== 0) {
		http_response_code(500);
		error_log('文件读取失败: ' . $filename);
		exit;
	}

	// 启动下载，分块传输
	$downloaded = 0;
	while (!feof($f) && connection_status() === CONNECTION_NORMAL && ($downloaded < ($to - $from + 1))) {
		$block = min(1024 * 8, ($to - $from + 1) - $downloaded);
		echo fread($f, $block);
		$downloaded += $block;
		flush();
	}

	fclose($f);
	exit;
}