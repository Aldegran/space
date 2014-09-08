<?php
/**
* 
*/
class planetClass extends BaseClass
{
	
	public function run()
	{
		if(!Service::loginCheckText()) return;
		$planet = Service::session('planet');
		$this->db->connect();
		if($this->check('name')){
			if($this->check('use','True')){
				$this->usePlanet($this->db->escaped($this->param['name']));
			}
			$name = $this->param['name'];
		} else {
			$name = is_array($planet) ? $planet['name'] : false;
		}
		
		if($name){
			if($this->check('info','True')){
				$this->planetInfo($this->db->escaped($name), 0, false);
			} else if($this->check('near','True')){
				$this->planetNear($this->db->escaped($name));
			} else if($this->check('task')){
				$this->setTask($this->db->escaped($name), $this->param['task']);
			} else if($this->check('study')){
				Service::otherClass('science')->setStudy($this->db->escaped($name), $this->param['study']);
			} else if($this->check('knows')){
				Service::otherClass('science')->knowsSciences($this->db->escaped($name));
			} else if($this->check('available')){
				Service::otherClass('science')->availableSciences($this->db->escaped($name),$this->check('verbose','True'));
			} else if($this->check('list','True')){
				$this->myPlanets();
			} else if($this->check('create')){
				$coords = explode(' ', $this->param['create']);
				$this->createPlaten($coords);
			} else if($this->check('all', 'True')){
				$this->allPlanets();
			} else {
				$this->planetInfo($name);
			}
		} else {
			if($this->check('list','True')){
				$this->myPlanets();
			} else if($this->check('create')){
				$coords = explode(' ', $this->param['create']);
				$this->createPlaten($coords);
			} else if($this->check('all', 'True')){
				$this->allPlanets();
			} else {
				Service::textN('Not selected any planets');
			}
		} 
		$this->db->close();
	}

	private function usePlanet($name){
		$result = $this->db->getArray('SELECT id, name FROM planets WHERE user_id='.$this->db->escaped(Service::session('ID')).' AND name="'.$name.'"', true);
		if(is_array($result) && $result['id'] > 0){
			$planet = $result;
			Service::session("planet", $result);
			Service::textN('Use planet: '.$result['name']);
		} else {
			Service::textN('Access denied', 'red');
		}
	}

	private function planetInfo($name, $verbose = 0, $me = true){
		$this->planetTypes = Service::planetTypes();
		$this->planetTasks = Service::planetTasks();
		$item = $this->db->getArray('SELECT * FROM planets WHERE '.($me ? 'user_id='.$this->db->escaped(Service::session('ID')) .' AND ' : '').'name="'.$name.'"', true);
		if(is_array($item)){
			echo("Name:\t\t".$item['name']);
			echo("\nPosition:\t".$item['x']." : ".$item['y']);
			if(!$me && $item['user_id'] == Service::session('ID')){
				$me = true;
			}
			echo("\nDiameter:\t".$item['diameter']);
			if(!$me && $verbose > 1) echo("\nOwner:\t\t".$item['user_id']);
			else if($me) echo("\nOwner:\t\tME");
			if($me || $verbose > 1) {
				echo("\nSize:\t\t".floor($item['size'])." ");
			}
			if($me || $verbose > 2) {
				$n = $item['people']*($this->calculateInc('addSize', $item['type'], $item['task'])/100);
				if($n>0)  Service::text('+'.$n,'green');
				else if($n<0) Service::text($n,'red');
				echo("\nAdd size:\t");
				$this->calculateInc('addSize', $item['type'], $item['task'], true);
			}
			if($me || $verbose > 2) echo("\nType:\t\t".$this->planetTypes[$item['type']]['title']);
			if($me || $verbose > 3) {
				echo("\nPeople:\t\t".$item['people']." ");
				$n = $item['people']*($this->calculateInc('addPeople', $item['type'], $item['task'])/100);
				if($n>0)  Service::text('+'.$n,'green');
				else if($n<0) Service::text($n,'red');
			}
			if($me || $verbose > 2) {
				echo("\nAdd people:\t");
				$this->calculateInc('addPeople', $item['type'], $item['task'], true);
			}
			if($me || $verbose > 3) {
				echo("\nTechnology:\t".$item['technology']." ");
				$n = $item['people']*($this->calculateInc('addTechnology', $item['type'], $item['task'])/100);
				if($n>0)  Service::text('+'.$n,'green');
				else if($n<0) Service::text($n,'red');
			}
			if($me || $verbose > 2) {
				echo("\nAdd technology:\t");
				$this->calculateInc('addTechnology', $item['type'], $item['task'], true);
			}
			if($me || $verbose > 3) {
				echo("\nResources:\t".$item['resources']." ");
				$n = $item['people']*($this->calculateInc('addResources', $item['type'], $item['task'])/100);
				if($n>0) Service::text('+'.$n,'green');
				else if($n<0) Service::text($n,'red');
			}
			if($me || $verbose > 2) {
				echo("\nAdd resources:\t");
				$this->calculateInc('addResources', $item['type'], $item['task'], true);
			}
			if($me || $verbose > 5) echo("\nTask:\t".$this->planetTasks[$item['task']]['title']);
		} else {
			Service::textN('Undefined planet: '.$name, 'red');
		}
	}

	public function calculateInc($type, $planetType, $taskType, $color = false){
		 $this->planetTypes = Service::planetTypes();
		 $this->planetTasks = Service::planetTasks();
		 $n = ($this->planetTasks[$taskType][$type]/100)*($this->planetTypes[$planetType][$type] || 1);
		 if(!$color) return $n;
		 if($n>0) Service::text('+'.number_format($n,($n<0.01 ? 3 : 2)).'%','green');
		 else if($n<0) Service::text(number_format($n,($n>-0.01 ? 3 : 2)).'%','red');
		 else Service::text('0%');
	}

	private function planetNear($name){
		Service::textN('Planets near '.$name);
		$currentPlanet = $this->db->getArray('SELECT x, y, user_id, id FROM planets WHERE name="'.$name.'"', true);
		if(!is_array($currentPlanet)){
			Service::textN('Undefined planet: '.$name, 'red');
			return;
		}
		if($currentPlanet['user_id'] != Service::session('ID')){
			Service::textN('Cant get data from planet: '.$name, 'red');
			return;
		}
		$this->planets('SELECT *, ROUND(SQRT(POW((x-'.$currentPlanet['x'].'),2) + POW((y-'.$currentPlanet['y'].'),2))) AS d FROM planets WHERE id<>'.$currentPlanet['id'].' AND SQRT(POW((x-'.$currentPlanet['x'].'),2) + POW((y-'.$currentPlanet['y'].'),2))<100',0);
	}

	public function planets($query, $verbose = 0){
		$this->planetTypes = Service::planetTypes();
		$this->planetTasks = Service::planetTasks();
		$result = $this->db->getArray($query);
		if(is_array($result)){
			
			echo("Name\tPosition\tOwner\tDiam\tSize\tType\tPeople\tTech\tRes\tTask");
			foreach ($result as $item) {
				$me = false;
				if($item['user_id'] == Service::session('ID')) $me = true;
				echo("\n".$item['name']);
				echo("\t".$item['x']." : ".$item['y']);
				if(isset($item['d']))echo (' ('.$item['d'].')');
				else echo("\t");
				if(!$me && $verbose > 1) echo("\t".$item['user_id']);
				else if($me) echo("\tME");
				else echo ("\t- ");
				echo("\t".$item['diameter']);
				if($me || $verbose > 1) echo("\t".floor($item['size']));
				else echo ("\t- ");
				if($me || $verbose > 2) echo("\t".$this->planetTypes[$item['type']]['name']);
				else echo ("\t- ");
				if($me || $verbose > 3) echo("\t".$item['people']);
				else echo ("\t- ");
				if($me || $verbose > 3) echo("\t".$item['technology']);
				else echo ("\t- ");
				if($me || $verbose > 3) echo("\t".$item['resources']);
				else echo ("\t- ");
				if($me || $verbose > 4) echo("\t".$this->planetTasks[$item['task']]['name']);
				else echo ("\t- ");
			}
		} else {
			return false;
		}
	}

	private function createPlaten($coords = false){
		$this->planetTypes = Service::planetTypes();
		$this->planetTasks = Service::planetTasks();
		echo("Create planet:\t");
		$owner = isset($coords[0]) && is_numeric($coords[0])  && $coords[0] ? $coords[0] : 0;
		$size = rand(10,100);
		$p = array(
			'x' => isset($coords[1]) && is_numeric($coords[1]) ? $coords[1] : rand(1,100),
			'y' => isset($coords[2]) && is_numeric($coords[2]) ? $coords[2] : rand(1,100),
			'user_id' => $owner,
			'size' => $size,
			'diameter' => $size+10,
			'people' => $owner ? rand(1000,100000) : 0,
			'technology' => $owner ? rand(10,100) : 0,
			'resources' => $owner ? rand(10,100) : 0,
			'type' => 1,
			'name' => 0,
			'task' => 0
		);
		$query = 'INSERT INTO planets SET ';
		$m = array();
		foreach ($p as $key => $value) {
			$m[] = $key.'='.$this->db->escaped($value);
		}
		$res = $this->db->query($query.implode(', ', $m));
		$id = $this->db->lastId();
		if($id>0){
			$name = 
			$res = $this->db->query('UPDATE planets SET name="'.$name.'" WHERE id='.$id);
			Service::OK_FAIL($res!=false);
			$this->planetInfo($this->planetTypes[$p['type']]['name'].'-'.$id, 10, false);
		} else {
			Service::FAIL();
		}

	}

	private function allPlanets(){
		$this->planets('SELECT * FROM planets',10);
	}

	public function myPlanets(){
		$this->planets('SELECT * FROM planets WHERE user_id='.$this->db->escaped(Service::session('ID')),10);
	}

	public function setTask($name, $taskName){
		$this->planetTypes = Service::planetTypes();
		$this->planetTasks = Service::planetTasks();
		$taskId = -1;
		foreach ($this->planetTasks as $key => $value) {
			if($value['name'] == strtolower($taskName)){
				$taskId = $key;
				Service::text("Change task for planet ".$name." to ".$value['title'].":\t");
			}
		}
		if($taskId<0){
			Service::textN('Invalid task name '.$taskName, 'red');
			return;
		}
		//добавить проверка на то чья это планета
		$res = $this->db->query('UPDATE planets SET task='.$taskId.' WHERE name="'.$name.'"');
		Service::OK_FAIL($res!=false);
	}

	public function getIdByName($name){
		return $this->db->getArray('SELECT id FROM planets WHERE name="'.$this->db->escaped($name).'"',true,'id');
	}
}