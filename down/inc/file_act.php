<?
if ((user_access('down_file_delete') || $user['id']==$file_id['id_user'])  && isset($_GET['act']) && $_GET['act']=='edit' && isset($_GET['ok']) && $l!='/')
{
	$name=my_esc($_POST['name']);
	$opis=my_esc($_POST['opis']);
	if(strlen2($name)<2)$err[]= '短标题';
	if(strlen2($name)>128)$err[]= '长标题';
	if ($_POST['metka'] == 0 || $_POST['metka'] == 1)$metka = $_POST['metka'];
	else $err = '标签错误 +18';
	if(!isset($err)){
		dbquery("UPDATE `downnik_files` SET `metka` = '".$metka."', `name` = '".$name."',`opis` = '".$opis."' WHERE `id` = '$file_id[id]' LIMIT 1");
		$_SESSION['message']= '文件编辑成功';
		admin_log('交换器', '编辑文件', "编辑文件 [url=/down$dir_id[dir]$name.$file_id[ras]?showinfo]$file_id[name][/url]");
		header ("Location: /down$dir_id[dir]$file_id[id].$file_id[ras]?showinfo");
		exit;
	}
}
if ((user_access('down_file_delete') or $user['id']==$file_id['id_user']) && isset($_GET['act']) && $_GET['act']=='delete' && isset($_GET['ok']) && $l!='/')
{
	dbquery("DELETE FROM `downnik_files` WHERE `id` = '$file_id[id]'");
	dbquery("DELETE FROM `user_music` WHERE `id_file` = '$file_id[id]' AND `dir` = 'down'");
	unlink(H.'files/down/'.$file_id['id'].'.dat');	$_SESSION['message']= '文件已成功删除';
	header ("Location: /down$dir_id[dir]?".session_id());
	exit;
}
?>