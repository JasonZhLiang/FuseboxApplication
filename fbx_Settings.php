<?php
//		die('settings');
//In case no fuseaction was given, I'll set up one to use by default.
if(!isset($attributes["action"])){ $attributes["action"] = "summary.dashboard"; }

//useful constants
if(!isset($GLOBALS["self"])){ $GLOBALS["self"] = "index.php"; }
$XFA = array();
$PARAM = array();
// Global XFA's
$XFA['logout'] 		    = "login.logout";
$XFA['showLogin'] 	    = "login.showLogin";
$XFA['userSearch'] 	    = "users.list";
$XFA['siteList'] 	    = "sites.list";
$XFA['siteSearch'] 	    = "sites.search";

//should fusebox silently suppress its own error messages? default is FALSE
$Fusebox["suppressErrors"] = false;

$suppressLayout = false;
if($Fusebox["isHomeCircuit"]) {
//put settings here that you want to execute only when this is the application's home circuit (for example session_start(); )
} else {
//put settings here that you want to execute only when this is not an application's home circuit
}
//Put settings out here that should run regardless of whether this is the home app or not

/* These are default values used numerous places in the application
to validate a user's security level and logged in status */
if(!isset($client["groups"])){ $client["groups"] = ""; }
if(!isset($client["userID"])){ $client["userID"] = ""; }

/* Build a structure of the security groups. It is cached in the application scope. */
/* EXAMIN HOW THIS IS DONE, DOES IT NEED TO BE IN A DB? COULD I NOT GET IT WHEN A USER LOGS IN?*/
// include("act_buildGroupStruct.php");

/* page holds page layout elements like the left side bar, the breadcrumb trail, etc.
Note that this is not request scoped because we don't want these elements to be
overwritten during a page's cfmodule calls to other fuseactions. */

$page = array();

/* set the default left Fuseaction to run */
if (! $Fusebox['isCustomTag']){
	$page['leftBar']['action'] = "";
}


$page['breadcrumb'] = array();
$thisCrumb = array("name" => "Home", "url" => "" );
array_push ($page['breadcrumb'], $thisCrumb);
?>