# YOF
A Fast, Simple PHP Framework based on YAF&amp; Orange with a login/register/logout, aritcle publish, admin control panel DEMO

<H3>特点</H3>

> 1: 基于YAF 和 Orange 开发的一个PHP 框架, 简单, 易用, 高效

> 2: 底层使用的鸟哥的 YAF, MySQL 封装使用的是 Orange 的PDO 类, 支持链式操作! 调用非常方便

> 3: 支持多种运行环境, 根据运行环境决定域名常量,MySQL参数, 错误报告等

> 4: 支持默认控制器, 默认模型 [可以不创建模型文件也能对表进行CURD] 

> 5: DEMO 中包括用户的简单登录,注册,注销.文章的发布,管理,分页.个人资料修改

> 6: 后台管理登录,注销,修改密码, 文章&用户管理, 权限分配 

> 7: DEV 下MySQL 和PHP 错误全部都打印出来, 方便调试, TEST , WWW 下将不打印, 分别记录在对应的文件里 

> 8: 简易省市区三级联动

> 9: 三个模块:User, Api, Admin 

> 10: 一些常用的函数封装,位于 function 目录

<H3>安装 [建议在Linux下使用]</H3>

> 1: 安装YAF 扩展

> 2: 配置好 conf/DB_config.php 里的MySQL参数并将 dym.sql 导入自己的数据库

> 3: 配置虚拟主机, 写对应的HOSTS

> 4: 解压文件至指定目录,运行


<H3>设置</H3>
1: MySQL 参数 conf/DB_config.php => 默认支持读写分离, 若不分离, 设置为一样的值即可

2: 环境设置: environment.php

   > A: 开发环境请设置为 DEV, 此时所有错误将打印出来

   > B: 线上测试环境设置为 TEST, 此时 PHP 的错误将记录在 APP_PATH 下的 $当天日期_php.log, SQL 的错误将记录在 APP_PATH 下的 $当天日期_sql.log

   > C: 正式生产环境设置为 WWW, 此时 PHP 的错误将记录在 APP_PATH 下的 $当天日期_php.log, SQL 的错误将记录在 APP_PATH 下的 $当天日期_sql.log

   > D: 维护情况下设置为 MAINTAINCE, 此时访问网站将只显示一句话: 服务器正在维护, 请稍候访问. 当时可以自定义得更好些

注:正式生产环境千万不能设置为 DEV, 切记!!!

3: 配置网站域名, 图片域名, 静态文件域名等, 避免硬编码

   > 请打开 init.php, 根据 DEV, TEST, WWW 自行对 $SERVER_DOMAIN, $STATIC_DOMAIN, $IMG_DOMAIN 根据情况设置





其他: 若发现有BUG 或更好的建议,请联系 xwmhmily@126.com, 谢谢
