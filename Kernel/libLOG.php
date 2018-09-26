<?php

session_start();

require_once "libDB.php";	
require_once "libUTILS.php";
include_once "ihconfig.php";
	
$db = new database($db_server,$db_user,$db_password,$db_name);	

function crmSignIn($user, $pass){
	$u = strFixAndTrim($user);
	$p = strFixAndTrim($pass);
	
	if ((strlen($u) > 1) && (strlen($p) > 1)){
		global $db;
		$MyQ = "SELECT user_id, user_type FROM crm_users WHERE enabled = 1 AND user_name = '{$u}' AND user_key = SHA1('{$p}')";
		$UserData = $db->DoQuery($MyQ);
		if (!$db->is_error){
			if (is_array($UserData)){
				$MyQ = "UPDATE crm_users SET last_login = NOW() WHERE user_id = ".$UserData[0]['user_id'];
				$db->ExecCmd($MyQ);
				$_SESSION['CRMKEY'] = $UserData[0]['user_id'];
				$_SESSION['CRMUST'] = $UserData[0]['user_type'];
				return "OK";
			}else{
				return "0xLOG3";
			}
		}else{
			return "0xLOG2";
		}
	}else{
		return "0xLOG1";
	}
}

function crmSignOut(){
	if (isset($_SESSION['CRMKEY'])){
		$uid = strFixandTrim($_SESSION['CRMKEY']);
		unset($_SESSION['CRMKEY']);
		unset($_SESSION['CRMUST']);
		if (numericOnly($uid)){
			//global $db;
			//$MyQ = "UPDATE crm_users SET last_login = NOW();"
		}
	}
}

?>