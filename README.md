手机网站的内容管理系统

安装要求：

PHP4.2及以上（推荐PHP5）、MySQL、mod_rewrite支持、服务器。

•如果您能够在免费托管中安装和充分使用引擎，请在项目官方网站的论坛上报告：http://dcms-social.ru/forum/ 

推荐库（如果没有这些库，可能会缺少一些功能）：

1）iconv

2）FFMPEG

3）GD

4）mcrypt

包含模块：

1）聊天（聪明人+1000个问题，笑话+1000个笑话）。

2）论坛（2层，附加文件，搜索，书签）。

3）下载中心（无限数量的子文件夹、上传、导入、截图，评论，直接到文件的下载计数器。）

4）文件交换（正确支持中文文件和文件夹名称，无限子文件夹数量、屏幕截图、文件信息、自定义设置每个文件夹的上传）。

5）图书馆

6）RSS新闻

7）访客

8）投票系统

主要文件夹和引擎文件：

•附加到论坛的文件：sys/forum/files/（*.frf）

•交换机文件：sys/down/files/（*.DAT）

•主题：style/themes/（主题文件夹）

•网站规则：sys/add/rules.txt

•默认主题存档：sys/add/them.zip（用于通过管理员安装主题时替换丢失的主题文件）

安装：

1）创建MySQL数据库（是数据库，而不是表）。

2）将所有文件上载到根目录或子域文件夹。（引擎不会在子文件夹中工作）。

3）转到http://[您的网站]/install/

4）遵循所有安装步骤。

5）如果您在下一步安装方面遇到困难，或者您对引擎的改进有任何建议，请访问我们的论坛http://dcms-social.ru/forum/

额外的模块可以手动下载和安装。

如果您对开发引擎感兴趣，可以通过

电子支付系统WebMoney。钱包：R289951892735。

请向论坛申请编写模块的订单。

时装设计师（CMS）“DCMS-Social”：探索者

电子邮件：alex-borisi@ya.ru

项目官方网站：http://dcms-social.ru

支持：http://dcms-social.ru/forum/


plugins 全新设计
//是否显示图片
if ($set['set_show_icon'] == 2) {
    user::avatar($ank_kont['id']);
} elseif ($set['set_show_icon'] == 1) {
    echo user::avatar($ank_kont['id']);
}

user::nick()
user::get_user(,1,1,0)
user::avatar()       

待办事项
之前说过管理员能把站长改成普通用户还能禁言
刚刚我看到很久以前有人连着创建了10个小号不用就清理了
然后我发现还能删站长的号？
这两种bug会修吗