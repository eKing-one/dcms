<?



include_once '../../sys/inc/start.php';



include_once '../../sys/inc/compress.php';



include_once '../../sys/inc/sess.php';



include_once '../../sys/inc/home.php';



include_once '../../sys/inc/settings.php';



include_once '../../sys/inc/db_connect.php';



include_once '../../sys/inc/ipua.php';



include_once '../../sys/inc/fnc.php';



include_once '../../sys/inc/adm_check.php';



include_once '../../sys/inc/user.php';







$set['title'] = 'Лидеры';



include_once '../../sys/inc/thead.php';



title();







if (!isset($user))



header("location: /index.php?");







err();



aut();







if (isset($user))



{



if (isset($_POST['stav']) && isset($_POST['msg']))



{



if ($_POST['stav']==1)



{



	$st=1;



	$tm=$time+86400;



}



else if ($_POST['stav']==2)



{



	$st=2;



	$tm=$time+172800;



}



else if ($_POST['stav']==3)



{



	$st=3;



	$tm=$time+259200;



}



else if ($_POST['stav']==4)



{



	$st=4;



	$tm=$time+345600;



}



else if ($_POST['stav']==5)



{



	$st=5;



	$tm=$time+432000;



}



else if ($_POST['stav']==6)



{



	$st=6;



	$tm=$time+518400;



}



else if ($_POST['stav']==7)



{



	$st=7;



	$tm=$time+604800;



}







$msg=my_esc($_POST['msg']);







if ($user['money']>=$st)



{



if (dbresult(dbquery("SELECT COUNT(*) FROM `liders` WHERE `id_user` = '$user[id]'"), 0)==0)



{



	dbquery("INSERT INTO `liders` (`id_user`, `stav`, `msg`, `time`, `time_p`) values('$user[id]', '$st', '".$msg."', '$tm', '$time')");



}



else



{



	dbquery("UPDATE `liders` SET `time` = '$tm', `time_p` = '$time', `msg` = '$msg', `stav` = '$st' WHERE `id_user` = '$user[id]'");



}







dbquery("UPDATE `user` SET `money` = '".($user['money']-$st)."' WHERE `id` = '$user[id]' LIMIT 1");







$_SESSION['message'] = 'Вы успешно стали лидером';



header("Location: /user/liders/index.php?ok");



exit;







}else{



$err='У вас не достаточно средств';



}



}else{



$err='Поле сообщения не может быть пустым';



}



err();







echo '<div class="foot">';



echo '<img src="/style/icons/str2.gif" alt="S"/> <a href="/user/money/">Дополнительные услуги</a> | <b>Стать лидером</b>';



echo '</div>';







echo '<div class="mess">';



echo 'Для того, чтобы попасть в Лидеры необходимо минимум <b style="color:red;">1</b> <b style="color:green;">' . $sMonet[1] . '</b>, эта услуга в течение 1 дня обеспечит 



Ваше пребывание в данном ТОП\'е. Ваше положение в ТОП\'е зависит от кол-ва ' . $sMonet[0] . ' (общем времени пребывания)! 



Помимо этого, Ваша анкета будет ротироваться на страницах Знакомств и Поиска!';



echo '</div>';







echo '<form class="main" method="post" action="?">';



	echo 'Ставка: <select name="stav">



	<option value="1">1</option>



	<option value="2">2</option>



	<option value="3">3</option>



	<option value="4">4</option>



	<option value="5">5</option>



	<option value="6">6</option>



	<option value="7">7</option>



	</select> ' . $sMonet[0] . '<br />';



	



echo 'Подпись (215 символов)<textarea name="msg"></textarea><br />';







echo '<input value="Стать лидером" type="submit" />';



echo '</form>';



}











echo '<div class="foot">';



echo '<img src="/style/icons/str2.gif" alt="S"/> <a href="/user/money/">Дополнительные услуги</a> | <b>Стать лидером</b>';



echo '</div>';











include_once '../../sys/inc/tfoot.php';



?>