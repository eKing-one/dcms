<?







if (user_access('obmen_dir_edit') && isset($_GET['act']) && $_GET['act']=='set')



{



	echo "<form class=\"foot\" action='?act=set&amp;ok&amp;page=$page' method=\"post\">";



	echo "Название папки:<br />";



	echo "<input type='text' name='name' value='" . htmlspecialchars($dir_id['name']) . "' /><br />";



if ($dir_id['upload']==1)$check=' checked="checked"'; else $check=NULL;



	echo "<label><input type=\"checkbox\"$check name=\"upload\" value=\"1\" /> Выгрузка</label><br />";



	echo "Расширения через \";\":<br />";



	echo "<input type='text' name='ras' value='$dir_id[ras]' /><br />";



	echo "Максимальный размер файлов:<br />";



if ($dir_id['maxfilesize']<1024)$size=$dir_id['maxfilesize'];



elseif($dir_id['maxfilesize']>=1024 && $dir_id['maxfilesize']<1048576)$size=intval($dir_id['maxfilesize']/1024);



elseif($dir_id['maxfilesize']>=1048576)$size=intval($dir_id['maxfilesize']/1048576);







	echo '<input type="text" name="size" size="4" value="'.$size.'" />';



	echo '<select name="mn">';



if ($dir_id['maxfilesize']<1024)$sel=' selected="selected"';else $sel=NULL;



	echo '<option value="1"'.$sel.'>B</option>';



if ($dir_id['maxfilesize']>=1024 && $dir_id['maxfilesize']<1048576)$sel=' selected="selected"';else $sel=NULL;



	echo '<option value="1024"'.$sel.'>KB</option>';



if ($dir_id['maxfilesize']>=1048576)$sel=' selected="selected"';else $sel=NULL;



	echo '<option value="1048576"'.$sel.'>MB</option>';



	echo '</select><br />';



	echo '*настройки сервера не позволяют выгружать файлы объемом более: '.size_file($upload_max_filesize).'<br />';



	echo '<img src="/style/icons/ok.gif" alt="*"> <input class="submit" type="submit" value="Принять изменения" /> ';



	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="?">Отмена</a>]<br />';



	echo '</form>';



}



































if (user_access('obmen_dir_create') && isset($_GET['act']) && $_GET['act']=='mkdir')



{



	echo '<form class="foot" action="?act=mkdir&amp;ok&amp;page='.$page.'" method="post">';



	echo 'Название папки:<br />';



	echo '<input type="text" name="name" value="" /><br />';



	echo '<label><input type="checkbox" name="upload" value="1" /> Выгрузка</label><br />';



	echo 'Расширения через ";":<br />';



	echo '<input type="text" name="ras" value="" /><br />';



	echo 'Максимальный размер файлов:<br />';



	echo '<input type="text" name="size" size="4" value="500" />';



	echo '<select name="mn">';



	echo '<option value="1">B</option>';



	echo '<option value="1024" selected="selected">KB</option>';



	echo '<option value="1048576">MB</option>';



	echo '</select><br />';



	echo '*настройки сервера не позволяют выгружать файлы объемом более: '.size_file($upload_max_filesize).'<br />';



	echo '<img src="/style/icons/ok.gif" alt="*"> <input class="submit" type="submit" value="创建文件夹" /> ';	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="?">Отмена</a>]<br />';



	echo '</form>';



}







if (user_access('obmen_dir_edit') && isset($_GET['act']) && $_GET['act']=='rename' && $l!='/')



{



	echo '<form class="foot" action="?act=rename&amp;ok&amp;page='.$page.'" method="post">';



	echo 'Название папки:<br />';



	echo '<input type="text" name="name" value="'.$dir_id['name'].'"/><br />';	echo '<img src="/style/icons/ok.gif" alt="*"> <input class="submit" type="submit" value="Переименовать" /> ';	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="?">Отмена</a>]<br />';



	echo '</form>';



}











if (user_access('obmen_dir_edit') && isset($_GET['act']) && $_GET['act']=='mesto' && $l!='/')



{



	echo '<form class="foot" action="?act=mesto&amp;ok&amp;page='.$page.'" method="post">';



	echo 'Новый путь:<br />';



	echo '<select class="submit" name="dir_osn">';



	echo '<option value="/">[в корень]</option>';



	$q=dbquery("SELECT DISTINCT `dir` FROM `obmennik_dir` WHERE `dir` not like '$l%' ORDER BY 'dir' ASC");



while ($post = dbassoc($q))



{



	echo '<option value="'.$post['dir'].'">'.$post['dir'].'</option>';



}











	echo '</select><br />';



	echo '<img src="/style/icons/ok.gif" alt="*"> <input class="submit" type="submit" value="Переместить" />';	echo '[<img src="/style/icons/delete.gif" alt="*"> <a href="?">Отмена</a>]<br />';



	echo '</form>';



}







if (user_access('obmen_dir_delete') && isset($_GET['act']) && $_GET['act']=='delete' && $l!='/')



{







	echo '<div class="mess">';



	echo 'Удалить текущую папку ('.$dir_id['name'].')?<br />';



	echo '[<a href="?act=delete&amp;ok&amp;page='.$page.'"><img src="/style/icons/ok.gif" alt="*"> 是的</a>] ';



	echo '[<a href="?page='.$page.'"><img src="/style/icons/delete.gif" alt="*"> 取消</a>]<br />';



	echo '</div>';



}











if (user_access('obmen_dir_edit') || user_access('obmen_dir_delete') || user_access('obmen_dir_create'))



{



	echo '<div class="foot">';







if (user_access('obmen_dir_create'))



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=mkdir&amp;page='.$page.'">创建文件夹</a><br />';







if ($l!='/'){







if (user_access('obmen_dir_edit')){



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=rename&amp;page='.$page.'">Переименовать папку</a><br />';



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=set&amp;page='.$page.'">Параметры папки</a><br />';



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=mesto&amp;page='.$page.'">Переместить папку</a><br />';



}







if (user_access('obmen_dir_delete') && $dir_id['my'] == 0)



	echo '<img src="/style/icons/str.gif" alt="*"> <a href="?act=delete&amp;page='.$page.'">Удалить папку</a><br />';



}











	echo '</div>';



}











?>