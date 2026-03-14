<?php
require_once '../../sys/inc/start.php';
require_once '../../sys/inc/compress.php';
require_once '../../sys/inc/sess.php';
require_once '../../sys/inc/home.php';
require_once '../../sys/inc/settings.php';
require_once '../../sys/inc/db_connect.php';
require_once '../../sys/inc/ipua.php';
require_once '../../sys/inc/fnc.php';
require_once '../../sys/inc/user.php';

/* 用户面板 */
if (isset($user) && dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'notes' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
    header('Location: /user/ban.php?' . session_id());
    exit;
}

$set['title'] = '日记';
function meta_note_rss($str) {
	return str_replace('</head>', '<link rel="alternate" title="日记 RSS" href="../rss/notes.php" type="application/rss+xml" />' . "</head>", $str); // 在<head>结束前插入meta描述
}
ob_start('meta_note_rss');
require_once '../../sys/inc/thead.php';
title();
aut(); // 授权形式


/*** 搜索框 ****/
echo "<div class='foot'><form method=\"get\" action=\"search.php\">";
echo "<table><td><input style='width:95%;' type=\"text\" name=\"go\" maxlength=\"16\" /></td><td> ";
echo "<input type=\"submit\" value=\"搜索\" /></td></table>";
echo "</form></div>";

/**** 导航面板 ****/
echo "<div id='comments' class='menus'>";
echo "<div class='webmenu'>";
echo "<a href='index.php' class='activ'>日记</a>";
echo "</div>";
echo "<div class='webmenu last'>";
echo "<a href='dir.php'>类别</a>";
echo "</div>";
if (isset($user)) {
    echo "<div class='webmenu last'>";
    echo "<a href='user.php?id=" . $user['id'] . "'>我的</a>";
    echo "</div>";
}
echo "</div>";

/**** 排序 ****/
$sortir = isset($_GET['sort']) ? $_GET['sort'] : NULL;
switch ($sortir) {
    case 't':
        $order = 'order by `time` desc';
        echo "<div class='foot'><b>新的</b> | <a href='?sort=c'>热门的</a></div>";
        break;
    case 'c':
        $order = 'order by `count` desc';
        echo "<div class='foot'><a href='?sort=t'>新的</a> | <b>热门的</b></div>";
        /* 按时间排序热门日记 */
        echo "<div class='nav2'>";
        if (isset($_GET['new']) && $_GET['new'] == 't') {
            echo "<b>最新的</b> | <a href='?sort=c&new=m'>每月</a> | <a href='?sort=c&new=v'>长期</a>";
            $new = " AND `time`>'" . (time() - 600) . "' ";
        } elseif (isset($_GET['new']) && $_GET['new'] == 'm') {
            echo "<a href='?sort=c&new=t'>新的</a> | <b>=每月</b> | <a href='?sort=c&new=v'>长期</a>";
            $new = " AND `time`>'" . (time() - 2592000) . "' ";
        } elseif (isset($_GET['new']) && $_GET['new'] == 'v') {
            echo "<a href='?sort=c&new=t'>新的</a> | <a href='?sort=c&new=m'>每月</a> | <b>长期</b>";
            $new = null;
        } elseif (isset($_GET['sort']) && $_GET['sort'] == 'c') {
            echo "<b>新的</b> | <a href='?sort=c&new=m'>每月</a> | <a href='?sort=c&new=v'>长期</a>";
            $new = " AND `time`>'" . (time() - 600) . "' ";
        } else {
            $new = null;
        }
        echo "</div>";
        /* 按时间排序热门日记 */
        break;
    default:
        $order = 'order by `time` desc';
        echo "<div class='foot'><b>新的</b> | <a href='?sort=c'>热门的</a></div>";
}

if (!isset($_GET['sort']) or $_GET['sort'] != 'c') {
    $new = null;
}

$k_post = $db->queryColumn('SELECT COUNT(*) FROM `notes`');
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];

echo "<table class='post'>";
if ($k_post == 0) {
    echo "<div class='mess'>没有日记</div>";
}

$q = dbquery("SELECT * FROM `notes` $order LIMIT $start, $set[p_str]");
while ($post = dbassoc($q)) {
	/*-----------代码-----------*/
	if ($num == 0) {
		echo "  <div class='nav1'>";
		$num = 1;
	} elseif ($num == 1) {
		echo "  <div class='nav2'>";
		$num = 0;
	}
	/*---------------------------*/
	if ($post['private'] == 0) {
		$allowViewNote = true;
	} else {
		if (isset($user)) {
			if ($post['private'] == 1) {
				$frend = dbresult(dbquery("SELECT COUNT(*) FROM `frends` WHERE (`user` = '{$user['id']}' AND `frend` = '{$post['id_user']}') OR (`user` = '{$post['id_user']}' AND `frend` = '{$user['id']}') LIMIT 1"), 0);
				if ($user['id'] == $post['id_user'] || $frend == 2  || user_access('notes_delete')) {
					$allowViewNote = true;
				} else {
					$allowViewNote = false;
				}
			} elseif ($post['private'] == 2 && ($user['id'] == $post['id_user'] || user_access('notes_delete'))) {
				$allowViewNote = true;
			} else {
				$allowViewNote = false;
			}
		} else {
			$allowViewNote = false;
		}
	}
	if ($post['id_user'] === NULL) {
		echo '???';
	} else {
		echo user::nick($post['id_user'], 1, 1, 0);
	}
	echo " : <a href='list.php?id=" . $post['id'] . "'>";
	if ($allowViewNote) {
		echo text($post['name']);
	} else {
		echo '[不可见]';
	}
	echo "</a>";
	echo '<span style="float:right;color:#666;">' . vremja($post['time']) . '</span><br/>';
	if ($allowViewNote) {
		echo rez_text($post['msg'], 80);
		echo " <br/>";
		notes_sh($post['id']);
		// 评论、收藏、分享图标
		echo "<br/><img src='../../style/icons/uv.png'> <font color=#666>(" . dbresult(dbquery("SELECT COUNT(`id`)FROM `notes_komm` WHERE `id_notes`='$post[id]'"), 0) . ") &bull;";
		echo " <a href='fav.php?id=" . $post['id'] . "'><img src='../../style/icons/add_fav.gif'> (" . dbresult(dbquery("SELECT COUNT(`id`)FROM `bookmarks` WHERE `id_object`='" . $post['id'] . "' AND `type`='notes'"), 0) . ")</a> &bull; ";
		echo " <img src='../../style/icons/action_share_color.gif'> (" . dbresult(dbquery("SELECT COUNT(`id`)FROM `notes` WHERE `share_id`='" . $post['id'] . "' AND `share_type`='notes'"), 0) . ") </font>";
	} elseif ($post['private'] == 1) {
		echo '<font color="#999">[内容仅好友可见]</font>';
	} else {
		echo '<font color="#999">[内容仅作者可见]</font>';
	}
	echo "  </div>";
}
echo "</table>";

if (isset($_GET['sort'])) {
    $dop = "sort=" . my_esc($_GET['sort']) . "&amp;";
} else {
    $dop = '';
}

if ($k_page > 1) str('?' . $dop . '', $k_page, $page); // 输出页数

if (isset($user)) echo "<div class='foot'><a href='add.php'> 写日记</a></div>";

require_once '../../sys/inc/tfoot.php';