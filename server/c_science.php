<?php
/**
* 
*/
class scienceClass extends BaseClass
{
	
	public function run()
	{
		if(!Service::loginCheckText()) return;
		$planet = Service::session('planet');
		$this->db->connect();
		if($this->check('name')){
			if($this->check('check')){
				$c = $this->checkSienceForPlanet($this->param['name'], $this->param['check']);
				if(!$c){
					Service::text("Your can learn science ".$this->param['name']." for planet ".$planet['name'], 'green');
				}else{
					Service::text($c);
				}
			} else {
				$this->scienceInfo($this->param['name']);
			}
		} else {
			if($this->check('all', 'True')){
				$this->allSciences();
			}
		}
		$this->db->close();
	}

	public function setStudy($planet, $name){
		$p = $this->db->getArray('SELECT id, technology FROM planets WHERE name="'.$this->db->escaped($planet).'"',true);
		if(!is_array($p)){
			Service::textN('Undefined planet name '.$planet, 'red');
			return;
		}
		$s = $this->db->getArray('SELECT id, replaced, parent FROM sciences WHERE name="'.$this->db->escaped($name).'"',true);
		if(!is_array($s)){
			Service::textN('Undefined science name '.$name, 'red');
			return;
		}
		$c = $this->checkSienceForPlanet($s['id'],$p['id']);
		if($c){
			Service::text("Study ".$name."\t");
			Service::FAIL();
			Service::text($c);
			return;
		}
		Service::text("Study ".$name."\t");
		$res = $this->db->query('UPDATE planets SET technology='.($p['technology'] - $s['cost']).' WHERE id='.$p['id']);
		if($res == false){
			Service::FAIL();
			return;
		}
		if($s['replaced'] == 1){
			$this->db->query('DELETE FROM sciences_to_planets WHERE science_id='.$s['parent'].' AND planet_id='.$p['id']);
		}
		$res = $this->db->query('INSERT INTO sciences_to_planets SET science_id='.$s['id'].', planet_id='.$p['id']);
		Service::OK_FAIL($res!=false);
	}

	public function knowsSciences($planet){
		$id = Service::otherClass('planet')->getIdByName($planet);
		if(!is_numeric($id) || !$id){
			Service::textN('Undefined planet name '.$planet, 'red');
			return;
		}
		if(!$this->scienceList("Name\t\tTitle","SELECT s.name, s.title FROM sciences AS s, sciences_to_planets AS stp WHERE s.id=stp.science_id AND stp.planet_id=".$id)){
			Service::textN('No study of science');
		}
	}

	public function scienceList($title, $query){
		if(is_array($query)){
			$result = $query;
		} else {
			$result = $this->db->getArray($query);
		}
		if(is_array($result)){
			echo($title);
			foreach ($result as $item) {
				echo("\n");
				foreach ($item as $k=>$i){
					if($k=="parent") {
						if($i)echo "(need ".$this->getNameById($i).")\t";
					}
					else echo($i."\t");
				}
			}
			return true;
		}
		return false;
	}

	public function availableSciences($planet , $verbose = false){
		$planetId = Service::otherClass('planet')->getIdByName($this->db->escaped($planet));
		if(!$planetId){
			if($text)Service::textN('Undefined planet: '.$planet, 'red');
			return false;
		}
		$result = $this->db->getArray("SELECT id, name, title, cost FROM sciences");
		$a = false;
		if(is_array($result)){
			echo "Name\t\tCost\tTitle\n";
			foreach ($result as $item) {
				$n=$this->checkSienceForPlanet($item['id'], $planetId);
				if(!$n){
					$a = true;
					echo($item['name']."\t".$item['cost']."\t".$item['title']."\n");
				}else if($a && $verbose){
					Service::textN($item['name']."\t".$n,"red");
				}
			}
			if(!$a){
				Service::text('No available sciences');
			}
			return true;
		}
	}

	private function allSciences(){
		$this->scienceList("Name\t\tCost\tTitle","SELECT name, cost, title, name, parent FROM sciences");
	}

	public function getIdByName($name){
		return $this->db->getArray('SELECT id FROM sciences WHERE name="'.$this->db->escaped($name).'"',true,'id');
	}

	public function getNameById($id){
		return $this->db->getArray('SELECT name FROM sciences WHERE id='.$this->db->escaped($id),true,'name');
	}

	private function scienceInfo($name){
		$item = $this->db->getArray('SELECT * FROM sciences WHERE name="'.$this->db->escaped($name).'"', true);
		if(is_array($item)){
			echo("Name:\t\t".$item['name']);
			echo("\nTitle:\t\t".$item['title']);
			echo("\nCost:\t\t".$item['cost']);
			if($item['parent']){
				echo("\nNeed:\t\t".$this->getNameById($item['parent']));
			}
		} else {
			Service::text('Undefined science: '.$name, 'red');
		}
	}

	public function checkSienceForPlanet($id, $planetId){
		if(!is_numeric($id)){
			$n = $id;
			$id = $this->getIdByName($id);
			if(!$id){
				return 'Undefined science: '.$n;
			}
		}
		$science = $this->db->getArray('SELECT name, parent, cost FROM sciences WHERE id='.$id, true);
		if(!is_numeric($planetId)){
			$n = $planetId;
			$planetId = Service::otherClass('planet')->getIdByName($this->db->escaped($planetId));
			if(!$planetId){
				return 'Undefined planet: '.$n;
			}
		}
		$planet = $this->db->getArray('SELECT name, technology FROM planets WHERE id='.$this->db->escaped($planetId), true);

		if(is_array($this->db->getArray('SELECT * FROM sciences_to_planets WHERE science_id='.$id.' AND planet_id='.$planetId, true))){
			return 'The science '.$science['name'].' is already present on planet '.$planet['name'];
		}
		
		if($science['parent']>0 && !is_array($this->db->getArray('SELECT * FROM sciences_to_planets WHERE science_id='.$science['parent'].' AND planet_id='.$planetId, true))){
			return 'For learn '.$science['name'].' you need learn '.$this->getNameById($science['parent']).' first';
		}
		if($science['cost']>0 && $planet['technology'] < $science['cost']){
			return 'You need '.$science['cost'].' technology resources for learn '.$science['name'];
		}
		
		return false;
	}
	
}