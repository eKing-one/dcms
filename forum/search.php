<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
/* Бан пользователя */ 
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0)!=0){
	header('Location: /ban.php?'.SID);
	exit;
}
$searched = &$_SESSION['searched']; 
if (!isset($searched) || isset($_GET['newsearch']) || isset($_GET['null'])) 
{ 
    // зануляем весь запрос 
    $searched['in'] = array('m'=>null); 
    $searched['text'] = null; 
    $searched['query'] = null; 
    $searched['sql_query'] = null; 
    $searched['result'] = array(); 
    $searched['mark'] = array(); 
} 
if (isset($_GET['newsearch'])) 
include 'inc/search_act.php'; 
//网页标题 
$set['title']='论坛-搜索'; 
include_once '../sys/inc/thead.php'; 
title(); 
aut(); // форма авторизации 
err(); 
if (isset($_GET['newsearch'])) 
{ 
    if (count($searched['result'])!=0) 
    msg('应要求 "' . htmlentities($searched['text'], ENT_QUOTES, 'UTF-8') . '" 找到的火柴:' . count($searched['result'])); 
    elseif(!isset($err)) 
    msg('应要求"' . htmlentities($searched['text'], ENT_QUOTES, 'UTF-8') . '" 什么也没找到'); 
} 
$res = $searched['result']; 
if (count($res)!=0) 
{ 
    $k_post = count($res); 
    $k_page = k_page($k_post,$set['p_str']); 
    $page = page($k_page); 
    $start = $set['p_str'] * $page-$set['p_str']; 
    $end = min($set['p_str'] * $page,$k_post); 
    echo '<table class="post">'; 
    for($i = $start; $i < $end; $i++) 
    { 
        $them = $res[$i]; 
        if (dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_them` = '$them[id]'"),0) == $them['k_post'])
        { 
            // Определение подфорума 
            $forum = dbarray(dbquery("SELECT * FROM `forum_f` WHERE `id` = '$them[id_forum]' LIMIT 1"));
            // Определение раздела 
            $razdel = dbarray(dbquery("SELECT * FROM `forum_r` WHERE `id` = '$them[id_razdel]' LIMIT 1"));
            // Лесенка дивов 
            if ($num == 0) 
            { 
                echo '<div class="nav1">'; 
                $num = 1; 
            } 
            elseif ($num == 1) 
            { 
                echo '<div class="nav2">'; 
                $num = 0; 
            } 
            // Иконка темы 
            echo '<img src="/style/themes/' . $set['set_them'] . '/forum/14/them_' . $them['up'] . $them['close'] . '.png" alt="" /> ';
            // Ссылка на тему 
            echo '<a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/">' . text($them['name']) . '</a> 
            <a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/' . $them['id'] . '/?page=' . $pageEnd . '"> 
            (' . dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]' AND `id_razdel` = '$razdel[id]' AND `id_them` = '$them[id]'"),0) . ')</a><br/>';
            // Подфорум и раздел 
            echo '<a href="/forum/' . $forum['id'] . '/">' . text($forum['name']) . '</a> > <a href="/forum/' . $forum['id'] . '/' . $razdel['id'] . '/">' . text($razdel['name']) . '</a>
'; 
            // Автор темы 
            $ank = get_user($them['id_user']); 
            echo '作者: <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> (' . vremja($them['time_create']) . ') 
'; 
            // Последний пост  
            $post = dbarray(dbquery("SELECT * FROM `forum_p` WHERE `id_them` = '$them[id]' AND `id_razdel` = '$razdel[id]' AND `id_forum` = '$forum[id]' ORDER BY `time` DESC LIMIT 1"));
            // Автор последнего поста 
            if (isset($post['id_user'])) 
            { 
                $ank2 = get_user($post['id_user']); 
                echo '大使.: <a href="/info.php?id=' . $ank2['id'] . '">' . $ank2['nick'] . '</a> (' . vremja($post['time']) . ') 
';     
            } 
            echo '</div>'; 
        } 
        else 
        { 
            echo esc(br(bbcode(preg_replace($searched['mark'], "<span class='search_cit'>1</span>",htmlentities($them['msg'], ENT_QUOTES, 'UTF-8'))))).""; 
            echo "比赛总数: ".$them['k_post'].""; 
        } 
    } 
    echo '</table>'; 
    if ($k_page > 1)str('?',$k_page,$page); // 输出页数 
} 
else 
{ 
    include 'inc/search_form.php'; 
} 
// Меню возврата 
echo '<div class="foot">'; 
if (count($searched['result']) != 0)echo '<img src="/style/icons/str2.gif" /> <a href="?null=' . $passgen . '">新搜寻</a> 
'; 
echo '<img src="/style/icons/str2.gif" /> <a href="/forum/">论坛</a> | <b>论坛搜索</b>';
echo '</div>'; 
include_once '../sys/inc/tfoot.php';
