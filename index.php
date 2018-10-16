<?php

////////////////////////////////////////////////////////////
// Determine language
//
//		echo('<br /> <span style="background-color: #c0c000;"> >>> ['. substr($_SERVER['SCRIPT_NAME'], 1,2) .']</span>');
$langFolder = substr($_SERVER['SCRIPT_NAME'], 1,2);
if ("EN" == $langFolder) {
	define('LANG','_EN');// English 
}else if("FR" == $langFolder){
	define('LANG','_FR');// French	
}else{
	// Default language to use
	// - You can switch this for testing other language until virtual directories are 
	//		resolving on live server (note: you can only test one language at a time this way)
	define('LANG','_EN');// default to this
}



session_start(); 	// Must happen before any header information is passed.
require("../_common/config_inc.php");
if(DEBUG_STATUS || IS_TEST_SERVER){
	require(PATH_TO_COMMON . "/classes/debug_class.php");
}
require(PATH_TO_COMMON . "/errorHandler.php");
require(PATH_TO_COMMON . "/classes/PDODatabase.php");
require(PATH_TO_COMMON . "/classes/db_class.php");
require(PATH_TO_COMMON . "/classes/utils_class.php");
require(PATH_TO_COMMON . "/classes/ACL.php");
require(PATH_TO_COMMON . "/classes/Nav.php");
require(PATH_TO_COMMON . "/classes/Menu.php");
require(PATH_TO_COMMON . "/classes/validation_class.php"); // validation class
require(PATH_TO_COMMON . "/classes/translation_class.php");
include(PATH_TO_COMMON . "/classes/Components.php");
include(PATH_TO_COMMON . "/classes/EmailService.php");
include(PATH_TO_COMMON . "/classes/SystemBroadcastDrill/SysBroadcastDrillManager.php");

$PDOdb          = new App\PDODatabase(DB_DSN, DB_USER, DB_PASS);
$DBUtils	    = new DB(HOST,DB,USER,PWD, true);
$Utils		    = new Utils();
$ACL		    = new ACL();
$Nav		    = new Nav($ACL);
$trans		    = new TranslateUtil($PDOdb);
$Components     = new Components();
$EmailService   = new EmailService();

require("_bin/navAdmin_inc.php");
require("_bin/fbx_Fusebox3.0_PHP4.1.x.php");
?>