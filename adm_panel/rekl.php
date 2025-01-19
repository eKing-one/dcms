<?php
include_once '../sys/inc/start.php';
include_once '../sys/inc/compress.php';
include_once '../sys/inc/sess.php';
include_once '../sys/inc/home.php';
include_once '../sys/inc/settings.php';
include_once '../sys/inc/db_connect.php';
include_once '../sys/inc/ipua.php';
include_once '../sys/inc/fnc.php';
include_once '../sys/inc/adm_check.php';
include_once '../sys/inc/user.php';

user_access('adm_rekl',null,'index.php?'.SID);
adm_check();

if (isset($_GET['sel']) && is_numeric($_GET['sel']) && $_GET['sel']>0 && $_GET['sel']<=4) {
    $sel=intval($_GET['sel']);
    $set['title']='网站广告';
    include_once '../sys/inc/thead.php';
    title();

    if (isset($_GET['add']) && isset($_POST['name']) && $_POST['name']!=NULL && isset($_POST['link']) && isset($_POST['img']) && isset($_POST['ch']) && isset($_POST['mn'])) {
        // 添加广告链接
        $ch=intval($_POST['ch']);
        $mn=intval($_POST['mn']);
        $time_last=time()+$ch*$mn*60*60*24;
        if (isset($_POST['dop_str']) && $_POST['dop_str']==1) {
            $dop_str=1;
        } else {
            $dop_str=0;
        }
        $link=stripcslashes(htmlspecialchars($_POST['link']));
        $name=stripcslashes(htmlspecialchars($_POST['name']));
        $img=stripcslashes(htmlspecialchars($_POST['img']));
        dbquery("INSERT INTO `rekl` (`time_last`, `name`, `img`, `link`, `sel`, `dop_str`) VALUES ('$time_last', '$name', '$img', '$link', '$sel', '$dop_str')");
        msg('添加广告链接');
    } elseif (isset($_GET['set']) && dbresult(dbquery("SELECT COUNT(*) FROM `rekl` WHERE `sel` = '$sel' AND `id` = '".intval($_GET['set'])."'"),0) && isset($_POST['name']) && isset($_POST['link']) && isset($_POST['img']) && isset($_POST['ch']) && isset($_POST['mn'])) {
        // 修改广告链接
        $rekl = dbassoc(dbquery("SELECT * FROM `rekl` WHERE `sel` = '$sel' AND `id` = '".intval($_GET['set'])."' LIMIT 1"));
        $ch=intval($_POST['ch']);
        $mn=intval($_POST['mn']);
        if ($rekl['time_last']>time()) {
            $time_last=$rekl['time_last']+$ch*$mn*60*60*24;
        } else {
            $time_last=time()+$ch*$mn*60*60*24;
        }
        $link=stripcslashes(htmlspecialchars($_POST['link']));
        $name=stripcslashes(htmlspecialchars($_POST['name']));
        $img=stripcslashes(htmlspecialchars($_POST['img']));
        if (isset($_POST['dop_str']) && $_POST['dop_str']==1) {
            $dop_str=1;
        } else {
            $dop_str=0;
        }
        dbquery("UPDATE `rekl` SET `time_last` = '$time_last', `name` = '$name', `link` = '$link', `img` = '$img', `dop_str` = '$dop_str' WHERE `id` = '".intval($_GET['set'])."'");
        msg('广告链接已更改');
    } elseif (isset($_GET['del']) && dbresult(dbquery("SELECT COUNT(*) FROM `rekl` WHERE `sel` = '$sel' AND `id` = '".intval($_GET['del'])."'"),0)) {
        // 删除广告链接
        dbquery("DELETE FROM `rekl` WHERE `id` = '".intval($_GET['del'])."' LIMIT 1");
        msg('广告链接已被删除');
    }

    err();
    aut();

    $k_post=dbresult(dbquery("SELECT COUNT(*) FROM `rekl` WHERE `sel` = '$sel'"),0);
    $k_page=k_page($k_post,$set['p_str']);
    $page=page($k_page);
    $start=$set['p_str']*$page-$set['p_str'];
    $q=dbquery("SELECT * FROM `rekl` WHERE `sel` = '$sel' ORDER BY `time_last` DESC LIMIT $start, $set[p_str]");
    echo "<table class='post'>";
    if ($k_post==0) {
        echo "   <tr>";
        echo "  <td class='p_t'>";
        echo "没有广告";
        echo "  </td>";
        echo "   </tr>";
    }
    while ($post = dbassoc($q)) {
        echo "   <tr>";
        echo "  <td class='p_t'>";
        if ($post['img']==NULL) {
            echo "$post[name]<br />";
        } else {
            echo "<a href='$post[img]'>[图片]</a><br />";
        }
        if ($post['time_last']>time()) {
            echo "(до ".vremja($post['time_last']).")";
        } else {
            echo "(显示期限已过期)";
        }
        echo "  </td>";
        echo "   </tr>";
        echo "   <tr>";
        echo "  <td class='p_m'>";
        echo "链接: $post[link]<br />";
        if ($post['img']!=NULL) echo "图片: $post[img]<br />";
        if ($post['dop_str']==1) echo "访问时间: $post[count]<br />";
        echo "<a href='rekl.php?sel=$sel&amp;del=$post[id]&amp;page=$page'>删除</a><br />";
        if (isset($_GET['set']) && $_GET['set']==$post['id']) {
            echo "<form method='post' action='rekl.php?sel=$sel&amp;set=$post[id]&amp;page=$page'>";
            echo "链接:<br /><input type=\"text\" name=\"link\" value=\"$post[link]\" /><br />";
            echo "标题:<br /><input type=\"text\" name=\"name\" value=\"$post[name]\" /><br />";
            echo "图片:<br /><input type=\"text\" name=\"img\" value=\"$post[img]\" /><br />";
            if ($post['time_last']>time()) {
                echo "Продлить на:<br />";
            } else {
                echo "延伸至:<br />";
            }
            echo "<input type=\"text\" name=\"ch\" size='3' value=\"0\" />";
            echo "<select name=\"mn\">";
            echo "  <option value=\"1\" selected='selected'>天数</option>";
            echo "  <option value=\"7\">星期</option>";
            echo "  <option value=\"31\">个月</option>";
            echo "</select><br />";
            if ($post['dop_str']==1) {
                $dop=" checked='checked'";
            } else {
                $dop=NULL;
            }
            echo "<label><input type=\"checkbox\"$dop name=\"dop_str\" value=\"1\" /> 附加页</label><br />";
            echo "<input value=\"申请\" type=\"submit\" />";
            echo "</form>";
            echo "<a href='rekl.php?sel=$sel&amp;page=$page'>取消</a><br />";
        } else {
            echo "<a href='rekl.php?sel=$sel&amp;set=$post[id]&amp;page=$page'>修改</a><br />";
        }
        echo "  </td>";
        echo "   </tr>";
    }
    echo "</table>";
    if ($k_page>1) str("rekl.php?sel=$sel&amp;",$k_page,$page); // 输出页数
    echo "<form class='foot' method='post' action='rekl.php?sel=$sel&amp;add'>";
    echo "标题:<br /><input type=\"text\" name=\"name\" value=\"\" /><br />";
    echo "链接:<br /><input type=\"text\" name=\"link\" value=\"\" /><br />";
    echo "图片:<br /><input type=\"text\" name=\"img\" value=\"\" /><br />";
    echo "有效期限:<br />";
    echo "<input type=\"text\" name=\"ch\" size='3' value=\"1\" />";
    echo "<select name=\"mn\">";
    echo "  <option value=\"1\">天数</option>";
    echo "  <option value=\"7\" selected='selected'>星期</option>";
    echo "  <option value=\"31\">个月</option>";
    echo "</select><br />";
    echo "<label><input type=\"checkbox\" checked='checked' name=\"dop_str\" value=\"1\" /> 附加页</label><br />";
    echo "<input value=\"添加\" type=\"submit\" />";
    echo "</form>";
    echo "<div class='foot'>";
    echo "<a href='rekl.php'>广告一览表</a><br />";

    if (user_access('adm_panel_show')) echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
    echo "</div>";
    include_once '../sys/inc/tfoot.php';
}

$set['title']='网站广告';
include_once '../sys/inc/thead.php';
title();
err();
aut();
echo "<div class='menu'>";
echo "<a href='rekl.php?sel=3'>网站底部(home)</a><br />";
echo "<a href='rekl.php?sel=4'>网站的底部（其余）</a><br />";
echo "</div>";
if (user_access('adm_panel_show')){
    echo "<div class='foot'>";
    echo "&laquo;<a href='/adm_panel/'>返回管理面板</a><br />";
    echo "</div>";
}
include_once '../sys/inc/tfoot.php';