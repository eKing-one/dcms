<?php
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';

/* 用户封禁 */
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'files' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
	header('Location: ../ban.php?' . session_id());
	exit;
}

include_once '../../sys/inc/thead.php';

if (isset($user)) $ank['id'] = $user['id'];
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
// 播放列表作者的 ID
$ank = user::get_user($ank['id']);

// 如果列表作者被封禁
if (!$ank) {
	header("Location: ../../index.php?" . session_id());
	include_once '../../sys/inc/tfoot.php';
}

$set['title'] = '音乐 ' . $ank['nick'];
title();
aut();
?>
<style>
	#ajaxsPlayer {
		margin: auto;
	}

	.button {
		float: left;
	}

	.play {
		width: 20px;
		height: 20px;
		background-image: url(../../style/icons/play.png);
		display: block;
		cursor: pointer;
		margin: 2px;
	}

	.pause {
		width: 20px;
		height: 20px;
		background-image: url(../../style/icons/pause.png);
		display: block;
		cursor: pointer;
		display: none;
		margin: 2px;
	}

	.nameTrack {
		font: 14px/90% Helvetica, 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
		color: #666666;
		padding: 5px 30px;
		vertical-align: middle;
		width: 90%;
	}

	.clear {
		clear: both;
	}
</style>
<script type="text/javascript" src="../../assets/js/jquery/jquery-1.5.1.min.js"></script>
<script type="text/javascript" src="../../assets/js/user-music.js"></script>
<?php

echo '<div id="ajaxsPlayer">';

echo '<div class="foot"><img src="../../style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'], 1, 0, 0) . ' | <b>音乐</b></div>';

if ($set['web']) $set['p_str'] = 100;
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `user_music` WHERE `id_user` = '$ank[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];

if ($k_post == 0) {
	echo '<div class="mess">播放列表中没有曲目</div>';
}

$track = 0;
$q = dbquery("SELECT * FROM `user_music` WHERE `id_user` = '$ank[id]' ORDER BY `id` DESC LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	$mp3 = dbassoc(dbquery("SELECT * FROM `downnik_files` WHERE `id` = '$post[id_file]' LIMIT 1"));
	$dir = dbassoc(dbquery("SELECT * FROM `downnik_dir` WHERE `id` = '$mp3[id_dir]' LIMIT 1"));
	$ras = $mp3['ras'];
	/*-----------代码-----------*/
	if ($num == 0) {
		echo "  <div class='nav1'>";
		$num = 1;
	} elseif ($num == 1) {
		echo "  <div class='nav2'>";
		$num = 0;
	}
	/*---------------------------*/
	if ($webbrowser == 'web') {
		echo '<div class="track">';
		echo '<div class="button">';
		echo '<div class="play" id="' . $track . '" file="../../down' . $dir['dir'] . '/' . $mp3['id'] . '.' . $ras . '"></div>';
		echo '<div class="pause"></div>';
		echo '</div>';
		echo '<div class="nameTrack"><a href="../../down' . $dir['dir'] . $mp3['id'] . '.' . $ras . '"><img src="../../style/icons/d.gif" alt="*" title="下载曲目"></a> ' . htmlspecialchars($mp3['name']) . ' (' . size_file($mp3['size']) . ')</div>';
		echo '<div class="clear"></div>';
		echo '</div>';
	} else {
		echo '<a href="../../down' . $dir['dir'] . $mp3['id'] . '.' . $ras . '"><img src="../../style/icons/d.gif" alt="*" title="下载曲目"></a> ' . htmlspecialchars($mp3['name']) . ' (' . size_file($mp3['size']) . ')';
	}
	echo '</div>';
	$track++;
}
echo '</div>';

if ($k_page > 1) str('index.php?id=' . $ank['id'] . '&amp;', $k_page, $page); // 输出页数

echo '<div class="foot"><img src="../../style/icons/str2.gif" alt="*"> ' . user::nick($ank['id'], 1, 0, 0) . '</a> | <b>音乐</b></div>';

include_once '../../sys/inc/tfoot.php';