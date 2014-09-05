<?php
/**
* 
*/
class userClass extends BaseClass
{
	
	public function run()
	{
		if($this->check('list','True')){
			if(!Service::loginCheckText()) return;
			$this->userList();
		} else if($this->check('name')){
			if($this->check('password')){
				$this->login();
			} else {
				if(!Service::loginCheckText()) return;
				if($this->check('delete','True')){
					$this->delete();
				} else {
					$this->info();
				}
			}
		} else if($this->check('logout','True')){
			if(!Service::loginCheck()){
				Service::textN('Your not logined', 'red');
				$this->db->close();
				return;
			}
			$this->logout();
		}
	}

	private function userList(){
		$this->db->connect();
		echo ("Users:\t");
		echo ($this->db->getArray('SELECT COUNT(id) AS c FROM users', true, 'c'));
		$result = $this->db->getArray('SELECT * FROM users');
		echo("\nID:\tLogin:\tName:");
		foreach ($result as $item) {
			echo("\n".$item['id']);
			echo("\t".$item['login']);
			echo("\t".$item['name']);
		}
		echo("\n");
		$this->db->close();
	}

	private function login(){
		$this->db->connect();
		$result = $this->db->getArray('SELECT * FROM users WHERE login="'.$this->db->escaped($this->param['name']).'" AND password=MD5('.$this->db->escaped($this->param['password']).')', true);
		if(is_array($result) && $result['id'] > 0){
			session_start();
			Service::session("ID", $result['id']);
			echo(">".session_id());
		} else {
			Service::textN('Access denied', 'red');
		}
		$this->db->close();
	}

	private function delete(){
		$this->db->connect();
		echo("Delete ".$this->param['name'].":\t");
		$res = $this->db->query('DELETE FROM users WHERE '.(is_numeric($this->param['name'])? 'id='.$this->db->escaped($this->param['name']) : 'name="'.$this->db->escaped($this->param['name']).'"'));
		Service::OK_FAIL(true);
		$this->db->close();
	}

	private function info(){
		$this->db->connect();
		$result = $this->db->getArray('SELECT * FROM users WHERE '.(is_numeric($this->param['name'])? 'id='.$this->db->escaped($this->param['name']) : 'name="'.$this->db->escaped($this->param['name']).'"'), true);
		if(is_array($result)){
			echo("ID:\t".$result['id']."\n");
			echo("Login:\t".$result['login']."\n");
			echo("Name:\t".$result['name']."\n");
		} else {
			Service::textN('Undefined user: '.$this->param['name'], 'red');
		}
		$this->db->close();
	}

	private function logout(){
		Service::session("ID", false);
		Service::textN('Logout');
	}
}