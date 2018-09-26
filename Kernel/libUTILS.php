<?php

function strFixAndTrim( $subject = "" ){
	$regex =  array('/\s+/', '/^\s+/', '/\s+$/s', '/\'/', '/#/', '/;/', '/`/', '/Á/','/É/','/Í/','/Ó/','/Ú/');
	$replacement = array(" ", "", "", "\"", "", "", "", "A", "E", "I", "O", "U");
	return preg_replace( $regex, $replacement, $subject );
}

function numericOnly($test = ""){
	$pattern = '/^\d+\.?\d*$/';
	return preg_match($pattern,$test);
}

function getCurrentDateForQuery($type = 0, $day = 0, $month = 0, $year = 0, $time = true){
	$x = "";
	if ($day == 0){$day = date("d");}
	if ($month == 0){$month = date("m");}
	if ($year == 0){$year = date("Y");}
	$x = $year."-".sprintf("%02d",$month)."-".sprintf("%02d",$day);
	if ($time){
		if ($type === 1){
			$x .= " 23:59:59";
		}else{
			$x .= " 00:00:00";
		}
	}
	return $x;
}

function getDatePlusDays($addday = 0, $type = 0){
	$date = strtotime(getCurrentDateForQuery($type) . "+{$addday} day");
	return date("Y-m-d H:i:s",$date);
	
}

function getComboOptionsFromSQL($Data, $conSel = true, $valSel = "", $idExVal = 0, $trimTo = 0){
	if (!is_array($Data))
		return "";
	$dOptions = "";
	for($i=0; $i<count($Data); $i++){
		if (($conSel) && ($i == 0)){
			$selprop = (strlen($valSel) == 0) ? "selected='yes'" : "";
			$dOptions .= "<option value='NV' {$selprop}>Choose One</option>";
		}
		if($idExVal != 0){
			$Data[$i][0] = $Data[$i][0]."|".$Data[$i][$idExVal];
		}
		$cbdisplay = ($trimTo == 0) ? $Data[$i][1] : substr($Data[$i][1],0,$trimTo);
		if (($valSel == $Data[$i][0])){
			$dOptions .= "<option value='".$Data[$i][0]."' selected='yes'>".$cbdisplay."</option>";
		}else{
			$dOptions .= "<option value='".$Data[$i][0]."'>".$cbdisplay."</option>";
		}
	}
	return $dOptions;
}

function doTimeComboOptions($MaxTime = 59, $MinTime = 0){
	$times = "";
	for($i=$MinTime; $i<=$MaxTime; $i++){
		if($i == $MinTime){
			$times .= "<option value='$i' selected='yes'>$i</option>";
		}else{
			$times .= "<option value='$i'>$i</option>";
		}
	}
	return $times;
}

function getDaysComboOptions($conSel = true, $valSel = ""){
	$NSDays = array(
		0 => array(1,"Monday"),
		1 => array(2,"Tuesday"),
		2 => array(3,"Wednesday"),
		3 => array(4,"Thursday"),
		4 => array(5,"Friday"),
		5 => array(6,"Saturday"),
		6 => array(7,"Sunday"),
	);
	return getComboOptionsFromSQL($NSDays,$conSel,$valSel);
}

?>