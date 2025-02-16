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
} elseif (class_exists('getID3')) {	// 检查 getID3 库是否存在
	$getID3 = new getID3();
	$file_info = $getID3->analyze($file);  // 分析 MP3 文件

	// 输出音频的时长（长度）按不同格式显示
	if (isset($file_info['playtime_seconds']) && $file_info['playtime_seconds'] > 0) {
		$seconds = round($file_info['playtime_seconds']);

		// 如果小于一分钟
		if ($seconds < 60) {
			echo $seconds . "s";
		}
		// 如果大于一分钟但小于一小时
		elseif ($seconds < 3600) {
			$minutes = floor($seconds / 60);
			$seconds = $seconds % 60;
			echo $minutes . ":" . str_pad($seconds, 2, '0', STR_PAD_LEFT);
		}
		// 如果大于一小时
		else {
			$hours = floor($seconds / 3600);
			$minutes = floor(($seconds % 3600) / 60);
			$seconds = $seconds % 60;
			echo $hours . ":" . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ":" . str_pad($seconds, 2, '0', STR_PAD_LEFT);
		}
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
	if(($id3->getTag('length') <> 0)){echo '时长: ' . $id3->getTag('length') . "<br />";}
	if(($id3->getTag('bitrate')) <> 0){echo'比特率: ' . $id3->getTag('bitrate') . " Kbps<br />";}
	dbquery("INSERT INTO `media_info` (`file`, `size`, `lenght`, `bit`, `codec`) values('" . my_esc($jfile) . "', '$size', '" . $id3->getTag('length') . "', '" . $id3->getTag('bitrate') . "', 'mp3')");
}
