<?php
	class database{
		
		var $db_server; var $db_user; var $db_password; var $db;
		var $is_error; var $error_str;
		
		function database($s,$u,$p,$db){
			$this->db_server = $s;
			$this->db_user = $u;
			$this->db_password = $p;
			$this->db = $db;
			$this->is_error = false;
			$this->error_str = "";
		}
	
		function CreateCnx(){
			$this->is_error = false;
			if (!($link=mysqli_connect($this->db_server,$this->db_user,$this->db_password))){
				$this->is_error = true; 
				$this->error_str = "Error conectando a la base de datos. ".mysql_error();
				error_log($this->error_str,0);
				exit();
			}
			if (!mysqli_select_db($link,$this->db)){
				$this->is_error = true; 
				$this->error_str = "Error seleccionando la base de datos. ".mysql_error();
				error_log($this->error_str,0);
				exit();
			}
			return $link;
		}
	
		function ExecCmd($MySQLCmd){
			$this->is_error = false;
			$cnx = $this->CreateCnx();
			$resp = mysqli_query($cnx,$MySQLCmd);
			if (mysqli_error($cnx)){
				$this->is_error = true;
				$this->error_str = mysqli_error($cnx);
				error_log($this->error_str,0);
			}
			mysqli_close($cnx);
			return $resp;
		}
	
		function ExecCmdId($MySQLCmd){
			$this->is_error = false;
			$cnx = $this->CreateCnx();
			$resp = mysqli_query($cnx,$MySQLCmd);
			if (mysqli_error($cnx)){
				$this->is_error = true;
				$this->error_str = mysqli_error($cnx);
				error_log($this->error_str,0);
			}
			if ($resp >= 1)
				$resp = mysqli_insert_id($cnx);
			mysqli_close($cnx);
			return $resp;
		}
	
		function ExecMulCmd($MySQLCmds){
			$NSCmds = explode(";",$MySQLCmds);
			$cnx = $this->CreateCnx();
			for($i=0; $i<(count($NSCmds)-1); $i++){
				$resp = mysqli_query($cnx,$NSCmds[$i]);
				if (!$resp){
					error_log(mysqli_error($cnx),0);
					break;
				}
			}
			mysqli_close($cnx);
			return $i;
		}
	
		function DoQuery($MySQLQuery, $InArray = true){
			$this->is_error = false;
			$cnx = $this->CreateCnx();
			$result = mysqli_query($cnx,$MySQLQuery);
			if (mysqli_error($cnx)){
				$this->is_error = true;
				$this->error_str = mysqli_error($cnx);
				error_log($this->error_str,0);
			}
			mysqli_close($cnx);
			if (!$InArray){
				return $result;
			}else{
				$sqlArray = $this->sqlDataToArray($result);
				mysqli_free_result($result);
				return $sqlArray;
			}
		}
		
		function GetSingleData($_MySQLQuery){
			$this->is_error = false;
			$qres = $this->DoQuery($_MySQLQuery,false);
			if (!$this->is_error){
				if (mysqli_num_rows($qres) > 0){
					$row = mysqli_fetch_array($qres);
					//error_log("E: ".$row[0],0);
					return $row[0];
				}else{
					return 0;
				}
			}else{
				return false;
			}
		}
		
		function sqlDataToArray($rawData = null, $nCols = 0){
			if ($rawData == null)
				return 0;
			if (mysqli_num_rows($rawData) == 0)
				return 0;
			if (is_numeric($rawData))
				return 0;
			$arrData = array(mysqli_num_rows($rawData));
			$i = 0;
			if($row = mysqli_fetch_array($rawData)){
				do{
					$arrData[$i] = $row;
					$i++;
				}while ($row = mysqli_fetch_array($rawData));
			}
			return $arrData;
		}
	}
?>