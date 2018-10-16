<?php
/**
 * ACL Circuits
 */
$Fusebox["circuits"]["root"] 			= "root";
$Fusebox["circuits"]["summary"] 		= "root/summary";
$Fusebox["circuits"]["dashboard"] 		= "root/summary/dashboard";
// Dashboard Items
$Fusebox["circuits"]["reports"] 	    = "root/summary/actionItems/reports";
// CRM
$Fusebox["circuits"]["users"] 			= "root/CRM/users";
$Fusebox["circuits"]["usersChangePw"] 	= "root/CRM/changePw";
$Fusebox["circuits"]["pendingClients"] 	= "root/CRM/pendingClients";
$Fusebox["circuits"]["sites"] 	        = "root/CRM/sites";
$Fusebox["circuits"]["siteAssets"] 	    = "root/CRM/sites/siteAssets";
$Fusebox["circuits"]["assetsSims"] 	    = "root/CRM/assets/sims";
$Fusebox["circuits"]["assetsModems"] 	= "root/CRM/assets/modems";
$Fusebox["circuits"]["assetsRouters"] 	= "root/CRM/assets/routers";
$Fusebox["circuits"]["integraters"] 	= "root/CRM/assets/integraters";
// System_maintenance (CAUTION Folder is called system_maintenance NOT system_maintenence) // TODO
$Fusebox["circuits"]["sysMain"]			= "root/system_maintenance";
$Fusebox["circuits"]["sysTranslations"]	= "root/system_maintenance/translations/sysTranslations";
$Fusebox["circuits"]["dbTranslations"]	= "root/system_maintenance/translations/dbTranslations";
$Fusebox["circuits"]["sysTests"]	    = "root/system_maintenance/system_tests";
$Fusebox["circuits"]['sysEmails'] 		= "root/system_maintenance/system_emails";
$Fusebox["circuits"]['sysEmailTest']    = "root/system_maintenance/email_testing";
// Admin
$Fusebox["circuits"]['admin']           = "root/admin";
$Fusebox["circuits"]['SystemDrill']   = "root/systemDrill/";

/**
 * NON-ACL Circuits
 */
$Fusebox["circuits"]["services"] 		= "root/services";
$Fusebox["circuits"]["login"] 			= "root/login";
$Fusebox["circuits"]["twilio"] 			= "root/twilio";
$Fusebox["circuits"]["crons"] 			= "root/crons";
$Fusebox["circuits"]["keepAlive"] 		= "root/summary";
// Srever Density Monitoring
$Fusebox['circuits']['monitor'] 		= 'root/system_maintenance/monitoring/serverDensity';
