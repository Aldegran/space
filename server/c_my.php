<?php
/**
* 
*/
class myClass extends BaseClass
{
	
	public function run()
	{
		if(!Service::loginCheckText()) return;
		if(method_exists($this, $this->param['info'])){
			$c = $this->param['info'];
			$this->db->connect();
			$this->$c();
			$this->db->close();
		}else{
			Service::textN('Under construction','red');
		}
	}

	private function account(){
		$result = $this->db->getArray('SELECT * FROM users WHERE id='.$this->db->escaped(Service::session('ID')), true);
		if(is_array($result)){
			echo("Your account\nID:\t".$result['id']."\n");
			echo("Login:\t".$result['login']."\n");
			echo("Name:\t".$result['name']."\n");
		} else {
			Service::textN('Error', 'red');
		}
	}

	private function planets(){
		Service::text('Planets: ');
		Service::textN($this->db->getArray('SELECT COUNT(id) AS c FROM planets WHERE user_id='.$this->db->escaped(Service::session('ID')), true, 'c'));
		Service::otherClass('planet')->myPlanets();
	}

	public function myPlanets(){
		$this->planets();
	}

	
}