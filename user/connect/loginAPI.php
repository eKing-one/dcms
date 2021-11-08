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
include_once '../../sys/inc/shif.php';

if (isset($_POST['token']) && !isset($user) && $users['network'] && $users['identity'] && $_POST['loginAPI'] == true)
{
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `type_reg` = '" . $users['network'] . "' AND `identity` = '" . $users['identity'] . "'"),0) == 0)
{
/*
================================
Имя к id и пол
================================
*/
if ($users['network'] == 'odnoklassniki')
$idi = 'ok';
else
$idi = null;

if ($users['sex'] == 2) $pol = 1;
else $pol = 0;
/*
================================
Создаем ник
================================
*/
$identity = $users['identity'];
$identity = str_replace('http://www.facebook.com', '', $identity);
$identity = str_replace('http://openid.yandex.ru', '', $identity);
$identity = str_replace('http://vk.com', '', $identity);
$identity = str_replace('http://odnoklassniki.ru', '', $identity);
$identity = str_replace('http://my.mail.ru/mail', '', $identity);
$identity = str_replace('/', '', $identity);
$identity = str_replace('.', '', $identity);
$identity = $idi . $identity;

/*
================================
Проверяем наличие ника в базе
если есть то добавляем случайное 
число
================================
*/
if (dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `nick` = '" . $identity . "'"),0)!=0)
$identity = $identity . '_' . mt_rand(0000, 9999);
/*
================================
Регаем пользователя
================================
*/
$pass = $passgen;

dbquery("INSERT INTO `user` (`set_nick`, `nick`, `pass`, `date_reg`, `date_last`, `pol`, `ank_city`, `ank_name`, `identity`, `type_reg`)
 values('1', '$identity', '" . shif($pass) . "', '$time', '$time', '" . $pol . "', '" . $users['city'] . "', '" . $users['first_name'] . "', '" . $users['identity'] . "', '" . $users['network'] . "')",$db);

$user=dbassoc(dbquery("SELECT * FROM `user` WHERE `nick` = '". $identity ."' AND `pass` = '". shif($pass) ."' LIMIT 1"));// отправка сообщения
$msgg = "Уважаем".($pol == 1 ? "ый" : "ая")." $users[first_name], поздравляем с успешной регистрацией на сайте!  ".($pol == 1 ? ".дружба." : ".ромашки.")." [br] Ваши регистрационные данные: [br] логин: $identity пароль: $pass . [br]Изменить свой ник вы можете [url=/user/info/edit.php?set=nick]ТУТ[/url] [br]И в целях безопасности смените пароль [url=/secure.php]ТУТ[/url]";
dbquery("INSERT INTO `mail` (`id_user`, `id_kont`, `msg`, `time`) values('0', '$user[id]', '".my_esc($msgg)."', '$time')");

$_SESSION['id_user']=$user['id'];
setcookie('id_user', $user['id'], time()+60*60*24*365);
setcookie('pass', cookie_encrypt($pass,$user['id']), time()+60*60*24*365);
/*
================================
Загружаем фото
================================
*/
$photo = $users['photo_big'];

dbquery("INSERT INTO `gallery` (`id_user`, `name`) values('$user[id]', 'Личные фото')");

$gallery=dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id_user` = '$user[id]'  LIMIT 1"));

if ($photo) // Наличие фото
{
if ($imgc=@imagecreatefromstring(file_get_contents($photo)))
{
$name=$identity; // имя файла без расширения)),1);
$img_x=imagesx($imgc);
$img_y=imagesy($imgc);

if (!isset($err)){
dbquery("INSERT INTO `gallery_foto` (`id_gallery`, `name`, `ras`, `type`, `id_user`,`avatar`) values ('$gallery[id]', '$name', 'jpg', 'image/jpeg', '$user[id]','1')");

$id_foto=mysql_insert_id();
dbquery("UPDATE `gallery` SET `time` = '$time' WHERE `id` = '$gallery[id]' LIMIT 1");

$fot_id=$id_foto;
if ($img_x==$img_y)
{
$dstW=48; // ширина
$dstH=48; // высота 
}
elseif ($img_x>$img_y)
{
$prop=$img_x/$img_y;
$dstW=48;
$dstH=ceil($dstW/$prop);
}
else
{
$prop=$img_y/$img_x;
$dstH=48;
$dstW=ceil($dstH/$prop);
}

$screen=imagecreatetruecolor($dstW, $dstH);
imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
//imagedestroy($imgc);
imagejpeg($screen,H."sys/gallery/48/$id_foto.jpg",90);
@chmod(H."sys/gallery/48/$id_foto.jpg",0777);
imagedestroy($screen);

if ($img_x==$img_y)
{
$dstW=128; // ширина
$dstH=128; // высота 
}
elseif ($img_x>$img_y)
{
$prop=$img_x/$img_y;
$dstW=128;
$dstH=ceil($dstW/$prop);
}
else
{
$prop=$img_y/$img_x;
$dstH=128;
$dstW=ceil($dstH/$prop);
}

$screen=imagecreatetruecolor($dstW, $dstH);
imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
//imagedestroy($imgc);
$screen=img_copyright($screen); // наложение копирайта
imagejpeg($screen,H."sys/gallery/128/$id_foto.jpg",90);
@chmod(H."sys/gallery/128/$id_foto.jpg",0777);
imagedestroy($screen);

if ($img_x>640 || $img_y>640){
if ($img_x==$img_y)
{
$dstW=640; // ширина
$dstH=640; // высота 
}
elseif ($img_x>$img_y)
{
$prop=$img_x/$img_y;
$dstW=640;
$dstH=ceil($dstW/$prop);
}
else
{
$prop=$img_y/$img_x;
$dstH=640;
$dstW=ceil($dstH/$prop);
}

$screen=imagecreatetruecolor($dstW, $dstH);
imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
//imagedestroy($imgc);
$screen=img_copyright($screen); // наложение копирайта
imagejpeg($screen,H."sys/gallery/640/$id_foto.jpg",90);
imagedestroy($screen);
$imgc=img_copyright($imgc); // наложение копирайта
imagejpeg($imgc,H."sys/gallery/foto/$id_foto.jpg",90);
@chmod(H."sys/gallery/foto/$id_foto.jpg",0777);
}
else
{
$imgc=img_copyright($imgc); // наложение копирайта

imagejpeg($imgc,H."sys/gallery/640/$id_foto.jpg",90);
imagejpeg($imgc,H."sys/gallery/foto/$id_foto.jpg",90);
@chmod(H."sys/gallery/foto/$id_foto.jpg",0777);
}

@chmod(H."sys/gallery/640/$id_foto.jpg",0777);

imagedestroy($imgc);

crop(H."sys/gallery/640/$id_foto.jpg", H."sys/gallery/50/$id_foto.tmp.jpg");
resize(H."sys/gallery/50/$id_foto.tmp.jpg", H."sys/gallery/50/$id_foto.jpg", 50, 50);

@chmod(H."sys/gallery/50/$id_foto.jpg",0777);
@unlink(H."sys/gallery/50/$id_foto.tmp.jpg");

dbquery("UPDATE `gallery_foto` SET `avatar` = '1' WHERE `id` = '$id_foto' LIMIT 1");

}
}
}

	dbquery("update `user` set `wall` = '0' where `id` = '$user[id]' limit 1");

	
$_SESSION['message'] = 'Поздравляем с успешной регистрацией!';
header('Location: /umenu.php?login=' . $user['nick'] . '&pass=' . $pass);
exit;

}else{

	$user=dbassoc(dbquery("SELECT * FROM `user` WHERE `type_reg` = '" . $users['network'] . "' AND `identity` = '" . $users['identity'] . "' LIMIT 1"));
	
    $_SESSION['id_user'] = $user['id'];

    setcookie('id_user', $user['id'], time() + 60 * 60 * 24 * 365);	
    dbquery("UPDATE `user` SET `date_aut` = " . time() . " WHERE `id` = '$user[id]' LIMIT 1");
    dbquery("UPDATE `user` SET `date_last` = " . time() . " WHERE `id` = '$user[id]' LIMIT 1");
	
	$_SESSION['message'] = 'Вы успешно авторизовались';
	header("Location: /info.php");
	exit;
	
}
}

?>