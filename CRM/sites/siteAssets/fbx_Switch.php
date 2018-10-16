<?php
if (empty($Fusebox["action"])) $Fusebox["action"] = "default";
$FbAction = $FbSelf = $Fusebox["circuit"] . '.' . $Fusebox["action"];
switch ($Fusebox["action"]) {
    case "showIntegrators":
        $XFA["list"]       = $FbSelf;
        $XFA["return"]     = 'sites.siteDetail';
        $XFA["toggleLink"] = $Fusebox["circuit"] . '.toggleIntegratorLink';
        include("dsp_integratorsList.php");
        break;
    case "toggleIntegratorLink":
        $XFA["list"] = 'sites.siteDetail';
        include("act_toggleIntegratorLink.php");
        break;
    case "showRouters":
        $XFA["list"]       = $FbSelf;
        $XFA["return"]     = 'sites.siteDetail';
        $XFA["toggleLink"] = $Fusebox["circuit"] . '.toggleRouterLink';
        include("dsp_routerList.php");
        break;
    case "toggleRouterLink":
        $XFA["list"] = 'sites.siteDetail';
        include("act_toggleRouterLink.php");
        break;
    case "showModems":
        $XFA["list"]       = $FbSelf;
        $XFA["return"]     = 'sites.siteDetail';
        $XFA["toggleLink"] = $Fusebox["circuit"] . '.toggleModemLink';
        $XFA["linkSIM"]    = $Fusebox["circuit"] . '.showSims';
        include("dsp_modemList.php");
        break;
    case "toggleModemLink":
        $XFA["list"] = 'sites.siteDetail';
        include("act_toggleModemLink.php");
        break;
    case "showSims":
        $XFA["list"]       = $FbSelf;
        $XFA["return"]     = $Fusebox["circuit"] . '.showModems';
        $XFA["toggleLink"] = $Fusebox["circuit"] . '.toggleSimLink';
        include("dsp_simList.php");
        break;
    case "toggleSimLink":
        $XFA["return"] = $Fusebox["circuit"] . '.showSims';
        $XFA["list"]   = $Fusebox["circuit"] . '.showModems';
        include("act_toggleSimLink.php");
        break;
    default:
        print 'I received an Action that does not have a handler.';
        break;
}
?>