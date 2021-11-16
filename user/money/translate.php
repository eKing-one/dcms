<?//网页标题

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





if (isset($user))$ank['id'] = intval($_GET['id']);



$ank=get_user($ank['id']);

if(!$ank || $user['id'] == $ank['id']){header("Location: /index.php?".SID);exit;}



if (isset($_GET['act']) && $_POST['money'])

{

$money=abs(intval($_POST['money']));

if ($user['money'] < $money)$err = 'У вас не достаточно средств для перевода';



if (!$err)

{

dbquery("UPDATE `user` SET `money` = '" . ($ank['money'] + $money) . "' WHERE `id` ='$ank[id]';");

dbquery("UPDATE `user` SET `money` = '" . ($user['money'] - $money) . "' WHERE `id` ='$user[id]';");



$msg = "Пользователь [b]".$user['nick']."[/b] перевeл вам средства в колличестве [b] $money [/b] $sMonet[0]! [br]НЕ ЗАБУДЬТЕ СКАЗАТЬ СПАСИБО!";



dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$ank[id]', '$msg', '$time')");



$_SESSION['message'] = 'Перевод успешно выполнен';

header("Location: /info.php?id=$ank[id]");

exit;

}

}

$set['title']='Перевод '.$sMonet[0]; // заголовок страницы

include_once '../../sys/inc/thead.php';

title();

aut();

err();

echo "<div class='foot'>";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php?id=$ank[id]'>$ank[nick]</a> | Перевод<br />";

echo "</div>";



if (isset($user) & $user['money']<=1)

{

echo '<div class="mess">';

	if ($user['pol']==0){

		echo "Извини <b>красавица,</b> ";

	} else {

		echo "<b>Извини братан,</b> ";

	}

		echo "но чтобы переводить $sMonet[2] другим обитателям необходимо набрать минимум <b>2</b> $sMonet[2]<br/>У вас <b>$user[money] </b>$sMonet[0]";

echo '</div>';

}

else

{

echo '<div class="mess">';

echo "Ваши $sMonet[2]: <b>$user[money]</b><br />";

echo '</div>';



echo "<form class='main' action=\"?id=$ank[id]&amp;act\" method=\"post\">";

echo "Количество $sMonet[0]:<br />";

echo "<input type='text' name='money' value='1' /><br />";

echo "<input class='submit' type='submit' value='Перевести' /><br />";

echo "</form>";





}



echo "<div class='foot'>";

echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/info.php?id=$ank[id]'>$ank[nick]</a> | Перевод<br />";

echo "</div>";

include_once '../../sys/inc/tfoot.php';





?>





