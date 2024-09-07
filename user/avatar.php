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
only_reg();

//屏蔽 Notice 报错
error_reporting(E_ALL || ~E_NOTICE);

$set['title']='设置头像';
include_once '../sys/inc/thead.php';
title();
err();
aut();
	echo "<div class='main'>";
	echo user::avatar($ank['id']);
	echo "</div>";
	echo "<div class='main'>";
	echo "创建一个相册,上传头像后点击设置为头像";
	echo "</div>";
	//--------------------------相片册-----------------------------//
	echo "<div class='main'>";echo "<img src='/style/icons/photo.png' alt='*' /> ";
	echo "<a href='/photo/$user[id]/'>照片</a> ";
	echo "(" . dbresult(dbquery("SELECT COUNT(*) FROM `gallery_photo` WHERE `id_user` = '$user[id]'"),0) . ")";
	echo "</div>";
	//------------------------------------------------------------------// 
include_once '../sys/inc/tfoot.php';