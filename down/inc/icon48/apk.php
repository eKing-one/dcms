<?php
/**
 * 用于显示apk文件的图标
 * 此库在最新版本的apk文件中不再可用，因为 MANIFEST.MF 文件不再包含APK文件列表信息
 */

if (!test_file(H . "files/screens/48/{$post['id']}.png")) {
	$_SESSION['file_icon'] = null;
	include_once H . 'sys/inc/zip.php';
	$zip=new PclZip($file);
	$content = $zip->extract(PCLZIP_OPT_BY_NAME, "META-INF/MANIFEST.MF" ,PCLZIP_OPT_EXTRACT_AS_STRING);
	$i5 = explode('Name: ', $content[0]['content']);
	if (count($i5) > 1) {
		for ($i = 0; $i <= count($i5); $i++) {
			if (isset($i5[$i]) && !empty($i5[$i])) {
				$i6 = explode('icon', $i5[$i]);
				for ($i2 = 0; $i2 <= count($i6); $i2++) {
					if (count(explode('icon', $i5[$i])) > 1 && count(explode('.png', $i5[$i])) > 1) {
						$i5[$i] = preg_replace('#(png)(.*)(=)#isU', 'png', $i5[$i]);
						$_SESSION['file_icon'] = trim($i5[$i]);
					}
				}
			}
		}
		if ($_SESSION['file_icon']==null) {
			for ($i0 = 0; $i0 <= count($i5); $i0++) {
				$i60 = explode('', $i5[$i0]);
				for ($i20 = 0; $i20 <= count($i60); $i20++) {
					if (count(explode('', $i5[$i0])) > 1 && count(explode('.png', $i5[$i0])) > 1) {
						$i5[$i0] = preg_replace('#(png)(.*)(=)#isU', 'png', $i5[$i0]);
						$_SESSION['file_icon'] = trim($i5[$i0]);
					}
				}
			}
		}
		$icon = '' . $_SESSION['file_icon'];
		if ($icon == NULL) $icon = false;
		if ($icon) {
			$content = $zip->extract(PCLZIP_OPT_BY_NAME, $icon, PCLZIP_OPT_EXTRACT_AS_STRING);
			$j=fopen(H . "sys/tmp/{$sess}.png", 'w');
			fwrite($j, $content[0]['content']);
			fclose($j);
			chmod(H . "sys/tmp/{$sess}.png", 0777);
			copy(H . "sys/tmp/{$sess}.png", H."files/screens/48/{$post['id']}.png");
			resize(H . "files/screens/48/{$post['id']}.png", H."files/screens/48/{$post['id']}.png", 50, 50);
			chmod(H . "files/screens/48/{$post['id']}.png", 0777);
			unlink(H . "sys/tmp/{$sess}.png");
		}
		$_SESSION['file_icon'] = null;
	}
}
if (test_file(H . "files/screens/48/{$post['id']}.png")) echo "<img src='/files/screens/48/{$post['id']}.png' alt='*' /><br />";
$_SESSION['file_icon'] = null;
