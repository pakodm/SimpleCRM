<?php
	$lerr = "";
	
	if (isset($_GET['logout'])){
		require_once "Kernel/libLOG.php";
		crmSignOut();
	}
	
	if (isset($_POST['tb_usr'],$_POST['tb_pwd'])){
		require_once "Kernel/libLOG.php";
		$lerr = "Username or password incorrect";
		$result = crmSignIn($_POST['tb_usr'],$_POST['tb_pwd']);
		switch ($result){
			case "0xLOG2" : "Something went wrong. Try again later"; break;
		}
		if (strcmp($result,"OK") === 0){
			header("Location:crmfront.php");
			exit;
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>International House - Aguascalientes (CRM)</title>
<link href="ihcrm.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="scripts/login.js"></script>
<!--[if lt IE 9]>
<script src="scripts/html5.js"></script>
<![endif]-->
</head>
<body class="claro">
	<div style="width:100%; height:100px;">
		<div style="width:40%; height:100px; float:left">
			<img src="images/ih_logo.png" alt="International House">
		</div>
		<div style="width:60%; height:100px; float:left;">
			<div style="color:#003E7E; font-size:22px; font-weight:bold; margin-top:30px;">
				Student Prospection &amp; Follow Up
			</div>
		</div>
	</div>
	<div class="centerLogin">
		<form method="post" action="index.php">
		<table style="width:320px; margin:auto;" border="0" cellpadding="1" cellspacing="1">
			<tr style="height:130px;">
				<td colspan="2">
					<img src="images/ih_logo_textless.png" alt="Ih" style="position:relative; top:12px; left:115px;">
				</td>
			</tr>
			<tr>
				<td style="text-align:center">
					<input type="text" maxlength="50" name="tb_usr" id="tb_usr" class="field" style="color:#999999" value="Username" onfocus="userStatus(this,true);" onblur="userStatus(this,false);">
				</td>
			</tr>
			<tr>
				<td align="center">
					<input type="text" maxlength="50" name="tb_pwd" id="tb_pwd" class="field" style="color:#999999" value="Password" onfocus="passStatus(this,true);" onblur="passStatus(this,false);" onkeyup="enterSubmit(event);"/>
				</td>
			</tr>
			<tr style="height:10px;"><td></td></tr>
			<tr>
				<td style="text-align:center">
					<div class="Button150 "  style='text-align:center;' onclick="doLogin()"><span>Sign In</span></div>
                </td>
			</tr>
			<tr style="height:10px;"><td></td></tr>
			<tr>
            	<td style="text-align:center; color:#A90000" id="result"><?php echo $lerr; ?></td>
			</tr>
		</table>
		</form>
	</div>
</body>
</html>