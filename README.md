# YOF
A Fast, Simple PHP Framework based on YAF&amp; Orange with a login/register/logout, aritcle publish, admin control panel DEMO

特点<br />
1: 基于YAF 和 Orange 开发的一个PHP 框架, 简单, 易用 <br />
2: 底层使用的鸟哥的 YAF, MySQL 封装使用的是 Orange 的PDO 类, 支持链式操作! 调用非常方便 <br />
3: 支持多种运行环境, 根据运行环境决定域名常量,MySQL参数, 错误报告等 <br />
4: 支持默认控制器, 默认模型 [可以不创建模型文件也能对表进行CURD] <br />
5: DEMO 中包括用户的简单登录,注册,注销.文章的发布,管理,分页.个人资料修改 <br />
6: 后台管理登录,注销,修改密码, 文章&用户管理, 权限分配 <br />
7: DEV 下MySQL 和PHP 错误全部都打印出来, 方便调试, TEST , WWW 下将不打印, 分别记录在对应的文件里 <br />
8: 简易省市区三级联动 <br />
9: 三个模块:User, Api, Admin <br />

安装 [建议在Linux下使用]<br />
1: 安装YAF 扩展 <br />
2: 配置好 conf/DB_config.php 里的MySQL参数并将 dym.sql 导入自己的数据库<br />
3: 配置虚拟主机, 写对应的HOSTS<br />
4: 运行<br />

其他: 若发现有BUG 或更好的建议,请联系 xwmhmily@126.com
