<?
echo '<?xml version="1.0" encoding="utf-8"?>';

$set['web'] = true;
header("Content-type: text/html");
?>
<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <head>
        <title><? echo htmlspecialchars($set['title']); ?></title>
        <link rel="shortcut icon" href="/favicon.ico" /><link rel="stylesheet" href="/style/themes/<? echo $set['set_them']; ?>/style.css" type="text/css" />
        <link rel="stylesheet" href="/style/themes/<? echo $set['set_them']; ?>/tables.css" type="text/css" />

        <!-- Модальное окно -->
        <link rel="stylesheet" href="/ajax/style/style.css" type="text/css"/>
        <script type="text/javascript" src="/ajax/jquery.js"></script>
        <script type="text/javascript" src="/ajax/facebox.js"></script>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('a[rel*=facebox]').facebox({
                    loading_image: '/ajax/style/icons/loading.gif',
                    close_image: '/ajax/style/icons/closelabel.gif'
                })
            })
        </script>

        <script type="text/javascript" src="/ajax/ajax.js"></script><script type="text/javascript" src="/ajax/form-submit.js"></script>
        <link rel="alternate" title="Новости RSS" href="/news/rss.php" type="application/rss+xml" />
        <script src="/style/themes/<? echo $set['set_them']; ?>/js.js" type="text/javascript" language="JavaScript" charset="utf-8"></script>
        <!-- Диалоговое окно -->
        <script src="http://code.jquery.com/jquery-1.8.3.js"></script>
        <script src="/ajax/dialog.js"></script>
        <link type="text/css" href="/ajax/style/dialog.css" rel="stylesheet" />
        <script>
            function showContent2(link) {

                var cont = document.getElementById('contentBody');
                var loading = document.getElementById('loading');

                cont.innerHTML = loading.innerHTML;

                var http = createRequestObject();
                if (http)
                {
                    http.open('get', link);
                    http.onreadystatechange = function()
                    {
                        if (http.readyState == 4)
                        {
                            cont.innerHTML = http.responseText;
                        }
                    }
                    http.send(null);
                }
                else
                {
                    document.location = link;
                }
            }

            function createRequestObject()
            {
                try {
                    return new XMLHttpRequest()
                }
                catch (e)
                {
                    try {
                        return new ActiveXObject('Msxml2.XMLHTTP')
                    }
                    catch (e)
                    {
                        try {
                            return new ActiveXObject('Microsoft.XMLHTTP')
                        }
                        catch (e) {
                            return null;
                        }
                    }
                }
            }
        </script>  
    </head>
    <body><?php include_once H . 'style/themes/' . $set['set_them'] . '/title.php'; ?>
        <div class="head">
            <table class="nav">
                <tr>
                    <td class="logo">
                        <a href="/index.php" title="На главную"><img src="/style/themes/<? echo $set['set_them']; ?>/logo.png" alt="Logotype" /></a>
                    </td>
                    <td class="head_menu">
                        <?php include_once H . 'style/themes/' . $set['set_them'] . '/navigation.php'; ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="body">
            <table class="table">
                <tr>
                    <td class="block_menu_nav">
                        <?php include_once H . 'style/themes/' . $set['set_them'] . '/menu.php'; ?>
                    </td>
                    <td class="block_all_nav">
                        <div class="ind_cont">
                            <div class="title">
                                <? echo $set['title']; ?>
                            </div>
                            <div class='content_block'> 
                                <?
// Блок для подгрузки смайлов
                                if (isset($user)):
                                    ?>
                                    <div id="dialog" title="表情符号列表">
                                        <div id="contentBody">  

                                        </div>  

                                        <div id="loading" style="display: none">  
                                        正在加载...
                                        </div>
                                    </div><?
                                endif;
                                ?>