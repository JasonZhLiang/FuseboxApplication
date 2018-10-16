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
    $sqlDelete = "UPDATE erpcorp_admin.ModemList
				  SET `DeleteFlag`  = '1',
				      `DeleteBy` = :DeleteBy,				  
				      `DeleteDate` = :DeleteDate,
					  `ModifyDate` = :DeleteDate
				  WHERE (Modem_ID   = :DeleteID) 
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
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['Modem_ID']);

    $model['Modem_ID']    = $_POST['Modem_ID'];
    $model['vHash']       = $_POST['v'];
    $model['FSN']         = trim($_POST['FSN']);
    $model['IMEI']        = trim($_POST['IMEI']);
    $model['MAC']         = trim($_POST['MAC']);
    $model['Provider']    = trim($_POST['Provider']);
    $model['ModemStatus'] = trim($_POST['ModemStatus']);
    $model['Comments']    = trim($_POST['Comments']);

    //	VERIFY & VALIDATE INPUTS
    $frmValidator              = new Validate_fields;
    $frmValidator->check_4html = true;
    //	Because the back-end is english based, we Require an english title, to display on the NewsList page
    $frmValidator->add_text_field("FSN", $model['FSN'], "text", "y", 50);
    $frmValidator->add_text_field("IMEI", $model['IMEI'], "text", "y", 50); // TODO When checking length of string
    $frmValidator->add_text_field("MAC", $model['MAC'], "text", "y", 50);
    $frmValidator->add_num_field("Provider", $model['Provider'], 'number', "y");
    $frmValidator->add_num_field("ModemStatus", $model['ModemStatus'], 'number', "y");
    $frmValidator->add_text_field("Comments", $model['Comments'], "text", 'n', 500);

    $error = '';
    if ($frmValidator->validation()) {
        // if we don't set error we won't see any output.
    } else {
        $error = $frmValidator->create_msg();
    }

    // Special validation
    // if fail clear these special one out: $model['FSN'] = '';
    if (!preg_match("/^[Oo][Xx][Yy][0-9]{9}([0-9]{3})$/", $model['FSN'])) {
        $model['FSN'] = '';
        $error        .= "The field <b>FSN</b> is not valid.<br>";
    }

    if ( ! preg_match("/^(35)(9)(22[0-9])(\d{8})(\d)$/", $model['IMEI'])) {
        $model['IMEI'] = '';
        $error .= "The field <b>IMEI</b> is not valid.<br>";
    }
    ///^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/
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
            'FSN',
            'IMEI',
            'MAC',
            'Provider',
            'ModemStatus',
            'Comments',
        ];

        $qrySet = [];
        foreach ($fields as $field) {
            $qrySet[] = "`". $field ."` = :". $field ."";
        }
        $qrySet = implode(",". PHP_EOL, $qrySet);

        if ($Fusebox["action"] == "add") {
            $sql = "SELECT Modem_ID, FSN FROM erpcorp_admin.ModemList WHERE  (FSN = :FSN)";
            $PDOdb->prepare($sql);
            $PDOdb->bind('FSN', $model['FSN']);
            $PDOdb->execute();
            $FSNcheck = $PDOdb->rowCount();
            $rowFSN = $PDOdb->getRow();

            $sql = "SELECT Modem_ID, IMEI FROM erpcorp_admin.ModemList WHERE  (IMEI = :IMEI)";
            $PDOdb->prepare($sql);
            $PDOdb->bind('IMEI', $model['IMEI']);
            $PDOdb->execute();
            $IMEIcheck = $PDOdb->rowCount();
            $rowIMEI = $PDOdb->getRow();

            $sql = "SELECT Modem_ID, MAC FROM erpcorp_admin.ModemList WHERE  (MAC = :MAC)";
            $PDOdb->prepare($sql);
            $PDOdb->bind('MAC', $model['MAC']);
            $PDOdb->execute();
            $MACcheck = $PDOdb->rowCount();
            $rowMAC = $PDOdb->getRow();

            if (($FSNcheck != 0)||($IMEIcheck != 0)||($MACcheck != 0)) {
                if ($FSNcheck!=0){
                    $error .= "The field <b>FSN</b> entered number is already existed in database, please go back to see the record which ID is " . $rowFSN['Modem_ID'] . ".<br>";
                }
                if ($IMEIcheck!=0){
                    $error .= "The field <b>IMEI</b> entered number is already existed in database, please go back to see the record which ID is " . $rowIMEI['Modem_ID'] . ".<br>";
                }
                if ($MACcheck!=0){
                    $error .= "The field <b>MAC</b> entered number is already existed in database, please go back to see the record which ID is " . $rowMAC['Modem_ID'] . ".<br>";
                }
            } else {
                $sql = "INSERT INTO erpcorp_admin.ModemList SET "
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
            $sql = "UPDATE erpcorp_admin.ModemList SET "
                        . $qrySet . ",
                        ModifyDate = :ModifyDate,
                        ModifyBy   = :ModifyBy
                    WHERE Modem_ID = :Modem_ID
                    ";
            $PDOdb->prepare($sql);
            foreach ($fields as $field) {
                $PDOdb->bind($field, $model[$field]);
            }
            $PDOdb->bind('ModifyDate', $timeStamp);
            $PDOdb->bind('ModifyBy', $_SESSION['ADMIN_USER']['User_ID']);
            $PDOdb->bind('Modem_ID', $model['Modem_ID']);
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
	    Modem_ID,
        FSN,
        IMEI,
        MAC,
        Provider,
        ModemStatus,
        Comments
    FROM
        erpcorp_admin.ModemList   
    WHERE (Modem_ID = :Modem_ID) ";
    $PDOdb->prepare($sql);
    $PDOdb->bind('Modem_ID', $ID);
    $PDOdb->execute();
    if ($PDOdb->rowCount() == 0) {
        trigger_error("Error no record for ID #{$ID}: " . SEC_TYPE, E_USER_ERROR);
        exit();
    } else {
        $row = $PDOdb->getRow();
        $model['Modem_ID']    = $row['Modem_ID'];
        $model['FSN']         = $row['FSN'];
        $model['IMEI']        = $row['IMEI'];
        $model['MAC']         = $row['MAC'];
        $model['Provider']    = $row['Provider'];
        $model['ModemStatus'] = $row['ModemStatus'];
        $model['Comments']    = $row['Comments'];
        $model['vHash']       = $Utils->createVerificationHash($ID);
    }
} else if ($Fusebox["action"] == "add") {
    $model['Modem_ID']    = 'Auto Increment';
    $model['FSN']         = '';
    $model['IMEI']        = '';
    $model['MAC']         = '';
    $model['Provider']    = '';
    $model['ModemStatus'] = '';
    $model['Comments']    = '';
    $model['vHash']       = $Utils->createVerificationHash($model['Modem_ID']);
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
$sql = "SELECT PkID, Label_EN FROM erpcorp_admin._lookups WHERE  (CODE = 'MODEM_STATUS') ORDER BY SortOrder ";
$PDOdb->prepare($sql);
$PDOdb->execute();
$aStatus = array_column($PDOdb->getResultSet(),'Label_EN','PkID');
$optModemStatus = $Utils->OptionsBuildArray($aStatus, $model['ModemStatus']);

