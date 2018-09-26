<?php
	session_start();
	
	if (!isset($_SESSION['CRMKEY'],$_SESSION['CRMUST'])){
		header("Location:index.php");
		exit;
	}
	
	require_once "Kernel/libCRMKIT.php";
	
	$task_count = crmHowManyTasksToday($_SESSION['CRMKEY']);
	$demo_count = crmHowManyDemoToday($_SESSION['CRMKEY']);
	$isTopUser = hasTopAccess($_SESSION['CRMKEY']);
	$whoami = crmGetMyName($_SESSION['CRMKEY']);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>International House - Aguascalientes (CRM)</title>
<link href="ihcrm.css" rel="stylesheet" type="text/css">
<link href="lightbox.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="CoreServices/CoreKit.js"></script>
<script type="text/javascript" src="CoreServices/crmapp.js"></script>
<script type="text/javascript" src="CoreServices/utils.js"></script>
<script type="text/javascript" src="CoreServices/AjaxClass.js"></script>
<script type="text/javascript" src="CoreServices/json2min.js"></script>
<script type="text/javascript" src="CoreServices/greybox.js"></script>
<script type="text/javascript" src="scripts/calendar3.js"></script>
<!--[if lt IE 9]>
<script src="scripts/html5.js"></script>
<![endif]-->
</head>
<body class="claro">
	<div style="width:100%; height:100px;">
		<div style="width:35%; height:100px; float:left">
			<img src="images/ih_logo.png" alt="International House">
		</div>
		<div style="width:50%; height:100px; float:left;">
			<div style="color:#003E7E; font-size:22px; font-weight:bold; margin-top:30px;">
				Student Prospection &amp; Follow Up
			</div>
		</div>
		<div style="width:15%; height:100px; float:left">
			<div style="color:#003E7E; font-size:16px; font-weight:bold; margin-top:30px;">
				Welcome back <?php echo $whoami; ?>
			</div>
		</div>
	</div>
	<br><br>
	<div style="width: 90%; height:30px; margin:auto; color:#003E7E; font-size:18px; font-weight:bold;">Choose an activity to perform</div>
	<div class="crmDesktop">
		<table style="width:90%; margin:auto; text-align:center">
			<tr>
				<td style="width:25%">
					<?php if ($task_count > 0){ ?>
					<div class="crmRoundAlert" style="position:relative; left:50%; margin-top:-10px; margin-left:-60px; padding-top:6px;">
					<?php echo $task_count; ?>
					</div>
					<?php } ?>
					<img src="resources/follow_up.png" alt="Follow Up" title="Follow Up" onclick="getWorkspace(1);">
				</td>
				<td>
					<?php if ($demo_count > 0){ ?>
					<div class="crmRoundAlert" style="position:relative; left:50%; margin-top:-10px; margin-left:-60px; padding-top:6px;">
					<?php echo $demo_count; ?>
					</div>
					<?php } ?>
					<img src="resources/class_schedule.png" alt="Demo Class" title="Demo Class" onclick="getWorkspace(4);">
				</td>
				<td>
					<img src="resources/new_candidate.png" alt="New Prospect" title="New Prospect" onclick="getWorkspace(2);">
				</td>
				<td>
					<a href="index.php?logout=1"><img src="resources/sign_out.png" alt="Sign Out" title="Sign Out"></a>
				</td>
			</tr>
			<tr>
				<td>
					<span class="crmTextActivity">Follow Up</span>
				</td>
				<td>
					<span class="crmTextActivity">Demo Class</span>
				</td>
				<td>
					<span class="crmTextActivity">New prospect</span>
				</td>
				<td>
					<span class="crmTextActivity">Log out</span>
				</td>
			</tr>
			<tr style="height:30px;"><td colspan="4"></td></tr>
			<tr>
				<td>
					<img src="resources/register.png" alt="Register" title="Student registration" onclick="getWorkspace(6);">
				</td>
				<td>
					<img src="resources/settings.png" alt="Settings" title="Configuration" onclick="getWorkspace(3);">
				</td>
				<td>
					<?php //if ($isTopUser){ ?>
					<img src="resources/reports.png" alt="Reports" title="Reports" onclick="getWorkspace(5);">
					<?php //} ?>
				</td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>
					<span class="crmTextActivity">Registrations</span>
				</td>
				<td>
					<span class="crmTextActivity">Settings</span>
				</td>
				<td>
					<?php //if ($isTopUser){ ?>
					<span class="crmTextActivity">Reports</span>
					<?php //} ?>
				</td>
				<td></td>
				<td></td>
			</tr>
		</table>
	</div>
	<div class="crmWorkspace" id="Workspace">
		<div id="wsTitle">
			<img src="resources/x_close.png" alt="X" style="cursor:pointer; width:16px; height:auto;" onclick="hideLightbox()">
			<span id="wsTextTitle"></span>
		</div>
		<div class="crmWSArea" id="wsArea"></div>
	</div>
	<div class="crmWorkspaceLoading" id="LoaderInd"></div>
	<div class="crmAlertBox" id="winfo">
		<div id="InfoTitle"></div>
		<div style="width:90%; height:10px; margin:auto;"></div>
		<div id="InfoText"></div>
		<div class="Button150" style='text-align:center;' onclick="hideAlert()"><span>OK</span></div>
	</div>
	<input type="hidden" value="" id="ih_helper" size="1">
</body>
</html>
