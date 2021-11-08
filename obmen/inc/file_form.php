<?



if (user_access('obmen_file_edit') || $user['id']==$file_id['id_user'])



{



if (isset($_GET['act']) && $_GET['act']=='edit' && $l!='/')



{



	echo '<form method="post"  action="?showinfo&amp;act=edit&amp;ok">



	Название файла:<br />是的<



	<input name="name" type="text" maxlength="32" value="'.htmlspecialchars($file_id['name']).'" /><br />



	Описание:'.$tPanel.'



	<textarea name="opis">'.htmlspecialchars($file_id['opis']).'</textarea><br />';



	echo "<label><input type='checkbox' name='metka' value='1' ".($file_id['metka'] == 1?"checked='checked'":"")."/> Метка <font color=red>18+</font></label><br />";







	echo '<img src="/style/icons/ok.gif" alt="*"> <input value="Изменить" type="submit" /> <a href="?showinfo"><img src="/style/icons/delete.gif" alt="*"> Отмена</a><br />';



	include_once '../sys/inc/tfoot.php';



	exit;



}



}







if (user_access('obmen_file_delete') || $user['id']==$file_id['id_user'])



{



if (isset($_GET['act']) && $_GET['act']=='delete' && $l!='/')



{



	echo '<div class="err">';



	echo 'Удалить файл '.htmlspecialchars($file_id['name']).'?<br />';



	echo '<a href="?showinfo&amp;act=delete&amp;ok"><img src="/style/icons/ok.gif" alt="*"> Да</a> ';



	echo '<a href="?showinfo"><img src="/style/icons/delete.gif" alt="*"> 取消</a>';



	echo '</div>';	include_once '../sys/inc/tfoot.php';	



	exit;



}



}



?>