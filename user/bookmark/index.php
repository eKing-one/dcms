<?
/* 
修改书签模块PluginS 
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
if (isset($user)) $ank['id'] = $user['id'];
if (isset($_GET['id'])) $ank['id'] = intval($_GET['id']);
if ($ank['id'] == 0) {
    header("Location: /index.php?" . SID);
    exit;
}
$ank = user::get_user($ank['id']);
if (!$ank) {
    header("Location: /index.php?" . SID);
    exit;
}
$set['title'] =  $ank['nick'] . '的书签'; //网页标题
include_once '../../sys/inc/thead.php';
title();
err();
aut(); // 批准格式
echo '<div class="foot">';
echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> | <b>书签</b>';
echo '</div>';
if (isset($user) && $ank['id'] == $user['id']) {
    echo '<div class="mess">';
    echo '使用书签功能，您可以保存您感兴趣的人的链接，文件，照片，相册，笔记，讨论<br />';
    echo '</div>';
}
echo "<table>";
if (!isset($_GET['metki'])) {
    echo "<td class='nav1'><b>书签</b></td><td class='nav1'><a href='?id=" . $ank['id'] . "&metki'>标签</a></td>";
} elseif (isset($_GET['metki'])) {
    echo "<td class='nav1'><a href='index.php'>书签</a></td><td class='nav1'><b>标签</b></td>";
}
echo "</table>";
if (isset($_GET['metki'])) {
    echo '<div class="nav1">';
    $people = dbresult(dbquery("SELECT COUNT(id_object) FROM `bookmarks` WHERE `id_user` = '" . $ank['id'] . "' AND `type`='people'"), 0);
    echo '<img src="/style/icons/druzya.png" alt="*" /> ';
    echo '<a href="/user/bookmark/people.php?id=' . $ank['id'] . '">人们</a> (' . $people . ')';
    echo '</div>';
    echo '<div class="nav2">';
    $files = dbresult(dbquery("SELECT COUNT(id_object) FROM `bookmarks` WHERE `id_user` = '" . $ank['id'] . "' AND `type`='file'"), 0);
    echo '<img src="/style/icons/files.gif" alt="*" /> ';
    echo '<a href="/user/bookmark/files.php?id=' . $ank['id'] . '">档案</a> (' . $files . ')';
    echo '</div>';
    echo '<div class="nav1">';
    $foto = dbresult(dbquery("SELECT COUNT(id_object) FROM `bookmarks` WHERE `id_user` = '" . $ank['id'] . "' AND `type`='foto'"), 0);
    echo '<img src="/style/icons/foto.png" alt="*" /> ';
    echo '<a href="/user/bookmark/foto.php?id=' . $ank['id'] . '">照片</a> (' . $foto . ')';
    echo '</div>';
    echo '<div class="nav2">';
    $forum = dbresult(dbquery("SELECT COUNT(id_object) FROM `bookmarks` WHERE `id_user` = '" . $ank['id'] . "' AND `type`='forum'"), 0);
    echo '<img src="/style/icons/forum.png" alt="*" /> ';
    echo '<a href="/user/bookmark/forum.php?id=' . $ank['id'] . '">论坛</a> (' . $forum . ')';
    echo '</div>';
    echo '<div class="nav1">';
    $notes = dbresult(dbquery("SELECT COUNT(id_object) FROM `bookmarks` WHERE `id_user` = '" . $ank['id'] . "' AND `type`='notes'"), 0);
    echo '<img src="/style/icons/zametki.gif" alt="*" /> ';
    echo '<a href="/user/bookmark/notes.php?id=' . $ank['id'] . '">日记</a> (' . $notes . ')';
    echo '</div>';
    echo '<div class="foot">';
    echo '<img src="/style/icons/str2.gif" alt="*" /> <a href="/info.php?id=' . $ank['id'] . '">' . $ank['nick'] . '</a> | <b>书签</b>';
    echo '</div>';
} else {
    $k_post = dbresult(dbquery("SELECT COUNT(id_object) FROM `bookmarks` WHERE `id_user` = '$ank[id]'"), 0);
    $k_page = k_page($k_post, $set['p_str']);
    $page = page($k_page);
    $start = $set['p_str'] * $page - $set['p_str'];
    $q = dbquery("SELECT * FROM `bookmarks` WHERE `id_user`='$ank[id]' ORDER BY `time` DESC LIMIT $start,$set[p_str]");
    while ($post = dbassoc($q)) {
        echo "<div class='nav1'>";
        if ($post['type'] == 'forum') {
            $them = dbassoc(dbquery("SELECT * FROM `forum_t` WHERE `id`='$post[id_object]' LIMIT 1"));
            echo "<a href='/forum/" . $them['id_forum'] . "/" . $them['id_razdel'] . "/" . $them['id'] . "/'><img src='/style/icons/Forum.gif'> " . htmlspecialchars($them['name']) . "</a><br/>";
            echo substr(htmlspecialchars($them['text']), 0, 40) . " (添加时间 " . vremja($post['time']) . ")";
        } elseif ($post['type'] == 'notes') {
            $notes = dbassoc(dbquery("SELECT * FROM `notes` WHERE `id`='$post[id_object]' LIMIT 1"));
            echo "<a href='/plugins/notes/list.php?id=" . $notes['id'] . "'><img src='/style/icons/diary.gif'> " . htmlspecialchars($notes['name']) . "</a><br/>";
            echo substr(htmlspecialchars($notes['msg']), 0, 40) . "[...] (添加时间 " . vremja($post['time']) . ")";
        } elseif ($post['type'] == 'people') {
            $people = user::get_user($post['id_object']);
            echo "<img src='/style/icons/icon_readers.gif'> ";
            echo user::nick($people['id'], 1, 1, 0) . " <br/>";
            echo " (添加时间 " . vremja($post['time']) . ")";
        } elseif ($post['type'] == 'foto') {
            $foto = dbassoc(dbquery("SELECT * FROM `gallery_foto` WHERE `id`='$post[id_object]' LIMIT 1"));
            echo "<a href='/foto/" . $foto['id_user'] . "/" . $foto['id_gallery'] . "/" . $foto['id'] . "/'><img src='/style/icons/PhotoIcon.gif'> " . htmlspecialchars($foto['name']) . "</a><br/>";
            echo "<img style='height:60px;' src='/foto/foto0/" . $foto['id'] . "." . $foto['ras'] . "'>";
            echo substr(htmlspecialchars($foto['opis']), 0, 40) . "[...] (添加时间 " . vremja($post['time']) . ")";
        } elseif ($post['type'] == 'file') {
            $file_id = dbassoc(dbquery("SELECT id_dir,id,name,ras  FROM `obmennik_files` WHERE `id` = '" . $post['id_object'] . "'  LIMIT 1"));
            $dir = dbarray(dbquery("SELECT `dir` FROM `obmennik_dir` WHERE `id` = '$file_id[id_dir]' LIMIT 1"));
            echo '<img src="/style/icons/film.gif"> <a href="/obmen' . $dir['dir'] . $file_id['id'] . '.' . $file_id['ras'] . '?showinfo">' . htmlspecialchars($file_id['name']) . '.' . $file_id['ras'] . '</a>';
            echo " (添加时间 " . vremja($post['time']) . ")";
        }
        echo "</div>";
    }
}
include_once '../../sys/inc/tfoot.php';
