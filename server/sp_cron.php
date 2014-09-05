<pre><?php
include_once("service.php");
date_default_timezone_set('Europe/Helsinki');
function calculateInc($type, $planetType, $taskType){
	$planetTypes = Service::planetTypes();
	$planetTasks = Service::planetTasks();
	return ($planetTasks[$taskType][$type]/100)*($planetTypes[$planetType][$type] || 1);
}
session_start();
$_SESSION['debugDB']=true;
$db = DB::getInstance();
if(!$db->connect()) exit(0);
$result = $db->getArray('SELECT * FROM planets');
Service::showParams($result);
if(is_array($result)){
	foreach ($result as $item) {
		$m = array();
		$addPeople = calculateInc('addPeople', $item['type'], $item['task']);
		if($addPeople != 0){
			$m[] = "people=people+".round($item['people']*($addPeople/100));
		}
		$addTechnology = calculateInc('addTechnology', $item['type'], $item['task']);
		if($addTechnology != 0){
			$m[] = "technology=technology+".round($item['people']*($addTechnology/100));
		}
		$addResources = calculateInc('addResources', $item['type'], $item['task']);
		if($addResources != 0){
			$m[] = "resources=resources+".round($item['people']*($addResources/100));
		}
		$addSize = calculateInc('addSize', $item['type'], $item['task']);
		if($addSize != 0){
			$m[] = "size=size+".round($item['people']*($addSize/100));
		}
		Service::showParams($m);
		$db->query('UPDATE planets SET '.implode(', ',$m).' WHERE id='.$item['id']);
	}
	$db->query('UPDATE planets SET people=0 WHERE people<0');
	$db->query('UPDATE planets SET people=size*100000 WHERE people>size*100000');
	$db->query('UPDATE planets SET technology=0 WHERE technology<0');
	$db->query('UPDATE planets SET resources=0 WHERE resources<0');
	$db->query('UPDATE planets SET size=0, people=0 WHERE size<=0');
	$db->query('UPDATE planets SET size=diameter WHERE size>diameter');
}
$db->close();
exit(0);