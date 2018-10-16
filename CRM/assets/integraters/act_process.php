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
$addFlag = ($Fusebox["action"] == 'add') ? true : false;
if ($Fusebox["action"] == "delete") {
    // delete integrator from list and and reload page
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['Integrator_ID']);
    $Integrator_ID = $_POST['Integrator_ID'];
    $timeStamp = date("Y-m-d H:i:s");
    $sqlDelete = "UPDATE erpcorp_admin.integrators
				  SET `DeleteFlag`  = '1',
				      `DeleteBy` = :DeleteBy,				  
				      `DeleteDate` = :DeleteDate,
					  `ModifyDate` = :DeleteDate
				  WHERE (PkID   = :DeleteID) 
	 ";
    $PDOdb->prepare($sqlDelete);
    $PDOdb->bind('DeleteBy', $_SESSION['ADMIN_USER']['User_ID']);
    $PDOdb->bind('DeleteDate', $timeStamp);
    $PDOdb->bind('DeleteID', $Integrator_ID);
    $PDOdb->execute();
    if ($PDOdb->rowCount()>0) {
        session_write_close();
        header("Location: " . APP_URL . $XFA['list'] . "");
    } else {
        trigger_error("Error could not delete #{$Integrator_ID}: " . SEC_TYPE, E_USER_ERROR);
    }
    exit();
} elseif ($Fusebox["action"] == "undelete") {
    // restore integrator to list and reload page
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['Integrator_ID']);
    $Integrator_ID = $_POST['Integrator_ID'];
    $timeStamp = date("Y-m-d H:i:s");
    $sqlUnDelete = "UPDATE erpcorp_admin.integrators
				  SET `DeleteFlag` = '0',
				      `ModifyBy` = :ModifyBy,				  
					  `ModifyDate` = :ModifyDate
				  WHERE (PkID = :DeleteID) 
	 ";
    $PDOdb->prepare($sqlUnDelete);
    $PDOdb->bind('ModifyBy', $_SESSION['ADMIN_USER']['User_ID']);
    $PDOdb->bind('ModifyDate', $timeStamp);
    $PDOdb->bind('DeleteID', $Integrator_ID);
    $PDOdb->execute();

    if ($PDOdb->rowCount()>0) {
        session_write_close();
        header("Location: " . APP_URL . $XFA['list'] . "&show_delete=1");
    } else {
        trigger_error("Error could not restore #{$Integrator_ID}: " . SEC_TYPE, E_USER_ERROR);
    }
    exit();
} else if (!empty($_POST['inputFormSubmitted'])) {
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['PkID']);
    $model['PkID']             = $_POST['PkID'];
    $model['vHash']            = $_POST['v'];
    $model['Name']             = trim($_POST['Name']);
    $model['Address']          = trim($_POST['Address']);
    $model['City']             = trim($_POST['City']);
    $model['Province']         = trim($_POST['Province']);
    $model['PostalCode']       = trim($_POST['PostalCode']);
    $model['Country']          = trim($_POST['Country']);
    $model['PrimaryContact']   = trim($_POST['PrimaryContact']);
    $model['Phone']            = trim($_POST['Phone']);
    $model['PhoneType']        = trim($_POST['PhoneType']);
    $model['PhoneExt']         = trim($_POST['PhoneExt']);
    $model['Notes']            = trim($_POST['Notes']);
    $frmValidator              = new Validate_fields;
    $frmValidator->check_4html = true;
    $frmValidator->add_text_field("Name", $model['Name'], "text", "y", 50);
    $frmValidator->add_text_field("Primary Contact", $model['PrimaryContact'], "text", "y", 50);
    //$frmValidator->add_num_field("Phone", $model['Phone'], 'number', "y");
    $frmValidator->add_num_field("Phone Extension", $model['PhoneExt'], 'number', "n");
    $frmValidator->add_text_field("PhoneType", $model['PhoneType'], "text", "y", 50);
    $frmValidator->add_text_field("Address", $model['Address'], "text", "y", 100);
    $frmValidator->add_text_field("City", $model['City'], "text", 'y', 50);
    $frmValidator->add_text_field("Province", $model['Province'], "text", 'y', 50);
    $frmValidator->add_text_field("PostalCode", $model['PostalCode'], "text", 'y', 50);
    $frmValidator->add_text_field("Country", $model['Country'], "text", 'y', 50);
    $frmValidator->add_text_field("Notes", $model['Notes'], "text", 'n', 50);
    $error = '';
    if ($frmValidator->validation()) {
    } else {
        $error = $frmValidator->create_msg();
    }
    // Check phone format (accetps xxxxxxxxxx or xxx xxx xxxx or xxx-xxx-xxxx
    if ( ! preg_match("/^(\d{3})[- ]?(\d{3})[- ]?(\d{4})$/", $model['Phone'])) { // "/^(\d{10})$/"
        $model['Phone'] = '';
        $error .= "The field <b>Phone</b> is not valid. 10 Digits only, without country code .<br>";
    }
    else {
        //parse the phone number into a consistent format (xxxxxxxxxx)
        $model['Phone'] = preg_replace ('/[- ]/' , '' , $model['Phone'] );
    }
    if (empty($error)){
        // if all data is valid, update the database (add or modify)
        $timeStamp = date("Y-m-d H:i:s");

        $fields = [
            'Name',
            'Address',
            'City',
            'Province',
            'PostalCode',
            'Country',
            'PrimaryContact',
            'Phone',
            'PhoneType',
            'PhoneExt',
            'Notes',
        ];

        $qrySet = [];
        foreach ($fields as $field) {
            $qrySet[] = "`". $field ."` = :". $field ."";
        }
        $qrySet = implode(",". PHP_EOL, $qrySet);

        if ($Fusebox["action"] == "add") {
            $sql = "INSERT INTO erpcorp_admin.integrators SET "
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
        } elseif ($Fusebox["action"] == "edit") {
            $sql = "UPDATE erpcorp_admin.integrators SET "
                . $qrySet . ",
                        ModifyDate = :ModifyDate,
                        ModifyBy   = :ModifyBy
                    WHERE PkID = :PkID
                    ";
            $PDOdb->prepare($sql);
            foreach ($fields as $field) {
                $PDOdb->bind($field, $model[$field]);
            }
            $PDOdb->bind('ModifyDate', $timeStamp);
            $PDOdb->bind('ModifyBy', $_SESSION['ADMIN_USER']['User_ID']);
            $PDOdb->bind('PkID', $model['PkID']);
            $PDOdb->execute();
            if ($PDOdb->rowCount()>0) {
                session_write_close();
                header("Location: " . APP_URL . $XFA['list'] . "");
                exit();
            }
        } else {
            $error = "Unknown Operation - No changes were written to the database.";
        }
    }
} else if ($Fusebox["action"] == "edit") {
    $Utils->checkVerificationHash(@$_GET['v'], @$_GET['id']);
    $ID    = $_GET['id'];
    $vHash = $_GET['v'];
    $sql   = "
	    SELECT
	        PkID,
            Name,
            Address,
            City,
            Country,
            Province,
            PostalCode,
            Phone,
            PhoneType,
            PhoneExt,
            PrimaryContact,
            Notes
        FROM
            erpcorp_admin.integrators   
        WHERE (PkID = :PkID) ";

    $PDOdb->prepare($sql);
    $PDOdb->bind('PkID', $ID);
    $PDOdb->execute();

    if ($PDOdb->rowCount() == 0) {
        trigger_error("Error no record for ID #{$ID}: " . SEC_TYPE, E_USER_ERROR);
        exit();
    } else {
        $row = $PDOdb->getRow();
        $model          = $row;
        $model['vHash'] = $Utils->createVerificationHash($ID);
    }
} else if ($Fusebox["action"] == "add") {
    $addFlag                 = true;
    $model['PkID']           = 'Auto Increment';
    $model['Name']           = '';
    $model['Address']        = '';
    $model['City']           = '';
    $model['Province']       = '';
    $model['PostalCode']     = '';
    $model['Country']        = '';
    $model['Phone']          = '';
    $model['PhoneType']      = '';
    $model['PhoneExt']       = '';
    $model['PrimaryContact'] = '';
    $model['Notes']          = '';
    $model['vHash']          = $Utils->createVerificationHash($model['PkID']);
} else {
    trigger_error('Error aborting: ' . SEC_TYPE, E_USER_ERROR);
    exit();
}

$id  = $model['PkID'];
$sql = "SELECT c.Comment, 
               c.PkID, 
               c.ModifiedBy, 
               DATE_FORMAT(c.ModifiedDate, '%Y-%m-%d') AS ModifyDate,
               CONCAT(u.FirstName, ' ', u.LastName) AS FullName
		FROM erpcorp_admin.integrator_comments AS c
		INNER JOIN erp_main.users AS u ON c.ModifiedBy = u.User_ID
		WHERE IT_ID = :IT_ID
		ORDER BY ModifiedDate DESC
	;";

$PDOdb->prepare($sql);
$PDOdb->bind('IT_ID', $id);
$PDOdb->execute();

if ($PDOdb->rowCount() > 0) {
    while ($row = $PDOdb->getRow()) {
        $comments[$row["PkID"]] = $row["Comment"];
        $commentUser[$row["PkID"]] = $row["FullName"];
        $commentModifyDate[$row["PkID"]] = $row["ModifyDate"];
    }
}

$aPhoneType   = ['Cell' => 'Cell', 'Landline' => 'Landline'];
$optPhoneType = $Utils->OptionsBuildArray($aPhoneType, $model['PhoneType']);

$provinces    = $Utils->fetchProvinceList();
$optProvinces = $Utils->OptionsBuildArray($provinces, $model['Province']);

$chkIsCanada = $model['Country'] == 'Canada' ? ' checked ' : '';
$chkIsUS     = $model['Country'] == 'US' ? ' checked ' : '';