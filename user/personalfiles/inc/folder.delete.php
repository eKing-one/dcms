<?PHP
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
/*-----------------------Удаление папки----------------------*/
if (isset($_GET['delete_folder']) && !isset($_GET['ok'])) {
    $folder = dbassoc(dbquery("SELECT * FROM `user_files`  WHERE `id` = '" . intval($_GET['delete_folder']) . "' LIMIT 1"));
    echo "<div class='mess'><center>";
    echo "你真的想删除吗 <b>" . htmlspecialchars($folder['name']) . "</b><br />";
    echo "[<a href='?delete_folder=$folder[id]&amp;ok'><img src='/style/icons/ok.gif' alt='*'> 是的</a>] [<a href='?'><img src='/style/icons/delete.gif' alt='*'> 取消</a>] ";
    echo "</center></div>";
    include_once '../../sys/inc/tfoot.php';
}
$a = 0;
$b = 0;
if (isset($_GET['delete_folder']) && isset($_GET['ok'])) {
    $folder = dbassoc(dbquery("SELECT * FROM `user_files`  WHERE `id` = '" . intval($_GET['delete_folder']) . "' LIMIT 1"));
    $q = dbquery("SELECT * FROM `user_files` WHERE `id_dires` like '%/$dir[id]/" . intval($_GET['delete_folder']) . "/%'");
    while ($post = dbassoc($q)) {
        $a++;
        $q2 = dbquery("SELECT * FROM `downnik_files` WHERE `my_dir` = '$post[id]'");
        while ($post2 = dbassoc($q2)) {
            echo $post2['name'] . '<br />';
            unlink(H . 'files/down/' . $post2['id'] . '.dat');
            unlink(H . 'files/screens/128/' . $post2['id'] . '.gif');
            unlink(H . 'files/screens/128/' . $post2['id'] . '.png');
            unlink(H . 'files/screens/128/' . $post2['id'] . '.jpg');
            unlink(H . 'files/screens/128/' . $post2['id'] . '.jpeg');
            unlink(H . 'files/screens/48/' . $post2['id'] . '.gif');
            unlink(H . 'files/screens/48/' . $post2['id'] . '.png');
            unlink(H . 'files/screens/48/' . $post2['id'] . '.jpg');
            unlink(H . 'files/screens/48/' . $post2['id'] . '.jpeg');
            dbquery("DELETE FROM `user_music` WHERE `id_file` = '$post2[id]' AND `dir` = 'down'");
            dbquery("DELETE FROM `downnik_files` WHERE `id` = '$post2[id]'");
            $b++;
        }
        echo $post['name'] . '<br />';
        dbquery("DELETE FROM `user_files` WHERE `id` = '$post[id]' LIMIT 1");
    }
    $q2 = dbquery("SELECT * FROM `user_files` WHERE `id` = '" . intval($_GET['delete_folder']) . "'");
    while ($post = dbassoc($q2)) {
        $a++;
        $q3 = dbquery("SELECT * FROM `downnik_files` WHERE `my_dir` = '$post[id]'");
        while ($post2 = dbassoc($q3)) {
            echo $post2['name'] . '<br />';
            unlink(H . 'files/down/' . $post2['id'] . '.dat');
            unlink(H . 'files/screens/128/' . $post2['id'] . '.gif');
            unlink(H . 'files/screens/128/' . $post2['id'] . '.png');
            unlink(H . 'files/screens/128/' . $post2['id'] . '.jpg');
            unlink(H . 'files/screens/128/' . $post2['id'] . '.jpeg');
            unlink(H . 'files/screens/48/' . $post2['id'] . '.gif');
            unlink(H . 'files/screens/48/' . $post2['id'] . '.png');
            unlink(H . 'files/screens/48/' . $post2['id'] . '.jpg');
            unlink(H . 'files/screens/48/' . $post2['id'] . '.jpeg');
            dbquery("DELETE FROM `user_music` WHERE `id_file` = '$post2[id]' AND `dir` = 'down'");
            dbquery("DELETE FROM `downnik_files` WHERE `id` = '$post2[id]'");
            $b++;
        }
        dbquery("DELETE FROM `user_files` WHERE `id` = '$post[id]' LIMIT 1");
        echo $post['name'] . '<br />';
    }
    $_SESSION['message'] = "已删除 \"文件夹 $a \" и \"档案 $b\"";
    header("Location: ?" . session_id());
    exit;
}
/*------------------------------------------------------------*/
