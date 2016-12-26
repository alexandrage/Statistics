<?php
function Mon($ip, $port) {
	$mon = Query($ip, $port);
	$filename = '../tmp/'.$ip.'_'.$port.'.tmp';
	@$txt = json_decode(file_get_contents($filename))[0];
	$array = array();
	$i = 0;
	if(count($txt)>287) {
		$i = count($txt)-287;
	}
	for($i; $i < count($txt); ++$i) {
	   array_push($array, $txt[$i]);
	}
	array_push($array, $mon[0]);
	$handle = fopen($filename, "w");
	fwrite($handle, json_encode(array($array,$mon[1],$mon[2])));
	fclose($handle);
}

function Query($ip, $port) {
	require_once'MinecraftQuery.php';
	$time = 1;
	$Timer = MicroTime( true );
	$Query = new MinecraftQuery( );
	$Query->Connect($ip, $port, $time);
	$Players = $Query->GetInfo()['Players'];
	$MaxPlayers = $Query->GetInfo()['MaxPlayers'];
	return array($Players,$MaxPlayers,time());
}

function draw($title,$ip,$port) {
	$filename = '../tmp/'.$ip.'_'.$port.'.tmp';
    $graphics = @json_decode(file_get_contents($filename));
	include("../pChart/class/pData.class.php");
	include("../pChart/class/pDraw.class.php");
	include("../pChart/class/pImage.class.php");
	$myData = new pData();
	$myData->addPoints($graphics[0],$ip.$port);
	$myPicture = new pImage(350,230,$myData);
	$myPicture->drawGradientArea(0,0,700,230,DIRECTION_VERTICAL,array("StartR"=>245,"StartG"=>245,"StartB"=>245,"EndR"=>245,"EndG"=>245,"EndB"=>245,"Alpha"=>100)); 
	$myPicture->setFontProperties(array("FontName"=>"../pChart/fonts/calibril.ttf","FontSize"=>11));  
	$myPicture->drawText(15,145,"Игроков",array("R"=>100,"G"=>100,"B"=>100,"FontSize"=>10,"Angle"=>90)); 
	$myPicture->drawText(60,18,$title,array("FontSize"=>10));
	if($graphics[1]!=0) {
		$myPicture->drawText(100,34,$ip.'_'.$port." [online]",array("R"=>0,"G"=>200,"B"=>0,"FontSize"=>10));
	} else {
		$myPicture->drawText(100,34,$ip.'_'.$port." [offline]",array("R"=>200,"G"=>0,"B"=>0,"FontSize"=>10));
	}
	$myPicture->drawText(60,220,"Статистика за последние сутки",array("R"=>100,"G"=>100,"B"=>100,"FontSize"=>10)); 
	$myPicture->setFontProperties(array("FontName"=>"../pChart/fonts/calibril.ttf","FontSize"=>10)); 
	$myPicture->setGraphArea(50,40,340,200);
	$scaleSettings = array("MinDivHeight"=>25,"Factors"=>array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19),"CycleBackground"=>TRUE,"XMargin"=>0,"YMargin"=>0,"Mode"=>SCALE_MODE_ADDALL_START0,"XReleasePercent"=>0,"DrawXLines"=>FALSE, "RemoveXAxis"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200); 
	$myPicture->drawScale($scaleSettings);
	$myPicture->drawLineChart();
	$myPicture->render("../pictures/".$ip.'_'.$port.".png");// Сохранять png в папку pictures.
	//$myPicture->autoOutput("../pictures/".$ip.'_'.$port.".png");
}