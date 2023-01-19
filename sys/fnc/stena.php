<?php
/*
 ==========
 动态上活动的输出
 ==========
 */
function stena($id_us = NULL, $id = NULL)
{
    global $webbrowser;
    $ank_stena = dbassoc(dbquery("SELECT `id`,`pol` FROM `user` WHERE `id`='" . $id_us . "' LIMIT 1")); //确定评论的作者
    if (dbresult(dbquery("SELECT COUNT(`id`)FROM `stena` WHERE `id`='" . $id . "' LIMIT 1"), 0) == 1) { //如果存在带有此条目的评论，那么...
        $post = dbassoc(dbquery("SELECT * FROM `stena` WHERE `id`='" . $id . "' LIMIT 1"));
        if ($post) {
            if ($post['type'] == 'photo') { //如果化身更改
                echo " <span style='color:darkgreen;'>更换了" . ($ank_stena['pol'] == 0 ? '一个' : null) . "新头像</span><br/>";
                $photo = dbassoc(dbquery("SELECT `id`,`id_gallery`,`ras` FROM `gallery_photo` WHERE `id`='" . $post['info_1'] . "' LIMIT 1"));
                echo "<a href='/photo/" . $ank_stena['id'] . "/" . $photo['id_gallery'] . "/" . $photo['id'] . "/'><img class='stenka' style='width:" . ($webbrowser ? '240px;' : '60px;') . "' src='/photo/photo0/" . $photo['id'] . "." . $photo['ras'] . "'></a>";
            } elseif ($post['type'] == 'note') { //如果新日记
                $note = dbquery("SELECT `id`,`name`,`msg` FROM `notes` WHERE `id`='" . $post['info_1'] . "' LIMIT 1");
                if (dbrows($note) == 0) { //如果没有这样的日记，那么...
                    echo " <span style='color:#666;'>删除了" . ($ank_stena['pol'] == 0 ? '一个' : null) . "日记</span>";
                } else { //А, если существует, то...
                    $notes = dbassoc($note);
                    echo " <span style='color:darkgreen;'>创建了" . ($ank_stena['pol'] == 0 ? '一个' : null) . "日记</span><br/>";
                    echo "<a href='/plugins/notes/list.php?id=" . $notes['id'] . "'><b style='color:#999;'>" . text($notes['name']) . "</b></a><br/>";
                    echo '<span style="color:#666;">' . rez_text($notes['msg'], 82) . '</span>';
                }
            } elseif ($post['type'] == 'them') { //如果这是论坛的主题
                $dump = dbquery("SELECT `id`,`id_forum`,`id_razdel`,`name`,`text` FROM `forum_t` WHERE `id`='" . $post['info_1'] . "' LIMIT 1");
                if (dbrows($dump) == 0) { //如果没有这样的话题，那么...
                    echo " <span style='color:#666;'>删除了" . ($ank_stena['pol'] == 0 ? '一个' : null) . "论坛中的帖子</span>";
                } else { //如果有，那么...
                    $them = dbassoc($dump);
                    echo " <span style='color:darkgreen;'>论坛中创建了" . ($ank_stena['pol'] == 0 ? '一个' : null) . "新帖子</span><br/>";
                    echo " <a href='/forum/" . $them['id_forum'] . "/" . $them['id_razdel'] . "/" . $them['id'] . "/'><b style='color:#999;'>" . text($them['name']) . "</b></a><br/>";
                    echo " <span style='color:#666;'>" . rez_text($them['text'], 82) . "</span>";
                }
            }
        }
    }
}
