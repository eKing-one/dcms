<?
$set['title'] = '论坛 - ' . text($forum['name']) . ' - ' . text($razdel['name']) . ' - 新主题'; //网页标题
include_once '../sys/inc/thead.php';
title();
if (isset($_POST['name']) && isset($_POST['msg'])) {
   if (isset($_SESSION['time_c_t_forum']) && $_SESSION['time_c_t_forum'] > $time - 600 && $user['level'] == 0) $err = '你不能经常创建主题';
   $name = my_esc($_POST['name']);
   if (strlen2($name) < 3) $err[] = '主题的短名称';
   if (strlen2($name) > 32) $err[] = '主题名称不应超过32个字符';
   $mat = antimat($name);
   if ($mat) $err[] = '在主题的标题中发现了一个非法字符: ' . $mat;
   $msg = esc(stripslashes(htmlspecialchars($_POST['msg'])));
   if (strlen2($msg) < 10) $err[] = '短消息';
   if (strlen2($msg) > 30000) $err[] = '消息长度超过30,000个字符的限制';
   $mat = antimat($msg);
   if ($mat) $err[] = '在消息的文本中发现了一个非法字符: ' . $mat;
   $msg = my_esc($msg);
   if (!isset($err)) {
      $_SESSION['time_c_t_forum'] = $time;
      dbquery("INSERT INTO `forum_t` (`id_forum`, `id_razdel`, `time_create`, `id_user`, `name`, `time`, `text`) values('$forum[id]', '$razdel[id]', '$time', '$user[id]', '$name', '$time', '$msg')");
      $them['id'] = dbinsertid();
      if ($forum['adm'] != 1) {
         dbquery("insert into `stena`(`id_user`,`id_stena`,`time`,`info`,`info_1`,`type`) values('" . $user['id'] . "','" . $user['id'] . "','" . $time . "','new them in forum','" . $them['id'] . "','them')");
      }
      $q = dbquery("SELECT * FROM `frends` WHERE `user` = '" . $user['id'] . "' AND `i` = '1'");
      while ($f = dbarray($q)) {
         $a = user::get_user($f['frend']);
         $lentaSet = dbarray(dbquery("SELECT * FROM `tape_set` WHERE `id_user` = '" . $a['id'] . "' LIMIT 1")); // Общая настройка ленты
         if ($f['lenta_forum'] == 1 && $lentaSet['lenta_forum'] == 1)
            dbquery("INSERT INTO `tape` (`id_user`, `avtor`, `type`, `time`, `id_file`) values('$a[id]', '$user[id]', 'them', '$time', '$them[id]')");
      }
      dbquery("UPDATE `user` SET `rating_tmp` = '" . ($user['rating_tmp'] + 1) . "' WHERE `id` = '$user[id]' LIMIT 1");
      dbquery("UPDATE `forum_r` SET `time` = '$time' WHERE `id` = '$razdel[id]' LIMIT 1");
      $_SESSION['message'] = '主题成功创建';
      header("Location: /forum/$forum[id]/$razdel[id]/$them[id]/");
      exit;
   }
}
err();
aut();
echo "<form method=\"post\" name='message' action=\"/forum/$forum[id]/$razdel[id]/?act=new\">";
echo "主题名称:<br />";
echo "<input name=\"name\" type=\"text\" maxlength='32' value='' /><br />";
if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php')) {
   include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
} else {
   echo "信息:$tPanel<textarea name=\"msg\"></textarea><br />";
}
echo "<input value=\"创建\" type=\"submit\" /><br />";
echo "</form>";
echo "<div class=\"foot\">";
echo "<a href=\"/forum/$forum[id]/$razdel[id]/\" title='返回分区'>返回</a><br />";
echo "<a href=\"/forum/$forum[id]/\">" . text($forum['name']) . "</a><br />";
echo "<a href=\"/forum/\">论坛</a><br />";
echo "</div>";
