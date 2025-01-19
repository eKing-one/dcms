<?php
/*
=======================================
Подарки для Dcms-Social
Автор: Искатель
---------------------------------------
此脚本在许可下被破坏
DCMS-Social 引擎。
使用时，指定引用到
网址 http://dcms-social.ru
=======================================
*/
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
only_level(3);
$width = ($webbrowser == 'web' ? '100' : '70'); // Размер подарков при выводе в браузер
/*
==================================
Редактирование подарков
==================================
*/
if (isset($_GET['edit_gift']) && isset($_GET['category'])) {
	$category = dbassoc(dbquery("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['category']) . "' LIMIT 1"));
	$gift = dbassoc(dbquery("SELECT * FROM `gift_list` WHERE `id` = '" . intval($_GET['edit_gift']) . "' LIMIT 1"));
	if (!$category || !$gift) {
		$_SESSION['message'] = '没有这样的类别或礼物';
		header("Location: ?");
		exit;
	}
	if (isset($_POST['name']) && isset($_POST['money'])) // Редактирование записи
	{
		$name = my_esc($_POST['name']);
		$money = intval($_POST['money']);
		if ($money < 1) $err = '费用太少了';
		if (strlen2($name) < 2) $err = '名称太短了！要大于 2 字节！';
		if (strlen2($name) > 128) $err = '名称的长度超过 128 个字节的限制';
		if (!isset($err)) {
			dbquery("UPDATE `gift_list` SET `name` = '$name' , `money` = '$money', `id_category` = '$category[id]' WHERE `id` = '$gift[id]'");
			$_SESSION['message'] = '礼物已成功编辑';
			header('Location: ?category=' . $category['id'] . '&page=' . intval($_GET['page']));
			exit;
		}
	}
	if (isset($_GET['delete'])) // Удаление подарка
	{
		unlink(H . 'sys/gift/' . $gift['id'] . '.png');
		dbquery("DELETE FROM `gift_list` WHERE `id` = '$gift[id]'");
		dbquery("DELETE FROM `gifts_user` WHERE `id_gift` = '$gift[id]'");
		$_SESSION['message'] = '礼物被成功撤回';
		header("Location: ?category=$category[id]&page=" . intval($_GET['page']));
		exit;
	}
	$set['title'] = '编辑礼物';
	include_once '../../sys/inc/thead.php';
	title();
	aut();
	err();
	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">类别</a> |  <a href="?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>添加礼物</b><br />';
	echo '</div>';
	// Форма редактирования подарка
	echo '<form class="main" method="post" enctype="multipart/form-data"  action="?category=' . $category['id'] . '&amp;edit_gift=' . $gift['id'] . '&amp;page=' . intval($_GET['page']) . '">';
	echo '<img src="/sys/gift/' . $gift['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /><br />';
	echo '标题:<br /><input type="text" name="name" value="' . htmlspecialchars($gift['name']) . '" /><br />';
	echo '价格:<br /><input type="text" name="money" value="' . $gift['money'] . '" style="width:30px;"/><br />';
	echo '<input value="保存" type="submit" />';
	echo '</form>';
	echo '<div class="foot">';
	echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">类别</a> |  <a href="?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>添加礼物</b><br />';
	echo '</div>';
} else
	/*
==================================
Добавление подарков
==================================
*/
	if (isset($_GET['add_gift']) && isset($_GET['category'])) {
		$category = dbassoc(dbquery("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['category']) . "' LIMIT 1"));
		if (!$category) {
			$_SESSION['message'] = 'Нет такой категории';
			header("Location: ?");
			exit;
		}
		if (isset($_POST['name']) && isset($_POST['money']) && isset($_FILES['gift'])) // Создание записи
		{
			$name = my_esc($_POST['name']);
			$money = intval($_POST['money']);
			if ($money < 1) $err = 'Укажите стоимость подарка';
			if (strlen2($name) < 2) $err = 'Короткое название';
			if (strlen2($name) > 128) $err = 'Длина названия превышает предел в 128 символов';
			if (!isset($err)) {
				dbquery("INSERT INTO `gift_list` (`name`, `money`, `id_category`) values('$name', '$money', '$category[id]')");
				$file_id = dbinsertid();
				copy($_FILES['gift']['tmp_name'], H . 'sys/gift/' . $file_id . '.png');
				@chmod(H . 'sys/gift/' . $file_id . '.png', 0777);
				$_SESSION['message'] = 'Подарок успешно добавлен';
				header("Location: ?category=" . $category['id']);
				exit;
			}
		}
		$set['title'] = '附加礼物';
		include_once '../../sys/inc/thead.php';
		title();
		aut();
		err();
		echo '<div class="foot">';
		echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">类别</a> |  <a href="?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>附加礼物</b><br />';
		echo '</div>';
		// Форма создания категории
		echo '<form class="main" method="post" enctype="multipart/form-data"  action="?category=' . $category['id'] . '&amp;add_gift">';
		echo '标题：<br /><input type="text" name="name" value="" /><br />';
		echo '价格：<br /><input type="text" name="money" value="" style="width:30px;"/><br />';
		echo '礼物：<br /><input name="gift" accept="image/*,image/png" type="file" /><br />';
		echo '<input value="增加" type="submit" />';
		echo '</form>';
		echo '<div class="foot">';
		echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">类别</a> |  <a href="?category=' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</a> | <b>附加礼物</b><br />';
		echo '</div>';
	} else
		/*
==================================
Вывод подарков
==================================
*/
		if (isset($_GET['category'])) {
			$category = dbassoc(dbquery("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['category']) . "' LIMIT 1"));
			if (!$category) {
				$_SESSION['message'] = '没有这样的类别';
				header("Location: ?");
				exit;
			}
			$set['title'] = '礼物清单';
			include_once '../../sys/inc/thead.php';
			title();
			aut();
			err();
			echo '<div class="foot">';
			echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">类别</a> | <b>' . htmlspecialchars($category['name']) . '</b><br />';
			echo '</div>';
			// Список подарков
			$k_post = dbresult(dbquery("SELECT COUNT(id) FROM `gift_list`  WHERE `id_category` = '$category[id]'"), 0);
			if ($k_post == 0) {
				echo '<div class="mess">';
				echo '无赠品';
				echo '</div>';
			}
			$k_page = k_page($k_post, $set['p_str']);
			$page = page($k_page);
			$start = $set['p_str'] * $page - $set['p_str'];
			$q = dbquery("SELECT name,id,money FROM `gift_list` WHERE `id_category` = '$category[id]' ORDER BY `id` LIMIT $start, $set[p_str]");
			while ($post = dbassoc($q)) {
				/*-----------代码-----------*/
				if ($num == 0) {
					echo '<div class="nav1">';
					$num = 1;
				} elseif ($num == 1) {
					echo '<div class="nav2">';
					$num = 0;
				}
				/*---------------------------*/
				echo '<img src="/sys/gift/' . $post['id'] . '.png" style="max-width:' . $width . 'px;" alt="*" /><br />';
				echo '标题: ' . htmlspecialchars($post['name']) . '<br /> ';
				echo '成本: ' . $post['money'] . ' ' . $sMonet[0];
				echo ' <a href="create.php?category=' . $category['id'] . '&amp;edit_gift=' . $post['id'] . '&amp;page=' . $page . '"><img src="/style/icons/edit.gif" alt="*" /></a> ';
				echo ' <a href="create.php?category=' . $category['id'] . '&amp;edit_gift=' . $post['id'] . '&amp;page=' . $page . '&amp;delete"><img src="/style/icons/delete.gif" alt="*" /></a> ';
				echo '</div>';
			}
			if ($k_page > 1) str('create.php?category=' . intval($_GET['category']) . '&amp;', $k_page, $page); // 输出页数
			echo '<div class="foot">';
			echo '<img src="/style/icons/ok.gif" alt="*" />  <a href="?category=' . $category['id'] . '&amp;add_gift">加个礼物</a><br />';
			echo '</div>';
			echo '<div class="foot">';
			echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">类别</a> | <b>' . htmlspecialchars($category['name']) . '</b><br />';
			echo '</div>';
		} else
			/*
==================================
Создание категорий
==================================
*/
			if (isset($_GET['add_category'])) {
				if (isset($_POST['name']) && $_POST['name'] != NULL) // Создание записи
				{
					$name = my_esc($_POST['name']);
					if (strlen2($name) < 2) $err = '短标题';
					if (strlen2($name) > 128) $err = '标题长度超过 128 字符限制';
					if (!isset($err)) {
						dbquery("INSERT INTO `gift_categories` (`name`) values('$name')");
						$_SESSION['message'] = '类别已成功添加';
						header("Location: ?");
						exit;
					}
				}
				$set['title'] = '创建类别';
				include_once '../../sys/inc/thead.php';
				title();
				aut();
				err();
				echo '<div class="foot">';
				echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">类别</a><br />';
				echo '</div>';
				// Форма создания категории
				echo '<form class="main" method="post" action="?add_category">';
				echo '标题:<br /><input type="text" name="name" value="" /><br />';
				echo '<input value="增加" type="submit" />';
				echo '</form>';
				echo '<div class="foot">';
				echo '<img src="/style/icons/str2.gif" alt="*" />  <a href="?">类别</a><br />';
				echo '</div>';
			} else
				/*
==================================
Редактирование категорий
==================================
*/
				if (isset($_GET['edit_category'])) {
					$category = dbassoc(dbquery("SELECT * FROM `gift_categories` WHERE `id` = '" . intval($_GET['edit_category']) . "' LIMIT 1"));
					if (!$category) {
						$_SESSION['message'] = '没有这样的类别';
						header("Location: ?");
						exit;
					}
					if (isset($_POST['name']) && $_POST['name'] != NULL) // Создание записи
					{
						$name = my_esc($_POST['name']);
						if (strlen2($name) < 2) $err = '短标题';
						if (strlen2($name) > 128) $err = '标题长度超过 128 字符限制';
						if (!isset($err)) {
							dbquery("UPDATE `gift_categories` SET `name` = '$name' WHERE `id` = '$category[id]'");
							$_SESSION['message'] = '类别已成功重命名';
							header("Location: ?");
							exit;
						}
					}
					if (isset($_GET['delete'])) // Удаление категории
					{
						$q = dbquery("SELECT id FROM `gift_list` WHERE `id_category` = '$category[id]'");
						while ($post = dbassoc($q)) {
							unlink(H . 'sys/gift/' . $post['id'] . '.png');
							dbquery("DELETE FROM `gifts_user` WHERE `id_gift` = '$post[id]'");
						}
						dbquery("DELETE FROM `gift_list` WHERE `id_category` = '$category[id]'");
						dbquery("DELETE FROM `gift_categories` WHERE `id` = '$category[id]' LIMIT 1");
						$_SESSION['message'] = '类别已成功删除';
						header("Location: ?");
						exit;
					}
					$set['title'] = '编辑类别';
					include_once '../../sys/inc/thead.php';
					title();
					aut();
					err();
					// Форма редактирования категории
					echo '<form class="main" method="post" action="?edit_category=' . $category['id'] . '">';
					echo '标题:<br /><input type="text" name="name" value="' . htmlspecialchars($category['name']) . '" /><br />';
					echo '<input value="增加" type="submit" />';
					echo '</form>';
				} else
				/*
==================================
Вывод категорий
==================================
*/ {
					$set['title'] = '类别清单';
					include_once '../../sys/inc/thead.php';
					title();
					aut();
					err();
					// Список категорий	
					$k_post = dbresult(dbquery("SELECT COUNT(id) FROM `gift_categories`"), 0);
					if ($k_post == 0) {
						echo '<div class="mess">';
						echo '无类别';
						echo '</div>';
					}
					$q = dbquery("SELECT name,id FROM `gift_categories` ORDER BY `id`");
					while ($post = dbassoc($q)) {
						/*-----------代码-----------*/
						if ($num == 0) {
							echo '<div class="nav1">';
							$num = 1;
						} elseif ($num == 1) {
							echo '<div class="nav2">';
							$num = 0;
						}
						/*---------------------------*/
						echo '<img src="/style/themes/default/loads/14/dir.png" alt="*" /> <a href="create.php?category=' . $post['id'] . '">' . htmlspecialchars($post['name']) . '</a> ';
						echo '(' . dbresult(dbquery("SELECT COUNT(id) FROM `gift_list` WHERE `id_category` = '$post[id]'"), 0) . ')';
						echo ' <a href="create.php?edit_category=' . $post['id'] . '"><img src="/style/icons/edit.gif" alt="*" /></a> ';
						echo ' <a href="create.php?edit_category=' . $post['id'] . '&amp;delete"><img src="/style/icons/delete.gif" alt="*" /></a> ';
						echo '</div>';
					}
					echo '<div class="foot">';
					echo '<img src="/style/icons/ok.gif" alt="*" />  <a href="?add_category">创建类别</a><br />';
					echo '</div>';
				}
include_once '../../sys/inc/tfoot.php';
