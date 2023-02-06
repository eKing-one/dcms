<?php
if (isset($_GET['id'])) {
    include_once '../../sys/inc/start.php';
    include_once '../../sys/inc/compress.php';
    include_once '../../sys/inc/sess.php';
    include_once '../../sys/inc/home.php';
    include_once '../../sys/inc/settings.php';
    include_once '../../sys/inc/db_connect.php';
    include_once '../../sys/inc/ipua.php';
    include_once '../../sys/inc/fnc.php';
    include_once '../../sys/inc/user.php';
    $ank = user::get_user(intval($_GET['id']));
    echo ' <a onclick="anketaClose.submit()" name="myForm"><div class="form_info">隐藏详细信息</div></a>';
    /*=====================================用户配置文件，如果作者，我们输出链接到编辑字段，如果没有，那么没有=）=====================================*/
    if (isset($user) && $ank['id'] == $user['id']) {
        $loves = "<a href='/user/info/edit.php?act=ank_web&amp;set=loves'>";
        $opar = "<a href='/user/info/edit.php?act=ank_web&amp;set=opar'>";
        $volos = "<a href='/user/info/edit.php?act=ank_web&amp;set=volos'>";
        $ves = "<a href='/user/info/edit.php?act=ank_web&amp;set=ves'>";
        $glaza = "<a href='/user/info/edit.php?act=ank_web&amp;set=glaza'>";
        $rost = "<a href='/user/info/edit.php?act=ank_web&amp;set=rost'>";
        $osebe = "<a href='/user/info/edit.php?act=ank_web&amp;set=osebe'>";
        $telo = "<a href='/user/info/edit.php?act=ank_web&amp;set=telo'>";
        $avto = "<a href='/user/info/edit.php?act=ank_web&amp;set=avto'>";
        $baby = "<a href='/user/info/edit.php?act=ank_web&amp;set=baby'>";
        $proj = "<a href='/user/info/edit.php?act=ank_web&amp;set=proj'>";
        $zan = "<a href='/user/info/edit.php?act=ank_web&amp;set=zan'>";
        $smok = "<a href='/user/info/edit.php?act=ank_web&amp;set=smok'>";
        $mat_pol = "<a href='/user/info/edit.php?act=ank_web&amp;set=mat_pol'>";
        $mail = "<a href='/user/info/edit.php?act=ank_web&amp;set=mail'>";
        $icq = "<a href='/user/info/edit.php?act=ank_web&amp;set=icq'>";
        $skype = "<a href='/user/info/edit.php?act=ank_web&amp;set=skype'>";
        $mobile = "<a href='/user/info/edit.php?act=ank_web&amp;set=mobile'>";
        $a = "</a>";
    } else {
        $loves = "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $opar = "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $avto = "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $baby =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $zan = "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $smok = "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $mat_pol =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $proj =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $telo =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $volos = "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $ves =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $glaza =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $rost =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $osebe =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $mail =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $icq =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $skype =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $mobile =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $alko =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $nark =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $a = "</font>";
    }
    echo '<div class="nav2">';
    if ($ank['ank_rost'] != NULL) {
        echo $rost . '<span class="ank_n">身高:</span>' . $a . ' <span class="ank_d">' . $ank['ank_rost'] . '</span><br />';
    } else {
        echo $rost . '<span class="ank_n">身高:</span>' . $a . '<br />';
    }
    if ($ank['ank_ves'] != NULL) {
        echo $ves . '<span class="ank_n">体重:</span>' . $a . ' <span class="ank_d">' . $ank['ank_ves'] . '</span><br />';
    } else {
        echo $ves . '<span class="ank_n">体重:</span>' . $a . '<br />';
    }
    if ($ank['ank_cvet_glas'] != NULL) {
        echo $glaza . '<span class="ank_n">眼睛颜色:</span>' . $a . ' <span class="ank_d">' . $ank['ank_cvet_glas'] . '</span><br />';
    } else {
        echo $glaza . '<span class="ank_n">眼睛颜色:</span>' . $a . '<br />';
    }
    if ($ank['ank_volos'] != NULL) {
        echo $volos . '<span class="ank_n">头发:</span>' . $a . ' <span class="ank_d">' . $ank['ank_volos'] . '</span><br />';
    } else {
        echo $volos . '<span class="ank_n">头发:</span>' . $a . '<br />';
    }
    echo $telo . '<span class="ank_n">身体状况:</span>' . $a . '';
    if ($ank['ank_telosl'] == 1) {
        echo ' <span class="ank_d">没有人回复</span><br />';
    }
    if ($ank['ank_telosl'] == 2) {
        echo ' <span class="ank_d">瘦骨嶙峋</span><br />';
    }
    if ($ank['ank_telosl'] == 3) {
        echo ' <span class="ank_d">平常的</span><br />';
    }
    if ($ank['ank_telosl'] == 4) {
        echo ' <span class="ank_d">运动项目</span><br />';
    }
    if ($ank['ank_telosl'] == 5) {
        echo ' <span class="ank_d">肌肉发达</span><br />';
    }
    if ($ank['ank_telosl'] == 6) {
        echo ' <span class="ank_d">密密麻麻</span><br />';
    }
    if ($ank['ank_telosl'] == 7) {
        echo ' <span class="ank_d">全</span><br />';
    }
    if ($ank['ank_telosl'] == 0) {
        echo '<br />';
    }
    echo '</div>';
    /*=====================================约会用=====================================*/
    echo "<div class='nav1'>";
    echo "$loves<span class=\"ank_n\">约会目标:</span>$a<br />";
    if ($ank['ank_lov_1'] == 1) {
        echo "&raquo; 友谊与沟通<br />";
    }
    if ($ank['ank_lov_2'] == 1) {
        echo "&raquo; 通信<br />";
    }
    if ($ank['ank_lov_3'] == 1) {
        echo "&raquo; 爱情，关系<br />";
    }
    if ($ank['ank_lov_4'] == 1) {
        echo "&raquo; 经常性在一起<br />";
    }
    if ($ank['ank_lov_5'] == 1) {
        echo "&raquo; 性一两次<br />";
    }
    if ($ank['ank_lov_6'] == 1) {
        echo "&raquo; 团体性<br />";
    }
    if ($ank['ank_lov_7'] == 1) {
        echo "&raquo; 虚拟性<br />";
    }
    if ($ank['ank_lov_8'] == 1) {
        echo "&raquo; 我为钱提供性<br />";
    }
    if ($ank['ank_lov_9'] == 1) {
        echo "&raquo; 寻找性别为了钱<br />";
    }
    if ($ank['ank_lov_10'] == 1) {
        echo "&raquo; 婚姻、家庭创造<br />";
    }
    if ($ank['ank_lov_11'] == 1) {
        echo "&raquo; 出生，抚养孩子<br />";
    }
    if ($ank['ank_lov_12'] == 1) {
        echo "&raquo; 为vi结婚是的<br />";
    }
    if ($ank['ank_lov_13'] == 1) {
        echo "&raquo; 联合出租房屋<br />";
    }
    if ($ank['ank_lov_14'] == 1) {
        echo "&raquo; 体育活动<br />";
    }
    if ($ank['ank_o_par'] != NULL) {
        echo "$opar<span class=\"ank_n\">关于合作伙伴:</span>$a <span class=\"ank_d\">" . output_text($ank['ank_o_par']) . "</span><br />";
    } else {
        echo "$opar<span class=\"ank_n\">关于合作伙伴:</span>$a<br />";
    }
    if ($ank['ank_o_sebe'] != NULL) {
        echo "$osebe<span class=\"ank_n\">关于我:</span>$a <span class=\"ank_d\">" . output_text($ank['ank_o_sebe']) . "</span><br />";
    } else {
        echo "$osebe<span class=\"ank_n\">关于我:</span>$a<br />";
    }
    echo "</div>";
    /*=====================================关于我=====================================*/
    echo "<div class='nav2'>";
    if ($ank['ank_zan'] != NULL) {
        echo "$zan<span class=\"ank_n\">我的工作:</span>$a <span class=\"ank_d\">" . output_text($ank['ank_zan']) . "</span><br />";
    } else {
        echo "$zan<span class=\"ank_n\">我的工作:</span>$a<br />";
    }
    echo "$smok<span class=\"ank_n\">吸烟:</span>$a";
    if ($ank['ank_smok'] == 1) {
        echo " <span class=\"ank_d\">我不抽烟</span><br />";
    }
    if ($ank['ank_smok'] == 2) {
        echo " <span class=\"ank_d\">我抽烟</span><br />";
    }
    if ($ank['ank_smok'] == 3) {
        echo " <span class=\"ank_d\">很少</span><br />";
    }
    if ($ank['ank_smok'] == 4) {
        echo " <span class=\"ank_d\">我不干了</span><br />";
    }
    if ($ank['ank_smok'] == 5) {
        echo " <span class=\"ank_d\">成功退出</span><br />";
    }
    if ($ank['ank_smok'] == 0) {
        echo "<br />";
    }
    echo "$mat_pol<span class=\"ank_n\">财务状况:</span>$a";
    if ($ank['ank_mat_pol'] == 1) {
        echo " <span class=\"ank_d\">非永久性收入</span><br />";
    }
    if ($ank['ank_mat_pol'] == 2) {
        echo " <span class=\"ank_d\">永久小额收入</span><br />";
    }
    if ($ank['ank_mat_pol'] == 3) {
        echo " <span class=\"ank_d\">稳定的平均收入</span><br />";
    }
    if ($ank['ank_mat_pol'] == 4) {
        echo " <span class=\"ank_d\">我挣得很好/我有条件</span><br />";
    }
    if ($ank['ank_mat_pol'] == 5) {
        echo " <span class=\"ank_d\">我不赚钱</span><br />";
    }
    if ($ank['ank_mat_pol'] == 0) {
        echo "<br />";
    }
    echo "$avto<span class=\"ank_n\">汽车的可用性:</span>$a";
    if ($ank['ank_avto_n'] == 1) {
        echo " <span class=\"ank_d\">有</span><br />";
    }
    if ($ank['ank_avto_n'] == 2) {
        echo " <span class=\"ank_d\">取消</span><br />";
    }
    if ($ank['ank_avto_n'] == 3) {
        echo " <span class=\"ank_d\">我要买了。</span><br />";
    }
    if ($ank['ank_avto_n'] == 0) {
        echo "<br />";
    }
    if ($ank['ank_avto'] && $ank['ank_avto_n'] != 2 && $ank['ank_avto_n'] != 0) {
        echo "&raquo; <span class=\"ank_d\">" . output_text($ank['ank_avto']) . "</span><br />";
    }
    echo "$proj<span class=\"ank_n\">住宿设施:</span>$a";
    if ($ank['ank_proj'] == 1) {
        echo " <span class=\"ank_d\">独立公寓（出租或拥有）</span><br />";
    }
    if ($ank['ank_proj'] == 2) {
        echo " <span class=\"ank_d\">宿舍、公共公寓</span><br />";
    }
    if ($ank['ank_proj'] == 3) {
        echo " <span class=\"ank_d\">我和父母住在一起</span><br />";
    }
    if ($ank['ank_proj'] == 4) {
        echo " <span class=\"ank_d\">我和朋友住在一起/和朋友住在一起</span><br />";
    }
    if ($ank['ank_proj'] == 5) {
        echo " <span class=\"ank_d\">我和伴侣或配偶住在一起</span><br />";
    }
    if ($ank['ank_proj'] == 6) {
        echo " <span class=\"ank_d\">没有永久住房</span><br />";
    }
    if ($ank['ank_proj'] == 0) {
        echo "<br />";
    }
    echo "$baby<span class=\"ank_n\">有没有孩子:</span>$a";
    if ($ank['ank_baby'] == 1) {
        echo " <span class=\"ank_d\">取消</span><br />";
    }
    if ($ank['ank_baby'] == 2) {
        echo " <span class=\"ank_d\">不，但我想</span><br />";
    }
    if ($ank['ank_baby'] == 3) {
        echo " <span class=\"ank_d\">是的，我们住在一起</span><br />";
    }
    if ($ank['ank_baby'] == 4) {
        echo " <span class=\"ank_d\">是的，我们分开住</span><br />";
    }
    if ($ank['ank_proj'] == 0) {
        echo "<br />";
    }
    echo "</div>";
    if (isset($user) && $ank['id'] == $user['id']) {
        $alko = "<a href='/user/info/edit.php?act=ank_web&amp;set=alko'>";
        $nark = "<a href='/user/info/edit.php?act=ank_web&amp;set=nark'>";
    }
    /*=====================================此外=====================================*/
    echo "<div class='nav1'>";
    echo "$alko<span class=\"ank_n\">酒精:</span>$a";
    if ($ank['ank_alko_n'] == 1) echo " <span class=\"ank_d\">是的，我喝酒</span><br />";
    if ($ank['ank_alko_n'] == 2) echo " <span class=\"ank_d\">很少，在假期</span><br />";
    if ($ank['ank_alko_n'] == 3) echo " <span class=\"ank_d\">不，我断然不接受</span><br />";
    if ($ank['ank_alko_n'] == 0) echo "<br />";
    if ($ank['ank_alko'] && $ank['ank_alko_n'] != 3 && $ank['ank_alko_n'] != 0) echo "&raquo; <span class=\"ank_d\">" . output_text($ank['ank_alko']) . "</span><br />";
    echo "</div>";
    /*=====================================联系人=====================================*/
    echo "<div class='nav2'>";
    if ($ank['ank_icq'] != NULL && $ank['ank_icq'] != 0) {
        echo "$icq<span class=\"ank_n\">ICQ:</span>$a <span class=\"ank_d\">$ank[ank_icq]</span><br />";
    } else {
        echo "$icq<span class=\"ank_n\">ICQ:</span>$a<br />";
    }
    echo "$mail E-Mail:$a";
    if ($ank['ank_mail'] != NULL && ($ank['set_show_mail'] == 1 || isset($user) && ($user['level'] > $ank['level'] || $user['level'] == 4))) {
        if ($ank['set_show_mail'] == 0) {
            $hide_mail = ' (隐藏的)';
        } else {
            $hide_mail = NULL;
        }
        if (preg_match("#(@mail\.ru$)|(@bk\.ru$)|(@inbox\.ru$)|(@list\.ru$)#", $ank['ank_mail'])) {
            echo " <a href=\"mailto:$ank[ank_mail]\" title=\"写一封信\" class=\"ank_d\">$ank[ank_mail]</a>$hide_mail<br />";
        } else {
            echo " <a href=\"mailto:$ank[ank_mail]\" title=\"写一封信\" class=\"ank_d\">$ank[ank_mail]</a>$hide_mail<br />";
        }
    } else {
        echo "<br />";
    }
    if ($ank['ank_n_tel'] != NULL) {
        echo "$mobile<span class=\"ank_n\">电话:</span>$a <span class=\"ank_d\">$ank[ank_n_tel]</span><br />";
    } else {
        echo "$mobile<span class=\"ank_n\">电话:</span>$a<br />";
    }
    if ($ank['ank_skype'] != NULL) {
        echo "$skype<span class=\"ank_n\">Skype:</span>$a <span class=\"ank_d\">$ank[ank_skype]</span><br />";
    } else {
        echo "$skype<span class=\"ank_n\">Skype:</span>$a<br />";
    }
    echo "</div>";
    echo "</div>";
} else {
    echo ' <a onclick="anketa.submit()" name="myForm"><div class="form_info">显示详细资料</div></a>';
}
