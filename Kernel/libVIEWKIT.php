<?php

require_once "libCRMKIT.php";
require_once "libREPORTKIT.php";

function getWorkspaceView($wsid, $usrid, $dat){
	if (numericOnly($wsid)){
		switch($wsid){
			case 1: return followUpView($usrid);
			case 2: return newProspectView($usrid);
			case 3: return settingsView($usrid);
			case 4: return demoClassView($usrid);
			case 5: return reportsView($usrid);
			case 6: return registrationView($usrid);
			case 101: return crmViewCommentsHistory($usrid,$dat);
			case 301: return crmUserProfileView($usrid);
			case 302: return newCRMUserView($usrid);
			case 303: return crmEditReferrersView($usrid);
			case 304: return crmEditSaleStagesView($usrid);
			case 305: return crmEditReasonsView($usrid);
			case 306: return crmEditTeachersView($usrid);
			case 307: return crmEditClassroomView($usrid);
			case 308: return crmEditLevelView($usrid);
			case 309: return crmEditScheduleView($usrid);
			case 310: return crmEditAgreementsView($usrid);
			case 501: return crmGetAllProspectsReport($usrid);
			case 502: return crmGetWonProspectsReport($usrid);
			case 503: return crmGetLostedProspectsReport($usrid);
			case 504: return crmGetGeneralStatistics($usrid);
			case 505: return crmGetDemoClassAttendance($usrid);
			case 551: return crmEditProspectView($usrid,$dat);
			case 552: return crmViewProspectProgress($usrid,strFixAndTrim($dat));
			default: return "0x01NOVIEW";
		}
	}else{
		return "0x01NUMERR";
	}
}

function followUpView($user){
	$NSReturn = array("","");
	$NSReturn[0] = "Follow up candidates";
	$isTopUser = hasTopAccess($user);
	if ($isTopUser){
		$NSOpp = getMyOpportunities($user,true);
	}else{
		$NSOpp = getMyOpportunities($user);
	}

	$view = "<table class='crmWSAreaTable' style='width:750px' border='0' cellspacing='1' cellpadding='1'>";
	$view .= "<tr><td>Filter candidates by user: <select id='cb_filt1' onchange='filterCByUser(this.value);'>";
	if ($isTopUser){
		$view .= getComboOptionsFromSQL(getCRMUserType(3));
	}else{
		$view .= getComboOptionsFromSQL(getCRMUserType(3),true,$user);
	}
	$view .= "</select></td><td>Today Only: ";
	$view .= "<input type='checkbox' id='cb_filt2' onclick='filterPropetsFBy();'></td>";
	$view .= "<td>Filter by name or part of: ";	
	$view .= "<input type='text' size='30' maxlength='55' id='tb_filt3' onkeyup='filterFUPByName(this);'>";
	$view .= "</td></tr>";
	$view .= "<tr><td colspan='2'>Filter by Level: <select id='cb_filt4' onchange='filterPropetsFBy();'>";
	$view .= getComboOptionsFromSQL(getCatalogData("crm_class-level"));
	$view .= "</select></td><td> Filter by Company: <select id='cb_filt5' onchange='filterPropetsFBy();'>";
	$view .= getComboOptionsFromSQL(getCatalogData("crm_agreements"));
	$view .= "</select></td></tr>";
	$view .= "</table>";
	$view .= "<div style='width:420px; height:400px; float:left; overflow:auto;' id='divCandidates'>";
	$view .= getProspectsViewList($NSOpp);
	$view .= "</div>";
	$view .= "<div class='crmWSAreaMenuContent' style='width:330px' id='subWSArea'>";
	$view .= "</div>";
	$NSReturn[1] = $view;
	return json_encode($NSReturn);
}

function getProspectsViewList($NSOps){
	$list = "<table class='crmWSAreaTable' style='width:400px' border='0' cellspacing='1' cellpadding='1' id='TabHeader'>";
	$list .= "<tr><td style='width:180px;'>Name</td><td style='width:130px;'>Deadline</td><td style='width:90px;'>Phone</td></tr>";
	$list .= "</table>";
	$list .= "<table class='crmWSAreaTable' style='width:400px' border='0' cellspacing='1' cellpadding='1'>";
	if (is_array($NSOps)){
		for($i=0; $i<count($NSOps); $i++){
                        $fname = preg_replace("/[^a-z0-9.\s]+/i","",$NSOps[$i]['full_name']);
			$fup = $NSOps[$i]['id_prospect'].",".$NSOps[$i]['id_fup'];
			$list .= "<tr class='Prospectlist' onclick='getFollowUpData({$fup})'>";
			$list .= "<td style='border-bottom:1px solid #A0A0A0; text-align:left; width:180px;'>";
			$list .= substr($fname,0,25)."</td>";
			$list .= "<td style='border-bottom:1px solid #A0A0A0; text-align:left; width:130px'>".substr($NSOps[$i]['followup_date'],0,10)."</td>";
			$phonenbr = preg_replace('/\s/',"",$NSOps[$i]['phone_number']);
			$list .= "<td style='border-bottom:1px solid #A0A0A0; text-align:left; width:90px'>{$phonenbr}</td>";
		}
	}else{
		$list .= "<tr><td style='text-align:center'>No student candidates available.</td></tr>";
	}
	$list .= "</table>";
        //error_log($list,0);
	return $list;
}

function getProspectDetailView($idp,$id_fup,$user){
	$NSDetails = getProspectDetails($idp,$id_fup);
	$sview = (is_array($NSDetails)) ? "0" : $NSDetails;
	if (strncasecmp("0x0",$sview,3) !== 0){
		$NSStg = getSaleStages(true);
		$NSDemo = getDemoClassStage();
		$demostg = "0";
		$NSAgr = getAgreements(true);
		$NSLvl = getCatalogData("crm_class-level",true);
		if (is_array($NSDemo)){
			for($ik=0; $ik<count($NSDemo); $ik++){
				$demostg .= "|".$NSDemo[$ik]['id_stage'];
			}
		}
		$sview = "<table class='crmWSAreaTable' border='0' cellspacing='1' cellpadding='1' style='width:330px'>";
		$sview .= "<tr><td align='center'><b>Prospect Details</b></td></tr>";
		$sview .= "<tr><td>N: ".$NSDetails[0]['first_name']." ".$NSDetails[0]['last_name']."</td></tr>";
		$sview .= "<tr><td>P: ".$NSDetails[0]['phone_number']."</td></tr>";
		if (strcmp($NSDetails[0]['mobile_number'],"4490000000") === 0){
			$sview .= "<tr><td>M: N/A</td></tr>";
		}else{
			$sview .= "<tr><td>M: ".$NSDetails[0]['mobile_number']."</td></tr>";
		}
		$email = $NSDetails[0]['email'];
		$sview .= "<tr><td>E: <a href='mailto:{$email}' style='color:#0000FF'>".$NSDetails[0]['email']."</a></td></tr>";
		//$sview .= "<tr><td>R: ".$NSWhy[($NSDetails[0]['reason_id']-1)]['description']."</td></tr>";
		if ($NSDetails[0]['initial_level']*1 > 0){
			$sview .= "<tr><td>L: ".getDescriptionById($NSLvl,$NSDetails[0]['initial_level']*1)."</td></tr>";
		}else{
			$sview .= "<tr><td>L: Not Set</td></tr>";
		}
		if ($NSDetails[0]['agreement_id']*1 == 0){
			$sview .= "<tr><td>C: None</td></tr>";
		}else{
			$sview .= "<tr><td>C: ".getDescriptionById($NSAgr,$NSDetails[0]['agreement_id']*1,"company_name")."</td></tr>";
		}
		$sview .= "</table>";
		$sview .= "<div style='width:330px; height:2px; background:#FBC529'></div>";
		$sview .= "<table class='crmWSAreaTable' cellspacing='1' cellpadding='1' style='width:330px'>";
		$sview .= "<tr><td align='center' colspan='2'><b>Follow up Details and Next Step</b></td></tr>";
		if ($id_fup > 0){
			$sview .= "<tr><td style='width:80px; text-align:right'>Previous:</td>";
			$sview .= "<td style='width:250px; text-align:left'>".getDescriptionById($NSStg,$NSDetails[0]['sale_stg_id']*1)."</td></tr>";
			$sview .= "<tr><td style='width:80px; text-align:right'>To Do:</td>";
			$sview .= "<td style='width:250px; text-align:left'>".getDescriptionById($NSStg,$NSDetails[0]['next_stg_id']*1)."</td></tr>";
			$sview .= "<tr><td style='text-align:right'>Comments:</td><td style='text-align:left;'>";
			if ((strcmp($NSDetails[0]['comments'],"Initial Record") === 0) || (strlen($NSDetails[0]['comments']) <= 1)){
				$sview .= "No Comments";
			}else{
				$sview .= substr($NSDetails[0]['comments'],0,80);
			}
			$sview .= "<br><span class='spanLink' onclick='getCommentsHistory({$idp},{$id_fup});'>View Earlier Comments</span></td></tr>";
		}else{
			
		}
		if (isPlacementExamStage($NSDetails[0]['next_stg_id'])){
			$sview .= "<tr><td style='width:80px; text-align:right'>Took Test?:</td>";
			$sview .= "<td style='width:250px;' align='left'>";
			$sview .= "<input type='radio' name='rb_tooke' id='rb_yes'>Yes&nbsp;&nbsp;";
			$sview .= "<input type='radio' name='rb_tooke' id='rb_no'>No";
			$sview .= "</td></tr>";
		}
		$sview .= "<tr><td style='width:80px; text-align:right'>Next Step:</td>";
		$sview .= "<td style='width:250px;' align='left'><select id='cb_dat1' onchange='showClassLevel(this.value,\"{$demostg}\")'>";
		$sview .= getComboOptionsFromSQL(getSaleStages())."</select></td></tr>";
		$sview .= "<tr id='trlvlclass' style='visibility:collapse'><td style='width:80px; text-align:right'>Class Level:</td>";
		$sview .= "<td style='width:250px;' align='left'><select id='cb_dat3'>".getComboOptionsFromSQL(getCatalogData("crm_class-level"))."</select></td></tr>";
		$sview .= "<tr><td style='text-align:right'>When:</td>";
		$sview .= "<td align='left'><input type='text' size='20' maxlength='100' readonly='true' id='tb_date1'>&nbsp;";
		$sview .= "<a href='javascript:cal1.popup();'><img src='resources/cal.png' width='16' height='16' border='0' /></a></td></tr>";
		$sview .= "<tr><td style='text-align:right'>Comments:</td>";
		$sview .= "<td align='left'><textarea rows='5' cols='25' id='tb_dat2' onkeyup='doUpperCase(this);'></textarea></td></tr>";
		if (hasTopAccess($user)){
			$NSUsers = getCRMUserType(3);
			$sview .= "<tr><td style='text-align:right'>Assign To:</td>";
			$sview .= "<td align='left'><select id='cb_dat2'>".getComboOptionsFromSQL($NSUsers,false,$NSDetails[0]['assigned_to'])."</select></td></tr>";
		}
		$sview .= "<tr><td colspan='2' style='text-align:center; color:#A90000' id='td_errormsg'>&nbsp;</td></tr>";
		$sview .= "</table>";
		//$sview .= "<div class='divSpacer' style='width:330px;'></div>";
		$sview .= "<div class=\"Button150\" style='text-align:center;' onclick=\"saveFollowup({$idp},{$id_fup});\"><span>Save</span></div>";
	}
	return $sview;
	
}

function newProspectView($user){
	$NSReturn = array("","");
	$NSReturn[0] = "Register a new student candidate";
	$view = "<table class='crmWSAreaTable' border='0' cellspacing='1' cellpadding='1'>";
	$view .= "<tr><td colspan='2'>Fill in the required information</td></tr>";
	$view .= "<tr><td style='width:200px; text-align:right'>First Name:</td>";
	$view .= "<td style='width:550px;' align='left'><input type='text' size='50' maxlength='55' id='tb_dat1' onkeyup='doUpperCase(this);'></td></tr>";
	$view .= "<tr><td style='text-align:right'>Last Name(s):</td>";
	$view .= "<td align='left'><input type='text' size='50' maxlength='55' id='tb_dat2' onkeyup='doUpperCase(this);'></td></tr>";
	$view .= "<tr><td style='text-align:right'>E-Mail:</td>";
	$view .= "<td align='left'><input type='text' size='50' maxlength='100' id='tb_dat3'></td></tr>";
	$view .= "<tr><td style='text-align:right'>Phone number:</td>";
	$view .= "<td align='left'><input type='text' size='50' maxlength='11' id='tb_dat4' onkeyup='doUpperCase(this);'></td></tr>";
	$view .= "<tr><td style='text-align:right'>Mobile number:</td>";
	$view .= "<td align='left'><input type='text' size='50' maxlength='11' id='tb_dat5' onkeyup='doUpperCase(this);'></td></tr>";
	$view .= "<tr><td style='text-align:right'>Why Study English:</td>";
	$view .= "<td align='left'><select id='cb_dat6'>".getComboOptionsFromSQL(getStudyReasons())."</select></td></tr>";
	$view .= "<tr><td style='text-align:right'>Source / Referred By:</td>";
	$view .= "<td align='left'><select id='cb_dat7'>".getComboOptionsFromSQL(getHowFoundUs())."</select></td></tr>";
	$view .= "<tr><td style='text-align:right'>Company:</td>";
	$view .= "<td align='left'><select id='cb_dat11'>".getComboOptionsFromSQL(getAgreements())."</select></td></tr>";
	$view .= "<tr><td style='text-align:right'>Initial Level:</td>";
	$view .= "<td align='left'><select id='cb_dat12'>".getComboOptionsFromSQL(getCatalogData("crm_class-level"))."</select></td></tr>";
	$view .= "<tr><td colspan='2' style='text-align:center; color:#A90000' id='td_errormsg'>&nbsp;</td></tr>";
	$view .= "</table>";
	$view .= "<div style='width:750px; height:2px; background:#FBC529'></div>";
	$view .= "<div class='divSpacer' style='width:750px;'></div>";
	$view .= "<table class='crmWSAreaTable' border='0' cellspacing='1' cellpadding='1'>";
	$view .= "<tr><td style='width:200px; text-align:right'>Current Step:</td>";
	$view .= "<td style='width:550px;' align='left'><select id='cb_dat8'>".getComboOptionsFromSQL(getSaleStages())."</select></td></tr>";
	$view .= "<tr><td style='text-align:right'>Next Step:</td>";
	$view .= "<td align='left'><select id='cb_dat9'>".getComboOptionsFromSQL(getSaleStages())."</select></td></tr>";
	$view .= "<tr><td style='text-align:right'>When:</td>";
	$view .= "<td align='left'><input type='text' size='20' maxlength='100' readonly='true' id='tb_date1'>&nbsp;";
	$view .= "<a href='javascript:cal1.popup();'><img src='resources/cal.png' width='16' height='16' border='0' /></a></td></tr>";
	$view .= "<tr><td style='text-align:right'>Comments:</td>";
	$view .= "<td align='left'><textarea rows='4' cols='35' id='tb_dat13' onkeyup='doUpperCase(this);'></textarea></td></tr>";
	if (hasTopAccess($user)){
		$NSUsers = getCRMUserType(3);
		$view .= "<tr><td style='text-align:right'>Assign To:</td>";
		$view .= "<td align='left'><select id='cb_dat10'>".getComboOptionsFromSQL($NSUsers)."</select></td></tr>";
	}
	$view .= "</table>";
	$view .= "<div class='divSpacer' style='width:750px;'></div>";
	$view .= "<div class=\"Button150\" style='text-align:center;' onclick=\"saveNewProspect({$user});\"><span>Save</span></div>";
	$NSReturn[1] = $view;
	return json_encode($NSReturn);
}

function crmEditProspectView($user,$idp){
	$return = "0x01NUMERR";
	if ((numericOnly($idp)) && (numericOnly($user))){
		$showsave = false;
		$NSProspect = selectAllFrom("crm_prospects","id_prospect = {$idp}");
		$sview = "<table class='crmWSAreaTable' style='width:600px; border:0px' cellpadding='1' cellspacing='1'>";
		if (is_array($NSProspect)){
			$showsave = true;
			$d1 = $NSProspect[0]["first_name"];
			$sview .= "<tr><td style='width:150px; text-align:right'>First Name:</td>";
			$sview .= "<td style='width:450px;' align='left'><input type='text' size='50' maxlength='55' id='tb_dat1' onkeyup='doUpperCase(this);' value='${d1}'></td></tr>";
			
			$d1 = $NSProspect[0]["last_name"];
			$sview .= "<tr><td style='text-align:right'>Last Name(s):</td>";
			$sview .= "<td align='left'><input type='text' size='50' maxlength='55' id='tb_dat2' onkeyup='doUpperCase(this);' value='{$d1}'></td></tr>";
			
			$d1 = $NSProspect[0]["email"];
			$sview .= "<tr><td style='text-align:right'>E-Mail:</td>";
			$sview .= "<td align='left'><input type='text' size='50' maxlength='100' id='tb_dat3' value='{$d1}'></td></tr>";
			
			$d1 = $NSProspect[0]["phone_number"];
			$sview .= "<tr><td style='text-align:right'>Phone number:</td>";
			$sview .= "<td align='left'><input type='text' size='50' maxlength='11' id='tb_dat4' onkeyup='doUpperCase(this);' value='{$d1}'></td></tr>";
			
			$d1 = $NSProspect[0]["mobile_number"];
			$sview .= "<tr><td style='text-align:right'>Mobile number:</td>";
			$sview .= "<td align='left'><input type='text' size='50' maxlength='11' id='tb_dat5' onkeyup='doUpperCase(this);' value='{$d1}'></td></tr>";
			
			$d1 = $NSProspect[0]["reason_id"];
			$sview .= "<tr><td style='text-align:right'>Why Study English:</td>";
			$sview .= "<td align='left'><select id='cb_dat6'>".getComboOptionsFromSQL(getStudyReasons(),false,$d1)."</select></td></tr>";
			
			$d1 = $NSProspect[0]["referrer_id"];
			$sview .= "<tr><td style='text-align:right'>Source / Referred By:</td>";
			$sview .= "<td align='left'><select id='cb_dat7'>".getComboOptionsFromSQL(getHowFoundUs(),false,$d1)."</select></td></tr>";
			
			$d1 = $NSProspect[0]["agreement_id"];
			$sview .= "<tr><td style='text-align:right'>Company:</td>";
			$sview .= "<td align='left'><select id='cb_dat11'>".getComboOptionsFromSQL(getAgreements(),false,$d1)."</select></td></tr>";
			
			$d1 = $NSProspect[0]["initial_level"];
			$sview .= "<tr><td style='text-align:right'>Initial Level:</td>";
			$sview .= "<td align='left'><select id='cb_dat12'>".getComboOptionsFromSQL(getCatalogData("crm_class-level"),false,$d1)."</select></td></tr>";
			
			if (hasTopAccess($user)){
				$NSUsers = getCRMUserType(3);
				$d1 = $NSProspect[0]["assigned_to"];
				$sview .= "<tr><td style='text-align:right'>Assign To:</td>";
				$sview .= "<td align='left'><select id='cb_dat10'>".getComboOptionsFromSQL($NSUsers,false,$d1)."</select></td></tr>";
			}
		}else{
			$sview .= "<tr><td align='center'><b>Unable to retrieve prospect data. "; 
			$sview .= "Please refresh your browser window and try again or contact support</b></td></tr>";
		}
		$sview .= "<tr><td colspan='2' style='text-align:center; color:#A90000' id='td_errormsg'>&nbsp;</td></tr>";
		$sview .= "<tr>";
		$tdstyle = "style='width:300px;'";
		if ($showsave){
			$sview .= "<td style='width:300px;'>";
			$sview .= "<div class=\"Button150\" style='text-align:center;' onclick=\"updateProspect({$user},{$idp});\"><span>Save</span></div>";
			$sview .= "</td>";
			$tdstyle = "colspan='2'";
		}
		$sview .= "<td {$tdstyle} align='center'>";
		$sview .= "<div class=\"Button150\" style='text-align:center;' onclick=\"getSubWorkspace(501);\"><span>Cancel</span></div>";
		$sview .= "</td>";
		$sview .= "</table>";
		$return = $sview;
	}
	return $return;
}

function settingsView($user){
	$NSReturn = array("","");
	$NSReturn[0] = "CRM Settings";
	$view = "<div class='crmWSAreaMenu'>";
	$view .= "<span onclick='getSubWorkspace(301);'>My Profile</span><br>";
	if (isAdmin($user)){
		$view .= "<span onclick='getSubWorkspace(302);'>Edit Users</span><br>";
		$view .= "<span onclick='getSubWorkspace(303);'>Edit Source/Referrers</span><br>";
		$view .= "<span onclick='getSubWorkspace(304);'>Edit Sales Stages</span><br>";
		$view .= "<span onclick='getSubWorkspace(305);'>Edit Reasons</span><br>";
		$view .= "<span onclick='getSubWorkspace(306);'>Edit Teachers</span><br>";
		$view .= "<span onclick='getSubWorkspace(307);'>Edit Classroom</span><br>";
		$view .= "<span onclick='getSubWorkspace(308);'>Edit Level</span><br>";
		$view .= "<span onclick='getSubWorkspace(309);'>Edit Class Schedule</span><br>";
		$view .= "<span onclick='getSubWorkspace(310);'>Edit Agreements</span><br>";
	}
	$view .= "</div>";
	$view .= "<div class='crmWSAreaMenuContent' id='subWSArea'>";
	$view .= "Select an option from the menu on the left";
	$view .= "</div>";
	$NSReturn[1] = $view;
	return json_encode($NSReturn);
}

function demoClassView($user){
	$NSReturn = array("","");
	$NSReturn[0] = "Demo Class Attendance";
	$sview = "<table class='crmWSAreaTable' border='0' cellspacing='1' cellpadding='1'>";
	$sview .= "<tr><td colspan='3'>Choose a filter to display the matching classes</td></tr>";
	$sview .= "<tr>";
	$sview .= "<td style='width:100px; text-align:center'><b>Today Only</b></td>";
	$sview .= "<td style='width:200px; text-align:center'><b>Level</b></td>";
	$sview .= "<td style='width:200px; text-align:center'><b>Classroom</b></td>";
	$sview .= "<td style='width:250px; text-align:center'><b>Teacher</b></td></tr>";
	$sview .= "<tr>";
	$sview .= "<td align='center'><input type='checkbox' id='cb_filt4' checked='true' onclick='filterDemoClass()'></td>";
	//$sview .= "</td>";
	$sview .= "<td align='center'><select id='cb_filt1' onchange='filterDemoClass()'>";
	$sview .= getComboOptionsFromSQL(getCatalogData("crm_class-level"),true,"",0,17)."</select></td>";
	$sview .= "<td align='center'><select id='cb_filt2' onchange='filterDemoClass()'>";
	$sview .= getComboOptionsFromSQL(getCatalogData("crm_classroom"))."</select></td>";
	$sview .= "<td align='center'><select id='cb_filt3' onchange='filterDemoClass()'>";
	$sview .= getComboOptionsFromSQL(getTeachers(),true,"",0,30)."</select></td></tr>";
	$sview .= "</table>";
	$sview .= "<div style='height:240px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildDemoClassList(array("NV","NV","NV","NV"),$user);
	$sview .= "</div>";
	$sview .= "<div style='height:2px; background:#FBC529'></div>";
	$sview .= "<table class='crmWSAreaTable' border='0' cellspacing='1' cellpadding='1'>";
	$sview .= "<tr><td style='text-align:center' colspan='4'><b>Update Demo Class Details</b></td>";
	$sview .= "<tr><td style='text-align:left' colspan='4'><b>Prospect:</b> <span id='sp_pname'>Select one from the above list</span>";
	$sview .= "<input type='hidden' id='ih_crecord' size='1' value='0'></td>";
	$sview .= "<tr><td style='width:115px;' align='right'>Level:</td>";
	$sview .= "<td style='width:160px;' align='left'><select id='cb_dat1'>".getComboOptionsFromSQL(getCatalogData("crm_class-level"))."</select></td>";
	$sview .= "<td style='width:115px;' align='right'>Classroom:</td>";
	$sview .= "<td style='width:160px;' align='left'><select id='cb_dat2'>".getComboOptionsFromSQL(getCatalogData("crm_classroom"))."</select></td>";
	$sview .= "</tr>";
	$sview .= "<tr><td align='right'>Teachers:</td>";
	$sview .= "<td align='left'><select id='cb_dat3'>".getComboOptionsFromSQL(getTeachers())."</select></td>";
	$sview .= "<td align='right'>Came to Class:</td>";
	$sview .= "<td align='left'><select id='cb_dat4'>".getComboOptionsFromSQL(array(array(1,"Yes"),array(2,"No")))."</select></td></tr>";
	$sview .= "<tr><td colspan='4' style='text-align:center; color:#A90000' id='td_errormsg'>&nbsp;</td></tr>";
	$sview .= "</table>";
	$sview .= "<div class='divSpacer' style='width:750px;'></div>";
	$sview .= "<div class=\"Button150\" style='text-align:center;' onclick=\"updateDemoClass();\"><span>Save</span></div>";
	$NSReturn[1] = $sview;
	return json_encode($NSReturn);
}

function crmBuildDemoClassList($opts, $usr){
	$NSDemos = crmFilterDemoClass($opts,$usr);
	$view = (is_array($NSDemos)) ? "0" : $NSDemos;
	if (strncasecmp("0x0",$view,3) !== 0){
		$view = "<table class='crmWSAreaTable' style='width:730px;' border='0' cellspacing='1' cellpadding='1'>";
		if (is_array($NSDemos)){
			$NSLvl = getCatalogData("crm_class-level",true,"*",true);
			$NSTch = getCatalogData("crm_teachers",true,"*",true);
			$NSClr = getCatalogData("crm_classroom",true,"*",true);
			$view .= "<tr><td style='width:200px; text-align:center'><b>Prospect</b></td>";
			$view .= "<td style='width:80px; text-align:center'><b>Date</b></td>";
			$view .= "<td style='width:150px; text-align:center'><b>Level</b></td>";
			$view .= "<td style='width:150px; text-align:center'><b>Classroom</b></td>";
			$view .= "<td style='width:150px; text-align:center'><b>Teacher</b></td>";
			for($i=0; $i<count($NSDemos); $i++){
				if ($i % 2 == 0){
					$view .= "<tr style='background:#DEDEDE'>";
				}else{
					$view .= "<tr>";
				}
				$pname = substr($NSDemos[$i]['first_name'],0,12)." ".substr($NSDemos[$i]['last_name'],0,13);
				$opts = $NSDemos[$i]['id_level']."|".$NSDemos[$i]['id_classroom']."|".$NSDemos[$i]['id_teacher'];
				$view .= "<td><span class='spanLink' onclick='setDemoClassDetails(".$NSDemos[$i]['id_record'].",\"{$opts}\",\"{$pname}\");'>{$pname}</span></td>";
				$ADate = explode(" ",$NSDemos[$i]['class_date']);
				$view .= "<td align='center'>".$ADate[0]."</td>";
				$dsp = "Not Set";
				if ($NSDemos[$i]['id_level']*1 > 0){
					if (strlen($NSLvl[$NSDemos[$i]['id_level']]['description']) > 1){
						$dsp = $NSLvl[$NSDemos[$i]['id_level']]['description'];
					}
				}
				$view .= "<td align='left'>".substr($dsp,0,20)."</td>";
				
				$dsp = "Not Set";
				if ($NSDemos[$i]['id_classroom']*1 > 0){
					if (strlen($NSClr[$NSDemos[$i]['id_classroom']]['description']) > 1){
						$dsp = $NSClr[$NSDemos[$i]['id_classroom']]['description'];
					}
				}
				$view .= "<td align='left'>".substr($dsp,0,20)."</td>";
				
				$dsp = "Not Set";
				if ($NSDemos[$i]['id_teacher']*1 > 0){
					if (strlen($NSTch[$NSDemos[$i]['id_teacher']]['first_name']) > 1){
						$dsp = $NSTch[$NSDemos[$i]['id_teacher']]['first_name']." ".$NSTch[$NSDemos[$i]['id_teacher']]['last_name'];
					}
				}
				$view .= "<td align='left'>".substr($dsp,0,20)."</td>";
				$view .= "</tr>";
			}
		}else{
			$view .= "<tr><td align='center'>There are no prospects scheduled for a demo class today</td></tr>";
		}
		$view .= "</table>";
	}
	return $view;
}

function reportsView($user){
	$NSReturn = array("","");
	$NSReturn[0] = "Operation Reports";
	$view = "<div class='crmWSAreaMenu' style='width:150px;'>";
	$view .= "<span onclick='getSubWorkspace(505);'>Demo Class List</span><br>";
	$view .= "<span onclick='getSubWorkspace(501);'>Prospects List</span><br>";
	if (hasTopAccess($user)){
		$view .= "<span onclick='getSubWorkspace(502);'>Prospects Enrolled</span><br>";
		$view .= "<span onclick='getSubWorkspace(503);'>Prospects Losted</span><br>";
		//$view .= "<span onclick='getSubWorkspace(504);'>General Statistics</span><br>";
	}
	$view .= "</div>";
	$view .= "<div class='crmWSAreaMenuContent' id='subWSArea' style='width:600px;'>";
	$view .= "Select an option from the menu on the left";
	$view .= "</div>";
	$NSReturn[1] = $view;
	return json_encode($NSReturn);
}

function registrationView($user){
	$NSReturn = array("","");
	$NSReturn[0] = "Student Registration";
	$NSStgs = getRegistrationStages();
	$stgs = "";
	if (is_array($NSStgs)){
		for($i=0; $i<count($NSStgs); $i++){
			$stgs .= $NSStgs[$i]['id_stage'].",";
		}
	}
	$stgs = trim($stgs,",");
	$NSData = strlen($stgs) > 0 ? getDBReady2CloseProspects($stgs) : 0;
	$sview = "<table class='crmWSAreaTable' border='0' cellspacing='1' cellpadding='1'>";
	$sview .= "<tr><td style='width:210px; text-align:center'><b>Name</b></td><td style='width:100px; text-align:center'><b>Phone</b></td>";
	$sview .= "<td style='width:100px; text-align:center'><b>Mobile</b></td><td style='width:100px; text-align:center'><b>Date</b></td>";
	$sview .= "<td style='width:210px; text-align:center'><b>Level</b></td></tr>";
	$sview .= "</table>";
	$sview .= "<div style='height:240px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildRegistrationList($NSData, $user);
	$sview .= "</div>";
	$NSReturn[1] = $sview;
	return json_encode($NSReturn);
}

function crmBuildRegistrationList($NSList, $user){
	$view = "<table class='crmWSAreaTable' style='width:730px;' border='0' cellspacing='1' cellpadding='1'>";
	if (is_array($NSList)){
		for($i=0; $i<count($NSList); $i++){
			if ($i % 2 == 0){
				$view .= "<tr style='background:#DEDEDE'>";
			}else{
				$view .= "<tr>";
			}
			$pname = substr($NSList[$i]['first_name'],0,12)." ".substr($NSList[$i]['last_name'],0,13);
			$view .= "<td style='width:210px; text-align:left'>{$pname}</td>";
			$view .= "<td style='width:100px; text-align:right'>".$NSList[$i]['phone_number']."</td>";
			$view .= "<td style='width:100px; text-align:right'>".$NSList[$i]['mobile_number']."</td>";
			$view .= "<td style='width:100px; text-align:center'>".$NSList[$i]['fdate']."</td>";
			$view .= "<td style='width:220px; text-align:left'>".$NSList[$i]['clevel']."</td>";
			$view .= "</tr>";
		}
	}else{
		$view .= "<tr><td style='text-align:center'>There are no prospects scheduled for registration</td></tr>";
	}
	$view .= "</table>";
	return $view;
}

function crmUserProfileView($user){
	$idkey = (numericOnly($user)) ? $user : 0;
	$NSCData = getSingleCatalogData("crm_users",$idkey);
	$uname = (is_array($NSCData)) ? $NSCData[0]['user_name'] : "";
	$sdata = "<table class='crmWSAreaTable' style='width:550px;' border='0' cellspacing='1' cellpadding='1'>";
	$sdata .= "<tr><td style='text-align:center' colspan='2'><b>User Profile</b></td>";
	$sdata .= "<tr><td style='width:150px; text-align:right'>User Name:</td>";
	$sdata .= "<td style='width:400px;' align='left'>{$uname}</td></tr>";
	$sdata .= "<tr><td style='text-align:right'>Password:</td>";
	$sdata .= "<td align='left'><input type='password' size='40' maxlength='55' id='tb_dat1'></td></tr>";
	$sdata .= "<tr><td style='text-align:right'>Type Password Again:</td>";
	$sdata .= "<td align='left'><input type='password' size='40' maxlength='55' id='tb_dat2'></td></tr>";
	$sdata .= "<tr><td colspan='2' style='text-align:center; color:#A90000' id='td_errormsg'>&nbsp;</td></tr>";
	$sdata .= "</table>";
	$sdata .= "<div class='divSpacer' style='width:550px;'></div>";
	$sdata .= "<div class=\"Button150\" style='text-align:center;' onclick=\"saveMyProfile({$user});\"><span>Save</span></div>";
	return $sdata;
}

function newCRMUserView($user){
	$NSUsers = getCRMUsers();
	$sview = "<div style='width:550px; height:270px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildSettingsCatalog(8);
	$sview .= "</div>";
	$sview .= "<div style='width:550px; height:2px; background:#FBC529'></div>";
	$sview .= "<div style='width:550px;' id='crmEditUser'>";
	$sview .= getUserDataForEdit(0, $user);
	$sview .= "</div>";
	return $sview;
}

function getUserDataForEdit($iduser = 0, $user){
	$idkey = (numericOnly($iduser)) ? $iduser : 0;
	$NSCData = getSingleCatalogData("crm_users",$idkey);
	$uname = (is_array($NSCData)) ? $NSCData[0]['user_name'] : "";
	$sdata = "<table class='crmWSAreaTable' style='width:550px;' border='0' cellspacing='1' cellpadding='1'>";
	$sdata .= "<tr><td style='text-align:center' colspan='2'><b>New User</b></td>";
	$sdata .= "<tr><td style='width:150px; text-align:right'>User Name:</td>";
	$sdata .= "<td style='width:400px;' align='left'><input type='text' size='40' maxlength='55' id='tb_dat1' value='{$uname}'></td></tr>";
	$sdata .= "<tr><td style='text-align:right'>Password:</td>";
	$sdata .= "<td align='left'><input type='password' size='40' maxlength='55' id='tb_dat2'></td></tr>";
	$sdata .= "<tr><td style='text-align:right'>Type Password Again:</td>";
	$sdata .= "<td align='left'><input type='password' size='40' maxlength='55' id='tb_dat3'></td></tr>";
	$sdata .= "<tr><td style='text-align:right'>User Type:</td>";
	$sdata .= "<td align='left'><select id='cb_dat4'>".getComboOptionsFromSQL(getUserTypes())."</select></td></tr>";
	$sdata .= "<tr><td colspan='2' style='text-align:center; color:#A90000' id='td_errormsg'>&nbsp;</td></tr>";
	$sdata .= "</table>";
	$sdata .= "<div class='divSpacer' style='width:550px;'></div>";
	$sdata .= "<div class=\"Button150\" style='text-align:center;' onclick=\"saveNewUser({$user},{$iduser});\"><span>Save</span></div>";
	return $sdata;
}

function crmBuildSettingsCatalog($catalog_id = 0){
	$NSCat = null;
	$catalog_name = "Error";
	switch ($catalog_id){
		case 1:
			$NSCat = getHowFoundUs(true);
			$catalog_name = "referrers";
			break;
		case 2:
			$NSCat = getSaleStages(true);
			$catalog_name = "sale stages";
			break;
		case 3:
			$NSCat = getStudyReasons(true);
			$catalog_name = "reasons";
			break;
		case 4:
			$NSCat = getCatalogData("crm_teachers",true);
			$catalog_name = "teachers";
			break;
		case 5:
			$NSCat = getCatalogData("crm_classroom",true);
			$catalog_name = "classrooms";
			break;
		case 6:
			$NSCat = getCatalogData("crm_class-level",true);
			$catalog_name = "class levels";
			break;
		case 7:
			$NSCat = getCatalogData("crm_agreements",true);
			$catalog_name = "agreements";
			break;
		case 8:
			$NSCat = getCRMUsers();
			$catalog_name = "users";
			break;
	}
	$catalog = "<table class='crmWSAreaTable' style='width:530px;' border='0' cellspacing='1' cellpadding='1'>";
	if ($catalog_id == 2){
		$catalog .= "<tr><td style='text-align:center; width:240px;'><b>Description</b></td>";
		$catalog .= "<td style='text-align:center; width:80px;'><b>Success (%)</b></td>";
		$catalog .= "<td style='text-align:center; width:50px;'><b>Visible</b></td>";
		$catalog .= "<td style='text-align:center; width:160px;'><b>Options</b></td></tr>";
	}else if ($catalog_id == 4){
		$catalog .= "<tr><td style='text-align:center; width:280px;'><b>Name</b></td>";
		$catalog .= "<td style='text-align:center; width:50px;'><b>Available</b></td>";
		$catalog .= "<td style='text-align:center; width:200px;'><b>Options</b></td></tr>";
	}elseif ($catalog_id == 7){
		$catalog .= "<tr><td style='text-align:center; width:190px;'><b>Company/Institution</b></td>";
		$catalog .= "<td style='text-align:center; width:170px;'><b>Agreement</b></td>";
		$catalog .= "<td style='text-align:center; width:50px;'><b>Visible</b></td>";
		$catalog .= "<td style='text-align:center; width:110px;'><b>Options</b></td></tr>";
	}elseif ($catalog_id == 8){
		$catalog .= "<tr><td style='text-align:center; width:180px;'><b>User Name</b></td>";
		$catalog .= "<td style='text-align:center; width:150px;'><b>Last Login</b></td>";
		$catalog .= "<td style='text-align:center; width:50px;'><b>Enabled</b></td>";
		$catalog .= "<td style='text-align:center; width:150px;'><b>Options</b></td></tr>";
	}else{
		$catalog .= "<tr><td style='text-align:center; width:280px;'><b>Description</b></td>";
		$catalog .= "<td style='text-align:center; width:50px;'><b>Visible</b></td>";
		$catalog .= "<td style='text-align:center; width:200px;'><b>Options</b></td></tr>";
	}
	if (is_array($NSCat)){
		for ($i=0; $i<count($NSCat); $i++){
			if ($i % 2 == 0){
				$catalog .= "<tr style='background:#DEDEDE'>";
			}else{
				$catalog .= "<tr>";
			}
			$vtxt1 = "Invisible"; $vtxt2 = "Visible";
			if ($catalog_id == 8){
				$v = ($NSCat[$i]['enabled']*1 == 1) ? true : false;
				$vtxt1 = "Disable"; $vtxt2 = "Enable";
			}else{
				$v = ($NSCat[$i]['visible']*1 == 1) ? true : false;
			}
			if ($catalog_id == 4){
				$catalog .= "<td align='left'>".$NSCat[$i]['first_name']." ".$NSCat[$i]['last_name']."</td>";
			}elseif ($catalog_id == 7){
				$catalog .= "<td align='left'>".$NSCat[$i]['company_name']."</td>";
				$catalog .= "<td align='left'>".$NSCat[$i]['agreement']."</td>";
			}elseif ($catalog_id == 8){
				$catalog .= "<td align='left'>".$NSCat[$i]['user_name']."</td>";
				$catalog .= "<td align='left'>".$NSCat[$i]['last_login']."</td>";
			}else{
				$catalog .= "<td align='left'>".$NSCat[$i]['description']."</td>";
			}
			if ($catalog_id == 2){
				$catalog .= "<td align='right'>".$NSCat[$i]['success_ratio']."</td>";
			}
			$catalog .= "<td align='center'>";
			$catalog .= ($v) ? "Y" : "N";
			$catalog .= "</td>";
			$catalog .= "<td align='left'>";
			if ($catalog_id == 8){
				$catalog .= "&nbsp;<span class='spanLink' onclick='editUser(".$NSCat[$i][0].")'>Edit</span>&nbsp;";
			}else{
				$catalog .= "&nbsp;<span class='spanLink' onclick='editCatalog({$catalog_id},".$NSCat[$i][0].")'>Edit</span>&nbsp;";
			}
			if ($v){
				$catalog .= "&nbsp;<span class='spanLink' onclick='toggleVisible({$catalog_id},".$NSCat[$i][0].",0);'>{$vtxt1}</span>&nbsp;";
			}else{
				$catalog .= "&nbsp;<span class='spanLink' onclick='toggleVisible({$catalog_id},".$NSCat[$i][0].",1);'>{$vtxt2}</span>&nbsp;";
			}
			$catalog .= "&nbsp;<span class='spanLink' onclick='deleteCatalog({$catalog_id},".$NSCat[$i][0].")'>Delete</span>&nbsp;";
			$catalog .= "</td></tr>";
		}
	}else{
		$catalog .= "<tr><td colspan='3'>No {$catalog_name} were found</td></tr>";
	}
	$catalog .= "</table>";
	return $catalog;
}

function crmBuildCatalogEditView($catalog_id, $idkey = 0){
	$NSCat = null;
	$catalog_name = "Error";
	$table_name = "NA";
	switch ($catalog_id){
		case 1:
			$catalog_name = "Referrer";
			$table_name = "crm_referrers";
			break;
		case 2:
			$catalog_name = "Sale Stage";
			$table_name = "crm_sale-stages";
			break;
		case 3:
			$catalog_name = "Reason";
			$table_name = "crm_reasons";
			break;
		case 4:
			$catalog_name = "Teacher";
			$table_name = "crm_teachers";
			break;
		case 5:
			$catalog_name = "Classroom";
			$table_name = "crm_classroom";
			break;
		case 6:
			$catalog_name = "Class Level";
			$table_name = "crm_class-level";
			break;
		case 7:
			$catalog_name = "Agreements";
			$table_name = "crm_agreements";
			break;
	}
	$NSCData = getSingleCatalogData($table_name,$idkey);
	$d1 = is_array($NSCData) ? $NSCData[0][1] : "";
	$d2 = is_array($NSCData) ? $NSCData[0][2] : "";
	$vis = is_array($NSCData) ? $NSCData[0]['visible'] : "1";
	$catalog = "<table class='crmWSAreaTable' style='width:550px;' border='0' cellspacing='1' cellpadding='1'>";
	$catalog .= "<tr><td style='text-align:center' colspan='2'><b>New {$catalog_name}</b></td>";
	if ($catalog_id == 4){
		$catalog .= "<tr><td style='width:150px; text-align:right'>First Name:</td>";
		$catalog .= "<td style='width:400px;' align='left'><input type='text' size='40' maxlength='70' id='tb_dat1' value='{$d1}'></td></tr>";
		$catalog .= "<tr><td style='width:150px; text-align:right'>Last Name:</td>";
		$catalog .= "<td style='width:400px;' align='left'><input type='text' size='40' maxlength='90' id='tb_dat2' value='{$d2}'></td></tr>";
	}elseif ($catalog_id == 7){
		$catalog .= "<tr><td style='width:200px; text-align:right'>Company/Institution Name:</td>";
		$catalog .= "<td style='width:350px;' align='left'><input type='text' size='40' maxlength='90' id='tb_dat1' value='{$d1}'></td></tr>";
		$catalog .= "<tr><td style='width:200px; text-align:right'>Agreement:</td>";
		$catalog .= "<td style='width:350px;' align='left'><input type='text' size='40' maxlength='90' id='tb_dat2' value='{$d2}'></td></tr>";
	}elseif ($catalog_id == 8){
		
	}else{
		$catalog .= "<tr><td style='width:150px; text-align:right'>Description:</td>";
		$catalog .= "<td style='width:400px;' align='left'><input type='text' size='40' maxlength='55' id='tb_dat1' value='{$d1}'></td></tr>";
	}
	if ($catalog_id == 2){
		$catalog .= "<tr><td style='width:150px; text-align:right'>Success Ratio (%):</td>";
		$catalog .= "<td style='width:400px;' align='left'><input type='text' size='40' maxlength='3' id='tb_dat2' value='{$d2}'></td></tr>";
	}
	$catalog .= "<tr><td colspan='2' style='text-align:center; color:#A90000' id='td_errormsg'>&nbsp;</td></tr>";
	$catalog .= "</table>";
	$catalog .= "<div class='divSpacer' style='width:550px;'></div>";
	$catalog .= "<div class=\"Button150\" style='text-align:center;' onclick=\"saveCatalog({$catalog_id},{$idkey},{$vis});\"><span>Save</span></div>";
	return $catalog;
}

function crmEditReferrersView($user){
	$sview = "<div style='width:550px; height:270px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildSettingsCatalog(1);
	$sview .= "</div>";
	$sview .= "<div style='width:550px; height:2px; background:#FBC529'></div>";
	$sview .= "<div style='width:550px;' id='crmEditCatalog'>";
	$sview .= crmBuildCatalogEditView(1);
	$sview .= "</div>";
	return $sview;
}

function crmEditSaleStagesView($user){
	$sview = "<div style='width:550px; height:270px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildSettingsCatalog(2);
	$sview .= "</div>";
	$sview .= "<div style='width:550px; height:2px; background:#FBC529'></div>";
	$sview .= "<div style='width:550px;' id='crmEditCatalog'>";
	$sview .= crmBuildCatalogEditView(2);
	$sview .= "</div>";
	return $sview;
}

function crmEditReasonsView($user){
	$sview = "<div style='width:550px; height:270px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildSettingsCatalog(3);
	$sview .= "</div>";
	$sview .= "<div style='width:550px; height:2px; background:#FBC529'></div>";
	$sview .= "<div style='width:550px;' id='crmEditCatalog'>";
	$sview .= crmBuildCatalogEditView(3);
	$sview .= "</div>";
	return $sview;
}

function crmEditTeachersView($user){
	$sview = "<div style='width:550px; height:270px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildSettingsCatalog(4);
	$sview .= "</div>";
	$sview .= "<div style='width:550px; height:2px; background:#FBC529'></div>";
	$sview .= "<div style='width:550px;' id='crmEditCatalog'>";
	$sview .= crmBuildCatalogEditView(4);
	$sview .= "</div>";
	return $sview;
}

function crmEditClassroomView($user){
	$sview = "<div style='width:550px; height:270px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildSettingsCatalog(5);
	$sview .= "</div>";
	$sview .= "<div style='width:550px; height:2px; background:#FBC529'></div>";
	$sview .= "<div style='width:550px;' id='crmEditCatalog'>";
	$sview .= crmBuildCatalogEditView(5);
	$sview .= "</div>";
	return $sview;
}

function crmEditLevelView($user){
	$sview = "<div style='width:550px; height:270px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildSettingsCatalog(6);
	$sview .= "</div>";
	$sview .= "<div style='width:550px; height:2px; background:#FBC529'></div>";
	$sview .= "<div style='width:550px;' id='crmEditCatalog'>";
	$sview .= crmBuildCatalogEditView(6);
	$sview .= "</div>";
	return $sview;
}

function crmEditAgreementsView($user){
	$sview = "<div style='width:550px; height:270px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmBuildSettingsCatalog(7);
	$sview .= "</div>";
	$sview .= "<div style='width:550px; height:2px; background:#FBC529'></div>";
	$sview .= "<div style='width:550px;' id='crmEditCatalog'>";
	$sview .= crmBuildCatalogEditView(7);
	$sview .= "</div>";
	return $sview;
}

function crmScheduleView($options, $user){
	$NSSche = crmFilterSchedule($options, $user);
	$view = (is_array($NSSche)) ? "0" : $NSSche;
	if (strncasecmp("0x0",$view,3) !== 0){
		$view = "<table class='crmWSAreaTable' style='width:530px;' border='0' cellspacing='1' cellpadding='1'>";
		if (is_array($NSSche)){
			$day = 0; $row = 0;
			for ($i=0; $i<count($NSSche); $i++){
				if ($day != $NSSche[$i]['id_day']){
					$day = $NSSche[$i]['id_day'];
					$astxt = "";
					switch ($day){
						case 1: $astxt = "Monday"; break;
						case 2: $astxt = "Tuesday"; break;
						case 3: $astxt = "Wednesday"; break;
						case 4: $astxt = "Thursday"; break;
						case 5: $astxt = "Friday"; break;
						case 6: $astxt = "Saturday"; break;
						case 7: $astxt = "Sunday"; break;
					}
					$view .= "<tr><td colspan='5'><b>{$astxt}</b></td></tr>";
					$row = 0;
				}
				if ($row % 2 == 0){
					$view .= "<tr style='background:#DEDEDE'>";
				}else{
					$view .= "<tr>";
				}
				$color = "000000";
				if ($NSSche[$i]['available']*1 == 0){ $color = "A90000"; }
				$view .= "<td style='color:#{$color}' align='left'>".date("H:i:s",strtotime($NSSche[$i]['start_time']))." - ";
				$view .= date("H:i:s",strtotime($NSSche[$i]['end_time']))."</td>";
				$view .= "<td style='color:#{$color}' align='left'>".$NSSche[$i]['ldesc']."</td>";
				$view .= "<td style='color:#{$color}' align='left'>".$NSSche[$i]['cdesc']."</td>";
				$view .= "<td style='color:#{$color}' align='left'>".$NSSche[$i]['fname']."</td>";
				if ($NSSche[$i]['available']*1 == 1){
					$view .= "<td align='left'><span class='spanLink' onclick='toggleSchedule(".$NSSche[$i]['id_schedule'].",0);'>Disable</span></td>";
				}else{
					$view .= "<td align='left'><span class='spanLink' onclick='toggleSchedule(".$NSSche[$i]['id_schedule'].",1);'>Enable</span></td>";
				}
				$view .= "</tr>";
				$row++;
			}
		}else{
			$view .= "<tr><td align='center'>No available classes on the specified filters</td></tr>";
		}
		$view .= "</table>";
	}
	return $view;
}

function crmEditScheduleView($user){
	$sview = "<table class='crmWSAreaTable' style='width:550px;' border='0' cellspacing='1' cellpadding='1'>";
	$sview .= "<tr><td colspan='4'>Choose a filter to display the matching classes</td></tr>";
	$sview .= "<tr><td style='width:100px; text-align:center'><b>Day</b></td>";
	$sview .= "<td style='width:150px; text-align:center'><b>Level</b></td>";
	$sview .= "<td style='width:150px; text-align:center'><b>Classroom</b></td>";
	$sview .= "<td style='width:150px; text-align:center'><b>Teacher</b></td></tr>";
	$sview .= "<tr><td align='center'><select id='cb_filt1' onchange='filterSchedule()'>";
	$sview .= getDaysComboOptions()."</select></td>";
	$sview .= "<td align='center'><select id='cb_filt2' onchange='filterSchedule()'>";
	$sview .= getComboOptionsFromSQL(getCatalogData("crm_class-level"),true,"",0,17)."</select></td>";
	$sview .= "<td align='center'><select id='cb_filt3' onchange='filterSchedule()'>";
	$sview .= getComboOptionsFromSQL(getCatalogData("crm_classroom"))."</select></td>";
	$sview .= "<td align='center'><select id='cb_filt4' onchange='filterSchedule()'>";
	$sview .= getComboOptionsFromSQL(getTeachers(),true,"",0,25)."</select></td></tr>";
	$sview .= "</table>";
	$sview .= "<div style='width:550px; height:240px; overflow:auto;' id='crmDivCatalog'>";
	$sview .= crmScheduleView(array("NV","NV","NV","NV"),$user);
	$sview .= "</div>";
	$sview .= "<div style='width:550px; height:2px; background:#FBC529'></div>";
	$sview .= "<table class='crmWSAreaTable' style='width:550px;' border='0' cellspacing='1' cellpadding='1'>";
	$sview .= "<tr><td style='text-align:center' colspan='4'><b>New Schedule</b></td>";
	$sview .= "<tr><td style='width:115px;' align='right'>Day:</td>";
	$sview .= "<td style='width:160px;' align='left'><select id='cb_dat1'>".getDaysComboOptions()."</select></td>";
	$sview .= "<td style='width:115px;' align='right'>Level:</td>";
	$sview .= "<td style='width:160px;' align='left'><select id='cb_dat2'>".getComboOptionsFromSQL(getCatalogData("crm_class-level"))."</select></td>";
	$sview .= "</tr>";
	$sview .= "<tr><td align='right'>Classroom:</td>";
	$sview .= "<td align='left'><select id='cb_dat3'>".getComboOptionsFromSQL(getCatalogData("crm_classroom"))."</select></td>";
	$sview .= "<td align='right'>Teachers:</td>";
	$sview .= "<td align='left'><select id='cb_dat4'>".getComboOptionsFromSQL(getTeachers())."</select></td></tr>";
	$sview .= "<tr><td align='right'>Start time:</td>";
	$sview .= "<td align='left'><select id='cb_st1'>".doTimeComboOptions(23,0)."</select> : ";
	$sview .= "<select id='cb_st2'>".doTimeComboOptions()."</select></td>";
	$sview .= "<td align='right'>End time:</td>";
	$sview .= "<td align='left'><select id='cb_et1'>".doTimeComboOptions(23,0)."</select> : ";
	$sview .= "<select id='cb_et2'>".doTimeComboOptions()."</select></td></tr>";
	$sview .= "<tr><td colspan='4' style='text-align:center; color:#A90000' id='td_errormsg'>&nbsp;</td></tr>";
	$sview .= "</table>";
	$sview .= "<div class='divSpacer' style='width:550px;'></div>";
	$sview .= "<div class=\"Button150\" style='text-align:center;' onclick=\"saveSchedule();\"><span>Save</span></div>";
	return $sview;
}

function crmViewCommentsHistory($u,$p){
	$NSReturn = array("","");
	$NSP = getSingleCatalogData("crm_prospects",$p);
	$NSReturn[0] = "Comments' History For ".$NSP[0]['first_name']." ".$NSP[0]['last_name'];
	$NSC = crmGetProspectComments($p);
	$view = "<table class='crmWSAreaTable' style='table-layout:fixed; border:0px' cellspacing='1' cellpadding='1'>";
	if (is_array($NSC)){
		for($i=0; $i<count($NSC); $i++){
			if (!(strcmp($NSC[$i]['comments'],"Initial Record") === 0) && (strlen($NSC[$i]['comments']) > 1)){
				$view .= "<tr><td align='left'><div style='border-bottom:2px solid #FBC529'>";
				$view .= $NSC[$i]['cdate']."</div></td></tr>";
				$view .= "<tr><td aling='left' style='word-wrap:break-word;'>".$NSC[$i]['comments']."<br></td></tr>";
				$view .= "<tr><td align='right'>".$NSC[$i]['user_name']."&nbsp;</td></tr>";
			}
		}
	}else{
		$view .= "<tr><td style='text-align:center'>No comments found</td></tr>";
	}
	
	$view .= "</table>";
	$view .= "<div class='divSpacer' style='width:750px;'></div>";
	$view .= "<div class=\"Button150\" style='text-align:center;' onclick=\"getWorkspace(1);\"><span>Go Back</span></div>";
	$NSReturn[1] = $view;
	return json_encode($NSReturn);
}

?>
