<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
$set['title'] = '每日签到';
include_once '../../sys/inc/thead.php';
title();
only_reg();
if (!isset($user))
    header("location: /index.php?");
err();
aut();

$today = date("Y-m-d");
$result = dbquery("SELECT `checkin_date` FROM `user_log` WHERE `checkin_date` = '$today' LIMIT 1");
$checkin_date = dbresult($result, 0);

if ($today == $checkin_date){
    echo "<div class ="mess">";
    echo "今天已经签到过啦";
    echo "</div>'";
} else {
    dbquery("UPDATE `user` SET `balls` = `balls` + 500 " . "WHERE `id` = `$user[id]' LIMIT 1");
    dbquery("UPDATE `user_log` SET `checkin_date` = date("Y-m-d")" . "WHERE `id` = `$user[id]' LIMIT 1");
    echo "<div class = "mess">";
    echo "签到成功!";
    echo "</div>"
}

include_once '../sys/inc/tfoot.php';
