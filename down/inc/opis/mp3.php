<?php
echo '大小: ' . size_file($size) . "<br />";
$jfile = $name;
$media = dbassoc(dbquery("SELECT * FROM `media_info` WHERE `file` = '" . my_esc($jfile) . "' AND `size` = '$size' LIMIT 1"));
if ($media != NULL) {
	echo '时长: ' . $media['lenght'] . "<br />";
	echo "比特率: " . $media['bit'] . " Kbps<br />";
} elseif (class_exists('ffmpeg_movie')) {
	$media = new ffmpeg_movie($file);
	if (intval($media->getDuration()) > 3599) {
		echo '时长: ' . intval($media->getDuration() / 3600) . ":" . date('s', fmod($media->getDuration() / 60, 60)) . ":" . date('s', fmod($media->getDuration(), 3600)) . "<br />";
	} elseif (intval($media->getDuration()) > 59) {
		echo '时长: ' . intval($media->getDuration() / 60) . ":" . date('s', fmod($media->getDuration(), 60)) . "<br />";
	} else {
		echo '时长: ' . intval($media->getDuration()) . " 秒<br />";
	}
	echo "比特率: " . ceil(($media->getBitRate()) / 1024) . " Kbps<br />";
	if (intval($media->getDuration()) > 3599) dbquery("INSERT INTO `media_info` (`file`, `size`, `lenght`, `bit`, `codec`) values('" . my_esc($jfile) . "', '$size', '" . intval($media->getDuration() / 3600) . ":" . date('s', fmod($media->getDuration() / 60, 60)) . ":" . date('s', fmod($media->getDuration(), 3600)) . "', '" . ceil(($media->getBitRate()) / 1024) . "', 'mp3')");
	if (intval($media->getDuration()) > 59) {
		dbquery("INSERT INTO `media_info` (`file`, `size`, `lenght`, `bit`, `codec`) values('" . my_esc($jfile) . "', '$size', '" . intval($media->getDuration() / 60) . ":" . date('s', fmod($media->getDuration(), 60)) . "', '" . ceil(($media->getBitRate()) / 1024) . "', 'mp3')");
	} else {
		dbquery("INSERT INTO `media_info` (`file`, `size`, `lenght`, `bit`, `codec`) values('" . my_esc($jfile) . "', '$size', '" . intval($media->getDuration()) . " 秒', '" . ceil(($media->getBitRate()) / 1024) . "', 'mp3')");
	}
} else {
	// 检查 getID3 库是否存在
	if (class_exists('getID3')) {
		$getID3 = new getID3();
		$file_info = $getID3->analyze($file);  // 分析 MP3 文件

		// 输出时长
		if (isset($file_info['playtime_seconds']) && $file_info['playtime_seconds'] > 0) {
			echo '时长: ' . round($file_info['playtime_seconds']) . " 秒<br />";
		}

		// 输出比特率
		if (isset($file_info['audio']['bitrate']) && $file_info['audio']['bitrate'] > 0) {
			echo '比特率: ' . round($file_info['audio']['bitrate'] / 1000) . " Kbps<br />";
		}

		dbquery("INSERT INTO `media_info` (`file`, `size`, `lenght`, `bit`, `codec`) values('" . my_esc($jfile) . "', '$size', '" . round($file_info['playtime_seconds']) . " 秒', '" . round($file_info['audio']['bitrate'] / 1000) . "', 'mp3')");
	} else {
		include_once H . 'sys/inc/mp3.php';
		$id3 = new MP3_Id();
		$result = $id3->read($file);
		$result = $id3->study();
		if(($id3->getTag('length') <> 0)){echo 'Время: ' . $id3->getTag('length') . "<br />";}
		if(($id3->getTag('bitrate')) <> 0){echo'Битрейт: ' . $id3->getTag('bitrate') . " Kbps<br />";}
		dbquery("INSERT INTO `media_info` (`file`, `size`, `lenght`, `bit`, `codec`) values('" . my_esc($jfile) . "', '$size', '" . $id3->getTag('length') . "', '" . $id3->getTag('bitrate') . "', 'mp3')");
	}
}