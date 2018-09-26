<?php

require_once "libIOKIT.php";
require_once "libUTILS.php";

function isAdmin($uid){
	$uid = strFixAndTrim($uid);
	$NSU = selectAllFrom("crm_users","user_id = {$uid}","user_type");
	if (is_array($NSU)){
		$ut = $NSU[0][0];
		if (numericOnly($ut)){
			$NST = selectAllFrom("crm_user-type");
			if (preg_match("/^admin/i",$NST[($ut-1)]['description'])){
				return true;
			}
		}
	}
	return false;
}

function isManager($uid){
	$uid = strFixAndTrim($uid);
	$NSU = selectAllFrom("crm_users","user_id = {$uid}","user_type");
	if (is_array($NSU)){
		$ut = $NSU[0][0];
		if (numericOnly($ut)){
			$NST = selectAllFrom("crm_user-type");
			if (preg_match("/^manage/i",$NST[($ut-1)]['description'])){
				return true;
			}
		}
	}
	return false;
}

function hasTopAccess($uid){
	$uid = strFixAndTrim($uid);
	if (numericOnly($uid)){
		$NSU = selectAllFrom("crm_users","user_id = {$uid}","user_type");
		if (is_array($NSU)){
			$ut = $NSU[0][0];
			if (numericOnly($ut)){
				$NST = selectAllFrom("crm_user-type");
				if (preg_match("/^(admin|manage)/i",$NST[($ut-1)]['description'])){
					return true;
				}
				/*
				if (preg_match("/^manage/i",$NST[($ut-1)]['description'])){
					return true;
				}
				*/
			}
		}
	}
	return false;
}

function crmHowManyTasksToday($user){
	$f1 = getCurrentDateForQuery();
	$f2 = getCurrentDateForQuery(1);
	$count = 0;
	if (numericOnly($user)){
		if (hasTopAccess($user)){
			$count = getDBTasksForDates($f1,$f2);
		}else{
			$count = getDBTasksForDates($f1,$f2,$user);
		}
	}
	return $count;
}

function crmHowManyDemoToday($user){
	$f1 = getDatePlusDays(1); //getCurrentDateForQuery();
	$f2 = getDatePlusDays(1,1); //getCurrentDateForQuery(1);
	$count = 0;
	if (numericOnly($user)){
		if (hasTopAccess($user)){
			$count = getDBDemoForDates($f1,$f2,0,false);
		}else{
			$count = getDBDemoForDates($f1,$f2,$user,false);
		}
	}
	return $count;
}

function getCRMUserType($type = 0){
	$NS = 0;
	if ($type > 0){
		if (numericOnly($type)){
			$NS = selectAllFrom("crm_users","user_type = {$type}");
		}
	}
	return $NS;
}

function crmGetMyName($user){
	$uname = "E";
	if (numericOnly($user)){
		$NS = selectAllFrom("crm_users","user_id = {$user}");
		$uname = (is_array($NS)) ? $NS[0]['user_name'] : "N/A";
	}
	return $uname;
}

function getDescriptionById($NS, $id, $field = "description"){
	$desc = "N/A";
	if (is_array($NS)){
		for($i=0; $i<count($NS); $i++){
			if ($NS[$i][0] == $id){
				$desc = $NS[$i][$field];
				break;
			}
		}
	}
	return $desc;
}

function getCRMUsers(){
	$NS = selectAllFrom("crm_users","user_id > 1");
	return $NS;
}

function getUserTypes(){
	$NS = selectAllFrom("crm_user-type");
	return $NS;
}

function getStudyReasons($all = false){
	return getCatalogData("crm_reasons",$all);
}

function getHowFoundUs($all = false){
	return getCatalogData("crm_referrers",$all);
}

function getAgreements($all = false){
	return getCatalogData("crm_agreements",$all);
}

function getCatalogData($table, $all = false, $fd = "*", $ashash = false){
	$wh = $all ? "" : "visible = 1";
	$NS = selectAllFrom($table,$wh,$fd);
	if ($ashash){
		$NSHash = array();
		if (is_array($NS)){
			for($i=0; $i<count($NS); $i++){
				$NSHash[$NS[$i][DBSchema::getKeyfield($table)]] = $NS[$i];
			}
			return $NSHash;
		}else{
			return $NS;
		}
	}else{
		return $NS;
	}
}

function getSaleStages($all = false){
	$wh = $all ? "" : "visible = 1";
	$fd = $all ? "*" : "id_stage, description";
	$NS = selectAllFrom("crm_sale-stages",$wh,$fd);
	return $NS;
}

function getLostedStages(){
	$NS = selectAllFrom("crm_sale-stages","success_ratio = 0","id_stage, visible");
	return $NS;
}

function getWonStages(){
	$NS = selectAllFrom("crm_sale-stages","success_ratio = 100","id_stage, visible");
	return $NS;
}

function getRegistrationStages(){
	$NS = selectAllFrom("crm_sale-stages","success_ratio BETWEEN 85 AND 99","id_stage, visible");
	return $NS;
}

function getTeachers($all = false){
	$f = "id_teacher, CONCAT(first_name,' ',last_name) as full_name";
	return getCatalogData("crm_teachers",$all,$f);
}

function getSingleCatalogData($catalog, $idkey){
	$catalog = strFixAndTrim($catalog);
	if ((numericOnly($idkey)) && (strlen($catalog) > 4) && ($idkey*1 > 0)){
		$keyfield = DBSchema::getKeyfield($catalog);
		return selectAllFrom($catalog,"{$keyfield} = {$idkey}");
	}
	return 0;
}

function crmFilterOpsByUser($d,$user){
	$v = "NV";
	$a = "NV";
	$n = "";
	$l = "NV";
	$c = "NV";
	if (is_array($d)){
		$v = $d[0]; $a = $d[1];
		$n = strFixAndTrim($d[2]); $l = $d[3];
		$c = $d[4];
	}
	if (numericOnly($v.$a.$l.$c)){
		if ($v*1 > 0){
			$NS = getMyOpportunities($v, false, $a, $n, $l, $c);
		}else{
			$NS = getMyOpportunities($v, true, $a, $n, $l, $c);
		}
	}else{
		$NS = getMyOpportunities($v, true, false, $n, 0, 0);
	}
	return getProspectsViewList($NS);
	
}

function getIsClosedStage($ids){
	$isClosed = false;
	if (numericOnly($ids)){
		$NSSR = getSingleCatalogData("crm_sale-stages",$ids);
		if (is_array($NSSR)){
			$sr = ($NSSR[0]['success_ratio']*1);
			if (($sr == 100) || ($sr == 0)){
				$isClosed = true;
			}
		}
	}
	return $isClosed;
}

function crmSaveProspect($data, $user){
	$ret = "0x02NSDERR";
	if (is_array($data)){
		$ret = "0x02NUMERR";
		$agrid = (strcmp($data[11],"NV") === 0) ? 0 : $data[11];
		$classid = (strcmp($data[12],"NV") === 0) ? 0 : $data[12];
		if (numericOnly($data[6].$data[5].$data[7].$data[8].$data[10].$agrid.$classid)){
			$fupusr = $user;
			if ((isAdmin($user)) || (isManager($user))){
				if ($data[10]*1 > 0){$fupusr = $data[10];}
			}
			$ret = "0x02INSERR";
			$NSData = array(
				"id_prospect" => 0,
				"first_name" => $data[0],
				"last_name" => $data[1],
				"phone_number" => preg_replace('/\s/',"",$data[3]),
				"mobile_number" => $data[4],
				"email" => $data[2],
				"referrer_id" => $data[6],
				"reason_id" => $data[5],
				"assigned_to" => $fupusr,
				"agreement_id" => $agrid,
				"initial_level" => $classid,
			);
			$pid = insertDataInto("crm_prospects",$NSData,true);
			if ($pid > 0){
				$comnts = strFixAndTrim($data[13]);
				$extrad = ($data[7]*1 == 6) ? "E=1" : "";
				$ret = "0x02FUPERR";
				$NSData = array(
					"id_followup" => 0,
					"id_prospect" => $pid,
					"record_date" => "NOW()",
					"sale_stg_id" => $data[7],
					"next_stg_id" => $data[8],
					"followup_date" => $data[9],
					"user_id" => $fupusr,
					"is_current" => 1,
					"comments" => "{$comnts}",
					"is_closed" => 0,
					"extra_data" => $extrad,
				);
				if (insertDataInto("crm_followup",$NSData) > 0){
					if ((isDemoClassStage($data[7])) || (isDemoClassStage($data[8]))){
						crmAddToDemoClass($pid,$data[9],$classid);
					}
					$ret = "OK";
				}
			}
		}
	}
	return $ret;
}

function getProspectDetails($idp,$idf){
	$ret = "0x09NUMERR";
	if ((numericOnly($idp)) && (numericOnly($idf))){
		$ret = "0x09PROERR";
		if ($idp > 0){
			if ($idf == 0){
				$ret = selectAllFrom("crm_prospects","id_prospect = {$idp}");
			}else{
				$ret = getDBProspectDetails($idp,$idf);
			}
		}
	}
	return $ret;
}

function crmUpdateFollowUp($data, $user){
	$ret = "0x0ANSDERR";
	if (is_array($data)){
		$ret = "0x0ANUMERR";
		if (numericOnly($data[0].$data[1].$data[2].$data[5].$data[6].$data[7])){
			$fupusr = $user;
			if (hasTopAccess($user)){
				if ($data[5]*1 > 0){$fupusr = $data[5];}
			}
			$data[4] = strFixAndTrim($data[4]);
			$ret = "0x02INSERR";
			$stg = ($data[1]*1 == 0) ? $data[2] : getWorkedSaleStage($data[1]);
			$closed = getIsClosedStage($data[2]) ? 1 : 0;
			$NSData = array(
				"id_followup" => 0,
				"id_prospect" => $data[0],
				"record_date" => "NOW()",
				"sale_stg_id" => $stg,
				"next_stg_id" => $data[2],
				"followup_date" => $data[3],
				"user_id" => $fupusr,
				"is_current" => 1,
				"comments" => "{$data[4]}",
				"is_closed" => $closed,
				"extra_data" => "",
			);
			$x = insertDataInto("crm_followup",$NSData);
			if ($data[1]*1 > 0){
				if ($data[7]*1 > 0){
					$NSData = array(
						"is_current" => 0,
						"extra_data" => "E=".$data[7],
					);
				}else{
					$NSData = array(
						"is_current" => 0,
					);
				}
				updateDataFrom("crm_followup",$NSData,"id_followup = ".$data[1]);
			}
			if ($fupusr != $user){
				$NSData = array(
					"assigned_to" => $fupusr
				);
				updateDataFrom("crm_prospects",$NSData,"id_prospect = ".$data[0]);
			}
			if (isDemoClassStage($data[2])){
				crmAddToDemoClass($data[0],$data[3],$data[6]);
			}
			if ($x > 0){
				$ret = "OK";
			}
			
		}
	}
	return $ret;
}

function crmAddToDemoClass($pid = 0, $classdate = "", $classid = 0){
	$ret = true;
	if (numericOnly($pid.$classid)){
		$NSData = array(
			"id_record" => 0,
			"id_prospect" => $pid,
			"class_date" => $classdate,
			"id_classroom" => 0,
			"id_level" => $classid,
			"id_teacher" => 0,
			"show_up" => 0,
		);
		$ret = (insertDataInto("crm_demo-class",$NSData) > 0);
	}
	return $ret;
}

function crmUpdateDemoClass($data){
	$ret = "0xNUMERR";
	$idclass = $data[4];
	$data[0] = (strcmp($data[0],"NV") === 0) ? "0" : $data[0];
	$data[1] = (strcmp($data[1],"NV") === 0) ? "0" : $data[1];
	$data[2] = (strcmp($data[2],"NV") === 0) ? "0" : $data[2];
	$data[3] = (strcmp($data[3],"NV") === 0) ? "0" : $data[3];
	
	$numstr = $data[0].$data[1].$data[2].$data[3];
	if (numericOnly($idclass.$numstr)){
		$aret = array("0","Error no definido");
		$NSData = array(
			"id_classroom" => $data[1],
			"id_level" => $data[0],
			"id_teacher" => $data[2],
			"show_up" => $data[3],
		);
		if (updateDataFrom("crm_demo-class",$NSData,"id_record = ".$idclass) > 0){
			$aret[0] = "1";
		}else{
			$aret[1] = "Update Class data failed. Try again later or contact support";
		}
		$ret = json_encode($aret);
	}
	
	return $ret;
}

function crmSaveUser($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x03NSDERR";
		if (is_array($data)){
			$uid = (numericOnly($data[3])) ? $data[3] : 0;
			if ($uid > 0){
				$NSData = array("user_name" => $data[0],"user_type" => $data[2]);
				if (strlen($data[1]) > 3){
					$NSData["user_key"] = $data[1];
				}
				$ret = (updateDataFrom("crm_users",$NSData,"user_id = {$uid}") > 0) ? "OK" : "0x03UPDERR";
			}else{
				$NSData = array(
					"user_id" => $uid,
					"user_name" => $data[0],
					"user_key" => $data[1],
					"last_login" => "2000-01-01 00:00:00",
					"enabled" => "1",
					"user_type" => $data[2],
				);
				$ret = (insertDataInto("crm_users",$NSData) > 0) ? "OK" : "0x03INSERR";
			}
		}
	}
	return $ret;
}

function crmEditCatalog($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x04NSDERR";
		if (is_array($data)){
			$ret = "0x04NUMERR";
			if (numericOnly($data[0].$data[1])){
				$ret = crmBuildCatalogEditView($data[0],$data[1]);
			}
		}
	}
	return $ret;
}

function crmDeleteFromCatalog($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x04NSDERR";
		if (is_array($data)){
			$ret = "0x04NUMERR";
			if (numericOnly($data[0].$data[1])){
				$table = DBSchema::getTableNameByID($data[0]);
				$idkey = $data[1];
				$NSData = array(DBSchema::getKeyfield($table) => $idkey);
				$ret = (deleteDataFrom($table,$NSData) > 0) ? crmBuildSettingsCatalog($data[0]*1) : "0x0CDELERR";
			}
		}
	}
	return $ret;
}

function crmSaveCatalog($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x04NSDERR";
		if (is_array($data)){
			$ret = "0x04NUMERR";
			if (numericOnly($data[0].$data[1])){
				$table = "NT";
				$idkey = $data[1];
				$NSData = array("");
				if ($data[0]*1 == 1){
					$table = "crm_referrers";
					$NSData = array("referrer_id" => $idkey, "description" => $data[2], "visible" => $data[4]);
				}
				if ($data[0]*1 == 2){
					$table = "crm_sale-stages";
					$NSData = array("id_stage" => $idkey, "description" => $data[2], "success_ratio" => $data[3], "visible" => $data[4]);
				}
				if ($data[0]*1 == 3){
					$table = "crm_reasons";
					$NSData = array("id_reason" => $idkey, "description" => $data[2], "visible" => $data[4]);
				}
				if ($data[0]*1 == 4){
					$table = "crm_teachers";
					$NSData = array("id_teacher" => $idkey, "first_name" => $data[2], "last_name" => $data[3], "visible" => $data[4]);
				}
				if ($data[0]*1 == 5){
					$table = "crm_classroom";
					$NSData = array("id_classroom" => $idkey, "description" => $data[2], "visible" => $data[4]);
				}
				if ($data[0]*1 == 6){
					$table = "crm_class-level";
					$NSData = array("id_level" => $idkey, "description" => $data[2], "visible" => $data[4]);
				}
				if ($data[0]*1 == 7){
					$table = "crm_agreements";
					$NSData = array("id_agreement" => $idkey, "company_name" => $data[2], "agreement" => $data[3], "visible" => $data[4]);
				}
				if ($idkey*1 > 0){
					$ret = (insertDataInto($table,$NSData,false,true) > 0) ? crmBuildSettingsCatalog($data[0]*1) : "0x04INSERR";
				}else{
					$ret = (insertDataInto($table,$NSData) > 0) ? crmBuildSettingsCatalog($data[0]*1) : "0x04INSERR";
				}
			}
		}
	}
	return $ret;
}

function crmToggleVisible($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x05NSDERR";
		if (is_array($data)){
			$ret = "0x05NUMERR";
			if ((numericOnly($data[0])) && (numericOnly($data[1])) && (numericOnly($data[2]))){
				$table = DBSchema::getTableNameByID($data[0]);
				$idkey = $data[1];
				$whfield = DBSchema::getKeyfield($table)." = {$idkey}";
				$NSData = array(
					"visible" => $data[2]
				);
				switch($data[0]*1){
					case 8:
						$NSData = array(
							"enabled" => $data[2]
						);
						break;
				}
				$ret = "0x05UPDERR";
				if (updateDataFrom($table,$NSData,$whfield) > 0){
					$ret = crmBuildSettingsCatalog($data[0]*1);
				}
			}
		}
	}
	return $ret;
}

function crmSaveSchedule($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x06NSDERR";
		if (is_array($data)){
			$ret = "0x05NUMERR";
			$allNum = true;
			for($i=0; $i<count($data); $i++){
				if (!numericOnly($data[$i])){
					$allNum = false;
				}
			}
			if ($allNum){
				$aret = array("0","Error no definido");
				$st = sprintf("%02d",$data[4]).":";
				$st .= sprintf("%02d",$data[5]).":00";
				$et = sprintf("%02d",$data[6]).":";
				$et .= sprintf("%02d",$data[7]).":00";
				if ((strtotime($et) - strtotime($st)) > 0){
					$NSCurrent = getScheduleByTimeAndDay($data[0],$st,$et);
					$noConflict = true;
					$ctype = "";
					if (is_array($NSCurrent)){
						for ($i=0; $i<count($NSCurrent); $i++){
							if (($NSCurrent[$i]['id_teacher']*1) == ($data[3]*1)){
								$ctype = "teacher";
								$noConflict = false;
								break;
							}
							if (($NSCurrent[$i]['id_classroom']*1) == ($data[2]*1)){
								$ctype = "classroom";
								$noConflict = false;
								break;
							}
						}
					}
					if ($noConflict){
						$NSData = array(
							"id_schedule" => 0,
							"id_day" => $data[0],
							"start_time" => "2035-01-01 ".$st,
							"end_time" => "2035-01-01 ".$et,
							"id_teacher" => $data[3],
							"id_classroom" => $data[2],
							"id_level" => $data[1],
							"available" => 1,
						);
						if (insertDataInto("crm_class-schedule",$NSData) > 0){
							$aret[0] = "1";
							$aret[1] = "OK";
						}else{
							$aret[1] = "Error while adding the class schedule. Try again later";
						}
					}else{
						$aret[1] = "Conflict. Selected {$ctype} is not available for the selected day and time";
					}
				}else{
					$aret[1] = "Data Error: Start Time is set after End Time.";
				}
				$ret = json_encode($aret);
			}
		}
	}
	return $ret;
}

function crmFilterSchedule($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x06NSDERR";
		if (is_array($data)){
			$ret = "0x05NUMERR";
			$dow = (strcasecmp($data[0],"NV") === 0) ? 0 : $data[0];
			$lvl = (strcasecmp($data[1],"NV") === 0) ? 0 : $data[1];
			$cro = (strcasecmp($data[2],"NV") === 0) ? 0 : $data[2];
			$tch = (strcasecmp($data[3],"NV") === 0) ? 0 : $data[3];
			if ((numericOnly($dow)) && (numericOnly($lvl)) && (numericOnly($cro)) && (numericOnly($tch))){
				if (($dow+$lvl+$cro+$tch) === 0){
					$dow = date("w");
					if ($dow == 0){ $dow = 7; }
				}
				$ret = getScheduleByOptions($dow,$lvl,$cro,$tch);
			}
		}
	}
	return $ret;
}

function crmFilterDemoClass($data, $user){
	if (hasTopAccess($user)){$user = 0;}
	$ret = "0x06NSDERR";
	if (is_array($data)){
		$ret = "0x05NUMERR";
		$lvl = (strcasecmp($data[0],"NV") === 0) ? 0 : $data[0];
		$cro = (strcasecmp($data[1],"NV") === 0) ? 0 : $data[1];
		$tch = (strcasecmp($data[2],"NV") === 0) ? 0 : $data[2];
		$all = (strcasecmp($data[3],"NV") === 0) ? 1 : $data[3];
		if ((numericOnly($lvl)) && (numericOnly($cro)) && (numericOnly($tch)) && (numericOnly($all))){
			$ret = getTodayDemoClass($user,$lvl,$cro,$tch,$all);
		}
	}
	return $ret;
}

function crmFilterDemoClassAttendance($data, $user){
	$ret = "0x16NSDERR";
	if (is_array($data)){
		$ret = crmBuildDemoAttendanceReport(getDBDemoClassAttendanceList($data[0],$data[1]));
	}
	return $ret;
}

function crmToggleSchedule($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x05NSDERR";
		if (is_array($data)){
			$ret = "0x05NUMERR";
			if ((numericOnly($data[0])) && (numericOnly($data[1]))){
				$NSData = array(
					"available" => $data[1]
				);
				$table = "crm_class-schedule";
				$whfield = "id_schedule = ".$data[0];
				$ret = "0x08UPDERR";
				if (updateDataFrom($table,$NSData,$whfield) > 0){
					$ret = "OK";
				}
			}
		}
	}
	return $ret;
}

function crmUpdateMyProfileData($data, $user){
	$ret = "0x05NSDERR";
	if (is_array($data)){
		$ret = "0x0ENUMERR";
		if (numericOnly($data[2].$user)){
			$ret = "0x0DUSRERR";
			if (strcmp($data[2],$user) === 0){
				$ret = "0x0DPMMERR";
				if (strcmp(strFixAndTrim($data[0]),strFixAndTrim($data[1])) === 0){
					$ret = "0x0DLENERR";
					if (strlen(strFixAndTrim($data[0])) > 1){
						$NSData = array("user_key" => $data[0]);
						$ret = (updateDataFrom("crm_users",$NSData,"user_id = {$user}") > 0) ? "OK" : "0x0DUPDERR";
					}
				}
				
			}
		}
	}
	return $ret;
}

function crmUpdateProspect($data, $user){
	$ret = "0x11NSDERR";
	if (is_array($data)){
		$ret = "0x11NUMERR";
		$agrid = (strcmp($data[7],"NV") === 0) ? 0 : $data[7];
		$classid = (strcmp($data[9],"NV") === 0) ? 0 : $data[9];
		if (numericOnly($data[6].$data[5].$data[8].$data[9].$agrid.$classid)){
			$fupusr = $user;
			$idp = $data[10];
			if ((isAdmin($user)) || (isManager($user))){
				if ($data[8]*1 > 0){$fupusr = $data[8];}
			}
			$NSData = array(
					"first_name" => $data[0],
					"last_name" => $data[1],
					"phone_number" => preg_replace('/\s/',"",$data[3]),
					"mobile_number" => $data[4],
					"email" => $data[2],
					"referrer_id" => $data[6],
					"reason_id" => $data[5],
					"assigned_to" => $fupusr,
					"agreement_id" => $agrid,
					"initial_level" => $classid,
			);
			$ret = (updateDataFrom("crm_prospects",$NSData,"id_prospect = {$idp}") > 0) ? "OK" : "0x11UPDERR";
		}
	}
	return $ret;
}


function crmDeleteProspect($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x05NSDERR";
		if (is_array($data)){
			$ret = "0x05NUMERR";
			if (numericOnly($data[0])){
				$idp = $data[0];
				$NSP = selectAllFrom("crm_prospects","id_prospect = {$idp}");
				$audittxt = "";
				if (is_array($NSP)){
					for($i=0; $i<10; $i++)
						$audittxt .= $NSP[0][$i]."|";
				}
				$NSData = array(
					"id_audit" => 0,
					"id_prospect" => $idp,
					"id_user" => $user,
					"audit_date" => "NOW()",
					"audit_data" => $audittxt,
				);
				insertDataInto("crm_audit",$NSData);
				deactivateDBProspect($idp);
				$NSData = array(DBSchema::getKeyfield("crm_prospects") => $idp);
				$ret = (deleteDataFrom("crm_prospects",$NSData) > 0) ? crmGetAllProspectsReport($user) : "0x10DELERR";
			}
		}
	}
	return $ret;
}

function crmActivateProspect($data, $user){
	$ret = "0x03ADMERR";
	if (isAdmin($user)){
		$ret = "0x05NSDERR";
		if (is_array($data)){
			$ret = "0x05NUMERR";
			if (numericOnly($data[0])){
				$idp = $data[0];
				$ret = activateDBProspect($idp) ? crmGetAllProspectsReport($user) : "0x14UPDERR";
			}
		}
	}
	return $ret;
}

function crmFilterProspectRBy($data,$user){
	$dat1 = strFixAndTrim($data[0]);
	$dat2 = strFixAndTrim($data[1]);
	$dat3 = strFixAndTrim($data[2]);
	$id = strFixAndTrim($data[3]);
	if (strcasecmp($dat3, "NV") === 0){$dat3 = 0;}
	if (!numericOnly($dat2)){$dat2 = 0;}
	if (!numericOnly($dat3)){$dat3 = 0;}
	switch ($id){
		case 1: return crmBuildProspectsReport(crmGetLostedProspectsReport($user, $dat1, $dat3,true));
		case 2: return crmBuildProspectsReport(crmGetWonProspectsReport($user, $dat1, $dat3,true));
		default: return crmBuildAllProspectsReport(getDBAllProspects($dat1,$dat2,$dat3),$user);
	}
}

function crmGetProspectComments($idp){
	$ret = "0x02NUMERR";
	if (numericOnly($idp)){
		$ret = getDBProspectComments($idp);
	}
	return $ret;
}

/*
 * ALTER TABLE `crm_followup` ADD COLUMN `extra_data` VARCHAR(50) AFTER `is_closed`;
 */

?>