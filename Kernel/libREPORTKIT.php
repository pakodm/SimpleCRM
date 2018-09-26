<?php

	function crmGetDemoClassAttendance($uid){
		$sview = "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1' class='crmWSAreaTable'>";
		$sview .= "<tr><td>Show attendance list from: <input type='text' size='15' maxlength='50' readonly='true' id='tb_date1'>&nbsp;";
		$sview .= "<a href='javascript:cal1.popup();'><img src='resources/cal.png' width='16' height='16' border='0' /></a></td>";
		$sview .= "<td>to: <input type='text' size='15' maxlength='50' readonly='true' id='tb_date2'>&nbsp;";
		$sview .= "<a href='javascript:cal2.popup();'><img src='resources/cal.png' width='16' height='16' border='0' /></a></td>";
		$sview .= "<td><span class='spanLink' onclick='filterDemoAttBy()'>FILTER</span></td></tr>";
		$sview .= "</table>";
		$sview .= "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1' id='TabHeader'>";
		$sview .= "<tr><td style='width:100px;'>Date</td><td style='width:180px;'>Level</td>";
		$sview .= "<td style='width:200px;'>Name</td>";
		$sview .= "<td style='width:100px;'>Details</td></tr>";
		$sview .= "</table>";
		$sview .= "<div style='width:600px; height:250px; overflow:auto;' id='DemoClassAttList'>";
		$sview .= crmBuildDemoAttendanceReport(getDBDemoClassAttendanceList());
		$sview .= "</div><br>";
		$sview .= "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1' class='crmWSAreaTable'>";
		$sview .= "<tr><td align='left'><span class='spanLink' onclick='exportToXLS(2);'>Exportar A Excel";
		$sview .= "</span></td></tr>";
		$sview .= "</table>";
		return $sview;
	}
	
	function crmBuildDemoAttendanceReport($NSList){
		$srep = "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1'>";
		if (is_array($NSList)){
			for($i=0; $i<count($NSList); $i++){
				$full_name = $NSList[$i]['first_name']." ".$NSList[$i]['last_name'];
				if ($i % 2 == 0){
					$srep .= "<tr style='background:#DEDEDE'>";
				}else{
					$srep .= "<tr>";
				}
				$srep .= "<td align='left' style='width:100px;'>".$NSList[$i]['classdate']."<td>";
				$srep .= "<td align='left' style='width:180px;'>".$NSList[$i]['clevel']."<td>";
				$srep .= "<td align='left' style='width:200px;'>".substr($full_name,0,30)."</td>";
				$srep .= "<td align='left' style='width:100px;'>".$NSList[$i]['phone_number']."<td>";
				$srep .= "</tr>";
			}
		}else{
			$srep .= "<tr><td align='center'>No demo class attendants found</td></tr>";
		}
		$srep .= "</table>";
		return $srep;
	}

	function crmGetAllProspectsReport($uid){
		//$NSProspects = getDBAllProspects();
		$sview = "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1' class='crmWSAreaTable'>";
		$sview .= "<tr><td colspan='2'>To filter prospects type the name or part of: ";
		$sview .= "<input type='text' size='30' maxlength='55' id='tb_dat1' onkeyup='filterByName(this,0);'>";
		$sview .= "</td></tr>";
		$sview .= "<tr><td>Show Prospects: <select id='cb_status' onchange='filterProspectsRBy(0);'>";
		$sview .= "<option value='0' selected>All</option>";
		$sview .= "<option value='1'>Active</option><option value='2'>Non-Active</option></select>";
		$sview .= "</td><td>Filter by Level: <select id='cb_level' onchange='filterProspectsRBy(0);'>";
		$sview .= getComboOptionsFromSQL(getCatalogData("crm_class-level"));
		$sview .= "</select></td></tr>";
		$sview .= "</table>";
		$sview .= "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1' id='TabHeader'>";
		$sview .= "<tr><td style='width:240px;'>Name</td><td style='width:100px;'>Phone</td>";
		//$sview .= "<td style='width:0px;'>E-Mail</td>";
		$sview .= "<td style='width:80px;'>Active</td>";
		$sview .= "<td style='width:160px;'>Actions</td></tr>";
		$sview .= "</table>";
		$sview .= "<div style='width:600px; height:250px; overflow:auto;' id='AllProspektList'>";
		$sview .= crmBuildAllProspectsReport(getDBAllProspects(),$uid);
		$sview .= "</div><br>";
		$sview .= "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1' class='crmWSAreaTable'>";
		$sview .= "<tr><td align='left'><span class='spanLink' onclick='exportToXLS(1);'>Exportar A Excel";
		$sview .= "</span></td></tr>";
		$sview .= "</table>";
		return $sview;
	}
	
	function crmBuildAllProspectsReport($NSProspects,$user){
		$srep = "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1'>";
		if (is_array($NSProspects)){
			for($i=0; $i<count($NSProspects); $i++){
				$full_name = $NSProspects[$i]['first_name']." ".$NSProspects[$i]['last_name'];
				if ($i % 2 == 0){
					$srep .= "<tr style='background:#DEDEDE'>";
				}else{
					$srep .= "<tr>";
				}
				$srep .= "<td align='left' style='width:240px;'>".substr($full_name,0,30)."</td>";
				$srep .= "<td align='left' style='width:100px;'>".$NSProspects[$i]['phone_number']."</td>";
				//$sview .= "<td align='left' style='width:130px;'>".$NSProspects[$i]['email']."</td>";
				$active = ($NSProspects[$i]['is_closed']*1 == 1) ? "N" : "Y";
				$srep .= "<td align='center' style='width:80px;'>".$active."</td>";
				$srep .= "<td align='left' style='width:160px'>";
				if (hasTopAccess($user)){
					$srep .= "<span class='spanLink' onclick='editProspect(".$NSProspects[$i]['id_prospect'].");'>Edit</span>&nbsp;&nbsp;";
					$srep .= "<span class='spanLink' onclick='deleteProspect(".$NSProspects[$i]['id_prospect'].");'>Delete</span>&nbsp;";
					if (strcmp($active,"N") === 0){
						$srep .= "<span class='spanLink' onclick='activateProspect(".$NSProspects[$i]['id_prospect'].");'>Activate</span>&nbsp;";
					}
				}
				$srep .= "</td>";
				$srep .= "</tr>";
			}
		}else{
			$srep .= "<tr><td align='center'>No prospects found</td></tr>";
		}
		$srep .= "</table>";
		return $srep;
	}
	
	function crmGetGeneralStatistics($uid, $f1 = "", $f2 = ""){
		$sg1 = getDBGS1($f1,$f2);
		if (strlen($f1) < 9){$f1 = getCurrentDateForQuery(0,1,0,0,false);}
		if (strlen($f2) < 9){$f2 = getCurrentDateForQuery(0,0,0,0,false);}
		$sview = "<table style='width:600px; border:0px' cellpadding='1' cellspacing='1'>";
		$sview .= "<tr><td style='width:100px; text-align:right;'><b>From</b>:</td>";
		$sview .= "<td style='width:200px; text-align:left'>".$f1."</td>";
		$sview .= "<td style='width:100px; text-align:right;'><b>To</b>:</td>";
		$sview .= "<td style='width:200px; text-align:left'>".$f2."</td></tr>";
		$sview .= "</table>";
		$sview .= "{$sg1}";
		return $sview;
	}
	
	function crmGetLostedProspectsReport($uid, $n = "", $l = 0, $dataOnly = false){
		$LS = getLostedStages();
		$losted = "";
		if (is_array($LS)){
			for($i=0; $i<count($LS); $i++){
				$losted .= $LS[$i]['id_stage'].","; 
			}
			$losted = trim($losted,",");
		}
		if ($dataOnly){
			return getDBProspectsByStage($losted, $n, $l);
		}else{
			return buildProspectReport(getDBProspectsByStage($losted, $n, $l),1);
		}
	}
	
	function crmGetWonProspectsReport($uid, $n = "", $l = 0, $dataOnly = false){
		$LS = getWonStages();
		$won = "";
		if (is_array($LS)){
			for($i=0; $i<count($LS); $i++){
				$won .= $LS[$i]['id_stage'].",";
			}
			$won = trim($won,",");
		}
		if ($dataOnly){
			return getDBProspectsByStage($won, $n, $l);
		}else{
			return buildProspectReport(getDBProspectsByStage($won, $n, $l),2);
		}
	}
	
	function buildProspectReport($NSP, $id = 0){
		$sview = "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1' class='crmWSAreaTable'>";
		$sview .= "<tr><td>To filter prospects type the name or part of: ";
		$sview .= "<input type='text' size='30' maxlength='55' id='tb_dat1' onkeyup='filterByName(this,{$id});'>";
		$sview .= "</td></tr>";
		$sview .= "<tr>";
		$sview .= "<td>Filter by Level: <select id='cb_level' onchange='filterProspectsRBy({$id});'>";
		$sview .= getComboOptionsFromSQL(getCatalogData("crm_class-level"));
		$sview .= "</select></td></tr>";
		$sview .= "</table>";
		$sview .= "<table style='width:600px; border:0px' cellpadding='1' cellspacing='1' id='TabHeader'>";
		$sview .= "<tr><td style='width:200px;'>Name</td><td style='width:100px;'>Date</td>";
		$sview .= "<td style='width:200px;'>Comments</td><td style='width:100px;'>User</td></tr>";
		//$sview .= "<td style='width:130px;'>Actions</td></tr>";
		$sview .= "</table>";
		$sview .= "<div style='width:600px; height:250px; overflow:auto;' id='AllProspektList'>";
		$sview .= crmBuildProspectsReport($NSP);
		$sview .= "</div>";
		return $sview;
	}
	
	function crmBuildProspectsReport($NSProspects){
		$srep = "<table style='width:580px; border:0px' cellpadding='1' cellspacing='1'>";
		if (is_array($NSProspects)){
			for($i=0; $i<count($NSProspects); $i++){
				if ($i % 2 == 0){
					$srep .= "<tr style='background:#DEDEDE'>";
				}else{
					$srep .= "<tr>";
				}
				$srep .= "<td align='left' style='width:200px;'><span class='spanLink' ";
				$srep .= "onclick='getProspectProgress(".$NSProspects[$i]['id_prospect'].");'>";
				$srep .= substr($NSProspects[$i]['full_name'],0,20)."</span></td>";
				$srep .= "<td align='left' style='width:100px;'>".$NSProspects[$i]['rdate']."</td>";
				$srep .= "<td align='left' style='width:200px;'>".$NSProspects[$i]['comments']."</td>";
				$srep .= "<td align='left' style='width:100px;'>".$NSProspects[$i]['user_name']."</td></tr>";
			}
		}else{
			$srep .= "<tr><td align='center'>No available prospects on the selected report</td></tr>";
		}
		$srep .= "</table>";
		return $srep;
	}
	
	function crmViewProspectProgress($idp = 0){
		if (numericOnly($idp)){
			$NSData = selectAllFrom("crm_followup","id_prospect = {$idp}");
			if (is_array($NSData)){
				
			}
		}
	}
?>