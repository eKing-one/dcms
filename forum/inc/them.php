<?php

//重点修改
if (isset($_GET['act']) && $_GET['act'] == 'txt') {
    ob_clean();
    ob_implicit_flush();
    header('Content-Type: text/plain; charset=utf-8', true);
    header('Content-Disposition: attachment; filename="' . retranslit($them['name']) . '.txt";');
    echo "主题: $them[name] ($forum[name]/$razdel[name])\r";
    $q = dbquery("SELECT * FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' ORDER BY `time` ASC");
    //echo "\r";
    while ($post = dbassoc($q)) {
        echo "\r";
        $ank = user::get_user($post['id_user']);
        echo "$ank[nick] (" . date("Y m d H:i", $post['time']) . ")\r";
        if ($post['cit'] != NULL && dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id` = '$post[cit]'"), 0) == 1) {
            $cit = dbassoc(dbquery("SELECT * FROM `forum_p` WHERE `id` = '$post[cit]' LIMIT 1"));
            $ank_c = user::get_user($cit['id_user']);
            echo "--报价--\r";
            echo "$ank_c[nick] (" . date("Y m d в H:i", $cit['time']) . "):\r";
            echo trim(br($cit['msg'], "\r")) . "\r";
            echo "----------\r";
        }
        echo trim(br($post['msg'], "\r")) . "\r";
    }
    echo "\r来源: http://$_SERVER[SERVER_NAME]/forum/$forum[id]/$razdel[id]/$them[id]/\r";
    exit;
}
if (isset($user) && isset($_GET['f_del']) && is_numeric($_GET['f_del']) && isset($_SESSION['file'][$_GET['f_del']])) {
    @unlink($_SESSION['file'][$_GET['f_del']]['tmp_name']);
}
if (isset($user) && isset($_GET['zakl']) && $_GET['zakl'] == 1) {
    if (dbresult(dbquery("SELECT COUNT(*) FROM `bookmarks` WHERE `id_user` = $user[id] AND `type`='forum' AND `id_object` = '$them[id]'"), 0) != 0) {
        $err[] = "主题已在您的书签中";
    } else {
        dbquery("INSERT INTO `bookmarks` (`id_user`, `time`,  `id_object`, `type`) values('$user[id]', '$time', '$them[id]', 'forum')");
        msg('主题已添加到书签');
    }
} elseif (isset($user) && isset($_GET['zakl']) && $_GET['zakl'] == 0) {
    dbquery("DELETE FROM `bookmarks` WHERE `id_user` = '$user[id]' AND `type`='forum' AND `id_object` = '$them[id]'");
    msg('主题已从书签中删除');
}
if (isset($user) && isset($_GET['act']) && $_GET['act'] == 'new' && isset($_FILES['file_f']) && preg_match('#\.#', $_FILES['file_f']['name']) && isset($_POST['file_s'])) {
    copy($_FILES['file_f']['tmp_name'], H . 'sys/tmp/' . $user['id'] . '_' . md5_file($_FILES['file_f']['tmp_name']) . '.forum.tmp');
    chmod(H . 'sys/tmp/' . $user['id'] . '_' . md5_file($_FILES['file_f']['tmp_name']) . '.forum.tmp', 0777);
    if (isset($_SESSION['file'])) $next_f = count($_SESSION['file']);
    else $next_f = 0;
    $file = esc(stripcslashes(htmlspecialchars($_FILES['file_f']['name'])));
    $_SESSION['file'][$next_f]['name'] = preg_replace('#\.[^\.]*$#i', NULL, $file); // имя файла без расширения
    $_SESSION['file'][$next_f]['ras'] = strtolower(preg_replace('#^.*\.#i', NULL, $file));
    $_SESSION['file'][$next_f]['tmp_name'] = H . 'sys/tmp/' . $user['id'] . '_' . md5_file($_FILES['file_f']['tmp_name']) . '.forum.tmp';
    $_SESSION['file'][$next_f]['size'] = filesize(H . 'sys/tmp/' . $user['id'] . '_' . md5_file($_FILES['file_f']['tmp_name']) . '.forum.tmp');
    $_SESSION['file'][$next_f]['type'] = $_FILES['file_f']['type'];
}
if (isset($user) && ($them['close'] == 0 || $them['close'] == 1 && user_access('forum_post_close')) && isset($_GET['act']) && $_GET['act'] == 'new' && isset($_POST['msg']) && !isset($_POST['file_s'])) {
    $msg = $_POST['msg'];
    if (strlen2($msg) < 2)
        $err = 'Короткое сообщение';
    if (strlen2($msg) > 1024)
        $err = 'Длина сообщения превышает предел в 1024 символа';
    $mat = antimat($msg);
    if ($mat)
        $err[] = 'В тексте сообщения обнаружен мат: ' . $mat;
    if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' AND `id_user` = '$user[id]' AND `msg` = '" . my_esc($msg) . "' LIMIT 1"), 0) != 0)
        $err = 'Ваше сообщение повторяет предыдущее';
    if (!isset($err)) {
        if (isset($_POST['cit']) && is_numeric($_POST['cit']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id` = '" . intval($_POST['cit']) . "' AND `id_them` = '" . intval($_GET['id_them']) . "' AND `id_razdel` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "'"), 0) == 1)
            $cit = intval($_POST['cit']);
        else
            $cit = 'null';
        dbquery("UPDATE `user` SET `balls` = '" . ($user['balls'] + 1) . "' WHERE `id` = '$user[id]' LIMIT 1");
        dbquery("UPDATE `forum_zakl` SET `time_obn` = '$time' WHERE `id_them` = '$them[id]'");
        dbquery("INSERT INTO `forum_p` (`id_forum`, `id_razdel`, `id_them`, `id_user`, `msg`, `time`, `cit`) values('$forum[id]', '$razdel[id]', '$them[id]', '$user[id]', '" . my_esc($msg) . "', '$time', $cit)");
        $post_id = mysql_insert_id();
        if (isset($_SESSION['file']) && isset($user)) {
            for ($i = 0; $i < count($_SESSION['file']); $i++) {
                if (isset($_SESSION['file'][$i]) && is_file($_SESSION['file'][$i]['tmp_name'])) {
                    dbquery("INSERT INTO `forum_files` (`id_post`, `name`, `ras`, `size`, `type`) values('$post_id', '" . $_SESSION['file'][$i]['name'] . "', '" . $_SESSION['file'][$i]['ras'] . "', '" . $_SESSION['file'][$i]['size'] . "', '" . $_SESSION['file'][$i]['type'] . "')");
                    $file_id = mysql_insert_id();
                    copy($_SESSION['file'][$i]['tmp_name'], H . 'sys/forum/files/' . $file_id . '.frf');
                    unlink($_SESSION['file'][$i]['tmp_name']);
                }
            }
            unset($_SESSION['file']);
        }
        unset($_SESSION['msg']);
        $ank = user::get_user($them['id_user']); // Определяем автора
        dbquery("UPDATE `user` SET `rating_tmp` = '" . ($user['rating_tmp'] + 1) . "' WHERE `id` = '$user[id]' LIMIT 1");
        dbquery("UPDATE `forum_r` SET `time` = '$time' WHERE `id` = '$razdel[id]' LIMIT 1");
        /*
====================================
Обсуждения
====================================
*/
        $q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $them['id_user'] . "' AND `i` = '1'");
        while ($f = dbarray($q)) {
            $a = user::get_user($f['frend']);
            $discSet = dbarray(dbquery("SELECT * FROM `discussions_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); // Общая настройка обсуждений
            if ($f['disc_forum'] == 1 && $discSet['disc_forum'] == 1) /* Фильтр рассылки */ {
                // друзьям автора
                if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'them' AND `id_sim` = '$them[id]' LIMIT 1"), 0) == 0) {
                    if ($them['id_user'] != $a['id'] || $a['id'] != $user['id'])
                        dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$a[id]', '$them[id_user]', 'them', '$time', '$them[id]', '1')");
                } else {
                    $disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$a[id_user]' AND `type` = 'them' AND `id_sim` = '$them[id]' LIMIT 1"));
                    if ($them['id_user'] != $a['id'] || $a['id'] != $user['id'])
                        dbquery("UPDATE `discussions` SET `count` = '" . ($disc['count'] + 1) . "', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'them' AND `id_sim` = '$them[id]' LIMIT 1");
                }
            }
        }
        // отправляем автору
        if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$them[id_user]' AND `type` = 'them' AND `id_sim` = '$them[id]' LIMIT 1"), 0) == 0) {
            if ($them['id_user'] != $user['id'] && $them['id_user'] != $ank_otv['id'])
                dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$them[id_user]', '$them[id_user]', 'them', '$time', '$them[id]', '1')");
        } else {
            $disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$them[id_user]' AND `type` = 'them' AND `id_sim` = '$them[id]' LIMIT 1"));
            if ($them['id_user'] != $user['id'] && $them['id_user'] != $ank_otv['id'])
                dbquery("UPDATE `discussions` SET `count` = '" . ($disc['count'] + 1) . "', `time` = '$time' WHERE `id_user` = '$them[id_user]' AND `type` = 'them' AND `id_sim` = '$them[id]' LIMIT 1");
        }
        /*
==========================
Уведомления об ответах
==========================
*/
        if (isset($user) && ($respons == TRUE || isset($_POST['cit']))) {
            // 	Уведомление при цитате
            if (isset($_POST['cit'])) {
                $cit2 = dbassoc(dbquery("SELECT * FROM `forum_p` WHERE `id` = '$cit' LIMIT 1"));
                $ank_otv['id'] = $cit2['id_user'];
            }
            $notifiacation = dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '" . $ank_otv['id'] . "' LIMIT 1"));
            if ($notifiacation['komm'] == 1)
                dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$them[id]', 'them_komm', '$time')");
        }
        $_SESSION['message'] = '消息已成功添加';
        header("Location: ?page=" . intval($_GET['page']) . "");
        exit;
    }
}
/*
================================
Модуль жалобы на пользователя
и его сообщение либо контент
в зависимости от раздела
================================
*/
if (isset($_GET['spam']) && isset($user)) {
    $mess = dbassoc(dbquery("SELECT * FROM `forum_p` WHERE `id` = '" . intval($_GET['spam']) . "' limit 1"));
    $spamer = user::get_user($mess['id_user']);
    if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'forum' AND `spam` = '" . $mess['msg'] . "'"), 0) == 0) {
        if (isset($_POST['spamus'])) {
            if ($mess['id_user'] != $user['id']) {
                $msg = mysql_real_escape_string($_POST['spamus']);
                if (strlen2($msg) < 3) $err = '更详细地说明投诉的原因';
                if (strlen2($msg) > 1512) $err = '文本的长度超过512个字符的限制';
                if (isset($_POST['types'])) $types = intval($_POST['types']);
                else $types = '0';
                if (!isset($err)) {
                    dbquery("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$them[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'forum', '" . my_esc($mess['msg']) . "')");
                    $_SESSION['message'] = 'Заявка на рассмотрение отправлена';
                    header("Location: /forum/$forum[id]/$razdel[id]/$them[id]/?spam=$mess[id]&page=$pageEnd");
                    exit;
                }
            }
        }
    }
    aut();
    err();
    if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'forum'"), 0) == 0) {
        echo "<div class='mess'>虚假信息会导致昵称被屏蔽。
如果你经常被一个写各种讨厌的东西的人惹恼，你可以把他加入黑名单。</div>";
        echo "<form class='nav1' method='post' action='/forum/$forum[id]/$razdel[id]/$them[id]/?spam=$mess[id]&amp;page=" . intval($_GET['page']) . "'>";
        echo "<b>用户:</b> ";
        echo " " . user::avatar($spamer['id']) . "  " . group($spamer['id']) . " <a href=\"/info.php?id=$spamer[id]\">$spamer[nick]</a>";
        echo "" . medal($spamer['id']) . " " . online($spamer['id']) . " (" . vremja($mess['time']) . ")<br />";
        echo "<b>违规：</b> <font color='green'>" . output_text($mess['msg']) . "</font><br />";
        echo "原因：<br /><select name='types'>";
        echo "<option value='1' selected='selected'>垃圾邮件/广告</option>";
        echo "<option value='2' selected='selected'>欺诈行为</option>";
        echo "<option value='3' selected='selected'>进攻</option>";
        echo "<option value='0' selected='selected'>其他</option>";
        echo "</select><br />";
        echo "评论:";
        echo $tPanel . "<textarea name=\"spamus\"></textarea><br />";
        echo "<input value=\"发送\" type=\"submit\" />";
        echo "</form>";
    } else {
        echo "<div class='mess'>投诉有关<font color='green'>$spamer[nick]</font> 它将在不久的将来考虑。</div>";
    }
    echo "<div class='foot'>";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?page=" . intval($_GET['page']) . "'>返回</a><br />";
    echo "</div>";
    include_once '../sys/inc/tfoot.php';
    exit;
}
if ($them['close'] == 1)
    $err = '主题不开放供讨论';
if (
    isset($user) &&  $user['balls'] >= 50 && $user['rating'] >= 0 && isset($_GET['id_file'])
    &&
    dbresult(dbquery("SELECT COUNT(*) FROM `forum_files` WHERE `id` = '" . intval($_GET['id_file']) . "'"), 0) == 1
    &&
    dbresult(dbquery("SELECT COUNT(*) FROM `forum_files_rating` WHERE `id_user` = '$user[id]' AND `id_file` = '" . intval($_GET['id_file']) . "'"), 0) == 0
) {
    if (isset($_GET['rating']) && $_GET['rating'] == 'down') {
        dbquery("INSERT INTO `forum_files_rating` (`id_user`, `id_file`, `rating`) values('$user[id]', '" . intval($_GET['id_file']) . "', '-1')");
        msg('您的负面反馈被接受');
    } elseif (isset($_GET['rating']) && $_GET['rating'] == 'up') {
        dbquery("INSERT INTO `forum_files_rating` (`id_user`, `id_file`, `rating`) values('$user[id]', '" . intval($_GET['id_file']) . "', '1')");
        msg('你的积极反馈被接受');
    }
}
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$avtor = user::get_user($them['id_user']);
err();
aut();
echo "<div class='foot'>";
echo '<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/">' . text($razdel['name']) . '</a> | <b>' . output_text($them['name']) . '</b>';
echo "</div>";
/*
======================================
主题移动
======================================
*/
if (isset($_GET['act']) && $_GET['act'] == 'mesto' && (user_access('forum_them_edit') || $ank2['id'] == $user['id'])) {
    echo "<form method=\"post\" action=\"/forum/$forum[id]/$razdel[id]/$them[id]/?act=mesto&amp;ok\">";
    echo "<div class='mess'>";
    echo "移动主题 <b>" . output_text($them['name']) . "</b>";
    echo "</div>";
    echo "<div class='main'>";
    echo "分类:<br />";
    echo "<select name=\"razdel\">";
    if (user_access('forum_them_edit')) {
        $q = dbquery("SELECT * FROM `forum_f` ORDER BY `pos` ASC");
        while ($forums = dbassoc($q)) {
            echo "<optgroup label='$forums[name]'>";
            $q2 = dbquery("SELECT * FROM `forum_r` WHERE `id_forum` = '$forums[id]' ORDER BY `time` DESC");
            while ($razdels = dbassoc($q2)) {
                echo "<option" . ($razdel['id'] == $razdels['id'] ? ' selected="selected"' : null) . " value=\"$razdels[id]\">" . text($razdels['name']) . "</option>";
            }
            echo "</optgroup>";
        }
    } else {
        $q2 = dbquery("SELECT * FROM `forum_r` WHERE `id_forum` = '$forum[id]' ORDER BY `time` DESC");
        while ($razdels = dbassoc($q2)) {
            echo "<option" . ($razdel['id'] == $razdels['id'] ? ' selected="selected"' : null) . " value='$razdels[id]'>" . text($razdels['name']) . "</option>";
        }
    }
    echo "</select><br />";
    echo "<input value=\"移动\" type=\"submit\" /> ";
    echo "<img src='/style/icons/delete.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/'>取消</a><br />";
    echo "</form>";
    echo "</div>";
    echo "<div class='foot'>";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/?'>在主题</a><br />";
    echo "</div>";
    include_once '../sys/inc/tfoot.php';
    exit;
}
/*
======================================
Редактирование темы
======================================
*/
if (isset($_GET['act']) && $_GET['act'] == 'set' && (user_access('forum_them_edit') || $ank2['id'] == $user['id'])) {
    echo "<form method='post' action='/forum/$forum[id]/$razdel[id]/$them[id]/?act=set&amp;ok'>";
    echo "<div class='mess'>";
    echo "编辑主题<b>" . output_text($them['name']) . "</b>";
    echo "</div>";
    echo "<div class=\"main\">";
    echo "标题:<br />";
    echo "<input name='name' type='text' maxlength='32' value='" . text($them['name']) . "' /><br />";
    echo "消息:$tPanel<textarea name=\"msg\">" . text($them['text']) . "</textarea><br />";
    if ($user['level'] > 0) {
        if ($them['up'] == 1) $check = ' checked="checked"';
        else $check = NULL;
        echo "<label><input type=\"checkbox\"$check name=\"up\" value=\"1\" /> 总是在楼上</label><br />";
    }
    if ($them['close'] == 1) $check = ' checked="checked"';
    else $check = NULL;
    echo "<label><input type=\"checkbox\"$check name=\"close\" value=\"1\" /> 关闭</label><br />";
    if ($ank2['id'] != $user['id']) {
        echo "<label><input type=\"checkbox\" name=\"autor\" value=\"1\" /> 剥夺作者的权利</label><br />";
    }
    echo "<input value=\"修改\" type=\"submit\" /> ";
    echo "<img src='/style/icons/delete.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/'>取消</a><br />";
    echo "</form>";
    echo "</div>";
    echo "<div class='foot'>";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/?'>在主题</a><br />";
    echo "</div>";
    include_once '../sys/inc/tfoot.php';
    exit;
}
if (user_access('forum_post_ed') && isset($_GET['del'])) // удаление поста
{
    dbquery("DELETE FROM `forum_p` WHERE `id` = '" . intval($_GET['del']) . "' LIMIT 1");
    $_SESSION['message'] = '邮件已成功删除';
    header("Location: /forum/$forum[id]/$razdel[id]/$them[id]/?page=" . intval($_GET['page']) . "");
    exit;
}
/*
======================================
Удаление темы
======================================
*/
if (isset($_GET['act']) && $_GET['act'] == 'del' && user_access('forum_them_del') && ($ank2['level'] <= $user['level'] || $ank2['id'] == $user['id'])) {
    echo "<div class=\"mess\">";
    echo "确认删除主题 <b>" . output_text($them['name']) . "</b><br />";
    echo "</div>";
    echo "<div class=\"main\">";
    echo "[<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?act=delete&amp;ok\"><img src='/style/icons/ok.gif' alt='*'> 是的</a>] [<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/\"><img src='/style/icons/delete.gif' alt='*'> 取消</a>]<br />";
    echo "</div>";
    echo "<div class='foot'>";
    echo "<img src='/style/icons/fav.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/?'>在主题</a><br />";
    echo "</div>";
    include_once '../sys/inc/tfoot.php';
    exit;
}
/*
=========
Опрос от VoronoZ
=========
*/
if (isset($_GET['act']) && $_GET['act'] == 'vote' && (user_access('forum_them_edit') || $ank2['id'] == $user['id'])) {
    if (dbresult(dbquery("SELECT COUNT(`id`) FROM `votes_forum` WHERE `them` = '" . abs(intval($them['id'])) . "' LIMIT 1"), 0) != 0) {
        if (isset($_POST['del']) && isset($user)) {
            dbquery("UPDATE `forum_t` SET `vote`='',`vote_time`='',`vote_close` ='0' WHERE `id` = '$them[id]' LIMIT 1");
            dbquery("DELETE FROM `votes_forum` WHERE `them` = '$them[id]'  ");
            dbquery("DELETE FROM `votes_user` WHERE `them` = '$them[id]'  ");
            $_SESSION['message'] = '调查被删除了！';
            header("Location:/forum/$forum[id]/$razdel[id]/$them[id]/");
        }
        if (isset($_POST['send']) && isset($user)) {
            $close = (isset($_POST['close']) ? 1 : 0);
            $text = my_esc($_POST['text']);
            if (strlen2($text) < 3) $err[] = '简短调查主题';
            if (strlen2($text) > 42) $err[] = '调查主题必须少于40个字符';
            $mat = antimat($text);
            if ($mat) $err[] = '在调查主题中发现了一个伴侣: ' . $mat;
            if (!isset($err)) {
                dbquery("UPDATE `forum_t` SET `vote`='$text',`vote_close` ='$close' WHERE `id` = '$them[id]' LIMIT 1");
            }
            for ($x = 1; $x < 7; $x++) {
                $add = my_esc($_POST['vote_' . $x . '']);
                if (strlen2($add) > 23) $err = '调查选项 № ' . $x . ' 太久了';
                if ($_POST['vote_1'] == NULL || $_POST['vote_2'] == NULL) $err = '前两个选项必须填写';
                $mat = antimat($add);
                if ($mat) $err = '在调查版本中 № ' . $x . '  检测到配偶: ' . $mat;
                if (!isset($err)) {
                    dbquery("UPDATE `votes_forum` SET `var`='$add' WHERE `num` = '$x' LIMIT 1");
                    $_SESSION['message'] = '调查已更改！';
                    header("Location:/forum/$forum[id]/$razdel[id]/$them[id]/");
                }
            }
        }
        err();
        function sub($str, $ch)
        {
            if ($ch < strlen($str)) {
                $str = iconv('UTF-8', 'windows-1251', $str);
                $str = substr($str, 0, $ch);
                $str = iconv('windows-1251', 'UTF-8', $str);
                $str .= '...';
            }
            return $str;
        }
        echo "<form method='post' action='/forum/$forum[id]/$razdel[id]/$them[id]/?act=vote'>";
        echo "<div class='nav1'>";
        echo "<img src='/style/icons/rating.png' alt='*'> 调查: <b>" . (mb_strlen($them['vote']) <= 15 ? output_text($them['vote']) : output_text(sub($them['vote'], 15))) . "</b><br/>";
        echo "</div>";
        echo "<div class='main'>";
        echo "<b>Т调查电邮</b>: <div style='border-top: 1px dashed red; padding: 2px;'>" . $tPanel . "<textarea name='text'>" . output_text($them['vote']) . "</textarea></div><br/>";
        $q = dbquery("SELECT * FROM `votes_forum` WHERE `them` = '" . abs(intval($them['id'])) . "' ORDER BY `id` ASC  LIMIT 6");
        while ($row = dbassoc($q)) {
            echo "选项№ $row[num] <div style='border-top: 1px dashed red; padding: 2px;'><input name='vote_$row[num]' type='text' value='" . (isset($row['var']) ? output_text($row['var']) : NULL) . "' maxlength='24' placeholder='未填写'  /></div>";
        }
        echo "<label><input type='checkbox' name='close' " . ($them['vote_close'] == '1' ? "checked='checked' value='1' /> 打开调查" : "value='1'/> 关闭调查") . " </label>
";
        echo '<input value="更改" name="send" type="submit" />  
<input value="删除调查" name="del" type="submit" /> 
</form>';
    } else {
        if (isset($_POST['send']) && isset($user)) {
            $text = my_esc($_POST['text']);
            if (strlen2($text) < 3) $err[] = '简短调查主题';
            if (strlen2($text) > 42) $err[] = '调查主题必须少于40个字符';
            $mat = antimat($text);
            if ($mat) $err[] = '在调查主题中发现了一个伴侣: ' . $mat;
            if (!isset($err)) {
                dbquery("UPDATE `forum_t` SET `vote`='$text',`vote_close` ='0' WHERE `id` = '$them[id]' LIMIT 1");
            }
            for ($x = 1; $x < 7; $x++) {
                $add = my_esc($_POST['add_' . $x . '']);
                if (strlen2($add) > 23) $err = '调查选项№ ' . $x . ' 太久了';
                if ($_POST['add_1'] == NULL || $_POST['add_2'] == NULL) $err = '前两个选项必须填写';
                $mat = antimat($add);
                if ($mat) $err = '在调查版本中 № ' . $x . '  检测到配偶: ' . $mat;
                if (!isset($err)) {
                    dbquery("INSERT INTO `votes_forum` (`them`,`var`,`num`) values('$them[id]','$add','$x')");
                    $_SESSION['message'] = '调查已添加！';
                    header("Location:/forum/$forum[id]/$razdel[id]/$them[id]/");
                }
            }
        }
        err();
        echo "<form method='post' action='/forum/$forum[id]/$razdel[id]/$them[id]/?act=vote'>";
        echo "<div class='main'>";
        echo '调查主题:' . $tPanel . '<textarea name="text"></textarea><br/> 
';
        for ($x = 1; $x < 7; $x++)
            echo "选项№ $x <div style='border-top: 1px dashed red; padding: 2px;'><input name='add_$x' type='text' maxlength='15' placeholder='未填写' /></div>";
        echo '<input value="增加" type="submit" name="send" /> </form>';
    }
    echo "<img src='/style/icons/delete.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/'>取消</a>
";
    echo "</form>";
    echo "</div>";
    echo "<div class='foot'>";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/?'>在主题</a>
";
    echo "</div>";
    include_once '../sys/inc/tfoot.php';
    exit;
}
if (isset($_GET['vote_user']) && dbresult(dbquery("SELECT * FROM `votes_user` WHERE `var` = '" . intval($_GET['vote_user']) . "' AND `them`='$them[id]' "), 0) != 0) {
    $us = intval($_GET['vote_user']);
    $k_post = dbresult(dbquery("SELECT * FROM `votes_user` WHERE  `var` = '$us' AND `them`='$them[id]'"), 0);
    $k_page = k_page($k_post, $set['p_str']);
    $page = page($k_page);
    $start = $set['p_str'] * $page - $set['p_str'];
    $q = dbquery("SELECT `id_user`,`time` FROM `votes_user` WHERE  `var` = '$us' AND `them`='$them[id]' ORDER BY `time`  LIMIT $start, $set[p_str] ");
    while ($row = dbassoc($q)) {
        $ank = user::get_user($row['id_user']);
        echo '<table class="post">';
        #Div Block's
        if ($num == 0) {
            echo '<div class="nav1">';
            $num = 1;
        } elseif ($num == 1) {
            echo '<div class="nav2">';
            $num = 0;
        }
        echo user::nick($ank['id'], 1, 1, 0) . ' ' . vremja($row['time']) . '</div>';
    }
    if ($k_page > 1)
        str("/forum/$forum[id]/$razdel[id]/$them[id]/?vote_user=$us&", $k_page, $page);
    echo '<div class="foot">
                    <img src="/style/icons/fav.gif" alt="*"> <a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id
    '] . '/?">以...为主题</a>
                </div>';
    include_once '../sys/inc/tfoot.php';
    exit;
}
/* End Vote */
/* Голосование в опросе*/
if (isset($_POST['go']) && isset($_POST['vote']) && isset($user)) {
    $vote = abs(intval($_POST['vote']));
    if (dbresult(dbquery("SELECT * FROM `votes_user` WHERE `them` = '" . abs(intval($them['id'])) . "'  AND `id_user`='$user[id]' LIMIT 1"), 0) == 0  && $them['vote_close'] != '1' && $them['close'] == '0') {
        dbquery("INSERT INTO `votes_user` (`them`,`id_user`,`var`,`time`) values('$them[id]','$user[id]','$vote','" . time() . "')");
        $_SESSION['message'] = '你的投票被接受了!';
        header("Location:/forum/$forum[id]/$razdel[id]/$them[id]/");
    } else {
        $_SESSION['message'] = '错误 !';
        header("Location:/forum/$forum[id]/$razdel[id]/$them[id]/");
    }
}
/*
======================================
选题时间和内容
======================================
*/
echo "<div class='mess'><img src='/style/icons/blogi.png'> 作者: " . user::nick($them['id_user'],1,1,0) . " <br/>";
echo "<img src='/style/icons/alarm.png' alt='*' /> 创建时间: " . vremja($them['time']) . " <br/>";
echo "<img src='/style/icons/kumr.gif'> 标题: <b>" . text($them['name']) . "</b></div>";
echo "<div class='nav2'>" . output_text($them['text']) . " ";
/*
==========
调查
==========
*/
$vote_c = dbresult(dbquery("SELECT COUNT(*) FROM `votes_forum` WHERE `them` = '" . abs(intval($them['id'])) . "' LIMIT 1"), 0);
if ($vote_c != 0) {
?><div class="round_corners poll_block stnd_padd">
        <div style="font-size:14px;">调查: <b><?= output_text($them['vote']); ?></b></div>
        <?php
        $q = dbquery("SELECT * FROM `votes_forum` WHERE `them` = '" . abs(intval($them['id'])) . "' AND `var` != '' LIMIT 6");
        ?>
        <form action="" method="post">
            <?php
            while ($row = dbassoc($q)) {
                $sum = dbresult(dbquery("SELECT COUNT(*) FROM `votes_user` WHERE `them` = '$row[them]'"), 0);
                $var = dbresult(dbquery("SELECT COUNT(*) FROM `votes_user` WHERE `them` = '$row[them]' AND `var` = '$row[num]'"), 0);
                if ($sum == 0) $poll = 0;
                elseif ($var == 0) $poll = 0;
                else $poll = ($var / $sum) * 100;
                $us = dbresult(dbquery("SELECT COUNT(*) FROM `votes_user` WHERE `them` = '" . abs(intval($them['id'])) . "'  AND `id_user`='$user[id]' LIMIT 1"), 0);
                if ($us == '0' && isset($user)) {
            ?>
                    <input type="radio" value="<?= $row['num']; ?>" name="vote" />&nbsp;<?= output_text($row['var']); ?></a> - <a href="?vote_user=<?= $row['num']; ?>"><?= $var; ?> чел.</a></br>
                <?php } else { ?>
                    <?= output_text($row['var']); ?> <a href="?vote_user=<?= $row['num']; ?>"><?= $var; ?></a></br><img src="/forum/img.php?img=<?= $poll; ?>" alt="*" /></br>
                <?php }
            }
            if (isset($user) && $us == 0 && $them['vote_close'] != '1' && $them['close'] == 0) {
                ?><input type="submit" name="go" value="投票" />
                <?php }
            echo '</form></div>';
        }
        echo "</div>";
        /*
======================================
В закладки и поделиться
======================================
*/
        if (!empty($them['id_edit'])) {
            echo "<div class='nav2'>";
            echo "<span style='color:#666;'><img src='/style/icons/edit.gif'> 修改了 " . user::nick($them['id_edit'],1,1,0) . " " . vremja($them['time_edit']) . "</span></div>";
        } elseif (!empty($them['id_close'])) {
            echo "<div class='nav2'>";
            echo "<span style='color:#666;'><img src='/style/icons/topic_locked.gif'> 主题已关闭 " . user::nick($them['id_edit'],1,1,0) . " " . vremja($them['time_edit']) . "</span></div>";
        }
        echo "<div class='mess'>";
        $share = dbresult(dbquery("SELECT COUNT(`id`)FROM `notes` WHERE `share_id`='" . $them['id'] . "' AND `share_type`='forum'"), 0);
        if (dbresult(dbquery("SELECT COUNT(`id`)FROM `notes` WHERE `id_user`='" . $user['id'] . "' AND `share_type`='forum' AND `share_id`='" . $them['id'] . "' LIMIT 1"), 0) == 0 && isset($user)) {
            echo " <a href='/forum/share.php?id=" . $them['id'] . "'><img src='/style/icons/action_share_color.gif'> 分享: (" . $share . ")</a>";
        } else {
            echo "<img src='/style/icons/action_share_color.gif'> 共享  (" . $share . ")";
        }
        if (isset($user)) {
            $markinfo = dbresult(dbquery("SELECT COUNT(`id`) FROM `bookmarks` WHERE `id_object` = '" . $them['id'] . "' AND `type`='forum'"), 0);
            echo "<br/><img src='/style/icons/add_fav.gif' alt='*' /> ";
            if (dbresult(dbquery("SELECT COUNT(`id`) FROM `bookmarks` WHERE `id_object` = '$them[id]' AND `id_user` = '$user[id]' AND `type`='forum'"), 0) == 0)
                echo " <a href=\"?page=$page&amp;zakl=1\" title='添加到书签'>添加到书签</a><br />";
            else {
                echo " <a href=\"?page=$page&amp;zakl=0\" title='从书签中删除'>从书签中删除</a><br />";
            }
        }
        echo "</div>";
        /*
======================================
Кнопки действия с темой
======================================
*/
        if (isset($user) && (((!isset($_GET['act']) || $_GET['act'] != 'post_delete') && (user_access('forum_post_ed') || $ank2['id'] == $user['id']))
            || ((user_access('forum_them_edit') || $ank2['id'] == $user['id']))
            || (user_access('forum_them_del') || $ank2['id'] == $user['id']))) {
            echo "<div class=\"foot\">";
            if (user_access('forum_them_edit') || $them['id_user'] == $user['id']) {
                echo "<img src='/style/icons/settings.gif' width='16'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/?act=set'><font color='darkred'>编辑</font></a><br/>";
                echo "<img src='/style/icons/glavnaya.gif' width='16'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/?act=mesto'><font color='darkred'>移动</font></a>";
                if ($vote_c == 0) {
                ?><br /><img src="/style/icons/top10.png"> <a href="/forum/<?= $forum['id']; ?>/<?= $razdel['id']; ?>/<?= $them['id']; ?>/?act=vote">
                        <font color="darkred">添加调查</font>
                    </a> <?
                        } else {
                            echo '<br/><img src="/style/icons/diary.gif"> <a href="?act=vote"><font color="darkred">编辑调查</font></a>';
                        }
                    }
                    if (user_access('forum_them_del') || $ank2['id'] == $user['id']) {
                        echo "<br/><img src='/style/icons/delete.gif' width='16'> <a href='/forum/$forum[id]/$razdel[id]/$them[id]/?act=del'><font color='darkred'>删除主题</font></a>";
                    }
                    echo "</div>";
                }
                echo "<div class='foot'>评论：</div>";
                /*------------сортировка по времени--------------*/
                if (isset($user)) {
                    echo "<div id='comments' class='menus'>";
                    echo "<div class='webmenu'>";
                    echo "<a href='/forum/$forum[id]/$razdel[id]/$them[id]/?page=$page&amp;sort=1' class='" . ($user['sort'] == 1 ? 'activ' : '') . "'>在下面</a>";
                    echo "</div>";
                    echo "<div class='webmenu'>";
                    echo "<a href='/forum/$forum[id]/$razdel[id]/$them[id]/?page=$page&amp;sort=0' class='" . ($user['sort'] == 0 ? 'activ' : '') . "'>在顶部</a>";
                    echo "</div>";
                    echo "</div>";
                }
                /*---------------alex-borisi---------------------*/
                if ((user_access('forum_post_ed') || isset($user) && $ank2['id'] == $user['id']) && isset($_GET['act']) && $_GET['act'] == 'post_delete') {
                    $lim = NULL;
                } else $lim = " LIMIT $start, $set[p_str]";
                $q = dbquery("SELECT * FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' ORDER BY `time` $sort$lim");
                if (dbrows($q) == 0) {
                    echo "<div class='mess'>";
                    echo "没有留言在主题";
                    echo "</div>";
                }
                while ($post = dbassoc($q)) {
                    $ank = user::get_user($post['id_user']);
                    /*-----------代码-----------*/
                    if ($num == 0) {
                        echo '<div class="nav1">';
                        $num = 1;
                    } elseif ($num == 1) {
                        echo '<div class="nav2">';
                        $num = 0;
                    }
                    /*---------------------------*/
                    if ((user_access('forum_post_ed') || isset($user) && $ank2['id'] == $user['id']) && isset($_GET['act']) && $_GET['act'] == 'post_delete') {
                        echo '<input type="checkbox" name="post_' . $post['id'] . '" value="1" />';
                    }
                    echo user::avatar($post['id_user']);
                    echo user::nick($ank['id'], 1, 1, 0) . ' <span style="float:right;color:#666;">' . vremja($post['time']) . '</span><br/>';
                    $postBan = dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE (`razdel` = 'all' OR `razdel` = 'forum') AND `post` = '1' AND `id_user` = '$ank[id]' AND (`time` > '$time' OR `navsegda` = '1')"), 0);
                    if ($postBan == 0) // Блок сообщения
                    {
                        if ($them['id_user'] == $post['id_user']) // Отмечаем автора темы
                            echo '<font color="#999">主题作者</font><br />';
                        /*------------Вывод статуса-------------*/
                        $status = dbassoc(dbquery("SELECT * FROM `status` WHERE `pokaz` = '1' AND `id_user` = '$ank[id]' LIMIT 1"));
                        if (isset($status['id']) && $set['st'] == 1) {
                            echo "<div class='st_1'></div>";
                            echo "<div class='st_2'>";
                            echo "" . output_text($status['msg']) . "";
                            echo "</div>";
                        }
                        /*---------------------------------------*/
                        # Цитирование поста
                        if ($post['cit'] != NULL && dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id` = '$post[cit]'"), 0) == 1) {
                            $cit = dbassoc(dbquery("SELECT * FROM `forum_p` WHERE `id` = '$post[cit]' LIMIT 1"));
                            $ank_c = user::get_user($cit['id_user']);
                            echo '<div class="cit">
<b>' . $ank_c['nick'] . ' (' . vremja($cit['time']) . '):</b><br />
' . output_text($cit['msg']) . '<br />
</div>';
                        }
                        echo output_text($post['msg']) . '<br />'; // Посты темы
                        echo '<table>';
                        include H . '/forum/inc/file.php'; // Прекрепленные файлы
                        echo '</table>';
                    } else {
                        echo output_text($banMess) . '<br />';
                    }
                    if (isset($user)) {
                        if ($them['close'] == 0) {
                            if (isset($user) &&  $user['id'] != $ank['id'] && $ank['id'] != 0) {
                                echo '<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/?response=' . $ank['id'] . '&amp;page=' . $page . '" title="Ответить ' . $ank['nick'] . '">回答</a> | ';
                                echo '<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/' . $post['id'] . '/cit" title="引用 ' . $ank['nick'] . '">报价</a>';
                            }
                        }
                        echo '<span style="float:right;">';
                        if ($them['close'] == 0) // 当主题关闭时隐藏按钮 。                                                                        
                        {
                            if (user_access('forum_post_ed') && ($ank['level'] <= $user['level'] || $ank['level'] == $user['level'] &&  $post['id_user'] == $user['id']))
                                echo "<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/$post[id]/edit\" title='修改岗位$ank[nick]'  class='link_s'><img src='/style/icons/edit.gif' alt='*'> </a> ";
                            elseif ($user['id'] == $post['id_user'] && $post['time'] > time() - 600)
                                echo "<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/$post[id]/edit\" title='修改我的职位。'  class='link_s'><img src='/style/icons/edit.gif' alt='*'> (" . ($post['time'] + 600 - time()) . " sec)</a> ";
                            if ($user['id'] != $ank['id'] && $ank['id'] != 0) // 帖子制定者及系统除外 
                            {
                                echo "<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?spam=$post[id]&amp;page=$page\" title='是垃圾邮件。'  class='link_s'><img src='/style/icons/blicon.gif' alt='*' title='这是垃圾邮件'></a>";
                            }
                        }
                        if (user_access('forum_post_ed')) // 删除帖子
                        {
                            echo "<a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?del=$post[id]&amp;page=$page\" title='删除'  class='link_s'><img src='/style/icons/delete.gif' alt='*' title='删除'></a>";
                        }
                        echo "&nbsp;";
                        echo '</span><br/>';
                    }
                    echo ' ' . ($webbrowser ? null : '<br/>') . ' </div>';
                }
                if ((user_access('forum_post_ed') || isset($user) && $ank2['id'] == $user['id']) && isset($_GET['act']) && $_GET['act'] == 'post_delete') {
                } elseif ($k_page > 1) str("/forum/$forum[id]/$razdel[id]/$them[id]/?", $k_page, $page); // 输出页数
                if ((user_access('forum_post_ed') || isset($user) && $ank2['id'] == $user['id']) && isset($_GET['act']) && $_GET['act'] == 'post_delete') {
                } elseif (isset($user) && ($them['close'] == 0 || $them['close'] == 1 && user_access('forum_post_close'))) {
                    if (isset($user)) {
                        echo "<div class='foot'>";
                        echo '新讯息:';
                        echo "</div>";
                    }
                    if ($user['set_files'] == 1)
                        echo "<form method='post' name='message' enctype='multipart/form-data' action='/forum/$forum[id]/$razdel[id]/$them[id]/new?page=$page&amp;$passgen&amp;" . $go_otv . "'>";
                    else
                        echo "<form method='post' name='message' action='/forum/$forum[id]/$razdel[id]/$them[id]/new?page=$page&amp;$passgen&amp;" . $go_otv . "'>";
                    if (isset($_POST['msg']) && isset($_POST['file_s']))
                        $msg2 = output_text($_POST['msg'], false, true, false, false, false);
                    else
                        $msg2 = NULL;
                    if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
                        include H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
                    else
                        echo "$tPanel<textarea name=\"msg\">$otvet$msg2</textarea><br />";
                    if ($user['set_files'] == 1) {
                        if (isset($_SESSION['file'])) {
                            echo "附加档案:<br />";
                            for ($i = 0; $i < count($_SESSION['file']); $i++) {
                                if (isset($_SESSION['file'][$i]) && is_file($_SESSION['file'][$i]['tmp_name'])) {
                                    echo "<img src='/style/themes/$set[set_them]/forum/14/file.png' alt='' />";
                                    echo $_SESSION['file'][$i]['name'] . '.' . $_SESSION['file'][$i]['ras'] . ' (';
                                    echo size_file($_SESSION['file'][$i]['size']);
                                    echo ") <a href='/forum/$forum[id]/$razdel[id]/$them[id]/d_file$i' title='从列表中删除'><img src='/style/themes/$set[set_them]/forum/14/del_file.png' alt='' /></a>";
                                    echo "<br />";
                                }
                            }
                        }
                        echo "<input name='file_f' type='file' /><br />";
                        echo "<input name='file_s' value='附加文件' type='submit' /><br />";
                    }
                    echo '<input name="post" value="发送" type="submit" /><br />
</form>';
                }
                            ?>