<?
/*
=======================================
Статусы юзеров для Dcms-Social
Автор: Искатель
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
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
if (isset($_GET['id']) && dbresult(dbquery("SELECT COUNT(*) FROM `status` WHERE `id` = '" . intval($_GET['id']) . "'"), 0) == 1) {
    $post = dbassoc(dbquery("SELECT * FROM `status` WHERE `id` = '" . intval($_GET['id']) . "' LIMIT 1"));
    $ank = dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
    if (isset($user) && ($user['level'] > $ank['level']) || $post['id_user'] == $user['id'])
        dbquery("DELETE FROM `status` WHERE `id` = '$post[id]'");
    dbquery("DELETE FROM `status_komm` WHERE `id_status` = '$post[id]'");
    dbquery("DELETE FROM `status_like` WHERE `id_status` = '$post[id]'");
    $_SESSION['message'] = '状态草草删除'; // “草草删除”...看起来是全删罢
    header("Location: index.php?id=$ank[id]");
    exit;
}
