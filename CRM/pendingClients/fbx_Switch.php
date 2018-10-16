<?php
// Switch level debuging
(IS_TEST_SERVER)? define('DBG_SWITCH',0) /*TEST SERVER */: define('DBG_SWITCH',0)/*LIVE*/;
// 	Security: 	circuit level access control
$Utils->authenticateAdminUser();


// SET defaultFuseaction here for this Circuit
if(empty($Fusebox["action"])) $Fusebox["action"] = "Home"; // Note in this site it has an upper case H ???
$CURRENT_SECTION = $managingSectionTitle = '';
$FbSelf     = $Fusebox["circuit"] .'.'. $Fusebox["action"];
$FbAction   = $Fusebox["circuit"] .'.'. $Fusebox["action"];
$Nav->setNavigationAids($FbAction, $CURRENT_SECTION, $managingSectionTitle); /* by Ref */

// required classes for circuit
require(PATH_TO_COMMON . '/classes/modules/pendingClients/PendingClientForm.php');
require(PATH_TO_COMMON . '/classes/modules/pendingClients/PendingClientData.php');

$DBP = new DB(PENDING_HOST, PENDING_DB, PENDING_USER, PENDING_PWD, true);
$PDOdbPending = new App\PDODatabase(PENDING_DSN, PENDING_USER, PENDING_PWD);

//echo($CURRENT_SECTION .'<br>');
define('FILTER_LIST_EULA', 0);
define('FILTER_LIST_PENDING', 1);
define('FILTER_LIST_ADDRESS_VALIDATION', 2);
define('FILTER_LIST_SITEMATCH', 3);
define('FILTER_LIST_REVIEW', 4);
define('FILTER_LIST_PUBLISH', 5);

define('SECTION_ADMIN_FIELDS', 0);
define('SECTION_CLIENT_INPUTS', 1);
define('SECTION_OFFICE_INPUTS', 2);

define('DISPLAY_REPORTING_MESSAGES', 0);

$XFA['EULAlist']	        	= $Fusebox["circuit"] .'.listEULAnc';
$XFA['pendingList']	    		= $Fusebox["circuit"] .'.listPending';
$XFA['addressValidationList'] 	= $Fusebox["circuit"] .'.listAddressValidation';
$XFA['addressList']	    		= $Fusebox["circuit"] .'.listAddressCheck';
$XFA['reviewList']	    		= $Fusebox["circuit"] .'.listReview';
$XFA['publishList']	    		= $Fusebox["circuit"] .'.listPublish';


switch($Fusebox["action"]) {
////////////////////////////////////////////////////////////
// The following sections all go through the same page
////////////////////////////////////////////////////////////

    // Step 1
	case "Fusebox.defaultFuseaction":
    case "listPending";
	    $PARAM['filter']= FILTER_LIST_PENDING;
		$XFA['edit']	= $Fusebox["circuit"] .'.reviewPending';
		$XFA['review']	= $Fusebox["circuit"] .'.collectFinalDetails';
		$XFA['delete']	= $Fusebox["circuit"] .'.delete';
        $XFA['filter']  = $FbSelf;
		include("dsp_list.php");
		break;

    case "delete";
    case "reviewPending":
        $XFA['process']     = $FbSelf;
        $XFA['return']	    = $Fusebox["circuit"] .'.listPending';
        $XFA['continue']	= $Fusebox["circuit"] .'.validateAddress';
        include("act_reviewPending.php");
        include("dsp_reviewPending.php");
        break;

	// EULA Step 0
	case "listEULAnc";
        $PARAM['filter']= FILTER_LIST_EULA;
		$XFA['edit']	= $Fusebox["circuit"] .'.reviewEULA';
		$XFA['delete']	= $Fusebox["circuit"] .'.deleteEULA';
        $XFA['filter']  = $FbSelf;
		include("dsp_list.php");
		break;
    
    case "deleteEULA";
    case "reviewEULA":
        $XFA['process']     = $FbSelf;
        $XFA['return']	    = $Fusebox["circuit"] .'.listEULAnc';
        $XFA['continue']	= $Fusebox["circuit"] .'.listEULAnc';
        include("act_reviewPending.php");
        include("dsp_reviewEULA.php");
        break;

	// Step 2
	case "listAddressValidation";
        $PARAM['filter']= FILTER_LIST_ADDRESS_VALIDATION;
        $XFA['edit']	= $Fusebox["circuit"] .'.validateAddress';
        $XFA['delete']	= $Fusebox["circuit"] .'.deleteValidation';
        $XFA['filter']  = $FbSelf;
        include("dsp_list.php");
        break;

    case "deleteValidation":
        $XFA['process']     = $FbSelf;
        $XFA['return']	    = $Fusebox["circuit"] .'.listAddressValidation';
        $XFA['continue']	= $Fusebox["circuit"] .'.listAddressValidation';
        include("act_reviewPending.php");
        break;
        
    case "validateAddress";
        $XFA['process']     = $FbSelf;
        $XFA['return']	    = $Fusebox["circuit"] .'.listAddressValidation';
        $XFA['continue']	= $Fusebox["circuit"] .'.checkAddress';
        $XFA['storeMatches']= $Fusebox["circuit"] .'.ajax_storeAddress';
        include("dsp_validateAddress.php");
        break;

    // Step 3
    case "listAddressCheck";
        $PARAM['filter']= FILTER_LIST_SITEMATCH;
		$XFA['edit']	= $Fusebox["circuit"] .'.checkAddress';
		$XFA['delete']	= $Fusebox["circuit"] .'.deleteCheckAddress';
        $XFA['filter']  = $FbSelf;
		include("dsp_list.php");
		break;
    
    case "deleteCheckAddress":
        $XFA['process']     = $FbSelf;
        $XFA['return']	    = $Fusebox["circuit"] .'.listAddressCheck';
        $XFA['continue']	= $Fusebox["circuit"] .'.listAddressCheck';
        include("act_reviewPending.php");
        break;
    
    case "checkAddress";
        $XFA['process']     = $FbSelf;
        $XFA['return']	    = $Fusebox["circuit"] .'.listAddressCheck';
        $XFA['continue']	= $Fusebox["circuit"] .'.collectFinalDetails';
        $XFX['verify']      = $Fusebox["circuit"] .'verifyAddress';
        include("act_checkAddress.php");
        include("dsp_checkAddress.php");
        break;

    // Step 4
	case "listReview";
        $PARAM['filter']= FILTER_LIST_REVIEW;
		$XFA['edit']	= $Fusebox["circuit"] .'.collectFinalDetails';
		$XFA['delete']	= $Fusebox["circuit"] .'.deleteReview';
        $XFA['filter']  = $FbSelf;
		include("dsp_list.php");
		break;
    
    case "deleteReview":
        $XFA['process']     = $FbSelf;
        $XFA['return']	    = $Fusebox["circuit"] .'.listReview';
        $XFA['continue']	= $Fusebox["circuit"] .'.listReview';
        include("act_reviewPending.php");
        break;

    case "collectFinalDetails";
        $XFA['process'] = $FbSelf;
        $XFA['return']	= $Fusebox["circuit"] .'.listReview';
        $XFA['continue']= $Fusebox["circuit"] .'.publish';
        include("act_collectFinalDetails.php");
        include("dsp_collectFinalDetails.php");
        break;

    // Step 5
    case "listPublish";
        $PARAM['filter']= FILTER_LIST_PUBLISH;
        $XFA['edit']	= $Fusebox["circuit"] .'.listPublish';
		$XFA['delete']	= $Fusebox["circuit"] .'.delete';
        $XFA['filter']  = $FbSelf;
		include("dsp_list.php");
		break;

	case "publish";
		$XFA['return']	= $Fusebox["circuit"] .'.listPublish';
		require(PATH_TO_COMMON . "/classes/phpass/PasswordHash.php");
		include("act_publish.php");
		include("dsp_publish.php");
		break;

    case "ajax_storeAddress":
        include("ajax_storeAddress.php");
        break;

	default:
		//include("dsp_features.php");
		break;
}
?>
