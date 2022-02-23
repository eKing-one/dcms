<?php
/*
=======================================
DCMS-Social 用户个人文件
作者：探索者
---------------------------------------
此脚本在许可下被破坏
DCMS-Social 引擎。
使用时，指定引用到
网址 http://dcms-social.ru
---------------------------------------
接点
ICQ：587863132
http://dcms-social.ru
=======================================
*/
if ($dir['osn'] < 6 && $ank['id'] == $user['id']) {
    echo "<div class='foot'>";
    echo "<img src='/style/icons/str.gif' alt='*'> <a href='?add'>创建文件夹</a> | <a href='?upload'>添加文件</a><br />";
    echo "</div>";
}
