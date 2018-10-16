<?php
$Utils->authenticateAdminUser();
$managingSectionTitle = '[default - fixMe]';
if (empty($Fusebox["action"])) $Fusebox["action"] = "default";
$FbAction = 'sites.list';
$FbSelf   = $Fusebox["circuit"] . '.' . $Fusebox["action"];
$Nav->setNavigationAids($FbAction, $CURRENT_SECTION, $managingSectionTitle); /* by Ref */

switch($Fusebox["action"]) {
    case "ajax_newCamera":
        include("ajax_newCamera.php");
        session_write_close();
        exit();
        break;

    case "ajax_newSortOrder":
        include("ajax_sortOrder.php");
        session_write_close();
        exit();
        break;

    case "ajax_newGroupSortOrder":
        include("ajax_sortGroupOrder.php");
        session_write_close();
        exit();
        break;

    case "ajax_pingCam":
        include(PATH_TO_COMMON . '/classes/ErpOnvif/Onvif.php');
        include("ajax_pingCamera.php");
        session_write_close();
        exit();
        break;
}

switch($Fusebox["action"]) {
    case "Fusebox.defaultFuseaction":
    case "default":
    case "list":
        $XFA['process']   = $FbSelf;
        $XFA['listFR']    = $Fusebox["circuit"] . '.listFR';
        $XFA['viewUsers'] = $Fusebox["circuit"] . '.siteUsers';
        $XFA['camLink']   = $Fusebox["circuit"] . '.camManage';
        include("dsp_list.php");
        break;

    case "search":
        $XFA['process']  = $FbSelf;
        $XFA['edit']     = $Fusebox["circuit"] . '.siteDetail';
        $XFA['delete']   = $Fusebox["circuit"] . '.deleteSite';
        $XFA['userLink'] = 'users.edit';
        require_once('act_search.php');
        include("dsp_search.php");
        break;

    case "showFRList":
        $XFA["return"]  = $Fusebox["circuit"] . '.siteDetail';
        $XFA["process"] = $FbSelf;
        $XFA["add"]     = $Fusebox["circuit"] . '.addFRList';
        include("dsp_listEditableFR.php");
        break;

    case "addFRList":
        $XFA["return"]  = $Fusebox["circuit"] . '.showFRList';
        $XFA["process"] = $FbSelf;
        include("act_editFR.php");
        include("dsp_editFR.php");
        break;

    case "siteDetail":
        $XFA["search"]        = $Fusebox["circuit"] . '.search';
        $XFA["editFRList"]    = $Fusebox["circuit"] . '.showFRList';
        $XFA["editUsersList"] = 'users.list';
        $XFA["editSite"]      = $Fusebox["circuit"] . '.editSite';
        $XFA["editCameras"]   = $Fusebox["circuit"] . '.camManage';
        $XFA["editIntegrator"]= 'siteAssets.showIntegrators';
        $XFA["editRouters"]   = 'siteAssets.showRouters';
        $XFA['editModems']    = 'siteAssets.showModems';
        require(PATH_TO_COMMON . '/classes/SiteInfoManager.php');
        require(PATH_TO_COMMON . "/classes/UserPS.php");
        require(PATH_TO_COMMON . '/classes/modules/dashboards/Dashboard_PS.php');
        include("dsp_admindashboard.php");
        break;

    case "editSite":
    case "deleteSite":
        $XFA["list"]    = $Fusebox['circuit'] . ".search";
        $XFA["return"]  = $Fusebox["circuit"] . '.siteDetail';
        $XFA["process"] = $FbSelf;
        require_once('act_processSite.php');
        require_once('dsp_editSite.php');
        break;

    case "showSectorList":
    case "siteUsers":
        $XFA['return']     = $Fusebox["circuit"] . '.list';
        $XFA['proxyLogin'] = HTTPS_DOMAIN . '/ps/' . APP_URL . 'login.proxyLogin';
        include("dsp_viewSiteUsers.php");
        break;

    case "listFR":
        $XFA['process']   = $FbSelf;
        $XFA['list']      = $Fusebox["circuit"] . '.list';
        $XFA['viewUsers'] = $Fusebox["circuit"] . '.orgUsers';
        $XFA['sysDrillInfo'] = 'SystemDrill.sysDrillInfo';
        include("dsp_listFR.php");
        break;

    case "orgUsers":
        $XFA['return']     = $Fusebox["circuit"] . '.listFR';
        $XFA['proxyLogin'] = HTTPS_DOMAIN . '/fr/' . APP_URL . 'login.proxyLogin';
        include("dsp_viewOrgUsers.php");
        break;

    case "viewFeed":
        $XFA["return"]  = $Fusebox["circuit"] . '.camManage';
        $XFA["newCam"]  = $Fusebox["circuit"] . '.ajax_newCamera';
        $XFA["imgFeed"] = $Fusebox["circuit"] . '.viewDirectFeed';
        include(PATH_TO_COMMON . '/classes/cameras/CameraData.php');
        include(PATH_TO_COMMON . '/classes/cameras/CameraComponent.php');
        include("dsp_viewDirectFeed.php");
        break;

    case "viewDirectFeed":
        include(PATH_TO_COMMON . '/classes/cameras/CameraAccess.php');
        include("act_getVideo.php");
        break;

    case "edit":
    case "add":
        $XFA["return"]  = $Fusebox["circuit"] . '.camManage';
        $XFA["pingCam"] = $Fusebox["circuit"] . '.ajax_pingCam';
        $XFA['process'] = $FbSelf;
        include(PATH_TO_COMMON . '/classes/cameras/CameraAccess.php');
        include(PATH_TO_COMMON . '/classes/cameras/CameraListRenderer.php');
        include("act_process.php");
        include("dsp_edit.php");
        break;

    case "addGroup":
    case "editGroup":
    case "deleteGroup":
        $XFA["return"]  = $Fusebox["circuit"] . '.camManage';
        $XFA['process'] = $FbSelf;
        include("act_processGroup.php");
        include("dsp_editGroup.php");
        break;

    case "camManage":
        $XFA["add"]         = $Fusebox["circuit"] . '.add';
        $XFA["addGroup"]    = $Fusebox["circuit"] . '.addGroup';
        $XFA["edit"]        = $Fusebox["circuit"] . '.edit';
        $XFA["editGroup"]   = $Fusebox["circuit"] . '.editGroup';
        $XFA["deleteGroup"] = $Fusebox["circuit"] . '.deleteGroup';
        $XFA["sort"]        = $Fusebox["circuit"] . '.ajax_newSortOrder';
        $XFA["groupSort"]   = $Fusebox["circuit"] . '.ajax_newGroupSortOrder';
        $XFA['list']        = $Fusebox["circuit"] . '.list';
        $XFA["viewCam"]     = $Fusebox["circuit"] . '.viewFeed';
        $XFA['return']      = $Fusebox["circuit"] . '.camManage';
        $XFA["process"]     = $Fusebox["circuit"] . '.camManage';
        $XFA["siteDetail"]  = $Fusebox["circuit"] . '.siteDetail';
        include(PATH_TO_COMMON . '/classes/cameras/CameraAccess.php');
        include(PATH_TO_COMMON . '/classes/cameras/CameraListRenderer.php');
        include("dsp_camManage.php");
        break;

    default:
        //print "Ix received a fuseaction called <b>" . $Fusebox["action"] . "</b> that circuit <b>" . $Fusebox["circuit"] . "</b> does not have a handler for.";
        print 'I received an Action that does not have a handler.';
        break;
}
?>