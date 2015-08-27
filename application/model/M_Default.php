<?php
/**
 * File: M_Default.php
 * Functionality: Default model
 * Author: Nic XIE
 * Date: 2013-5-8
 * Remark:
 */

class M_Default extends Model {

	function __construct($table) {
		$this->table = TB_PREFIX.$table;
		parent::__construct();
	}

}