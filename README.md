# DCMS - 手机网站内容管理系统

DCMS 原是俄罗斯的社交网站和 CMS，后由 [eKing](https://github.com/eKing-one) 进行简中翻译后，通过 [CN_DCMS-Social](http://dcms.net.cn/) 引入中文互联网，以几乎所有老旧手机都能访问闻名怀旧圈，迅速吸引一批 Z 世代扎根。现在，eKing 将 CN_DCMS-Social 的源代码公开（基于原版发行版 `1.9.11`），以获得更好的发展。

（以下内容由 DCMS 原作者 [Alex Borisi](mailto:alex-borisi@ya.ru) 编写，eKing 初步翻译，经 [Diamochang](https://github.com/Diamochang) 使用[通义千问](https://tongyi.aliyun.com/qianwen)优化翻译并规范 Markdown 格式）

## 安装要求

- PHP 7 （推荐使用PHP 7.4.3）
- MySQL 数据库支持
- Apache `mod_rewrite` 模块支持（可用Nginx替代）

### 推荐库

以下库为可选但**强烈建议**安装，以确保所有功能的正常使用：

1. iconv
2. FFmpeg
3. GD
4. mcrypt

## 功能模块

1. 聊天系统（包含全民答题 +1000 个问题， +1000 个笑话）
2. 论坛（支持双层嵌套，附件上传，搜索功能，书签）
3. 下载中心（无限子文件夹数量，上传、导入、截图功能，评论及直接统计文件下载次数）
4. 文件交换平台（全面支持中文文件和文件夹名称，无限子文件夹数量，屏幕截图，文件信息，可为每个文件夹自定义上传设置）
5. 图书馆功能
6. RSS新闻订阅
7. 访客统计功能
8. 投票系统

## 主要文件夹与引擎文件

- 论坛附件文件：`sys/forum/files/`（*.frf 文件）
- 下载中心文件：`sys/down/files/`（*.DAT 文件）
- 主题样式文件：`style/themes/`（主题文件夹）
- 网站规则文件：`sys/add/rules.txt`
- 默认主题存档：`sys/add/theme.zip`（用于管理员安装或替换丢失的主题文件）

## 安装前体验

你可以前往 [GuGuan123](https://github.com/guguan123/) 开设的[副站](https://dcms.myredirect.us/)体验各项功能的最新改进。相关技术信息请参见[他的博客](http://u5a.cn/B8Ng5)。

## 安装步骤

1. 克隆本仓库。
2. 创建一个 MySQL 数据库（注意是数据库而不是表）。
3. 将所有文件上传至根目录或子域文件夹（请注意，引擎无法在子文件夹内运行）。
4. 访问 `http://[您的网站]/install/`
5. 遵循所有安装步骤进行操作。

如遇安装困难或有任何改进建议，请访问我们的[网站](http://dcms.net.cn/)，在[论坛板块](https://dcms.net.cn/forum/12/20/)中反馈。

## 扩展模块

额外模块可以手动下载和安装。

安装方式：下载压缩包，然后解压到网站根目录。

## 开发贡献

如果您对开发引擎有兴趣，可 Fork 本仓库并在修改后创建 Pull Request。

## 本项目引用的第三方库

- [IPSet](https://github.com/wikimedia/ipset)
- [ua-parser](https://github.com/ua-parser/uap-php)

## 待办事项

- [ ] 移除代码 `version_stable()`
- [ ] 移除 token 相关代码
- [x] 修复在 "书签" 页面无法翻页
- [ ] 将用户名与昵称区分,在个人主页可显示用户名(或仅限管理员可查看用户名)
- [ ] 纠正翻译和翻译部分残留的文本
- [x] CDN 支持
- [x] 暗色模式
- [ ] 档案页报错
- [ ] "网站领袖" 新译名
- [ ] "书签分类" -> "日记" 翻页后无法显示标题
- [ ] "书签分类" -> "论坛" 出现数据库查询的 Warning 报错
- [x] 完善“是否允许游客访问《网站资料与帮助》页面”功能，优化 plugins/rules/index.php:13 与 plugins/rules/post.php:13 的代码实现方式
- [ ] 修复RSS订阅功能
- [ ] 修复更新页面
- [ ] 提供给客户端的API
- [ ] PHP 8 支持
