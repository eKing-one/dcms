<?php
/**
 * 笑话机器人相关的逻辑部分
 */

$shutnik_last = dbassoc(dbquery("SELECT * FROM `chat_post` WHERE `room` = '$room[id]' AND `shutnik` = '1' ORDER BY id DESC LIMIT 1"));
if ($shutnik_last==NULL || $shutnik_last['time']<time()-$set['shutnik_new']) {
    $k_vopr=dbresult(dbquery("SELECT COUNT(*) FROM `chat_shutnik`"),0);
    $shutnik = dbassoc(dbquery("SELECT * FROM `chat_shutnik` LIMIT ".rand(0,$k_vopr).",1"));
    dbquery("INSERT INTO `chat_post` (`shutnik`, `time`, `msg`, `room`, `privat`) values('1', '$time', '$shutnik[anek]', '$room[id]', '0')");
}