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
$model = [];
$addFlag = ($Fusebox["action"] == 'add')? true : false;


// Determine if either form (delete or Add/Edit) has been submitted.
if ($Fusebox["action"] == "delete") {
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['DeleteID']);

    $DeleteID = $_POST['DeleteID'];
    $vHash    = $_POST['v'];

    $timeStamp = date("Y-m-d H:i:s");
    $sqlDelete = "UPDATE erpcorp_admin.SIMList SET 
                    `DeleteFlag`  = '1',
                    `DeleteBy`    = :DeleteBy,
                    `DeleteDate`  = :DeleteDate,
                    `ModifyDate`  = :DeleteDate
                  WHERE (SIMcard_ID = :DeleteID) 
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
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['SIMcard_ID']);

    $model['SIMcard_ID']       = $_POST['SIMcard_ID'];
    $model['vHash']            = $_POST['v'];
    $model['UPCcode']          = trim($_POST['UPCcode']);
    $model['SerialNumber']     = trim($_POST['SerialNumber']);
    $model['ICCID']            = trim($_POST['ICCID']);
    $model['Provider']         = trim($_POST['Provider']);
    $model['SIMstatus']        = trim($_POST['SIMstatus']);
    $model['Comments']         = trim($_POST['Comments']);

    //	VERIFY & VALIDATE INPUTS
    $frmValidator = new Validate_fields;
    $frmValidator->check_4html = true;
    //	Because the back-end is english based, we Require an english title, to display on the NewsList page
    $frmValidator->add_text_field("UPCcode", $model['UPCcode'], "text", "y", 50);
    $frmValidator->add_text_field("SerialNumber", $model['SerialNumber'], "text",  "y", 50); // TODO When checking length of string
    $frmValidator->add_text_field("ICCID", $model['ICCID'], "text",  "y", 50);
    $frmValidator->add_num_field("Provider", $model['Provider'], 'number', "y");
    $frmValidator->add_num_field("SIMstatus", $model['SIMstatus'], 'number', "y");
    $frmValidator->add_text_field("Comments", $model['Comments'], "text",  'n', 500);

    $error = '';
    if ($frmValidator->validation()) {
        // if we don't set error we won't see any output.
        // $error = "All form fields are valid!"; // replace this text if you like...
    } else {
        $error = $frmValidator->create_msg();
    }

    // Special validation
    // if fail clear these special one out: $model['UPCcode'] = '';
    if ( ! preg_match("/^6[0-9]{9}([0-9]{2})?$/", $model['UPCcode'])) {
        $model['UPCcode'] = '';
        $error .= "The field <b>UPCcode</b> is not valid.<br>";
    }

    if ( ! preg_match("/^(89)(1)(22[0-9])(\d{12})(\d)$/", $model['ICCID'])) {
        $model['ICCID'] = '';
        $error .= "The field <b>ICCID</b> is not valid.<br>";
    }
    //	END VERIFY & VALIDATE INPUTS

    if ( ! empty($error)) {
        //		"inputError" - fall through with error message & re-populate form
    } else {
         $timeStamp = date("Y-m-d H:i:s");
         $fields = [
            'UPCcode',
            'SerialNumber',
            'ICCID',
            'Provider',
            'SIMstatus',
            'Comments',
        ];

        $qrySet = [];
        foreach ($fields as $field) {
            $qrySet[] = "`". $field ."` = :". $field ."";
        }
        $qrySet = implode(",". PHP_EOL, $qrySet);

        if ($Fusebox["action"] == "add") {
            $sql = "SELECT SIMcard_ID, ICCID FROM erpcorp_admin.SIMList WHERE  (ICCID = :ICCID)" ;
            $PDOdb->prepare($sql);
            $PDOdb->bind('ICCID', $model['ICCID']);
            $PDOdb->execute();
            if (($PDOdb->rowCount() != 0)) {
                $row = $PDOdb->getRow();
                $error .= "The field <b>ICCID</b> entered number is already existed, please go back to see the record which ID is ".$row['SIMcard_ID'].".<br>";
            }else{
                $sql = "INSERT INTO erpcorp_admin.SIMList SET "
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
            $sql = "UPDATE erpcorp_admin.SIMList SET "
                . $qrySet . ",
                        ModifyDate = :ModifyDate,
                        ModifyBy   = :ModifyBy
                    WHERE SIMcard_ID = :SIMcard_ID
                    ";
            $PDOdb->prepare($sql);
            foreach ($fields as $field) {
                $PDOdb->bind($field, $model[$field]);
            }
            $PDOdb->bind('ModifyDate', $timeStamp);
            $PDOdb->bind('ModifyBy', $_SESSION['ADMIN_USER']['User_ID']);
            $PDOdb->bind('SIMcard_ID', $model['SIMcard_ID']);
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

    $ID     = $_GET['id'];
    $vHash  = $_GET['v'];

    $sql = "
	SELECT
	    SIMcard_ID,
        UPCcode,
        SerialNumber,
        ICCID,
        Provider,
        SIMstatus,
        Comments
    FROM
        erpcorp_admin.SIMList   
    WHERE (SIMcard_ID = :SIMcard_ID) ";

    $PDOdb->prepare($sql);
    $PDOdb->bind('SIMcard_ID', $ID);
    $PDOdb->execute();
    if ($PDOdb->rowCount() == 0) {
        trigger_error("Error no record for ID #{$ID}: " . SEC_TYPE, E_USER_ERROR);
        exit();
    } else {
        $row = $PDOdb->getRow();
        $model['SIMcard_ID']     = $row['SIMcard_ID'];
        $model['UPCcode']        = $row['UPCcode'];
        $model['SerialNumber']   = $row['SerialNumber'];
        $model['ICCID']          = $row['ICCID'];
        $model['Provider']       = $row['Provider'];
        $model['SIMstatus']      = $row['SIMstatus'];
        $model['Comments']       = $row['Comments'];
        $model['vHash']          = $Utils->createVerificationHash($ID);
    }
} else if ($Fusebox["action"] == "add") {
    $addFlag = true;
    $model['SIMcard_ID']       = 'Auto Increment';
    $model['UPCcode']          = '';
    $model['SerialNumber']     = '';
    $model['ICCID']            = '';
    $model['Provider']         = '';
    $model['SIMstatus']        = '';
    $model['Comments']         = '';
    $model['vHash']            = $Utils->createVerificationHash($model['SIMcard_ID']);
} else {
    trigger_error('Error aborting: ' . SEC_TYPE, E_USER_ERROR);
    exit();
}

// Final setup
// Create drop-down list for Provider
$sql = "SELECT PkID, Label_EN FROM erpcorp_admin._lookups WHERE  (CODE = 'TELCOM') ORDER BY SortOrder ";
$PDOdb->prepare($sql);
$PDOdb->execute();
$aProvider = array_column($PDOdb->getResultSet(),'Label_EN','PkID');
$optProviders = $Utils->OptionsBuildArray($aProvider, $model['Provider']);

// Create drop-down list for Provider
$sql = "SELECT PkID, Label_EN FROM erpcorp_admin._lookups WHERE  (CODE = 'SIM_STATUS') ORDER BY SortOrder ";
$PDOdb->prepare($sql);
$PDOdb->execute();
$aStatus = array_column($PDOdb->getResultSet(),'Label_EN','PkID');
$optSIMstatus = $Utils->OptionsBuildArray($aStatus, $model['SIMstatus']);


