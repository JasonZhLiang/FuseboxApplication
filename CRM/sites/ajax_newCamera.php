<?php
////////////////////////////////////////////////////////////
// File: ajax_newCamera.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2014-02-20
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in JetBrains PhpStorm
//		
//
////////////////////////////////////////////////////////////

// TODO: no md5 hashing of GET id for security TBS 2016-06-23
if ( ! isset($_GET["camera"]) || ! is_numeric($_GET["camera"]) ) {
	echo(0);
} else {
	$_SESSION["ADMIN_USER"]["SiteCameras"]['curCamera'] = $_GET["camera"];
	echo(1);
}
