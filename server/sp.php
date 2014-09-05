<?php

/*
0 — Сбросить на значение по умолчанию
1 — повысить яркость
2 — понизить яркость
4 — подчеркнуть
5 — нормальная яркость
7 — Инвертировать
8 — скрыть

Черный текст 30
Черный фон 40
Красный текст 31
Красный фон 41
Зеленый текст 32
Зеленый фон 42
Желтый текст 33
Желтый фон 43
Синий текст 34
Синий фон 44
Фиолетовый цвет 35
Фиолетовый фон 45
Голубой цвет 36
Голубой фон 46
Белый цвет 37
Белый фон 47
*/

/**
* 
*/

include_once("service.php");

function generallParcer($params)
{
	if(!isset($params['command'])) {
		return false;
	}
	$availableMethods = array('test','debug','user','register','my','planet','science');
	if(in_array($params['command'], $availableMethods)) {
		$c = $params['command'].'Class';
		include_once('c_'.$params['command'].'.php');
		$command = new $c($params);
		$command->run();
		return true;
	} else {
		Service::textN("Undefined command: ".$params['command'],"red");
		return false;
	}
}


date_default_timezone_set('Europe/Helsinki');
Service::sessionStart();
//$_SESSION['t']=1;
//Service::showParams($_SESSION);
//echo (session_name()."\n".session_id()."\n");
if(Service::session('request')){
	Service::showParams($_REQUEST);
}
generallParcer($_GET);