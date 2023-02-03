<?php

/***** 我们确定转贴记录的类型 *****/
function notes_sh($id = NULL)
{
    $sql = dbquery("SELECT * FROM `notes` WHERE `id`='" . (int)$id . "' LIMIT 1");
    if (dbrows($sql) == 1) {
        $post = dbassoc($sql);
        if ($post['share'] == 1) {
            if ($post['share_type'] == 'notes') {
?><div style="padding-left:5px;padding-top:5px;margin-left:17px;border-top:1px solid #b3b3b3;border-left:1px solid #b3b3b3;">
                    <img src='/style/icons/repostik.png' style='width:16px;'><i> 记录转贴 <? echo "<a href='/plugins/notes/list.php?id=" . $post['share_id'] . "'><b style='color:#758b9b;'>" . text($post['share_name']) . "</b></a></i><br/>";
                                                                                        echo " " . rez_text(smiles(htmlspecialchars($post['share_text']))) . " ";
                                                                                        ?>
                </div><?
                    } elseif ($post['share_type'] == 'forum') {
                        $them = dbassoc(dbquery("SELECT `id_forum`,`id`,`id_razdel` FROM `forum_t` WHERE `id`='" . $post['share_id'] . "' LIMIT 1"));
                        ?><div style="padding-left:5px;padding-top:5px;margin-left:17px;border-top:1px solid #b3b3b3;border-left:1px solid #b3b3b3;">
                    <img src='/style/icons/repostik.png' style='width:16px;'><i> 重新发布论坛主题 <? echo "<a href='/forum/" . $them['id_forum'] . "/" . $them['id_razdel'] . "/" . $post['share_id'] . "/'><b style='color:#758b9b;'>" . text($post['share_name']) . "</b></span></i></a><br/>";
                                                                                            echo " " . rez_text(smiles(htmlspecialchars($post['share_text']))) . " ";
                                                                                            ?>
                </div><?
                    }
                }
            }
        }
        function notes_share($id = NULL)
        {
            $sql = dbquery("SELECT * FROM `notes` WHERE `id`='" . (int)$id . "' LIMIT 1");
            if (dbrows($sql) == 1) {
                $post = dbassoc($sql);
                if ($post['share'] == 1) {
                    if ($post['share_type'] == 'notes') {
                        ?><div style="padding-left:5px;padding-top:5px;margin-left:17px;border-top:1px solid #b3b3b3;border-left:1px solid #b3b3b3;">
                    <img src='/style/icons/repostik.png' style='width:16px;'><i> 记录转贴 <? echo "<a href='/plugins/notes/list.php?id=" . $post['share_id'] . "'><b style='color:#758b9b;'>" . text($post['share_name']) . "</b></a></i><br/>";
                                                                                        echo " " . output_text($post['share_text']) . " ";
                                                                                        ?>
                </div><?
                    } elseif ($post['share_type'] == 'forum') {
                        $them = dbassoc(dbquery("SELECT `id_forum`,`id`,`id_razdel` FROM `forum_t` WHERE `id`='" . $post['share_id'] . "' LIMIT 1"));
                        ?><div style="padding-left:5px;padding-top:5px;margin-left:17px;border-top:1px solid #b3b3b3;border-left:1px solid #b3b3b3;">
                    <img src='/style/icons/repostik.png' style='width:16px;'><i> 重新发布论坛主题 <? echo "<a href='/forum/" . $them['id_forum'] . "/" . $them['id_razdel'] . "/" . $post['share_id'] . "/'><b style='color:#758b9b;'>" . text($post['share_name']) . "</b></span></i></a><br/>";
                                                                                            echo " " . output_text($post['share_text']) . " ";
                                                                                            ?>
                </div><?
                    }
                }
            }
        }
                        ?>