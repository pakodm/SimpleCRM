<?php
session_start();

if (!isset($_SESSION['CRMKEY'],$_SESSION['CRMUST'])){exit;}

require_once "Kernel/libUTILS.php";
require_once "Kernel/libVIEWKIT.php";

$action = $_GET['jxid'];
$result = '0xNOXID';

switch ($action){
	case 1:
		if (isset($_GET['wsid'])){
			$ihdat = strFixAndTrim($_GET['dat']);
			$result = getWorkspaceView($_GET['wsid'], $_SESSION['CRMKEY'],$ihdat);
		}else{
			$result = "0x01NOWSID";
		}
		break;
	case 2:
		if (isset($_POST['DAT'], $_POST['UID'])){
			$result = crmSaveProspect(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x02NOREQD";
		}
		break;
	case 3:
		if (isset($_POST['DAT'], $_POST['UID'])){
			$result = crmSaveUser(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x03NOREQD";
		}
		break;
	case 4:
		if (isset($_POST['DAT'])){
			$result = crmSaveCatalog(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x04NOREQD";
		}
		break;
	case 5:
		if (isset($_POST['DAT'])){
			$result = crmToggleVisible(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x05NOREQD";
		}
		break;
	case 6:
		if (isset($_POST['DAT'])){
			$result = crmSaveSchedule(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x06NOREQD";
		}
		break;
	case 7:
		if (isset($_POST['DAT'])){
			$result = crmScheduleView(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x07NOREQD";
		}
		break;
	case 8:
		if (isset($_POST['DAT'])){
			$result = crmToggleSchedule(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x08NOREQD";
		}
		break;
	case 9:
		if (isset($_POST['D1'],$_POST['D2'])){
			$result = getProspectDetailView($_POST['D1'],$_POST['D2'],$_SESSION['CRMKEY']);
		}else{
			$result = "0x09NOREQD";
		}
		break;
	case 10:
		if (isset($_POST['DAT'])){
			$result = crmUpdateFollowUp(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x0ANOREQD";
		}
		break;
	case 11:
		if (isset($_POST['DAT'])){
			$result = crmEditCatalog(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x0BNOREQD";
		}
		break;
	case 12:
		if (isset($_POST['DAT'])){
			$result = crmDeleteFromCatalog(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x0CNOREQD";
		}
		break;
	case 13:
		if (isset($_POST['DAT'])){
			$result = getUserDataForEdit($_POST['DAT'], $_SESSION['CRMKEY']);
		}else{
			$result = "0x0DNOREQD";
		}
		break;
	case 14:
		if (isset($_POST['DAT'])){
			$result = crmUpdateMyProfileData(json_decode($_POST['DAT']), $_SESSION['CRMKEY']);
		}else{
			$result = "0x0ENOREQD";
		}
		break;
	case 15:
		if (isset($_POST['DAT'])){
			$result = crmFilterOpsByUser(json_decode($_POST['DAT']), $_SESSION['CRMKEY']);
		}else{
			$result = "0x0FNOREQD";
		}
		break;
	case 16:
		if (isset($_POST['DAT'])){
			$result = crmDeleteProspect(json_decode($_POST['DAT']), $_SESSION['CRMKEY']);
		}else{
			$result = "0x10NOREQD";
		}
		break;
	case 17:
		if (isset($_POST['DAT'], $_POST['UID'])){
			$result = crmUpdateProspect(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x11NOREQD";
		}
		break;
	case 18:
		if (isset($_POST['DAT'])){
			$result = crmBuildDemoClassList(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x12NOREQD";
		}
		break;
	case 19:
		if (isset($_POST['DAT'])){
			$result = crmUpdateDemoClass(json_decode($_POST['DAT']),$_SESSION['CRMKEY']);
		}else{
			$result = "0x13NOREQD";
		}
		break;
	case 20:
		if (isset($_POST['DAT'])){
			$result = crmActivateProspect(json_decode($_POST['DAT']), $_SESSION['CRMKEY']);
		}else{
			$result = "0x14NOREQD";
		}
		break;
	case 21:
		if (isset($_POST['DAT'])){
			$result = crmFilterProspectRBy(json_decode($_POST['DAT']), $_SESSION['CRMKEY']);
		}else{
			$result = "0x15NOREQD";
		}
		break;
	case 22:
		if (isset($_POST['DAT'])){
			$result = crmFilterDemoClassAttendance(json_decode($_POST['DAT']), $_SESSION['CRMKEY']);
		}else{
			$result = "0x16NOREQD";
		}
}

echo $result;
?>