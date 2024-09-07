<?
include_once '../../sys/inc/start.php';
include_once '../../sys/inc/compress.php';
include_once '../../sys/inc/sess.php';
include_once '../../sys/inc/home.php';
include_once '../../sys/inc/settings.php';
include_once '../../sys/inc/db_connect.php';
include_once '../../sys/inc/ipua.php';
include_once '../../sys/inc/fnc.php';
include_once '../../sys/inc/user.php';
only_reg();
$set['title'] = '对照片的评分';
include_once '../../sys/inc/thead.php';
title();
if (isset($user)) $ank['id'] = $user['id'];
$ank = user::get_user($ank['id']);
if (!$ank) {
    header("Location: /index.php?" . SID);
    exit;
}
err();
aut(); // форма авторизации
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/info.php'>$user[nick]</a> | 用户对照片的评分<br />";
echo "</div>";
$k_post = dbresult(dbquery("SELECT COUNT(*) FROM `gallery_rating` WHERE `avtor` = '$ank[id]'"), 0);
$k_page = k_page($k_post, $set['p_str']);
$page = page($k_page);
$start = $set['p_str'] * $page - $set['p_str'];
$q = dbquery("SELECT * FROM `gallery_rating` WHERE `avtor` = '$ank[id]' ORDER BY `time` DESC LIMIT $start, $set[p_str]");
if ($k_post == 0) {
    echo "  <div class='mess'>";
    echo "没有评分";
    echo "  </div>";
}
$num = 0;
while ($post = dbassoc($q)) {
    //$ank=dbassoc(dbquery("SELECT * FROM `user` WHERE `id` = $post[id_user] LIMIT 1"));
    $ank2 = user::get_user($post['id_user']);
    $photo = dbassoc(dbquery("SELECT * FROM `gallery_photo` WHERE `id` = $post[id_photo]"));
    if ($photo['id'] && $ank2['id']) {
        $gallery = dbassoc(dbquery("SELECT * FROM `gallery` WHERE `id` = $photo[id_gallery]"));
        //-----------代码-----------//
        if ($num == 0) {
            echo "  <div class='nav1'>";
            $num = 1;
        } elseif ($num == 1) {
            echo "  <div class='nav2'>";
            $num = 0;
        }
        //---------------------------//
        if ($post['read'] == 1) {
            $color = "<font color='red'>";
            $color2 = "</font>";
        } else {
            $color = null;
            $color2 = null;
        }
        echo "<table>";
        echo "   <tr>";
        echo "  <td style='vertical-align:top;'>";
        user::avatar($ank2['id']);
        echo user::nick($ank2['id']) . "<br />";
        echo "<img src='/style/icons/$post[like].png' alt=''/> $color" . vremja($post['time']) . "$color2";
        echo "  </td>";
        echo "  <td style='vertical-align:top;'>";
        echo "<a href='/photo/$user[id]/$gallery[id]/$photo[id]/'><img class='show_photo' src='/photo/photo" . ($set['web'] ? "128" : "50") . "/$photo[id].$photo[ras]' alt='$photo[name]' align='right'/></a>";
        echo "  </td>";
        echo "   </tr>";
        echo "</table>";
        echo "</div>";
    } else {
        dbquery("DELETE FROM `gallery_rating` WHERE `avtor` = '$post[avtor]' AND `id_photo` = '$post[id_photo]'");
    }
}
dbquery("UPDATE `gallery_rating` SET `read`='0' WHERE `avtor` = '$user[id]' AND `read`='1'");
if ($k_page > 1) str("?", $k_page, $page); // 输出页数
echo "<div class='foot'>";
echo "<img src='/style/icons/str2.gif' alt='*'> <a href='/user/info.php'>$user[nick]</a> | 对照片的评分<br />";
echo "</div>";
include_once '../../sys/inc/tfoot.php';
