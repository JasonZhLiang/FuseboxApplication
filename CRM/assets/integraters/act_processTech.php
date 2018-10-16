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
$addFlag = ($Fusebox["action"] == 'addTech') ? true : false;

if ($Fusebox["action"] == "delTech") {
    $Utils->checkVerificationHash(@$_POST['tv'], @$_POST['User_ID']);
    $DeleteID  = $_POST['User_ID'];
    $timeStamp = date("Y-m-d H:i:s");

    $sqlDelete = "UPDATE erpcorp_admin.technicians
				  SET `DeleteFlag`  = '1',
				      `DeleteBy` = :DeleteBy,				  
				      `DeleteDate` = :DeleteDate,
					  `ModifyDate` = :DeleteDate
				  WHERE (User_ID   = :DeleteID) 
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
        trigger_error("Error could not delete #{$DeleteID}: technician", E_USER_ERROR);
        exit();
    }
} else if (!empty($_POST['inputFormSubmitted'])) {
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['ID']);
    $Utils->checkVerificationHash(@$_POST['vit'], @$_POST['itid']);
    $model['User_ID']          = $_POST['User_ID'];
    $model['IT_ID']            = $_POST['itid'];
    $model['vHash']            = $_POST['v'];
    $model['vHashT']           = $_POST['vit'];
    $model['FirstName']        = trim($_POST['FirstName']);
    $model['LastName']         = trim($_POST['LastName']);
    $model['Phone_1']          = trim($_POST['Phone_1']);
    $model['PhoneExt_1']       = trim($_POST['PhoneExt_1']);
    $model['PhoneType_1']      = trim($_POST['PhoneType_1']);
    $model['Email']            = trim($_POST['Email']);
    $model['Comments']         = trim($_POST['Comments']);
    $frmValidator              = new Validate_fields;
    $frmValidator->check_4html = true;
    $frmValidator->add_text_field("FirstName", $model['FirstName'], "text", "y", 50);
    $frmValidator->add_text_field("LastName", $model['LastName'], "text", "y", 50);
    // $frmValidator->add_num_field("Phone", $model['Phone_1'], 'number', "y");  //hpone number is validated below
    $frmValidator->add_num_field("Phone Extension", $model['PhoneExt_1'], 'number', "n");
    $frmValidator->add_text_field("Phone Type", $model['PhoneType_1'], "text", "y", 50);
    $frmValidator->add_link_field("Email", $model['Email'], "email", "y");
    $frmValidator->add_text_field("Comments", $model['Comments'], "text", 'n', 255);
    $error = '';
    if ($frmValidator->validation()) {
    } else {
        $error = $frmValidator->create_msg();
    }

    // Check phone format (accetps xxxxxxxxxx or xxx xxx xxxx or xxx-xxx-xxxx
    if ( ! preg_match("/^(\d{3})[- ]?(\d{3})[- ]?(\d{4})$/", $model['Phone_1'])) { // "/^(\d{10})$/"
        $model['Phone_1'] = '';
        $error .= "The field <b>Phone</b> is not valid. 10 Digits only, without country code .<br>";
    }
    else {
        //parse the phone number into a consistent format (xxxxxxxxxx)
        $model['Phone_1'] = preg_replace ('/[- ]/' , '' , $model['Phone_1'] );
    }
    if (!empty($error)) {
    } else {
        $timeStamp = date("Y-m-d H:i:s");
         $fields = [
            'FirstName',
            'LastName',
            'Phone_1',
            'PhoneExt_1',
            'PhoneType_1',
            'Email',
            'IT_ID',
            'Comments',
        ];

        $qrySet = [];
        foreach ($fields as $field) {
            $qrySet[] = "`". $field ."` = :". $field ."";
        }
        $qrySet = implode(",". PHP_EOL, $qrySet);

        if ($Fusebox["action"] == "addTech") {
             $sql = "INSERT INTO erpcorp_admin.technicians SET "
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
        } elseif ($Fusebox["action"] == "editTech") {
            $sql = "UPDATE erpcorp_admin.technicians SET "
                . $qrySet . ",
                        ModifyDate = :ModifyDate,
                        ModifyBy   = :ModifyBy
                    WHERE User_ID = :User_ID
                    ";
            $PDOdb->prepare($sql);
            foreach ($fields as $field) {
                $PDOdb->bind($field, $model[$field]);
            }
            $PDOdb->bind('ModifyDate', $timeStamp);
            $PDOdb->bind('ModifyBy', $_SESSION['ADMIN_USER']['User_ID']);
            $PDOdb->bind('User_ID', $model['User_ID']);
            $PDOdb->execute();
            if ($PDOdb->rowCount()>0) {
                session_write_close();
                header("Location: " . APP_URL . $XFA['list'] . "");
                exit();
            }
        } else {
            $error = "Unknown Opperation - No changes were written to the database.";
        }
    }
} else if ($Fusebox["action"] == "editTech") {
    $Utils->checkVerificationHash(@$_GET['tv'], @$_GET['tid']);
    $ID    = $_GET['tid'];
    $vHash = $_GET['tv'];
    $sql   = "
	SELECT
	    User_ID,
        FirstName,
        LastName,
        Phone_1,
        PhoneExt_1,
        PhoneType_1,
        Email,
        IT_ID,
        Comments
    FROM
        erpcorp_admin.technicians   
    WHERE (User_ID = :User_ID) ";
    $PDOdb->prepare($sql);
    $PDOdb->bind('User_ID', $ID);
    $PDOdb->execute();
    if ($PDOdb->rowCount() == 0) {
        trigger_error("Error no record for ID #{$ID}: technician", E_USER_ERROR);
        exit();
    } else {
        $row = $PDOdb->getRow();
        $model           = $row;
        $model['vHash']  = $Utils->createVerificationHash($ID);
        $model['vHashT'] = $Utils->createVerificationHash($model['IT_ID']);
    }
} else if ($Fusebox["action"] == "addTech") {
    $addFlag              = true;
    $model['User_ID']     = 'Auto Increment';
    $model['FirstName']   = '';
    $model['LastName']    = '';
    $model['Email']       = '';
    $model['Comments']    = '';
    $model['Phone_1']     = '';
    $model['PhoneExt_1']     = '';
    $model['PhoneType_1'] = '';
    $model['IT_ID']       = $_GET['tid'];
    $model['vHash']       = $Utils->createVerificationHash($model['User_ID']);
    $model['vHashT']      = $Utils->createVerificationHash($model['IT_ID']);
} else {
    trigger_error('Error aborting: technician', E_USER_ERROR);
    exit();
}
$aPhoneType   = ['Cell' => 'Cell', 'Landline' => 'Landline'];
$optPhoneType = $Utils->OptionsBuildArray($aPhoneType, $model['PhoneType_1']);