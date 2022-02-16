<?



/*



=======================================



Лента друзей для Dcms-Social



Автор: Искатель



---------------------------------------



Этот скрипт распостроняется по лицензии



движка Dcms-Social. 



При использовании указывать ссылку на



оф. сайт http://dcms-social.ru



---------------------------------------



Контакты



ICQ: 587863132



http://dcms-social.ru



=======================================



*/



include_once '../../sys/inc/start.php';



include_once '../../sys/inc/compress.php';



include_once '../../sys/inc/sess.php';



include_once '../../sys/inc/home.php';



include_once '../../sys/inc/settings.php';



include_once '../../sys/inc/db_connect.php';



include_once '../../sys/inc/ipua.php';



include_once '../../sys/inc/fnc.php';



include_once '../../sys/inc/user.php';







only_reg();



$set['title']='设置提要';



include_once '../../sys/inc/thead.php';



title();







$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '".$user['id']."' LIMIT 1"));







if (isset($_POST['save'])){



 // Лента фото



if (isset($_POST['lenta_foto']) && ($_POST['lenta_foto']==0 || $_POST['lenta_foto']==1))



{



dbquery("UPDATE `tape_set` SET `lenta_foto` = '".intval($_POST['lenta_foto'])."' WHERE `id_user` = '$user[id]'");



}



 // Лента файлов



if (isset($_POST['lenta_files']) && ($_POST['lenta_files']==0 || $_POST['lenta_files']==1))



{



dbquery("UPDATE `tape_set` SET `lenta_files` = '".intval($_POST['lenta_files'])."' WHERE `id_user` = '$user[id]'");



}



 // Лента смены аватара



if (isset($_POST['lenta_avatar']) && ($_POST['lenta_avatar']==0 || $_POST['lenta_avatar']==1))



{



dbquery("UPDATE `tape_set` SET `lenta_avatar` = '".intval($_POST['lenta_avatar'])."' WHERE `id_user` = '$user[id]'");



}



 // Лента новых друзей



if (isset($_POST['lenta_frends']) && ($_POST['lenta_frends']==0 || $_POST['lenta_frends']==1))



{



dbquery("UPDATE `tape_set` SET `lenta_frends` = '".intval($_POST['lenta_frends'])."' WHERE `id_user` = '$user[id]'");



}



 // Лента статусов



if (isset($_POST['lenta_status']) && ($_POST['lenta_status']==0 || $_POST['lenta_status']==1))



{



dbquery("UPDATE `tape_set` SET `lenta_status` = '".intval($_POST['lenta_status'])."' WHERE `id_user` = '$user[id]'");



}



 // Лента оценок статуса



if (isset($_POST['lenta_status_like']) && ($_POST['lenta_status_like']==0 || $_POST['lenta_status_like']==1))



{



dbquery("UPDATE `tape_set` SET `lenta_status_like` = '".intval($_POST['lenta_status_like'])."' WHERE `id_user` = '$user[id]'");



}



 // Лента дневников



if (isset($_POST['lenta_notes']) && ($_POST['lenta_notes']==0 || $_POST['lenta_notes']==1))



{



dbquery("UPDATE `tape_set` SET `lenta_notes` = '".intval($_POST['lenta_notes'])."' WHERE `id_user` = '$user[id]'");



}



 // Лента форума



if (isset($_POST['lenta_forum']) && ($_POST['lenta_forum']==0 || $_POST['lenta_forum']==1))



{



dbquery("UPDATE `tape_set` SET `lenta_forum` = '".intval($_POST['lenta_forum'])."' WHERE `id_user` = '$user[id]'");



}











$_SESSION['message'] = '更改已成功接受';



header('Location: settings.php');



exit;



}



err();



aut();







echo "<div id='comments' class='menus'>";







echo "<div class='webmenu'>";



echo "<a href='/user/info/settings.php'>普通</a>";



echo "</div>"; 







echo "<div class='webmenu last'>";



echo "<a href='/user/tape/settings.php' class='activ'>录音带</a>";



echo "</div>"; 







echo "<div class='webmenu last'>";



echo "<a href='/user/discussions/settings.php'>讨论</a>";



echo "</div>"; 







echo "<div class='webmenu last'>";



echo "<a href='/user/notification/settings.php'>通知书</a>";



echo "</div>"; 











echo "<div class='webmenu last'>";



echo "<a href='/user/info/settings.privacy.php' >私隐保护</a>";



echo "</div>"; 



echo "<div class='webmenu last'>";echo "<a href='/user/info/secure.php' >密码</a>";echo "</div>"; 







echo "</div>";











echo "<form action='?' method=\"post\">";



 // Лента друзей



echo "<div class='mess'>";



echo "关于新朋友的通知";



echo "</div>";







echo "<div class='nav1'>";



echo "<input name='lenta_frends' type='radio' ".($lentaSet['lenta_frends']==1?' checked="checked"':null)." value='1' /> 是的 ";



echo "<input name='lenta_frends' type='radio' ".($lentaSet['lenta_frends']==0?' checked="checked"':null)." value='0' /> 否定 ";



echo "</div>";



 // Лента Дневников



echo "<div class='mess'>";



echo "关于新日记的通知";



echo "</div>";







echo "<div class='nav1'>";



echo "<input name='lenta_notes' type='radio' ".($lentaSet['lenta_notes']==1?' checked="checked"':null)." value='1' /> 是的 ";



echo "<input name='lenta_notes' type='radio' ".($lentaSet['lenta_notes']==0?' checked="checked"':null)." value='0' /> 否定 ";



echo "</div>";







 // Лента темах



echo "<div class='mess'>";



echo "关于论坛中新主题的通知";



echo "</div>";







echo "<div class='nav1'>";



echo "<input name='lenta_forum' type='radio' ".($lentaSet['lenta_forum']==1?' checked="checked"':null)." value='1' /> 是的 ";



echo "<input name='lenta_forum' type='radio' ".($lentaSet['lenta_forum']==0?' checked="checked"':null)." value='0' /> 否定 ";



echo "</div>";







 // Лента фото



echo "<div class='mess'>";



echo "关于新照片的通知";



echo "</div>";







echo "<div class='nav1'>";



echo "<input name='lenta_foto' type='radio' ".($lentaSet['lenta_foto']==1?' checked="checked"':null)." value='1' /> 是的 ";



echo "<input name='lenta_foto' type='radio' ".($lentaSet['lenta_foto']==0?' checked="checked"':null)." value='0' /> 否定 ";



echo "</div>";



 // Лента о смене аватара



echo "<div class='mess'>";



echo "有关更改头像的通知";



echo "</div>";







echo "<div class='nav1'>";



echo "<input name='lenta_avatar' type='radio' ".($lentaSet['lenta_avatar']==1?' checked="checked"':null)." value='1' /> 是的 ";



echo "<input name='lenta_avatar' type='radio' ".($lentaSet['lenta_avatar']==0?' checked="checked"':null)." value='0' /> 否定 ";



echo "</div>";



 // Лента файлов



echo "<div class='mess'>";



echo "关于新文件的通知";



echo "</div>";







echo "<div class='nav1'>";



echo "<input name='lenta_files' type='radio' ".($lentaSet['lenta_files']==1?' checked="checked"':null)." value='1' /> 是的 ";



echo "<input name='lenta_files' type='radio' ".($lentaSet['lenta_files']==0?' checked="checked"':null)." value='0' /> 否定 ";



echo "</div>";







 // Лента статусов



echo "<div class='mess'>";



echo "关于新状态的通知";



echo "</div>";







echo "<div class='nav1'>";



echo "<input name='lenta_status' type='radio' ".($lentaSet['lenta_status']==1?' checked="checked"':null)." value='1' /> 是的 ";



echo "<input name='lenta_status' type='radio' ".($lentaSet['lenta_status']==0?' checked="checked"':null)." value='0' /> 否定 ";



echo "</div>";







 // Лента оценок статуса



echo "<div class='mess'>";



echo "有关的通知 \"Like\" 对朋友的状态";



echo "</div>";







echo "<div class='nav1'>";



echo "<input name='lenta_status_like' type='radio' ".($lentaSet['lenta_status_like']==1?' checked="checked"':null)." value='1' /> 是的 ";



echo "<input name='lenta_status_like' type='radio' ".($lentaSet['lenta_status_like']==0?' checked="checked"':null)." value='0' /> 否定 ";



echo "</div>";







echo "<div class='main'>";



echo "<input type='submit' name='save' value='保存' />";



echo "</div>";







echo "</form>";







echo "<div class=\"foot\">\n";



echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php?id=$user[id]'>$user[nick]</a> | \n";



echo '<b>录音带</b>';



echo "</div>\n";



	



include_once '../../sys/inc/tfoot.php';



?>