<?php
/*
=======================================
DCMS-Social 用户个人文件
作者：探索者
---------------------------------------
此脚本在许可下被破坏
DCMS-Social 引擎。
使用时，指定引用到
网址 http://dcms-social.ru
---------------------------------------
接点
ICQ：587863132
http://dcms-social.ru
=======================================
*/
if (isset($_GET['edit'])) {
	if (isset($_GET['ok'])) {
		$name = my_esc($_POST['name']);
		$opis = my_esc($_POST['opis']);
		if (strlen2($name) < 2) $err[] = '短名';
		if (strlen2($name) > 128) $err[] = '长名';
		if ($_POST['metka'] == 0 || $_POST['metka'] == 1) $metka = $_POST['metka'];
		else $err = '标签错误 +18';
		if (!isset($err)) {
			dbquery("UPDATE `downnik_files` SET `metka` = '" . my_esc($metka) . "', `name` = '" . $name . "',`opis` = '" . $opis . "' WHERE `id` = '$file_id[id]' LIMIT 1");
			$_SESSION['message'] = '该文件已成功编辑';
			header('Location: ?id_file=' . $file_id['id']);
			exit;
		}
	}
	echo '<div class="foot">';
	echo '<img src="/style/icons/str.gif" alt="*">  <a href="?go=' . $file_id['id'] . '">移动文件</a>';
	echo '</div>';
	echo '<form method="post"  action="?id_file=' . $file_id['id'] . '&amp;edit&amp;ok">
	档案名称:<br />
	<input name="name" type="text" maxlength="32" value="' . text($file_id['name']) . '" /><br />
	资料描述:<br />
	<textarea name="opis">' . text($file_id['opis']) . '</textarea><br />';
	echo "<label><input type='checkbox' name='metka' value='1' " . ($file_id['metka'] == 1 ? "checked='checked'" : "") . "/> 马克 <font color=red>18+</font></label><br />";
	echo '<img src="/style/icons/ok.gif" alt="*"> <input value="修改" type="submit" /> <a href="?id_file=' . $file_id['id'] . '"><img src="/style/icons/delete.gif" alt="*"> 取消</a><br />';
	echo "<div class='foot'>";
	echo "<img src='/style/icons/up_dir.gif' alt='*'> " . ($dir['osn'] == 1 ? '<a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">档案</a>' : '') . " " . user_files($dir['id_dires']) . " " . ($dir['osn'] == 1 ? '' : '&gt; <a href="/user/personalfiles/' . $ank['id'] . '/' . $dir['id'] . '/">' . text($dir['name']) . '</a>') . "";
	echo "</div>";
	include_once '../../sys/inc/tfoot.php';
	exit;
}
