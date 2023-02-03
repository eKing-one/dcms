<?php
$plus = dbresult(dbquery("SELECT COUNT(`id`)FROM `notes` WHERE `time`>'" . ($time - 86000) . "'"), 0);
$count = dbresult(dbquery("SELECT COUNT(`id`)FROM `notes`"), 0);
if ($plus > 0) {
        $e = $count . " + " . $plus;
} else {
        $e = $count;
}
echo '<div style="padding: 6px 10px;" class="foot"><a href="/plugins/notes/">';

echo '<b>日记</b> (' . $e . ')</a></div>';

$q = dbquery("SELECT * FROM `notes` ORDER BY `time` DESC LIMIT 3");
if (dbrows($q) == 0) {
        echo '<div class="nav2" style="color:#666;">没有记录</div>';
} else {
        while ($post = dbassoc($q)) {
                $note_name = '<a href="/plugins/notes/list.php?id=' . $post['id'] . '"><span style="color:#06f">' . text($post['name']) . '</span></a>';
                
                $count_comm = dbresult(dbquery("SELECT COUNT(`id`) FROM `notes_komm` WHERE `id_notes`='" . $post['id'] . "'"), 0);
                echo "<div style='border-bottom:1px #d5dde5 solid;' class='nav2'>";

                echo user::nick($post['id_user'], 1, 1, 0) . ' : ' . $note_name;
                echo '<br />';
                echo rez_text($post['msg'], 80);
                echo '<br />';
                echo ($post['share'] == 1 ? "(!) <i>转贴条目</i><br/>" : null);
                echo '<img src="/style/icons/comm_num_gray.png">' . $count_comm . '<span style="float:right;color:#666;"><small>';
                echo vremja($post['time']);
                echo '</small></div>';
        }
}
echo '<div class="nav1">';
if (isset($user)) {
        echo '<a href="/plugins/notes/add.php">写作</a>';
}
echo '<span style="float:right;"><a href="/plugins/notes/">所有参赛作品&rarr;</a></span><br /></div>';
