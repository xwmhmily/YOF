<?php
/**
 * File: menu.php
 * Functionality: 管理员左边的菜单数组
 * Author: Technology Department
 * Date: 2012-11-24
 * Remark: 
 * 1: name => 显示的文字
 * 2: url     => 链接
 * 3: dep   => 依赖关系
 * 	A: 如果有则表示如果勾选此项, 需要对应选中的 dep
 * 	B: 如果此项标识为 dep, 则表示此项不在左边菜单, 属于业务流程权限
 */

$menu = array(
	1 => array('name' => '角色权限',
		'sub' => array(
			100 => array(
				'name' => '角色列表',
				'url' => '/admin/role',
			),
		),
	),
	2 => array('name' => '用户管理',
		'sub' => array(
			200 => array(
				'name' => '用户列表',
				'url' => '/admin/user',
			),
		),
	),
	3 => array('name' => '文章管理',
		'sub' => array(
			300 => array(
				'name' => '文章列表',
				'url' => '/admin/article',
			),
			301 => array(
				'name' => '文章静态化',
				'url' => '/admin/article/static',
			),
		),
	),
);