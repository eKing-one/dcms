<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/user.php';
include_once '../sys/inc/icons.php';
/* Бан пользователя */
if (dbresult(dbquery("SELECT COUNT(*) FROM `ban` WHERE `razdel` = 'forum' AND `id_user` = '$user[id]' AND (`time` > '$time' OR `view` = '0' OR `navsegda` = '1')"), 0) != 0) {
  header('Location: /ban.php?' . SID);
  exit;
}
if (
  isset($_GET['id_forum']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE" . ((!isset($user) || $user['level'] == 0) ? " `adm` = '0' AND" : null) . " `id` = '" . intval($_GET['id_forum']) . "'"), 0) == 1
  && isset($_GET['id_razdel']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "'"), 0) == 1
  && isset($_GET['id_them']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id` = '" . intval($_GET['id_them']) . "' AND `id_razdel` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "'"), 0) == 1
  && isset($_GET['id_post']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id` = '" . intval($_GET['id_post']) . "' AND `id_them` = '" . intval($_GET['id_them']) . "' AND `id_razdel` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "'"), 0) == 1
) {
  $forum = dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  $razdel = dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  $them = dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '" . intval($_GET['id_them']) . "' AND `id_razdel` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  $post = dbassoc(dbquery("SELECT * FROM `forum_p` WHERE `id` = '" . intval($_GET['id_post']) . "' AND `id_them` = '" . intval($_GET['id_them']) . "' AND `id_razdel` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  $post2 = dbassoc(dbquery("SELECT * FROM `forum_p` WHERE `id_them` = '" . intval($_GET['id_them']) . "' AND `id_razdel` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "' ORDER BY `id` DESC LIMIT 1"));
  if (isset($user)) {
    $ank = user::get_user($post['id_user']);
    if (
      isset($_GET['act']) && $_GET['act'] == 'edit' && isset($_POST['msg']) && isset($_POST['post']) &&
      // редактирование поста
      (
        (user_access('forum_post_ed'))
        // права группы на редактирование
        ||
        (isset($user) && $user['id'] == $post['id_user'] && $post['time'] > time() - 600 && $post['id_user'] == $post2['id_user'])
        // право на редактирование своего поста, если он поседний в теме
      )
    ) {
      $msg = $_POST['msg'];
      if (isset($_POST['translit']) && $_POST['translit'] == 1) $msg = translit($msg);
      if (strlen2($msg) < 2) $err[] = '短消息';
      if (strlen2($msg) > 1024) $err[] = '邮件长度超过1024个字符的限制';
      $mat = antimat($msg);
      if ($mat) $err[] = '在消息的文本中发现了一个非法字符: ' . $mat;
      if (!isset($err)) dbquery("UPDATE `forum_p` SET `msg` = '" . my_esc($msg) . "' WHERE `id` = '$post[id]' LIMIT 1");
    } elseif (isset($_GET['act']) && $_GET['act'] == 'edit' && (user_access('forum_post_ed') && ($ank['level'] < $user['level'] || $ank['level'] == $user['level'] && $ank['id'] == $user['id']) || isset($user) && $post['id'] == $post2['id'] && $post['id_user'] == $user['id'] && $post['time'] > time() - 600)) {
      $set['title'] = '论坛-帖子编辑'; //网页标题
      include_once '../sys/inc/thead.php';
      title();
      echo "<div class='nav2'><form method='post' name='message' action='/forum/$forum[id]/$razdel[id]/$them[id]/$post[id]/edit'>";
      $msg2 = output_text($post['msg'], false, true, false, false, false);
      if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
        include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
      else
        echo "通信:<br /><textarea name=\"msg\">" . $msg2 . "</textarea><br />";
      echo "<input name='post' value='修改' type='submit' /><br />";
      echo "</form></div>";
      echo "<div class=\"foot\">";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=end\" title='返回在主题'>在主题</a><br />";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/\" title='至该组'>" . text($razdel['name']) . "</a><br />";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/\" title='到子论坛'>" . text($forum['name']) . "</a><br />";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">论坛</a><br />";
      echo "</div>";
      include_once '../sys/inc/tfoot.php';
    } elseif (isset($_GET['act']) && $_GET['act'] == 'delete' && isset($user) && $them['close'] == 0 && ((user_access('forum_post_ed') && ($ank['level'] <= $user['level'] || $ank['level'] == $user['level'] && $ank['id'] == $user['id'])) || $post['id'] == $post2['id'] && $post['id_user'] == $user['id'] && $post['time'] > time() - 600)) {
      dbquery("DELETE FROM `forum_p` WHERE `id` = '" . intval($_GET['id_post']) . "' AND `id_them` = '" . intval($_GET['id_them']) . "' AND `id_razdel` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "' LIMIT 1");
    } elseif (isset($_GET['act']) && $_GET['act'] == 'msg' && $them['close'] == 0 && isset($user)) {
      $ank = user::get_user($post['id_user']);
      $set['title'] = '论坛- ' . text($them['name']); //网页标题
      include_once '../sys/inc/thead.php';
      title();
      aut();
      echo "<div class='nav2'><form method='post' name='message' action='/forum/$forum[id]/$razdel[id]/$them[id]/new'>";
      echo "<a href='/info.php?id=$ank[id]'>查看资料</a><br />";
      $msg2 = $ank['nick'] . ', ';
      if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
        include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
      else
        echo "信息:<br /><textarea name=\"msg\">$ank[nick], </textarea><br />";
      echo "<input name='post' value='发送信息' type='submit' /><br />";
      echo "</form></div>";
      echo "<div class=\"foot\">";
      echo "<img src='/style/icons/str.gif' alt='*'> <a href=\"/smiles.php\">表情符号</a><br />";
      echo "<img src='/style/icons/str.gif' alt='*'> <a href=\"/rules.php\">规则</a><br />";
      echo "</div>";
      echo "<div class=\"foot\">";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=end\" title='返回在主题'>在主题</a><br />";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/\" title='至该组'>" . text($razdel['name']) . "</a><br />";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/\" title='到子论坛'>" . text($forum['name']) . "</a><br />";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">论坛</a><br />";
      echo "</div>";
      include_once '../sys/inc/tfoot.php';
    } elseif (isset($_GET['act']) && $_GET['act'] == 'cit' && $them['close'] == 0 && isset($user)) {
      //$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
      $ank = user::get_user($post['id_user']);
      $set['title'] = '论坛- ' . text($them['name']); //网页标题
      include_once '../sys/inc/thead.php';
      title();
      aut();
      echo "<div class='nav2'>该消息将被引用:<br/>";
      echo "<div class='cit'>";
      echo output_text($post['msg']) . "<br />";
      echo "</div>";
      echo "<form method='post' name='message' action='/forum/$forum[id]/$razdel[id]/$them[id]/new'>";
      echo "<input name='cit' value='$post[id]' type='hidden' />";
      $msg2 = $ank['nick'] . ', ';
      if ($set['web'] && is_file(H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php'))
        include_once H . 'style/themes/' . $set['set_them'] . '/altername_post_form.php';
      else
        echo "信息:<br /><textarea name=\"msg\">$ank[nick], </textarea><br />";
      echo "<input name='post' value='发送信息' type='submit' /><br />";
      echo "</form></div>";
      echo "<div class=\"foot\">";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/$them[id]/?page=end\" title='回到正题'>在主题</a><br />";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/$razdel[id]/\" title='至该组'>" . text($razdel['name']) . "</a><br />";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/\" title='到子论坛'>" . text($forum['name']) . "</a><br />";
      echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">论坛</a><br />";
      echo "</div>";
      include_once '../sys/inc/tfoot.php';
    }
  }
}
if (
  isset($_GET['id_forum']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE" . ((!isset($user) || $user['level'] == 0) ? " `adm` = '0' AND" : null) . " `id` = '" . intval($_GET['id_forum']) . "'"), 0) == 1
  && isset($_GET['id_razdel']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "'"), 0) == 1
  && isset($_GET['id_them']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id` = '" . intval($_GET['id_them']) . "' AND `id_razdel` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "'"), 0) == 1
) {
  $forum = dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  $razdel = dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  $them = dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id` = '" . intval($_GET['id_them']) . "' AND `id_razdel` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  /*
===============================
将通知标记为已读
===============================
*/
  dbquery("UPDATE `notification` SET `read` = '1' WHERE `id_object` = '$them[id]' AND `type` = 'them_komm' AND `id_user` = '$user[id]'");
  /*------------清除这个讨论的柜台-------------*/
  if (isset($user)) {
    dbquery("UPDATE `discussions` SET `count` = '0' WHERE `id_user` = '$user[id]' AND `type` = 'them' AND `id_sim` = '$them[id]' LIMIT 1");
  }
  /*---------------------------------------------------------*/
  $set['title'] = '论坛- ' . text($them['name']); //网页标题
  include_once '../sys/inc/thead.php';
  title();
  $ank2 = user::get_user($them['id_user']);
  include 'inc/set_them_act.php';
  include 'inc/them.php';
  include 'inc/set_them_form.php';
  echo "<div class=\"foot\">";
  echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">论坛</a> | <a href=\"/forum/$forum[id]/\" title='到子论坛'>" . text($forum['name']) . "</a> | <a href=\"/forum/$forum[id]/$razdel[id]/\" title='至该组'>" . text($razdel['name']) . "</a><br />";
  echo "</div>";
  include_once '../sys/inc/tfoot.php';
}
if (
  isset($_GET['id_forum']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE" . ((!isset($user) || $user['level'] == 0) ? " `adm` = '0' AND" : null) . " `id` = '" . intval($_GET['id_forum']) . "'"), 0) == 1
  && isset($_GET['id_razdel']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_r` WHERE `id` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "'"), 0) == 1
) {
  $forum = dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  $razdel = dbassoc(dbquery("SELECT * FROM `forum_r` WHERE `id` = '" . intval($_GET['id_razdel']) . "' AND `id_forum` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  if (isset($user) && isset($_GET['act']) && $_GET['act'] == 'new' && (!isset($_SESSION['time_c_t_forum']) || $_SESSION['time_c_t_forum'] < $time - 600 || $user['level'] > 0))
    include 'inc/new_t.php'; // создание новой темы
  else {
    $set['title'] = '论坛 - ' . text($razdel['name']); //网页标题
    include_once '../sys/inc/thead.php';
    title();
    if (user_access('forum_razd_edit')) include 'inc/set_razdel_act.php';
    include 'inc/razdel.php';
    if (user_access('forum_razd_edit')) include 'inc/set_razdel_form.php';
    echo "<div class=\"foot\">";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/$forum[id]/\">" . text($forum['name']) . "</a><br />";
    echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">论坛</a><br />";
    echo "</div>";
  }
  include_once '../sys/inc/tfoot.php';
}
if (isset($_GET['id_forum']) && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f` WHERE" . ((!isset($user) || $user['level'] == 0) ? " `adm` = '0' AND" : null) . " `id` = '" . intval($_GET['id_forum']) . "'"), 0) == 1) {
  $forum = dbassoc(dbquery("SELECT * FROM `forum_f` WHERE `id` = '" . intval($_GET['id_forum']) . "' LIMIT 1"));
  $set['title'] = '论坛- ' . text($forum['name']); //网页标题
  include_once '../sys/inc/thead.php';
  title();
  include 'inc/set_forum_act.php'; // действия над подфорумом
  include 'inc/forum.php'; // содержимое
  include 'inc/set_forum_form.php'; // формы действий над подфорумом
  echo "<div class=\"foot\">";
  echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">论坛</a><br />";
  echo "</div>";
  include_once '../sys/inc/tfoot.php';
}
$set['title'] = '论坛'; //网页标题
include_once '../sys/inc/thead.php';
title();
if (user_access('forum_for_create') && isset($_GET['act']) && isset($_GET['ok']) && $_GET['act'] == 'new' && isset($_POST['name']) && isset($_POST['opis']) && isset($_POST['pos'])) {
  $name = my_esc($_POST['name']);
  if (strlen2($name) < 3) $err = '名字太短了';
  if (strlen2($name) > 32) $err = '名字太低了';
  $opis = $_POST['opis'];
  if (strlen2($opis) > 512) $err = '描述太长';
  $opis = my_esc($opis);
  if (!isset($_POST['icon']) || $_POST['icon'] == null)
    $icons = 'default';
  else
    $icons = preg_replace('#[^a-z0-9 _\-\.]#i', null, $_POST['icon']);
  $pos = intval($_POST['pos']);
  if (!isset($err)) {
    admin_log('论坛', '子论坛', "创建子论坛'$name'");
    dbquery("INSERT INTO `forum_f` (`opis`, `name`, `pos`, `icon`) values('$opis', '$name', '$pos', '$icons')");
    msg('子论坛已成功创建');
  }
}
err();
aut(); // форма авторизации
echo "<div class=\"err\">";
echo "<a href='/rules.php'>规则</a><br />";
echo "</div>";
echo "<div class=\"main\">";
echo "<img src='/style/icons/New.gif'> 新的: <a href='/forum/new_t.php'>&bull; 主题</a> | ";
echo "<a href='/forum/new_p.php'>&bull; 通讯</a><br />";
if (isset($user)) {
  echo "<img src='/style/icons/top.gif'> 我的: <a href='/user/info/them_p.php?id=" . $user['id'] . "'>&bull; 主题</a> | ";
  echo "<a href='/user/bookmark/forum.php?id=" . $user['id'] . "'> &bull; 书签</a> | <a href='/user/info/them_p.php?id=" . $user['id'] . "&komm'> &bull; 职位</a><br/>";
}
echo "<img src='/style/icons/searcher.png'> <a href='/forum/search.php'>论坛搜索<br /></a>";
echo "</div>";
echo "<table class='post'>";
$q = dbquery("SELECT * FROM `forum_f`" . ((!isset($user) || $user['level'] == 0) ? " WHERE `adm` = '0'" : null) . " ORDER BY `pos` ASC");
if (dbrows($q) == 0) {
  echo "  <div class='mess'>";
  echo "没有子论坛";
  echo "  </div>";
}
while ($forum = dbassoc($q)) {
  /*-----------代码-----------*/
  if ($num == 0) {
    echo "  <div class='nav1'>";
    $num = 1;
  } elseif ($num == 1) {
    echo "  <div class='nav2'>";
    $num = 0;
  }
  /*---------------------------*/
  echo "<img src='/style/forum/$forum[icon]' alt='*'/> ";
  echo "<a href='/forum/$forum[id]/'><b>" . text($forum['name']) . "</b></a> <span style='color:#666;'>(" . dbresult(dbquery("SELECT COUNT(*) FROM `forum_p` WHERE `id_forum` = '$forum[id]'"), 0) . '/' . dbresult(dbquery("SELECT COUNT(*) FROM `forum_t` WHERE `id_forum` = '$forum[id]'"), 0) . ")";
  if ($forum['opis'] != NULL) echo '<br />' . output_text($forum['opis']);
  echo "  </span> </div>";
}
echo "</table>";
echo "<div class='foot'>";
echo "<img src='/style/icons/soob114.gif'> <a href='on-forum.php'>谁在论坛？</a> | <a href='/user/admin.user.php?forum'>版主</a>";
echo "</div>";
if (user_access('forum_for_create') && (isset($_GET['act']) && $_GET['act'] == 'new' || dbresult(dbquery("SELECT COUNT(*) FROM `forum_f`"), 0) == 0)) {
  echo "<form method=\"post\" action=\"/forum/index.php?act=new&amp;ok\">";
  echo "子论坛的名称:<br />";
  echo "<input name=\"name\" type=\"text\" maxlength='32' value='' /><br />";
  echo "资料描述:<br />";
  echo "<textarea name=\"opis\"></textarea><br />";
  echo "职位:<br />";
  $pos = dbresult(dbquery("SELECT MAX(`pos`) FROM `forum_f`"), 0) + 1;
  echo "<input name=\"pos\" type=\"text\" maxlength='3' value='$pos' /><br />";
  $icon = array();
  $opendiricon = opendir(H . 'style/forum');
  while ($icons = readdir($opendiricon)) {
    if (preg_match('#^\.|default.png#', $icons)) continue;
    $icon[] = $icons;
  }
  closedir($opendiricon);
  echo "图标:<br />";
  echo "<select name='icon'>";
  echo "<option value='default.png'>默认情况下</option>";
  for ($i = 0; $i < sizeof($icon); $i++) {
    echo "<option value='$icon[$i]'>$icon[$i]</option>";
  }
  echo "</select><br />";
  echo "<input value=\"创建\" type=\"submit\" /><br />";
  echo "<img src='/style/icons/str2.gif' alt='*'> <a href=\"/forum/\">取消</a><br />";
  echo "</form>";
}
if (user_access('forum_for_create') && dbresult(dbquery("SELECT COUNT(*) FROM `forum_f`"), 0) > 0) {
  echo "<div class=\"foot\">";
  echo "<img src='/style/icons/str.gif' alt='*'> <a href=\"/forum/?act=new\">新的子论坛</a><br />";
  echo "</div>";
}
include_once '../sys/inc/tfoot.php';
