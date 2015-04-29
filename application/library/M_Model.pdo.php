<?php
/**
 * File: M_Model.class.php
 * Functionality: Core PDO model class
 * Author: Nic XIE
 * Date: 2013-2-28
 * Note:
 *	1 => This class requires PDO support !
 *	2 => $conn MUST BE set to static for transaction !
 * ---------------- DO NOT MODIFY THIS FILE UNLESS YOU FULLY UNDERSTAND ! -------------
 */

abstract class M_Model {

	private static $obj;
	private static $conn;
	private $result = NULL;				
	protected $table = '';				
	private $options = '';              // SQL 中的 field, where, orderby, limit
	private $selectOne = FALSE;

	// success code of PDO
	private $successCode = '00000';

	// The result of last operation: 0 => failure,  1 => success
	private $success = FALSE;

	// SQL log file: Log SQL error for debug if NOT under DEV
	private $logFile = '';

	/**
	 * Constructor
	 * <br /> 1: Connect to MySQL
	 *
	 * @param string => use default DB if parameter is not specified !
	 * @return NULL
	 */
	function __construct($db = '') {
		$this->logFile = APP_PATH. '/'.CUR_DATE.'_sql.log';
		if(!file_exists($this->logFile) && ENVIRONMENT != 'DEV'){
			touch($this->logFile);
		}

		if($db){
			$this->connect($db);
		}else{
			if(!$this->conn){
				$this->connect();
			}
		}
	}

	/**
	 * Connect to MySQL [Support read/write splitting]
	 *
	 * @param string => use default DB if parameter is not specified !
	 * @return NULL
	 */
	private function connect($type = 'WRITE') {
		include CONFIG_PATH.'/DB_config.php';
		$db     = $DB_Config['Default'];
		$driver = $DB_Config['TYPE'];

		$host = $DB_Config[$type.'_HOST'];
		$port = $DB_Config[$type.'_PORT'];
		$user = $DB_Config[$type.'_USER'];
		$pswd = $DB_Config[$type.'_PSWD'];

		if(!$port){
			$port = 3306;
		}

		$dsn = $driver.':host='.$host.';port='.$port.';dbname='.$db;

		try{
			if(!self::$obj[$type]) {
				self::$conn = self::$obj[$type] = new PDO($dsn, $user, $pswd);
				self::$conn->query('SET NAMES utf8');
				unset($db, $driver, $host, $port, $user, $pswd, $dsn);
			}
		}catch(PDOException $e){
			if(ENVIRONMENT == 'DEV'){
				Helper::raiseError(debug_backtrace(), $e->getMessage());
			}else{
				file_put_contents($this->logFile, $e->getMessage().PHP_EOL, FILE_APPEND);
			}
		}
	}


	/**
	 * Add table prefix
	 *
	 * @param string => target table
	 * @return table with TB_PREFIX
	 */
	public function addPrefix($table) {
		$this->table = TB_PREFIX . $table;
		return $this->table;
	}


	/**
	 * Field
	 */
	final public function Field($field){
		if(!$field){
			return $this;
		}

		if(is_array($field)){
			foreach($field as $val){
				$str .= '`'.$val.'`, ';
			}

			$this->options['field'] = substr($str, 0, strlen($str)-2); // 2:　Cos there is a BLANK
		}else{
			$this->options['field'] = $field;
		}

		unset($str, $field);
		return $this;
	}

	/**
	 * Where
	 */
	final public function Where($where){
		if(!$where){
			return $this;
		}

		if(is_array($where)){
			$total = sizeof($where);
			$i = 1;
			foreach($where as $key => $val){
				$str .= '`'.$key.'` = "'.$val.'"';
				if($i != $total){
					$str .= ' AND ';
				}
				$i++;
			}
		}else{
			$str = $where;
		}

		$this->options['where'] = $str;	
		unset($str, $i, $total, $where);

		return $this;
	}


	/*
	 * Order
	 */
	final public function Order($order){
		if(!$order){
			return $this;
		}

		if(is_array($order)){
			$total = sizeof($order);
			$i = 1;
			foreach($order as $key => $val){
				$str .= '`'.$key.'` '.$val;
				if($i != $total){
					$str .= ' , ';
				}
				$i++;
			}
		}else{
			$str = $order;
		}

		$this->options['order'] = $str;	
		unset($str, $i, $total, $order);

		return $this;
	}

	/*
	 * Limit
	 */
	final public function Limit($limit){
		if(!$limit){
			return $this;
		}

		$this->options['limit'] = $limit;	
		unset($limit);

		return $this;
	}

	// Reset SQL options
	final private function _reset() {
		unset($this->options);
	}
	

	/**
	 * Select all records
	 * @return records on success or FALSE on failure 
	 */
	final public function Select(){
		$this->sql = $this->generateSQL();

		// 连接DB
		$this->connect('READ');

		$this->Execute();
		$result = $this->success ? $this->Fetch() : NULL;

		if($this->selectOne == TRUE){
			$data = $result[0];
		}else{
			$data = $result;
		}

		$this->selectOne = FALSE;
		return $data;
	}


	/**
	 * Select one record
	 */
	final public function SelectOne(){
		$this->options['limit'] = 1;
		$this->selectOne = TRUE;

		return $this->Select();
	}


	/**
	 * Insert | Add a new record
	 *
	 * @param Array => Array('field1'=>'value1', 'field2'=>'value2', 'field3'=>'value1')
	 * @return FALSE on failure or inserted_id on success
	 */
	final public function Insert($maps = array()) {
		if (!$maps || !is_array($maps)) {
			return FALSE;
		} else {
			$fields = $values = array();

			foreach ($maps as $key => $value) {
				$fields[] = '`' . $key . '`';
				$values[] = "'$value'";
			}

			$fieldString = implode(',', $fields);
			$valueString = implode(',', $values);

			$this->sql = 'INSERT INTO ' . $this->table . " ($fieldString) VALUES ($valueString)";

			$this->connect();
			$this->Execute();

			return $this->success ? $this->getInsertID() : NULL;
		}
	}


	/**
	 * Insert | Add a list record
	 *
	 * @param type $data
	 * @return boolean
	 */
	public function MultiInsert($data){
		$sql = "INSERT INTO ". $this->table;
		$sqlFieldArr = array();
		$sqlValueArr = array();
		$first = TRUE;

		foreach($data as $item){
			if(!is_array($item)){
				return FALSE;
			}

			if($first){
				$sqlFieldArr = array_keys($item);

				$sqlFieldStr = implode('`,`', $sqlFieldArr);
				$first = FALSE;
			}

			$tmp = implode('\',\'', $item);
			$tmp = "('$tmp')";
			$sqlValueArr[] = $tmp;
		}

		$sqlValueStr = implode(',', $sqlValueArr);
		$sql .= "(`$sqlFieldStr`) VALUES $sqlValueStr";

		$this->sql = $sql;
		$this->Execute();

		return $this->success ? $this->getInsertID() : NULL;
	}

	/**
	 * Replace | Add a new record if not exit, update if exits;
	 *
	 * @param Array => Array('field1'=>'value1', 'field2'=>'value2', 'field3'=>'value1')
	 * @return FALSE on failure or inserted_id on success
	 */
	final public function ReplaceInto($maps) {
		if (!$maps || !is_array($maps)) {
			return FALSE;
		} else {
			$fields = $values = array();

			foreach ($maps as $key => $value) {
				$fields[] = '`' . $key . '`';
				$values[] = "'$value'";
			}

			$fieldString = implode(',', $fields);
			$valueString = implode(',', $values);

			$sql = 'REPLACE INTO ' . $this->table . " ($fieldString) VALUES ($valueString)";
			$this->sql = $sql;

			$this->Execute();

			return $this->success ? $this->getInsertID() : NULL;
		}
	}


	/**
	 * Execute special SELECT SQL statement
	 *
	 * @param string  => SQL statement for execution
	 */
	final public function Query($sql) {
		if($sql){
			$this->sql = $sql;
		}

		$this->connect('READ');
		$this->Execute();
		$this->checkResult();

		if($this->success){
			return $this->Fetch();
		}else{
			return FALSE;
		}
	}

	// 根据ID 查询字段:
	public function SelectByID($field = '', $id){
		$where = array('id' => $id);
		return $this->Field($field)->Where($where)->SelectOne();
	}

	// 根据ID更新某一条记录
	public function UpdateByID($map, $id){
		$where = array('id' => $id);
		return $this->Where($where)->Limit(1)->Update($map);
	}

	// 根据ID删除某一条记录
	public function DeleteByID($id){
		if(!$id || !is_numeric($id)){
			return FALSE;
		}

		$where = array('id' => $id);
		return $this->Where($where)->Limit(1)->Delete();
	}

	// 根据ID获取某个字段
	public function SelectFieldByID($field, $id){
		$where = array('id' => $id);
		$data = $this->Field($field)->Where($where)->SelectOne();
		return $data[$field];
	}

	/**
	 * Generate SQL by options
	 */
	final protected function generateSQL(){
		$field = $this->options['field'];
		if(!$field){
			$field = '*';
		}

		$sql = 'SELECT '. $field .' FROM `'. $this->table. '`';

		$where = $this->options['where'];
		if($where){
			$sql .= ' WHERE '. $where;
		}

		$order = $this->options['order'];
		if($order){
			$sql .= ' ORDER BY '. $order;
		}

		$limit = $this->options['limit'];
		if($limit){
			$sql .= ' LIMIT '. $limit;
		}

		return $sql;
	}


	/**
	 * Return last inserted_id
	 *
	 * @param NULL
	 * @return the last inserted_id
	 */
	public function getInsertID() {
		return self::$conn->lastInsertId();
	}


	/**
	 * Fetch data
	 */
	private function Fetch() {
		return $this->result->fetchAll(PDO::FETCH_ASSOC);
	}


	/**
	 * Calculate record counts
	 *
	 * @param string => where condition
	 * @return int => total record counts
	 */
	final public function Total() {
		$data = $this->Field('COUNT(*) AS `total`')->SelectOne();
		return $data['total'];
	}


	/**
	 * Execute SELECT | INSERT SQL statements
	 *
	 * <br /> Remark:  If error occurs and UAT is TRUE, call raiseError() to display error and halt !
	 * @param string => SQL statement to execute
	 * @return result of execution
	 */
	final private function Execute() {
		$this->result = self::$conn->query($this->sql);
		$this->checkResult();
	}


	/**
	 * Update record(s)
	 *
	 * @param array  => $maps = array('field1'=>value1, 'field2'=>value2, 'field3'=>value3))
	 * @param string => where condition
	 * @param boolean $self => self field ?
	 * @return FALSE on failure or affected rows on success
	 */
	final public function Update($maps, $self = FALSE) {
		if (!$maps) {
			return FALSE;
		} else {
			$this->sql = 'UPDATE ' . $this->table . ' SET ';
			$sets = array();
			if($self){
				foreach ($maps as $key => $value) {
					if (strpos($value, '+') !== FALSE) {
						list($flag, $v) = explode('+', $value);
						$sets[] = "`$key` = `$key` + '$v'";
					} elseif (strpos($value, '-') !== FALSE) {
						list($flag, $v) = explode('-', $value);
						$sets[] = "`$key` = `$key` - '$v'";
					} else {
						$sets[] = "`$key` = '$value'";
					}
				}
			} else {
				foreach ($maps as $key => $value) {
					$sets[] = "`$key` = '$value'";
				}
			}

			$this->sql .= implode(',', $sets). ' ';

			$where = $this->options['where'];
			if($where){
				$this->sql .= ' WHERE '.$where;
			}

			$limit = $this->options['limit'];
			if($limit){
				$this->sql .= ' LIMIT '.$limit;
			}

			//echo 'SQL: '.$this->sql;

			$this->connect();

			return $this->Exec();
		}
	}


	/**
	 * Delete record(s)
	 * @param string => where condition for deletion
	 * @return FALSE on failure or affected rows on success
	 */
	final public function Delete() {
		$where = $this->options['where'];

		if(!$where){
			return FALSE;
		}

		$this->sql = 'DELETE FROM `'.$this->table.'` WHERE '.$where;

		$limit = $this->options['limit'];
		if($limit){
			$this->sql .= ' LIMIT '.$limit;
		}

		$this->connect();
		return $this->Exec();
	}


	/**
	 * Execute UPDATE, DELETE SQL statements
	 * <br />Remark:  If error occurs and UAT is TRUE, call raiseError() to display error and halt !
	 *
	 * @return result of execution
	 */
	final private function Exec() {
		$rows = self::$conn->exec($this->sql);
		$this->checkResult();

		return $rows;
	}


	private function getUnderscore($total = 10, $sub = 0) {
		$result = '';
		for($i=$sub; $i<= $total; $i++){
			$result .= '_';
		}
		return $result;
	}

	/**
	 * Check result for the last execution
	 *
	 * @param NULL
	 * @return NULL
	 */
	final private function checkResult() {
		$this->_reset();

		if (self::$conn->errorCode() != $this->successCode) {
			$this->success = FALSE;
			$error = self::$conn->errorInfo();
			$traceInfo = debug_backtrace();

			if (ENVIRONMENT == 'DEV') {
				Helper::raiseError($traceInfo, $error[2], $this->sql);
			} else {
				// Log error SQL and reason for debug
				$errorMsg = getClientIP(). ' | ' .date('Y-m-d H:i:s') .PHP_EOL;
				$errorMsg .= 'SQL: '. $this->sql .PHP_EOL;
				$errorMsg .= 'Error: '.$error[2]. PHP_EOL;

				$title =  'LINE__________FUNCTION__________FILE______________________________________'.PHP_EOL;
				$errorMsg .= $title;

				foreach ($traceInfo as $v) {
					$errorMsg .= $v['line'];
					$errorMsg .= $this->getUnderscore(10, strlen($v['line']));
					$errorMsg .= $v['function'];
					$errorMsg .= $this->getUnderscore(20, strlen($v['function']));
					$errorMsg .= $v['file'].PHP_EOL;
				}

				file_put_contents($this->logFile, PHP_EOL.$errorMsg, FILE_APPEND);

				return FALSE;
			}
		}else{
			$this->success = TRUE;
		}
	}

	// ********* Execute transaction ********* //
	/**
	 * Start a transaction
	 *
	 * @param NULL
	 * @return TRUE on success or FALSE on failure
	 */
	public function beginTransaction() {
		self::$conn->beginTransaction();
	}


	/**
	 * Commit a transaction
	 *
	 * @param NULL
	 * @return TRUE on success or FALSE on failure
	 */
	public function Commit() {
		self::$conn->commit();
	}


	/**
	 * Rollback a transaction
	 *
	 * @param  NULL
	 * @return TRUE on success or FALSE on failure
	 */
	public function Rollback() {
		self::$conn->rollBack();
	}
	// *************** End ***************** //


	/**
	 * Close connection
	 *
	 * @param NULL
	 * @return NULL
	 */
	private function Close() {
		self::$conn = NULL;
	}


	/**
	 * Destructor
	 *
	 * @param NULL
	 * @return NULL
	 */
	function __destruct() {
		$this->Close();
	}

}