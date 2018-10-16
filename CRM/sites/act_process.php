<?php
////////////////////////////////////////////////////////////
// File: act_process.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2013-10-25
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in JetBrains PhpStorm
//		- v2.0 Modifications by HO
//
//
////////////////////////////////////////////////////////////

$camInputFormFields 		= array();
$error 		= "";
$HTMLreq 	= " required ";
$curSite	= $_SESSION["ADMIN_USER"]["Site_ID"];
$objCamAccess = New CameraAccess($PDOdb, $XFA, $curSite);
$userType   = 'admin';

if ( ! empty($_POST["camInputFormSubmitted"])) {

    $camInputFormFields = $objCamAccess->processFormSubmission($_POST);
    $camInputFormFields["PkID"] = $_POST["PkID"];
    $camInputFormFields["SortOrder"] 	= $_POST["order"];
    $camInputFormFields["prevGroup"] 	= $_POST["prevGroup"];
	$camGroup			= empty($_POST['groupSelect']) ? '': $_POST['groupSelect'];

//    trigger_error('$_POST Array: '.print_r($_POST, 1), E_USER_ERROR);
//    die('dumping $_POST array');
	// Validate & enter contents of site edit form into database
	// Validation START
	$lang = "en";
	$lang = LANG == "_FR" ? "fr": $lang;
	$validator = new Validate_fields();
	$validator->language	= $lang;
	$validator->check_4html = true;
    $validator = $objCamAccess->validateFormInputs($validator, $camInputFormFields); // section 1 -> client inputs for site and contacts
	// Validation END

	if ( ! $validator->validation()) {
		$error = $validator->create_msg();
	} else {

	    $objCamAccess->updateInsertQry($camInputFormFields, $camGroup, $Fusebox["action"], $userType, $curSite);

		session_write_close();
		header("Location: ".APP_URL . $XFA["return"] ."&sid=" . $curSite ."&vid=". md5(SEED.$curSite));
		exit();
	}

	$id = $camInputFormFields["PkID"];  //even necessary???

} else if ( $Fusebox["action"] == "edit" ) {

	if ( empty($_GET["id"]) || empty($_GET['v']) ) {
		trigger_error('Missing parameters for camera Edit', E_USER_ERROR);
	}

	if ( ! is_numeric($_GET["id"]) || $_GET['v'] != md5(SEED . $_GET["id"]) ) {
		trigger_error('Failed validation of camera id', E_USER_ERROR);
	}
	$id = $_GET["id"];
    $camInputFormFields = $objCamAccess->addEditInitializeCameraInfo($Fusebox["action"], $id, $userType);
    $camGroup = $camInputFormFields['camGroup'];

} else if ( $Fusebox["action"] == "add") {
	if ( empty($_GET["g"]) || empty($_GET['v']) ) {
		trigger_error('Missing parameters for camera Add', E_USER_ERROR);
	}

	if ( ! is_numeric($_GET["g"]) || $_GET['v'] != md5(SEED . $_GET["g"]) ) {
		trigger_error('Failed validation of camera group id', E_USER_ERROR);
	}

	$camGroup			= $_GET["g"];
    $camInputFormFields = $objCamAccess->addEditInitializeCameraInfo($Fusebox["action"], '', $userType);
}

// Create Group drop-down list
$sql = "SELECT
			scg.GroupName,
			scg.PkID
		FROM site_cameras_groups AS scg
		WHERE
			scg.Site_ID = :Site_ID AND
			scg.DeleteFlag = 0
";
$PDOdb->prepare($sql);
$PDOdb->bind('Site_ID',$curSite);
$PDOdb->execute();
$aGroup = array_column($PDOdb->getResultSet(),'GroupName','PkID');
$optCamGroups = $Utils->OptionsBuildArray($aStatus, $camGroup);
$areGroups = true;
