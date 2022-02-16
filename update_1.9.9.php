<?php
include_once 'sys/inc/start.php';
include_once 'sys/inc/compress.php';
include_once 'sys/inc/sess.php';
include_once 'sys/inc/home.php';
include_once 'sys/inc/settings.php';
include_once 'sys/inc/db_connect.php';
include_once 'sys/inc/ipua.php';
include_once 'sys/inc/fnc.php';
include_once 'sys/inc/user.php';
user_access('adm_panel_show',null,'/index.php?'.SID);
$set['title']='版本升级至 1.9.9';
include_once 'sys/inc/thead.php';
title();
err();
aut();
if(!isset($_GET['step'])){
 echo '<div class="mess">';
 echo '<br/>你可以阅读更新列表<a href="http://dcms-social.ru/plugins/download/file.php?id=141">在发布出版物中</a><br/>';
 echo '你已经用文件替换解压缩了存档。只剩下垃圾了 Tables.</div>';
 echo '<div class="mess"><a href="?step=one">开始向 1.9.9</a></div>';
 }elseif(isset($_GET['step']) && $_GET['step']=='one'){
 echo '<div class="mess">';
 echo '<div class="nav2">让我们选择需要填充的表格</div>';
 echo '<form method="post" action="/update_1.9.9.php?step=two">';
 echo '<input type="checkbox" name="votes" value="1"> 论坛调查<br/>';
 echo '<input type="checkbox" name="stena_komm" value="1">记述墙<br/>';
 echo '<input type="checkbox" name="notes_share" value="1"> "为日记分享“ <br/>';
 echo '<input type="checkbox" name="bookmarks" value="1"> 新书签<br/>';
 echo '<input type="checkbox" name="opis_r" value="1"> 论坛部分说明<br/>';
 echo '<input type="submit" name="dump" value="注入">';
 echo '</form></div>';
 }elseif(isset($_GET['step']) && $_GET['step']=='two'){
 if(isset($_POST['dump'])){
 if(isset($_POST['votes']) && $_POST['votes']==1){
 dbquery("alter table `forum_t` add `vote_close` enum('0','1') default '0'");
 dbquery("alter table `forum_t` add `vote` varchar(32) default NULL");
 dbquery("CREATE TABLE `votes_forum` (
  `id` int(11) NOT NULL auto_increment,
  `them` int(11) NOT NULL,
  `var` varchar(32) NOT NULL,
  `num` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_forum` (`them`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
dbquery("CREATE TABLE `votes_user` (
  `id` int(11) NOT NULL auto_increment,
  `them` int(11) NOT NULL,
  `var` varchar(32) NOT NULL,
  `id_user` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
 }
 if(isset($_POST['stena_komm']) && $_POST['stena_komm']==1){
 dbquery("CREATE TABLE `stena_komm` (
  `id` int(11) auto_increment,
  `id_user` int(11) default NULL,
  `msg` varchar(1024) default NULL,
  `time` int(11) default NULL,
  `id_stena` int(11) default NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
  }
 if(isset($_POST['notes_share']) && $_POST['notes_share']==1){
 dbquery("alter table `notes` add `share` enum('0','1') default '0'");
 dbquery("alter table `notes` add `share_type` varchar(12) default 'notes'");
 dbquery("alter table `notes` add `share_id` int(11) default null");
 dbquery("alter table `notes` add `share_id_user` int(11) default null");
 }
 if(isset($_POST['bookmarks']) && $_POST['bookmarks']==1){
 dbquery("CREATE TABLE `bookmarks` (
  `id` int(11) auto_increment,
  `id_user` int(11) default NULL,
  `id_object` int(11) default NULL,
  `time` int(11) default NULL,
  `type` varchar(6) default NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
 }
 if(isset($_POST['opis_r']) && $_POST['opis_r']==1){
 dbquery("alter table `forum_r` add `opis` varchar(256) default NULL");
 }
 }
 echo '<div class="msg">所有指定的表都已填充。删除文件update_1.9.9.php!!! <br/>
 <a href="?step=three">完成</a></div>';
 }elseif(isset($_GET['step']) && $_GET['step']=='three'){
 @unlink('/update_1.9.9.php');
 header('Location: /index.php');
 @unlink('/update_1.9.9.php');
 }
 include_once 'sys/inc/tfoot.php';