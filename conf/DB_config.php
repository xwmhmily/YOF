<?php
/*
 * File: DB_config.php
 * Functionality: DB config
 * Author: Nic XIE
 * Date: 2012-2-10
 * Remark: if there are more then one DBs in your applicaton, add as below
 	'DB_KEY' => 'DB_NAME',
	 For example: 'DB' => 'test', 'LOG' => 'log',
 	 And 'TYPE' is prepared for PDO !
 */

// 之所以没放入 application.ini, 是因为要区分 ENVIRONMENT
if('DEV' == strtoupper(ENVIRONMENT)){
	$DB_Config = array(
		'TYPE' => 'mysql',
		'READ_HOST' => '127.0.0.1',
		'READ_PORT' => 3306,

		'READ_USER' => 'root',
		'READ_PSWD' => '123456',

		'WRITE_HOST' => '127.0.0.1',
		'WRITE_PORT' => 3306,

		'WRITE_USER' => 'root',
		'WRITE_PSWD' => '123456',

		'Default'  => 'dym',
	);
}else {
	$DB_Config = array(
		'TYPE' => 'mysql',
		'READ_HOST' => '127.0.0.1',
		'READ_PORT' => 3306,

		'READ_USER' => 'root',
		'READ_PSWD' => '',

		'WRITE_HOST' => '127.0.0.1',
		'WRITE_PORT' => 3306,

		'WRITE_USER' => 'root',
		'WRITE_PSWD' => '',

		'Default'  => 'orange',
	);
}
