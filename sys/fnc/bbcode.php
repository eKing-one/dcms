<?php

function bbcodehightlight($arr) {
	return '<div class="cit" style="overflow:scroll;clip:auto;max-width:480px;">' . preg_replace('#<code>(.*?)</code>#si', '\\1', highlight_string($arr[0], 1)) . '</div>' . "\n";
}

function bbcodeplayvideo($data){
	$url = $data[1];
	$iframeUrl = '';
	if (preg_match('#\.youku\.com/.*/id_([a-zA-Z0-9=]+)#', $url, $arr)) {			//优酷
		$iframeUrl = 'https://player.youku.com/embed/' . $arr[1];
	} else if (preg_match('#kg.*\.qq\.com/.*\bs=([a-zA-Z0-9=]+)#', $url, $arr)) {	// 全民K歌
		$iframeUrl = 'https://kg.qq.com/node/play?s=' . $arr[1];
	} else if (preg_match('#\.qq\.com/.*/([a-zA-Z0-9=]+)#', $url, $arr)) {			//腾讯视频
		$iframeUrl = 'https://v.qq.com/txp/iframe/player.html?vid=' . $arr[1];
	} else if (preg_match('#\b(?:bilibili\.com|b23\.tv)\b.*\bav(\d+)(?:.*\bp=(\d+))?#', $url, $arr)) {		//哔哩哔哩 av号
		$iframeUrl = "https://player.bilibili.com/player.html?aid={$arr[1]}&page={$arr[2]}";
	} else if (preg_match('#\b(?:bilibili\.com|b23\.tv)\b.*\b(BV[\w]+)(?:.*\bp=(\d+))?#', $url, $arr)) {	//哔哩哔哩 BV号
		$iframeUrl = "https://player.bilibili.com/player.html?bvid={$arr[1]}&page={$arr[2]}";
	}
	if (empty($iframeUrl)) {
		return "视频解析错误";
	} else {
		return '<a target="_blank" href="' . $url . '">视频链接</a><br/><iframe src="' . $iframeUrl . '" seamless allowfullscreen sandbox="allow-scripts allow-forms allow-same-origin allow-popups"><a href="' . $url . '">' . $url . '</a></iframe>';
	}
}

# 处理 [img] 标签的内容
function img_preg($arr) {
	global $set;
	if (preg_match('#^http://' . preg_quote($_SERVER['HTTP_HOST']) . '#', $arr[1]) || !preg_match('#://#', $arr[1]) || $set['bb_external_img'] == '1') {
		if (true) {	// 意义不明而且毫无作用的判断
			return '<img decoding=async class="bbcode-img" src="' . $arr[1] . '" alt="img" />';
		} else {
			return '<img class="bbcode-img" src="/style/no_image.png" alt="No Image" />';
		}
	} else {
		return '<a target="_blank" rel="nofollow" href="' . $arr[1] . '">外部站点的图像</a>';
	}
}

function BBcode($msg) {
	global $set;
	$bbcode = array();
	$bbcode['/\[br\]/isU'] = '<br />';
	if ($set['bb_i']) $bbcode['/\[i\](.+)\[\/i\]/isU'] = '<em>$1</em>';
	if ($set['bb_c']) $bbcode['/\[c\](.+)\[\/c\]/isU'] = '<div style="text-align: center;">$1</div>';
	if ($set['bb_b']) $bbcode['/\[b\](.+)\[\/b\]/isU'] = '<strong>$1</strong>';
	if ($set['bb_u']) $bbcode['/\[u\](.+)\[\/u\]/isU'] = '<span style="text-decoration:underline;">$1</span>';
	if ($set['bb_big']) $bbcode['/\[big\](.+)\[\/big\]/isU'] = '<span style="font-size:large;">$1</span>';
	if ($set['bb_small']) $bbcode['/\[small\](.+)\[\/small\]/isU'] = '<span style="font-size:small;">$1</span>';
	if ($set['bb_red']) $bbcode['/\[red\](.+)\[\/red\]/isU'] = '<span style="color:#ff0000;">$1</span>';
	if ($set['bb_yellow']) $bbcode['/\[yellow\](.+)\[\/yellow\]/isU'] = '<span style="color:#ffff22;">$1</span>';
	if ($set['bb_green']) $bbcode['/\[green\](.+)\[\/green\]/isU'] = '<span style="color:#00bb00;">$1</span>';
	if ($set['bb_blue']) $bbcode['/\[blue\](.+)\[\/blue\]/isU'] = '<span style="color:#0000bb;">$1</span>';
	if ($set['bb_white']) $bbcode['/\[white\](.+)\[\/white\]/isU'] = '<span style="color:#ffffff;">$1</span>';
	if ($set['bb_size']) $bbcode['/\[size=([0-9]+)\](.+)\[\/size\]/isU'] = '<span style="font-size:$1px;">$2</span>';
	if ($set['bb_img']) $msg = preg_replace_callback('/\[img\]((?!javascript:|data:|document.cookie).+)\[\/img\]/isU', 'img_preg', $msg);
	if (count($bbcode)) $msg = preg_replace(array_keys($bbcode), array_values($bbcode), $msg);

	if ($set['bb_code']) {
		$msg = preg_replace_callback('#&lt;\?(.*?)\?&gt;#sui', 'bbcodehightlight', $msg);
		$msg = preg_replace('#\[code\](.*?)\[/code\]#si', '\1', $msg);
	}
	$msg = preg_replace_callback('#\[video\](.*?)\[/video\]#', 'bbcodeplayvideo', $msg);
	return $msg;
}