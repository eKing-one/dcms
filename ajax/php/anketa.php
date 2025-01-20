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


        $osebe = "<a href='/user/info/edit.php?act=ank_web&amp;set=osebe'>";
        $mat_pol = "<a href='/user/info/edit.php?act=ank_web&amp;set=mat_pol'>";
        $mail = "<a href='/user/info/edit.php?act=ank_web&amp;set=mail'>";
        $icq = "<a href='/user/info/edit.php?act=ank_web&amp;set=icq'>";
        $skype = "<a href='/user/info/edit.php?act=ank_web&amp;set=skype'>";
        $mobile = "<a href='/user/info/edit.php?act=ank_web&amp;set=mobile'>";
        $a = "</a>";
    } else {
        $mat_pol =  "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $osebe =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $mail =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $icq =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $skype =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $mobile =   "<font style='padding:1px; color : #005ba8; padding:1px;'>";
        $a = "</font>";
    }
    /*=====================================关于我=====================================*/
    if ($ank['ank_o_sebe'] != NULL) {
        echo "$osebe<span class=\"ank_n\">关于我:</span>$a <span class=\"ank_d\">" . output_text($ank['ank_o_sebe']) . "</span><br />";
    } else {
        echo "$osebe<span class=\"ank_n\">关于我:</span>$a<br />";
    }
    echo "</div>";
    
    /*=====================================联系方式=====================================*/
    echo "<div class='nav2'>";
    if ($ank['ank_icq'] != NULL && $ank['ank_icq'] != 0) {
        echo "$icq<span class=\"ank_n\">ICQ:</span>$a <span class=\"ank_d\">$ank[ank_icq]</span><br />";
    } else {
        echo "$icq<span class=\"ank_n\">ICQ:</span>$a<br />";
    }
    echo "$mail E-Mail:$a";
    if ($ank['email'] != NULL && ($ank['set_show_mail'] == 1 || isset($user) && ($user['level'] > $ank['level'] || $user['level'] == 4))) {
        if ($ank['set_show_mail'] == 0) {
            $hide_mail = ' (隐藏的)';
        } else {
            $hide_mail = NULL;
        }
        if (preg_match("#(@mail\.ru$)|(@bk\.ru$)|(@inbox\.ru$)|(@list\.ru$)#", $ank['email'])) {
            echo " <a href=\"mailto:$ank[email]\" title=\"写一封信\" class=\"ank_d\">$ank[email]</a>$hide_mail<br />";
        } else {
            echo " <a href=\"mailto:$ank[email]\" title=\"写一封信\" class=\"ank_d\">$ank[email]</a>$hide_mail<br />";
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
