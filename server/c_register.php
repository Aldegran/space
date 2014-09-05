<?php
/**
* 
*/
class registerClass extends BaseClass
{
	
	public function run()
	{
		if($this->check('name') && $this->check('login') && $this->check('password')){
			$this->db->connect();
			if($this->db->getArray('SELECT COUNT(id) AS c FROM users WHERE login="'.$this->db->escaped($this->param['login']).'"', true, 'c') > 0){
				Service::textN("Login is busy", 'red');
			} else {
				echo("Register:\t");
				$res = $this->db->query('INSERT INTO users SET login="'.$this->db->escaped($this->param['login']).'", password=MD5("'.$this->db->escaped($this->param['password']).'"), name="'.$this->db->escaped($this->param['name']).'"');
				Service::OK_FAIL($this->db->lastId()>0);
			}

			$this->db->close();
		} else {
			Service::textN("Incorect params", 'red');
		}
		
	}
	
}