<?php
if (isset($user))
    echo "<div class='user_nick'>$user[nick]</div>";
else
    echo "<div class='user_nick'>客人</div>";
echo '<div class="user_menu">';

    if (isset($user)) {
        if (!isset($user) && !isset($_GET['id'])) {
            header("Location: /index.php?" . session_id());
        }
        if (isset($user)) $ank['id'] = $user['id'];
        echo "<div class='avatar'>";
        user::avatar($ank['id']);
        echo "</div>";
        include_once H . 'sys/inc/user/my_aut.php';
        echo "<br />";
        echo "
<div id='inf'>
<span class=\"ank_n\">分数:</span> <span class=\"ank_d\">$user[balls]</span><br />";
        echo "<span class=\"ank_n\">评级:</span> <span class=\"ank_d\">$user[rating]</span><br />";
        if ($user['level'] > 0) {
            if ($user['ip'] != 0) echo "<span class=\"ank_n\">IP:</span> <span class=\"ank_d\">" . long2ip($user['ip']) . "</span><br />";
            if ($user['ua'] != NULL) echo "<span class=\"ank_n\">UA:</span> <span class=\"ank_d\">$user[ua]</span><br />";
            if (opsos($user['ip'])) echo "<span class=\"ank_n\">IP:</span> <span class=\"ank_d\">" . opsos($user['ip']) . "</span></div><br />";
        }
    } else {
        echo "
<div class='form'>
<form method='post' action='/'>";
        echo "用户名:<br /><input type='text' name='nick' maxlength='32' /><br />";
        echo "密码 (<a href='/user/pass.php'>Забыли</a>):<br /><input type='password' name='pass' maxlength='32' /><br />";
        echo "<input type='submit' value='登录' />";
        echo "</form>";
        echo "<a href='/user/reg.php'><b>注册</b></a><br /></div>";
    }
    ?>
</div>