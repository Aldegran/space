<?php
/**
* 
*/
class debugClass extends BaseClass
{
	
	public function run()
	{
		if($this->check('session','True')){
			Service::showParams($_SESSION);
		}
		if($this->check('dbq','True')){
			$this->dbq();
		}
		if($this->check('request','True')){
			$this->request();
		}
	}

	private function dbq(){
		if(Service::isSession()){
			Service::session('debugDB' , !Service::session('debugDB'));
			echo ("Show query:\t");
			echo (Service::session('debugDB') ? 'enabled' : 'disabled');
		}
	}
	private function request(){
		if(Service::isSession()){
			Service::session('request' , !Service::session('request'));
			echo ("Show request:\t");
			echo (Service::session('request') ? 'enabled' : 'disabled');
		}
	}
}