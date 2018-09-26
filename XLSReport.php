<?php
	session_start();

	if (!isset($_SESSION['CRMKEY'],$_SESSION['CRMUST'])){
		header("Location:index.php");
		exit;
	}
	
	if (!isset($_GET['XLSID'])){exit;}
	if (!isset($_GET['PARMS'])){exit;}
	
	require_once "Kernel/libCRMKIT.php";
	require_once "Kernel/libXLS.php";
	
	$NSParms = explode("|",base64_decode($_GET['PARMS']));
	//error_log(count($NSParms));
	$id = (is_array($NSParms)) ? strFixAndTrim($NSParms[count($NSParms)-2]) : 0;
	$file_name = 'reporte-prospectos';
	$NSData = array();
	
	if ($id*1 == $_GET['XLSID']){
		if ($id*1 == 1){
			$dat1 = strFixAndTrim($NSParms[0]);
			$dat2 = strFixAndTrim($NSParms[1]);
			$dat3 = strFixAndTrim($NSParms[2]);

			if (strcasecmp($dat3, "NV") === 0){$dat3 = 0;}
			if (!numericOnly($dat2)){$dat2 = 0;}
			if (!numericOnly($dat3)){$dat3 = 0;}
			
			$NSPros = getDBProspectsForXLS($dat1,$dat2,$dat3);
			//error_log("P: ".count($NSPros),0);
			$NSData = array(1 => array('NOMBRE','APELLIDO','TELEFONO','CELULAR','E-MAIL','EMPRESA','NIVEL','SEGUIMIENTO','ACTIVO'));
			if (is_array($NSPros)){
				for ($j=0; $j<count($NSPros); $j++){
					$estado = $NSPros[$j]['is_closed']*1 > 0 ? "NO" : "SI";
					array_push($NSData,array(
						$NSPros[$j]['first_name'],
						$NSPros[$j]['last_name'],
						$NSPros[$j]['phone_number'],
						$NSPros[$j]['mobile_number'],
						$NSPros[$j]['email'],
						$NSPros[$j]['company_name'],
						$NSPros[$j]['clevel'],
						$NSPros[$j]['sdesc'],
						$estado
					));
				}
			}
		}
		if ($id*1 == 2){
			$dat1 = strFixAndTrim($NSParms[0]);
			$dat2 = strFixAndTrim($NSParms[1]);
			$NSList = getDBDemoClassAttendanceList($dat1,$dat2);
			error_log("L: ".count($NSList),0);
			$NSData = array(1 => array('FECHA','NIVEL','NOMBRE','APELLIDO','TELEFONO','CELULAR','E-MAIL','SALON','RESULTADO'));
			if (is_array($NSList)){
				for ($j=0; $j<count($NSList); $j++){
					$estado = "Desconocido";
					if ($NSList[$j]['show_up']*1 == 1){$estado = "Asistio";}
					if ($NSList[$j]['show_up']*1 == 2){$estado = "No Asistio";}
					array_push($NSData,array(
						$NSList[$j]['classdate'],
						$NSList[$j]['clevel'],
						$NSList[$j]['first_name'],
						$NSList[$j]['last_name'],
						$NSList[$j]['phone_number'],
						$NSList[$j]['mobile_number'],
						$NSList[$j]['email'],
						$NSList[$j]['classroom'],
						$estado
					));
				}
			}
		}
	}
	
	$xls = new Excel_XML('UTF-8', true, 'Prospectos');
	$xls->addArray($NSData);
	$xls->generateXML("{$file_name}");
?>