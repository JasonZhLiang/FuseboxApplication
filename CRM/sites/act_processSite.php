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
$site = [];
if ($Fusebox["action"] == "deleteSite") {
    $Utils->checkVerificationHash(@$_POST['vDelete'], @$_POST['DeleteID']);
    $DeleteID  = $_POST['DeleteID'];
    $vHash     = $_POST['vDelete'];
//    $qDelete   = $_POST['qDelete'];
//    $sDelete   = $_POST['sDelete'];
    $timeStamp = date("Y-m-d H:i:s");
    $sql = "UPDATE sites
				  SET `DeleteFlag` = '1',
				      `ModifyBy` = :ModifyBy,				  
					  `ModifyDate` = :ModifyDate
				  WHERE Site_ID = :Site_ID
	 ";
    $PDOdb->prepare($sql);
    $PDOdb->bind('ModifyBy', $_SESSION['ADMIN_USER']['User_ID']);
    $PDOdb->bind('ModifyDate', $timeStamp);
    $PDOdb->bind('Site_ID', $DeleteID);
    $PDOdb->execute();
    if ($PDOdb->rowCount() > 0) {
        session_write_close();
        header("Location: " . APP_URL . $XFA['list'] );
//        header("Location: " . APP_URL . $XFA['list'] . "&qryCondition=" . $qDelete . "&searchSiteName=" . $sDelete);
        exit();
    } else {
        trigger_error("Error could not delete #{$DeleteID}: site", E_USER_ERROR);
        exit();
    }
} else if (!empty($_POST['inputFormSubmitted'])) {
    $Utils->checkVerificationHash(@$_POST['v'], @$_POST['id']);
    $site['Site_ID']           = $_POST['id'];
    $site['vHash']             = $_POST['v'];
//    $site['s']                 = $_POST['s'];
//    $site['q']                 = $_POST['q'];
    $site['SiteName']          = trim($_POST['SiteName']);
    $site['SiteDescription']   = trim($_POST['SiteDescription']);
    $site['Address_1']         = trim($_POST['Address_1']);
    $site['PaidNumUsers']      = trim($_POST['PaidNumUsers']);
    $site['MaxNumUsers']       = trim($_POST['MaxNumUsers']);
    $site['PaidNumContacts']   = trim($_POST['PaidNumContacts']);
    $site['MaxNumContacts']    = trim($_POST['MaxNumContacts']);
    $site['Stories']           = trim($_POST['Stories']);
    $site['StoriesBelowGrade'] = trim($_POST['StoriesBelowGrade']);
    $site['SquareFootage']     = trim($_POST['SquareFootage']);
    $frmValidator              = new Validate_fields;
    $frmValidator->check_4html = true;
    $frmValidator->add_text_field("SiteName", $site['SiteName'], "text", "y", 50);
    $frmValidator->add_text_field("SiteDescription", $site['SiteDescription'], "text", "y", 250);
    $frmValidator->add_text_field("Address_1", $site['Address_1'], "text", "y", 50);
    $frmValidator->add_text_field("Stories", $site['Stories'], "text", "y", 50);
    $frmValidator->add_text_field("StoriesBelowGrade", $site['StoriesBelowGrade'], "text", "y", 50);
    $frmValidator->add_num_field("PaidNumUsers", $site['PaidNumUsers'], 'number', "y");
    $frmValidator->add_num_field("MaxNumUsers", $site['MaxNumUsers'], 'number', "y");
    $frmValidator->add_num_field("PaidNumContacts", $site['PaidNumContacts'], 'number', "y");
    $frmValidator->add_num_field("MaxNumContacts", $site['MaxNumContacts'], 'number', "y");
    $frmValidator->add_num_field("SquareFootage", $site['SquareFootage'], 'number', "y");
    $error = '';
    if (!$frmValidator->validation()) {
        $error = $frmValidator->create_msg();
    }
    if ($site['PaidNumUsers']>$site['MaxNumUsers']){
        $error .= 'The field <b>Max Number of Users</b> Allowed cannot be less than Subscribed';
    }
    if ($site['PaidNumContacts']>$site['MaxNumContacts']){
        $error .= 'The field <b>Max Number of Tenant Contacts</b> Allowed cannot be less than Subscribed';
    }
    if (empty($error)) {

        $timeStamp = date("Y-m-d H:i:s");
        $fields = [
            'SiteName',
            'SiteDescription',
            'Address_1',
            'Stories',
            'StoriesBelowGrade',
            'SquareFootage',
            'PaidNumUsers',
            'MaxNumUsers',
            'PaidNumContacts',
            'MaxNumContacts',
        ];

        $qrySet = [];
        foreach ($fields as $field) {
            $qrySet[] = "`". $field ."` = :". $field ."";
        }
        $qrySet = implode(",". PHP_EOL, $qrySet);

        $sql       = "UPDATE sites SET "
                    . $qrySet . " ,
                    `ModifyDate` = :ModifyDate,
                    `ModifyBy` = :ModifyBy 
                     WHERE (Site_ID = :Site_ID)";

        $PDOdb->prepare($sql);
        foreach ($fields as $field) {
            $PDOdb->bind($field, $site[$field]);
        }
        $PDOdb->bind('ModifyDate', $timeStamp);
        $PDOdb->bind('ModifyBy', $_SESSION['ADMIN_USER']['User_ID']);
        $PDOdb->bind('Site_ID', $site['Site_ID']);
        $PDOdb->execute();

        if ($PDOdb->rowCount()>0) {
            session_write_close();
//            header("Location: " . APP_URL . $XFA['list'] . "&qryCondition=" . $site['q'] . "&searchSiteName=" . $site['s']);
            header("Location: " . APP_URL . $XFA['return']."&id=" . $site["Site_ID"] . "&v=" . $site['vHash'] );
            exit();
        }
    }
} else if ($Fusebox["action"] == "editSite") {
    $Utils->checkVerificationHash(@$_GET['v'], @$_GET['id']);
    $id    = $_GET['id'];
    $vHash = $_GET['v'];
//    $s     = $_GET['s'];
//    $q     = $_GET['q'];
    $sql   = "SELECT Site_ID, SiteName, SiteDescription, Address_1, Stories, StoriesBelowGrade, SquareFootage, PaidNumUsers, MaxNumUsers, PaidNumContacts, MaxNumContacts
              FROM sites   
              WHERE (Site_ID = :Site_ID)";
    $PDOdb->prepare($sql);
    $PDOdb->bind('Site_ID', $id);
    $PDOdb->execute();
    if ($PDOdb->rowCount() == 0) {
        trigger_error("Error no record for ID #{$id}: " . SEC_TYPE, E_USER_ERROR);
        exit();
    } else {
        $row = $PDOdb->getRow();
        $site['Site_ID']           = $row['Site_ID'];
        $site['SiteName']          = $row['SiteName'];
        $site['SiteDescription']   = $row['SiteDescription'];
        $site['Address_1']         = $row['Address_1'];
        $site['Stories']           = $row['Stories'];
        $site['StoriesBelowGrade'] = $row['StoriesBelowGrade'];
        $site['SquareFootage']     = $row['SquareFootage'];
        $site['PaidNumUsers']      = $row['PaidNumUsers'];
        $site['MaxNumUsers']       = $row['MaxNumUsers'];
        $site['PaidNumContacts']   = $row['PaidNumContacts'];
        $site['MaxNumContacts']    = $row['MaxNumContacts'];
        $site['vHash']             = $Utils->createVerificationHash($id);
//        $site['s']                 = $s;
//        $site['q']                 = $q;
    }
} else {
    trigger_error('Error aborting: edit site', E_USER_ERROR);
    exit();
}

$paidNumUsers       = [2 => '2', 10 => '10', 20 => '20', 30 => '30', 40 => '40', 50 => '50', 60 => '60', 70 => '70', 80 => '80', 90 => '90', 100 => '100'];
$selPaidNumUsers    = $Utils->OptionsBuildArray($paidNumUsers, $site['PaidNumUsers']);
$paidNumContacts    = [25 => '25', 50 => '50', 75 => '75', 100 => '100', 125 => '125', 150 => '150', 175 => '175', 200 => '200', 225 => '225', 250 => '250', 275 => '275'];
$selPaidNumContacts = $Utils->OptionsBuildArray($paidNumContacts, $site['PaidNumContacts']);

