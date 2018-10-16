<?php
////////////////////////////////////////////////////////////
// File: fbx_switch
//
// Information:
//		Date		- 2018-02-02
//		Author		- Jasonzh L
//		Version		- 1.0
//
// History:
//		- v1.0 2018-02-02: initial development
//
////////////////////////////////////////////////////////////

$CURRENT_SECTION = $managingSectionTitle = '';
$FbAction = $FbSelf = $Fusebox["circuit"] .'.'. $Fusebox["action"];

define("SEC_TYPE", "Modems");
// 	Security: 	circuit level access control
$Utils->authenticateAdminUser();

switch($Fusebox["action"]) {
	case "Fusebox.defaultFuseaction":
		$Fusebox["action"] = 'list';
    case 'list':
        $XFA['add'] 			= $Fusebox["circuit"].'.add';
        $XFA['edit'] 			= $Fusebox["circuit"].'.edit';
        $XFA['delete'] 			= $Fusebox["circuit"].'.delete';
        $XFA["list"]            = $FbSelf;
		require_once('dsp_list.php');
		break;

	case "add":
    case "edit":
    case "delete":
        $XFA["list"]     = $Fusebox['circuit'].".list" ;
        $XFA["process"]  = $FbSelf;
        require_once('act_process.php');
        require_once('dsp_edit.php');
        break;

    default:
		//print "Ix received a fuseaction called <b>" . $Fusebox["action"] . "</b> that circuit <b>" . $Fusebox["circuit"] . "</b> does not have a handler for.";
        print 'I received an Action that does not have a handler.';
		break;
}
// now render Navigational Aids 
$Nav->setNavigationAids($FbAction, $CURRENT_SECTION, $managingSectionTitle);
