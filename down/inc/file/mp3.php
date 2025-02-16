<?php
if (test_file(H . "files/screens/128/$file_id[id].gif")) {
	echo "<img src='/files/screens/128/$file_id[id].gif' alt='屏幕...' /><br />";
}
$url =   '/down' . $dir_id['dir'] . $file_id['id'] . '.' . $file_id['ras'] . '';
?>
<figure>
	<audio controls src="<?= $url ?>">
		您的浏览器不支持在线收听
	</audio>
</figure>
<?php
if ($file_id['opis'] != NULL) {
	echo "资料描述: ";
	echo output_text($file_id['opis']);
	echo "<br />";
}
if (class_exists('ffmpeg_movie')) {
	$media = new ffmpeg_movie($file);
	if (intval($media->getDuration()) > 3599) {
		echo '' . intval($media->getDuration() / 3600) . ":" . date('s', fmod($media->getDuration() / 60, 60)) . ":" . date('s', fmod($media->getDuration(), 3600)) . "";
	} elseif (intval($media->getDuration()) > 59) {
		echo '' . intval($media->getDuration() / 60) . ":" . date('s', fmod($media->getDuration(), 60)) . "";
	} else {
		echo '' . intval($media->getDuration()) . " 秒";
	}
	echo " | " . ceil(($media->getBitRate()) / 1024) . " Kbps";
	if ($media->getAudioChannels() == 1) {
		echo "| 单声道";
	} else {
		echo "| 立体声";
	}
	echo ' | ' . $media->getAudioSampleRate() . " Hz";
	if (($media->getArtist()) <> "") {
		echo ' | ' . $media->getArtist();
	}
	if (($media->getGenre()) <> "") echo ' | ' . $media->getGenre();
} elseif (class_exists('getID3')) {			// 检查 getID3 是否已加载
	// 创建 getID3 实例
	$getID3 = new getID3();
	$file_info = $getID3->analyze($file);	// 分析 MP3 文件

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

	// 输出比特率（bitrate）
	if (isset($file_info['audio']['bitrate']) && $file_info['audio']['bitrate'] > 0) {
		echo ' | ' . round($file_info['audio']['bitrate'] / 1000) . " Kbps";
	}

	// 输出声道模式（mode）
	if (isset($file_info['audio']['channels']) && $file_info['audio']['channels'] > 0) {
		$channels = ($file_info['audio']['channels'] == 1) ? '单声道' : '立体声';
		echo ' | ' . $channels;
	}

	// 输出频率（frequency）
	if (isset($file_info['audio']['sample_rate']) && $file_info['audio']['sample_rate'] > 0) {
		echo ' | ' . $file_info['audio']['sample_rate'] . " Hz";
	}

	// 输出专辑名称（album）
	if (isset($file_info['tags']['id3v2']['album']) && !empty($file_info['tags']['id3v2']['album'][0])) {
		echo ' | ' . $file_info['tags']['id3v2']['album'][0];
	}

	// 输出艺术家（artists）
	if (isset($file_info['tags']['id3v2']['artist']) && !empty($file_info['tags']['id3v2']['artist'][0])) {
		echo ' | ' . $file_info['tags']['id3v2']['artist'][0];
	}

	// 输出音乐类型（genre）
	if (isset($file_info['tags']['id3v2']['genre']) && !empty($file_info['tags']['id3v2']['genre'][0])) {
		echo ', ' . $file_info['tags']['id3v2']['genre'][0];
	}
} else {
	include_once H . 'sys/inc/mp3.php';
	$id3 = new MP3_Id();
	$result = $id3->read($file);
	$result = $id3->study();
	if (($id3->getTag('length') <> 0)) {
		echo $id3->getTag('length');
	}
	if (($id3->getTag('bitrate')) <> 0) {
		echo ' | ' . $id3->getTag('bitrate') . " Kbps";
	}
	if (($id3->getTag('mode')) <> "") {
		echo ' | ' . $id3->getTag('mode') . "";
	}
	if (($id3->getTag('frequency')) <> 0) {
		echo ' | ' . $id3->getTag('frequency') . " Hz";
	}
	if (($id3->getTag('album')) <> "") {
		echo ' | ' . $id3->getTag('album');
	}
	if (($id3->getTag('artists')) <> "") {
		echo ' | ' . $id3->getTag('artists');
	}
	if (($id3->getTag('genre')) <> "") {
		echo ', ' . $id3->getTag('genre');
	}
}
// var_dump($id3);
