<?php
////////////////////////////////////////////////////////////
// File: act_process
//
// Description:
//		Displays list of items
//
// Information:
//		Date	- 2018-02-02
//		Author	- Jasonzh L
//		Version	- 1.0
//
// History:
//		- v1.0 initial development
//
////////////////////////////////////////////////////////////
// Init
$model   = [];
$addFlag = ($Fusebox["action"] == 'add')? true : false;

// Determine if either form (delete or Add/Edit) has been submitted.
if ($Fusebox["action"] == "delete") {
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['DeleteID']);

    $DeleteID = $_POST['DeleteID'];
    $vHash    = $_POST['v'];

    $timeStamp = date("Y-m-d H:i:s");
    $sqlDelete = "UPDATE erpcorp_admin.RouterList
				  SET `DeleteFlag`  = '1',
				      `DeleteBy` = :DeleteBy,				  
				      `DeleteDate` = :DeleteDate,
					  `ModifyDate` = :DeleteDate
				  WHERE (Router_ID   = :DeleteID) 
	 ";
    $PDOdb->prepare($sqlDelete);
    $PDOdb->bind('DeleteBy', $_SESSION['ADMIN_USER']['User_ID']);
    $PDOdb->bind('DeleteDate', $timeStamp);
    $PDOdb->bind('DeleteID', $DeleteID);
    $PDOdb->execute();
    if ($PDOdb->rowCount()>0) {
        session_write_close();
        header("Location: " . APP_URL . $XFA['list'] . "");
        exit();
    } else {
        // DO NOT Fall through; that would try to display eventEdit screen.
        trigger_error("Error could not delete #{$DeleteID}: " . SEC_TYPE, E_USER_ERROR);
        exit();
    }


} else if (!empty($_POST['inputFormSubmitted'])) {
    //	inputForm Submitted - GET all FORM VARIABLES
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['Router_ID']);

    $model['Router_ID']    = $_POST['Router_ID'];
    $model['vHash']        = $_POST['v'];
    $model['PID']          = trim($_POST['PID']);
    $model['ChassisSN']    = trim($_POST['ChassisSN']);
    $model['MAC']          = trim($_POST['MAC']);
    $model['Provider']     = trim($_POST['Provider']);
    $model['RouterStatus'] = trim($_POST['RouterStatus']);
    $model['Comments']     = trim($_POST['Comments']);

    //	VERIFY & VALIDATE INPUTS
    $frmValidator              = new Validate_fields;
    $frmValidator->check_4html = true;
    //	Because the back-end is english based, we Require an english title, to display on the NewsList page
    $frmValidator->add_text_field("PID", $model['PID'], "text", "y", 50);
    $frmValidator->add_text_field("ChassisSN", $model['ChassisSN'], "text", "y", 50);
    $frmValidator->add_text_field("MAC", $model['MAC'], "text", "y", 50);
    $frmValidator->add_num_field("Provider", $model['Provider'], 'number', "y");
    $frmValidator->add_num_field("RouterStatus", $model['RouterStatus'], 'number', "y");
    $frmValidator->add_text_field("Comments", $model['Comments'], "text", 'n', 500);

    $error = '';
    if ($frmValidator->validation()) {
        // if we don't set error we won't see any output.
        // $error = "All form fields are valid!"; // replace this text if you like...
    } else {
        $error = $frmValidator->create_msg();
    }

    if (!preg_match("/^(1PC)[0-9]{3}[:-][A-Za-z][0-9]$/", $model['PID'])) {
        $model['PID'] = '';
        $error        .= "The field <b>PID</b> is not valid.<br>";
    }

    if (!preg_match("/^(SFJC)(\d{4})[A-Za-z](\d{2})[A-Za-z]$/", $model['ChassisSN'])) {
        $model['ChassisSN'] = '';
        $error              .= "The field <b>ChassisSN</b> is not valid.<br>";
    }

    if (!preg_match("/^([0-9A-Fa-f]{2}){5}([0-9A-Fa-f]{2})$/", $model['MAC'])) {
        $model['MAC'] = '';
        $error        .= "The field <b>MAC</b> is not valid.<br>";
    }
    //	END VERIFY & VALIDATE INPUTS

    if (!empty($error)) {
        //		"inputError" - fall through with error message & re-populate form
    } else {
        $timeStamp = date("Y-m-d H:i:s");

        $fields = [
            'PID',
            'ChassisSN',
            'MAC',
            'Provider',
            'RouterStatus',
            'Comments',
        ];

        $qrySet = [];
        foreach ($fields as $field) {
            $qrySet[] = "`". $field ."` = :". $field ."";
        }
        $qrySet = implode(",". PHP_EOL, $qrySet);
        if ($Fusebox["action"] == "add") {
            $sql = "SELECT Router_ID, ChassisSN FROM erpcorp_admin.RouterList WHERE  (ChassisSN = :ChassisSN)";
            $PDOdb->prepare($sql);
            $PDOdb->bind('ChassisSN', $model['ChassisSN']);
            $PDOdb->execute();
            if (($PDOdb->rowCount() != 0)) {
                $row = $PDOdb->getRow();
                $error   .= "The field <b>ChassisSN</b> entered number is already existed, please go back to see the record which ID is " . $row['Router_ID'] . ".<br>";
            } else {
                $sql = "INSERT INTO erpcorp_admin.RouterList SET "
                    . $qrySet . ",
                            ModifyDate = :ModifyDate,
                            ModifyBy   = :ModifyBy,
                            CreateDate = :CreateDate,
                            CreateBy   = :CreateBy
                            ";
                $PDOdb->prepare($sql);
                foreach ($fields as $field) {
                    $PDOdb->bind($field, $model[$field]);
                }
                $PDOdb->bind('ModifyDate', $timeStamp);
                $PDOdb->bind('ModifyBy', $_SESSION['ADMIN_USER']['User_ID']);
                $PDOdb->bind('CreateDate', $timeStamp);
                $PDOdb->bind('CreateBy', $_SESSION['ADMIN_USER']['User_ID']);
                $PDOdb->execute();
                if ($PDOdb->rowCount()>0) {
                    session_write_close();
                    header("Location: " . APP_URL . $XFA['list'] . "");
                    exit();
                }
            }
        } elseif ($Fusebox["action"] == "edit") {
            $sql = "UPDATE erpcorp_admin.RouterList SET "
                . $qrySet . ",
                        ModifyDate = :ModifyDate,
                        ModifyBy   = :ModifyBy
                    WHERE Router_ID = :Router_ID
                    ";
            $PDOdb->prepare($sql);
            foreach ($fields as $field) {
                $PDOdb->bind($field, $model[$field]);
            }
            $PDOdb->bind('ModifyDate', $timeStamp);
            $PDOdb->bind('ModifyBy', $_SESSION['ADMIN_USER']['User_ID']);
            $PDOdb->bind('Router_ID', $model['Router_ID']);
            $PDOdb->execute();
            if ($PDOdb->rowCount()>0) {
                session_write_close();
                header("Location: " . APP_URL . $XFA['list'] . "");
                exit();
            }
        } else {        // don't assume anything
            $error = "Unknown Opperation - No changes were written to the database.";
        }
    }
} else if ($Fusebox["action"] == "edit") {
    //  populate the form ... for the edit
    $Utils->checkVerificationHash(@$_GET['v'], @$_GET['id']);

    $ID    = $_GET['id'];
    $vHash = $_GET['v'];

    $sql = "
	SELECT
	    Router_ID,
        PID,
        TAN,
        ChassisSN,
        MAC,
        Provider,
        RouterStatus,
        Comments
    FROM
        erpcorp_admin.RouterList   
    WHERE (Router_ID = :Router_ID) ";
    $PDOdb->prepare($sql);
    $PDOdb->bind('Router_ID', $ID);
    $PDOdb->execute();
    if (($PDOdb->rowCount() == 0)) {
        trigger_error("Error no record for ID #{$ID}: " . SEC_TYPE, E_USER_ERROR);
        exit();
    } else {
        $row = $PDOdb->getRow();
        $model['Router_ID']    = $row['Router_ID'];
        $model['PID']          = $row['PID'];
        $model['TAN']          = $row['TAN'];
        $model['ChassisSN']    = $row['ChassisSN'];
        $model['MAC']          = $row['MAC'];
        $model['Provider']     = $row['Provider'];
        $model['RouterStatus'] = $row['RouterStatus'];
        $model['Comments']     = $row['Comments'];
        $model['vHash']        = $Utils->createVerificationHash($ID);
    }
} else if ($Fusebox["action"] == "add") {
    $model['Router_ID']    = 'Auto Increment';
    $model['PID']          = '';
    $model['TAN']          = '';
    $model['ChassisSN']    = '';
    $model['MAC']          = '';
    $model['Provider']     = '';
    $model['RouterStatus'] = '';
    $model['Comments']     = '';
    $model['vHash']        = $Utils->createVerificationHash($model['Router_ID']);
} else {
    trigger_error('Error aborting: ' . SEC_TYPE, E_USER_ERROR);
    exit();
}

// Final setup

// Create drop-down list for Provider
$sql = "SELECT PkID, Label_EN FROM erpcorp_admin._lookups WHERE  (CODE = 'ROUTER_PROVIDER') ORDER BY SortOrder ";
$PDOdb->prepare($sql);
$PDOdb->execute();
$aProvider = array_column($PDOdb->getResultSet(),'Label_EN','PkID');
$optProviders = $Utils->OptionsBuildArray($aProvider, $model['Provider']);

// Create drop-down list for Provider
$sql = "SELECT PkID, Label_EN FROM erpcorp_admin._lookups WHERE  (CODE = 'ROUTER_STATUS') ORDER BY SortOrder ";
$PDOdb->prepare($sql);
$PDOdb->execute();
$aStatus = array_column($PDOdb->getResultSet(),'Label_EN','PkID');
$optRouterStatus = $Utils->OptionsBuildArray($aStatus, $model['RouterStatus']);

