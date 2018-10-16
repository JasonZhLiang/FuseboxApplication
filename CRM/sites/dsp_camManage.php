<?php
/**
 * Created by PhpStorm.
 * User: harun
 * Date: 2017-05-23
 * Time: 11:26 AM
 */

if ( ! empty($_GET["sid"]) ) {
    if (empty($_GET["vid"]) || (md5(SEED.$_GET["sid"]) != $_GET["vid"])) {
        session_write_close();
        header("Location:". APP_URL . $XFA["list"]);
        exit();
    }
    $_SESSION["ADMIN_USER"]["Site_ID"] = $_GET["sid"]; // TODO ???
    $Site_ID =$_GET ["sid"];
    if(!empty($_SESSION['ADMIN_USER']['curSite']['Site_ID'])){
        $rtnLink = $XFA["siteDetail"]."&id=".$_SESSION['ADMIN_USER']['curSite']['Site_ID']."&v=".md5(SEED.$_SESSION['ADMIN_USER']['curSite']['Site_ID'])."#assets";
    }else{
        $rtnLink = $XFA['return'];
    }
} else {
}

$userType = 'admin';
$camAccess = New CameraAccess($PDOdb, $XFA, $Site_ID);
$camListRenderer = New CameraListRenderer($Components, $trans, $XFA, $userType);

$cameras = [];
$rowCount = 0;
$maxSort = [];

if ( ! empty($_POST["inputFormSubmitted"]) && ! empty($_POST["id"]) && is_numeric($_POST["id"]) ) {
    $delete_id = $_POST['id'];  //PkID  of camera
    $group_ID = $_POST['group_ID'];

    $sql = "UPDATE site_cameras SET
				DeleteFlag = 1
			WHERE PkID = :PkID
	;";
    $PDOdb->prepare($sql);
    $PDOdb->bind('PkID', $delete_id);
    $PDOdb->execute();
    // Adjust the SortOrder to prevent any glitches
    $sql = "
			UPDATE site_cameras SET
				SortOrder 		= SortOrder-1
			WHERE SortOrder > (SELECT TargetSort.SortOrder FROM (SELECT si.SortOrder FROM site_cameras AS si WHERE si.PkID = :PkID) AS TargetSort)
			AND	Site_ID = :Site_ID
			AND	CameraGroup_ID = :CameraGroup_ID
			AND DeleteFlag = 0
		;";
    $PDOdb->prepare($sql);
    $PDOdb->bind('PkID', $delete_id);
    $PDOdb->bind('Site_ID', $Site_ID);
    $PDOdb->bind('CameraGroup_ID', $group_ID);
    $PDOdb->execute();
}


$PriceLevel = 0;
$cameraGroups = $siteCameras = $cameraNames = [];

$camAccess->qryAndBuildCameraStructs();
$cameraGroups = $camAccess->getCameraGroupStruct();
$siteCameras = $camAccess->getCameraAccessInfoStruct();
$groupCount = $camAccess->getGroupCount();

$_SESSION["ADMIN_USER"]["SiteCameras"]["cameras"] = $siteCameras;

$_SESSION["ADMIN_USER"]["SiteCameras"]['groups'] = $cameraGroups;

$camListRenderer->setFieldsAndRenderListPage($PriceLevel, $cameraGroups, $groupCount, $rtnLink, $Site_ID);
