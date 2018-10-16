<?php
////////////////////////////////////////////////////////////
// File: act_process
//
// Description:
//		Process User
//
// Information:
//		Date	- 2013-04-14
//		Author	- ross MacLachlan
//		Version	- 2.0
//
// History:
//		- v2.0 initial development
//		- 2014-07-14
//		 1) adapted to utilize more recent coding standard
//		 2) adapted to use a user-Class
//		 3) delete now only executes if Validation matches
//		 4) add/edit uses data-model for display
//		 5) input validation error feedback now atually displayed & used
//		 6) 
//		 7) 
//		 8) 
//		 9) 
//
// TODO
//		- We need to prevent two users of the same Email. Curently this field has a Unique-constraint index 
//		  but the add/edit page would cause error if one tries to add a person of the same Email
//
////////////////////////////////////////////////////////////
$vDebug = false;
$model = array();
$curUser_ID = $_SESSION['ADMIN_USER']['User_ID'];
$HTML5req = (0) ? '' : ' required '; // HTML5_REQ_OFF ??

if ( ($Fusebox["action"] == 'delete') && ( isset($_POST['DeleteID']) && is_numeric($_POST['DeleteID']) ) ) {
	$DeleteID 	= $_POST['DeleteID'];
	$verify 	= $_POST['vDelete'];
	$DeleteBy 	= $_POST['DeleteBy'];
	$timeStamp 	= date("Y-m-d H:i:s");

	if( ( empty($verify)) || ($verify !== md5(SEED.$DeleteID)) ){
		trigger_error('Error: Delete Failed For UserID ['.  $DeleteID .'] - key error.', E_USER_ERROR);
		exit();
	}
	
	//	TODO:	determine what to do about Email-Addresses for Deleted  users because it is now a Unique Constraint
	//			2014-07-14 Rok    & 	RM  (also noted in -method- )
	$sql = "UPDATE users SET
				DeleteFlag   	= 1,
				ModifyDate   	= :ModifyDate,
				ModifyBy     	= :ModifyBy
			WHERE User_ID = :User_ID
			";
    $PDOdb->prepare($sql);
    $PDOdb->bind('ModifyDate', $timeStamp);
    $PDOdb->bind('ModifyBy', $curUser_ID);
    $PDOdb->bind('User_ID', $DeleteID);
    $PDOdb->execute();

	$Utils->redirectUser(APP_URL .$XFA['return']);
	exit();

} elseif ( ($Fusebox["action"] == 'undelete') && ( isset($_POST['DeleteID']) && is_numeric($_POST['DeleteID']) ) ) {
    $DeleteID 	= $_POST['DeleteID'];
    $verify 	= $_POST['vDelete'];
    $DeleteBy 	= $_POST['DeleteBy'];
    $timeStamp 	= date("Y-m-d H:i:s");
    if( ( empty($verify)) || ($verify !== md5(SEED.$DeleteID)) ){
        trigger_error('Error: Delete Failed For UserID ['.  $DeleteID .'] - key error.', E_USER_ERROR);
        exit();
    }
    $sql = "UPDATE users SET
				DeleteFlag   	= 0,
				ModifyDate   	= :ModifyDate,
				ModifyBy     	= :ModifyBy
			WHERE User_ID = :User_ID
			";
    $PDOdb->prepare($sql);
    $PDOdb->bind('ModifyDate', $timeStamp);
    $PDOdb->bind('ModifyBy', $curUser_ID);
    $PDOdb->bind('User_ID', $DeleteID);
    $PDOdb->execute();
    $Utils->redirectUser(APP_URL .$XFA['return']. "&show_delete=1");
    exit();

} elseif(isset($_POST['inputFormSubmitted'])) {
	$model['User_ID'] 			= trim($_POST['id']);
	$model['FirstName'] 		= trim($_POST['FirstName']);
	$model['LastName'] 			= trim($_POST['LastName']);
	if (isset($_POST['isSysAdmin'])){
		$model['isSysAdmin'] 	= (empty($_POST['isSysAdmin']))			? 0 : 1;
	}
	$model['Email'] 		= isset($_POST['Email']) ? trim($_POST['Email']): '';
	$model['AccessLevel'] 	= trim($_POST['AccessLevel']);
	$model['isActive'] 		= (empty($_POST['isActive']))			? 0 : 1;
	$model['Lang'] 			= trim($_POST['Lang']);
	$model['City'] 			= trim($_POST['City']);
	$model['PositionTitle'] = trim($_POST['PositionTitle']);
	$model['Province'] 		= trim($_POST['Province']);
	$model['Phone_1'] 		= trim($_POST['Phone_1']);
	$model['PhoneType_1'] 	= trim($_POST['PhoneType_1']);
	$model['PhoneExt_1'] 	= trim($_POST['PhoneExt_1']);

	$Password       = ( ! empty($_POST['Password'])) ? trim($_POST['Password']) : '';
	$verifyPW       = ( ! empty($_POST['verifyPW'])) ? trim($_POST['verifyPW']) : '';
	/*
	echo('<pre> in the form:<br />'. print_r($_POST, 1). '</pre>');
	echo('<pre> in the data-model:<br />'. print_r($model, 1). '</pre>');
	die();
	*/
	
	//	VERIFY & VALIDATE INPUTS
	$frmValidator = new Validate_fields;
	$frmValidator->check_4html = true;
	$frmValidator->add_text_field("First Name", $model['FirstName'], "text", "y", 30);
	$frmValidator->add_text_field("Last Name", $model['LastName'], "text", "y", 30);
	$frmValidator->add_text_field("Province", $model['Province'], "text", "n", 30);
	$frmValidator->add_text_field("Position / Title", $model['PositionTitle'], "text", "n", 30);

	if($Fusebox["action"] == 'add'){
		$frmValidator->add_text_field("Email / Login", $model['Email'], "text", "y", 250);
        $frmValidator->add_text_field("Password", $Password, "text", "y", 32);
        $frmValidator->add_text_field("Confirm Password", $verifyPW, "text", "y", 32);
    }	
	$error = ''; // reset error
	if ( ! $frmValidator->validation()) {
		$error = $frmValidator->create_msg();
	}

	// special checks that 'append' to $error
	if($Fusebox["action"] == 'add'){
		if ( User::isEmailAddressInUse($model['Email']) ) {
			$error .= 'The email you have provided is already being used in the system.<BR>';
		}
		//	verify that passwords is min 6 characters & `p === verify`
		$Utils->validateChkPassword($Password, $verifyPW, $error);
	}

	if ( ($Fusebox["action"] == "edit")&&( !  is_numeric($model['User_ID']))  ) {
		$error .= 'Invalid Input:  cannot process your request for  [user: '. $model['User_ID'] .'] .<BR>';
	}

	//	END VERIFY & VALIDATE INPUTS

	if (empty($error)) {
		// Input is all good
		$model['timeStamp'] = date("Y-m-d H:i:s");
		$rtnValue = false;

        $fields = [
            'Lang',
            'LastName',
            'FirstName',
            'Province',
            'PositionTitle',
            'Phone_1',
            'PhoneType_1',
            'PhoneExt_1',
        ];

        $qrySet = [];
        foreach ($fields as $field) {
            $qrySet[] = "`". $field ."` = :". $field ."";
        }
        $qrySet = implode(",". PHP_EOL, $qrySet);

		if ($Fusebox["action"] == "add") {
			// special for this form
			$hasher = new PasswordHash();
			$model['Password'] = $hasher->HashPassword($Password);

			// Add email, password and create fields
			$sql = "INSERT INTO users SET
					". $qry .",
					ModifyDate  = :ModifyDate,
					ModifyBy    = :ModifyBy,
					Email       = :Email,
					CreateDate  = :CreateDate,
					CreateBy    = :CreateBy,
					Password    = :Password
					";
            $PDOdb->prepare($sql);
            foreach ($fields as $field) {
                $PDOdb->bind($field, $model[$field]);
            }
            $PDOdb->bind('ModifyDate', $model['timeStamp']);
            $PDOdb->bind('ModifyBy', $curUser_ID);
            $PDOdb->bind('Email', $model['Email']);
            $PDOdb->bind('CreateDate', $model['timeStamp']);
            $PDOdb->bind('CreateBy', $curUser_ID);
            $PDOdb->bind('Password', $model['Password']);
            $PDOdb->execute();
            $rtnValue = $PDOdb->lastInsertId();
		}elseif ($Fusebox["action"] == "edit") {
			$sql = "UPDATE users SET
						". $qry ."
					WHERE User_ID = :User_ID
			";
            $PDOdb->prepare($sql);
            foreach ($fields as $field) {
                $PDOdb->bind($field, $model[$field]);
            }
            $PDOdb->bind('User_ID', $model['User_ID']);
            $PDOdb->execute();
			$rtnValue = $model['User_ID'];

		}else {		// don't assume anything
			$error .= "Unknown Operation - No changes were written to the database.";
		}
		
		if ( ! empty($rtnValue)) {
			$Utils->redirectUser(APP_URL .  $XFA['return'] .'' );
		}	
		 
	}// endif - if (empty($error)) 
		
}elseif(($Fusebox["action"] == 'edit')){

	if( empty($_GET['u']) || ! is_numeric($_GET['u']) ){	// don't assume anything
		trigger_error("Error in processing data " . __FILE__ . ': ' . __LINE__ , E_USER_ERROR);	
	}

	$thisID = $_GET['u'];
	$sql = "SELECT 	User_ID, Email, isSysAdmin, isActive, AccessLevel, Lang, 
					FirstName, LastName, City, Province, PositionTitle, Phone_1, PhoneType_1, PhoneExt_1
			FROM users
			WHERE User_ID = :User_ID
			  AND DeleteFlag = '0'
		;";
    $PDOdb->prepare($sql);
    if ($vDebug) $PDOdb->debugShowQuery();
    $PDOdb->bind('User_ID', $thisID);
    $PDOdb->execute();

	if ($PDOdb->rowCount() == 0) {	// don't assume anything
		trigger_error("Error in processing data " . __FILE__ . ': ' . __LINE__ , E_USER_ERROR);	
	}else{
		$row = $PDOdb->getRow();
		$model = $row;
		$model['WhoDoneIt_ID'] = $curUser_ID ;
	}
    $sites=[];
    $sql = "SELECT s.Site_ID, s.SiteName, s.SiteDescription, s.City, s.Address_1, x.AccessLevel, x.isNotified, x.isBroadCast, x.isBroadCastSMS, x.isBroadCastVoice, x.canInvite, 
                   CONCAT(u.LastName, ', ', u.FirstName) AS FullName
				FROM sites AS s
				LEFT JOIN site_user_xref As x ON s.Site_ID = x.Site_ID
				LEFT JOIN users AS u ON x.User_ID = u.User_ID
				WHERE s.DeleteFlag = 0 AND x.User_ID = :User_ID
				ORDER BY s.SiteName
		;";
    $PDOdb->prepare($sql);
    if ($vDebug) $PDOdb->debugShowQuery();
    $PDOdb->bind('User_ID', $thisID);
    $PDOdb->execute();
    if ($PDOdb->rowCount() > 0) {
        while ($row = $PDOdb->getRow()) {
            $row['verifyToken'] = md5(SEED.$row['Site_ID']);
            $sites[]			= $row;
        }
        $_SESSION['tagetUser']['User_ID'] = $thisID;
    } else {
        $sitesInfo = "No associated site(s) returned for this user";
    }
}elseif($Fusebox["action"] == 'add'){
	$model['User_ID'] = '000';;						
	$model['Lang'] = '_EN';
	$model['AccessLevel'] = 999;
	$model['FirstName'] = $model['LastName'] = $model['Email'] = '';
	$model['City'] = $model['Province'] = $model['PositionTitle'] = '';
	$model['Phone_1'] = $model['PhoneType_1'] = $model['PhoneExt_1'] = '';
	$model['isActive'] = $model['isSysAdmin'] = 0;

}else{	// don't assume anything
	trigger_error("Error in processing data " . __FILE__ . ': ' . __LINE__ , E_USER_ERROR);	
}

//	drop-down selectors ...
// Get provinces list according to Language preference
$sql = "SELECT ID, Province". LANG ." AS Province
		FROM zref_province
		WHERE SortID = 1 /* limit results to Canadian Provinces only for now */
		ORDER BY Province 
;";

$AccessLevel_Options  = $Utils->buildAccessLevelOptions($aAccessLevels, $model['AccessLevel'], $_SESSION['ADMIN_USER']['AccessLevel']) ;

//	Checkboxes
$model['isActive_cb'] = ($model['isActive'])? ' checked ' : '';
$model['isSysAdmin_cb']  = ( (array_key_exists('isSysAdmin', $model)) && ($model['isSysAdmin']) ) ? ' checked ' : '';


//	 RadioButtons
//		we offer 2 languages French & English (english being our default, if the language is NOT F it will be E
$model['langEnglishSelected_rb'] = $model['langFrenchSelected_rb']  ='';
if ($model['Lang'] == '_FR'){
	$model['langFrenchSelected_rb'] =  ' checked ';
}else{
	$model['langEnglishSelected_rb'] =  ' checked ';
}


?>