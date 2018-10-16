<?php
////////////////////////////////////////////////////////////
// File: fbx_switch
//		
//
// Information:
//		Date		- 2014-06-17
//		Author		- Rick	O'S
//		Version		- 2.0
//
// History:
//		- v2.0 2014-06-17: initial development 
//		 1) 
//
////////////////////////////////////////////////////////////

$CURRENT_SECTION = $managingSectionTitle = '';
$FbAction = $FbSelf = $Fusebox["circuit"] .'.'. $Fusebox["action"];

// 	Security: 	circuit level access control
$Utils->authenticateAdminUser();

switch($Fusebox["action"]) {
	case "Fusebox.defaultFuseaction":
		$Fusebox["action"] = 'list';
	case 'list_rtn':
		require_once('act_restore_from_session.php');
		$FbAction = $FbSelf = $Fusebox["circuit"].'.list';
	case 'list':
		$XFA['process'] 		= $FbSelf;
		$XFA['return'] 			= $FbSelf;
        $XFA['siteLink'] 		= 'sites.siteDetail';
		$XFA['details'] 		= $Fusebox["circuit"].'.details';
		$XFA['delete'] 			= $Fusebox["circuit"].'.delete';
        $XFA['undelete']        = $Fusebox["circuit"].'.undelete';
		$XFA['edit'] 			= $Fusebox["circuit"].'.edit';
		$XFA['add'] 			= $Fusebox["circuit"].'.add';
		$XFA['invoices']		= 'invoice.contact';
		$XFA['3rdP_proxyLogin']	= 'login.simLogin' ;	//	3p - login
        $XFA["unlock"]          = $Fusebox["circuit"] .'.ajax_unlockUser';
        $XFA["toggleActive"]    = $Fusebox["circuit"] .'.ajax_toggleUserActive';
		require_once('act_list.php');
		require_once('dsp_list.php');
		break;
	case "details":
        $XFA['edit'] 		= $Fusebox["circuit"].'.edit';
		$XFA['list'] 		= $Fusebox["circuit"].'.list';		 
		$XFA['return'] 		= $Fusebox["circuit"].'.list';
		require_once('dsp_details.php');
		break;

    case 'delete':
    case "undelete":
	case "add":
	case "edit":
		$XFA['process'] 	  = $FbSelf;
		$XFA['return'] 		  = $Fusebox["circuit"].'.list_rtn';
        $XFA['editSitesList'] = 'sites.search';
		require(PATH_TO_COMMON . "/classes/User.php");
		require_once('act_process.php');
		require_once('dsp_edit.php');
		break;

    case "ajax_unlockUser":
        include("ajax_unlockUser.php");
        exit();
        break;

    case "ajax_toggleUserActive":
        include("ajax_toggleUserActive.php");
        exit();
        break;

	default:
		//print "Ix received a fuseaction called <b>" . $Fusebox["action"] . "</b> that circuit <b>" . $Fusebox["circuit"] . "</b> does not have a handler for.";
        print 'I received an Action that does not have a handler.';
		break;
}
// now render Navigational Aids 
$Nav->setNavigationAids($FbAction, $CURRENT_SECTION, $managingSectionTitle); 

//		echo('<pre>'. print_r($_SESSION['ADMIN_USER'], 1)  .'</pre><hr>'); 	
//			DevDebug::showArrayInTable( $_POST, "\$_POST"); echo('<hr color="red">'); 
//			DevDebug::showArrayInTable( $_GET, "\$_GET"); echo('<hr color="red">'); 
//			die('<br />die in '. __FILE__ .'  @ line # '. __LINE__ .'');

	
?>