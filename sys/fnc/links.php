<?php
# 处理 [img] 标签的内容
function img_preg($arr) {
	global $set;
	if (preg_match('#^http://' . preg_quote($_SERVER['HTTP_HOST']) . '#', $arr[1]) || !preg_match('#://#', $arr[1]) || $set['bb_external_img'] == '1') {
		if (preg_match('/\.(?:jp(?:e?g|e|2)|gif|png|tiff?|bmp|ico)$/i', $arr[1])) {
			if (true) {
				return '<a href="https://' . $_SERVER['HTTP_HOST'] . '/go.php?go=' . base64_encode(html_entity_decode($arr[1])) . '"><img decoding=async style="max-width:240px; max-height:320px;" src="' . text($arr[1]) . '" alt="img" /></a>';
			}
		} else {
			return '<img style="max-width:240px;" src="/style/no_image.png" alt="No Image" />';
		}
	} else {
		return '<a target="_blank" href="http://' . $_SERVER['HTTP_HOST'] . '/go.php?go=' . base64_encode(html_entity_decode($arr[1])) . '">外部站点的图像</a>';
	}
}

function links_preg1($arr) {
	global $set;
	if (preg_match('#^https://' . preg_quote($_SERVER['HTTP_HOST']) . '#', $arr[1]) || !preg_match('#://#', $arr[1])) {
		return '<a href="' . $arr[1] . '">' . $arr[2] . '</a>';
	} else {
		return '<a' . ($set['web'] ? ' target="_blank"' : null) . ' href="http://' . $_SERVER['HTTP_HOST'] . '/go.php?go=' . base64_encode(html_entity_decode($arr[1])) . '">' . $arr[2] . '</a>';
	}
}

function links_preg2($arr) {
	global $set;
	if (preg_match('#^https://' . preg_quote($_SERVER['HTTP_HOST']) . '#', $arr[2])) {
		return $arr[1] . '<a href="' . $arr[2] . '">' . $arr[2] . '</a>' . $arr[4];
	}
	else if (preg_match('#^http://' . preg_quote($_SERVER['HTTP_HOST']) . '#', $arr[2])) {
		return $arr[1] . '<a href="' . $arr[2] . '">' . $arr[2] . '</a>' . $arr[4];
	} else {
		 return $arr[1] . '<a' . ($set['web'] ? ' target="_blank"' : null) . ' href="http://' . $_SERVER['HTTP_HOST'] . '/go.php?go=' . base64_encode(html_entity_decode($arr[2])) . '">外部链接</a>' . $arr[4];
	}
}

function links($msg) {
	global $set;
	if ($set['bb_img']) $msg = preg_replace_callback('/\[img\]((?!javascript:|data:|document.cookie).+)\[\/img\]/isU', 'img_preg', $msg);
	if ($set['bb_url']) $msg = preg_replace_callback('/\[url=((?!javascript:|data:|document.cookie).+)\](.+)\[\/url\]/isU', 'links_preg1', $msg);
	if ($set['bb_http']) $msg = preg_replace_callback('~(^|\s)([a-z]+://([^ \r\n\t`\'"]+))(\s|$)~iu', 'links_preg2', $msg);
	return $msg;
}
