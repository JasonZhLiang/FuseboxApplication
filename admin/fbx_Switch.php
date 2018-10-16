<?php
////////////////////////////////////////////////////////////
// File: fbx_switch
//		
//
// Information:
//		Date		- 2016-04-21
//		Author		- Rick	O'S
//		Version		- 1.0.1
//
// History:
//		- 2016-04-21: v 1.0.1
//		 1) ported here from another client.  Minor adjustments to centralize the classes into Common-Path
//		- 2014-09-02: initial development
//
////////////////////////////////////////////////////////////
		
$CURRENT_SECTION = $managingSectionTitle = '';
$FbAction = $FbSelf = $Fusebox["circuit"] .'.'. $Fusebox["action"];

// 	Security: 	circuit level access control 
$Utils->authenticateAdminUser();

/*
// 		ACCESS-CONTROL:	Steven, and Andrea Only
//		$XFA['noAccess'] - is set in the settings file as a global XFA,  in some cases I might override it locally but not here/
if(! $Utils->restrictContentDisplay('Expose_AdminUsers')) {
    echo('<div style="background-image:url(/images/ecom/statement_top_logo.jpg); background-position: 99% 12 ;  background-repeat:no-repeat; min-height: 333px; border:1px solid #900; color: #990000; font-size: 2em; font-weight: bold;margin: 22px; padding: 42px 20px;">
		  	<span style="font-size: 1.2em; margin-left: 169px;">Access Denied:</span>
			<div style="text-align: center; color: #333333; ">you are not permitted to access this page. <br />Please <a href="'.APP_URL . $XFA['noAccess'] .'"  >click here</a> to continue ...
		  </div>
		');
    die();
}
*/


// SET defaultFuseaction here for this Circuit
if(empty($Fusebox["action"])) $Fusebox["action"] = "listUsers";
$CURRENT_SECTION = $managingSectionTitle = '';
$FbAction = $Fusebox["circuit"] .'.'. $Fusebox["action"];
$Nav->setNavigationAids($FbAction, $CURRENT_SECTION, $managingSectionTitle); /* by Ref */


$maxPermittedPwLength = 72; 		//	2014-02-19 RoK:  used in act_ & in dsp_ files


switch($Fusebox["action"]) {
	case "Fusebox.defaultFuseaction":
		$Fusebox["action"] = 'list';
	case 'list':		
		$XFA["self"] 		 = $FbSelf ;
        $XFA["addUser"]   = $Fusebox['thisCircuit'].".addUser" ;


        $XFA["editUser"]     = $Fusebox['thisCircuit'].".modifyUser" ;
        $XFA["accessRights"] = $Fusebox['thisCircuit'].'.accessRights' ;
        $XFA["editUser"]     = $Fusebox['thisCircuit'].".accessRights" ;


        $XFA["removeUser"]   = $Fusebox['thisCircuit'].".deleteUser" ;
        $XFA["changePwd"]    = $Fusebox['thisCircuit'].".changePwd" ; //. ""'usersChangePw.home' ;


		require_once('dsp_list.php');
		break;
    case 'accessRights':
        $XFA["process"]     = $FbSelf;
        $XFA["return"] 		= $Fusebox['thisCircuit'].'.list' ;
        require_once ('act_accessRights.php');
        require_once('dsp_accessRights.php');
        break;

	default:
        print 'I received an Action that does not have a handler.';
		break;
}

//	set Nav aids here  to ensure that the Navigation remains set correctly when I manipulate it after the control logic
//	this will fix the Nav not setting correctly  ...  setting the navigation-aids here at the bottom permit us to
//	adjust it in the switch
if( ($Fusebox["action"] == 'add')||($Fusebox["action"] == 'edit') ){
	$FbAction = $Fusebox['thisCircuit'].'.list';
}
$Nav->setNavigationAids($FbAction, $CURRENT_SECTION, $managingSectionTitle);
