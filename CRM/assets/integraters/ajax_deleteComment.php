<?php
////////////////////////////////////////////////////////////
// File: ajax_deleteComment.php
//
// Description:
//
//      - 
//
//
// Information:
//		Date		- 2014-02-11
//		Author		- TBS
//		Version	    - 1.0
//
// History:
//		- v1.0 initial development in JetBrains PhpStorm
//		
//
////////////////////////////////////////////////////////////

$result		= 0;
$id 		= $_GET["id"];

$sql = "DELETE FROM erpcorp_admin.integrator_comments
		WHERE PkID = :PkID
;";

$PDOdb->prepare($sql);
$PDOdb->bind('PkID', $id);
$PDOdb->execute();

echo($PDOdb->getResultSet());
