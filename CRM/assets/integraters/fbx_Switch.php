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
$FbAction        = $FbSelf = $Fusebox["circuit"] . '.' . $Fusebox["action"];

define("SEC_TYPE", "Integrators");
$Utils->authenticateAdminUser();

switch ($Fusebox["action"]) {
    case "Fusebox.defaultFuseaction":
        $Fusebox["action"] = 'list';
        break;
    case "list":
        $XFA['add']      = $Fusebox["circuit"] . '.add';
        $XFA['edit']     = $Fusebox["circuit"] . '.edit';
        $XFA['delete']   = $Fusebox["circuit"] . '.delete';
        $XFA['undelete'] = $Fusebox["circuit"] . '.undelete';
        $XFA["list"]     = $FbSelf;
        $XFA["addTech"]  = $Fusebox["circuit"] . '.addTech';
        $XFA["editTech"] = $Fusebox["circuit"] . '.editTech';
        $XFA["delTech"]  = $Fusebox["circuit"] . '.delTech';
        require_once('dsp_list.php');
        break;
    case "addTech":
    case "editTech":
    case "delTech":
        $XFA["list"]        = $Fusebox['circuit'] . ".list";
        $XFA["processTech"] = $FbSelf;
        require_once('act_processTech.php');
        require_once('dsp_editTech.php');
        break;
    case "add":
    case "edit":
    case "delete":
    case "undelete":
        $XFA["list"]    = $Fusebox['circuit'] . ".list";
        $XFA["process"] = $FbSelf;
        $XFA["addComment"] = $Fusebox["circuit"] . '.addComment';
        $XFA["deleteComment"]= $Fusebox["circuit"] . '.deleteComment';
        $XFA['updateComment']= $Fusebox["circuit"] . '.updateComment';
        $XFA['updateRadio']= $Fusebox["circuit"] . '.updateRadio';
        require_once('act_process.php');
        require_once('dsp_edit.php');
        break;
    case "addComment":
        require_once('ajax_addComment.php');
        exit();
        break;
    case "deleteComment":
        require_once('ajax_deleteComment.php');
        exit();
        break;
    case "updateComment":
        require_once('ajax_updateComment.php');
        exit();
        break;
    case "updateRadio":
        require_once('ajax_updateRadio.php');
        exit();
        break;
    default:
        print 'I received an Action that does not have a handler.';
        break;
}
// now render Navigational Aids 
$Nav->setNavigationAids($FbAction, $CURRENT_SECTION, $managingSectionTitle);
