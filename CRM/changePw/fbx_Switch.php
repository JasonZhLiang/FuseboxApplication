<?php
//		
$CURRENT_SECTION = $managingSectionTitle = '';
$FbAction = $Fusebox["circuit"] .'.'. $Fusebox["action"];
$Nav->setNavigationAids($FbAction, $CURRENT_SECTION, $managingSectionTitle); 	// by Ref

require(PATH_TO_COMMON . "/classes/phpass/PasswordHash.php");
require(PATH_TO_COMMON . "/classes/User.php");

$Utils->authenticateAdminUser();

$UserObj = new User();

// global XFA
$XFA["showUserList"] = $Fusebox['thisCircuit'].".listUsers" ;

switch($Fusebox["action"]) {

	case "Fusebox.defaultFuseaction":

	case "home":
		$XFA['process']     = $Fusebox["circuit"] .'.confirmPwd';
		$XFA['return']      = "users.listUsers";
		$XFA['continue']    = $Fusebox["circuit"] .'.changePwd';
		include("dsp_confirmPwd.php");
		break;

	case "confirmPwd":
        $XFA['process'] = $FbAction;
        $XFA['return']  = "summary.dashboard";
		$XFA['continue']    = $Fusebox["circuit"] .'.changePwd';
	    include("dsp_confirmPwd.php");
        break;

    case "changePwd":
        $XFA['process'] = $FbAction;
        $XFA['return']  = "summary.dashboard";
		$XFA['continue']    = $Fusebox["circuit"] .'.changePwd';
	    include("dsp_changePwdForm.php");
        break;

	default:
		//print "Ix received a fuseaction called <b>" . $Fusebox["action"] . "</b> that circuit <b>" . $Fusebox["circuit"] . "</b> does not have a handler for.";
        print 'I received an Action that does not have a handler.';
		break;
}