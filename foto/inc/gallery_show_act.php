<?
// Удаление альбома
if ((user_access('foto_alb_del') || isset($user) && $user['id']==$ank['id']) && isset($_GET['act']) && $_GET['act']=='delete' && isset($_GET['ok']))
{
	$q = dbquery("SELECT * FROM `gallery_foto` WHERE `id_gallery` = '$gallery[id]'");
	
	while ($post = dbassoc($q))
	{
		@unlink(H."sys/gallery/48/$post[id].jpg");
		@unlink(H."sys/gallery/50/$post[id].jpg");
		@unlink(H."sys/gallery/128/$post[id].jpg");
		@unlink(H."sys/gallery/640/$post[id].jpg");
		@unlink(H."sys/gallery/foto/$post[id].jpg");
		dbquery("DELETE FROM `gallery_komm` WHERE `id_foto` = '$post[id]' LIMIT 1");
		dbquery("DELETE FROM `gallery_foto` WHERE `id` = '$post[id]' LIMIT 1");
		dbquery("DELETE FROM `mark_foto` WHERE `id_foto` = '$post[id]' LIMIT 1");
	}
	
	if ($user['id'] != $ank['id'])
	admin_log('Фотогалерея','相片册',"Удаление альбома " . text($gallery['name']) . " (фотографий: ".dbrows($q).")");
	dbquery("DELETE FROM `gallery` WHERE `id` = '$gallery[id]' LIMIT 1");
	$_SESSION['message'] = 'Фотоальбом успешно удален';
	header("Location: /foto/$ank[id]/");
	exit;
}

// Загрузка фото
if (isset($user) && $user['id'] == $ank['id'] && isset($_FILES['file']))
{

if ($imgc = @imagecreatefromstring(file_get_contents($_FILES['file']['tmp_name'])))
{

$name = $_POST['name'];

if ($name == null)
$name = esc(stripcslashes(htmlspecialchars(preg_replace('#\.[^\.]*$#i', NULL, $_FILES['file']['name'])))); 
   
if (!preg_match("#^([A-zА-я0-9\-\_\ ])+$#ui",$name))$err = 'В названии фото присутствуют запрещенные символы';
if (strlen2($name) < 3)$err = 'Короткое название';
if (strlen2($name) > 32)$err = 'Название не должно быть длиннее 32-х символов';

$name = my_esc($name);

if (isset($_POST['metka']) && ($_POST['metka'] == 0 || $_POST['metka'] == 1))
$metka = $_POST['metka'];
else 
{
	$metka = 0;
}

$msg = $_POST['opis'];

if (strlen2($msg) > 1024)$err = 'Длина описания превышает предел в 1024 символов';
$msg = my_esc($msg);
$img_x = imagesx($imgc);
$img_y = imagesy($imgc);
if ($img_x > $set['max_upload_foto_x'] || $img_y>$set['max_upload_foto_y'])
   $err = 'Размер изображения превышает ограничения в '.$set['max_upload_foto_x'].'*'.$set['max_upload_foto_y'];

if (!isset($err))
{

if (isset($_GET['avatar']))
{
	dbquery("UPDATE `gallery_foto` SET `avatar` = '0' WHERE `id_user` = '$user[id]'");
	dbquery("INSERT INTO `gallery_foto` (`id_gallery`, `name`, `ras`, `type`, `opis`, `id_user`,`avatar`, `metka`, `time`) values ('$gallery[id]', '$name', 'jpg', 'image/jpeg', '$msg', '$user[id]','1', '$metka', '$time')");
}
else
{
	dbquery("INSERT INTO `gallery_foto` (`id_gallery`, `name`, `ras`, `type`, `opis`, `id_user`, `metka`, `time`) values ('$gallery[id]', '$name', 'jpg', 'image/jpeg', '$msg', '$user[id]', '$metka', '$time')");
}


$id_foto = mysql_insert_id();

dbquery("UPDATE `gallery` SET `time` = '$time' WHERE `id` = '$gallery[id]' LIMIT 1");
		   
$q = dbquery("SELECT * FROM `frends` WHERE `user` = '$user[id]' AND `lenta_foto` = '1' AND `i` = '1'");

$foto['id'] = $id_foto;

/*
* Лента друзей
*/

dbquery("UPDATE `tape` SET `count` = '0' WHERE  `type` = 'album' AND `read` = '1' AND `id_file` = '$gallery[id]'");

$q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $gallery['id_user'] . "' AND `i` = '1'");

while ($f = dbarray($q))
{
	$a = get_user($f['frend']);
	
	// Общая настройка ленты
	$lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '".$a['id']."' LIMIT 1")); 
	
	/* Фильтр рассылки */	
	if  ($f['lenta_foto'] == 1 && $lentaSet['lenta_foto'] == 1)
	{
		/* Если грузим со страницы то отправляем как смену аватара */
		if (isset($_GET['avatar']))
		{
			if ($a['id'] != $user['id'] && $foto['id'] != $avatar['id'])
			dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`, `avatar`) values('$a[id]', '$gallery[id_user]', 'avatar', '$time', '$foto[id]', '1', '$avatar[id]')"); 
		}
		else
		{ 
			/* Если нет то просто шлем в ленту как новое фото */

			if (dbresult(dbquery("SELECT COUNT(*) FROM `tape` WHERE `id_user` = '$a[id]' AND `type` = 'album' AND `id_file` = '$gallery[id]' LIMIT 1"),0)==0)
			{
				dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`, `count`) values('$a[id]', '$gallery[id_user]', 'album', '$time', '$gallery[id]', '1')"); 
			}
			else
			{
				$tape = dbarray(dbquery("SELECT * FROM `tape` WHERE `type` = 'album' AND `id_file` = '$gallery[id]'"));
				dbquery("UPDATE `tape` SET `count` = '".($tape['count']+1)."', `read` = '0', `time` = '$time' WHERE `id_user` = '$a[id]' AND `type` = 'album' AND `id_file` = '$gallery[id]' LIMIT 1");
			}
		}
	}
		
}


if ($img_x == $img_y)
{
	$dstW = 48; // ширина
	$dstH = 48; // высота 
}
elseif ($img_x > $img_y)
{
	$prop = $img_x / $img_y;
	$dstW = 48;
	$dstH = ceil($dstW / $prop);
}
else
{
	$prop = $img_y / $img_x;
	$dstH = 48;
	$dstW = ceil($dstH / $prop);
}

$screen = imagecreatetruecolor($dstW, $dstH);
imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
//imagedestroy($imgc);
imagejpeg($screen,H."sys/gallery/48/$id_foto.jpg",90);
@chmod(H."sys/gallery/48/$id_foto.jpg",0777);
imagedestroy($screen);

if ($img_x == $img_y)
{
	$dstW = 128; // ширина
	$dstH = 128; // высота 
}
elseif ($img_x > $img_y)
{
	$prop = $img_x / $img_y;
	$dstW = 128;
	$dstH = ceil($dstW / $prop);
}
else
{
	$prop = $img_y / $img_x;
	$dstH = 128;
	$dstW = ceil($dstH / $prop);
}

$screen = imagecreatetruecolor($dstW, $dstH);
imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
//imagedestroy($imgc);
// $screen = img_copyright($screen); // наложение копирайта
imagejpeg($screen,H."sys/gallery/128/$id_foto.jpg",90);
@chmod(H."sys/gallery/128/$id_foto.jpg",0777);
imagedestroy($screen);

if ($img_x > 640 || $img_y > 640)
{
	if ($img_x == $img_y)
	{
		$dstW = 640; // ширина
		$dstH = 640; // высота 
	}
	elseif ($img_x > $img_y)
	{
		$prop = $img_x / $img_y;
		$dstW = 640;
		$dstH = ceil($dstW / $prop);
	}
	else
	{
		$prop = $img_y / $img_x;
		$dstH = 640;
		$dstW = ceil($dstH / $prop);
	}

	$screen = imagecreatetruecolor($dstW, $dstH);
	imagecopyresampled($screen, $imgc, 0, 0, 0, 0, $dstW, $dstH, $img_x, $img_y);
	// imagedestroy($imgc);
	// $screen=img_copyright($screen); // наложение копирайта
	imagejpeg($screen,H."sys/gallery/640/$id_foto.jpg",90);
	imagedestroy($screen);
	$imgc=img_copyright($imgc); // наложение копирайта
	imagejpeg($imgc,H."sys/gallery/foto/$id_foto.jpg",90);
	@chmod(H."sys/gallery/foto/$id_foto.jpg",0777);
}
else
{
	imagejpeg($imgc,H."sys/gallery/640/$id_foto.jpg",90);
	$imgc = img_copyright($imgc); // наложение копирайта
	
	imagejpeg($imgc,H."sys/gallery/foto/$id_foto.jpg",90);
	@chmod(H."sys/gallery/foto/$id_foto.jpg",0777);
}

@chmod(H."sys/gallery/640/$id_foto.jpg",0777);
imagedestroy($imgc);

crop(H."sys/gallery/640/$id_foto.jpg", H."sys/gallery/50/$id_foto.tmp.jpg");
resize(H."sys/gallery/50/$id_foto.tmp.jpg", H."sys/gallery/50/$id_foto.jpg", 50, 50);

@chmod(H."sys/gallery/50/$id_foto.jpg",0777);
@unlink(H."sys/gallery/50/$id_foto.tmp.jpg");

if (isset($_GET['avatar']))
{
	$_SESSION['message'] = 'Фото успешно установлено';
	header("Location: /info.php");
	exit;
}

$_SESSION['message'] = 'Фото успешно загружено';
header("Location: /foto/$ank[id]/$gallery[id]/$id_foto/");
exit;
}
}
else 
$err = 'Выбранный Вами формат изображения не поддерживается';

}


// Редактирование альбома
if (isset($_GET['edit']) && $_GET['edit'] == 'rename' && isset($_GET['ok']) && (isset($_POST['name']) || isset($_POST['opis'])))
{
	$name = $_POST['name'];
	$pass = $_POST['pass'];
	$privat = intval($_POST['privat']);
  $privat_komm=intval($_POST['privat_komm']);

	if (strlen2($name) < 3)$err = 'Короткое название';
	if (strlen2($name) > 32)$err = 'Название не должно быть длиннее 32-х символов';
	$name = my_esc($name);
	$pass = my_esc($pass);
	$msg = $_POST['opis'];

	if (strlen2($msg) > 1024)$err = 'Длина описания превышает предел в 1024 символа';
	$msg = my_esc($msg);

	if (!isset($err))
	{
		if ($user['id']!=$ank['id'])
		admin_log('Фотогалерея','Фотографии',"Переименование альбома пользователя '[url=/id$ank[id]]" . user::nick($ank['id'], 0) . "[/url]'");
		dbquery("UPDATE `gallery` SET `name` = '$name', `privat` = '$privat', `privat_komm` = '$privat_komm', `pass` = '$pass', `opis` = '$msg' WHERE `id` = '$gallery[id]' LIMIT 1");
		$_SESSION['message'] = 'Изменения успешно приняты';
		header("Location: /foto/$ank[id]/?");
		exit;
	}
}
?>