<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
$show_all=true;
include_once '../sys/inc/user.php';
include_once '../sys/inc/thead.php';
title();
aut();


// 检测旧密码是否正确并更新
function check_and_update_password($nick, $password) {
    // 获取用户的加密密码
    $result = dbquery("SELECT pass FROM user WHERE nick = '" . my_esc($nick) . "'");
    
    if (dbrows($result) > 0) {
        $current_password = dbresult($result, 0, 0);
        if (strlen($current_password) > 32) {
            return 'Your password is new and you don\'t need to update it';
        }

        // 使用 shif 函数加密旧密码进行比较
        if ($current_password == shif($password)) {
            return 'Your password is valid, but you should update it.';
        } else {
            return 'Old password is incorrect.';
        }
    } else {
        return 'User not found.';
    }
}

// 更新密码
function update_password($nick, $password) {
    // 验证旧密码是否正确并更新密码
    $result_message = check_and_update_password($nick, $password);
    
    if ($result_message === 'Your password is valid, but you should update it.') {
        // 旧密码正确且长度合法，使用 password_hash 函数重新加密密码
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // 更新密码到数据库
        $query = "UPDATE user SET pass = '" . my_esc($hashed_password) . "' WHERE nick = '" . my_esc($nick) . "'";
        $update_result = dbquery($query);
        
        if ($update_result) {
            return 'Password updated successfully.';
        } else {
            return 'Failed to update password.';
        }
    } else {
        return $result_message;
    }
}

if (isset($_POST['nick'])&& isset($_POST['pass'])) {
    $password = $_POST['pass'];
    $nick = $_POST['nick'];
    // 更新密码并获取反馈信息
    $update_password = update_password($nick, $password);
    if ($update_password == 'Password updated successfully.') {
        msg($update_password);
    } else {
        $err[] = $update_password;
        err();
    }
}
?>
<form method='post'>输入昵称：<input name='nick' type='text' /><br/>输入密码：<input name='pass' type='password' /><br/><input type='submit' value='更新' /></form>
<?php
include_once '../sys/inc/tfoot.php';