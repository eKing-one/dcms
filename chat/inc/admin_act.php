<?
if (user_access('chat_room') && isset($_GET['set']) && isset($_GET['ok']) && is_numeric($_GET['set']) && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '" . intval($_GET['set']) . "'"), 0) == 1) {
    $room = dbassoc(dbquery("SELECT * FROM `chat_rooms` WHERE `id` = '" . intval($_GET['set']) . "' LIMIT 1"));
    $name = esc(stripcslashes(htmlspecialchars($_POST['name'])));
    $opis = my_esc($_POST['opis']);
    $pos = intval($_POST['pos']);
    if ($_POST['bots'] == 1 || $_POST['bots'] == 3) $umnik = 1;
    else $umnik = 0;
    if ($_POST['bots'] == 2 || $_POST['bots'] == 3) $shutnik = 1;
    else $shutnik = 0;
    dbquery("UPDATE `chat_rooms` SET `name` = '$name', `opis` = '$opis', `pos` = '$pos', `umnik` = '$umnik', `shutnik` = '$shutnik' WHERE `id` = '$room[id]' LIMIT 1");
    admin_log('聊天', '房间参数', "修改房间 $name");
    msg('房间参数更改');
}
if (user_access('chat_room') && isset($_GET['act']) && isset($_GET['ok']) && $_GET['act'] == 'add_room' && isset($_POST['name']) && esc($_POST['name']) != NULL) {
    $name = esc(stripcslashes(htmlspecialchars($_POST['name'])));
    $opis = my_esc($_POST['opis']);
    $pos = intval($_POST['pos']);
    if ($_POST['bots'] == 1 || $_POST['bots'] == 3) $umnik = 1;
    else $umnik = 0;
    if ($_POST['bots'] == 2 || $_POST['bots'] == 3) $shutnik = 1;
    else $shutnik = 0;
    dbquery("INSERT INTO `chat_rooms` (`name`, `opis`, `pos`, `umnik`, `shutnik`) values('$name', '$opis', '$pos', '$umnik', '$shutnik')");
    admin_log('聊天', '房间参数', "增加房间 '$name', 资料描述: $opis");
    msg('房间添加成功');
}
if (user_access('chat_room') && isset($_GET['delete']) && is_numeric($_GET['delete']) && dbresult(dbquery("SELECT COUNT(*) FROM `chat_rooms` WHERE `id` = '" . intval($_GET['delete']) . "'"), 0) == 1) {
    $room = dbassoc(dbquery("SELECT * FROM `chat_rooms` WHERE `id` = '" . intval($_GET['delete']) . "' LIMIT 1"));
    dbquery("DELETE FROM `chat_rooms` WHERE `id` = '$room[id]' LIMIT 1");
    dbquery("DELETE FROM `chat_post` WHERE `room` = '$room[id]'");
    admin_log('聊天', '房间参数', "删除房间 '$room[name]'");
    msg('房间被成功删除');
}
if (user_access('chat_clear') && isset($_GET['act']) && $_GET['act'] == 'clear2') {
    admin_log('聊天', '结算', "信息清理室");
    dbquery("TRUNCATE `chat_post`");
    msg('所有房间都被清理干净');
}
