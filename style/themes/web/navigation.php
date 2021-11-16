<?


if (isset($user)){


/*


=================================





=================================


*/


echo '<a href="/info.php?"><span class="link_title"><img src="/style/themes/web/images/user.png" alt=""/>
<br/>Моя стр</span></a>';


/*


=================================


Почта


=================================


*/


$k_new=dbresult(dbquery("SELECT COUNT(`mail`.`id`) FROM `mail`


 LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'


 WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` IS NULL OR `users_konts`.`type` = 'common' OR `users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'"),0);


$k_new_fav=dbresult(dbquery("SELECT COUNT(`mail`.`id`) FROM `mail`


 LEFT JOIN `users_konts` ON `mail`.`id_user` = `users_konts`.`id_kont` AND `users_konts`.`id_user` = '$user[id]'


 WHERE `mail`.`id_kont` = '$user[id]' AND (`users_konts`.`type` = 'favorite') AND `mail`.`read` = '0'"),0);





 if ($k_new!=0 && $k_new_fav==0){


 


echo "<a href='/new_mess.php'><span class='link_title'><img src='/style/themes/web/images/mail.png' alt=''/>  <b class='count'>+$k_new</b>
<br/> Почта </span></a>";


}else{


echo "<a href='/konts.php'><span class='link_title'><img src='/style/themes/web/images/mail.png' alt=''/>
<br/>Почта</span></a>";


}


/*


=================================


Лента


=================================


*/


$lenta = dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$user[id]' AND `read` = '0' "),0);


echo "<a href='/user/tape/index.php'><span class='link_title'><img src='/style/themes/web/images/lenta.png' alt=''/>";
$k_l = $lenta;


if($k_l>0)echo " <b class='count'>+$k_l</b>";
echo "<br/>Лента";





echo "</span></a>";





/*


=================================


Обсуждения


=================================


*/


$discuss = dbresult(dbquery("SELECT COUNT(`count`) FROM `discussions` WHERE `id_user` = '$user[id]' AND `count` > '0' "),0); // Обсуждения


$k_l = $discuss;


echo "<a href='/user/discussions/index.php'><span class='link_title'><img src='/style/themes/web/images/disc.png' alt=''/>";
if($k_l>0)echo " <b class='count'>+$k_l</b>";
echo "<br/>Обсуждения";





echo "</span></a>";


/*


=================================


Уведомления


=================================


*/


$k_notif = dbresult(dbquery("SELECT COUNT(`read`) FROM `notification` WHERE `id_user` = '$user[id]' AND `read` = '0'"), 0); // Уведомления


$k_l = $k_notif;


if($k_l>0){


	echo "<a href='/user/notification/index.php'><span class='link_title'><img src='/style/themes/web/images/notif2.png' alt=''/><b class='count'>+$k_l</b>
<br/>Уведомления";





	echo "</span></a>";


}





/*


=================================


Друзья


=================================


*/


$k_f = dbresult(dbquery("SELECT COUNT(id) FROM `frends_new` WHERE `to` = '$user[id]' LIMIT 1"), 0);


if ($k_f>0)


echo "<a href='/user/frends/new.php'><span class='link_title'><img src='/style/themes/web/images/frend.png' alt=''/><b class='count'>+$k_f</b>
<br/>Друзья </span></a>";


else 


echo '<a href="/user/frends/?id='.$user['id'].'"><span class="link_title"><img src="/style/themes/web/images/frend.png" alt=""/>
<br/>Друзья</span></a>';


/*


=================================


Обновить


=================================


*/


echo '<a href="'.htmlspecialchars($_SERVER['REQUEST_URI']).'"><span class="link_title"><img src="/style/themes/web/images/refresh.png"/>
<br/>Обновить</span></a>';


}elseif ($_SERVER['PHP_SELF'] != '/aut.php' && $_SERVER['PHP_SELF'] != '/reg.php'){














echo '<script src="//ulogin.ru/js/ulogin.js"></script>';


$tUlogin = '<div id="uLogin" data-ulogin="display=panel;fields=first_name,last_name,city,sex,photo,photo_big;providers=vkontakte,odnoklassniki,mailru,facebook,yandex;hidden;redirect_uri=http%3A%2F%2F'.$_SERVER['HTTP_HOST'].'/user/connect/loginAPI.php"></div>';





echo '<a href="#user" rel="facebox"><span class="link_title2"><img src="/style/themes/web/images/key.png" alt=""/><br />Авторизация/Регистрация</span></a>';


echo '<div id="user" style="display:none;">';


echo '<div class="mess">


登录 через:<br />' . $tUlogin;


echo '</div>';


echo "<div class = 'foot'>Авторизация</div>";


	echo "<form class='mess' method='post' action='/login.php'>


		Логин:<br /><input type='text' name='nick' maxlength='32' /><br />


		Пароль:<br /><input type='password' name='pass' maxlength='32' /><br />


		<label><input type='checkbox' name='aut_save' value='1' /> Запомнить меня</label><br />


		<input type='submit' value='登录' /> <a href='/pass.php'>Забыли пароль?</a> <br />


		</form><br />";


echo "<div class = 'foot'>Регистрация</div>";


echo "<form class='mess' method='post' action='/reg.php?$passgen'>";


echo "Выберите ник [A-z0-9 -_]:<br /><input type='text' name='nick' maxlength='32' /><br />";


echo "Регистрируясь, Вы автоматически соглашаетесь с <a href='/rules.php'>правилами</a> сайта<br />";


echo "<input type='submit' value='Продолжить' />";


echo "</form><br />";


			


echo'</div>';











}