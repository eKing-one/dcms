<?
// 活动评分和积分
dbquery("UPDATE `user` SET `balls` = '" . ($user['balls'] + 1) . "', `rating_tmp` = '" . ($user['rating_tmp'] + 1) . "' WHERE `id` = '$user[id]' LIMIT 1");
?>