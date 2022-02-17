<?
list($msec, $sec) = explode(chr(32), microtime());



if ($_SERVER['PHP_SELF'] != '/index.php') 
{
?>
	<div class="foot">
	<img src="/style/icons/icon_glavnaya.gif" alt="*" /> <a href="/index.php">На главную</a>
	</div>
<?
}
?>
<div class="copy">
<center>
&copy; <a href="http://dcms-social.ru" style="text-transform: capitalize;"><?=text($_SERVER['HTTP_HOST'])?></a> - <?=date('Y');?> г.
</center>
</div>

<div class="foot">
На сайте: 
<a href="/online.php"><?=dbresult(dbquery("SELECT COUNT(*) FROM `user` WHERE `date_last` > ".(time()-600).""), 0)?></a> &amp;
<a href="/online_g.php"><?=dbresult(dbquery("SELECT COUNT(*) FROM `guests` WHERE `date_last` > ".(time()-600)." AND `pereh` > '0'"), 0)?></a>

</div>

<div class="rekl">
<?
$page_size = ob_get_length(); 
ob_end_flush();
rekl(3);
?>
<center>
PGen: <?=round(($sec + $msec) - $conf['headtime'], 3)?>сек
</center>
</div>

</div>
  
  
<script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


<script>
function change_ajax(link){
      $('#load').html('<img src="/load.gif" style="position: fixed; top: 25%; left: 50%;transform: translateX(-50%); text-align: center; background: rgba(50,50,50,0.5); padding: 5px;">');

      $.post (link, {'load_ajax' : null},
          function (data){
            var data = $(data);
            var elem = data.find('#content').html();
            $("#content").html(elem);
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
            $('#load').html('');
          }
      );
    }

    if (history.pushState){
      $(window).on('popstate', function(event){
        var loc = event.location || ( event.originalEvent && event.originalEvent.location ) || document.location;
        change_ajax(loc.href);
      });
 $(document).on('click', 'a[load == "ajax"]', function(e){
        var link = $(this).attr('href');
        if (link != null){
          change_ajax(link);
          var titl = $('div[title]').text();
          document.title = titl;
          history.pushState(link, titl, link);
          e.preventDefault();
        }
      });
         }
  </script>
  
</body>
</html>
<?
exit;
?>