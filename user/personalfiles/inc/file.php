<?//网页标题//网页标题




/*




=======================================




Личные файлы юзеров для Dcms-Social




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














$file_id=dbassoc(dbquery("SELECT * FROM `obmennik_files` WHERE `id`='".intval($_GET['id_file'])."' LIMIT 1"));









if ($file_id['id_user']!=$ank['id']){echo '错误!';exit;}









$dir_id=dbassoc(dbquery("SELECT * FROM `obmennik_dir` WHERE `id` = '$file_id[id_dir]' LIMIT 1"));




$ras=$file_id['ras'];




$file=H."sys/obmen/files/$file_id[id].dat";




$name=$file_id['name'];




$size=$file_id['size'];



















/*




================================




Модуль жалобы на пользователя




и его сообщение либо контент




в зависимости от раздела




================================




*/




if (isset($_GET['spam'])  && isset($user))




{




$mess = dbassoc(dbquery("SELECT * FROM `obmennik_komm` WHERE `id` = '".intval($_GET['spam'])."' limit 1"));




$spamer = get_user($mess['id_user']);




if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'files_komm' AND `spam` = '".$mess['msg']."'"),0)==0)




{




if (isset($_POST['msg']))




{




if ($mess['id_user']!=$user['id'])




{




$msg=mysql_real_escape_string($_POST['msg']);









if (strlen2($msg)<3)$err='更详细地说明投诉的原因';




if (strlen2($msg)>1512)$err='文本的长度超过512个字符的限制';









if(isset($_POST['types'])) $types=intval($_POST['types']);




else $types='0'; 




if (!isset($err))




{




dbquery("INSERT INTO `spamus` (`id_object`, `id_user`, `msg`, `id_spam`, `time`, `types`, `razdel`, `spam`) values('$file_id[id]', '$user[id]', '$msg', '$spamer[id]', '$time', '$types', 'files_komm', '".my_esc($mess['msg'])."')");




$_SESSION['message'] = '考虑申请已发出'; 




header("Location: ?id_file=$file_id[id]&spam=$mess[id]&page=".intval($_GET['page'])."");




exit;




}




}




}




}




$set['title']= '申诉'; // заголовок страницы




include_once '../../sys/inc/thead.php';




title();




aut();




err();









if (dbresult(dbquery("SELECT COUNT(*) FROM `spamus` WHERE `id_user` = '$user[id]' AND `id_spam` = '$spamer[id]' AND `razdel` = 'files_komm'"),0)==0)




{




echo "<div class='mess'>虚假信息会导致昵称被屏蔽。 




如果你经常被一个写各种讨厌的东西的人惹恼，你可以把他加入黑名单。</div>";




echo "<form class='nav1' method='post' action='?id_file=$file_id[id]&amp;spam=$mess[id]&amp;page=".intval($_GET['page'])."'>";




echo "<b>用户:</b> ";




echo " ".status($spamer['id'])."  ".group($spamer['id'])." <a href=\"/info.php?id=$spamer[id]\">$spamer[nick]</a>";




echo "".medal($spamer['id'])." ".online($spamer['id'])." (".vremja($mess['time']).")<br />";




echo "<b>违规：</b> <font color='green'>".output_text($mess['msg'])."</font><br />";




echo "原因：<br /><select name='types'>";




echo "<option value='1' selected='selected'>垃圾邮件/广告</option>";




echo "<option value='2' selected='selected'>欺诈行为</option>";




echo "<option value='3' selected='selected'>进攻</option>";




echo "<option value='0' selected='selected'>其他</option>";




echo "</select><br />";




echo "评论:$tPanel";




echo "<textarea name=\"msg\"></textarea><br />";




echo "<input value=\"发送\" type=\"submit\" />";




echo "</form>";




}else{




echo "<div class='mess'>投诉有关<font color='green'>$spamer[nick]</font> 它将在不久的将来考虑。</div>";




}









echo "<div class='foot'>";




echo "<img src='/style/icons/str2.gif' alt='*'> <a href='?id_file=$file_id[id]&amp;page=".intval($_GET['page'])."'>返回</a><br />";




echo "</div>";




include_once '../../sys/inc/tfoot.php';




exit;




}




/*




==================================




The End




==================================




*/









/*------------очищаем счетчик этого обсуждения-------------*/




if (isset($user))




{




dbquery("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1");




}




/*---------------------------------------------------------*/














/*------------------------Мне нравится------------------------*/




if (isset($user) && $ank['id']!=$user['id'] && isset($_GET['like']) && ($_GET['like']==1 || $_GET['like']==0) && dbresult(dbquery("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `id_user` = '$user[id]'"),0)==0)




{




dbquery("INSERT INTO `like_object` (`id_user`, `id_object`, `type`, `like`) VALUES ('$user[id]', '$file_id[id]', 'obmen', '".intval($_GET['like'])."')");




dbquery("UPDATE `user` SET `balls` = '".($ank['balls']+1)."', `rating_tmp` = '".($ank['rating_tmp']+1)."' WHERE `id` = '$ank[id]' LIMIT 1");









}




/*------------------------------------------------------------*/



















/*------------------------Моя музыка--------------------------*/




$music_people = dbresult(dbquery("SELECT COUNT(*) FROM `user_music` WHERE `dir` = 'obmen' AND `id_file` = '$file_id[id]'"),0);




if (isset($user))




$music = dbresult(dbquery("SELECT COUNT(*) FROM `user_music` WHERE `id_user` = '$user[id]' AND `dir` = 'obmen' AND `id_file` = '$file_id[id]'"),0);









if (isset($user) && isset($_GET['play']) && ($_GET['play']==1 || $_GET['play']==0) && ($file_id['ras']=='mp3' || $file_id['ras']=='wav' || $file_id['ras']=='ogg'))




{




	if ($_GET['play']==1 && $music==0) // Добавляем в плейлист




	{




	dbquery("INSERT INTO `user_music` (`id_user`, `id_file`, `dir`) VALUES ('$user[id]', '$file_id[id]', 'obmen')");




	dbquery("UPDATE `user` SET `balls` = '".($ank['balls']+1)."', `rating_tmp` = '".($ank['rating_tmp']+1)."' WHERE `id` = '$ank[id]' LIMIT 1");




	$_SESSION['message']='Трек добавлен в плейлист';




	}




	




	if ($_GET['play']==0 && $music==1) // Удаляем из плейлиста




	{




	dbquery("DELETE FROM `user_music` WHERE `id_user` = '$user[id]' AND `id_file` = '$file_id[id]' AND `dir` = 'obmen' LIMIT 1");




	dbquery("UPDATE `user` SET `rating_tmp` = '".($ank['rating_tmp']-1)."' WHERE `id` = '$ank[id]' LIMIT 1");




	$_SESSION['message']='Трек удален из плейлиста';




	}




	header ("Location: ?id_file=$file_id[id]");




	exit;




}




/*------------------------------------------------------------*/




 




$set['title']= htmlspecialchars($file_id['name']); // заголовок страницы




include_once '../../sys/inc/thead.php';




title();









if ((user_access('obmen_komm_del') || $ank['id'] == $user['id']) && isset($_GET['del_post']) && dbresult(dbquery("SELECT COUNT(*) FROM `obmennik_komm` WHERE `id` = '".intval($_GET['del_post'])."' AND `id_file` = '$file_id[id]'"),0))




{




dbquery("DELETE FROM `obmennik_komm` WHERE `id` = '".intval($_GET['del_post'])."' LIMIT 1");




$_SESSION['message']='Комментарий успешно удален';




header ("Location: ?id_file=$file_id[id]");




}









if (isset($user))




dbquery("UPDATE `notification` SET `read` = '1' WHERE `type` = 'files_komm' AND `id_user` = '$user[id]' AND `id_object` = '$file_id[id]'");









if (isset($_POST['msg']) && isset($user))




{




$msg=$_POST['msg'];




if (isset($_POST['translit']) && $_POST['translit']==1)$msg=translit($msg);









$mat=antimat($msg);




if ($mat)$err[]='В тексте сообщения обнаружен мат: '.$mat;









if (strlen2($msg)>1024){$err[]='Сообщение слишком длинное';}




elseif (strlen2($msg)<2){$err[]='Короткое сообщение';}




elseif (dbresult(dbquery("SELECT COUNT(*) FROM `obmennik_komm` WHERE `id_file` = '$file_id[id]' AND `id_user` = '$user[id]' AND `msg` = '".mysql_escape_string($msg)."' LIMIT 1"),0)!=0){$err='Ваше сообщение повторяет предыдущее';}




elseif(!isset($err)){




$ank=get_user($file_id['id_user']);









/*




====================================




Обсуждения




====================================




*/




$q = dbquery("SELECT * FROM `frends` WHERE `user` = '".$file_id['id_user']."' AND `i` = '1'");




while ($f = dbarray($q))




{




$a=get_user($f['frend']);




$discSet = dbarray(dbquery("SELECT * FROM `discussions_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); // Общая настройка обсуждений









if ($f['disc_forum']==1 && $discSet['disc_forum']==1) /* Фильтр рассылки */




{









	// друзьям автора




	if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"),0)==0)




	{




	if ($file_id['id_user']!=$a['id'] || $a['id'] != $user['id'])




	dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$a[id]', '$file_id[id_user]', 'obmen', '$time', '$file_id[id]', '1')");




	}




	else




	{




	$disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"));




	if ($file_id['id_user']!=$a['id'] || $a['id']!= $user['id'])




	dbquery("UPDATE `discussions` SET `count` = '".($disc['count']+1)."', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1");




	}









}









}









// отправляем автору




if (dbresult(dbquery("SELECT COUNT(*) FROM `discussions` WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"),0)==0)




{




if ($file_id['id_user'] != $user['id'])




dbquery("INSERT INTO `discussions` (`id_user`, `avtor`, `type`, `time`, `id_sim`, `count`) values('$file_id[id_user]', '$file_id[id_user]', 'obmen', '$time', '$file_id[id]', '1')");




}




else




{




$disc = dbarray(dbquery("SELECT * FROM `discussions` WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1"));




if ($file_id['id_user'] != $user['id'])




dbquery("UPDATE `discussions` SET `count` = '".($disc['count']+1)."', `time` = '$time' WHERE `id_user` = '$file_id[id_user]' AND `type` = 'obmen' AND `id_sim` = '$file_id[id]' LIMIT 1");




}









		/*




		==========================




		Уведомления об ответах




		==========================




		*/




		if (isset($user) && $respons==TRUE){




		$notifiacation=dbassoc(dbquery("SELECT * FROM `notification_set` WHERE `id_user` = '".$ank_otv['id']."' LIMIT 1"));




			




			if ($notifiacation['komm'] == 1 && $ank_otv['id'] != $user['id'])




			dbquery("INSERT INTO `notification` (`avtor`, `id_user`, `id_object`, `type`, `time`) VALUES ('$user[id]', '$ank_otv[id]', '$file_id[id]', 'files_komm', '$time')");




		




		}














dbquery("INSERT INTO `obmennik_komm` (`id_file`, `id_user`, `time`, `msg`) values('$file_id[id]', '$user[id]', '$time', '".my_esc($msg)."')");




dbquery("UPDATE `user` SET `balls` = '".($user['balls']+1)."', `rating_tmp` = '".($user['rating_tmp']+1)."' WHERE `id` = '$user[id]' LIMIT 1");




$_SESSION['message']='Сообщение успешно добавлено';




header ("Location: ?id_file=$file_id[id]");




exit;




}




}









err();




aut(); // форма авторизации



















echo "<div class='foot'>";




echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'<a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">文件</a>':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; <a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">'.htmlspecialchars($dir['name']).'</a>')."";




echo "</div>";














/*--------------------Папка под паролем--------------------*/




if ($dir['pass']!=NULL)




{




if (isset($_POST['password']))




{




$_SESSION['pass']=my_esc($_POST['password']);




if ($_SESSION['pass']!=$dir['pass'])




{$_SESSION['message'] = 'Неверный пароль'; $_SESSION['pass']=NULL;}




header("Location: ?");




}









if (!user_access('obmen_dir_edit') && ($user['id']!=$ank['id'] && $_SESSION['pass']!=$dir['pass']))




{




echo '<form action="?id_file='.$file_id['id'].'" method="POST">Пароль: <br />		<input type="pass" name="password" value="" /><br />		




<input type="submit" value="登录"/></form>';




echo "<div class='foot'>";




echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'文件':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; '.htmlspecialchars($dir['name']))."";




echo "</div>";




include_once '../../sys/inc/tfoot.php';




exit;




}




}




/*---------------------------------------------------------*/














 // Инклудим редактор




if (isset($user) && user_access('obmen_file_edit') || $ank['id']==$user['id'])




include "inc/file.edit.php";









 // Инклудим удаление




if (isset($user) && user_access('obmen_file_delete') || $ank['id']==$user['id'])




include "inc/file.delete.php";














echo '<div class="main">';




if ($dir_id['my']!=1)




{




if ($user['id']==$file_id['id_user'])




echo '<img src="/style/icons/z.gif" alt="*"> Зона обмена: <a href="/obmen'.$dir_id['dir'].'">'.$dir_id['name'].'</a> <a href="/obmen/?trans='.$file_id['id'].'"><img src="/style/icons/edit.gif" alt="*"></a><br />';




else




echo '<img src="/style/icons/z.gif" alt="*"> Зона обмена: <a href="/obmen'.$dir_id['dir'].'">'.$dir_id['name'].'</a><br /> ';




}














include_once H.'obmen/inc/icon14.php';




echo htmlspecialchars($file_id['name']).'.'.$ras.' ';




if ($file_id['metka'] == 1)echo '<font color=red><b>(18+)</b></font> ';




echo vremja($file_id['time']).'<br />';




echo '</div>';









if (($user['abuld'] == 1 || $file_id['metka'] == 0 || $file_id['id_user'] == $user['id'])) // Метка 18+ 




{




echo '<div class="main">';




if(is_file(H."obmen/inc/file/$ras.php"))include H."obmen/inc/file/$ras.php";




else




include_once H.'obmen/inc/file.php';




echo '</div>';




}elseif (!isset($user)){




echo '<div class="mess">';




echo '<img src="/style/icons/small_adult.gif" alt="*"><br /> Данный файл содержит изображения эротического характера. Только зарегистрированные пользователи старше 18 лет могут просматривать такие файлы. <br />';




echo '<a href="/aut.php">Вход</a> | <a href="/reg.php">Регистрация</a>';




echo '</div>';




}else{




echo '<div class="mess">';




echo '<img src="/style/icons/small_adult.gif" alt="*"><br /> 




	Данный файл содержит изображения эротического характера. 




	Если Вас это не смущает и Вам 18 или более лет, то можете <a href="?id_file='.$file_id['id'].'&amp;sess_abuld=1">продолжить просмотр</a>. 




	Или Вы можете отключить предупреждения в <a href="/user/info/settings.php">настройках</a>.';




	echo '</div>';




}














/*----------------------листинг-------------------*/




$listr = dbassoc(dbquery("SELECT * FROM `obmennik_files` WHERE `my_dir` = '$dir[id]' AND `id` < '$file_id[id]' ORDER BY `id` DESC LIMIT 1"));




$list = dbassoc(dbquery("SELECT * FROM `obmennik_files` WHERE `my_dir` = '$dir[id]' AND `id` > '$file_id[id]' ORDER BY `id`  ASC LIMIT 1"));




echo '<div class="c2" style="text-align: center;">';




if (isset($list['id'])) echo '<span class="page">'.($list['id']?'<a href="?id_file='.$list['id'].'">&laquo; Пред.</a> ':'&laquo; Пред. ').'</span>';









$k_1=dbresult(dbquery("SELECT COUNT(*) FROM `obmennik_files` WHERE `id` > '$file_id[id]' AND `my_dir` = '$dir[id]'"),0)+1;




$k_2=dbresult(dbquery("SELECT COUNT(*) FROM `obmennik_files` WHERE `my_dir` = '$dir[id]'"),0);




echo ' ('.$k_1.' из '.$k_2.') ';









if (isset($listr['id'])) echo '<span class="page">'.($listr['id']?'<a href="?id_file='.$listr['id'].'">След. &raquo;</a>':' След. &raquo;').'</span>';




echo '</div>';




/*----------------------alex-borisi---------------*/



















if (($user['abuld'] == 1 || $file_id['metka'] == 0 || $file_id['id_user'] == $user['id'])) // Метка 18+ 




{




/*----------------Действия над файлом-------------*/




if (user_access('obmen_file_edit') || $user['id']==$file_id['id_user'])




{




	echo '<div class="main">';




		if ($user['id']==$file_id['id_user'] && $dir_id['my']==1)echo '[<a href="/obmen/?trans='.$file_id['id'].'"><img src="/style/icons/z.gif" alt="*"> в зону</a>]';




	echo ' [<img src="/style/icons/edit.gif" alt="*"> <a href="?id_file='.$file_id['id'].'&amp;edit">ред.</a>]';




	echo ' [<img src="/style/icons/delete.gif" alt="*"> <a href="?id_file='.$file_id['id'].'&amp;delete">удл.</a>]';




	echo '</div>';




}




/*----------------------alex-borisi---------------*/














echo '<div class="main">';




if (isset($user) && $ank['id'] != $user['id'] && dbresult(dbquery("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `id_user` = '$user[id]'"),0)==0)




{




echo '[<img src="/style/icons/like.gif" alt="*"> <a href="?id_file='.$file_id['id'].'&amp;like=1">Мне нравится</a>] ';




echo '[<a href="?id_file='.$file_id['id'].'&amp;like=0"><img src="/style/icons/dlike.gif" alt="*"></a>]';









}else{




echo '[<img src="/style/icons/like.gif" alt="*"> 




'.dbresult(dbquery("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `like` = '1'"),0).'] ';




echo '[<img src="/style/icons/dlike.gif" alt="*"> 




'.dbresult(dbquery("SELECT COUNT(*) FROM `like_object` WHERE `id_object` = '$file_id[id]' AND `type` = 'obmen' AND `like` = '0'"),0).']';




}




echo '</div>';









echo '<div class="main">';




if ($file_id['ras']=='jar')




echo '<img src="/style/icons/d.gif" alt="*"> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'">Скачать JAR ('.size_file($size).')</a> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.jad">JAD</a> <br />';




else




echo '<img src="/style/icons/d.gif" alt="*"> <a href="/obmen'.$dir_id['dir'].$file_id['id'].'.'.$file_id['ras'].'">Скачать ('.size_file($size).')</a><br />';




echo 'Скачан ('.$file_id['k_loads'].')';




echo '</div>';














/*-------------------Моя музыка---------------------*/




if (isset($user) && ($file_id['ras']=='mp3' || $file_id['ras']=='wav' || $file_id['ras']=='ogg'))




{




echo '<div class="main">';




if ($music==0)




echo '<a href="?id_file='.$file_id['id'].'&amp;play=1"><img src="/style/icons/play.png" alt="*"></a> ('.$music_people.')';




else




echo '<a href="?id_file='.$file_id['id'].'&amp;play=0"><img src="/style/icons/play.png" alt="*"></a> ('.$music_people.') <img src="/style/icons/ok.gif" alt="*">';




echo '</div>';




}




/*--------------------------------------------------*/




}




include_once 'inc/komm.php'; // комментарии









echo "<div class='foot'>";




echo "<img src='/style/icons/up_dir.gif' alt='*'> ".($dir['osn']==1?'<a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">文件</a>':'')." ".user_files($dir['id_dires'])." ".($dir['osn']==1?'':'&gt; <a href="/user/personalfiles/'.$ank['id'].'/'.$dir['id'].'/">'.htmlspecialchars($dir['name']).'</a>')."";




echo "</div>";









?>