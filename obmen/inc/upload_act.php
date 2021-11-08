<?
/*
Dcms-Social
Искатель
http://mydcms.ru
*/
if (isset($_GET['act']) && $_GET['act']=='upload' && isset($_GET['ok']) && $l!='/' && $user['id']==$trans['id_user'])
{

$dir_my=dbassoc(dbquery("SELECT * FROM `obmennik_dir` WHERE `id` = '$trans[id_dir]' LIMIT 1"));
//if (dbresult(dbquery("SELECT COUNT(*) FROM `obmennik_files` WHERE `size` = '$trans[size]'"),0)>1 && $dir_my['my']!=1)$err = 'Такой в файл уже есть в обменнике';

if ($dir_id['upload']==1){

$ras=$trans['ras'];
$rasss=explode(';', $dir_id['ras']);
$ras_ok=false;
for($i=0;$i<count($rasss);$i++)
{
	if ($rasss[$i]!=NULL && $ras==$rasss[$i])$ras_ok=true;
}

if (!$ras_ok)$err='Неверное расширение файла';

	if (!$err)
	{
	dbquery("UPDATE `obmennik_files` SET `id_dir` = '$dir_id[id]' WHERE `id` = '$trans[id]' LIMIT 1");
	$_SESSION['message'] = 'Файл успешно добавлен в папку '.$dir_id['name'].' зоны обмена';
	header('Location: /user/personalfiles/'.$trans['id_user'].'/'.$trans['my_dir'].'/?id_file='.$trans['id'].'');
	exit;
	}else{
	$_SESSION['message'] = $err;
	header('Location: /user/personalfiles/'.$trans['id_user'].'/'.$trans['my_dir'].'/?id_file='.$trans['id'].'');
	exit;
	}
}else{
echo "Ошибка! Эта папка не доступна!";
exit;
}
}

?>