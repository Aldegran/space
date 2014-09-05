<?php
class Service
{
	
	public function planetTypes() {
		return array(
			0 => array(
				'name' => "DP",
				'title'=> "Dead planet",
				'addPeople' => -10,
				'addTechnology' => -10,
				'addResources' => 0,
				'addSize' => 0
			),
			1 => array(
				'name' => "TR",
				'title'=> "Terain planet",
				'addPeople' => 1,
				'addTechnology' => 0.1,
				'addResources' => 0.05,
				'addSize' => 0
			)
		);
	}
	public function planetTasks(){
		return array(
			0 => array(
				'name' => "none",
				'title'=> "Create people",
				'addPeople' => 100,
				'addTechnology' => 0,
				'addResources' => 0,
				'addSize' => 0
			),
			1 => array(
				'name' => "product",
				'title'=> "Create technology",
				'addPeople' => 0,
				'addTechnology' => 100,
				'addResources' => 0,
				'addSize' => 0
			),
			2 => array(
				'name' => "digg",
				'title'=> "Resource extraction",
				'addPeople' => 0,
				'addTechnology' => 0,
				'addResources' => 100,
				'addSize' => -0.1
			),
			3 => array(
				'name' => "terraforming",
				'title'=> "Terraforming",
				'addPeople' => 0,
				'addTechnology' => 0,
				'addResources' => 0,
				'addSize' => 1
			)
		);
	}
	public function text($text, $color = false, $bg = false)
	{
		$colors = array('black','red','green','yellow','blue','pink','light_blue','white');
		if($color && in_array($color, $colors)){
			echo("\033[".(30 + array_search($color, $colors) + ($bg ? 10 : 0)).'m');
			echo($text);
			echo("\033[0m");
		}else{
			echo($text);
		}
	}

	public function textN($text, $color = false, $bg = false)
	{
		self::text($text, $color, $bg);
		echo("\n");
	}

	public function showParams($params){
		echo("\033[34m");
		print_r($params);
		echo("\033[0m");
	}

	public function OK($noBR = false){
		Service::text('OK','green');
		if(!$noBR) echo "\n";
	}

	public function FAIL($noBR = false){
		Service::text('FAIL','red');
		if(!$noBR) echo "\n";
	}

	public function OK_FAIL($check){
		if($check) self::OK();
		else self::FAIL();
		return $check;
	}

	public function sessionStart(){
		if (isset($_COOKIE[session_name()])) {
			//echo "Session Start: ".$_COOKIE[session_name()]."\n";
			session_start();
			return true;
		}

		return false;
	}

	public function isSession(){
		return isset($_SESSION);
	}

	public function session($name, $value=null){
		if(!self::isSession()) return false;
		if($value !== null){
			$_SESSION[$name] = $value;
			return $value;
		}
		return isset($_SESSION[$name]) ? $_SESSION[$name] : false;
	}

	public function loginCheck(){
		return self::session('ID') != false;
	}


	public function loginCheckText(){
		if(!self::loginCheck()){
			self::textN('Need login or register', 'red');
			return false;
		}
		return true;
	}

	public function otherClass($name, $params = false){
		include_once('c_'.$name.'.php');
		$c = $name.'Class';
		$command = new $c($params);
		return $command;
	}
}

class DB
{
	
	protected static $instance;
	private static $session;
	private static $config = array(
		'db_host' => 'localhost',
		'db_user' => 'root',
		'db_pass' => 'qwe123',
		'db_name' => 'sp'
	);

	public static function getInstance() {
		if (self::$instance === null)
		return self::$instance = new self();
		else
		return self::$instance;
	}

	public function connect(){
		if(!self::$session=@mysql_connect(
				self::$config['db_host'],
				self::$config['db_user'],
				self::$config['db_pass'])
			){
			
			return false;
		}
		if(!mysql_select_db(self::$config['db_name'])){
			return false;
		}
		//self::query("SET CHARACTER SET utf8");
		return true;
	}

	public function close(){
		if(self::$session){
			mysql_close(self::$session);
			return true;
		}
		return false;
	}

	public function query($query){
		/*if(!self::isConnect()){
			if(!self::connect())return;
		}*/
		if(Service::session('debugDB'))Service::textN($query,"blue");
		$result =  mysql_query($query,self::$session) or die('Ошибка в запросе к БД '.$query."\n".mysql_error());
		//var_dump($result);
		return $result;
	}

	public function lastId(){
		return mysql_insert_id(self::$session);
	}

	public function isConnect(){
		return (bool) self::$session;
	}

	public function escaped($value){
		return mysql_escape_string($value);
	}

	public function getArray($result,$one_row=false,$one_field=false){
		if($one_field)$one_row=true;
		if(is_string($result))$result=self::query($result);
		if($one_row){
			//var_dump($result);
			if(!$one_field || !is_string($one_field))return mysql_fetch_assoc($result);
			$result=mysql_fetch_assoc($result);
			return $result[$one_field];
		}
		$return=array();
		while($str=mysql_fetch_assoc($result)){
			$return[]=$str;
		}
		return $return;
	} 
}

class BaseClass {

	protected $param;
	protected $db;

	function __construct($params)
	{
		$this->param = $params;
		$this->db = DB::getInstance();
	}

	protected function check($name, $value = null){
		if($value === null){
			return isset($this->param[$name]) && $this->param[$name] != "False";
		}

		return isset($this->param[$name]) && $this->param[$name] == $value;
	}
}