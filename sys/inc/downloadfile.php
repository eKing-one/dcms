<?php
function DownloadFile($filename, $name, $mimetype='application/octet-stream') {
	if (!file_exists($filename)) die('找不到文件');
	ob_end_clean();
	$from = 0;
	$size = filesize($filename);
	$to = $size;

	// 如果要求文件段
	if (isset($_SERVER['HTTP_RANGE'])) {
		if (preg_match('#bytes=-([0-9]*)#i', $_SERVER['HTTP_RANGE'], $range)) {					// 如果指定了文件末尾的段
			$from = $size - $range[1];
			$to = $size;
		} elseif (preg_match('#bytes=([0-9]*)-#i', $_SERVER['HTTP_RANGE'], $range)) {			// 如果仅指定了 start 标签
			$from = $range[1];
			$to = $size;
		} elseif (preg_match('#bytes=([0-9]*)-([0-9]*)#i', $_SERVER['HTTP_RANGE'], $range)) {	// 如果指定了文件段
			$from = $range[1];
			$to = $range[2];
		}
		header('HTTP/1.1 206 Partial Content');
		$cr = 'Content-Range: bytes ' . $from . '-' . $to . '/' . $size;
	} else {
		header('HTTP/1.1 200 Ok');
	}

	$etag = md5($filename);
	$etag = substr($etag, 0, 8) . '-' . substr($etag, 8, 7) . '-' . substr($etag, 15, 8);
	header('ETag: "' . $etag . '"');
	header('Accept-Ranges: bytes');
	header('Content-Length: ' . ($to - $from));
	if (isset($cr)) header($cr);
	header('Connection: close');
	header('Content-Type: ' . $mimetype);
	header('Last-Modified: ' . gmdate('r', filemtime($filename)));
	header("Last-Modified: " . gmdate("D, d M Y H:i:s", filemtime($filename)) . " GMT");
	header("Expires: " . gmdate("D, d M Y H:i:s", time() + 3600) . " GMT");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=\"" . $name . "\"");
	/*
	if (preg_match('#^image/#i', $mimetype)) {
		header('Content-Disposition: filename="' . $name . '";');
	} else {
		header('Content-Disposition: attachment; filename="' . $name . '";');
	}
	*/
	$f = fopen($filename, 'rb');
	fseek($f, $from, SEEK_SET);
	$size = $to;
	$downloaded = 0;
	while (!feof($f) and !connection_status() and ($downloaded < $size)) {
		$block = min(1024 * 8, $size - $downloaded);
		echo fread($f, $block);
		$downloaded += $block;
		flush();
	}
	fclose($f);
	exit();
}