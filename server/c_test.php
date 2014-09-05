<?php
/**
* 
*/
class testClass extends BaseClass
{
	
	public function run()
	{
		$tests = false;
		if($this->check('db','True')){
			$tests = true;
			echo("Test Database\t");
			if(!Service::OK_FAIL($this->db->connect()))$this->db->close();
		}
		if(!$tests) Service::textN('Test Complete');
	}
}