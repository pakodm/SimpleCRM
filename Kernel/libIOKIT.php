<?php
	include_once "ihconfig.php";
	require_once "libDB.php";
	require_once "libSCHEMA.php";
	
	$db = new database($db_server,$db_user,$db_password,$db_name);
	
	function selectAllFrom($table = "", $where = "", $fields = "*"){
		$fields = (strlen($fields) === 0) ? "*" : $fields;
		if (strlen($table) > 3){
			global $db;
			$MyQ = "SELECT {$fields} FROM `{$table}` ";
			if (strlen($where) > 3){
				$MyQ .= "WHERE {$where} ";
			}
			$MyQ .= "ORDER BY 1";
			$NSData = $db->DoQuery($MyQ);
			if ($db->is_error){
				error_log($MyQ,0);
			}
			return $NSData;
		}else{
			return 0;
		}
	}
	
	function deleteDataFrom($table = "", $where = ""){
		$result = 0;
		if ((strlen($table) > 3) && (is_array($where))){
			global $db;
			$MyQry = "DELETE FROM `{$table}` WHERE ";
			foreach ($where as $field => $value){
				$MyQry .= "{$field} = ";
				$Type = DBSchema::getDataType($table,$field);
				$quote = "";
				if ($Type > 1){$quote = "'";}
				$sanval = filter_var(strFixAndTrim($value),FILTER_SANITIZE_STRING);
				if (($Type == 3) && (strcmp($sanval,"NOW()") === 0)){$quote = "";}
				if ($Type == 5){$quote = ""; $sanval = "SHA1('{$sanval}')";}
				$MyQry .= $quote.$sanval.$quote.",";
			}
			$MyQ = trim($MyQry,",");
			$result = $db->ExecCmd($MyQ);
			if ($db->is_error){
				error_log($MyQ,0);
			}
		}
		return $result;
	}
	
	function updateDataFrom($table = "", $data = "", $where = ""){
		$result = 0;
		if ((strlen($table) > 3) && (is_array($data))){
			global $db;
			$MyQry = "UPDATE `{$table}` SET ";
			foreach ($data as $field => $value){
				$MyQry .= "{$field} = ";
				$Type = DBSchema::getDataType($table,$field);
				$quote = "";
				if ($Type > 1){$quote = "'";}
				$sanval = filter_var(strFixAndTrim($value),FILTER_SANITIZE_STRING);
				if (($Type == 3) && (strcmp($sanval,"NOW()") === 0)){$quote = "";}
				if ($Type == 5){$quote = ""; $sanval = "SHA1('{$sanval}')";}
				$MyQry .= $quote.$sanval.$quote.",";
			}
			$MyQ = trim($MyQry,",");
			if (strlen($where) > 3){
				/* TODO: Maybe "where" statements should also be a key=>value structure */
				$MyQ .= " WHERE {$where}";
			}
			$result = $db->ExecCmd($MyQ);
			if ($db->is_error){
				error_log($MyQ,0);
			}
		}
		return $result;
	}
	
	/**
	 * $data is an array key => value structure.
	 * keys represent table fields
	 * values represent data to be inserted, can be an array for multiple inserts.
	 */
	function insertDataInto($table = "", $data = "", $retInsId = false, $useReplace = false){
		$result = 0;
		if ((strlen($table) > 3) && (is_array($data))){
			global $db;
			$nsvalues = array("");
			if ($useReplace){
				$MyQry = "REPLACE INTO `{$table}` (";
			}else{
				$MyQry = "INSERT INTO `{$table}` (";
			}
			foreach ($data as $field => $value){
				$MyQry .= "{$field},";
				$Type = DBSchema::getDataType($table,$field);
				$quote = "";
				if ($Type > 1){$quote = "'";}
				if (is_array($value)){
					if (count($nsvalues) == 1){$nsvalues = array_pad($nsvalues,count($value),"");}
					for($i=0; $i<count($value); $i++){
						$sanval = filter_var(strFixAndTrim($value[$i]),FILTER_SANITIZE_STRING);
						if (($Type == 3) && (strcmp($sanval,"NOW()") === 0)){$quote = "";}
						if ($Type == 5){$quote = ""; $sanval = "SHA1('{$sanval}')";}
						$nsvalues[$i] .= $quote.$sanval.$quote.",";
					}
				}else{
					$sanval = filter_var(strFixAndTrim($value),FILTER_SANITIZE_STRING);
					if (($Type == 3) && (strcmp($sanval,"NOW()") === 0)){$quote = "";}
					if ($Type == 5){$quote = ""; $sanval = "SHA1('{$sanval}')";}
					$nsvalues[0] .= $quote.$sanval.$quote.",";
				}
			}
			$MyQry .= ")";
			$MyQ = preg_replace('/,\)/',") VALUES ",$MyQry);
			for($i=0; $i<count($nsvalues)-1; $i++){
				$MyQ .= "(".trim($nsvalues[$i],",")."),";
			}
			$MyQ .= "(".trim($nsvalues[count($nsvalues)-1],",").");";
			
			if ($retInsId){
				$result = $db->ExecCmdId($MyQ);
			}else{
				$result = $db->ExecCmd($MyQ);
			}
			if ($db->is_error){
				error_log($MyQ,0);
			}
		}
		return $result;
	}
	
	function getDBGS1($f1 = "", $f2 = ""){
		$result = 0;
		global $db;
		if (strlen($f1) < 10){$f1 = getCurrentDateForQuery(0,1);}
		if (strlen($f2) < 10){$f2 = getCurrentDateForQuery(1);}
		$MyQ = "SELECT count(*) FROM crm_followup where is_current = 1 ";
		$MyQ .= "AND record_date BETWEEN '{$f1}' AND '{$f2}'";
		$result = $db->GetSingleData($MyQ);
		return $result;
	}
	
	function getDBTasksForDates($f1 = "", $f2 = "", $usr = 0, $retro = true){
		$result = 0;
		if ((strlen($f1) > 4) && (strlen($f2) > 4)){
			global $db;
			$MyQ = "SELECT count(id_followup) FROM `crm_followup` WHERE is_closed = 0 AND is_current = 1 ";
			if (!$retro){
				$MyQ .= " AND followup_date BETWEEN '{$f1}' AND '{$f2}' ";
			}else{
				$MyQ .= " AND followup_date < '{$f2}' ";
			}
			if ($usr > 0){
				$MyQ .= "AND user_id = {$usr} ";
			}
			$result = $db->GetSingleData($MyQ);
		}
		return $result;
	}
	
	function getDBDemoForDates($f1 = "", $f2 = "", $usr = 0, $retro = true){
		$result = 0;
		if ((strlen($f1) > 4) && (strlen($f2) > 4)){
			global $db;
			$MyQ = "SELECT count(d.id_record) FROM `crm_demo-class` d, crm_prospects p WHERE d.show_up = 0 ";
			if (!$retro){
				$MyQ .= "AND d.class_date BETWEEN '{$f1}' AND '{$f2}' ";
			}else{
				$MyQ .= "AND d.class_date < '{$f2}' ";
			}
			if ($usr > 0){
				$MyQ .= "AND p.assigned_to = {$usr} "; 
			}
			$MyQ .= "AND d.id_prospect = p.id_prospect";
			$result = $db->GetSingleData($MyQ);
		}
		return $result;
	}
	
	function getMyOpportunities($user, $all = false, $dater = false, $fname = "", $levl = 0, $agree = 0){
		if (numericOnly($user)){
			global $db;
			$MyQ = "SELECT p.id_prospect, CONCAT(p.first_name,' ',p.last_name) as full_name, ";
			$MyQ .= "p.phone_number, p.email, IFNULL(f.id_followup,0) as id_fup, ";
			$MyQ .= "IFNULL(f.followup_date, '2000-01-01 00:00:00') as followup_date, ";
			$MyQ .= "IFNULL(f.sale_stg_id,0) as current_stg, IFNULL(f.next_stg_id,0) as next_stg ";
			$MyQ .= "FROM `crm_prospects` p LEFT JOIN `crm_followup` f ON f.id_prospect = p.id_prospect ";
			$MyQ .= "WHERE ";
			if (!$all){
				//$MyQ .= "p.assigned_to = {$user} AND ";
				$MyQ .= "f.user_id = {$user} AND ";
			}
			$MyQ .= "(is_current = 1 OR ISNULL(is_current)) AND is_closed = 0 ";
			if ($dater){
				$MyQ .= "AND followup_date <= ADDDATE(NOW(),1) ";
			}
			if (strlen($fname) > 0){
				$fname = strFixAndTrim($fname);
				$MyQ .= "AND (p.first_name LIKE '%{$fname}%' OR p.last_name LIKE '%{$fname}%') ";
			}
			if ($levl > 0){
				$MyQ .= "AND p.initial_level = {$levl} ";
			}
			if ($agree > 0){
				$MyQ .= "AND p.agreement_id = {$agree} ";
			}
			$MyQ .= "GROUP BY f.id_prospect ";
			$MyQ .= "ORDER BY 6 ASC ";
			return $db->DoQuery($MyQ);
		}
		return 0;
	}
	
	function getDBProspectDetails($idp, $idf){
		if (numericOnly($idf)){
			global $db;
			$MyQ = "SELECT p.*, f.record_date, sale_stg_id, next_stg_id, followup_date, comments ";
			$MyQ .= "FROM crm_prospects p INNER JOIN crm_followup f ON f.id_prospect = p.id_prospect ";
			$MyQ .= "WHERE f.id_followup = {$idf}";
			return $db->DoQuery($MyQ);
		}
		return 0;
	}
	
	function getWorkedSaleStage($idf){
		if (numericOnly($idf)){
			global $db;
			$MyQ = "SELECT next_stg_id FROM `crm_followup` WHERE id_followup = {$idf}";
			return $db->GetSingleData($MyQ);
		}
		return 0;
	}
	
	function getDemoClassStage(){
		global $db;
		$MyQ = "SELECT id_stage FROM `crm_sale-stages` where description like '%demo%' or description like '%class%'";
		return $db->DoQuery($MyQ);
	}
	
	function isDemoClassStage($stgid){
		$ret = false;
		if (numericOnly($stgid)){
			global $db;
			$MyQ = "SELECT * FROM `crm_sale-stages` where id_stage = {$stgid} AND (description like '%demo%' or description like '%class%')";
			$NS = $db->DoQuery($MyQ);
			$ret = (is_array($NS));
		}
		
		return $ret;
	}
	
	function getPlacementExamStage(){
		global $db;
		$MyQ = "SELECT id_stage FROM `crm_sale-stages` where ";
		$MyQ .= "description like '%exam%' or description like '%placement%' or description like '%test%'";
		return $db->DoQuery($MyQ);
	}
	
	function isPlacementExamStage($stgid){
		$ret = false;
		if (numericOnly($stgid)){
			global $db;
			$MyQ = "SELECT * FROM `crm_sale-stages` where id_stage = {$stgid} AND ";
			$MyQ .= "(description like '%test%' or description like '%placement%' or description like '%exam%')";
			$NS = $db->DoQuery($MyQ);
			$ret = (is_array($NS));
		}
	
		return $ret;
	}
	
	function getTodayDemoClass($usr = 0, $n = 0, $c = 0, $t = 0, $a = 1){
		if (numericOnly($usr.$n.$c.$t)){
			$f2 = getDatePlusDays(1,1); //getCurrentDateForQuery(1);
			global $db;
			$MyQ = "SELECT d.*, p.first_name, p.last_name, p.assigned_to ";
			$MyQ .= "FROM `crm_demo-class` d INNER JOIN `crm_prospects` p ON p.id_prospect = d.id_prospect ";
			$MyQ .= "WHERE d.show_up = 0 ";
			if ($a > 0){
				$MyQ .= "AND d.class_date < '{$f2}' ";
			}
			if ($usr > 0){
				$MyQ .= "AND p.assigned_to = {$usr} ";
			}
			if ($n > 0){ $MyQ .= "AND d.id_level = {$n} "; }
			if ($c > 0){ $MyQ .= "AND d.id_classroom = {$c} "; }
			if ($t > 0){ $MyQ .= "AND d.id_teacher = {$t} "; }
			$MyQ .= "ORDER BY d.class_date";
			return $db->DoQuery($MyQ);
		}
		return 0;
	}
	
	function getScheduleByTimeAndDay($day = 0, $start = "00:00:00", $end = "00:00:00"){
		if (numericOnly($day)){
			global $db;
			if ((strtotime($end) - strtotime($start)) > 0){
				$fqet = date("H:i:s",(strtotime($end)-1));
				$sqst = date("H:i:s",(strtotime($start)+1));
				/*
				$MyQ = "SELECT id_schedule, id_teacher, id_classroom, id_level ";
				$MyQ .= "FROM `crm_class-schedule` WHERE id_day = {$day} AND ";
				$MyQ .= "(start_time BETWEEN '2035-01-01 {$start}' AND '2035-01-01 {$fqet}' OR ";
				$MyQ .= "end_time BETWEEN '2035-01-01 {$sqst}' AND '2035-01-01 {$end}') ";
				$MyQ .= "AND available = 1 ";
				$MyQ .= "ORDER BY id_schedule";
				*/
				$MyQ = "SELECT id_schedule, id_teacher, id_classroom, id_level FROM `crm_class-schedule` ";
				$MyQ .= "WHERE id_day = {$day} AND ";
				$MyQ .= "((start_time <= '2035-01-01 {$start}' AND end_time >= '2035-01-01 {$end}') ";
				$MyQ .= "OR (start_time BETWEEN '2035-01-01 {$start}' AND '2035-01-01 {$fqet}') ";
				$MyQ .= "OR (end_time BETWEEN '2035-01-01 {$sqst}' AND '2035-01-01 {$end}')) ";
				$MyQ .= "AND available = 1 ORDER BY id_schedule";
				return $db->DoQuery($MyQ);
			}
		}
		return 0;
	}
	
	function getScheduleByOptions($day = 0, $level = 0, $classroom = 0, $teacher = 0){
		if ((numericOnly($day)) && (numericOnly($level)) && (numericOnly($classroom)) && (numericOnly($teacher))){
			global $db;
			$MyQ = "SELECT s.id_schedule, s.start_time, s.end_time, s.id_day, l.description as ldesc, c.description as cdesc, ";
			$MyQ .= "CONCAT(t.first_name,' ',t.last_name) as fname, s.available ";
			$MyQ .= "FROM `crm_class-schedule` s INNER JOIN `crm_class-level` l ON l.id_level = s.id_level ";
			$MyQ .= "INNER JOIN `crm_classroom` c ON c.id_classroom = s.id_classroom ";
			$MyQ .= "INNER JOIN `crm_teachers` t ON t.id_teacher = s.id_teacher WHERE ";
			$whst = "";
			if ($day > 0){
				$whst .= "s.id_day = {$day} ";
			}
			if ($level > 0){
				if (strlen($whst) > 1){ $whst .= "AND ";}
				$whst .= "s.id_level = {$level} ";
			}
			if ($classroom > 0){
				if (strlen($whst) > 1){ $whst .= "AND ";}
				$whst .= "s.id_classroom = {$classroom} ";
			}
			if ($teacher > 0){
				if (strlen($whst) > 1){ $whst .= "AND ";}
				$whst .= "s.id_teacher = {$teacher} ";
			}
			$MyQ .= $whst;
			$MyQ .= "ORDER BY s.id_day, start_time, s.id_level, l.description, fname";
			return $db->DoQuery($MyQ);
		}
		return 0;
	}
	
	function getDBAllProspectOverview($nxtstg = ""){
		global $db;
		$MyQ = "SELECT p.id_prospect, p.first_name, p.last_name, p.email, p.phone_number, r.description as referrer, z.description as reason, ";
		$MyQ .= "s.description as stage, f.next_stg_id, f.record_date, f.is_closed ";
		$MyQ .= "FROM crm_prospects p, crm_referrers r, crm_reasons z, crm_followup f, `crm_sale-stages` s ";
		$MyQ .= "WHERE p.referrer_id = r.referrer_id AND p.reason_id = z.id_reason AND p.id_prospect = f.id_prospect ";
		$MyQ .= "AND f.is_current = 1 AND f.next_stg_id = s.id_stage ";
		if (strlen($nxtstg) > 0){
			$MyQ .= "AND f.next_stg_id IN ({$nxtstg}) ";
		}
		$MyQ .= "ORDER BY f.is_closed ASC, p.last_name, p.first_name";
		return $db->DoQuery($MyQ);
	}
	
	function getDBAllProspects($pname = "", $active = 0, $lvl = 0){
		global $db;
		$MyQ = "SELECT p.*, f.is_closed FROM crm_prospects p, crm_followup f ";
		$MyQ .= "WHERE p.id_prospect = f.id_prospect AND f.is_current = 1 ";
		if (strlen($pname) > 0){
			$MyQ .= "AND (p.first_name LIKE '%{$pname}%' OR p.last_name LIKE '%{$pname}%') ";
		}
		if ($active > 0){
			if ($active*1 == 1){$MyQ .= "AND f.is_closed = 0 ";}
			if ($active*1 == 2){$MyQ .= "AND f.is_closed = 1 ";}
		}
		if ($lvl > 0){
			$MyQ .= "AND p.initial_level = {$lvl} ";
		}
		$MyQ .= "ORDER BY f.is_closed ASC, p.last_name, p.first_name LIMIT 500";
		return $db->DoQuery($MyQ);
	}
	
	function getDBProspectsForXLS($pname = "", $active = 0, $lvl = 0){
		global $db;
		$MyQ = "SELECT p.first_name, p.last_name, p.phone_number, p.mobile_number, p.email, a.company_name, ";
		$MyQ .= "l.description as 'clevel', s.description as 'sdesc', f.is_closed ";
		$MyQ .= "FROM crm_prospects p INNER JOIN crm_agreements a ON p.agreement_id = a.id_agreement ";
		$MyQ .= "INNER JOIN `crm_class-level` l ON p.initial_level = l.id_level ";
		$MyQ .= "INNER JOIN crm_followup f ON (p.id_prospect = f.id_prospect AND f.is_current = 1) ";
		$MyQ .= "INNER JOIN `crm_sale-stages` s ON f.next_stg_id = s.id_stage ";
		$Myf = "";
		if (strlen($pname) > 0){
			$pname = strFixAndTrim($pname);
			$Myf .= "AND (p.first_name LIKE '%{$pname}%' OR p.last_name LIKE '%{$pname}%') ";
		}
		if ($active > 0){
			if ($active*1 == 1){$Myf .= "AND f.is_closed = 0 ";}
			if ($active*1 == 2){$Myf .= "AND f.is_closed = 1 ";}
		}
		if (numericOnly($lvl) && $lvl > 0){
			$Myf .= "AND p.initial_level = {$lvl} ";
		}
		if (strlen($Myf) > 0){$MyQ .= implode("WHERE", explode("AND", $Myf, 2));}
		$MyQ .= "ORDER BY f.is_closed ASC, p.last_name, p.first_name LIMIT 500";
		return $db->DoQuery($MyQ);
	}
	
	function getDBProspectsByStage($stages = "", $pname = "", $lvl = 0){
		if (strlen($stages) > 0){
			global $db;
			$MyQ = "SELECT f.id_followup, f.id_prospect, DATE_FORMAT(f.record_date,'%Y-%m-%d') as rdate, ";
			$MyQ .= "f.comments, concat(p.first_name,' ',p.last_name) as full_name, u.user_name "; 
			$MyQ .= "FROM crm_followup f, crm_prospects p, crm_users u "; 
			$MyQ .= "WHERE f.next_stg_id in ({$stages}) ";
			$MyQ .= "AND f.id_prospect = p.id_prospect AND u.user_id = f.user_id ";
			if (strlen($pname) > 0){
				$MyQ .= "AND (p.first_name LIKE '%{$pname}%' OR p.last_name LIKE '%{$pname}%') ";
			}
			if ($lvl > 0){
				$MyQ .= "AND p.initial_level = {$lvl} ";
			}
			$MyQ .= "ORDER BY f.record_date, p.last_name, p.first_name LIMIT 500";
			return $db->DoQuery($MyQ);
		}
	}
	
	function deactivateDBProspect($id_prospect){
		if (numericOnly($id_prospect)){
			global $db;
			$MyQ = "UPDATE crm_followup SET is_closed = 1 WHERE id_prospect = {$id_prospect} AND is_current = 1";
			$result = $db->ExecCmd($MyQ);
			if ($db->is_error){
				error_log($MyQ,0);
			}
			return ($result > 0) ? true : false;
		}else{
			return false;
		}
	}
	
	function activateDBProspect($id_prospect){
		if (numericOnly($id_prospect)){
			global $db;
			$MyQ = "UPDATE crm_followup SET is_closed = 0 WHERE id_prospect = {$id_prospect} AND is_closed = 1 AND is_current = 1";
			$result = $db->ExecCmd($MyQ);
			if ($db->is_error){
				error_log($MyQ,0);
			}
			return ($result > 0) ? true : false;
		}else{
			return false;
		}
	}
	
	function getDBProspectComments($id_prospect){
		if (numericOnly($id_prospect)){
			global $db;
			$MyQ = "SELECT DATE_FORMAT(f.record_date,'%Y-%m-%d') as cdate, f.comments, u.user_name ";
			$MyQ .= "FROM crm_followup f INNER JOIN crm_users u ON u.user_id = f.user_id ";
			$MyQ .= "WHERE id_prospect = {$id_prospect} ORDER BY record_date DESC";
			return $db->DoQuery($MyQ);
		}
		return 0;
	}
	
	function getDBDemoClassAttendanceList($f1 = '', $f2 = ''){
		$d1 = strFixAndTrim($f1);
		$d2 = strFixAndTrim($f2);
		if (strlen($d1) != 10){$d1 = getCurrentDateForQuery(0,0,0,0,false);}
		if (strlen($d2) != 10){$d2 = getCurrentDateForQuery(0,0,0,0,false);}
		global $db;
		$MyQ = "SELECT p.first_name, p.last_name, p.phone_number, p.mobile_number, p.email, ";
		$MyQ .= "DATE_FORMAT(c.class_date,'%Y-%m-%d') as 'classdate', IFNULL(l.description,'Not Set') as 'clevel', ";
		$MyQ .= "c.show_up, IFNULL(r.description,'Not Set') as 'classroom' ";
		$MyQ .= "FROM `crm_demo-class` c INNER JOIN `crm_prospects` p ON c.id_prospect = p.id_prospect ";
		$MyQ .= "LEFT JOIN `crm_class-level` l ON l.id_level = c.id_level ";
		$MyQ .= "LEFT JOIN `crm_classroom` r ON r.id_classroom = c.id_classroom ";
		$MyQ .= "WHERE c.class_date BETWEEN '{$d1} 00:00:00' AND '{$d2} 23:59:59' ";
		$MyQ .= "ORDER BY c.class_date";
		return $db->DoQuery($MyQ);
	}
	
	function getDBReady2CloseProspects($stg){
		$v = str_replace(",", "0", $stg);
		if (numericOnly($v)){
			global $db;
			$MyQ = "SELECT p.first_name, p.last_name, p.phone_number, p.mobile_number, p.email, ";
			$MyQ .= "DATE_FORMAT(f.followup_date,'%Y-%m-%d') as fdate, f.comments, l.description as clevel ";
			$MyQ .= "FROM crm_followup f INNER JOIN crm_prospects p ON p.id_prospect = f.id_prospect ";
			$MyQ .= "INNER JOIN `crm_class-level` l ON l.id_level = p.initial_level ";
			$MyQ .= "WHERE f.is_current = 1 AND f.is_closed = 0 AND f.next_stg_id IN ({$stg}) ";
			//$MyQ .= "AND f.followup_date "
			$MyQ .= "ORDER BY followup_date ";
			return $db->DoQuery($MyQ);
		}
		return 0;
	}
	
?>