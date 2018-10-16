<?php
if (!$Fusebox["isCustomTag"]) { //skip if module call
	// Defaults
	$Fusebox["layoutDir"] = "_bin/";
	$Fusebox["layoutFile"] = "lay_default.php";
    define("LAY_EMPTY", "");

	switch ($Fusebox['circuit']) {
		case "login":
			$Fusebox["layoutFile"] = 'lay_login.php';
			break;
        case "crons":
        case "services":
        case "monitor":
        case "keepAlive":
            $Fusebox["layoutFile"] = "";
		    break;
	}
	switch( $Fusebox['circuit'].".".$Fusebox['action'] ) {
        case 'sites.viewDirectFeed':
        case 'reports.downloadFROrgsReport':
            $Fusebox["layoutFile"] = LAY_EMPTY;
            break;
//        case 'sites.viewFeed':
//            $Fusebox["layoutFile"] = "lay_camera.php";
//            break;

	    case "invoice.print":
			$Fusebox["layoutFile"] = "";
			break;
	}
		// ajax
	if(substr($Fusebox['action'], 0, 5) == 'ajax_'){
		$Fusebox["layoutFile"] = "";
	}
}
?>