<?
function medal($user = 0) {
	// 获取数据库中用户的 rating 值
	$ank = dbarray(dbquery("SELECT `rating` FROM `user` WHERE `id` = $user LIMIT 1"));
	
	// 检查查询结果是否为空
	if (!isset($ank['rating'])) {
		// 如果结果为空，直接返回空字符串或者做其他处理
		return '';
	}

	// 如果有 rating 值，继续进行逻辑判断
	$img = 0;
	if ($ank['rating'] >= 6 && $ank['rating'] <= 11)
		$img = 1;
	elseif ($ank['rating'] >= 12 && $ank['rating'] <= 19)
		$img = 2;
	elseif ($ank['rating'] >= 20 && $ank['rating'] <= 27)
		$img = 3;
	elseif ($ank['rating'] >= 28 && $ank['rating'] <= 37)
		$img = 4;
	elseif ($ank['rating'] >= 38 && $ank['rating'] <= 47)
		$img = 5;
	elseif ($ank['rating'] >= 48 && $ank['rating'] <= 59)
		$img = 6;
	elseif ($ank['rating'] >= 60 && $ank['rating'] <= 9999999)
		$img = 7;

	// 如果有匹配的图片编号，返回对应的图片标签
	if ($img != 0) {
		return ' <img src="/style/medal/' . $img . '.png" alt="DS" />';
	}

	return ''; // 如果没有符合的条件，返回空字符串
}